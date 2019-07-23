<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
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
                if (isset($request->employment)) {
                    $users = DB::table('users')
                        ->where('department_id', $request->code)
                        ->where('employment_status', $request->employment)
                        ->where('is_deleted', 0)
                        ->orderby('name','asc')
                        ->get();
                } else {
                    $users = DB::table('users')
                        ->where('department_id', $request->code)
                        ->where('is_deleted', 0)
                        ->orderby('name','asc')
                        ->get();
                }
            } else {
                if (isset($request->employment)) {
                    $users = DB::table('users')
                        ->where('employment_status', $request->employment)
                        ->where('is_deleted', 0)
                        ->orderby('name','asc')
                        ->get();
                } else {
                    $users = DB::table('users')->where('is_deleted', 0)->orderby('name','asc')->get();
                }
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
        $statuses = DB::table('generalcodes')->where('identification_id', Config::get('const.C001.value'))->where('is_deleted', 0)->orderby('sort_seq','asc')->get();
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
        $businessDays = DB::table('generalcodes')->where('identification_id', Config::get('const.C007.value'))->where('is_deleted', 0)->orderby('sort_seq','asc')->get();
        return $businessDays;
    }
    
    /**
     * 休暇区分リスト取得
     *
     * @return list
     */
    public function getHoliDayList(){
        $holiDays = DB::table('generalcodes')->where('identification_id', Config::get('const.C008.value'))->where('is_deleted', 0)->orderby('sort_seq','asc')->get();
        return $holiDays;
    }

    /**
     * 時間単位リスト取得
     *
     * @return list
     */
    public function getTimeUnitList(){
        $timeUnits = DB::table('generalcodes')->where('identification_id', 'C009')->where('is_deleted', 0)->orderby('sort_seq','asc')->get();
        return $timeUnits;
    }

    /**
     * 時間の丸めリスト取得
     *
     * @return list
     */
    public function getTimeRoundingList(){
        $timeRounds = DB::table('generalcodes')->where('identification_id', 'C010')->where('is_deleted', 0)->orderby('sort_seq','asc')->get();
        return $timeRounds;
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
            $what_weekday['id'] = Config::get('const.C006.sat');
            $what_weekday['week_name'] = Config::get('const.WEEK_KANJI.sat');
        }elseif($dt->isSunday()){
            $what_weekday['id'] = Config::get('const.C006.sun');
            $what_weekday['week_name'] = Config::get('const.WEEK_KANJI.sun');
        }elseif($dt->isMonday()){
            $what_weekday['id'] = Config::get('const.C006.mon');
            $what_weekday['week_name'] = Config::get('const.WEEK_KANJI.mon');
        }elseif($dt->isTuesday()){
            $what_weekday['id'] = Config::get('const.C006.tue');
            $what_weekday['week_name'] = Config::get('const.WEEK_KANJI.tue');
        }elseif($dt->isWednesday()){
            $what_weekday['id'] = Config::get('const.C006.wed');
            $what_weekday['week_name'] = Config::get('const.WEEK_KANJI.wed');
        }elseif($dt->isThursday()){
            $what_weekday['id'] = Config::get('const.C006.thu');
            $what_weekday['week_name'] = Config::get('const.WEEK_KANJI.thu');
        }elseif($dt->isFriday()){
            $what_weekday['id'] = Config::get('const.C006.fri');
            $what_weekday['week_name'] = Config::get('const.WEEK_KANJI.fri');
        }

        return $what_weekday;
    }
}
