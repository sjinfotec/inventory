<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\ShiftInformation;



class ApiCommonController extends Controller
{
    /**
     * ユーザーリスト取得
     *
     * @return void
     */
    public function getUserList(){
        $users = DB::table('users')->where('is_deleted', 0)->orderby('id','asc')->get();
        return $users;
    }

    /**
     * ユーザーのシフト情報取得
     *
     * @return void
     */
    public function getShiftInformation(Request $request){
        $code = $request->code;
        $shift_info = new ShiftInformation();

        $shift_info->setUsercodeAttribute($code);
        $results = $shift_info->getUserShift();

        return $results;
        
    }
}
