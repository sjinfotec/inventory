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
        $order_no = "$";
        $row_seq = "$";

        $indexorhome = 1;       // メニューより起動
        return view('edit_work_order',
            compact(
                'authusers',
                'order_no',
                'row_seq',
                'indexorhome'
            ));
    }
    
    /**
     * ホームページからの初期処理
     *
     * @return void
     */
    public function homeindex(Request $request)
    {
        // 日次警告アラートリダイレクト
        return redirect()->route('edit_work_order.edithome', [
            'order_no' => $_GET["order_no"],
            'row_seq' => $_GET["row_seq"]
        ]);
    }

    /**
     * 初期処理
     *
     * @return void
     */
    public function edithome(Request $request)
    {
        $order_no = null;
        $row_seq = null;
        if (isset($request->order_no)) {
            $order_no = $request->order_no;
        }
        if (isset($request->row_seq)) {
            $row_seq = $request->row_seq;
        }
        $authusers = Auth::user();
        $login_user_code = $authusers->code;
        $accountid = $authusers->account_id;
        $indexorhome = 2;       // ホームより起動
        return view('edit_work_order',
            compact(
                'authusers',
                'order_no',
                'row_seq',
                'indexorhome'
            ));
    }
    //
}
