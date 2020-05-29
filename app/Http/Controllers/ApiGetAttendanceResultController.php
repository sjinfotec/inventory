<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\User;
use App\WorkTime;
use App\WorkTimeLog;
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
        try{
            $card_id = $request->card_id;       // カードID
            $mode = $request->mode;             // 打刻モード
            $user = new User();
            $work_time = new WorkTime();
            $systemdate = Carbon::now();
            $response = collect();              // 端末の戻り値
            $this->source_mode = '';
            $array_chkAttendance_result = array(Config::get('const.RESULT_CODE.normal'), Config::get('const.RESULT_CODE.normal'));
            // カード情報存在チェック
            $is_exists = DB::table('card_informations')->where('card_idm', $card_id)->exists();
            if($is_exists){
                $user_datas = $user->getUserCardData($card_id);
                if (count($user_datas) > 0) {
                    foreach($user_datas as $user_data) {
                        // chkAttendance implement
                        $array_impl_chkAttendance = array (
                            'user_data' => $user_data,
                            'mode' => $mode,
                            'systemdate' => $systemdate
                        );
                        $array_chkAttendance_result = $this->chkAttendance($array_impl_chkAttendance);
                        if($array_chkAttendance_result[0] == Config::get('const.RESULT_CODE.normal')){
                            $response->put(Config::get('const.PUT_ITEM.result'),Config::get('const.RESULT_CODE.success'));
                            // insertTable implement
                            $array_impl_insertTable = array (
                                'user_data' => $user_data,
                                'mode' => $mode,
                                'card_id' => $card_id,
                                'array_chkAttendance_result' => $array_chkAttendance_result,
                                'systemdate' => $systemdate
                            );
                            $this->insertTable($array_impl_insertTable);
                        } elseif($array_chkAttendance_result[0] == Config::get('const.C018.forget_stamp')) {
                            // エラー追加 20200121
                            $response->put(Config::get('const.PUT_ITEM.result'),Config::get('const.RESULT_CODE.mode_illegal'));
                            // insertTable implement
                            $array_impl_insertTable = array (
                                'user_data' => $user_data,
                                'mode' => $mode,
                                'card_id' => $card_id,
                                'array_chkAttendance_result' => $array_chkAttendance_result,
                                'systemdate' => $systemdate
                            );
                            $this->insertTable($array_impl_insertTable);
                        } elseif($array_chkAttendance_result[0] == Config::get('const.RESULT_CODE.dup_time_check')) {
                            $response->put(Config::get('const.PUT_ITEM.result'),Config::get('const.RESULT_CODE.dup_time_check'));
                        } else {
                            // エラー追加 20200121
                            $response->put(Config::get('const.PUT_ITEM.result'),Config::get('const.RESULT_CODE.unknown'));
                            // insertTable implement
                            $array_impl_insertTable = array (
                                'user_data' => $user_data,
                                'mode' => $mode,
                                'card_id' => $card_id,
                                'array_chkAttendance_result' => $array_chkAttendance_result,
                                'systemdate' => $systemdate
                            );
                            $this->insertTable($array_impl_insertTable);
                        }
                        $response->put(Config::get('const.PUT_ITEM.user_code'),$user_data->code);
                        $response->put(Config::get('const.PUT_ITEM.user_name'),$user_data->name);
                        $response->put(Config::get('const.PUT_ITEM.record_time'),$systemdate->format('H:i:s'));
                        $response->put(Config::get('const.PUT_ITEM.source_mode'),$this->source_mode);
                        break;
                    }
                } else {
                    // エラー追加 20200121
                    $response->put(Config::get('const.PUT_ITEM.result'),Config::get('const.RESULT_CODE.user_not_exsits'));
                }
            }else{  // カード情報が存在しない
                // エラー追加 20200121
                $response->put(Config::get('const.PUT_ITEM.result'),Config::get('const.RESULT_CODE.card_not_exsits'));
            }

            return $response;
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error(str_replace('{0}', 'work_times', Config::get('const.LOG_MSG.data_insert_erorr')));
            Log::error($e->getMessage());
            throw $e;
        }
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
    private function chkAttendance($params){
        // パラメータ
        $user_data = $params['user_data'];
        $mode = $params['mode'];
        $systemdate = $params['systemdate'];

        $apicommon = new ApiCommonController();
        $work_time_model = new WorkTime();
        $work_time_model->setParamDepartmentcodeAttribute($user_data->department_code);
        $work_time_model->setParamUsercodeAttribute($user_data->code);
        $work_time_model->setParamdatefromNoneditAttribute($systemdate->format('Ymd His'));
        $this->source_mode = '';
        // MAX打刻取得
        $chk_result = Config::get('const.RESULT_CODE.normal');
        $chk_max_times = Config::get('const.RESULT_CODE.normal');
        $check_interval = Config::get('const.RESULT_CODE.normal');
        $daily_times = $work_time_model->getDailyMaxData();
        if(count($daily_times) > 0){
            $i=0;
            foreach ($daily_times as $result) {
                // モードチェック
                if(isset($result->mode)){
                    $i += 1;
                    $this->source_mode = $result->mode;
                    // モードが同じでタイム間が5秒以内は登録しない（重複打刻防止のため）
                    if ($mode == $this->source_mode ) {
                        $check_dup = $apicommon->diffSecoundSerial($result->record_datetime, $systemdate );
                        if ($check_dup <= 5) {
                            $chk_result = Config::get('const.RESULT_CODE.dup_time_check');
                        }
                    }

                    if ($chk_result == Config::get('const.RESULT_CODE.normal')) {
                        $chk_result = $apicommon->chkMode($mode, $this->source_mode);
                        if ($chk_result == Config::get('const.RESULT_CODE.normal')) {
                            // 出勤インターバルチェック（緊急はやらない）
                            if ($mode == Config::get('const.C005.attendance_time')) {
                                if ($this->source_mode == Config::get('const.C005.leaving_time')) {
                                    $check_interval = $apicommon->chkInteval($systemdate, $result->record_datetime);
                                }
                            }
                        }
                    }
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

        // 各モード1日最大5回まで(ToDo チェック保留 SQL修正必要)
        /*$work_time_model->setParamDepartmentcodeAttribute($user_data->department_code);
        $work_time_model->setParamUsercodeAttribute($user_data->code);
        $work_time_model->setParamModeAttribute($mode);
        $value_count = $work_time_model->getModeCount();
        Log::debug('chkAttendance value_count = '.$value_count);
        if (isset($value_count)) {
            if ($value_count >= Config::get('const.C019.max_times')) {
                $chk_max_times = Config::get('const.RESULT_CODE.max_times');
            }
        } */

        return array($chk_result,  $chk_max_times,  $check_interval);
    }

    /**
     * 登録
     *
     * @param Request $request
     * @param [type] $id
     * @return void
     */
    private function insertTable($params) {
        // パラメータ
        $user_data = $params['user_data'];
        $mode = $params['mode'];
        $card_id = $params['card_id'];
        $array_chkAttendance_result = $params['array_chkAttendance_result'];
        $systemdate = $params['systemdate'];

        try{
            // 打刻データ登録
            DB::beginTransaction();
            // insertTime implement
            $array_impl_insertTime = array (
                'user_data' => $user_data,
                'mode' => $mode,
                'array_chkAttendance_result' => $array_chkAttendance_result,
                'systemdate' => $systemdate
            );
            $this->insertTime($array_impl_insertTime);
            $array_impl_insertTimeLogs = array (
                'user_data' => $user_data,
                'mode' => $mode,
                'card_id' => $card_id,
                'systemdate' => $systemdate
            );
            $this->insertTimeLogs($array_impl_insertTimeLogs);
            DB::commit();
        }catch(\PDOException $pe){
            DB::rollBack();
            throw $pe;
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 打刻データ登録
     *
     * @param Request $request
     * @param [type] $id
     * @return void
     */
    private function insertTime($params) {
        // パラメータ
        $user_data = $params['user_data'];
        $mode = $params['mode'];
        $array_chkAttendance_result = $params['array_chkAttendance_result'];
        $systemdate = $params['systemdate'];

        try{
            $work_time = new WorkTime();
            $work_time->setUsercodeAttribute($user_data->code);
            $work_time->setDepartmentcodeAttribute($user_data->department_code);
            $work_time->setRecordtimeAttribute($systemdate);
            $work_time->setModeAttribute($mode);
            $work_time->setCheckresultAttribute($array_chkAttendance_result[0]);
            $work_time->setCheckmaxtimeAttribute($array_chkAttendance_result[1]);
            $work_time->setCheckintervalAttribute($array_chkAttendance_result[2]);
            $work_time->setIseditorAttribute(false);
            $work_time->setCreateduserAttribute($user_data->code);
            $work_time->setSystemDateAttribute($systemdate);
            $work_time->insertWorkTime();
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * ログデータ登録
     *
     * @param Request $request
     * @param [type] $id
     * @return void
     */
    private function insertTimeLogs($params) {
        // パラメータ
        $user_data = $params['user_data'];
        $mode = $params['mode'];
        $card_id = $params['card_id'];
        $systemdate = $params['systemdate'];

        try{
            $work_time_log = new WorkTimeLog();
            $work_time_log->setUsercodeAttribute($user_data->code);
            $work_time_log->setDepartmentcodeAttribute($user_data->department_code);
            $work_time_log->setEmploymentstatusAttribute($user_data->employment_status);
            $work_time_log->setRecordtimeAttribute($systemdate);
            $work_time_log->setModeAttribute($mode);
            $work_time_log->setCardidmAttribute($card_id);
            $work_time_log->setCreateduserAttribute($user_data->code);
            $work_time_log->setSystemDateAttribute($systemdate);
            $work_time_log->insertWorkTimeLog();

        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }

}
