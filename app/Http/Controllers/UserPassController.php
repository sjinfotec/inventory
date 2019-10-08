<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\UserModel;
use Carbon\Carbon;

class UserPassController extends Controller
{
    const SUCCESS = 0;
    const FAILED = 1;

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
        $pass_word = bcrypt($request->password);
        $response = collect();
        $systemdate = Carbon::now();
        $user = Auth::user();
        $user_code = $user->code;
        $code = $user_code;

        // パスワード変更
        DB::beginTransaction();
        try{
            $users = new UserModel();
            $users->setCodeAttribute($code);
            $users->setPasswordAttribute($pass_word);
            $users->setUpdateduserAttribute($user_code);
            $users->setUpdatedatAttribute($systemdate);
            $users->updatePassWord();
            DB::commit();
            $response->put('result',self::SUCCESS);

        }catch(\PDOException $pe){
            Log::error($pe->getMessage());
            DB::rollBack();
            $response->put('result',self::FAILED);
        }catch(\Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
            $response->put('result',self::FAILED);
        }
        return $response;

    }
}
