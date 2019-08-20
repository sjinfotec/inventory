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

    protected $table_generalcodes = 'generalcodes';

    /**
     * ユーザーリスト取得
     *
     * @param  Request  getdo 0:取得しない、1:取得する
     * @return list users
     */
    public function getUserList(Request $request){

        Log::DEBUG('$getUserList param= $request->getdo '.$request->getdo);
        Log::DEBUG('$getUserList param= $request->targetdate '.$request->targetdate);
        // パラメータチェック　getdoは必須
        if (!isset($request->getdo)) { return null; }
        $getdo = $request->getdo;
        if (!isset($getdo)) { return null; }
        // 適用期間日付の取得
        $dt = null;
        if (isset($request->targetdate)) {
            $dt = new Carbon($request->targetdate);
        } else {
            $dt = new Carbon();
        }
        $target_date = $dt->format('Ymd');
        // ログインユーザの権限取得
        $chk_user_id = Auth::user()->code;
        $role = $this->getUserRole($chk_user_id, $target_date);
        if(!isset($role)) { return null; }

        $subquery1 = DB::table('users')
            ->selectRaw('MAX(apply_term_from) as max_apply_term_from')
            ->selectRaw('code as code')
            ->where('apply_term_from', '<=',$target_date)
            ->where('is_deleted', '=', 0)
            ->groupBy('code');

        if ($getdo == 1) {
            if (isset($request->code)) {
                if (isset($request->employment)) {
                    $mainQuery = DB::table('users')
                        ->JoinSub($subquery1, 't1', function ($join) { 
                            $join->on('t1.code', '=', 'users.code');
                            $join->on('t1.max_apply_term_from', '=', 'users.apply_term_from');
                        })
                        ->where('users.department_code', $request->code)
                        ->where('users.employment_status', $request->employment);
                    if($role < 8){
                        $mainQuery->where('users.code','=',$chk_user_id);
                    }
                    $users = $mainQuery->where('users.is_deleted', 0)
                        ->orderby('users.code','asc')
                        ->get();
                } else {
                    $mainQuery = DB::table('users')
                        ->JoinSub($subquery1, 't1', function ($join) { 
                            $join->on('t1.code', '=', 'users.code');
                            $join->on('t1.max_apply_term_from', '=', 'users.apply_term_from');
                        })
                        ->where('users.department_code', $request->code);
                    if($role < 8){
                        $mainQuery->where('users.code','=',$chk_user_id);
                    }
                    $users = $mainQuery->where('users.is_deleted', 0)
                        ->orderby('users.code','asc')
                        ->get();
                }
            } else {
                if (isset($request->employment)) {
                    $mainQuery = DB::table('users')
                        ->JoinSub($subquery1, 't1', function ($join) { 
                            $join->on('t1.code', '=', 'users.code');
                            $join->on('t1.max_apply_term_from', '=', 'users.apply_term_from');
                        })
                        ->where('users.employment_status', $request->employment);
                    if($role < 8){
                        $mainQuery->where('users.code','=',$chk_user_id);
                    }
                    $users = $mainQuery->where('users.is_deleted', 0)
                        ->orderby('users.code','asc')
                        ->get();
                } else {
                    $mainQuery = DB::table('users')
                        ->JoinSub($subquery1, 't1', function ($join) { 
                            $join->on('t1.code', '=', 'users.code');
                            $join->on('t1.max_apply_term_from', '=', 'users.apply_term_from');
                        });
                    if($role < 8){
                        $mainQuery->where('users.code','=',$chk_user_id);
                    }
                    $users = $mainQuery->where('users.is_deleted', 0)->get();
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
    public function getDepartmentList(Request $request){
        // 適用期間日付の取得
        $dt = null;
        if (isset($request->targetdate)) {
            $dt = new Carbon($request->targetdate);
        } else {
            $dt = new Carbon();
        }
        $target_date = $dt->format('Ymd');
        // ログインユーザの権限取得
        $chk_user_id = Auth::user()->code;
        $role = $this->getUserRole($chk_user_id, $target_date);
        if(!isset($role)) { return null; }
        $subquery1 = DB::table('departments')
            ->selectRaw('MAX(apply_term_from) as max_apply_term_from')
            ->selectRaw('code as code')
            ->where('apply_term_from', '<=',$target_date)
            ->where('is_deleted', '=', 0)
            ->groupBy('code');

        if($role < 8){
            $departments = DB::table('departments')
                ->JoinSub($subquery1, 't1', function ($join) { 
                    $join->on('t1.code', '=', 'departments.code');
                    $join->on('t1.max_apply_term_from', '=', 'departments.apply_term_from');
                })
                ->Join('users', function ($join) { 
                    $join->on('users.department_code', '=', 'departments.code')
                    ->where('users.is_deleted', '=', 0);
                })
                ->select('departments.code','departments.name')
                ->where('users.code','=',$chk_user_id)
                ->where('departments.is_deleted', 0)
                ->orderby('departments.code','asc')
                ->get();
        } else {
            $departments = DB::table('departments')
                ->select('departments.code','departments.name')
                ->JoinSub($subquery1, 't1', function ($join) { 
                    $join->on('t1.code', '=', 'departments.code');
                    $join->on('t1.max_apply_term_from', '=', 'departments.apply_term_from');
                })
                ->where('departments.is_deleted', 0)
                ->orderby('departments.code','asc')
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
        $chk_user_id = Auth::user()->code;
        // 適用期間日付の取得（現在日付とする）
        $dt = new Carbon();
        $target_date = $dt->format('Ymd');
        $role = $this->getUserRole($chk_user_id, $target_date);
        return $role;
    }
        
    /** ユーザー権限取得
     *
     * @return list departments
     */
    public function getUserRole($user_id, $target_date){
        // ユーザの権限取得
        $dt = null;
        if (isset($target_date)) {
            $dt = new Carbon($target_date);
        } else {
            $dt = new Carbon();
        }
        $target_date = $dt->format('Ymd');
        $subquery1 = DB::table('users')
            ->selectRaw('code as code')
            ->selectRaw('department_code as department_code')
            ->selectRaw('MAX(apply_term_from) as max_apply_term_from')
            ->where('apply_term_from', '<=',$target_date)
            ->where('is_deleted', '=', 0)
            ->groupBy('code', 'department_code');
        $userrole = DB::table('users')
            ->JoinSub($subquery1, 't1', function ($join) { 
                $join->on('t1.code', '=', 'users.code');
                $join->on('t1.department_code', '=', 'users.department_code');
                $join->on('t1.max_apply_term_from', '=', 'users.apply_term_from');
            })
            ->where('users.code', $user_id)
            ->where('users.is_deleted', 0)
            ->value('role');
        if(!isset($userrole)) { return null; }

        Log::DEBUG('$getUserRole end '.$userrole);
        return $userrole;
    }
        
    /** ユーザー適用期間開始サブクエリー作成
     *
     * @return string サブクエリー
     */
    public function getUserApplyTermSubquery($targetdate){
        // 適用期間日付の取得
        $dt = null;
        if (isset($targetdate)) {
            $dt = new Carbon($targetdate);
        } else {
            $dt = new Carbon();
        }
        $target_date = $dt->format('Ymd');

        // usersの最大適用開始日付subquery
        $subquery = DB::table('users')
            ->select('code as code')
            ->selectRaw('MAX(apply_term_from) as max_apply_term_from')
            ->where('apply_term_from', '<=',$target_date)
            ->where('is_deleted', '=', 0)
            ->groupBy('code');
        return $subquery;
    }
        
    /** 部署適用期間開始サブクエリー作成
     *
     * @return string サブクエリー
     */
    public function getDepartmentApplyTermSubquery($targetdate){
        // 適用期間日付の取得
        $dt = null;
        if (isset($targetdate)) {
            $dt = new Carbon($targetdate);
        } else {
            $dt = new Carbon();
        }
        $target_date = $dt->format('Ymd');

        // departmentsの最大適用開始日付subquery
        $subquery1 = DB::table('departments')
            ->select('code as code')
            ->selectRaw('MAX(apply_term_from) as max_apply_term_from')
            ->where('apply_term_from', '<=',$target_date)
            ->where('is_deleted', '=', 0)
            ->groupBy('code');
        $subquery2 = DB::table('departments as t1')
            ->select('t1.code as code', 't1.name as name')
            ->JoinSub($subquery1, 't2', function ($join) { 
                $join->on('t1.code', '=', 't2.code');
                $join->on('t1.apply_term_from', '=', 't2.max_apply_term_from');
            })
            ->where('t1.is_deleted', '=', 0);
        return $subquery2;
    }

    /** 雇用形態リスト取得
     *
     * @return list statuses
     */
    public function getEmploymentStatusList(){
        Log::DEBUG('$getEmploymentStatusList in ');
        $statuses = DB::table($this->table_generalcodes)->where('identification_id', Config::get('const.C001.value'))->where('is_deleted', 0)->orderby('sort_seq','asc')->get();
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
        $businessDays = DB::table($this->table_generalcodes)->where('identification_id', Config::get('const.C007.value'))->where('is_deleted', 0)->orderby('sort_seq','asc')->get();
        return $businessDays;
    }
    
    /**
     * 休暇区分リスト取得
     *
     * @return list
     */
    public function getHoliDayList(){
        $holiDays = DB::table($this->table_generalcodes)->where('identification_id', Config::get('const.C008.value'))->where('is_deleted', 0)->orderby('sort_seq','asc')->get();
        return $holiDays;
    }

    /**
     * 時間単位リスト取得
     *
     * @return list
     */
    public function getTimeUnitList(){
        $timeUnits = DB::table($this->table_generalcodes)->where('identification_id', 'C009')->where('is_deleted', 0)->orderby('sort_seq','asc')->get();
        return $timeUnits;
    }

    /**
     * 時間の丸めリスト取得
     *
     * @return list
     */
    public function getTimeRoundingList(){
        $timeRounds = DB::table($this->table_generalcodes)->where('identification_id', 'C010')->where('is_deleted', 0)->orderby('sort_seq','asc')->get();
        return $timeRounds;
    }

    /**
     * 個人休暇リスト取得
     *
     * @return list
     */
    public function getUserLeaveKbnList(){
        $userLeaveKbnList = DB::table($this->table_generalcodes)->where('identification_id', 'C013')->where('is_deleted', 0)->orderby('sort_seq','asc')->get();
        return $userLeaveKbnList;
    }

    /**
     * モードリスト取得
     *
     * @return list
     */
    public function getModeList(){
        $modeList = DB::table($this->table_generalcodes)->where('identification_id', 'C005')->where('is_deleted', 0)->orderby('sort_seq','asc')->get();
        return $modeList;
    }

    /**
     * コードリスト取得（Request）
     *
     * @return list
     */
    public function getRequestGeneralList(Request $request){
        // パラメータチェック
        Log::DEBUG('$getRequestGeneralList in '.$request->identificationid);
        if (!isset($request->identificationid)) { return null; }
        return $this->getGeneralList($request->identificationid);
    }

    /**
     * コードリスト取得
     *
     * @return list
     */
    public function getGeneralList($identification_id){
        $codeList = DB::table($this->table_generalcodes)->where('identification_id', $identification_id)->where('is_deleted', 0)->orderby('sort_seq','asc')->get();
        return $codeList;
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
            $dt = new Carbon($basic_date);
            $convDateTime = date_format($dt, 'Y-m-d').' '.$target_time;
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
        Log::DEBUG('diffTimeSerial() from = '.$from);
        Log::DEBUG('diffTimeSerial() to = '.$to);
        $interval = $to - $from;
        Log::DEBUG('diffTimeSerial() interval = '.$interval);
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
