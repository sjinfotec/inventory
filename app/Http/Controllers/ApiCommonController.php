<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\ShiftInformation;
use App\WorkingTimeTable;
use App\Calendar;
use App\Setting;
use App\Demand;
use App\Confirm;
use App\Company;
use App\UserHolidayKubun;
use App\ApprovalRouteNo;
use App\WorkTimeLog;
use App\WorkTime;
use App\UserModel;
use App\CsvItemSelection;
use App\CalendarSettingInformation;
use App\FeatureItemSelection;


/**
 * 共通API
 *
 *      1.適用期間開始サブクエリー作成
 *          ユーザー適用期間開始サブクエリー作成    : getUserApplyTermSubquery, makeUserApplyTermSql                        : users
 *          部署適用期間開始サブクエリー作成        : getDepartmentApplyTermSubquery, makeDepartmentApplyTermSql            : departments
*          タイムテーブル適用期間開始サブクエリー作成  : getTimetableApplyTermSubquery, makeWorkingTimeTableApplyTermSql    : working_timetables
 *      2.リスト作成
 *          ユーザーリスト取得          : getUserList               : users
 *          ユーザーリストCSV作成取得   : getUserListCsv            : users
 *          部署リスト取得              : getDepartmentList         : departments
 *          タイムテーブルリスト取得     : getTimeTableList         : working_timetables
 *          承認リスト取得              : getApprovalroutenoList    : approvals 
 *          承認明細リスト取得          : getApprovalauthorizerList : approval_authorizers
 *          営業日区分リスト取得        : getBusinessDayList        : generalcodes
 *          休暇区分リスト取得          : getHoliDayList            : generalcodes
 *          個人休暇リスト取得          : getUserLeaveKbnList       : generalcodes
 *          モードリスト取得            : getModeList               : generalcodes
 *          共通コードリスト取得        : getRequestGeneralList     : generalcodes
 *          共通コードリスト取得        : getGeneralList            : generalcodes
 *          承認者リスト取得            : getConfirmlList           : confirms
 *      3.ユーザ情報取得
 *          ユーザ情報取得                              : getUserInfo               : users            
 *          ユーザシフト情報取得                        : getShiftInformation               : ShiftInformation        
 *          ログインユーザー部署ロール取得（画面から）      : getLoginUserDepartment            
 *          ログインユーザー権限取得（画面から）            : getLoginUserRole                  
 *          ユーザー権限取得                            : getUserRole                       : users                   
 *          ユーザー部署取得                            : getUserDepartment                 : users 
 *          ユーザーの部署と雇用形態と権限取得          : getUserDepartmentEmploymentRole      : users
 *          ユーザーメールアドレス取得                  : getUserMailAddress                : users      
 *          ユーザー休暇区分取得                        : getUserHolidaykbn                 : UserHolidayKubun  CalendarSettingInformation            
 *          ユーザー所定時刻半休時刻取得                : getWorkingHours                   : WorkingTimeTable             
 *          ユーザー打刻時刻から所定時刻取得             : getWorkingHoursByStamp            : WorkingTimeTable             
 *      4.その他情報取得
 *          会社情報取得                                : getCompanyInfoApply       : Company
 *          指定月締日取得（画面から）                  : getClosingDay              : Setting   
 *          指定月締日取得                              : getCommonClosingDay       : Setting  
 *          曜日取得                                    : getWeekDay
 *          日付のフォーマット YYYY年MM月DD日（WEEK）    : getYMDWeek                 : Calendar   
 *          勤務状況取得                               : getWorgingStatusInfo       : work_timelogs
*           CSV対象項目取得                             : getCsvItem                : csv_item_selections
 *      5.算出情報取得
 *          翌日を求める                                            : getNextDay
 *          指定時間（スタンプ）後を求める                          : getAfterDayTime
 *          時間範囲内に休憩時間が何時間あるか求める（日次集計用）      : calcBetweenBreakTime
 *          時間差を求める（時間）                                  : diffTimeTime
 *          時間差を求める（シリアルで返却）                        : diffTimeSerial
 *          時間差を秒まで求める（シリアルで返却）                  : diffSecoundSerial
 *          時間丸め処理（時間丸めする：出勤用）                    : roundTimeByTimeStart
 *          時間丸め処理（時間丸めする：退勤用）                    : roundTimeByTimeEnd
 *          時間丸め処理（シリアルで丸めする）                      : roundTime
 *      6.判定・チェック
 *          法定法定外休日判定              : jdgBusinessKbn    : Calendar
 *          時間範囲内であるか判定          : chkBetweenTime
 *          出勤時間差をチェック            : chkInteval        : Setting
 *          打刻のモードチェック            : chkMode
 *          緊急かの判定                    : isEmagency
 *      7.変換
 *          時刻日付変換                            : convTimeToDate
 *          時刻日付変換                            : convTimeToDateTarget
 *          時刻日付変換from                        : convTimeToDateFrom
 *          時刻日付変換to                          : convTimeToDateTo
 *          インターバル時間を取得して分に変換する    : getIntevalMinute
 *      8.設定
 *          タイムテーブルの分解                        : analyzeTimeTable
 *          タイムテーブル労働開始終了時間テーブル設定    : setWorkingStartEndTimeTable
 *          タイムテーブル編集設定                      : edtTimeTable
 *          reqestクエリーセット                        : setRequestQeury
 *      9.登録更新
 *          勤怠登録（勤怠編集/カレンダー一括編集）     : addAttendanceWork
 * 
 *
 */
class ApiCommonController extends Controller
{

    protected $table_generalcodes = 'generalcodes';
    protected $table_companies = 'companies';
    protected $table_confirms = 'confirms';
    protected $table_users = 'users';
    protected $table_departments = 'departments';
    protected $table_working_timetables = 'working_timetables';
    protected $table_approval_route_nos = 'approval_route_nos';
    protected $table_post_informations = 'post_informations';
    protected $table_user_holiday_kubuns = 'user_holiday_kubuns';

    private $array_messagedata = array();

    // -------------  1.適用期間開始サブクエリー作成  start ---------------------------------------------- //

    /** ユーザー適用期間開始サブクエリー作成
     *
     * @return string サブクエリー
     */
    public function getUserApplyTermSubquery($targetdate){
        try {
            // 適用期間日付の取得
            $dt = null;
            if (isset($targetdate)) {
                $dt = new Carbon($targetdate);
            } else {
                $dt = new Carbon();
            }
            $target_date = $dt->format('Ymd');

            // usersの最大適用開始日付subquery
            $subquery = DB::table($this->table_users)
                ->select('code as code')
                ->selectRaw('MAX(apply_term_from) as max_apply_term_from')
                ->where('apply_term_from', '<=',$target_date)
                ->where('role', '<', Config::get('const.C017.admin_user'))
                ->where('is_deleted', '=', 0)
                ->groupBy('code');
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.subquery_illegal')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.subquery_illegal')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
        return $subquery;
    }
        
    /** ユーザー適用期間開始サブクエリー作成（SQL）
     *
     * @return string サブクエリー
     */
    public function makeUserApplyTermSql($apply_date, $role){
        $makeSql = "";
        $makeSql .= " select ";
        $makeSql .= "   code as code ";
        $makeSql .= "   ,MAX(apply_term_from) as max_apply_term_from ";
        $makeSql .= " from ";
        $makeSql .= " ".$this->table_users." ";
        $makeSql .= " where ? = ? ";
        if (!empty($apply_date)) {
            $makeSql .= " and apply_term_from <= ? ";
        }
        if (!empty($role)) {
            $makeSql .= " and role <= ? ";
        }
        $makeSql .= " and is_deleted = ? ";
        $makeSql .= " group by code ";

        return $makeSql;
    }
        
    /** 部署適用期間開始サブクエリー作成
     *
     * @return string サブクエリー
     */
    public function getDepartmentApplyTermSubquery($targetdate){
        try {
            // 適用期間日付の取得
            $dt = null;
            if (isset($targetdate)) {
                $dt = new Carbon($targetdate);
            } else {
                $dt = new Carbon();
            }
            $target_date = $dt->format('Ymd');

            // departmentsの最大適用開始日付subquery
            $subquery1 = DB::table($this->table_departments)
                ->select('code as code')
                ->selectRaw('MAX(apply_term_from) as max_apply_term_from')
                ->where('apply_term_from', '<=',$target_date)
                ->where('is_deleted', '=', 0)
                ->groupBy('code');
            $mainquery = DB::table($this->table_departments.' as t1')
                ->select('t1.code as code', 't1.name as name')
                ->JoinSub($subquery1, 't2', function ($join) { 
                    $join->on('t1.code', '=', 't2.code');
                    $join->on('t1.apply_term_from', '=', 't2.max_apply_term_from');
                })
                ->where('t1.kill_from_date', '>',$target_date)
                ->where('t1.is_deleted', '=', 0);
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_departments, Config::get('const.LOG_MSG.subquery_illegal')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_departments, Config::get('const.LOG_MSG.subquery_illegal')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
        return $mainquery;
    }
        
    /** 部署適用期間開始サブクエリー作成（SQL）
     *
     * @return string サブクエリー
     */
    public function makeDepartmentApplyTermSql($apply_date, $kill_date){
        $makeSql = "";
        $makeSql .= " select ";
        $makeSql .= "   t1.code as code ";
        $makeSql .= "   ,t1.name as name ";
        $makeSql .= " from ";
        $makeSql .= " ".$this->table_departments." as t1 ";
        $makeSql .= "   inner join ( ";
        $makeSql .= "     select ";
        $makeSql .= "       code as code ";
        $makeSql .= "       , MAX(apply_term_from) as max_apply_term_from ";
        $makeSql .= "     from ";
        $makeSql .= "       ".$this->table_departments;
        $makeSql .= "     where ? = ? ";
        if (!empty($apply_date)) {
            $makeSql .= "   and apply_term_from <= ? ";
        }
        $makeSql .= "       and is_deleted = ? ";
        $makeSql .= "     group by code ";
        $makeSql .= "   )  as t2 ";
        $makeSql .= "   on t1.code = t2.code ";
        $makeSql .= "   and t1.apply_term_from = t2.max_apply_term_from ";
        $makeSql .= " where ? = ? ";
        if (!empty($kill_date)) {
            $makeSql .= "   and t1.kill_from_date >= ? ";
        }
        $makeSql .= "       and t1.is_deleted = ? ";

        return $makeSql;
    }
        
    /** タイムテーブル適用期間開始サブクエリー作成
     *
     * @return string サブクエリー
     */
    public function getTimetableApplyTermSubquery($targetdate){
        try {
            // 適用期間日付の取得
            $dt = null;
            if (isset($targetdate)) {
                $dt = new Carbon($targetdate);
            } else {
                $dt = new Carbon();
            }
            $target_date = $dt->format('Ymd');

            // departmentsの最大適用開始日付subquery
            $subquery1 = DB::table($this->table_working_timetables)
                ->select('no as no')
                ->selectRaw('MAX(apply_term_from) as max_apply_term_from')
                ->where('apply_term_from', '<=',$target_date)
                ->where('is_deleted', '=', 0)
                ->groupBy('no');
            $subquery2 = DB::table($this->table_working_timetables.' as t1')
                ->select(
                    't1.no as no',
                    't1.name as name',
                    't1.from_time as from_time',
                    't1.to_time as to_time',
                    't1.ago_time_no as ago_time_no',
                    't1.working_time_kubun as working_time_kubun'
                )
                ->JoinSub($subquery1, 't2', function ($join) { 
                    $join->on('t1.no', '=', 't2.no');
                    $join->on('t1.apply_term_from', '=', 't2.max_apply_term_from');
                })
                ->whereNotNull('t1.from_time')
                ->where('t1.is_deleted', '=', 0);
                return $subquery2;
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_working_timetables, Config::get('const.LOG_MSG.subquery_illegal')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_working_timetables, Config::get('const.LOG_MSG.subquery_illegal')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }
        
    /** タイムテーブル適用期間開始サブクエリー作成（SQL）
     *
     * @return string サブクエリー
     */
    public function makeWorkingTimeTableApplyTermSql($apply_date){
        $makeSql = "";
        $makeSql .= " select ";
        $makeSql .= "   t1.no as no ";
        $makeSql .= "   ,t1.name as name ";
        $makeSql .= "   ,t1.from_time as from_time ";
        $makeSql .= "   ,t1.to_time as to_time ";
        $makeSql .= "   ,t1.ago_time_no as ago_time_no ";
        $makeSql .= "   ,t1.working_time_kubun as working_time_kubun ";
        $makeSql .= " from ";
        $makeSql .= " ".$this->table_working_timetables." as t1 ";
        $makeSql .= "   inner join ( ";
        $makeSql .= "     select ";
        $makeSql .= "       no as no ";
        $makeSql .= "       , MAX(apply_term_from) as max_apply_term_from ";
        $makeSql .= "     from ";
        $makeSql .= "       ".$this->table_working_timetables;
        $makeSql .= "     where ? = ? ";
        if (!empty($apply_date)) {
            $makeSql .= "   and apply_term_from <= ? ";
        }
        $makeSql .= "       and is_deleted = ? ";
        $makeSql .= "     group by no ";
        $makeSql .= "   )  as t2 ";
        $makeSql .= "   on t1.no = t2.no ";
        $makeSql .= "   and t1.apply_term_from = t2.max_apply_term_from ";
        $makeSql .= " where ? = ? ";
        $makeSql .= "   and t1.no < ? ";
        $makeSql .= "   and t1.from_time is not null ";
        $makeSql .= "   and t1.is_deleted = ? ";

        return $makeSql;
    }
    // -------------  １．適用期間開始サブクエリー作成  end ---------------------------------------------- //

    // -------------  2.リスト作成  start -------------------------------------------------------------- //
    /**
     * ユーザーリスト取得
     *
     * @param  Request  getdo 0:取得しない、1:取得する
     * @return list users
     */
    public function getUserList(Request $request){

        $this->array_messagedata = array();
        $details = new Collection();
        $result = true;
        try {
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $details,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            $targetdate = '';
            if (isset($params['targetdate'])) {
                $targetdate =  $params['targetdate'];
            }
            // 適用期間日付の取得
            $dt = null;
            if ($targetdate != '') {
                $dt = new Carbon($targetdate);
            } else {
                $dt = new Carbon();
            }
            $target_date = $dt->format("Ymd");

            $killvalue = false;
            if (isset($params['killvalue'])) {
                $killvalue =  $params['killvalue'];
            }
            $getdo = 0;
            if (isset($params['getDo'])) {
                $getdo =  $params['getDo'];
            }
            if ($getdo != 1) {
                return response()->json(
                    ['result' => true, 'details' => $details,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $departmentcode = null;
            if (isset($params['departmentcode'])) {
                $departmentcode =  $params['departmentcode'];
            }
            $employmentcode = null;
            if (isset($params['employmentcode'])) {
                $employmentcode =  $params['employmentcode'];
            }

            $managementcode = Config::get('const.C017.admin_user');
            if (isset($params['managementcode'])) {
                $managementcode =  $params['managementcode'];
            }
            $arrayrole = array();
            if (isset($params['roles'])) {
                $arrayrole =  $params['roles'];
            }
            // ログインユーザの権限取得
            $chk_user_id = Auth::user()->code;
            $role = $this->getUserRole($chk_user_id, $target_date);
            if(!isset($role)) {
                // エラー追加 20200121
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $chk_user_id, Config::get('const.LOG_MSG.not_setting_role')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.not_setting_role');
                return response()->json(
                    ['result' => false, 'details' => $details,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $subquery1 = DB::table($this->table_users)
                ->selectRaw('MAX(apply_term_from) as max_apply_term_from')
                ->selectRaw('code as code')
                ->where('apply_term_from', '<=',$targetdate)
                ->where('is_deleted', '=', 0)
                ->groupBy('code');

            if (isset($departmentcode)) {
                if (isset($employmentcode)) {
                    $mainQuery = DB::table($this->table_users)
                        ->JoinSub($subquery1, 't1', function ($join) { 
                            $join->on('t1.code', '=', $this->table_users.'.code');
                            $join->on('t1.max_apply_term_from', '=', $this->table_users.'.apply_term_from');
                        })
                        ->where($this->table_users.'.department_code', $departmentcode)
                        ->where($this->table_users.'.employment_status', $employmentcode);
                    if($role == Config::get('const.C025.general_user')){
                        $mainQuery->where($this->table_users.'.code','=',$chk_user_id);
                    } else {
                        $mainQuery->where($this->table_users.'.management','<',$managementcode);
                    }
                    if(isset($params['roles'])){
                        $mainQuery->whereIn($this->table_users.'.role', $arrayrole);
                    }
                    if (!$killvalue) {
                        $details = $mainQuery
                            ->where($this->table_users.'.kill_from_date', '>',$target_date)
                            ->where($this->table_users.'.is_deleted', 0)
                            ->orderby($this->table_users.'.code','asc')
                            ->get();
                    } else {
                        $details = $mainQuery
                            ->where($this->table_users.'.is_deleted', 0)
                            ->orderby($this->table_users.'.code','asc')
                            ->get();
                    }
                } else {
                    $mainQuery = DB::table($this->table_users)
                        ->JoinSub($subquery1, 't1', function ($join) { 
                            $join->on('t1.code', '=', $this->table_users.'.code');
                            $join->on('t1.max_apply_term_from', '=', $this->table_users.'.apply_term_from');
                        })
                        ->where($this->table_users.'.department_code', $departmentcode);
                    if($role == Config::get('const.C025.general_user')){
                        $mainQuery->where($this->table_users.'.code','=',$chk_user_id);
                    } else {
                        $mainQuery->where($this->table_users.'.management','<',$managementcode);
                    }
                    if(isset($params['roles'])){
                        $mainQuery->whereIn($this->table_users.'.role', $arrayrole);
                    }
                    if (!$killvalue) {
                        $details = $mainQuery
                            ->where($this->table_users.'.kill_from_date', '>',$target_date)
                            ->where($this->table_users.'.is_deleted', 0)
                            ->orderby($this->table_users.'.code','asc')
                            ->get();
                    } else {
                        $details = $mainQuery
                            ->where($this->table_users.'.is_deleted', 0)
                            ->orderby($this->table_users.'.code','asc')
                            ->get();
                    }
                }
            } else {
                if (isset($employmentcode)) {
                    $mainQuery = DB::table($this->table_users)
                        ->JoinSub($subquery1, 't1', function ($join) { 
                            $join->on('t1.code', '=', $this->table_users.'.code');
                            $join->on('t1.max_apply_term_from', '=', $this->table_users.'.apply_term_from');
                        })
                        ->where($this->table_users.'.employment_status', $employmentcode);
                    if($role == Config::get('const.C025.general_user')){
                        $mainQuery->where($this->table_users.'.code','=',$chk_user_id);
                    } else {
                        $mainQuery->where($this->table_users.'.management','<',$managementcode);
                    }
                    if(isset($params['roles'])){
                        $mainQuery->whereIn($this->table_users.'.role', $arrayrole);
                    }
                    if (!$killvalue) {
                        $details = $mainQuery
                            ->where($this->table_users.'.kill_from_date', '>',$target_date)
                            ->where($this->table_users.'.is_deleted', 0)
                            ->orderby($this->table_users.'.code','asc')
                            ->get();
                    } else {
                        $details = $mainQuery
                            ->where($this->table_users.'.is_deleted', 0)
                            ->orderby($this->table_users.'.code','asc')
                            ->get();
                    }
                } else {
                    $mainQuery = DB::table($this->table_users)
                        ->JoinSub($subquery1, 't1', function ($join) { 
                            $join->on('t1.code', '=', $this->table_users.'.code');
                            $join->on('t1.max_apply_term_from', '=', $this->table_users.'.apply_term_from');
                        });
                    if($role == Config::get('const.C025.general_user')){
                        $mainQuery->where($this->table_users.'.code','=',$chk_user_id);
                    } else {
                        $mainQuery->where($this->table_users.'.management','<',$managementcode);
                    }
                    if(isset($params['roles'])){
                        $mainQuery->whereIn($this->table_users.'.role', $arrayrole);
                    }
                    if (!$killvalue) {
                        $details = $mainQuery
                            ->where($this->table_users.'.kill_from_date', '>',$target_date)
                            ->where($this->table_users.'.is_deleted', 0)
                            ->get();
                    } else {
                        $details = $mainQuery
                            ->where($this->table_users.'.is_deleted', 0)
                            ->get();
                    }
                }
            }

            return response()->json(
                ['result' => true, 'details' => $details,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }
    /**
     * ユーザーリストCSV作成取得
     *
     * @param  Request
     * @return list users
     */
    public function getUserListCsv(Request $request){

        // Log::debug('getUserListCsv = ');
        $this->array_messagedata = array();
        $details = new Collection();
        $result = true;
        try {
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $details,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            $targetdate = '';
            if (isset($params['targetdate'])) {
                $targetdate =  $params['targetdate'];
            }
            // 適用期間日付の取得
            $dt = null;
            if ($targetdate != '') {
                $dt = new Carbon($targetdate);
            } else {
                $dt = new Carbon();
            }
            $target_date = $dt->format("Ymd");
            $departmentcode = null;
            if (isset($params['departmentcode'])) {
                $departmentcode =  $params['departmentcode'];
            }
            $employmentcode = null;
            if (isset($params['employmentcode'])) {
                $employmentcode =  $params['employmentcode'];
            }
            $usercode = null;
            if (isset($params['usercode'])) {
                $employmentcode =  $params['usercode'];
            }
            $killvalue = false;
            if (isset($params['killvalue'])) {
                $killvalue =  $params['killvalue'];
            }
            /// users->getFullUserDetails呼び出し
            $users_model = new UserModel();
            $details = $users_model->getUserDetailsCsv();
            $result_details = Collect($details);
            return response()->json(
                ['result' => true, 'details' => $result_details,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }
        
    /** 部署リスト取得
     *
     * @return list departments
     */
    public function getDepartmentList(Request $request){
        $this->array_messagedata = array();
        $details = new Collection();
        $result = true;
        try {
            // 適用期間日付の取得
            $dt = new Carbon();
            $killvalue = false;
            // パラメータチェック
            $params = array();
            if (isset($request->keyparams)) {
                $params = $request->keyparams;
                // 適用期間日付の取得
                $dt = null;
                if (isset($params['targetdate'])) {
                    $dt = new Carbon($params['targetdate']);
                } else {
                    $dt = new Carbon();
                }
                if (isset($params['killvalue'])) {
                    $killvalue = $params['killvalue'];
                }
            }
            $target_date = $dt->format('Ymd');

            // ログインユーザの権限取得
            $chk_user_id = Auth::user()->code;
            $role = $this->getUserRole($chk_user_id, $target_date);
            if(!isset($role)) {
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.not_setting_role');
                // エラー追加 20200121
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $chk_user_id, Config::get('const.LOG_MSG.not_setting_role')));
                return response()->json(
                    ['result' => false, 'details' => $details,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $subquery1 = DB::table($this->table_departments)
                ->selectRaw('MAX(apply_term_from) as max_apply_term_from')
                ->selectRaw('code as code')
                ->where('apply_term_from', '<=',$target_date)
                ->where('is_deleted', '=', 0)
                ->groupBy('code');

            if($role == Config::get('const.C025.general_user')){
                $mainQuery = DB::table($this->table_departments)
                    ->JoinSub($subquery1, 't1', function ($join) { 
                        $join->on('t1.code', '=', $this->table_departments.'.code');
                        $join->on('t1.max_apply_term_from', '=', $this->table_departments.'.apply_term_from');
                    })
                    ->Join($this->table_users, function ($join) { 
                        $join->on($this->table_users.'.department_code', '=', $this->table_departments.'.code')
                        ->where($this->table_users.'.is_deleted', '=', 0);
                    })
                    ->select($this->table_departments.'.code',$this->table_departments.'.name')
                    ->where($this->table_users.'.code','=',$chk_user_id);
                    if (!$killvalue) {
                        $mainQuery
                            ->where($this->table_departments.'.kill_from_date', '>',$target_date)
                            ->where($this->table_departments.'.is_deleted', 0)
                            ->orderby($this->table_departments.'.code','asc');
                    } else {
                        $mainQuery
                            ->where($this->table_departments.'.is_deleted', 0)
                            ->orderby($this->table_departments.'.code','asc');
                    }
                    $details = $mainQuery->get();
                } else {
                    $mainQuery = DB::table($this->table_departments)
                        ->select($this->table_departments.'.code',$this->table_departments.'.name')
                        ->JoinSub($subquery1, 't1', function ($join) { 
                            $join->on('t1.code', '=', $this->table_departments.'.code');
                            $join->on('t1.max_apply_term_from', '=', $this->table_departments.'.apply_term_from');
                        });
                    if (!$killvalue) {
                        $mainQuery
                            ->where($this->table_departments.'.kill_from_date', '>',$target_date)
                            ->where($this->table_departments.'.is_deleted', 0)
                            ->orderby($this->table_departments.'.code','asc');
                    } else {
                        $mainQuery
                            ->where($this->table_departments.'.is_deleted', 0)
                            ->orderby($this->table_departments.'.code','asc');
                    }
                    $details = $mainQuery->get();
            }
            return response()->json(
                ['result' => true, 'details' => $details,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * タイムテーブルリスト取得
     *
     * @return list
     */
    public function getTimeTableList(){
        $this->array_messagedata = array();
        $details = new Collection();
        $result = true;
        try {
            $time_tables = new WorkingTimeTable();
            $details = $time_tables->getTimeTables();

            return response()->json(
                ['result' => $result, 'details' => $details,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

    }

    /**
     * 承認リスト取得
     *
     * @return list
     */
    public function getApprovalroutenoList(Request $request){
        $this->array_messagedata = array();
        $details = new Collection();
        $result = true;
        try {
            $approval_route_no_model = new ApprovalRouteNo();
            $details = $approval_route_no_model->getList();

            return response()->json(
                ['result' => $result, 'details' => $details,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 承認明細リスト取得
     *
     * @return list
     */
    public function getApprovalauthorizerList(){
        $this->array_messagedata = array();
        $details = new Collection();
        $result = true;
        try {
            $approval_authorizers_model = new ApprovalAuthorizer();
            $details = $approval_authorizers_model->getList();

            return response()->json(
                ['result' => $result, 'details' => $details,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 営業日区分リスト取得
     *
     * @return list
     */
    public function getBusinessDayList(){
        $this->array_messagedata = array();
        $details = new Collection();
        $result = true;
        try {
            $details =
                DB::table($this->table_generalcodes)
                    ->where('identification_id', Config::get('const.C007.value'))
                    ->where('is_deleted', 0)
                    ->orderby('sort_seq','asc')
                    ->get();

            return response()->json(
                ['result' => $result, 'details' => $details,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }
    
    /**
     * 休暇区分リスト取得
     *
     * @return list
     */
    public function getHoliDayList(){
        $this->array_messagedata = array();
        $details = new Collection();
        $result = true;
        try {
            $details =
                DB::table($this->table_generalcodes)
                    ->where('identification_id', Config::get('const.C008.value'))
                    ->where('is_deleted', 0)
                    ->orderby('sort_seq','asc')
                    ->get();

            return response()->json(
                ['result' => $result, 'details' => $details,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 個人休暇リスト取得
     *
     * @return list
     */
    public function getUserLeaveKbnList(){
        $this->array_messagedata = array();
        $details = new Collection();
        $result = true;
        try {
            $details =
                DB::table($this->table_generalcodes)
                    ->where('identification_id', 'C013')
                    ->where('is_deleted', 0)
                    ->orderby('sort_seq','asc')
                    ->get();

            return response()->json(
                ['result' => $result, 'details' => $details,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * モードリスト取得
     *
     * @return list
     */
    public function getModeList(){
        $this->array_messagedata = array();
        $details = new Collection();
        $result = true;
        try {
            $feature_model = new FeatureItemSelection();
            $feature_model->setParamaccountidAttribute(Config::get('const.ACCOUNTID.account_id'));
            $feature_model->setParamselectioncodeAttribute(Config::get('const.EDITION.EDITION'));
            $feature_model->setParamitemcodeAttribute(Config::get('const.C042.mode_list'));
            $feature_data = $feature_model->getItem();
            $value_select = "0";
            foreach($feature_data as $item) {
                $value_select = $item->value_select;
                break;
            }
            $details = DB::table($this->table_generalcodes)
                ->where('identification_id', 'C005')
                ->where('use_free_item', '<=', $value_select)
                ->where('is_deleted', 0)
                ->orderby('sort_seq','asc')
                ->get();

            return response()->json(
                ['result' => $result, 'details' => $details,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 共通コードリスト取得（Request）
     *
     * @return list
     */
    public function getRequestGeneralList(Request $request){
        $this->array_messagedata = array();
        $details = new Collection();
        $result = true;
        try{
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $details,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            if (!isset($params['identificationid'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "identificationid", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $details,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $details = $this->getGeneralList($params['identificationid']);
            return response()->json(
                ['result' => true, 'details' => $details,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 共通コードリスト取得
     *
     * @return list
     */
    public function getGeneralList($identification_id){
        $details = new Collection();
        try {
            $details =
                DB::table($this->table_generalcodes)
                    ->where('identification_id', $identification_id)
                    ->where('is_deleted', 0)
                    ->orderby('sort_seq','asc')
                    ->get();
            return $details;
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 承認者リスト取得
     *
     * @return list
     */
    public function getConfirmlList(Request $request){
        $codeList = new Collection();
        try {
            // パラメータチェック
            if (isset($request->getdo)) {
                $getdo = $request->getdo;
            } else {
                $getdo = 1;
            }
            if ($getdo != 1) { return null; }

            $dt = null;
            if (isset($targetdate)) {
                $dt = new Carbon($targetdate);
            } else {
                $dt = new Carbon();
            }
            $target_date = $dt->format('Ymd');
            if (isset($request->orFinal)) {
                $orFinal = $request->orFinal;
            } else {
                $orFinal = "";
            }
            if (isset($request->mainorsub)) {
                $mainorsub = $request->mainorsub;
            } else {
                $mainorsub = "";
            }
            $confirm_model = new Confirm();
            $confirm_model->setParamSeqAttribute($orFinal);
            $confirm_model->setParamMainsubAttribute($mainorsub);
            $codeList = $confirm_model->selectConfirmList($target_date);
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
        return $codeList;
    }
    // -------------  2.リスト作成  end -------------------------------------------------------------- //

    // -------------  3.ユーザ情報取得  start -------------------------------------------------------- //

    /**
     *  ユーザ情報取得
     *
     * @return list departments
     */
    public function getUserInfo($target_date, $code, $department_code, $employment_status){
        try {
            $dt = null;
            if (isset($target_date)) {
                $dt = new Carbon($target_date);
            } else {
                $dt = new Carbon();
            }
            $target_date = $dt->format('Ymd');
            // usersの最大適用開始日付subquery
            $subquery3 = $this->getUserApplyTermSubquery($target_date);
            // departmentsの最大適用開始日付subquery
            $subquery4 = $this->getDepartmentApplyTermSubquery($target_date);
            $mainquery = DB::table($this->table_users)
                ->select(
                    $this->table_users.'.code as code',
                    $this->table_users.'.name as name',
                    $this->table_users.'.department_code as department_code',
                    $this->table_users.'.employment_status as employment_status')
                ->JoinSub($subquery3, 't1', function ($join) { 
                    $join->on('t1.code', '=', $this->table_users.'.code');
                    $join->on('t1.max_apply_term_from', '=', $this->table_users.'.apply_term_from');
                })
                ->JoinSub($subquery4, 't2', function ($join) { 
                    $join->on('t2.code', '=', $this->table_users.'.department_code');
                });
            if (isset($code)) {
                $mainquery    
                    ->where($this->table_users.'.code', $code);
            }
            if (isset($department_code)) {
                $mainquery    
                    ->where($this->table_users.'.department_code', $department_code);
            }
            if (isset($employment_status)) {
                $mainquery    
                    ->where($this->table_users.'.employment_status', $employment_status);
            }
            $data = $mainquery    
                ->where($this->table_users.'.kill_from_date', ">=", $target_date)
                ->where($this->table_users.'.is_deleted', 0)
                ->get();
            return $data;
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
            
    }

    /**
     * ユーザシフト情報取得
     *
     * @return void
     */
    public function getShiftInformation(Request $request){
        $this->array_messagedata = array();
        $details = new Collection();
        $result = true;
        try {
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $details,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            if (!isset($params['usercode'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "usercode", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $details,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            if (!isset($params['from'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "from", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $details,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            if (!isset($params['to'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "to", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $details,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $departmentcode = null;
            if (isset($params['departmentcode'])) {
                $departmentcode = $params['departmentcode'];
            }
            $usercode = $params['usercode'];
            $no = null;
            if (isset($params['no'])) {
                $no = $params['no'];
            }
            $from = new Carbon($params['from']);
            $from = $from->format("Ymd");
            $to = new Carbon($params['to']);
            $to = $to->format("Ymd");

            $shift_info = new ShiftInformation();
            $shift_info->setParamdepartmentcodeAttribute($departmentcode);
            $shift_info->setParamusercodeAttribute($usercode);
            $shift_info->setParamWorkingtimetablenoAttribute($no);
            $shift_info->setParamfromdateAttribute($from);
            $shift_info->setParamtodateAttribute($to);
            $details = $shift_info->getUserShift();

            return response()->json(
                ['result' => true, 'details' => $details,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }
    /** ユーザー部署ロール取得（画面から）
     *
     * @return list departments
     */
    public function getLoginUserDepartment(){
        $this->array_messagedata = array();
        $details = new Collection();
        $result = true;
        try {
            // ログインユーザの権限取得
            $chk_user_id = Auth::user()->code;
            $details = $this->getUserDepartment($chk_user_id, null);

            return response()->json(
                ['result' => $result, 'details' => $details,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }
        
    /** ユーザー権限取得（画面から）
     *
     * @return list departments
     */
    public function getLoginUserRole(){
        $this->array_messagedata = array();
        $role = null;
        $result = true;
        try {
            // ログインユーザの権限取得
            $chk_user_id = Auth::user()->code;
            // 適用期間日付の取得（現在日付とする）
            $dt = new Carbon();
            $target_date = $dt->format('Ymd');
            $role = $this->getUserRole($chk_user_id, $target_date);

            return response()->json(
                ['result' => $result, 'role' => $role,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }
        
    /** ユーザー権限取得
     *
     * @return list departments
     */
    public function getUserRole($user_id, $target_date){
        try {
            // ユーザの権限取得
            $dt = null;
            if (isset($target_date)) {
                $dt = new Carbon($target_date);
            } else {
                $dt = new Carbon();
            }
            $target_date = $dt->format('Ymd');
            $subquery1 = DB::table($this->table_users)
                ->selectRaw('code as code')
                ->selectRaw('department_code as department_code')
                ->selectRaw('MAX(apply_term_from) as max_apply_term_from')
                ->where('apply_term_from', '<=',$target_date)
                ->where('is_deleted', '=', 0)
                ->groupBy('code', 'department_code');
            $userrole = DB::table($this->table_users)
                ->JoinSub($subquery1, 't1', function ($join) { 
                    $join->on('t1.code', '=', $this->table_users.'.code');
                    $join->on('t1.department_code', '=', $this->table_users.'.department_code');
                    $join->on('t1.max_apply_term_from', '=', $this->table_users.'.apply_term_from');
                })
                ->where($this->table_users.'.code', $user_id)
                ->where($this->table_users.'.is_deleted', 0)
                ->value('role');
            if(!isset($userrole)) { return null; }
            return $userrole;
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

    }

    /** ユーザー部署取得
     *
     * @return list departments
     */
    public function getUserDepartment($user_id, $target_date){
        try {
            $dt = null;
            if (isset($target_date)) {
                $dt = new Carbon($target_date);
            } else {
                $dt = new Carbon();
            }
            $target_date = $dt->format('Ymd');
            // usersの最大適用開始日付subquery
            $subquery3 = $this->getUserApplyTermSubquery($target_date);
            // departmentsの最大適用開始日付subquery
            $subquery4 = $this->getDepartmentApplyTermSubquery($target_date);
            $mainquery = DB::table($this->table_users)
                ->select(
                    $this->table_users.'.code as code',
                    $this->table_users.'.name as name',
                    $this->table_users.'.department_code as department_code',
                    't2.name as department_name',
                    $this->table_users.'.role as role')
                ->JoinSub($subquery3, 't1', function ($join) { 
                    $join->on('t1.code', '=', $this->table_users.'.code');
                    $join->on('t1.max_apply_term_from', '=', $this->table_users.'.apply_term_from');
                })
                ->JoinSub($subquery4, 't2', function ($join) { 
                    $join->on('t2.code', '=', $this->table_users.'.department_code');
                })
                ->where($this->table_users.'.code', $user_id)
                ->where($this->table_users.'.is_deleted', 0)
                ->get();
                return $mainquery;
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
            
    }

    /** ユーザーの部署と雇用形態と権限取得
     *
     * @return list departments
     */
    public function getUserDepartmentEmploymentRole($user_id, $target_date){
        try {
            $dt = null;
            if (isset($target_date)) {
                $dt = new Carbon($target_date);
            } else {
                $dt = new Carbon();
            }
            $target_date = $dt->format('Ymd');
            // usersの最大適用開始日付subquery
            $subquery3 = $this->getUserApplyTermSubquery($target_date);
            // departmentsの最大適用開始日付subquery
            $subquery4 = $this->getDepartmentApplyTermSubquery($target_date);
            $mainquery = DB::table($this->table_users)
                ->select(
                    $this->table_users.'.code as code',
                    $this->table_users.'.name as name',
                    $this->table_users.'.department_code as department_code',
                    't2.name as department_name',
                    $this->table_users.'.employment_status as employment_status',
                    't3.code_name as employment_name',
                    $this->table_users.'.role as role')
                ->JoinSub($subquery3, 't1', function ($join) { 
                    $join->on('t1.code', '=', $this->table_users.'.code');
                    $join->on('t1.max_apply_term_from', '=', $this->table_users.'.apply_term_from');
                })
                ->JoinSub($subquery4, 't2', function ($join) { 
                    $join->on('t2.code', '=', $this->table_users.'.department_code');
                })
                ->leftJoin($this->table_generalcodes.' as t3', function ($join) { 
                    $join->on('t3.code', '=', $this->table_users.'.employment_status')
                    ->where('t3.identification_id', '=', Config::get('const.C001.value'))
                    ->where('t3.is_deleted', '=', 0);
                });
            if (isset($user_id)) {
                $mainquery    
                    ->where($this->table_users.'.code', $user_id);
            }
            $data = $mainquery    
                ->where($this->table_users.'.is_deleted', 0)
                ->get();
            return $data;
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
            
    }
        
    /** ユーザーメールアドレス取得
     *
     * @return list departments
     */
    public function getUserMailAddress($user_id, $target_date){
        $useremail = null;
        try {
            // ユーザの権限取得
            $dt = null;
            if (isset($target_date)) {
                $dt = new Carbon($target_date);
            } else {
                $dt = new Carbon();
            }
            $target_date = $dt->format('Ymd');
            $subquery1 = DB::table($this->table_users)
                ->selectRaw('code as code')
                ->selectRaw('department_code as department_code')
                ->selectRaw('MAX(apply_term_from) as max_apply_term_from')
                ->where('apply_term_from', '<=',$target_date)
                ->where('is_deleted', '=', 0)
                ->groupBy('code', 'department_code');
            $useremail = DB::table($this->table_users)
                ->JoinSub($subquery1, 't1', function ($join) { 
                    $join->on('t1.code', '=', $this->table_users.'.code');
                    $join->on('t1.department_code', '=', $this->table_users.'.department_code');
                    $join->on('t1.max_apply_term_from', '=', $this->table_users.'.apply_term_from');
                })
                ->where($this->table_users.'.code', $user_id)
                ->where($this->table_users.'.is_deleted', 0)
                ->value($this->table_users.'.email');
            if(!isset($useremail)) { return null; }
            return $useremail;
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

    }

    /**
     * ユーザー休暇区分取得
     *
     * @param target_date YYYYMMDD
     * @return list
     */
    public function getUserHolidaykbn($user_id, $target_date){
        $holiday_kbn = null;
        try {
            // ユーザー休暇区分取得
            $calendar_setting_model = new CalendarSettingInformation();
            $calendar_setting_model->setParamusercodeAttribute($user_id);
            $calendar_setting_model->setParamfromdateAttribute($target_date);
            $results = $calendar_setting_model->getCalenderInfo();
            foreach($results as $item) {
                if (isset($item->holiday_kubun)) {
                    $holiday_kbn = $item->holiday_kubun;
                    break;
                }
            }
            return $holiday_kbn;
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * ユーザー休暇区分取得
     *
     * @param target_date YYYYMMDD
     * @return list
     */
    // public function getUserHolidaykbn($user_id, $target_date){
    //     $holiday_kbn = null;
    //     try {
    //         // ユーザー休暇区分取得
    //         $user_holiday_model = new UserHolidayKubun();
    //         $user_holiday_model->setParamUsercodeAttribute($user_id);
    //         $user_holiday_model->setParamdatefromAttribute($target_date);
    //         $results = $user_holiday_model->getDetail();
    //         foreach($results as $item) {
    //             if (isset($item->holiday_kubun)) {
    //                 $holiday_kbn = $item->holiday_kubun;
    //                 break;
    //             }
    //         }
    //         return $holiday_kbn;
    //     }catch(\PDOException $pe){
    //         Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$pe');
    //         throw $pe;
    //     }catch(\Exception $e){
    //         Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$e');
    //         Log::error($e->getMessage());
    //         throw $e;
    //     }
    // }

    /**
     * ユーザー所定時刻半休時刻取得
     *
     * @param Request
     * @return list
     */
    public function getWorkingHours(Request $request){
        
        $this->array_messagedata = array();
        $workingHours = array();
        $result = true;
        try{
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $workingHours,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            if (!isset($params['target_date'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "target_date", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $workingHours,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            if (!isset($params['user_code'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "user_code", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $workingHours,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $target_date = $params['target_date'];
            $user_code = $params['user_code'];
            $department_code = "";
            if (isset($params['department_code'])) {
                $department_code = $params['department_code'];
            } else {
                // department_code取得する
                $datas = $this->getUserDepartmentEmploymentRole($user_code, $target_date);
                foreach ($datas as $item) {
                    if (isset($item->department_code)) {
                        $department_code = $item->department_code;
                    }
                    break;
                }
            }
            // usersのworking_timetables_noまたはshift_informationsのworking_timetables_noより取得
            $time_tables = new WorkingTimeTable();
            $target_dateYmd = date_format(new Carbon($target_date), 'Ymd');
            $time_tables->setParamdatefromAttribute($target_dateYmd);
            $time_tables->setParamdatetoAttribute($target_dateYmd);
            $time_tables->setParamDepartmentcodeAttribute($department_code);
            $time_tables->setParamUsercodeAttribute($user_code);
            $workingHours = $time_tables->getWorkingTimeTable();
            $regular_start_time = null;
            $regular_start_recordtime = null;
            $regular_start_record_date = null;
            $regular_end_time = null;
            $regular_end_recordtime = null;
            $regular_end_record_date = null;
            $lunch_start_time = null;
            $lunch_start_recordtime = null;
            $lunch_end_time = null;
            $lunch_end_recordtime = null;
            $regular_2after_recordtime = null;
            $after_legal_working_hours_day = null;
            /*  AM10:00からの休憩とかPM3:00からの休憩とかありうるので
            *  所定時間の開始時刻＋2時間後 <= 休憩開始 and 休憩時間が30分以上
            *  を昼休みの定義とする */
            $min_from_time = date_format(new Carbon($target_dateYmd.' 23:59:59'), 'Y-m-d H:i:s');
            $min_from_datetime = null;
            foreach($workingHours as $item) {
                if ($item->working_time_kubun == Config::get('const.C004.regular_working_time')) {
                    // 出退勤が複数回ある場合も最初のみ設定
                    if ($regular_start_time == null) { $regular_start_time = $item->working_timetable_from_time; }
                    if ($regular_start_recordtime == null) {
                        $regular_start_recordtime = $item->working_timetable_from_record_time;
                        $regular_start_record_date = date_format(new Carbon($regular_start_recordtime), 'Y-m-d');
                    }
                    if ($regular_end_time == null) { $regular_end_time = $item->working_timetable_to_time; }
                    if ($regular_end_recordtime == null) {
                        $regular_end_recordtime = $item->working_timetable_to_record_time;
                        $regular_end_record_date = date_format(new Carbon($regular_end_recordtime), 'Y-m-d');
                    }
                    // 所定時間の開始時刻から2時間後を求める
                    if ($regular_2after_recordtime == null) {
                        $after_legal_working_hours_day = 7200;
                        $regular_2after_recordtime = $this->getAfterDayTime(
                            $regular_start_recordtime,
                            $after_legal_working_hours_day,
                            'Y-m-d H:i:s');
                        $regular_2after_record_date = date_format(new Carbon($regular_2after_recordtime), 'Y-m-d');
                    }
                } elseif ($item->working_time_kubun == Config::get('const.C004.regular_working_breaks_time')) {
                    if ($regular_2after_recordtime != null) {
                        // 所定時刻内の休憩時間であるか判断する $regular_start_recordtime - $regular_end_recordtime
                        $working_timetable_from_record_datetime = null;
                        $working_timetable_to_record_datetime = null;
                        if ($regular_start_record_date == $regular_end_record_date) {
                            if (($item->working_timetable_from_record_time >= $regular_start_recordtime &&
                                $item->working_timetable_from_record_time <= $regular_end_recordtime) &&
                                ($item->working_timetable_to_record_time >= $regular_start_recordtime &&
                                $item->working_timetable_to_record_time <= $regular_end_recordtime)) {
                                $working_timetable_from_record_datetime = $item->working_timetable_from_record_time;
                                $working_timetable_to_record_datetime = $item->working_timetable_to_record_time;
                            }
                        } else {
                            // 所定時間が日またぎの場合
                            $w_working_timetable_from_record_datetime = $item->working_timetable_from_record_time;
                            $w_working_timetable_to_record_datetime = $item->working_timetable_to_record_time;
                            // Log::debug('getWorkingHours  $item->working_timetable_from_record_time = '.$item->working_timetable_from_record_time);
                            // Log::debug('getWorkingHours  $regular_start_recordtime = '.$regular_start_recordtime);
                            if ($item->working_timetable_from_record_time < $regular_start_recordtime) {
                                $w_working_timetable_from_record_datetime = 
                                    date_format(new Carbon($regular_end_record_date.' '.$item->working_timetable_from_time),'Y-m-d H:i:s');
                            }
                            // Log::debug('getWorkingHours  $item->working_timetable_to_record_time = '.$item->working_timetable_to_record_time);
                            // Log::debug('getWorkingHours  $regular_start_recordtime = '.$regular_start_recordtime);
                            if ($item->working_timetable_to_record_time < $regular_start_recordtime) {
                                $w_working_timetable_to_record_datetime = 
                                    date_format(new Carbon($regular_end_record_date.' '.$item->working_timetable_to_time),'Y-m-d H:i:s');
                            }
                            // Log::debug('getWorkingHours  $w_working_timetable_from_record_datetime = '.$w_working_timetable_from_record_datetime);
                            // Log::debug('getWorkingHours  $w_working_timetable_to_record_datetime = '.$w_working_timetable_to_record_datetime);
                            if (($w_working_timetable_from_record_datetime >= $regular_start_recordtime &&
                                $w_working_timetable_from_record_datetime <= $regular_end_recordtime) &&
                                ($w_working_timetable_to_record_datetime >= $regular_start_recordtime &&
                                $w_working_timetable_to_record_datetime <= $regular_end_recordtime)) {
                                $working_timetable_from_record_datetime = $w_working_timetable_from_record_datetime;
                                $working_timetable_to_record_datetime = $w_working_timetable_to_record_datetime;
                                // Log::debug('getWorkingHours  $working_timetable_from_record_datetime = '.$working_timetable_from_record_datetime);
                                // Log::debug('getWorkingHours  $working_timetable_to_record_datetime = '.$working_timetable_to_record_datetime);
                            }
                        }
                        if ($working_timetable_from_record_datetime != null && $working_timetable_to_record_datetime != null) {
                            if ($working_timetable_from_record_datetime >= $regular_2after_recordtime) {
                                $calc_times = $this->diffTimeSerial($working_timetable_from_record_datetime, $working_timetable_to_record_datetime);
                                // from-toで30分以上か？
                                // Log::debug('getWorkingHours  $calc_times = '.$calc_times);
                                if ($calc_times >= 1800) {
                                    $lunch_start_time = $item->working_timetable_from_time;
                                    $lunch_start_recordtime = $working_timetable_from_record_datetime;
                                    $lunch_end_time = $item->working_timetable_to_time;
                                    $lunch_end_recordtime = $working_timetable_to_record_datetime;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            // 設定
            $array_workingHours = array(
                'user_code' => $user_code,
                'department_code' => $department_code,
                'regular_start_time' => $regular_start_time,
                'regular_start_recordtime' => $regular_start_recordtime,
                'regular_end_time' => $regular_end_time,
                'regular_end_recordtime' => $regular_end_recordtime,
                'lunch_start_time' => $lunch_start_time,
                'lunch_start_recordtime' => $lunch_start_recordtime,
                'lunch_end_time' => $lunch_end_time,
                'lunch_end_recordtime' => $lunch_end_recordtime
            );
            return response()->json(
                ['result' => true, 'details' => $array_workingHours,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * ユーザー打刻時刻から所定時刻取得
     *
     * @return list
     */
    public function getWorkingHoursByStamp($params){
        $this->array_messagedata = array();
        try{
            $target_date = $params['target_date'];
            $user_code = $params['user_code'];
            $mode = $params['mode'];
            $department_code = "";
            if (isset($params['department_code'])) {
                $department_code = $params['department_code'];
            } else {
                // department_code取得する
                $datas = $this->getUserDepartmentEmploymentRole($user_code, $target_date);
                foreach ($datas as $item) {
                    if (isset($item->department_code)) {
                        $department_code = $item->department_code;
                    }
                    break;
                }
            }
            $record_datetime = $params['record_datetime'];
            // Log::debug('         apicommon getWorkingHoursByStamp = '.date_format(new Carbon($record_datetime), 'H:i:s'));
            $record_datetime_date = date_format(new Carbon($target_date), 'Y-m-d')." ".date_format(new Carbon($record_datetime), 'H:i:s');
            // Log::debug('         apicommon getWorkingHoursByStamp $record_datetime_date = '.$record_datetime_date);
            // usersのカレンダーからタイムテーブルの所定時刻を取得する
            $time_tables = new WorkingTimeTable();
            $target_dateYmd = date_format(new Carbon($target_date), 'Ymd');
            $time_tables->setParamdatefromAttribute($target_dateYmd);
            $time_tables->setParamdatetoAttribute($target_dateYmd);
            $time_tables->setParamDepartmentcodeAttribute($department_code);
            $time_tables->setParamUsercodeAttribute($user_code);
            // Log::debug('         apicommon getWorkingHoursByStamp $target_dateYmd = '.$target_dateYmd);
            // Log::debug('         apicommon getWorkingHoursByStamp $department_code = '.$department_code);
            // Log::debug('         apicommon getWorkingHoursByStamp $user_code = '.$user_code);
            $workingHours = $time_tables->getWorkingTimeTable();
            $working_from_time = null;
            $working_to_time = null;
            $working_to_time_date = null;
            // Log::debug('         apicommon getWorkingHoursByStamp $workingHours = '.count($workingHours));
            foreach($workingHours as $item) {
                // Log::debug('         apicommon getWorkingHoursByStamp $item->working_time_kubun = '.$item->working_time_kubun);
                if ($item->working_time_kubun == Config::get('const.C004.regular_working_time')) {
                    // Log::debug('         apicommon getWorkingHoursByStamp $record_datetime_date = '.$record_datetime_date);
                    // Log::debug('         apicommon getWorkingHoursByStamp $item->working_timetable_to_record_time = '.$item->working_timetable_to_record_time);
                    if ($mode == Config::get('const.C005.attendance_time') ||
                        $mode == Config::get('const.C005.missing_middle_time') ||
                        $mode == Config::get('const.C005.public_going_out_time')) {
                        if ($record_datetime_date < $item->working_timetable_to_record_time) {
                            // Log::debug('         apicommon getWorkingHoursByStamp $working_to_time_date = '.$working_to_time_date);
                            if ($working_from_time == null) {
                                $working_from_time = $item->working_timetable_from_time;
                                $working_to_time = $item->working_timetable_to_time;
                                $working_to_time_date = $item->working_timetable_to_record_time;
                            } elseif ($working_to_time_date > $item->working_timetable_to_record_time) {
                                $working_from_time = $item->working_timetable_from_time;
                                $working_to_time = $item->working_timetable_to_time;
                            }
                        }
                    } elseif ($mode == Config::get('const.C005.leaving_time') ||
                            $mode == Config::get('const.C005.missing_middle_return_time') ||
                            $mode == Config::get('const.C005.public_going_out_return_time')) {
                        if ($record_datetime_date > $item->working_timetable_to_record_time ||
                            ($record_datetime_date > $item->working_timetable_from_record_time &&
                            $record_datetime_date <= $item->working_timetable_to_record_time)) {
                            // Log::debug('         apicommon getWorkingHoursByStamp $working_to_time_date = '.$working_to_time_date);
                            if ($working_from_time == null) {
                                $working_from_time = $item->working_timetable_from_time;
                                $working_to_time = $item->working_timetable_to_time;
                                $working_to_time_date = $item->working_timetable_to_record_time;
                            } elseif ($working_to_time_date < $item->working_timetable_to_record_time) {
                                $working_from_time = $item->working_timetable_from_time;
                                $working_to_time = $item->working_timetable_to_time;
                            }
                        }
                    }
                    // Log::debug('         apicommon getWorkingHoursByStamp $working_from_time = '.$working_from_time);
                    // Log::debug('         apicommon getWorkingHoursByStamp $working_to_time = '.$working_to_time);
                }
            }
            // Log::debug('         apicommon getWorkingHoursByStamp $working_from_time = '.$working_from_time);
            // Log::debug('         apicommon getWorkingHoursByStamp $working_to_time = '.$working_to_time);
            // 設定
            $array_workingHours = array(
                'working_from_time' => $working_from_time,
                'working_to_time' => $working_to_time
            );
            return $array_workingHours;
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    // -------------  3.ユーザ情報取得  end -------------------------------------------------------- //
        
    
    // -------------  4.その他情報取得  start ------------------------------------------------------ //
    /**
     *  会社情報取得
     *
     * @return list departments
     */
    public function getCompanyInfoApply(Request $request){
        $this->array_messagedata = array();
        $details = new Collection();
        $result = true;
        try {
            $company_model = new Company();
            $details = $company_model->getCompanyInfoApply();

            return response()->json(
                ['result' => $result, 'details' => $details,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

    }

    /**
     * 指定月締日取得（画面から）
     *
     * @return list
     */
    public function getClosingDay(Request $request){
        
        $this->array_messagedata = array();
        $closing = null;
        $result = true;
        try{
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'closing' => $closing,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            if (!isset($params['target_date'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "target_date", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'closing' => $closing,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $target_date = $params['target_date'];
            // 設定マスタより締め日取得
            $target_date = $this->setRequestQeury($params['target_date']);
            $setting_model = new Setting();
            $target_dateYmd = new Carbon($target_date.'15');
            $setting_model->setParamFiscalmonthAttribute(date_format($target_dateYmd, 'm'));
            $setting_model->setParamYearAttribute(date_format($target_dateYmd, 'Y'));
            $closing = $setting_model->getMonthClosing();
            return response()->json(
                ['result' => true, 'closing' => $closing,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
            return $closing;
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 指定月締日取得
     *
     * @param target_ym YYYYMM
     * @return list
     */
    public function getCommonClosingDay($target_ym){
        $closing = null;
        try {
            // 設定マスタより締め日取得
            $target_dateYmd = new Carbon($target_ym.'15');
            $setting_model = new Setting();
            $setting_model->setParamFiscalmonthAttribute(date_format($target_dateYmd, 'm'));
            $setting_model->setParamYearAttribute(date_format($target_dateYmd, 'Y'));
            $closing = $setting_model->getMonthClosing();
            return $closing;
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
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
            $what_weekday['week_name'] = "（".Config::get('const.WEEK_KANJI.sat')."）";
        }elseif($dt->isSunday()){
            $what_weekday['id'] = Config::get('const.C006.sun');
            $what_weekday['week_name'] = "（".Config::get('const.WEEK_KANJI.sun')."）";
        }elseif($dt->isMonday()){
            $what_weekday['id'] = Config::get('const.C006.mon');
            $what_weekday['week_name'] = "（".Config::get('const.WEEK_KANJI.mon')."）";
        }elseif($dt->isTuesday()){
            $what_weekday['id'] = Config::get('const.C006.tue');
            $what_weekday['week_name'] = "（".Config::get('const.WEEK_KANJI.tue')."）";
        }elseif($dt->isWednesday()){
            $what_weekday['id'] = Config::get('const.C006.wed');
            $what_weekday['week_name'] = "（".Config::get('const.WEEK_KANJI.wed')."）";
        }elseif($dt->isThursday()){
            $what_weekday['id'] = Config::get('const.C006.thu');
            $what_weekday['week_name'] = "（".Config::get('const.WEEK_KANJI.thu')."）";
        }elseif($dt->isFriday()){
            $what_weekday['id'] = Config::get('const.C006.fri');
            $what_weekday['week_name'] = "（".Config::get('const.WEEK_KANJI.fri')."）";
        }

        return $what_weekday;
    }

    /**
     * 日付のフォーマット YYYY年MM月DD日（WEEK）
     *
     * @param [type] $dt
     * @param [type] $format
     * @return array
     */
    public function getYMDWeek($dt){
        // フォーマット 2019年10月01日(火)
        $array_week = array();
        $array_week[] = Config::get('const.WEEK_KANJI.sun');
        $array_week[] = Config::get('const.WEEK_KANJI.mon');
        $array_week[] = Config::get('const.WEEK_KANJI.tue');
        $array_week[] = Config::get('const.WEEK_KANJI.wed');
        $array_week[] = Config::get('const.WEEK_KANJI.thu');
        $array_week[] = Config::get('const.WEEK_KANJI.fri');
        $array_week[] = Config::get('const.WEEK_KANJI.sat');
        $target_date = new Carbon($dt);
        $date_name = date_format($target_date, 'Y年m月d日')."（".$array_week[$target_date->dayOfWeek]."）";
//        $date_name = date_format($target_date, 'Ymd');
        return $date_name;
    }

    /**
     * 日付のフォーマット YYYY年MM月DD日（WEEK）
     *
     * @param [type] $dt
     * @param [type] $format
     * @return array
     */
    // public function getYMDWeek($dt){
    //     // フォーマット 2019年10月01日(火)
    //     $date_name = '';
    //     $calender_model = new Calendar();
    //     $calender_model->setParamfromdateAttribute(date_format(new Carbon($dt), 'Ymd'));
    //     $calender_model->setDateAttribute(date_format(new Carbon($dt), 'Ymd'));
    //     $calendars = $calender_model->getCalenderDate();
    //     if (count($calendars) > 0) {
    //         foreach ($calendars as $result) {
    //             if (isset($result->date_name)) {
    //                 $date_name = $result->date_name;
    //             }
    //             break;
    //         }
    //     }
    //     return $date_name;
    // }

    /**
     * 勤務状況取得
     *
     * @param [type] $dt
     * @param [type] $format
     * @return array
     */
    public function getWorgingStatusInfo(Request $request){
        $result = true;
        $details = array();
        try {
            $worktimelog_model = new WorkTimeLog();
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $details,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            if (!isset($params['target_date'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "target_date", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $details,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            // $params['target_date'] = "2020-04-08 08:00:00";
            $target_date = $params['target_date'];
            $details = $worktimelog_model->getWorkinTimeLog(date_format(new Carbon($target_date), 'Ymd'));
            $result_details = Collect($details);
            $ondetails = $result_details->whereIn('mode', [
                Config::get('const.C005.attendance_time'),
                Config::get('const.C005.missing_middle_return_time'),
                Config::get('const.C005.public_going_out_return_time'),
                Config::get('const.C005.emergency_time')]);
            $offdetails = $result_details->whereIn('mode', [
                Config::get('const.C005.leaving_time'),
                Config::get('const.C005.missing_middle_time'),
                Config::get('const.C005.public_going_out_time'),
                Config::get('const.C005.emergency_return_time'),
                null]);
            return response()->json(
                ['result' => true, 'ondetails' => $ondetails, 'offdetails' => $offdetails,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }


    /**
     * CSV対象項目取得
     *
     * @param [type] $dt
     * @param [type] $format
     * @return array
     */
    public function getCsvItem(Request $request){
        $result = true;
        $details = array();
        try {
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $details,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            if (!isset($params['selection_code'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "selection_code", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $details,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $selection_code = $params['selection_code'];
            $is_select = null;
            if (isset($params['is_select'])) {
                $is_select = $params['is_select'];
            }
            $csvitem_model = new CsvItemSelection();
            $csvitem_model->setParamaccountidAttribute(
                array(Config::get('const.ACCOUNTID.account_id')));
            $csvitem_model->setParamselectioncodeAttribute($selection_code);
            $csvitem_model->setParamisselectAttribute($is_select);
            $details = $csvitem_model->getCsvItem();
            return response()->json(
                ['result' => true, 'details' => $details,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * お知らせ取得
     *
     * @return list
     */
    public function getPostInformations(Request $request){
        // $details = new Collection();
        try {
            $details =
                DB::table($this->table_post_informations)
                    ->select('id','content','created_at')
                    ->where('is_deleted', 0)
                    ->orderby('created_at','desc')
                    ->get();

            // foreach($details as $item){
            //     $temp_date = new Carbon($item->created_at);
            //     $item->created_at = $temp_date->format('Y年m月d日');
            // }
            return $details;
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_post_informations, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_post_informations, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    // -------------  4.その他情報取得  end ------------------------------------------------------ //
    
    // -------------  5.算出情報取得  start ------------------------------------------------------ //

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
     * 指定時間（スタンプ）後を求める
     *
     * @return 指定時間後（前）
     */
    public function getAfterDayTime($target_date, $add_time, $str_format){
        $dt = new Carbon($target_date);
        if ($add_time > 0) {
            return date_format($dt->addSecond($add_time), $str_format);
        } elseif($add_time == 0) {
            return date_format($dt, $str_format);
        } else {
            return date_format($dt->subSecond(0 - $add_time), $str_format);
        }
    }

    /**
     * 時間範囲内に休憩時間が何時間あるか求める（日次集計用）
     * 
     *      前提
     *          target_from_datetime <= target_to_datetime
     *
     * @return 
     */
    public function calcBetweenBreakTime(
        $target_from_datetime, $target_to_datetime, $current_date,
        $timetables, $working_timetable_no,
        $setting_from_datetime, $setting_to_datetime){
        // 休憩時間を含んでいる場合、休憩時間累計を求めて減算する
        // 休憩でフィルター
        $filtered = $timetables->where('no', $working_timetable_no)
            ->where('working_time_kubun', Config::get('const.C004.regular_working_breaks_time'));
        // 休憩時間帯は複数あるかも
        $calc_times = 0;
        foreach($filtered as $result_breaks_time) {
            $from_time = $result_breaks_time->from_time;        // 休憩開始時刻(H:i:s)
            $to_time = $result_breaks_time->to_time;            // 休憩終了時刻(H:i:s)
            if (isset($from_time) && isset($to_time)) {
                // まずはcurrent_dateで判定
                $current_date_ymd = date_format(new Carbon($current_date),'Y-m-d');
                // from_time日付付与
                $time_calc_from = $current_date_ymd.' '.$from_time;
                // $time_calc_from = 
                //     $this->convTimeToDateFrom($from_time, $current_date, $target_from_datetime, $target_to_datetime);         
                // to_time日付付与
                $time_calc_to = $current_date_ymd.' '.$to_time;
                // $time_calc_to = 
                //     $this->convTimeToDateTo($from_time, $to_time, $current_date, $target_from_datetime, $target_to_datetime);
                // calcBetweenBreakTimeSub implement
                $array_impl_calcBetweenBreakTimeSub = array (
                    'time_calc_from' => $time_calc_from,
                    'time_calc_to' => $time_calc_to,
                    'setting_from_datetime' => $setting_from_datetime,
                    'setting_to_datetime' => $setting_to_datetime,
                    'target_from_datetime' => $target_from_datetime,
                    'target_to_datetime' => $target_to_datetime
                );
                $calc_times_sub = $this->calcBetweenBreakTimeSub($array_impl_calcBetweenBreakTimeSub);
                if ($calc_times_sub == 0) {
                    // current_dateの翌日で判定
                    $dt = new Carbon($current_date);
                    $current_date_ymd = date_format($dt->copy()->addDay(),'Y-m-d');
                    // from_time日付付与
                    $time_calc_from = $current_date_ymd.' '.$from_time;
                    // to_time日付付与
                    $time_calc_to = $current_date_ymd.' '.$to_time;
                    $array_impl_calcBetweenBreakTimeSub = array (
                        'time_calc_from' => $time_calc_from,
                        'time_calc_to' => $time_calc_to,
                        'setting_from_datetime' => $setting_from_datetime,
                        'setting_to_datetime' => $setting_to_datetime,
                        'target_from_datetime' => $target_from_datetime,
                        'target_to_datetime' => $target_to_datetime
                    );
                    $calc_times_sub = $this->calcBetweenBreakTimeSub($array_impl_calcBetweenBreakTimeSub);
                }
                $calc_times += $calc_times_sub;
            }
        }
        return $calc_times;
    }

    /**
     * 時間範囲内に休憩時間が何時間あるか求める（日次集計用）
     * 
     * @return 
     */
    public function calcBetweenBreakTimeSub($params)
    {
        $time_calc_from = $params['time_calc_from'];
        $time_calc_to = $params['time_calc_to'];
        $setting_from_datetime = $params['setting_from_datetime'];
        $setting_to_datetime = $params['setting_to_datetime'];
        $target_from_datetime = $params['target_from_datetime'];
        $target_to_datetime = $params['target_to_datetime'];

        $calc_times = 0;
        $chk_time = true;
        if (isset($setting_from_datetime) && isset($setting_to_datetime)) {
            // タイムテーブル設定時刻のチェックを行う場合
            // タイムテーブル時間範囲内に休憩開始終了時刻がある場合に計算する
            // Log::debug('calcBetweenBreakTime $time_calc_from = '.$time_calc_from);
            // Log::debug('calcBetweenBreakTime $time_calc_to = '.$time_calc_to);
            // Log::debug('calcBetweenBreakTime $setting_from_datetime = '.$setting_from_datetime);
            // Log::debug('calcBetweenBreakTime $setting_to_datetime = '.$setting_to_datetime);
            if (($time_calc_from <= $setting_from_datetime || $time_calc_from >= $setting_to_datetime) &&
                ($time_calc_to <= $setting_from_datetime || $time_calc_to >= $setting_to_datetime)) {
                $chk_time = false;
            }
        }
        if ($chk_time) {
            //  指定時間範囲内に休憩開始終了時刻がある場合に計算する
            // Log::debug('calcBetweenBreakTime $time_calc_from = '.$time_calc_from);
            // Log::debug('calcBetweenBreakTime $time_calc_to = '.$time_calc_to);
            // Log::debug('calcBetweenBreakTime $target_from_datetime = '.$target_from_datetime);
            // Log::debug('calcBetweenBreakTime $target_to_datetime = '.$target_to_datetime);
            if (($time_calc_from >= $target_from_datetime && $time_calc_from <= $target_to_datetime) ||
                ($time_calc_to >= $target_from_datetime && $time_calc_to <= $target_to_datetime)) {
                if ($target_from_datetime > $time_calc_from) {
                    $time_calc_from = $target_from_datetime;
                }
                if ($target_to_datetime < $time_calc_to) {
                    $time_calc_to = $target_to_datetime;
                }
                // Log::debug('calcBetweenBreakTime $time_calc_from = '.$time_calc_from);
                // Log::debug('calcBetweenBreakTime $time_calc_to = '.$time_calc_to);
                if ($time_calc_from < $time_calc_to) {
                    $calc_times += $this->diffTimeSerial($time_calc_from, $time_calc_to);
                }
            }
        }
        return $calc_times;
    }

    /**
     * 時間差を求める（時間）
     *
     * @return 時間差
     */
    public function diffTimeTime($time_from, $time_to){
        $from = new Carbon($from);
        $to   = new Carbon($to); 
        $interval = $from->diff($to);
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
        $interval = $to - $from;
        return $interval;
    }
    
    /**
     * 時間差を秒まで求める（シリアルで返却）
     *
     * @return 時間差
     */
    public function diffSecoundSerial($time_from, $time_to){
        $from = strtotime(date('Y-m-d H:i:ss',strtotime($time_from)));
        $to   = strtotime(date('Y-m-d H:i:ss',strtotime($time_to))); 
        $from = strtotime($time_from);
        $to   = strtotime($time_to); 
        $interval = $to - $from;
        return $interval;
    }

    /**
     * 時間丸め処理（時間丸めする：出勤用）
     *
     * @return 分で返却
     */
    public function roundTimeByTimeStart($params){

        $current_date = $params['current_date'];
        $start_time = $params['start_time'];
        $time_unit = $params['time_unit'];
        $time_rounding = $params['time_rounding'];
        $working_timetable_no = $params['working_timetable_no'];
        $array_get_timetable_result = $params['array_get_timetable_result'];
        // 1分単位の場合はそのまま
        if ($time_unit == Config::get('const.C009.round1')) {
            return $start_time;
        }
        // start_time時刻が丸めタイムテーブルのどこに該当するか決める
        // start_time時刻<丸めタイムテーブルの労働開始時間
        $dt = new Carbon($start_time);
        $target_his = $dt->format("His");
        $source_start_time = null;
        $source_to_time = null;
        $last_start_time = null;
        foreach($array_get_timetable_result as $item) {
            if ($item['no'] == $working_timetable_no) {
                foreach($item['timetable'] as $edt_item) {
                    $last_start_time = $edt_item['from_time'];
                    if ($target_his < $edt_item['from_time']) {
                        $source_start_time = $edt_item['from_time'];
                        break;
                    }
                }
                break;
            }
        }
        // データない場合はそのまま
        if ($last_start_time == null) {
            return $start_time;
        }
        if ($source_start_time == null || $source_start_time == "") {
            $source_start_time = $last_start_time;
        }
        // 
        $carbon_ymd = $dt->format("Y-m-d");
        $source_dt = new Carbon($carbon_ymd." ".$source_start_time);
        $calc_times = $this->diffTimeSerial($start_time, $source_dt);
        // 分単位
        $calc_times_unit = 1;
        if ($time_unit == Config::get('const.C009.round5')) {
            $calc_times_unit = 5 * 60;
        } elseif ($time_unit == Config::get('const.C009.round10')) {
            $calc_times_unit = 10 * 60;
        } elseif ($time_unit == Config::get('const.C009.round15')) {
            $calc_times_unit = 15 * 60;
        } elseif ($time_unit == Config::get('const.C009.round30')) {
            $calc_times_unit = 30 * 60;
        } elseif ($time_unit == Config::get('const.C009.round60')) {
            $calc_times_unit = 60 * 60;
        }
        // start_time丸め
        $calc_times_round = floor($calc_times / $calc_times_unit) * $calc_times_unit;
        // 出勤時刻が労働時間開始前
        if ($target_his <= $source_start_time) {
            // source_dtの$calc_times_round前
            $result_round_time = date('Y-m-d H:i:00',strtotime((0-$calc_times_round).' second',strtotime($source_dt)));
        } else {
            // source_dtの$calc_times_round後
            if ($calc_times_round < 0) {
                $calc_times_round = 0-$calc_times_round;
            }
            $result_round_time = date('Y-m-d H:i:00',strtotime(('+'.$calc_times_round).' second',strtotime($source_dt)));
        }
        return $result_round_time;
    }

    /**
     * 時間丸め処理（時間丸めする：退勤用）
     *
     * @return 分で返却
     */
    public function roundTimeByTimeEnd($params){

        $current_date = $params['current_date'];
        $end_time = $params['end_time'];
        $time_unit = $params['time_unit'];
        $time_rounding = $params['time_rounding'];
        $working_timetable_no = $params['working_timetable_no'];
        $array_get_timetable_result = $params['array_get_timetable_result'];
        // 1分単位の場合はそのまま
        if ($time_unit == Config::get('const.C009.round1')) {
            return $end_time;
        }
        // end_time時刻が丸めタイムテーブルのどこに該当するか決める
        // end_time時刻<丸めタイムテーブルの労働開始時間
        $dt = new Carbon($end_time);
        $target_his = $dt->format("His");
        $source_end_time = null;
        $source_to_time = null;
        $last_end_time = null;
        foreach($array_get_timetable_result as $item) {
            if ($item['no'] == $working_timetable_no) {
                foreach($item['timetable'] as $edt_item) {
                    $last_end_time = $edt_item['to_time'];
                    if ($target_his < $edt_item['to_time']) {
                        $source_end_time = $edt_item['to_time'];
                        break;
                    }
                }
                break;
            }
        }
        // データない場合はそのまま
        if ($last_end_time == null) {
            return $end_time;
        }
        if ($source_end_time == null || $source_end_time == "") {
            $source_end_time = $last_end_time;
        }
        // 
        $carbon_ymd = $dt->format("Y-m-d");
        $source_dt = new Carbon($carbon_ymd." ".$source_end_time);
        $calc_times = $this->diffTimeSerial($end_time, $source_dt);
        // 分単位
        $calc_times_unit = 1;
        if ($time_unit == Config::get('const.C009.round5')) {
            $calc_times_unit = 5 * 60;
        } elseif ($time_unit == Config::get('const.C009.round10')) {
            $calc_times_unit = 10 * 60;
        } elseif ($time_unit == Config::get('const.C009.round15')) {
            $calc_times_unit = 15 * 60;
        } elseif ($time_unit == Config::get('const.C009.round30')) {
            $calc_times_unit = 30 * 60;
        } elseif ($time_unit == Config::get('const.C009.round60')) {
            $calc_times_unit = 60 * 60;
        }
        // end_time丸め
        $calc_times_round_double = $calc_times / $calc_times_unit;
        $calc_times_round_floor = floor($calc_times_round_double);
        if ($calc_times_round_double == $calc_times_round_floor) {
            $calc_times_round = floor($calc_times / $calc_times_unit) * $calc_times_unit;
        } else {
            $calc_times_round = $calc_times_unit + (floor($calc_times / $calc_times_unit) * $calc_times_unit);
        }
        // 退勤時刻が労働時間終了前
        if ($target_his <= $source_end_time) {
            // source_dtの$calc_times_round前
            $result_round_time = date('Y-m-d H:i:00',strtotime((0-$calc_times_round).' second',strtotime($source_dt)));
        } else {
            // source_dtの$calc_times_round後
            if ($calc_times_round < 0) {
                $calc_times_round = 0-$calc_times_round;
            }
            $result_round_time = date('Y-m-d H:i:00',strtotime(('+'.$calc_times_round).' second',strtotime($source_dt)));
        }
        return $result_round_time;
    }

    /**
     * 時間丸め処理（シリアルで丸めする）
     *
     * @return 分で返却
     */
    public function roundTime($round_time, $time_unit, $time_rounding){

        if ($time_rounding == Config::get('const.C010.round_half_up')) {
            // 四捨五入
            if ($time_unit == Config::get('const.C009.round1')) {
                // 分求める
                $result_round_time = round($round_time / 60);
                } elseif ($time_unit == Config::get('const.C009.round5')) {
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
            } elseif ($time_unit == Config::get('const.C009.round10')) {
                // 分求める
                $result_round_time = round($round_time / 60 / 10) * 10;
            } elseif ($time_unit == Config::get('const.C009.round15')) {
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
            } elseif ($time_unit == Config::get('const.C009.round30')) {
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
            } elseif ($time_unit == Config::get('const.C009.round60')) {
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
            if ($time_unit == Config::get('const.C009.round1')) {
                // 分求める
                $result_round_time = floor($round_time / 60);
            } elseif ($time_unit == Config::get('const.C009.round5')) {
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
            } elseif ($time_unit == Config::get('const.C009.round10')) {
                // 分求める
                $result_round_time = floor($round_time / 60 / 10) * 10;
            } elseif ($time_unit == Config::get('const.C009.round15')) {
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
            } elseif ($time_unit == Config::get('const.C009.round30')) {
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
            } elseif ($time_unit == Config::get('const.C009.round60')) {
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
            if ($time_unit == Config::get('const.C009.round1')) {
                // 分求める
                $result_round_time = ceil($round_time / 60);
            } elseif ($time_unit == Config::get('const.C009.round5')) {
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
            } elseif ($time_unit == Config::get('const.C009.round10')) {
                // 分求める
                $result_round_time = ceil($round_time / 60 / 10) * 10;
            } elseif ($time_unit == Config::get('const.C009.round15')) {
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
            } elseif ($time_unit == Config::get('const.C009.round30')) {
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
            } elseif ($time_unit == Config::get('const.C009.round60')) {
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
        } elseif ($time_unit == Config::get('const.C010.non')) {
            // なし
            $result_round_time = $round_time / 60;
        } else {
            $result_round_time = $round_time / 60;
        }

        return $result_round_time;
    }

    // -------------  5.算出情報取得  end ------------------------------------------------------- //
    
    // -------------  6.判定・チェック start ----------------------------------------------------- //
 
    /**
     * 法定法定外休日判定
     * 
     *
     * @return 
     */
    public function jdgBusinessKbn($params)
    {
        $departmentcode = $params['departmentcode'];
        $employmentstatus = $params['employmentstatus'];
        $usercode = $params['usercode'];
        $datefrom = $params['datefrom'];
        // 指定日が休日かどうか
        $calender_setting_model = new CalendarSettingInformation();
        $calender_setting_model->setParamdepartmentcodeAttribute($departmentcode);
        $calender_setting_model->setParamemploymentstatusAttribute($employmentstatus);
        $calender_setting_model->setParamusercodeAttribute($usercode);
        $calender_setting_model->setParamfromdateAttribute($datefrom);
        $calender_setting_model->setParamlimitAttribute(1);
        $calendars = $calender_setting_model->getCalenderInfo();
        $business_kubun = null;
        foreach ($calendars as $result) {
            if (isset($result->business_kubun)) {
                $business_kubun = $result->business_kubun;
            }
            break;
        }

        return $business_kubun;
    }
 
    /**
     * 法定法定外休日判定
     * 
     *
     * @return 
     */
    // public function jdgBusinessKbn($params)
    // {
    //     $departmentcode = $params['departmentcode'];
    //     $employmentstatus = $params['employmentstatus'];
    //     $usercode = $params['usercode'];
    //     $datefrom = $params['datefrom'];
    //     // 指定日が休日かどうか
    //     $business_kubun = null;
    //     $calender_model = new Calendar();
    //     $calender_model->setParamdepartmentcodeAttribute($departmentcode);
    //     $calender_model->setParamemploymentstatusAttribute($employmentstatus);
    //     $calender_model->setParamusercodeAttribute($usercode);
    //     $calender_model->setParamfromdateAttribute($datefrom);
    //     $calendars = $calender_model->getCalenderDate();
    //     if (count($calendars) > 0) {
    //         foreach ($calendars as $result) {
    //             if (isset($result->business_kubun)) {
    //                 $business_kubun = $result->business_kubun;
    //             }
    //             break;
    //         }
    //     }

    //     return $business_kubun;
    // }

    /**
     * 時間範囲内であるか判定
     * 
     *      target_from_datetime～target_to_datetimeはchk_from_datetime～chk_to_datetimeの範囲内に一部または全部あるか判定
     *      前提
     *          target_from_datetime <= target_to_datetime
     *          chk_from_datetime <= chk_to_datetime
     *
     * @return 
     */
    public function chkBetweenTime($target_from_datetime, $target_to_datetime, $chk_from_datetime, $chk_to_datetime){

        $chk_result = true;

        if ($target_from_datetime <=  $chk_from_datetime) {
            if ($target_to_datetime <=  $chk_from_datetime) {
                $chk_result = false;
            }
        } elseif ($target_from_datetime >=  $chk_to_datetime) {
            $chk_result = false;
        }
        return $chk_result;
    }
    
    /**
     * 出勤時間差をチェック
     * 
     *  設定する時刻はDATETIMEで
     *
     * @return 時間差
     */
    public function chkInteval($target_datetime, $before_datetime){
        // 設定項目よりインターバル時間を取得
        $setting_model = new Setting();
        $dt = new Carbon($target_datetime);
        $setting_model->setParamYearAttribute(date_format($dt, 'Y'));
        $setting_model->setParamFiscalmonthAttribute(date_format($dt, 'm'));
        $settings = $setting_model->getSettingDatas();
        $interval = 0;
        foreach($settings as $setting) {
            if (isset($setting->interval)) {
                $interval = $setting->interval;
                break;
            }
        }
        // 設定されていない場合はチェック不要
        if ($interval == 0) {return true;}
        // $target_datetime - $before_datetime <= $interval であること
        $diffInterval = $this->diffTimeSerial($before_datetime, $target_datetime);
        // $intervalも$diffIntervalもシリアル値
        if ($diffInterval < $interval * 60 * 60) {
            return Config::get('const.C018.interval_stamp');
        }
        return Config::get('const.RESULT_CODE.normal');
    }
    
    /**
     * 打刻のモードチェック
     *
     *  $target_mode：現打刻モード
     *  $source_mode：前回打刻モード
     * 
     * @return チェック結果
     */
    public function chkMode($target_mode, $source_mode){

        if ( $source_mode == '') {
            if ($target_mode == Config::get('const.C005.attendance_time')) {
                return Config::get('const.RESULT_CODE.normal');
            }
            return Config::get('const.C018.forget_stamp');
        }
        if ($target_mode == Config::get('const.C005.attendance_time')) {
            if ($source_mode == Config::get('const.C005.leaving_time')) {
                return Config::get('const.RESULT_CODE.normal');
            }
        } elseif ($target_mode == Config::get('const.C005.leaving_time')) {
            if ($source_mode == Config::get('const.C005.attendance_time')) {
                return Config::get('const.RESULT_CODE.normal');
            }
            if ($source_mode == Config::get('const.C005.missing_middle_return_time')) {
                return Config::get('const.RESULT_CODE.normal');
            }
            if ($source_mode == Config::get('const.C005.public_going_out_return_time')) {
                return Config::get('const.RESULT_CODE.normal');
            }
        } elseif ($target_mode == Config::get('const.C005.missing_middle_time')) {
            if ($source_mode == Config::get('const.C005.attendance_time')) {
                return Config::get('const.RESULT_CODE.normal');
            }
            if ($source_mode == Config::get('const.C005.missing_middle_return_time')) {
                return Config::get('const.RESULT_CODE.normal');
            }
            if ($source_mode == Config::get('const.C005.public_going_out_return_time')) {
                return Config::get('const.RESULT_CODE.normal');
            }
        } elseif ($target_mode == Config::get('const.C005.missing_middle_return_time')) {
            if ($source_mode == Config::get('const.C005.missing_middle_time')) {
                return Config::get('const.RESULT_CODE.normal');
            }
        } elseif ($target_mode == Config::get('const.C005.public_going_out_time')) {
            if ($source_mode == Config::get('const.C005.attendance_time')) {
                return Config::get('const.RESULT_CODE.normal');
            }
            if ($source_mode == Config::get('const.C005.missing_middle_return_time')) {
                return Config::get('const.RESULT_CODE.normal');
            }
            if ($source_mode == Config::get('const.C005.public_going_out_return_time')) {
                return Config::get('const.RESULT_CODE.normal');
            }
        } elseif ($target_mode == Config::get('const.C005.public_going_out_return_time')) {
            if ($source_mode == Config::get('const.C005.public_going_out_time')) {
                return Config::get('const.RESULT_CODE.normal');
            }
        } elseif ($target_mode == Config::get('const.C005.emergency_time')) {
            if ($source_mode == Config::get('const.C005.emergency_return_time')) {
                return Config::get('const.RESULT_CODE.normal');
            }
        } elseif ($target_mode == Config::get('const.C005.emergency_return_time')) {
            if ($source_mode == Config::get('const.C005.emergency_time')) {
                return Config::get('const.RESULT_CODE.normal');
            }
        } else {
            return Config::get('const.C018.forget_stamp');
        }
        return Config::get('const.C018.forget_stamp');
    }
  
    /**
     * 緊急かの判定
     * 
     *
     * @return 
     */
    public function isEmagency($working_timetable_no)
    {
        if ($working_timetable_no == Config::get('const.C999_NAME.emergency_timetable_no')) {
            return true;
        }
        return false;
    }
    // -------------  6.判定・チェック end ----------------------------------------------------- //


    // -------------  7.変換 start ------------------------------------------------------------ //
    
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
        if ($target_time < date_format($dt, 'H:i:s')) {
            $dt = new Carbon($target_time);
            $convDateTime = $this->getNextDay($basic_date, 'Y-m-d').' '.date_format($dt, 'H:i:s');
        } else {
            $dt = new Carbon($basic_date);
            $convDateTime = date_format($dt, 'Y-m-d').' '.$target_time;
        }

        return $convDateTime;
    }

    /**
     * 時刻日付変換
     *      target_timeがbasic_from_timeから24:00:00の時刻内であるときはbasic_from_timeと同じ日付を設定
     *      上記以外は
     *      target_timeが00:00:00からbasic_to_timeの時刻内であるときはbasic_to_timeと同じ日付を設定
     * @return 日付時刻
     */
    public function convTimeToDateTarget($target_time, $basic_from_time, $basic_to_time){
        // 日付付与
        $dt_from = new Carbon($basic_from_time);
        $dt_from_ymd = date_format($dt_from, 'Y-m-d');
        $dt_from23 = new Carbon($dt_from_ymd.' 23:59:59');
        $dt_from24 = $dt_from23->addSecond();
        $dt_target_time = new Carbon($dt_from_ymd.' '.$target_time);
        if ($dt_target_time >= $dt_from && $dt_target_time <= $dt_from24) {
            return $dt_target_time;
        }

        $dt_from = new Carbon($basic_to_time);
        $dt_from_ymd = date_format($dt_from, 'Y-m-d');
        $dt_from00 = new Carbon($dt_from_ymd.' 00:00:00');
        $dt_target_time = new Carbon($dt_from_ymd.' '.$target_time);
        if ($dt_target_time >= $dt_from00 && $dt_target_time <= $dt_from) {
            return $dt_target_time;
        }

        return null;
    }

    /**
     * 時刻日付変換from
     * @return 日付時刻
     */
    public function convTimeToDateFrom($from_time, $current_date, $target_from_time, $target_to_time){

        $current_date_ymd = date_format(new Carbon($current_date),'Ymd');
        $target_from_ymd = date_format(new Carbon($target_from_time),'Ymd');
        $target_from_his = date_format(new Carbon($target_from_time),'His');
        $target_to_ymd = date_format(new Carbon($target_to_time),'Ymd');
        $target_to_his = date_format(new Carbon($target_to_time),'His');
        // 日付付与
        $cnv_from_date = null;
        if ($current_date_ymd == $target_from_ymd) {
            if ($target_from_his > $from_time && $from_time >= Config::get('const.C015.night_to')) {
                //$cnv_from_date = new Carbon($target_from_ymd.' '.$target_from_his);
                $cnv_from_date = new Carbon($target_from_ymd.' '.$from_time);
            } else {
                if ($from_time >= Config::get('const.C015.night_to')) {
                    $cnv_from_date = new Carbon($target_from_ymd.' '.$from_time);
                } else {
                    $w_edt_date = new Carbon($target_from_ymd);
                    $w_edt_date = date_format($w_edt_date->addDay(),'Y-m-d');
                    $cnv_from_date = new Carbon($w_edt_date.' '.$from_time);
                }
            }
        } else {
            //$cnv_from_date = new Carbon($target_from_ymd.' '.$target_from_his);
            $cnv_from_date = new Carbon($target_from_ymd.' '.$from_time);
        }

        return $cnv_from_date;
    }

    /**
     * 時刻日付変換to
     * @return 日付時刻
     */
    public function convTimeToDateTo($from_time, $to_time, $current_date, $target_from_time, $target_to_time){

        $current_date_ymd = date_format(new Carbon($current_date),'Ymd');
        $target_from_ymd = date_format(new Carbon($target_from_time),'Ymd');    // 打刻時刻
        $target_from_his = date_format(new Carbon($target_from_time),'His');    // 打刻時刻
        $target_to_ymd = date_format(new Carbon($target_to_time),'Ymd');        // 打刻時刻
        $target_to_his = date_format(new Carbon($target_to_time),'His');        // 打刻時刻
        // 日付付与
        $cnv_to_date = null;
        if ($current_date_ymd == $target_to_ymd) {
            if ($from_time > $to_time) {
                //$cnv_to_date = new Carbon($target_to_ymd.' '.$target_to_his);
                $cnv_to_date = $this->getNextDay($target_to_ymd, 'Y-m-d').' '.$to_time;
            } else {
                if ($target_to_his < $to_time) {
                    $cnv_to_date = new Carbon($target_to_ymd.' '.$to_time);
                } else {
                    $cnv_to_date = new Carbon($target_to_ymd.' '.$to_time);
                }
            }
        } else {
            if ($from_time > $to_time) {
                if ($target_to_his < $to_time) {
                    //$cnv_to_date = new Carbon($target_to_ymd.' '.$target_to_his);
                    $cnv_to_date = new Carbon($target_to_ymd.' '.$to_time);
                } else {
                    $cnv_to_date = new Carbon($target_to_ymd.' '.$to_time);
                }
            } else {
                if ($to_time >= Config::get('const.C015.night_to')) {
                    $cnv_to_date = new Carbon($current_date_ymd.' '.$to_time);
                } else {
                    $cnv_to_date = new Carbon($target_to_ymd.' '.$to_time);
                }
            }
        }

        return $cnv_to_date;
    }
    
    /**
     * インターバル時間を取得して分に変換する
     * 
     *  設定する時刻はDATEで
     *
     * @return インターバル時間 (H:i:s)
     */
    public function getIntevalMinute($target_date){
        // 設定項目よりインターバル時間を取得
        $setting_model = new Setting();
        $dt = new Carbon($target_date);
        $setting_model->setParamYearAttribute(date_format($dt, 'Y'));
        $setting_model->setParamFiscalmonthAttribute(date_format($dt, 'm'));
        $settings = $setting_model->getSettingDatas();
        $interval = 0;
        foreach($settings as $setting) {
            if (isset($setting->interval)) {
                $interval = $setting->interval;
                break;
            }
        }
        $hh = floor($interval);
        $mm = ($interval - floor($interval)) * 60;
        return str_pad($hh, 2 , '0', STR_PAD_LEFT).":".str_pad($mm, 2 , '0', STR_PAD_LEFT).":00";
    }
    
    /**
     * timestampを時間単位の10進数に変換する
     * 
     *  設定する時刻はDATEで
     *
     * @return 99.99
     */
    public function cnvToDecFromStamp($target_time){
        $time_division = $target_time / 3600;
        $time_int = floor($time_division);
        $convert_time = $time_int;
        // 小数第３位が0でない場合は四捨五入
        $w_time_decimal_part = $time_division - $time_int;
        if ($w_time_decimal_part > 0) {
            $w_time_decimal_part_3 = $w_time_decimal_part * 100;
            $w_time_decimal_part_31 = $w_time_decimal_part_3 - floor($w_time_decimal_part_3);
            if ($w_time_decimal_part_31 > 0) {
                $convert_time += (ceil($w_time_decimal_part_3) / 100);
            } else {
                $convert_time += $w_time_decimal_part;
            }
        }
        return $convert_time;
    }
    
    /**
     * timestampをHisに変換する
     * 
     *  設定する時刻はDATEで
     *
     * @return 99.99
     */
    public function cnvToHisFromStamp($target_stamp){
        $dt_basic = new Carbon('2019-01-01 00:00:00');
        return date('H:i:00',strtotime(('+'.$target_stamp).' second',strtotime($dt_basic)));
    }

    // -------------  7.変換 end -------------------------------------------------------------- //

    // -------------  8.設定 start ------------------------------------------------------------ //

    /**
     * タイムテーブルの分解
     *
     * @return 
     */
    public function analyzeTimeTable($timetables, $working_time_kubun, $working_timetable_no){
        $array_times = array();
        if ($working_time_kubun != Config::get('const.C004.out_of_regular_working_time')) {
            $filtered = $timetables->where('no', $working_timetable_no)->where('working_time_kubun', $working_time_kubun);
            foreach($filtered as $result_time) {
                if (isset($result_time->from_time) && isset($result_time->to_time)) {
                    $array_times[] = array('from_time' => $result_time->from_time , 'to_time' => $result_time->to_time);
                }
            }
        } else {
            // 時間外労働時間は画面からの入力がないため、所定労働時間と休憩と深夜労働時間の時間以外を時間外労働時間とする
            $array_sets = array();
            for ($i=0;$i<24;$i++) {
                for ($j=0;$j<60;$j++) {
                    $array_sets[$i][$j] = 0;
                }
            }
            $filtered = $timetables
                ->where('no', $working_timetable_no)
                ->where('working_time_kubun', '!=', Config::get('const.C004.out_of_regular_working_time'))
                ->sortBy('from_time');
            foreach($filtered as $result_time) {
                if (isset($result_time->from_time) && isset($result_time->to_time)) {
                    $dt = new Carbon('2019-08-01 '.$result_time->from_time);
                    $check_from_hour = date_format($dt, 'H');
                    $check_from_minute = date_format($dt, 'i');
                    $dt = new Carbon('2019-08-01 '.$result_time->to_time);
                    $check_to_hour = date_format($dt, 'H');
                    $check_to_minute = date_format($dt, 'i');
                    if ($result_time->from_time < $result_time->to_time) {
                        for ($i=(int)$check_from_hour;$i<=$check_to_hour;$i++) {
                            if ($i == (int)$check_from_hour) {      // １回目
                                $minute_from = (int)$check_from_minute;
                                if ($check_from_hour == $check_to_hour) {
                                    $minute_to = (int)$check_to_minute;
                                } else {
                                    $minute_to = 60;
                                }
                            } elseif ($i == $check_to_hour) {   // 最後
                                $minute_from = 0;
                                $minute_to = (int)$check_to_minute;
                            } else {
                                $minute_from = 0;
                                $minute_to = 60;
                            }
                            for ($j=$minute_from;$j<$minute_to;$j++) {
                                $array_sets[$i][$j] = 1;
                            }
                        }
                    } else {
                        // fromから2400まで
                        for ($i=(int)$check_from_hour;$i<24;$i++) {
                            if ($i == (int)$check_from_hour) {      // １回目
                                $minute_from = (int)$check_from_minute;
                                $minute_to = 60;
                            } else {
                                $minute_from = 0;
                                $minute_to = 60;
                            }
                            for ($j=$minute_from;$j<$minute_to;$j++) {
                                $array_sets[$i][$j] = 1;
                            }
                        }
                        // 0からtoまで
                        for ($i=0;$i<=(int)$check_to_hour;$i++) {
                            if ($i == 0) {      // １回目
                                $minute_from = 0;
                                if (0 == $check_to_hour) {
                                    $minute_to = (int)$check_to_minute;
                                } else {
                                    $minute_to = 60;
                                }
                            } elseif ($i == $check_to_hour) {   // 最後
                                $minute_from = 0;
                                $minute_to = (int)$check_to_minute;
                            } else {
                                $minute_from = 0;
                                $minute_to = 60;
                            }
                            for ($j=$minute_from;$j<$minute_to;$j++) {
                                $array_sets[$i][$j] = 1;
                            }
                        }
                    }
                    // 配列=0の範囲を設定する
                    $temp_times = array();
                    $temp_from_time = "";
                    $temp_to_time = "";
                    $save_i = 0;
                    $save_j = 0;
                    for ($i=0;$i<24;$i++) {
                        for ($j=0;$j<60;$j++) {
                            if ($array_sets[$i][$j] == 0) {
                                if ($temp_from_time == "") {
                                    $temp_from_time = str_pad($i,2,0,STR_PAD_LEFT).':'.str_pad($j,2,0,STR_PAD_LEFT).':00';
                                }
                            } else {
                                if ($temp_from_time == "") {
                                    $temp_to_time ="";
                                } else {
                                    $temp_to_time = str_pad($i,2,0,STR_PAD_LEFT).':'.str_pad($j,2,0,STR_PAD_LEFT).':00';
                                    $temp_times[] = array('from_time' => $temp_from_time , 'to_time' => $temp_to_time);
                                    $temp_from_time ="";
                                    $temp_to_time ="";
                                }
                            }
                            $save_j = $j;
                        }
                        $save_i = $i;
                    }
                    if ($temp_from_time != "") {
                        $temp_from_time = str_pad($save_i,2,0,STR_PAD_LEFT).':'.str_pad($save_j,2,0,STR_PAD_LEFT).':00';
                        $temp_times[] = array('from_time' => $temp_from_time , 'to_time' => $temp_to_time);
                    }
                }
            }
            $array_times = $temp_times;
        }
        return $array_times;
    }
    
    
    /**
     * タイムテーブル労働開始終了時間テーブル設定
     * 
     * @return 時間差
     */
    public function setWorkingStartEndTimeTable($target_date){
        try {
            // タイムテーブル取得（所定時間と休憩時間）
            $timetable_model = new WorkingTimeTable();
            $dt = new Carbon($target_date);
            $timetable_model->setParamdatefromAttribute(date_format($dt, 'Ymd'));
            // タイムテーブル取得(丸め）
            $results = $timetable_model->getWorkingTimeTableRound();
            $current_no = null;
            $current_item = null;
            $array_no = array();
            $array_timetable = array();
            $array_edt_timetable = array();
            $breaktime_cnt = 0;
            // タイムテーブル労働開始終了時間テーブル設定
            foreach($results as $item) {
                if($current_no == null) {$current_no = $item->no;}
                if($current_item == null) {$current_item = $item;}
                if($current_no == $item->no) {
                    if ($item->working_time_kubun == Config::get('const.C004.regular_working_breaks_time')) {
                        $breaktime_cnt++;
                    }
                    $array_timetable[] = array(
                        'working_time_kubun' => $item->working_time_kubun,
                        'from_time' => $item->from_time,
                        'to_time' => $item->to_time
                    );
                } else {
                    // テーブル編集設定
                    $array_timetable = $this->edtTimeTable($array_timetable, $breaktime_cnt);
                    $array_no[] = array(
                        'no' => $current_item->no,
                        'name' => $current_item->name,
                        'timetable' => $array_timetable
                    );
                    $current_no = $item->no;
                    $current_item = $item;
                    $array_timetable = array();
                    $array_timetable[] = array(
                        'working_time_kubun' => $item->working_time_kubun,
                        'from_time' => $item->from_time,
                        'to_time' => $item->to_time
                    );
                }
            }
            // 残り
            if (count($array_timetable) > 0) {
                // テーブル編集設定
                $array_timetable = $this->edtTimeTable($array_timetable, $breaktime_cnt);
                $array_no[] = array(
                    'no' => $current_item->no,
                    'name' => $current_item->name,
                    'timetable' => $array_timetable
                );
            }
            return $array_no;
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.not_setting_timetable').'$pe');
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.not_setting_timetable').'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }
    
    /**
     * タイムテーブル編集設定
     *      
     * @return void
     */
    private function edtTimeTable($array_timetable, $breaktime_cnt)
    {
        $array_edt_timetable = array();
        $array_from_time = array();
        $array_to_time = array();
        $edt_item_cnt = 0;
        $jdg_time_kubun = 0;
        $first_from_time = null;
        if ($breaktime_cnt > 0) {
            // 休憩時間があれば休憩時間でのテーブル編集設定
            $jdg_time_kubun = Config::get('const.C004.regular_working_breaks_time');
        } else {
            // 休憩時間がなければ所定時間でのテーブル編集設定
            $jdg_time_kubun = Config::get('const.C004.regular_working_time');
        }
        foreach($array_timetable as $edt_item) {
            if ($edt_item['working_time_kubun'] == $jdg_time_kubun) {
                if ($edt_item['from_time'] != "" && $edt_item['from_time'] != null) {
                    if ($jdg_time_kubun == Config::get('const.C004.regular_working_breaks_time')) {
                        $array_from_time[] = $edt_item['to_time'];
                        $edt_item_cnt++;
                        if ($first_from_time == null) {
                            $first_from_time = $edt_item['from_time'];
                        } else {
                            $array_to_time[] = $edt_item['from_time'];
                        }
                    } else {
                        $array_from_time[] = $edt_item['from_time'];
                        $array_to_time[] = $edt_item['to_time'];
                    }
                }
            }
        }
        if ($jdg_time_kubun == Config::get('const.C004.regular_working_breaks_time')) {
            if ($first_from_time != null) {
                $array_to_time[] = $first_from_time;
            }
        }
        for ($i=0;$i<count($array_from_time);$i++) {
            $array_edt_timetable[] = array(
                'working_time_kubun' => $edt_item['working_time_kubun'],
                'from_time' => $array_from_time[$i],
                'to_time' => $array_to_time[$i]
            );
        }

        return $array_edt_timetable;
    }
    
    /**
     * reqestクエリーセット
     *      
     * @return void
     */
    public function setRequestQeury($val)
    {

        if(isset($val)){
            return $val;
        } else {
            return null;
        }
    }
    // -------------  8.設定 end ----------------------------------------------------------- //

    // -------------  9.登録更新  start ---------------------------------------------- //

    /**
     *  勤怠登録（勤怠編集）
     *
     * @return
     */
    public function addAttendanceWork($params){
        $user_code = $params['user_code'];
        $target_date = $params['target_date'];
        $details = $params['details'];
        $beforeids = $params['beforeids'];

        $systemdate = Carbon::now();
        $work_time_model = new WorkTime();
        $apicommon_model = new ApiCommonController();
        $this->array_messagedata = array();

        DB::beginTransaction();
        try{
            $user = Auth::user();
            $login_user_code = $user->code;
            $login_department_code = null;
            $dt = new Carbon($target_date);
            $target_date = $dt->format('Ymd');
            // ログインユーザー部署ApiCommonControllerで取得
            $login_department_code = null;
            $dep_results = $apicommon_model->getUserDepartment($login_user_code, $target_date);
            foreach($dep_results as $item) {
                $login_department_code = $item->department_code;
                break;
            }
            $department_code = null;
            $user_name = null;
            // 休暇登録
            //部署選択されていない場合は部署コードないためApiCommonControllerで取得
            if ($department_code == null) {
                $dep_results = $apicommon_model->getUserDepartment($user_code, $target_date);
                foreach($dep_results as $dep_result) {
                    $department_code = $dep_result->department_code;
                    $user_name = $dep_result->name;
                    break;
                }
            }
            $working_date = $target_date;
            // $user_holiday = new UserHolidayKubun();
            // $user_holiday->setParamUsercodeAttribute($user_code);
            // $user_holiday->setParamDepartmentcodeAttribute($department_code);
            // $user_holiday->setParamdatefromAttribute($working_date);
            // $user_holiday->setSystemDateAttribute($systemdate);
            // // 既に存在する場合は論理削除する
            // $is_exists = $user_holiday->isExistsKbn();
            // if($is_exists){
            //     $user_holiday->delKbn();
            // }
            $calendar_setting_model = new CalendarSettingInformation();
            $calendar_setting_model->setParamUsercodeAttribute($user_code);
            $calendar_setting_model->setParamDepartmentcodeAttribute($department_code);
            $calendar_setting_model->setParamfromdateAttribute($working_date);
            // 存在しない場合はエラーで返す
            $is_exists = $calendar_setting_model->isExists();
            if(!$is_exists){
                DB::rollBack();
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $user_name."さん", Config::get('const.MSG_ERROR.not_setting_calendar')));
                $this->array_messagedata[] = str_replace('{0}', $user_name."さん", Config::get('const.MSG_ERROR.not_setting_calendar'));
                return array(
                    'result' => false, 
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata
                );
            }
            $calendar_setting_model->setUpdatedatAttribute($systemdate);
            $calendar_setting_model->setUpdateduserAttribute($login_user_code);
            foreach($details as $item) {
                if($item['kbn_flag'] == 1){     // 休暇区分のみ登録
                    $calendar_setting_model->setHolidaykubunAttribute($item['user_holiday_kbn']);
                    // $user_holiday->setHolidaykubunAttribute($item['user_holiday_kbn']);
                    // $user_holiday->setCreateduserAttribute($login_user_code);
                    // $user_holiday->insertKbn();
                    // 勤怠時刻にIDを登録するのでSELECTする
                } else {
                    $calendar_setting_model->setHolidaykubunAttribute(0);
                }
                $calendar_setting_model->updateCalendar();
                // 先頭行のみの処理でよいのでbreakする
                break;
            }
            // 勤怠時刻登録
            // beforeidsが存在した場合は論理削除する
            for($i=0;$i<count($beforeids);$i++) {
                $work_time_model->setIdAttribute($beforeids[$i]);
                $work_time_model->setEditordepartmentcodeAttribute($login_department_code);
                $work_time_model->setEditorusercodeAttribute($login_user_code);
                $work_time_model->setUpdateduserAttribute($login_user_code);
                $work_time_model->setSystemDateAttribute($systemdate);
                $work_time_model->delWorkTime();
            }
            foreach($details as $item) {
                //部署選択されていない場合は部署コードないためApiCommonControllerで取得
                if ($department_code == null) {
                    if (isset($item['department_code'])) {
                        if ($item['department_code'] == "" || $item['department_code'] == null) {
                            $dep_results = $apicommon_model->getUserDepartment($item['user_code'], $target_date);
                            foreach($dep_results as $dep_result) {
                                $department_code = $dep_result->department_code;
                                break;
                            }
                        } else {
                            $department_code = $item['department_code'];
                        }
                    } else {
                        $dep_results = $apicommon_model->getUserDepartment($item['user_code'], $target_date);
                        foreach($dep_results as $dep_result) {
                            $department_code = $dep_result->department_code;
                            break;
                        }
                    }
                }
                $record_time = null;
                if ($item['time'] != "" && $item['time'] != null) {
                    $record_time = $item['date']." ".$item['time'];     // DB用
                } else {
                    $record_time = $item['date']." 00:00:01";
                }
                $work_time_model->setUsercodeAttribute($item['user_code']);
                $work_time_model->setDepartmentcodeAttribute($department_code);
                $work_time_model->setRecordtimeAttribute($record_time);
                $work_time_model->setModeAttribute($item['mode']);
                $work_time_model->setUserholidaykubunsidAttribute(null);
                $work_time_model->setCreateduserAttribute($login_user_code);
                $work_time_model->setSystemDateAttribute($systemdate);
                $positions_data = null; 
                if ((isset($item['x_positions']) && isset($item['y_positions']))) {
                    if (($item['x_positions'] != "") && ($item['y_positions'] != "")) {
                        $positions_data = $item['x_positions'].' '.$item['y_positions'];
                    }
                }
                $work_time_model->setPositionsAttribute($positions_data);
                $work_time_model->setIseditorAttribute(true);
                $work_time_model->setEditordepartmentcodeAttribute($login_department_code);
                $work_time_model->setEditorusercodeAttribute($login_user_code);
                $work_time_model->insertWorkTime();
            }
            DB::commit();
            return array(
                'result' => true, 
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata
            );

        }catch(\PDOException $pe){
            DB::rollBack();
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * お知らせ登録
     *
     * @return list
     */
    public function insertPostInformations(Request $request){
        $response = collect();
        $usercode = Auth::user()->code;
        $content = $request->content;
        $systemdate = Carbon::now();
        // 新規登録
        DB::beginTransaction();
        try{
            DB::table($this->table_post_informations)->insert(
                ['user_code' => $usercode, 'content' => $content ,'created_at' => $systemdate]
            );
            DB::commit();
            $response->put(Config::get('const.PUT_ITEM.result'),Config::get('const.RESULT_CODE.success'));
        }catch(\PDOException $pe){
            DB::rollBack();
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_post_informations, Config::get('const.LOG_MSG.insert_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_post_informations, Config::get('const.LOG_MSG.insert_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * お知らせ削除
     *
     * @return list
     */
    public function delPostInformations(Request $request){
        $response = collect();
        $id = $request->id;
        $systemdate = Carbon::now();
        // 新規登録
        DB::beginTransaction();
        try{
            DB::table($this->table_post_informations)->where('id', $id)->update(['is_deleted' => 1,'updated_at' => $systemdate]);
            DB::commit();
            $response->put(Config::get('const.PUT_ITEM.result'),Config::get('const.RESULT_CODE.success'));
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_post_informations, Config::get('const.LOG_MSG.data_delete_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;

        }catch(\Exception $e){
            DB::rollBack();
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_post_informations, Config::get('const.LOG_MSG.data_delete_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

     /**
     * 有給単位リスト取得
     *
     * @return list
     */
    public function getPaidTypeList(Request $request){
        $this->array_messagedata = array();
        $details = new Collection();
        $result = true;
        try {
            $details =
                DB::table($this->table_generalcodes)
                    ->where('identification_id', Config::get('const.C036.value'))
                    ->where('is_deleted', 0)
                    ->orderby('sort_seq','asc')
                    ->get();

            return response()->json(
                ['result' => $result, 'details' => $details,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }
    // -------------  9.登録更新  end ---------------------------------------------- //

}
