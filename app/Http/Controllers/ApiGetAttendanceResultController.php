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
        $collections = collect("");
        // カード情報存在チェック
        $is_exists = DB::table('card_informations')->where('card_idm', $card_id)->exists();
        if($is_exists){
            $user_data = $user->getUserCardData($card_id);
            $user_code = $user_data[0]->{'code'};
            $result = $this->dbConnect($user_code,$mode);
            if($result){
                // 成功
            }else{
                // エラー
            }
        }else{  // カード情報がない

        }

        return $user_data;
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
    private function dbConnect($user_id,$mode){
        $work_time = new WorkTime();
        DB::beginTransaction();
        try{


            DB::commit();
            return true;

        }catch(\PDOException $e){
            DB::rollBack();
            return false;
        }
    }

}
