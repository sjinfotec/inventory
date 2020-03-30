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



/**
 * 共通API
 *
 *      1.適用期間開始サブクエリー作成
 *          ユーザー適用期間開始サブクエリー作成    : getUserApplyTermSubquery, makeUserApplyTermSql                        : users
 *          部署適用期間開始サブクエリー作成        : getDepartmentApplyTermSubquery, makeDepartmentApplyTermSql            : departments
*          タイムテーブル適用期間開始サブクエリー作成  : getTimetableApplyTermSubquery, makeWorkingTimeTableApplyTermSql    : working_timetables
 *      2.リスト作成
 *          ユーザーリスト取得          : getUserList               : users
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
 *          シフト情報取得                              : getShiftInformation               : ShiftInformation        
 *          ログインユーザー部署ロール取得（画面から）      : getLoginUserDepartment            
 *          ログインユーザー権限取得（画面から）            : getLoginUserRole                  
 *          ユーザー権限取得                            : getUserRole                       : users                   
 *          ユーザー部署取得                            : getUserDepartment                 : users 
 *          ユーザーの部署と雇用形態と権限取得          : getUserDepartmentEmploymentRole      : users
 *          ユーザーメールアドレス取得                  : getUserMailAddress                : users      
 *          ユーザー休暇区分取得                        : getUserHolidaykbn                 : UserHolidayKubun              
 *          ユーザー所定時刻取得                        : getWorkingHours                   : WorkingTimeTable             
 *      4.その他情報取得
 *          会社情報取得                                : getCompanyInfoApply       : Company
 *          指定月締日取得（画面から）                  : getClosingDay              : Setting   
 *          指定月締日取得                              : getCommonClosingDay       : Setting  
 *          曜日取得                                    : getWeekDay
 *          日付のフォーマット YYYY年MM月DD日（WEEK）    : getYMDWeek                 : Calendar       
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
 *      7.変換
 *          時刻日付変換                            : convTimeToDate
 *          時刻日付変換                            : convTimeToDateTarget
 *          時刻日付変換from                        : convTimeToDateFrom
 *          時刻日付変換to                          : convTimeToDateTo
 *          インターバル時間を取得して分に変換する    : getIntevalMinute
 *      8.設定
 *          タイムテーブルの分解            : analyzeTimeTable
 *          reqestクエリーセット            : setRequestQeury
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
                    't1.working_time_kubun as working_time_kubun'
                )
                ->JoinSub($subquery1, 't2', function ($join) { 
                    $join->on('t1.no', '=', 't2.no');
                    $join->on('t1.apply_term_from', '=', 't2.max_apply_term_from');
                })
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
        $makeSql .= "   and t1.no not in ? ";
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
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
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
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
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
            throw $pe;
        }catch(\Exception $e){
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
            throw $pe;
        }catch(\Exception $e){
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
            throw $pe;
        }catch(\Exception $e){
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
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
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
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
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
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
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
            $details =
                DB::table($this->table_generalcodes)
                    ->where('identification_id', 'C005')
                    ->where('is_deleted', 0)
                    ->orderby('sort_seq','asc')
                    ->get();

            return response()->json(
                ['result' => $result, 'details' => $details,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
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
            throw $pe;
        }catch(\Exception $e){
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
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
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
            return $codeList;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            return $codeList;
        }
        return $codeList;
    }
    // -------------  2.リスト作成  end -------------------------------------------------------------- //

    // -------------  3.ユーザ情報取得  start -------------------------------------------------------- //
    /**
     * ユーザーのシフト情報取得
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
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
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
            throw $pe;
        }catch(\Exception $e){
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
            throw $pe;
        }catch(\Exception $e){
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
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
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
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
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
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
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
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_users, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
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
            $user_holiday_model = new UserHolidayKubun();
            $user_holiday_model->setParamUsercodeAttribute($user_id);
            $user_holiday_model->setParamdatefromAttribute($target_date);
            $results = $user_holiday_model->getDetail();
            foreach($results as $item) {
                if (isset($item->holiday_kubun)) {
                    $holiday_kbn = $item->holiday_kubun;
                    break;
                }
            }
            return $holiday_kbn;
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * ユーザー所定時刻取得
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
            $target_dateYmd = new Carbon($target_date);
            $time_tables->setParamdatefromAttribute(date_format($target_dateYmd, 'Ymd'));
            $time_tables->setParamdatetoAttribute(date_format($target_dateYmd, 'Ymd'));
            $time_tables->setParamDepartmentcodeAttribute($department_code);
            $time_tables->setParamUsercodeAttribute($user_code);
            $workingHours = $time_tables->getWorkingTimeTable();
            return response()->json(
                ['result' => true, 'details' => $workingHours,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
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
            throw $pe;
        }catch(\Exception $e){
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
            throw $pe;
        }catch(\Exception $e){
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
            throw $pe;
        }catch(\Exception $e){
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
     * 日付のフォーマット YYYY年MM月DD日（WEEK）
     *
     * @param [type] $dt
     * @param [type] $format
     * @return array
     */
    public function getYMDWeek($dt){
        // フォーマット 2019年10月01日(火)
        $date_name = '';
        $calender_model = new Calendar();
        $calender_model->setParamfromdateAttribute(date_format(new Carbon($dt), 'Ymd'));

        $calender_model->setDateAttribute(date_format(new Carbon($dt), 'Ymd'));
        $calendars = $calender_model->getCalenderDate();
        if (count($calendars) > 0) {
            foreach ($calendars as $result) {
                if (isset($result->date_name)) {
                    $date_name = $result->date_name;
                }
                break;
            }
        }
        return $date_name;
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
        $filtered = $timetables->where('no', $working_timetable_no)
            ->where('working_time_kubun', Config::get('const.C004.regular_working_breaks_time'));
        // 休憩時間帯は複数あるかも
        $calc_times = 0;
        foreach($filtered as $result_breaks_time) {
            $from_time = $result_breaks_time->from_time;        // 休憩開始時刻
            $to_time = $result_breaks_time->to_time;            // 休憩終了時刻
            if (isset($from_time) && isset($to_time)) {
                // from_time日付付与
                $time_calc_from = 
                    $this->convTimeToDateFrom($from_time, $current_date, $target_from_datetime, $target_to_datetime);         
                // to_time日付付与
                $time_calc_to = 
                    $this->convTimeToDateTo($from_time, $to_time, $current_date, $target_from_datetime, $target_to_datetime);         
                $chk_time = true;
                if (isset($setting_from_datetime) && isset($setting_to_datetime)) {
                    // タイムテーブル設定時刻のチェックを行う場合
                    // タイムテーブル時間範囲内に休憩開始終了時刻がある場合に計算する
                    if (($time_calc_from <= $setting_from_datetime || $time_calc_from >= $setting_to_datetime) &&
                        ($time_calc_to <= $setting_from_datetime || $time_calc_to >= $setting_to_datetime)) {
                        $chk_time = false;
                    }
                }
                if ($chk_time) {
                    //  指定時間範囲内に休憩開始終了時刻がある場合に計算する
                    if (($time_calc_from > $target_from_datetime && $time_calc_from < $target_to_datetime) ||
                        ($time_calc_to > $target_from_datetime && $time_calc_to < $target_to_datetime)) {
                        if ($target_from_datetime > $time_calc_from) {
                            $time_calc_from = $target_from_datetime;
                        }
                        if ($target_to_datetime < $time_calc_to) {
                            $time_calc_to = $target_to_datetime;
                        }
                        if ($time_calc_from < $time_calc_to) {
                            $calc_times += $this->diffTimeSerial($time_calc_from, $time_calc_to);
                        }
                    }
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
    public function roundTimeByTimeStart($round_date, $round_time, $time_unit, $time_rounding){

        $result_round_time = $round_time;
        $dt = new Carbon($result_round_time);
        $target_h = $dt->format("H");
        $target_i = $dt->format("i");
        $target_s = $dt->format("s");
        $w_time_h = (int)$target_h;
        $w_time_i = (int)$target_i;
        $result_w_time_h = $target_h;
        $result_w_time_i = $target_i;
        if ($time_unit == Config::get('const.C009.round1')) {
            $result_round_time = $round_time;
        } elseif ($time_unit == Config::get('const.C009.round5')) {
            if ($w_time_i >= 56) {
                $result_w_time_i = "00";
                $result_w_time_h = str_pad($w_time_h + 1, 2, 0, STR_PAD_LEFT);
            } elseif ($w_time_i >= 51) {
                $result_w_time_i = "55";
            } elseif ($w_time_i >= 46) {
                $result_w_time_i = "50";
            } elseif ($w_time_i >= 41) {
                $result_w_time_i = "45";
            } elseif ($w_time_i >= 36) {
                $result_w_time_i = "40";
            } elseif ($w_time_i >= 31) {
                $result_w_time_i = "35";
            } elseif ($w_time_i >= 26) {
                $result_w_time_i = "30";
            } elseif ($w_time_i >= 21) {
                $result_w_time_i = "25";
            } elseif ($w_time_i >= 16) {
                $result_w_time_i = "20";
            } elseif ($w_time_i >= 11) {
                $result_w_time_i = "15";
            } elseif ($w_time_i >= 6) {
                $result_w_time_i = "10";
            } elseif ($w_time_i >= 1) {
                $result_w_time_i = "05";
            } else {
                $result_w_time_i = "00";
            }
            $dt = new Carbon(substr($round_time,0,11).$result_w_time_h.":".$result_w_time_i.":00");
            $result_round_time = $dt->format("Y-m-d H:i:s");
        } elseif ($time_unit == Config::get('const.C009.round10')) {
            if ($w_time_i >= 51) {
                $result_w_time_i = "00";
                $result_w_time_h = str_pad($w_time_h + 1, 2, 0, STR_PAD_LEFT);
            } elseif ($w_time_i >= 41) {
                $result_w_time_i = "50";
            } elseif ($w_time_i >= 31) {
                $result_w_time_i = "40";
            } elseif ($w_time_i >= 21) {
                $result_w_time_i = "30";
            } elseif ($w_time_i >= 11) {
                $result_w_time_i = "20";
            } elseif ($w_time_i >= 1) {
                $result_w_time_i = "10";
            } else {
                $result_w_time_i = "00";
            }
            $dt = new Carbon(substr($round_time,0,11).$result_w_time_h.":".$result_w_time_i.":00");
            $result_round_time = $dt->format("Y-m-d H:i:s");
        } elseif ($time_unit == Config::get('const.C009.round15')) {
            if ($w_time_i >= 46) {
                $result_w_time_i = "00";
                $result_w_time_h = str_pad($w_time_h + 1, 2, 0, STR_PAD_LEFT);
            } elseif ($w_time_i >= 31) {
                $result_w_time_i = "45";
            } elseif ($w_time_i >= 16) {
                $result_w_time_i = "30";
            } elseif ($w_time_i >= 1) {
                $result_w_time_i = "15";
            } else {
                $result_w_time_i = "00";
            }
            $dt = new Carbon(substr($round_time,0,11).$result_w_time_h.":".$result_w_time_i.":00");
            $result_round_time = $dt->format("Y-m-d H:i:s");
        } elseif ($time_unit == Config::get('const.C009.round30')) {
            if ($w_time_i >= 31) {
                $result_w_time_i = "00";
                $result_w_time_h = str_pad($w_time_h + 1, 2, 0, STR_PAD_LEFT);
            } elseif ($w_time_i >= 1) {
                $result_w_time_i = "30";
            } else {
                $result_w_time_i = "00";
            }
            $dt = new Carbon(substr($round_time,0,11).$result_w_time_h.":".$result_w_time_i.":00");
            $result_round_time = $dt->format("Y-m-d H:i:s");
        } elseif ($time_unit == Config::get('const.C009.round60')) {
            $result_round_time = $round_time;
        }

        return $result_round_time;
    }

    /**
     * 時間丸め処理（時間丸めする：退勤用）
     *
     * @return 分で返却
     */
    public function roundTimeByTimeEnd($round_date, $round_time, $time_unit, $time_rounding){

        $result_round_time = $round_time;
        Log::DEBUG('roundTimeByTime $result_round_time = '.$result_round_time);
        $dt = new Carbon($result_round_time);
        $target_h = $dt->format("H");
        $target_i = $dt->format("i");
        $target_s = $dt->format("s");
        $w_time_h = (int)$target_h;
        $w_time_i = (int)$target_i;
        $result_w_time_h = $target_h;
        $result_w_time_i = $target_i;
        Log::DEBUG('roundTimeByTime $target_h = '.$target_h);
        Log::DEBUG('roundTimeByTime $target_i = '.$target_i);
        Log::DEBUG('roundTimeByTime $target_s = '.$target_s);
        if ($time_rounding == Config::get('const.C010.round_half_up')) {
            // 四捨五入
            if ($time_unit == Config::get('const.C009.round1')) {
                $result_round_time = $round_time;
            } elseif ($time_unit == Config::get('const.C009.round5')) {
                if ($w_time_i <= 2) {
                    $result_w_time_i = "00";
                } elseif ($w_time_i <= 7) {
                    $result_w_time_i = "05";
                } elseif ($w_time_i <= 12) {
                    $result_w_time_i = "10";
                } elseif ($w_time_i <= 17) {
                    $result_w_time_i = "15";
                } elseif ($w_time_i <= 22) {
                    $result_w_time_i = "20";
                } elseif ($w_time_i <= 27) {
                    $result_w_time_i = "25";
                } elseif ($w_time_i <= 32) {
                    $result_w_time_i = "30";
                } elseif ($w_time_i <= 37) {
                    $result_w_time_i = "35";
                } elseif ($w_time_i <= 42) {
                    $result_w_time_i = "40";
                } elseif ($w_time_i <= 47) {
                    $result_w_time_i = "45";
                } elseif ($w_time_i <= 52) {
                    $result_w_time_i = "50";
                } elseif ($w_time_i <= 57) {
                    $result_w_time_i = "55";
                } else {
                    $result_w_time_h = str_pad($w_time_h + 1, 2, 0, STR_PAD_LEFT);
                    $result_w_time_i = "00";
                }
                $dt = new Carbon(substr($round_time,0,11).$result_w_time_h.":".$result_w_time_i.":00");
                $result_round_time = $dt->format("Y-m-d H:i:s");
            } elseif ($time_unit == Config::get('const.C009.round10')) {
                if ($w_time_i <= 4) {
                    $result_w_time_i = "00";
                } elseif ($w_time_i <= 14) {
                    $result_w_time_i = "10";
                } elseif ($w_time_i <= 24) {
                    $result_w_time_i = "20";
                } elseif ($w_time_i <= 34) {
                    $result_w_time_i = "30";
                } elseif ($w_time_i <= 44) {
                    $result_w_time_i = "40";
                } elseif ($w_time_i <= 54) {
                    $result_w_time_i = "50";
                } else {
                    $result_w_time_h = str_pad($w_time_h + 1, 2, 0, STR_PAD_LEFT);
                    $result_w_time_i = "00";
                }
                $dt = new Carbon(substr($round_time,0,11).$result_w_time_h.":".$result_w_time_i.":00");
                $result_round_time = $dt->format("Y-m-d H:i:s");
            } elseif ($time_unit == Config::get('const.C009.round15')) {
                if ($w_time_i <= 6) {
                    $result_w_time_i = "00";
                } elseif ($w_time_i <= 21) {
                    $result_w_time_i = "15";
                } elseif ($w_time_i <= 36) {
                    $result_w_time_i = "30";
                } elseif ($w_time_i <= 51) {
                    $result_w_time_i = "45";
                } else {
                    $result_w_time_h = str_pad($w_time_h + 1, 2, 0, STR_PAD_LEFT);
                    $result_w_time_i = "00";
                }
                $dt = new Carbon(substr($round_time,0,11).$result_w_time_h.":".$result_w_time_i.":00");
                $result_round_time = $dt->format("Y-m-d H:i:s");
            } elseif ($time_unit == Config::get('const.C009.round30')) {
                if ($w_time_i <= 12) {
                    $result_w_time_i = "00";
                } elseif ($w_time_i <= 42) {
                    $result_w_time_i = "30";
                } else {
                    $result_w_time_h = str_pad($w_time_h + 1, 2, 0, STR_PAD_LEFT);
                    $result_w_time_i = "00";
                }
                $dt = new Carbon(substr($round_time,0,11).$result_w_time_h.":".$result_w_time_i.":00");
                $result_round_time = $dt->format("Y-m-d H:i:s");
            } elseif ($time_unit == Config::get('const.C009.round60')) {
                $result_round_time = $round_time;
            }
        } elseif ($time_rounding == Config::get('const.C010.round_down')) {
            // 切り捨て
            if ($time_unit == Config::get('const.C009.round1')) {
                $result_round_time = $round_time;
            } elseif ($time_unit == Config::get('const.C009.round5')) {
                if ($w_time_i <= 4) {
                    $result_w_time_i = "00";
                } elseif ($w_time_i <= 9) {
                    $result_w_time_i = "05";
                } elseif ($w_time_i <= 14) {
                    $result_w_time_i = "10";
                } elseif ($w_time_i <= 19) {
                    $result_w_time_i = "15";
                } elseif ($w_time_i <= 24) {
                    $result_w_time_i = "20";
                } elseif ($w_time_i <= 29) {
                    $result_w_time_i = "25";
                } elseif ($w_time_i <= 34) {
                    $result_w_time_i = "30";
                } elseif ($w_time_i <= 39) {
                    $result_w_time_i = "35";
                } elseif ($w_time_i <= 44) {
                    $result_w_time_i = "40";
                } elseif ($w_time_i <= 49) {
                    $result_w_time_i = "45";
                } elseif ($w_time_i <= 54) {
                    $result_w_time_i = "50";
                } else {
                    $result_w_time_i = "55";
                }
                $dt = new Carbon(substr($round_time,0,11).$result_w_time_h.":".$result_w_time_i.":00");
                $result_round_time = $dt->format("Y-m-d H:i:s");
            } elseif ($time_unit == Config::get('const.C009.round10')) {
                if ($w_time_i <= 9) {
                    $result_w_time_i = "00";
                } elseif ($w_time_i <= 19) {
                    $result_w_time_i = "10";
                } elseif ($w_time_i <= 29) {
                    $result_w_time_i = "20";
                } elseif ($w_time_i <= 39) {
                    $result_w_time_i = "30";
                } elseif ($w_time_i <= 49) {
                    $result_w_time_i = "40";
                } else {
                    $result_w_time_i = "50";
                }
                $dt = new Carbon(substr($round_time,0,11).$result_w_time_h.":".$result_w_time_i.":00");
                $result_round_time = $dt->format("Y-m-d H:i:s");
            } elseif ($time_unit == Config::get('const.C009.round15')) {
                if ($w_time_i <= 14) {
                    $result_w_time_i = "00";
                } elseif ($w_time_i <= 29) {
                    $result_w_time_i = "15";
                } elseif ($w_time_i <= 44) {
                    $result_w_time_i = "30";
                } else {
                    $result_w_time_i = "45";
                }
                Log::DEBUG('roundTimeByTime $substr($round_time,11) = '.substr($round_time,0,11).$result_w_time_h.":".$result_w_time_i.":00");
                $dt = new Carbon(substr($round_time,0,11).$result_w_time_h.":".$result_w_time_i.":00");
                Log::DEBUG('roundTimeByTime $dt = '.$dt);
                $result_round_time = $dt->format("Y-m-d H:i:s");
            } elseif ($time_unit == Config::get('const.C009.round30')) {
                if ($w_time_i <= 29) {
                    $result_w_time_i = "00";
                } else {
                    $result_w_time_i = "30";
                }
                $dt = new Carbon(substr($round_time,0,11).$result_w_time_h.":".$result_w_time_i.":00");
                $result_round_time = $dt->format("Y-m-d H:i:s");
            } elseif ($time_unit == Config::get('const.C009.round60')) {
                $result_round_time = $round_time;
            }
        } elseif ($time_rounding == Config::get('const.C010.round_up')) {
            // 切り上げ
            if ($time_unit == Config::get('const.C009.round1')) {
                $result_round_time = $round_time;
            } elseif ($time_unit == Config::get('const.C009.round5')) {
                if ($w_time_i <= 5) {
                    $result_w_time_i = "05";
                } elseif ($w_time_i <= 10) {
                    $result_w_time_i = "10";
                } elseif ($w_time_i <= 15) {
                    $result_w_time_i = "15";
                } elseif ($w_time_i <= 20) {
                    $result_w_time_i = "20";
                } elseif ($w_time_i <= 25) {
                    $result_w_time_i = "25";
                } elseif ($w_time_i <= 30) {
                    $result_w_time_i = "30";
                } elseif ($w_time_i <= 35) {
                    $result_w_time_i = "35";
                } elseif ($w_time_i <= 40) {
                    $result_w_time_i = "40";
                } elseif ($w_time_i <= 45) {
                    $result_w_time_i = "45";
                } elseif ($w_time_i <= 50) {
                    $result_w_time_i = "50";
                } elseif ($w_time_i <= 55) {
                    $result_w_time_i = "55";
                } else {
                    $result_w_time_h = str_pad($w_time_h + 1, 2, 0, STR_PAD_LEFT);
                    $result_w_time_i = "00";
                }
                $dt = new Carbon(substr($round_time,0,11).$result_w_time_h.":".$result_w_time_i.":00");
                $result_round_time = $dt->format("Y-m-d H:i:s");
            } elseif ($time_unit == Config::get('const.C009.round10')) {
                if ($w_time_i <= 10) {
                    $result_w_time_i = "10";
                } elseif ($w_time_i <= 20) {
                    $result_w_time_i = "20";
                } elseif ($w_time_i <= 30) {
                    $result_w_time_i = "30";
                } elseif ($w_time_i <= 40) {
                    $result_w_time_i = "40";
                } elseif ($w_time_i <= 50) {
                    $result_w_time_i = "50";
                } else {
                    $result_w_time_h = str_pad($w_time_h + 1, 2, 0, STR_PAD_LEFT);
                    $result_w_time_i = "00";
                }
                $dt = new Carbon(substr($round_time,0,11).$result_w_time_h.":".$result_w_time_i.":00");
                $result_round_time = $dt->format("Y-m-d H:i:s");
            } elseif ($time_unit == Config::get('const.C009.round15')) {
                if ($w_time_i <= 15) {
                    $result_w_time_i = "15";
                } elseif ($w_time_i <= 30) {
                    $result_w_time_i = "30";
                } elseif ($w_time_i <= 45) {
                    $result_w_time_i = "45";
                } else {
                    $result_w_time_h = str_pad($w_time_h + 1, 2, 0, STR_PAD_LEFT);
                    $result_w_time_i = "00";
                }
                $dt = new Carbon(substr($round_time,0,11).$result_w_time_h.":".$result_w_time_i.":00");
                $result_round_time = $dt->format("Y-m-d H:i:s");
            } elseif ($time_unit == Config::get('const.C009.round30')) {
                if ($w_time_i <= 30) {
                    $result_w_time_i = "30";
                } else {
                    $result_w_time_h = str_pad($w_time_h + 1, 2, 0, STR_PAD_LEFT);
                    $result_w_time_i = "00";
                }
                $dt = new Carbon(substr($round_time,0,11).$result_w_time_h.":".$result_w_time_i.":00");
                $result_round_time = $dt->format("Y-m-d H:i:s");
            } elseif ($time_unit == Config::get('const.C009.round60')) {
                $result_round_time = $round_time;
            }
        } elseif ($time_unit == Config::get('const.C010.non')) {
            $result_round_time = $round_time;
        } else {
            $result_round_time = $round_time;
        }
        Log::DEBUG('roundTimeByTime $result_round_time = '.$result_round_time);

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
        $business_kubun = null;
        $calender_model = new Calendar();
        $calender_model->setParamdepartmentcodeAttribute($departmentcode);
        $calender_model->setParamemploymentstatusAttribute($employmentstatus);
        $calender_model->setParamusercodeAttribute($usercode);
        $calender_model->setParamfromdateAttribute($datefrom);
        $calendars = $calender_model->getCalenderDate();
        if (count($calendars) > 0) {
            foreach ($calendars as $result) {
                if (isset($result->business_kubun)) {
                    $business_kubun = $result->business_kubun;
                }
                break;
            }
        }

        return $business_kubun;
    }

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
        } else {
            return Config::get('const.C018.forget_stamp');
        }
        return Config::get('const.C018.forget_stamp');
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

        Log::DEBUG('         ------------- convTimeToDateFrom in ');
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
        Log::DEBUG('         ------------- convTimeToDateFrom end '.$cnv_from_date);

        return $cnv_from_date;
    }

    /**
     * 時刻日付変換to
     * @return 日付時刻
     */
    public function convTimeToDateTo($from_time, $to_time, $current_date, $target_from_time, $target_to_time){

        Log::DEBUG('         ------------- convTimeToDateTo in ');

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
        Log::DEBUG('         ------------- convTimeToDateTo end '.$cnv_to_date);

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
    // -------------  7.変換 end -------------------------------------------------------------- //

    // -------------  8.設定 start ------------------------------------------------------------ //

    /**
     * タイムテーブルの分解
     *
     * @return 
     */
    public function analyzeTimeTable($timetables, $working_time_kubun, $working_timetable_no){
        Log::DEBUG('        タイムテーブルの分解 analyzeTimeTable in $working_time_kubun = '.$working_time_kubun);
        Log::DEBUG('        タイムテーブルの分解 analyzeTimeTable in $working_timetable_no = '.$working_timetable_no);
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
        foreach($array_times as $item) {
            Log::DEBUG('             タイムテーブルの分解 result --- analyzeTimeTable in $array_time from_time = '.$item['from_time']);
            Log::DEBUG('             タイムテーブルの分解 result --- analyzeTimeTable in $array_time to_time = '.$item['to_time']);
        }
        return $array_times;
    }
    
    
    /**
     * タイムテーブル労働開始終了時間テーブル設定
     * 
     * @return 時間差
     */
    public function setWorkingStartEndTimeTable($target_date){
        // タイムテーブル取得（所定時間と休憩時間）
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

    /**
     * お知らせ取得
     *
     * @return list
     */
    public function getPostInformations(Request $request){
        // $details = new Collection();
        try {
            $details =
                DB::table('post_informations')
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
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', 'post_informations', Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', 'post_informations', Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
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
            DB::table('post_informations')->insert(
                ['user_code' => $usercode, 'content' => $content ,'created_at' => $systemdate]
            );
            DB::commit();
            $response->put(Config::get('const.PUT_ITEM.result'),Config::get('const.RESULT_CODE.success'));
        }catch(\PDOException $pe){
            DB::rollBack();
            $response->put(Config::get('const.PUT_ITEM.result'),Config::get('const.RESULT_CODE.insert_error'));

        }catch(\Exception $e){
            DB::rollBack();
            $response->put(Config::get('const.PUT_ITEM.result'),Config::get('const.RESULT_CODE.insert_error'));
            Log::error(str_replace('{0}', 'post_informations', Config::get('const.LOG_MSG.data_insert_erorr')));
            Log::error($e->getMessage());
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
            DB::table('post_informations')->where('id', $id)->update(['is_deleted' => 1,'updated_at' => $systemdate]);
            DB::commit();
            $response->put(Config::get('const.PUT_ITEM.result'),Config::get('const.RESULT_CODE.success'));
        }catch(\PDOException $pe){
            DB::rollBack();
            $response->put(Config::get('const.PUT_ITEM.result'),Config::get('const.RESULT_CODE.insert_error'));

        }catch(\Exception $e){
            DB::rollBack();
            $response->put(Config::get('const.PUT_ITEM.result'),Config::get('const.RESULT_CODE.insert_error'));
            Log::error(str_replace('{0}', 'post_informations', Config::get('const.LOG_MSG.data_insert_erorr')));
            Log::error($e->getMessage());
        }
    }

}
