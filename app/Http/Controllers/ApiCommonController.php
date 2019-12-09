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



class ApiCommonController extends Controller
{

    protected $table_generalcodes = 'generalcodes';
    protected $table_companies = 'companies';
    protected $table_confirms = 'confirms';
    protected $table_working_timetables = 'working_timetables';

    /**
     * ユーザーリスト取得
     *
     * @param  Request  getdo 0:取得しない、1:取得する
     * @return list users
     */
    public function getUserList(Request $request){

        $users = new Collection();
        try {
            // パラメータチェック
            if (isset($request->getdo)) {
                $getdo = $request->getdo;
                if ($getdo != 1) { return null; }
            } else {
                $getdo = 1;
            }
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

            if (isset($request->code)) {
                if (isset($request->employment)) {
                    $mainQuery = DB::table('users')
                        ->JoinSub($subquery1, 't1', function ($join) { 
                            $join->on('t1.code', '=', 'users.code');
                            $join->on('t1.max_apply_term_from', '=', 'users.apply_term_from');
                        })
                        ->where('users.department_code', $request->code)
                        ->where('users.employment_status', $request->employment);
                    if($role == Config::get('const.C025.general_user')){
                        $mainQuery->where('users.code','=',$chk_user_id);
                    } else {
                        $mainQuery->where('users.management','<',Config::get('const.C017.admin_user'));
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
                    if($role == Config::get('const.C025.general_user')){
                        $mainQuery->where('users.code','=',$chk_user_id);
                    } else {
                        $mainQuery->where('users.management','<',Config::get('const.C017.admin_user'));
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
                    if($role == Config::get('const.C025.general_user')){
                        $mainQuery->where('users.code','=',$chk_user_id);
                    } else {
                        $mainQuery->where('users.management','<',Config::get('const.C017.admin_user'));
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
                    if($role == Config::get('const.C025.general_user')){
                        $mainQuery->where('users.code','=',$chk_user_id);
                    } else {
                        $mainQuery->where('users.management','<',Config::get('const.C017.admin_user'));
                    }
                    $users = $mainQuery->where('users.is_deleted', 0)->get();
                }
            }
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', 'users', Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', 'users', Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
        }

        return $users;
    }

    /**
     * ユーザーのシフト情報取得
     *
     * @return void
     */
    public function getShiftInformation(Request $request){
        $shift_results = new Collection();
        try {
            $code = $request->code;
            $no = $request->no;
            $from = new Carbon($request->from);
            $from = $from->format("Y/m/d");
            $to = new Carbon($request->to);
            $to = $to->format("Y/m/d");
        
            $shift_info = new ShiftInformation();

            $shift_info->setUsercodeAttribute($code);
            $shift_info->setWorkingtimetablenoAttribute($no);
            $shift_info->setStarttargetdateAttribute($from);
            $shift_info->setEndtargetdateAttribute($to);
            $shift_results = $shift_info->getUserShift();
        }catch(\PDOException $pe){
            return $shift_results;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            return $shift_results;
        }

        return $shift_results;
    }
        
    /** 部署リスト取得
     *
     * @return list departments
     */
    public function getDepartmentList(Request $request){
        $departments = new Collection();
        try {
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

            if($role == Config::get('const.C025.general_user')){
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
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', 'departments', Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', 'departments', Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
        }
        return $departments;
    }
        
    /** ユーザー部署ロール取得（画面から）
     *
     * @return list departments
     */
    public function getLoginUserDepartment(){
        $mainquery = new Collection();
        try {
            // ログインユーザの権限取得
            $chk_user_id = Auth::user()->code;
            $mainquery = $this->getUserDepartment($chk_user_id, null);
        }catch(\PDOException $pe){
            return $mainquery;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            return $mainquery;
        }
        return $mainquery;
    }
        
    /** ユーザー権限取得（画面から）
     *
     * @return list departments
     */
    public function getLoginUserRole(){
        $role = null;
        try {
            // ログインユーザの権限取得
            $chk_user_id = Auth::user()->code;
            // 適用期間日付の取得（現在日付とする）
            $dt = new Carbon();
            $target_date = $dt->format('Ymd');
            $role = $this->getUserRole($chk_user_id, $target_date);
        }catch(\PDOException $pe){
            return $role;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            return $role;
        }
        return $role;
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
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', 'users', Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', 'users', Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

        return $userrole;
    }
        
    /** ユーザー勤怠管理取得
     *
     * @return list departments
     */
    public function getUserManagement($user_id, $target_date){
        $usermanagement = null;
        try {
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
            $usermanagement = DB::table('users')
                ->JoinSub($subquery1, 't1', function ($join) { 
                    $join->on('t1.code', '=', 'users.code');
                    $join->on('t1.department_code', '=', 'users.department_code');
                    $join->on('t1.max_apply_term_from', '=', 'users.apply_term_from');
                })
                ->where('users.code', $user_id)
                ->where('users.is_deleted', 0)
                ->value('management');
            if(!isset($usermanagement)) { return null; }
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', 'users', Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            return $usermanagement;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', 'users', Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
            return $usermanagement;
        }

        return $usermanagement;
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
            $mainquery = DB::table('users')
                ->select(
                    'users.code as code',
                    'users.name as name',
                    'users.department_code as department_code',
                    't2.name as department_name',
                    'users.role as role')
                ->JoinSub($subquery3, 't1', function ($join) { 
                    $join->on('t1.code', '=', 'users.code');
                    $join->on('t1.max_apply_term_from', '=', 'users.apply_term_from');
                })
                ->JoinSub($subquery4, 't2', function ($join) { 
                    $join->on('t2.code', '=', 'users.department_code');
                })
                ->where('users.code', $user_id)
                ->where('users.is_deleted', 0)
                ->get();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', 'users', Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', 'users', Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
            
        return $mainquery;
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
            $subquery1 = DB::table('users')
                ->selectRaw('code as code')
                ->selectRaw('department_code as department_code')
                ->selectRaw('MAX(apply_term_from) as max_apply_term_from')
                ->where('apply_term_from', '<=',$target_date)
                ->where('is_deleted', '=', 0)
                ->groupBy('code', 'department_code');
            $useremail = DB::table('users')
                ->JoinSub($subquery1, 't1', function ($join) { 
                    $join->on('t1.code', '=', 'users.code');
                    $join->on('t1.department_code', '=', 'users.department_code');
                    $join->on('t1.max_apply_term_from', '=', 'users.apply_term_from');
                })
                ->where('users.code', $user_id)
                ->where('users.is_deleted', 0)
                ->value('users.email');
            if(!isset($useremail)) { return null; }
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', 'users', Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            return $useremail;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', 'users', Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
            return $useremail;
        }

        return $useremail;
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
            return $holiday_kbn;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            return $holiday_kbn;
        }
    }
        
    /** 会社情報取得
     *
     * @return list departments
     */
    public function getCompanyInfoApply(Request $request){

        $results = new Collection();
        try {
            $company_model = new Company();
            $results = $company_model->getCompanyInfoApply();
        }catch(\PDOException $pe){
            return $results;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            return $results;
        }
        return $results;
    }
        
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
            $subquery = DB::table('users')
                ->select('code as code')
                ->selectRaw('MAX(apply_term_from) as max_apply_term_from')
                ->where('apply_term_from', '<=',$target_date)
                ->where('role', '<', Config::get('const.C017.admin_user'))
                ->where('is_deleted', '=', 0)
                ->groupBy('code');
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', 'users', Config::get('const.LOG_MSG.subquery_illegal')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', 'users', Config::get('const.LOG_MSG.subquery_illegal')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
        return $subquery;
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
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', 'departments', Config::get('const.LOG_MSG.subquery_illegal')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', 'departments', Config::get('const.LOG_MSG.subquery_illegal')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
        return $subquery2;
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
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_working_timetables, Config::get('const.LOG_MSG.subquery_illegal')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_working_timetables, Config::get('const.LOG_MSG.subquery_illegal')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
        return $subquery2;
    }

    /**
     * 指定月締日取得（Request）
     *
     * @return list
     */
    public function getClosingDay(Request $request){
        $closing = null;
        try {
            // 設定マスタより締め日取得
            $target_date = $this->setRequestQeury($request->target_date);
            $setting_model = new Setting();
            $target_dateYmd = new Carbon($target_date.'15');
            $setting_model->setParamFiscalmonthAttribute(date_format($target_dateYmd, 'm'));
            $setting_model->setParamYearAttribute(date_format($target_dateYmd, 'Y'));
            $closing = $setting_model->getMonthClosing();
        }catch(\PDOException $pe){
            return $closing;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            return $closing;
        }
        return $closing;
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
        }catch(\PDOException $pe){
            return $closing;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            return $closing;
        }
        return $closing;
    }

    /** 雇用形態リスト取得
     *
     * @return list statuses
     */
    public function getEmploymentStatusList(){
        $statuses =  new Collection();
        try {
            $statuses = DB::table($this->table_generalcodes)->where('identification_id', Config::get('const.C001.value'))->where('is_deleted', 0)->orderby('sort_seq','asc')->get();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
        }
        return $statuses;
    }

    /**
     * タイムテーブルリスト取得
     *
     * @return list
     */
    public function getTimeTableList(){
        $results = new Collection();
        try {
            $time_tables = new WorkingTimeTable();
            $results = $time_tables->getTimeTables();
        }catch(\PDOException $pe){
            return $results;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            return $results;
        }
        return $results;
    }

    /**
     * 営業日区分リスト取得
     *
     * @return list
     */
    public function getBusinessDayList(){
        $businessDays = new Collection();
        try {
            $businessDays = DB::table($this->table_generalcodes)->where('identification_id', Config::get('const.C007.value'))->where('is_deleted', 0)->orderby('sort_seq','asc')->get();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
        }
        return $businessDays;
    }
    
    /**
     * 休暇区分リスト取得
     *
     * @return list
     */
    public function getHoliDayList(){
        $holiDays = new Collection();
        try {
            $holiDays = DB::table($this->table_generalcodes)->where('identification_id', Config::get('const.C008.value'))->where('is_deleted', 0)->orderby('sort_seq','asc')->get();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
        }
        return $holiDays;
    }

    /**
     * 時間単位リスト取得
     *
     * @return list
     */
    public function getTimeUnitList(){
        $timeUnits = new Collection();
        try {
            $timeUnits = DB::table($this->table_generalcodes)->where('identification_id', 'C009')->where('is_deleted', 0)->orderby('sort_seq','asc')->get();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
        }
        return $timeUnits;
    }

    /**
     * 時間の丸めリスト取得
     *
     * @return list
     */
    public function getTimeRoundingList(){
        $timeRounds = new Collection();
        try {
            $timeRounds = DB::table($this->table_generalcodes)->where('identification_id', 'C010')->where('is_deleted', 0)->orderby('sort_seq','asc')->get();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
        }
        return $timeRounds;
    }

    /**
     * 個人休暇リスト取得
     *
     * @return list
     */
    public function getUserLeaveKbnList(){
        $userLeaveKbnList = new Collection();
        try {
            $userLeaveKbnList = DB::table($this->table_generalcodes)->where('identification_id', 'C013')->where('is_deleted', 0)->orderby('sort_seq','asc')->get();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
        }
        return $userLeaveKbnList;
    }

    /**
     * モードリスト取得
     *
     * @return list
     */
    public function getModeList(){
        $modeList = new Collection();
        try {
            $modeList = DB::table($this->table_generalcodes)->where('identification_id', 'C005')->where('is_deleted', 0)->orderby('sort_seq','asc')->get();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
        }
        return $modeList;
    }

    /**
     * コードリスト取得（Request）
     *
     * @return list
     */
    public function getRequestGeneralList(Request $request){
        $results = new Collection();
        try {
            // パラメータチェック
            if (!isset($request->identificationid)) { return null; }
            $results = $this->getGeneralList($request->identificationid);
        }catch(\PDOException $pe){
            return $results;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            return $results;
        }
        return $results;
    }

    /**
     * コードリスト取得
     *
     * @return list
     */
    public function getGeneralList($identification_id){
        $codeList = new Collection();
        try {
            $codeList = DB::table($this->table_generalcodes)->where('identification_id', $identification_id)->where('is_deleted', 0)->orderby('sort_seq','asc')->get();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_generalcodes, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
        }
        return $codeList;
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
                Log::DEBUG('            $filtered from_time = '. $result_time->from_time);
                Log::DEBUG('            $filtered to_time = '.$result_time->to_time);
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
                    Log::DEBUG('            analyzeTimeTable $result_time->from_time = '.$result_time->from_time);
                    Log::DEBUG('            analyzeTimeTable $result_time->to_time = '.$result_time->to_time);
                    $dt = new Carbon('2019-08-01 '.$result_time->from_time);
                    $check_from_hour = date_format($dt, 'H');
                    $check_from_minute = date_format($dt, 'i');
                    $dt = new Carbon('2019-08-01 '.$result_time->to_time);
                    $check_to_hour = date_format($dt, 'H');
                    $check_to_minute = date_format($dt, 'i');
                    Log::DEBUG('            analyzeTimeTable check_from_hour = '.$check_from_hour);
                    Log::DEBUG('            analyzeTimeTable check_from_minute = '.$check_from_minute);
                    Log::DEBUG('            analyzeTimeTable check_to_hour = '.$check_to_hour);
                    Log::DEBUG('            analyzeTimeTable check_to_minute = '.$check_to_hour);
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
     * 翌日を求める
     *
     * @return 翌日
     */
    public function getNextDay($target_date, $str_format){
        $dt = new Carbon($target_date);
        return date_format($dt->addDay(), $str_format);
    }
 
    /**
     * 法定法定外休日判定
     * 
     *
     * @return 
     */
    public function jdgBusinessKbn($target_date)
    {
        // 指定日が休日かどうか
        $business_kubun = null;
        $calender_model = new Calendar();
        $calender_model->setDateAttribute(date_format(new Carbon($target_date), 'Ymd'));
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

        Log::DEBUG('from_time = '.$from_time);
        Log::DEBUG('current_date = '.$current_date);
        Log::DEBUG('target_from_time = '.$target_from_time);
        Log::DEBUG('target_to_time = '.$target_to_time);
        $current_date_ymd = date_format(new Carbon($current_date),'Ymd');
        $target_from_ymd = date_format(new Carbon($target_from_time),'Ymd');
        $target_from_his = date_format(new Carbon($target_from_time),'His');
        $target_to_ymd = date_format(new Carbon($target_to_time),'Ymd');
        $target_to_his = date_format(new Carbon($target_to_time),'His');
        Log::DEBUG('current_date_ymd = '.$current_date_ymd);
        Log::DEBUG('target_from_ymd = '.$target_from_ymd);
        Log::DEBUG('target_to_ymd = '.$target_to_ymd);
        Log::DEBUG('target_from_his = '.$target_from_his);
        Log::DEBUG('target_to_his = '.$target_to_his);
        // 日付付与
        $cnv_from_date = null;
        if ($current_date_ymd == $target_from_ymd) {
            if ($target_from_his > $from_time && $from_time >= Config::get('const.C015.night_to')) {
                //$cnv_from_date = new Carbon($target_from_ymd.' '.$target_from_his);
                $cnv_from_date = new Carbon($target_from_ymd.' '.$from_time);
                Log::DEBUG('cnv_from_date if then= '.$cnv_from_date);
            } else {
                if ($from_time >= Config::get('const.C015.night_to')) {
                    $cnv_from_date = new Carbon($target_from_ymd.' '.$from_time);
                } else {
                    $w_edt_date = new Carbon($target_from_ymd);
                    $w_edt_date = date_format($w_edt_date->addDay(),'Y-m-d');
                    $cnv_from_date = new Carbon($w_edt_date.' '.$from_time);
                }
                Log::DEBUG('cnv_from_date if else= '.$cnv_from_date);
            }
        } else {
            //$cnv_from_date = new Carbon($target_from_ymd.' '.$target_from_his);
            $cnv_from_date = new Carbon($target_from_ymd.' '.$from_time);
            Log::DEBUG('cnv_from_date i$current_date_ymd != $target_from_ymd '.$cnv_from_date);
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

        Log::DEBUG('from_time = '.$from_time);
        Log::DEBUG('to_time = '.$to_time);
        Log::DEBUG('current_date = '.$current_date);
        Log::DEBUG('target_from_time = '.$target_from_time);
        Log::DEBUG('target_to_time = '.$target_to_time);

        $current_date_ymd = date_format(new Carbon($current_date),'Ymd');
        $target_from_ymd = date_format(new Carbon($target_from_time),'Ymd');    // 打刻時刻
        $target_from_his = date_format(new Carbon($target_from_time),'His');    // 打刻時刻
        $target_to_ymd = date_format(new Carbon($target_to_time),'Ymd');        // 打刻時刻
        $target_to_his = date_format(new Carbon($target_to_time),'His');        // 打刻時刻
        Log::DEBUG('current_date_ymd = '.$current_date_ymd);
        Log::DEBUG('target_from_ymd = '.$target_from_ymd);
        Log::DEBUG('target_to_ymd = '.$target_to_ymd);
        Log::DEBUG('target_from_his = '.$target_from_his);
        Log::DEBUG('target_to_his = '.$target_to_his);
        // 日付付与
        $cnv_to_date = null;
        if ($current_date_ymd == $target_to_ymd) {
            if ($from_time > $to_time) {
                //$cnv_to_date = new Carbon($target_to_ymd.' '.$target_to_his);
                $cnv_to_date = $this->getNextDay($target_to_ymd, 'Y-m-d').' '.$to_time;
                Log::DEBUG('cnv_to_date if then= '.$cnv_to_date);
            } else {
                if ($target_to_his < $to_time) {
                    $cnv_to_date = new Carbon($target_to_ymd.' '.$to_time);
                    Log::DEBUG('cnv_to_date if then= '.$cnv_to_date);
                } else {
                    $cnv_to_date = new Carbon($target_to_ymd.' '.$to_time);
                    Log::DEBUG('cnv_to_date if else= '.$cnv_to_date);
                }
            }
        } else {
            if ($from_time > $to_time) {
                if ($target_to_his < $to_time) {
                    //$cnv_to_date = new Carbon($target_to_ymd.' '.$target_to_his);
                    $cnv_to_date = new Carbon($target_to_ymd.' '.$to_time);
                    Log::DEBUG('cnv_to_date if then= '.$cnv_to_date);
                } else {
                    $cnv_to_date = new Carbon($target_to_ymd.' '.$to_time);
                    Log::DEBUG('cnv_to_date if else= '.$cnv_to_date);
                }
            } else {
                if ($to_time >= Config::get('const.C015.night_to')) {
                    $cnv_to_date = new Carbon($current_date_ymd.' '.$to_time);
                    Log::DEBUG('cnv_to_date if else= '.$cnv_to_date);
                } else {
                    $cnv_to_date = new Carbon($target_to_ymd.' '.$to_time);
                    Log::DEBUG('cnv_to_date if else= '.$cnv_to_date);
                }
            }
        }
        Log::DEBUG('         ------------- convTimeToDateTo end '.$cnv_to_date);

        return $cnv_to_date;
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
        Log::DEBUG('インターバル時間 = '.str_pad($hh, 2 , '0', STR_PAD_LEFT).":".str_pad($mm, 2 , '0', STR_PAD_LEFT).":00");
        return str_pad($hh, 2 , '0', STR_PAD_LEFT).":".str_pad($mm, 2 , '0', STR_PAD_LEFT).":00";
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

}
