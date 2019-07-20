<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\ShiftInformation;
use App\WorkingTimeTable;



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
        foreach ($results as $item) {
            if(isset($item->target_date)){
                $date = new Carbon($item->target_date);
                $item->target_date = $date->format('Y/m/d');
            }
        }

        return $results;
    }
        
    /** 部署リスト取得
     *
     * @return list departments
     */
    public function getDepartmentList(){
        $departments = DB::table('departments')->select('id','name')->where('is_deleted', 0)->orderby('id','asc')->get();
        return $departments;
    }

    /** 雇用形態リスト取得
     *
     * @return list statuses
     */
    public function getEmploymentStatusList(){
        $statuses = DB::table('generalcodes')->where('identification_id', 'C001')->where('is_deleted', 0)->orderby('sort_seq','asc')->get();
        return $statuses;
    }

    /**
     * リスト取得
     *
     * @return list
     */
    public function getTimeTableList(){
        $time_tables = new WorkingTimeTable();
        $results = $time_tables->getTimeTables();
        return $results;
    }

    /**
     * 営業日区分リスト取得
     *
     * @return list
     */
    public function getBusinessDayList(){
        $businessDays = DB::table('generalcodes')->where('identification_id', 'C007')->where('is_deleted', 0)->orderby('sort_seq','asc')->get();
        return $businessDays;
    }
    
    /**
     * 休暇区分リスト取得
     *
     * @return list
     */
    public function getHoliDayList(){
        $holiDays = DB::table('generalcodes')->where('identification_id', 'C008')->where('is_deleted', 0)->orderby('sort_seq','asc')->get();
        return $holiDays;
    }

    /**
     * 曜日取得
     *
     * @param [type] $dt
     * @param [type] $format
     * @return array
     */
    public function getWeekDay($dt,$format){
        // 週末の定義
        $dt->setWeekendDays([
            Carbon::MONDAY,
            Carbon::TUESDAY,
            Carbon::WEDNESDAY,
            Carbon::THURSDAY,
            Carbon::FRIDAY,
            Carbon::SATURDAY,
            Carbon::SUNDAY,
        ]);
        
        $what_weekday = array();
        $target_format = $dt->format($format);
        $what_weekday['date'] = $target_format;
        // $is_holiday = JpCarbon::createFromDate($dt->year,$dt->month,$dt->day)->holiday;   //祝日以外は""を返す

        if($dt->isSaturday()){
            $what_weekday['id'] = 6;
            $what_weekday['week_name'] = '(土)';
        }elseif($dt->isSunday()){
            $what_weekday['id'] = 0;
            $what_weekday['week_name'] = '(日)';
        }elseif($dt->isMonday()){
            $what_weekday['id'] = 1;
            $what_weekday['week_name'] = '(月)';
        }elseif($dt->isTuesday()){
            $what_weekday['id'] = 2;
            $what_weekday['week_name'] = '(火)';
        }elseif($dt->isWednesday()){
            $what_weekday['id'] = 3;
            $what_weekday['week_name'] = '(水)';
        }elseif($dt->isThursday()){
            $what_weekday['id'] = 4;
            $what_weekday['week_name'] = '(木)';
        }elseif($dt->isFriday()){
            $what_weekday['id'] = 5;
            $what_weekday['week_name'] = '(金)';
        }

        return $what_weekday;
    }
}
