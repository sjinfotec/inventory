<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\User;
use App\CardInformation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class ApiCardRegisterController extends Controller
{

    /**
     * ユーザー取得(ListView表示用)
     *
     * @param Request $request
     * @return void
     */
    public function index(){
        $user = new User();
        $result = '';
        $response = collect();              // 端末の戻り値
        $lists = $user->getNotRegistUser();
        if(count($lists) > 0){
            $result = Config::get('const.RESULT_CODE.success');
        } else {         // 該当ユーザーがいない
            $result = Config::get('const.RESULT_CODE.user_not_exsits');
        } 

        return response()->json([Config::get('const.PUT_ITEM.result') => $result, Config::get('const.PUT_ITEM.listresult') => $lists]);
    }

    /**
     * カード新規登録
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request) { 
        $card_id = $request->card_id;               // カードID
        $user_code = $request->user_code;           // ユーザーコード
        $department_code = $request->department_code;   // 部署ID
        $user = new User();
        $card_info = new CardInformation();
        $systemdate = Carbon::now();
        $result = '';
        $response = collect();              // 端末の戻り値
        // 存在チェック
        $card_info->setCardIdmAttribute($card_id);
        $is_card_exists = $card_info->isCardInfoExists();
        if($is_card_exists){
            // 既にcard_idと紐づいたユーザーが存在する
            $response->put(Config::get('const.PUT_ITEM.result'),Config::get('const.RESULT_CODE.already_data'));
            Log::debug('already_data'.Config::get('const.LOG_MSG.already_data'));
        }else{
            // 新規登録
            DB::beginTransaction();
            try{
                $result = $this->insCardInfo($user_code,$card_id,$department_code);
                $user_datas = $user->getUserCardData($card_id);
                if (count($user_datas) > 0) {
                    DB::commit();
                    $response->put(Config::get('const.PUT_ITEM.result'),Config::get('const.RESULT_CODE.success'));
                    foreach ($user_datas as $user_data) {
                        $response->put(Config::get('const.PUT_ITEM.user_code'),$user_data->code);
                        $response->put(Config::get('const.PUT_ITEM.user_name'),$user_data->name);
                        break;
                    }
                } else {
                    DB::rollBack();
                    $response->put(Config::get('const.PUT_ITEM.result'),Config::get('const.RESULT_CODE.data_select_erorr'));
                    Log::error(str_replace('{0}', 'users', Config::get('const.LOG_MSG.data_select_erorr')));
                }
            }catch(\PDOException $pe){
                DB::rollBack();
                $response->put(Config::get('const.PUT_ITEM.result'),Config::get('const.RESULT_CODE.insert_error'));

            }catch(\Exception $e){
                DB::rollBack();
                $response->put(Config::get('const.PUT_ITEM.result'),Config::get('const.RESULT_CODE.insert_error'));
                Log::error(str_replace('{0}', 'card_informations', Config::get('const.LOG_MSG.data_insert_erorr')));
                Log::error($e->getMessage());
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
    private function insCardInfo($user_code,$card_id,$department_code){
        Log::error("insCardInfo".$card_id);
        $card_info = new CardInformation();
        $systemdate = Carbon::now();
        $login_user = Auth::user();
        try{
            Log::debug('insCardInfo $user_code = '.$user_code );
            Log::debug('insCardInfo $card_id = '.$card_id );
            Log::debug('insCardInfo $department_code = '.$department_code );
            $card_info->setUserCodeAttribute($user_code);
            $card_info->setDepartmentcodeAttribute($department_code);
            $card_info->setCardIdmAttribute($card_id);
            $card_info->setCreatedUserAttribute($login_user);
            $card_info->setSystemDateAttribute($systemdate);
            $card_info->insertCardInfo();

            return true;

        }catch(\PDOException $pe){
            Log::error(str_replace('{0}', 'card_informations', Config::get('const.LOG_MSG.data_insert_erorr')));
            Log::error($pe->getMessage());

        }catch(\Exception $e){
            Log::error(str_replace('{0}', 'card_informations', Config::get('const.LOG_MSG.data_insert_erorr')));
            Log::error($e->getMessage());
        }
    }

}
