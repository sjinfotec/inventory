<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EditWorkTimesController extends Controller
{
    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        return view('edit_work_times');
    }

    /** 詳細取得
     *
     * @return list results
     */
    public function get(Request $request){
        $code = $request->code;
        $year = $request->year;
        $month = $request->month;
        $users = new UserModel();
        $users->setCodeAttribute($code);
        $details = $users->getUserDetails();
        foreach ($details as $detail) {
            // $detail->password = dencrypt($detail->password);
        }
        
        return $details;
    }
}
