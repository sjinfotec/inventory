<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\User;
use App\WorkTime;
use App\Http\Controllers\ApiCommonController;
use Carbon\Carbon;

class ApiGetAttendanceResultController extends Controller
{

    private $source_mode = '';       // 登録済みのモード

    /**
     * 初期処理
     *
     * @param Request $request
     * @return void
     */
    public function index(){}

    /**
     * 登録
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request) { 
        $card_id = $request->card_id;       // カードID
        $mode = $request->mode;             // 打刻モード
        $user = new User();
        $work_time = new WorkTime();
        $systemdate = Carbon::now();
        $response = collect();              // 端末の戻り値
        $this->source_mode = '';
        // カード情報存在チェック
        $is_exists = DB::table('card_informations')->where('card_idm', $card_id)->exists();
        if($is_exists){
            $user_datas = $user->getUserCardData($card_id);
            if (count($user_datas) > 0) {
                foreach($user_datas as $user_data) {
                    $chk_result = $this->chkMode($user_data, $mode, $systemdate);
                    if($chk_result){
                        // 打刻データ登録
                        DB::beginTransaction();
                        try{
                            $ins_result = $this->insertTime($user_data, $mode, $systemdate);
                            $response->put(Config::get('const.PUT_ITEM.result'),Config::get('const.RESULT_CODE.success'));
                            $response->put(Config::get('const.PUT_ITEM.user_code'),$user_data->code);
                            $response->put(Config::get('const.PUT_ITEM.user_name'),$user_data->name);
                            $response->put(Config::get('const.PUT_ITEM.record_time'),$systemdate->format('H:i:s'));
                            $response->put(Config::get('const.PUT_ITEM.source_mode'),$this->source_mode);
                            DB::commit();

                        }catch(\PDOException $pe){
                            DB::rollBack();
                            Log::error(Config::get('insert_error = '.'const.RESULT_CODE.insert_error'));
                            Log::error(Config::get('$pe = '.$pe->getMessage()));
                            $response->put(Config::get('const.PUT_ITEM.result'),Config::get('const.RESULT_CODE.insert_error'));
                            $response->put(Config::get('const.PUT_ITEM.user_code'),$user_data->code);
                            $response->put(Config::get('const.PUT_ITEM.user_name'),$user_data->name);
                            $response->put(Config::get('const.PUT_ITEM.record_time'),$systemdate->format('H:i:s'));
                            $response->put(Config::get('const.PUT_ITEM.source_mode'),$this->source_mode);
                        }
                    }else{
                        Log::debug('カード情報チェック NG'.Config::get('const.RESULT_CODE.mode_illegal'));
                        $response->put(Config::get('const.PUT_ITEM.result'),Config::get('const.RESULT_CODE.mode_illegal'));
                        $response->put(Config::get('const.PUT_ITEM.user_code'),$user_data->code);
                        $response->put(Config::get('const.PUT_ITEM.user_name'),$user_data->name);
                        $response->put(Config::get('const.PUT_ITEM.record_time'),$systemdate->format('H:i:s'));
                        $response->put(Config::get('const.PUT_ITEM.source_mode'),$this->source_mode);
                    }
                    break;
                }
            } else {
                Log::debug('カード情報取得 NG'.Config::get('const.RESULT_CODE.user_not_exsits'));
                $response->put(Config::get('const.PUT_ITEM.result'),Config::get('const.RESULT_CODE.user_not_exsits'));
            }
        }else{  // カード情報が存在しない
            Log::debug('カード情報存在チェック NG'.Config::get('const.RESULT_CODE.card_not_exsits'));
            $response->put(Config::get('const.PUT_ITEM.result'),Config::get('const.RESULT_CODE.card_not_exsits'));
        }

        return $response;
    }

    /**
     * 1件取得
     *
     * @param [type] $id
     * @return void
     */
    public function show($id) { }

    /**
     * 更新
     *
     * @param Request $request
     * @param [type] $id
     * @return void
     */
    public function update(Request $request, $id) { }

    /**
     * 削除
     *
     * @param [type] $id
     * @return void
     */
    public function destroy($id) { }

    /**
     * 勤怠チェック
     *
     * @param [type] $user_id
     * @param [type] $mode
     * @return void
     */
    private function chkMode($user_data, $mode, $systemdate){
        $apicommon = new ApiCommonController();
        $work_time = new WorkTime();
        $work_time->setParamDepartmentcodeAttribute($user_data->department_code);
        Log::debug('setParamDepartmentcodeAttribute = '.$user_data->department_code);
        $work_time->setParamUsercodeAttribute($user_data->code);
        Log::debug('setParamdatefromNonformatAttribute = '.$systemdate->format('Ymd His'));
        $work_time->setParamdatefromNoneditAttribute($systemdate->format('Ymd His'));
        $this->source_mode = '';
        // MAX打刻取得
        $chk_result = true;
        $daily_times = $work_time->getDailyMaxData();
        if(count($daily_times) > 0)){
            Log::debug('MAX打刻取得 OK ');
            $i=0;
            foreach ($daily_times as $result) {
                // モードチェック
                if(isset($result->mode)){
                    $i += 1;
                    $this->source_mode = $result->mode;
                    $chk_result = $apicommon->chkMode($mode, $this->source_mode);
                } else {
                    // ない場合は出勤以外はエラーとする
                    $chk_result = $apicommon->chkMode($mode, '');
                }
                break;
            }
            if ($i == 0) {
                // ない場合は出勤以外はエラーとする
                $chk_result = $apicommon->chkMode($mode, '');
            }
        } else {
            // ない場合は出勤以外はエラーとする
            $chk_result = $apicommon->chkMode($mode, '');
        }

        return $chk_result;
    }

    /**
     * 打刻データ登録
     *
     * @param Request $request
     * @param [type] $id
     * @return void
     */
    public function insertTime($user_data, $mode, $systemdate) {

        try{
            $work_time = new WorkTime();
            $work_time->setUsercodeAttribute($user_data->code);
            $work_time->setDepartmentcodeAttribute($user_data->department_code);
            $work_time->setRecordtimeAttribute($systemdate);
            $work_time->setModeAttribute($mode);
            $work_time->setCreateduserAttribute($user_data->code);
            $work_time->setSystemDateAttribute($systemdate);
            $work_time->insertWorkTime();

            return true;

        }catch(\PDOException $pe){
            Log::error(str_replace('{0}', 'work_times', Config::get('const.LOG_MSG.data_insert_erorr')));
            Log::error($pe->getMessage());
        }
    }

}
