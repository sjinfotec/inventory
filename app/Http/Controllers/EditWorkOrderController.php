<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class EditWorkOrderController extends Controller
{
    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        $authusers = Auth::user();
        $login_user_code = $authusers->code;
        $accountid = $authusers->account_id;

        $indexorhome = 1;       // メニューより起動
        return view('edit_work_order',
            compact(
                'authusers',
                'indexorhome'
            ));
    }
    
    /**
     * ホームページからの初期処理
     *
     * @return void
     */
    public function homeindex()
    {
        // 日次警告アラートリダイレクト
        return redirect()->route('edit_work_order.edithome');
    }

    /**
     * 初期処理
     *
     * @return void
     */
    public function edithome()
    {
        $authusers = Auth::user();
        $login_user_code = $authusers->code;
        $accountid = $authusers->account_id;
        $indexorhome = 2;       // ホームより起動
        return view('edit_work_order',
            compact(
                'authusers',
                'indexorhome'
            ));
    }
    //
}
