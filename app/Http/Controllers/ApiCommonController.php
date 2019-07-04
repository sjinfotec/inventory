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
     * @param  Request  getdo 0:取得しない、1:取得する
     * @return list users
     */
    public function getUserList(Request $request){

        $getdo = $request->getdo;
        if (!isset($getdo)) { return null; }

        if ($getdo == 1) {
            if (isset($request->code)) {
                $users = DB::table('users')->where('department_code', $request->code)->where('is_deleted', 0)->orderby('name','asc')->get();
            } else {
                $users = DB::table('users')->where('is_deleted', 0)->orderby('name','asc')->get();
            }
        } else {
            return null;
        }
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
        
    /** 部署リスト取得
     *
     * @return list departments
     */
    public function getDepartmentList(){
        $departments = DB::table('departments')->where('is_deleted', 0)->orderby('code','asc')->get();
        return $departments;
    }
}