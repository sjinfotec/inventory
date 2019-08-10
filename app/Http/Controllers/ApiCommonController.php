<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
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

        // パラメータチェック　getdoは必須
        if (!isset($request->getdo)) { return null; }
        $getdo = $request->getdo;
        if (!isset($getdo)) { return null; }
        // ログインユーザの権限取得
        $chk_user_id = Auth::user()->id;
        $role = $this->getUserRole($chk_user_id);
        if(!isset($role)) { return null; }
        // 適用期間日付の取得
        $dt = null;
        if (isset($request->targetdate)) {
            $dt = new Carbon($request->targetdate);
        } else {
            $dt = new Carbon();
        }
        $target_date = $dt->format('Ymd');
        $max_date = DB::table('users')->where('apply_term_from', '<=',$target_date)->max('apply_term_from');
        if (!isset($max_date)) { return null; }

        if ($getdo == 1) {
            if (isset($request->code)) {
                if (isset($request->employment)) {
                    $mainQuery = DB::table('users')
                        ->where('department_code', $request->code)
                        ->where('employment_status', $request->employment)
                        ->where('apply_term_from', '=',$max_date);
                    if($role < 8){
                        $mainQuery->where('id','=',$chk_user_id);
                    }
                    $users = $mainQuery->where('is_deleted', 0)
                        ->orderby('code','asc')
                        ->get();
                } else {
                    Log::DEBUG('$max_date2= '.$max_date);
                    $mainQuery = DB::table('users')
                        ->where('department_code', $request->code)
                        ->where('apply_term_from', '=',$max_date);
                    if($role < 8){
                        $mainQuery->where('id','=',$chk_user_id);
                    }
                    $users = $mainQuery->where('is_deleted', 0)
                        ->orderby('code','asc')
                        ->get();
                }
            } else {
                Log::DEBUG('$max_date3 = '.$max_date);
                if (isset($request->employment)) {
                    $mainQuery = DB::table('users')
                        ->where('employment_status', $request->employment)
                        ->where('apply_term_from', '=',$max_date);
                    if($role < 8){
                        $mainQuery->where('id','=',$chk_user_id);
                    }
                    $users = $mainQuery->where('is_deleted', 0)
                        ->orderby('code','asc')
                        ->get();
                } else {
                    Log::DEBUG('$max_date4 = '.$max_date);
                    $mainQuery = DB::table('users')
                        ->where('apply_term_from', '=',$max_date);
                    if($role < 8){
                        $mainQuery->where('id','=',$chk_user_id);
                    }
                    $users = $mainQuery->where('is_deleted', 0)->get();
                }
            }
        } else {
            return null;
        }

        Log::DEBUG('$max_date5 = '.$max_date);
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
    public function getDepartmentList(Request $request){
        // ログインユーザの権限取得
        $chk_user_id = Auth::user()->id;
        $role = $this->getUserRole($chk_user_id);
        if(!isset($role)) { return null; }
        // 適用期間日付の取得
        $dt = null;
        if (isset($request->targetdate)) {
            $dt = new Carbon($request->targetdate);
        } else {
            $dt = new Carbon();
        }
        $target_date = $dt->format('Ymd');
        $max_date = DB::table('users')->where('apply_term_from', '<=',$target_date)->max('apply_term_from');
        if (!isset($max_date)) { return null; }

        if($role < 8){
            $departments = DB::table('departments')
                ->Join('users', function ($join) { 
                    $join->on('users.department_code', '=', 'departments.id')
                    ->where('users.is_deleted', '=', 0);
                })
                ->select('departments.id','departments.name')
                ->where('apply_term_from', '=',$max_date)
                ->where('users.id','=',$chk_user_id)
                ->where('departments.is_deleted', 0)
                ->orderby('departments.id','asc')
                ->get();
        } else {
            $departments = DB::table('departments')
                ->select('code','name')
                ->where('apply_term_from', '=',$max_date)
                ->where('is_deleted', 0)
                ->orderby('code','asc')
                ->get();
        }
        return $departments;
    }
        
    /** ユーザー権限取得（画面から）
     *
     * @return list departments
     */
    public function getLoginUserRole(){
        // ログインユーザの権限取得
        Log::DEBUG('$getLoginUserRole in ');
        $chk_user_id = Auth::user()->id;
        $role = $this->getUserRole($chk_user_id);
        Log::DEBUG('$getLoginUserRole end ');
        return $role;
    }
        
    /** ユーザー権限取得
     *
     * @return list departments
     */
    public function getUserRole($user_id){
        // ユーザの権限取得
        Log::DEBUG('$getUserRole in ');
        $role = DB::table('users')->where('id','=',''.$user_id)->where('is_deleted', 0)->value('role');     
        if(!isset($role)) { return null; }
        Log::DEBUG('$getUserRole end ');
        return $role;
    }

    /** 雇用形態リスト取得
     *
     * @return list statuses
     */
    public function getEmploymentStatusList(){
        Log::DEBUG('$getEmploymentStatusList in ');
        $statuses = DB::table('generalcodes')->where('identification_id', Config::get('const.C001.value'))->where('is_deleted', 0)->orderby('sort_seq','asc')->get();
        Log::DEBUG('$getEmploymentStatusList end ');
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
     * 個人休暇リスト取得
     *
     * @return list
     */
    public function getUserLeaveKbnList(){
        $userLeaveKbnList = DB::table('generalcodes')->where('identification_id', 'C013')->where('is_deleted', 0)->orderby('sort_seq','asc')->get();
        return $userLeaveKbnList;
    }

    /**
     * モードリスト取得
     *
     * @return list
     */
    public function getModeList(){
        $modeList = DB::table('generalcodes')->where('identification_id', 'C005')->where('is_deleted', 0)->orderby('sort_seq','asc')->get();
        return $modeList;
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
     * 翌日を求める
     *
     * @return 翌日
     */
    public function getNextDay($target_date, $str_format){
        $dt = new Carbon($target_date);
        return date_format($dt->addDay(), $str_format);
    }

    /**
     * 時刻日付変換
     *      target_time <= basic_time の場合は basic_dateの翌日+target_time に変換
     *      target_time >  basic_time の場合は basic_date +target_time に変換
     * @return 日付時刻
     */
    public function convTimeToDate($target_time, $basic_time, $basic_date){
        // 日付付与
        $convDateTime = null;
        $dt = new Carbon($basic_time);
        if ($target_time <= date_format($dt, 'H:i:s')) {
            $dt = new Carbon($target_time);
            $convDateTime = $this->getNextDay($basic_date, 'Y-m-d').' '.date_format($dt, 'H:i:s');
        } else {
            $convDateTime = $basic_date.' '.$target_time;
        }

        return $convDateTime;
    }

    /**
     * 時間差を求める（時間）
     *
     * @return 時間差
     */
    public function diffTimeTime($time_from, $time_to){
        $from = strtotime(date('Y-m-d H:i:00',strtotime($time_from)));
        $to   = strtotime(date('Y-m-d H:i:00',strtotime($time_to))); 
        $from = new Carbon($from);
        $to   = new Carbon($to); 
        $interval = $from->diff($to);
        Log::DEBUG('diffTimeTime interval = '.$interval);
        // 時間単位の差
        $dif_time = $interval->format('%H:%I:%S');
        return $dif_time;
    }
    
    /**
     * 時間差を求める（シリアルで返却）
     *
     * @return 時間差
     */
    public function diffTimeSerial($time_from, $time_to){
        $from = strtotime(date('Y-m-d H:i:00',strtotime($time_from)));
        $to   = strtotime(date('Y-m-d H:i:00',strtotime($time_to))); 
        Log::DEBUG('diffTimeSerial from = '.$from);
        Log::DEBUG('diffTimeSerial to = '.$to);
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
            // 四捨五入
            if ($time_rounding == Config::get('const.C009.round1')) {
                // 分求める
                $result_round_time = round($round_time / 60);
            } elseif ($time_rounding == Config::get('const.C009.round5')) {
                // 切り捨てて時間求める
                $w_time1 = floor($round_time / 60 / 60);
                $w_time2 = $w_time1 * 60;
                // 分の差を求める
                $w_time3 = ($round_time / 60) - $w_time2;
                if ($w_time3 < 3) {
                    $result_round_time = $w_time2;
                } elseif ($w_time3 < 8) {
                    $result_round_time = $w_time2 + 5;
                } elseif ($w_time3 < 13) {
                    $result_round_time = $w_time2 + 10;
                } elseif ($w_time3 < 18) {
                    $result_round_time = $w_time2 + 15;
                } elseif ($w_time3 < 23) {
                    $result_round_time = $w_time2 + 20;
                } elseif ($w_time3 < 28) {
                    $result_round_time = $w_time2 + 25;
                } elseif ($w_time3 < 33) {
                    $result_round_time = $w_time2 + 30;
                } elseif ($w_time3 < 38) {
                    $result_round_time = $w_time2 + 35;
                } elseif ($w_time3 < 43) {
                    $result_round_time = $w_time2 + 40;
                } elseif ($w_time3 < 48) {
                    $result_round_time = $w_time2 + 45;
                } elseif ($w_time3 < 53) {
                    $result_round_time = $w_time2 + 50;
                } elseif ($w_time3 < 58) {
                    $result_round_time = $w_time2 + 55;
                } else {
                    $result_round_time = $w_time2 + 60;
                }
            } elseif ($time_rounding == Config::get('const.C009.round10')) {
                // 分求める
                $result_round_time = round($round_time / 60 / 10) * 10;
            } elseif ($time_rounding == Config::get('const.C009.round15')) {
                // 切り捨てて時間求める
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
                // 切り捨てて時間求める
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
                // 切り捨てて時間求める
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
            // 切り捨て
            if ($time_rounding == Config::get('const.C009.round1')) {
                // 分求める
                $result_round_time = floor($round_time / 60);
            } elseif ($time_rounding == Config::get('const.C009.round5')) {
                // 切り捨てて時間求める
                $w_time1 = floor($round_time / 60 / 60);
                $w_time2 = $w_time1 * 60;
                // 分の差を求める
                $w_time3 = ($round_time / 60) - $w_time2;
                if ($w_time3 < 5) {
                    $result_round_time = $w_time2;
                } elseif ($w_time3 < 10) {
                    $result_round_time = $w_time2 + 5;
                } elseif ($w_time3 < 15) {
                    $result_round_time = $w_time2 + 10;
                } elseif ($w_time3 < 20) {
                    $result_round_time = $w_time2 + 15;
                } elseif ($w_time3 < 25) {
                    $result_round_time = $w_time2 + 20;
                } elseif ($w_time3 < 30) {
                    $result_round_time = $w_time2 + 25;
                } elseif ($w_time3 < 35) {
                    $result_round_time = $w_time2 + 30;
                } elseif ($w_time3 < 40) {
                    $result_round_time = $w_time2 + 35;
                } elseif ($w_time3 < 45) {
                    $result_round_time = $w_time2 + 40;
                } elseif ($w_time3 < 50) {
                    $result_round_time = $w_time2 + 45;
                } elseif ($w_time3 < 55) {
                    $result_round_time = $w_time2 + 50;
                } elseif ($w_time3 < 60) {
                    $result_round_time = $w_time2 + 55;
                } else {
                    $result_round_time = $w_time2 + 60;
                }
            } elseif ($time_rounding == Config::get('const.C009.round10')) {
                // 分求める
                $result_round_time = floor($round_time / 60 / 10) * 10;
            } elseif ($time_rounding == Config::get('const.C009.round15')) {
                // 切り捨てて時間求める
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
                // 切り捨てて時間求める
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
                // 切り捨てて時間求める
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
            // 切り上げ
            if ($time_rounding == Config::get('const.C009.round1')) {
                // 分求める
                $result_round_time = ceil($round_time / 60);
            } elseif ($time_rounding == Config::get('const.C009.round5')) {
                // 切り捨てて時間求める
                $w_time1 = floor($round_time / 60 / 60);
                $w_time2 = $w_time1 * 60;
                // 分の差を求める
                $w_time3 = ($round_time / 60) - $w_time2;
                if ($w_time3 < 5) {
                    $result_round_time = $w_time2 + 5;
                } elseif ($w_time3 < 10) {
                    $result_round_time = $w_time2 + 10;
                } elseif ($w_time3 < 15) {
                    $result_round_time = $w_time2 + 15;
                } elseif ($w_time3 < 20) {
                    $result_round_time = $w_time2 + 20;
                } elseif ($w_time3 < 25) {
                    $result_round_time = $w_time2 + 25;
                } elseif ($w_time3 < 30) {
                    $result_round_time = $w_time2 + 30;
                } elseif ($w_time3 < 35) {
                    $result_round_time = $w_time2 + 35;
                } elseif ($w_time3 < 40) {
                    $result_round_time = $w_time2 + 40;
                } elseif ($w_time3 < 45) {
                    $result_round_time = $w_time2 + 45;
                } elseif ($w_time3 < 50) {
                    $result_round_time = $w_time2 + 50;
                } elseif ($w_time3 < 55) {
                    $result_round_time = $w_time2 + 55;
                } elseif ($w_time3 < 60) {
                    $result_round_time = $w_time2 + 60;
                } else {
                    $result_round_time = $w_time2 + 60;
                }
            } elseif ($time_rounding == Config::get('const.C009.round10')) {
                // 分求める
                $result_round_time = ceil($round_time / 60 / 10) * 10;
            } elseif ($time_rounding == Config::get('const.C009.round15')) {
                // 切り捨てて時間求める
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
                // 切り捨てて時間求める
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
                // 切り捨てて時間求める
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
            // なし
            $result_round_time = $round_time / 60;
            Log::DEBUG('$round_time'.$round_time);
            Log::DEBUG('$result_round_time'.$result_round_time);
        } else {
            $result_round_time = $round_time / 60;
            Log::DEBUG(Config::get('const.LOG_MSG.not_set_time_rounding'));
        }

        return $result_round_time;
    }
    
    /**
     * モードのチェック
     *
     * @return チェック結果
     */
    public function chkMode($target_mode, $source_mode){

        if ($target_mode == Config::get('const.C005.attendance_time')) {
            if ($source_mode == Config::get('const.C005.leaving_time')) {
                return true;
            }
        } elseif ($target_mode == Config::get('const.C005.leaving_time')) {
            if ($source_mode == Config::get('const.C005.attendance_time')) {
                return true;
            }
            if ($source_mode == Config::get('const.C005.missing_middle_return_time')) {
                return true;
            }
        } elseif ($target_mode == Config::get('const.C005.missing_middle_time')) {
            if ($source_mode == Config::get('const.C005.attendance_time')) {
                return true;
            }
            if ($source_mode == Config::get('const.C005.missing_middle_return_time')) {
                return true;
            }
        } elseif ($target_mode == Config::get('const.C005.missing_middle_return_time')) {
            if ($source_mode == Config::get('const.C005.missing_middle_time')) {
                return true;
            }
        } else {
            return false;
        }
        return false;
    }

}
