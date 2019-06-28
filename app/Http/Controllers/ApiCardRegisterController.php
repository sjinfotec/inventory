<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\User;
use App\CardInformation;
use Carbon\Carbon;


class ApiCardRegisterController extends Controller
{
    const REGISTRATION_SUCCESS = 11;
    const REGISTRATION_FAILED = 12;
    const REGISTRATION_ERROR = 13;

    /**
     * ユーザー取得(ListView表示用)
     *
     * @param Request $request
     * @return void
     */
    public function index(){
        $user = new User();
        $lists = $user->getNotRegistUser();
        $collections = collect();
        foreach ($lists as $list) {
            // カードIDが紐づいていないユーザーのみ格納
            if(!isset($list->card_idm)){
                $collections->push($list);
            }
        }
        return $collections;
    }

    /**
     * カード新規登録
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request) { 
        $card_id = $request->card_id;       // カードID
        $user_code = $request->user_code;   // ユーザーコード
        $user = new User();
        $systemdate = Carbon::now();
        $response = collect();              // 端末の戻り値
        // 存在チェック
        $is_card_exists = DB::table('card_informations')->where('card_idm', $card_id)->exists();
        if($is_card_exists){
            // 既にcard_idと紐づいたユーザーが存在する
            $response->put('result',self::REGISTRATION_FAILED);
        }else{
            // 新規登録
            $result = $this->dbConnectInsert($user_code,$card_id);
            if($result){
                $user_name = DB::table('users')->where('code', $user_code)->value('name');
                $response->put('result',self::REGISTRATION_SUCCESS);
                $response->put('user_code',$user_code);
                $response->put('user_name',$user_name);
            }else{
                $response->put('result',self::REGISTRATION_ERROR);
            }
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
     * DB書き込み(新規)
     *
     * @param [type] $user_id
     * @param [type] $mode
     * @return void
     */
    private function dbConnectInsert($user_code,$card_id){
        $card_info = new CardInformation();
        $systemdate = Carbon::now();
        DB::beginTransaction();
        try{
            $card_info->setUserCodeAttribute($user_code);
            $card_info->setCardIdmAttribute($card_id);
            $card_info->setSystemDateAttribute($systemdate);
            $card_info->insertCardInfo();

            DB::commit();
            return true;

        }catch(\PDOException $e){
            DB::rollBack();
            return false;
        }
    }

}
