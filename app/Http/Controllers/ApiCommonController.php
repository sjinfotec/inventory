<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
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
    
    /**
     * 時間差を求める（時間）
     *
     * @return 時間差
     */
    public function diffTimeTime($time_from, $time_to){
        $from = new Carbon($time_from);
        $to   = new Carbon($time_to); 
        $interval = $from->diff($to);
        // 時間単位の差
        $dif_time = $interval->format('%H:%I:%S');
        Log::DEBUG('dif_time = '.$dif_time);
        return $dif_time;
    }
    
    /**
     * 時間差を求める（シリアルで返却）
     *
     * @return 時間差
     */
    public function diffTimeSerial($time_from, $time_to){
        $from = strtotime($time_from);
        $to   = strtotime($time_to); 
        $interval = $to - $from;
        Log::DEBUG('interval = '.$interval);
        return $interval;
    }
    
    /**
     * 時間丸め処理（シリアルで丸めする）
     *
     * @return 分で返却
     */
    public function roundTime($round_time, $time_unit, $time_rounding){

        if ($time_rounding == Config::get('const.C010.round_half_up')) {
            if ($time_rounding == Config::get('const.C009.round1')) {
                // 分求める
                $result_round_time = round($round_time / 60);
            } elseif ($time_rounding == Config::get('const.C009.round10')) {
                // 分求める
                $result_round_time = round($round_time / 60 / 10) * 10;
            } elseif ($time_rounding == Config::get('const.C009.round15')) {
                // 時間求める（切り捨て）
                $w_time1 = floor($round_time / 60 / 60);
                $w_time2 = $w_time1 * 60;
                // 分の差を求める
                $w_time3 = ($round_time / 60) - $w_time2;
                if ($w_time3 < 8) {
                    $result_round_time = $w_time2;
                } elseif ($w_time3 < 23) {
                    $result_round_time = $w_time2 + 15;
                } elseif ($w_time3 < 38) {
                    $result_round_time = $w_time2 + 30;
                } elseif ($w_time3 < 53) {
                    $result_round_time = $w_time2 + 45;
                } else {
                    $result_round_time = $w_time2 + 60;
                }
            } elseif ($time_rounding == Config::get('const.C009.round30')) {
                // 時間求める（切り捨て）
                $w_time1 = floor($round_time / 60 / 60);
                $w_time2 = $w_time1 * 60;
                // 分の差を求める
                $w_time3 = ($round_time / 60) - $w_time2;
                if ($w_time3 < 15) {
                    $result_round_time = $w_time2;
                } elseif ($w_time3 < 45) {
                    $result_round_time = $w_time2 + 30;
                } else {
                    $result_round_time = $w_time2 + 60;
                }
            } elseif ($time_rounding == Config::get('const.C009.round60')) {
                // 時間求める（切り捨て）
                $w_time1 = floor($round_time / 60 / 60);
                $w_time2 = $w_time1 * 60;
                // 分の差を求める
                $w_time3 = ($round_time / 60) - $w_time2;
                if ($w_time3 < 30) {
                    $result_round_time = $w_time2;
                } else {
                    $result_round_time = $w_time2 + 60;
                }
            }
        } elseif ($time_rounding == Config::get('const.C010.round_down')) {
            if ($time_rounding == Config::get('const.C009.round1')) {
                // 分求める
                $result_round_time = floor($round_time / 60);
            } elseif ($time_rounding == Config::get('const.C009.round10')) {
                // 分求める
                $result_round_time = floor($round_time / 60 / 10) * 10;
            } elseif ($time_rounding == Config::get('const.C009.round15')) {
                // 時間求める（切り捨て）
                $w_time1 = floor($round_time / 60 / 60);
                $w_time2 = $w_time1 * 60;
                // 分の差を求める
                $w_time3 = ($round_time / 60) - $w_time2;
                if ($w_time3 < 15) {
                    $result_round_time = $w_time2;
                } elseif ($w_time3 < 30) {
                    $result_round_time = $w_time2 + 15;
                } elseif ($w_time3 < 45) {
                    $result_round_time = $w_time2 + 30;
                } elseif ($w_time3 < 60) {
                    $result_round_time = $w_time2 + 45;
                } else {
                    $result_round_time = $w_time2 + 60;
                }
            } elseif ($time_rounding == Config::get('const.C009.round30')) {
                // 時間求める（切り捨て）
                $w_time1 = floor($round_time / 60 / 60);
                $w_time2 = $w_time1 * 60;
                // 分の差を求める
                $w_time3 = ($round_time / 60) - $w_time2;
                if ($w_time3 < 30) {
                    $result_round_time = $w_time2;
                } elseif ($w_time3 < 60) {
                    $result_round_time = $w_time2 + 30;
                } else {
                    $result_round_time = $w_time2 + 60;
                }
            } elseif ($time_rounding == Config::get('const.C009.round60')) {
                // 時間求める（切り捨て）
                $w_time1 = floor($round_time / 60 / 60);
                $w_time2 = $w_time1 * 60;
                // 分の差を求める
                $w_time3 = ($round_time / 60) - $w_time2;
                if ($w_time3 < 60) {
                    $result_round_time = $w_time2;
                } else {
                    $result_round_time = $w_time2 + 60;
                }
            }
        } elseif ($time_rounding == Config::get('const.C010.round_up')) {
            if ($time_rounding == Config::get('const.C009.round1')) {
                // 分求める
                $result_round_time = ceil($round_time / 60);
            } elseif ($time_rounding == Config::get('const.C009.round10')) {
                // 分求める
                $result_round_time = ceil($round_time / 60 / 10) * 10;
            } elseif ($time_rounding == Config::get('const.C009.round15')) {
                // 時間求める（切り捨て）
                $w_time1 = floor($round_time / 60 / 60);
                $w_time2 = $w_time1 * 60;
                // 分の差を求める
                $w_time3 = ($round_time / 60) - $w_time2;
                if ($w_time3 < 15) {
                    $result_round_time = $w_time2 + 15;
                } elseif ($w_time3 < 30) {
                    $result_round_time = $w_time2 + 30;
                } elseif ($w_time3 < 45) {
                    $result_round_time = $w_time2 + 45;
                } elseif ($w_time3 < 60) {
                    $result_round_time = $w_time2 + 60;
                } else {
                    $result_round_time = $w_time2 + 60;
                }
            } elseif ($time_rounding == Config::get('const.C009.round30')) {
                // 時間求める（切り捨て）
                $w_time1 = floor($round_time / 60 / 60);
                $w_time2 = $w_time1 * 60;
                // 分の差を求める
                $w_time3 = ($round_time / 60) - $w_time2;
                if ($w_time3 < 30) {
                    $result_round_time = $w_time2 + 30;
                } elseif ($w_time3 < 60) {
                    $result_round_time = $w_time2 + 60;
                } else {
                    $result_round_time = $w_time2 + 60;
                }
            } elseif ($time_rounding == Config::get('const.C009.round60')) {
                // 時間求める（切り捨て）
                $w_time1 = floor($round_time / 60 / 60);
                $w_time2 = $w_time1 * 60;
                // 分の差を求める
                $w_time3 = ($round_time / 60) - $w_time2;
                if ($w_time3 < 60) {
                    $result_round_time = $w_time2 + 60;
                } else {
                    $result_round_time = $w_time2 + 60;
                }
            }
        } elseif ($time_rounding == Config::get('const.C010.non')) {
            $result_round_time = $round_time / 60;
        } else {
            $result_round_time = $round_time / 60;
            Log::DEBUG(Config::get('const.LOG_MSG.not_set_time_rounding'));
        }

        return $result_round_time;
    }

}
