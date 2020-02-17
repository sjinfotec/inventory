<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\UserModel;
use Carbon\Carbon;

class UserPassController extends Controller
{

    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        return view('user_pass');
    }

    /**
     * パスワード変更
     *
     * @return void
     */
    public function passChange(Request $request){
        $this->array_messagedata = array();
        $details = array();
        $result = true;
        // パスワード変更
        DB::beginTransaction();
        try {
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                DB::rollBack();
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            if (!isset($params['user_id'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "user_id", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                DB::rollBack();
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            if (!isset($params['pass_word'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "pass_word", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                DB::rollBack();
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $pass_word = bcrypt($params['pass_word']);
            $code = $params['user_id'];
            $systemdate = Carbon::now();
            $user = Auth::user();
            $user_code = $user->code;
            $users = new UserModel();
            $users->setCodeAttribute($code);
            $users->setPasswordAttribute($pass_word);
            $users->setUpdateduserAttribute($user_code);
            $users->setUpdatedatAttribute($systemdate);
            $users->updatePassWord();
            DB::commit();
            return response()->json(
                ['result' => $result,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            DB::rollBack();
            return response()->json(
                ['result' => false,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\Exception $e){
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json(
                ['result' => false,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }

    }
}
