<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\User;
use App\WorkTime;
use Carbon\Carbon;

class ApiGetAttendanceResultController extends Controller
{
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
        $card_id = $request->card_id;
        $mode = $request->mode;
        $user = new User();
        $work_time = new WorkTime();
        $systemdate = Carbon::now();
        $response = collect();
        // カード情報存在チェック
        $is_exists = DB::table('card_informations')->where('card_idm', $card_id)->exists();
        if($is_exists){
            $user_data = $user->getUserCardData($card_id);
            $user_code = $user_data[0]->{'code'};
            $result = $this->dbConnect($user_code,$mode);
            if($result){
                $response->put('result','OK');
                $response->put('user_name',$user_data[0]->{'name'});
                $response->put('user_code',$user_code);
                $response->put('record_time',$systemdate->format('H:i:s'));
            }else{
                // エラー
            }
        }else{  // カード情報がない

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
     * 勤怠登録
     *
     * @param [type] $user_id
     * @param [type] $mode
     * @return void
     */
    private function dbConnect($user_code,$mode){
        $work_time = new WorkTime();
        $systemdate = Carbon::now();
        DB::beginTransaction();
        try{
            $work_time->setUserCodeAttribute($user_code);
            $work_time->setModeAttribute($mode);
            $work_time->setSystemDateAttribute($systemdate);
            $work_time->insertWorkTime();

            DB::commit();
            return true;

        }catch(\PDOException $e){
            DB::rollBack();
            return false;
        }
    }

}
