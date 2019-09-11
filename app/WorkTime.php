<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\ApiCommonController;
use Carbon\Carbon;

class WorkTime extends Model
{
    protected $table = 'work_times';
    protected $table_users = 'users';
    protected $table_departments = 'departments';
    protected $table_shift_informations = 'shift_informations';
    protected $table_user_holiday_kubuns = 'user_holiday_kubuns';
    // protected $guarded = array('id');

    //--------------- メンバー属性 -----------------------------------

    private $id;
    private $user_code;                     // ユーザーコード
    private $department_code;               // 部署コード
    private $record_time;                   // 打刻時間
    private $mode;                          // 打刻モード
    private $check_result;                  // 打刻チェック結果
    private $check_max_time;                // 打刻回数最大チェック結果
    private $created_user;                  // 作成ユーザー
    private $updated_user;                  // 修正ユーザー
    private $is_deleted;                    // 削除フラグ
    private $systemdate;

    // ユーザーコード
    public function getIdAttribute()
    {
        return $this->id;
    }

    public function setIdAttribute($value)
    {
        $this->id = $value;
    }

    // ユーザーコード
    public function getUsercodeAttribute()
    {
        return $this->user_code;
    }

    public function setUsercodeAttribute($value)
    {
        $this->user_code = $value;
    }

    // 部署コード
    public function getDepartmentcodeAttribute()
    {
        return $this->department_code;
    }

    public function setDepartmentcodeAttribute($value)
    {
        $this->department_code = $value;
    }


    // 打刻時間
    public function getRecordtimeAttribute()
    {
        return $this->record_time;
    }

    public function setRecordtimeAttribute($value)
    {
        $this->record_time = $value;
    }


    // 打刻モード
    public function getModeAttribute()
    {
        return $this->mode;
    }

    public function setModeAttribute($value)
    {
        $this->mode = $value;
    }


    // 打刻チェック結果
    public function getCheckresultAttribute()
    {
        return $this->check_result;
    }

    public function setCheckresultAttribute($value)
    {
        $this->check_result = $value;
    }


    // 打刻回数最大チェック結果
    public function getCheckmaxtimeAttribute()
    {
        return $this->check_max_time;
    }

    public function setCheckmaxtimeAttribute($value)
    {
        $this->check_max_time = $value;
    }
    

    // 作成ユーザー
    public function getCreateduserAttribute()
    {
        return $this->created_user;
    }

    public function setCreateduserAttribute($value)
    {
        $this->created_user = $value;
    }


    // 修正ユーザー
    public function getUpdateduserAttribute()
    {
        return $this->updated_user;
    }

    public function setUpdateduserAttribute($value)
    {
        $this->updated_user = $value;
    }


    // 削除フラグ
    public function getIsdeletedAttribute()
    {
        return $this->is_deleted;
    }

    public function setIsdeletedAttribute($value)
    {
        $this->is_deleted = $value;
    }

    // sysdate
    public function getSystemDateAttribute()
    {
        return $this->systemdate;
    }

    public function setSystemDateAttribute($value)
    {
        $this->systemdate = $value;
    }


    //--------------- メンバー編集項目属性 -----------------------------------

    // 打刻日付（work_timesのrecord_timeの日付部分）
    public function getRecordtimeDateAttribute()
    {
        return date_format($this->record_time, 'Y/m/d');
    }

    // 打刻時刻（work_timesのrecord_timeの時刻部分）
    public function getRecordtimeTimeAttribute($value)
    {
        return date_format($this->record_time, 'H:i:s');
    }


    //--------------- パラメータ項目属性 -----------------------------------

    private $param_date_from;                   // 開始日付（00:00:00から）
    private $param_date_to;                     // 終了日付（23:59:59まで）
    private $param_employment_status;           // 雇用形態
    private $param_department_code;             // 部署
    private $param_user_code;                   // ユーザー
    private $param_mode;                        // 打刻モード

    private $array_record_time;                 // 日付範囲配列
    private $massegedata;                       // メッセージ

    private $param_start_date;                  // 開始
    private $param_end_date;                    // 終了


    // 開始日付（00:00:00から）
    public function getParamdatefromAttribute()
    {
        return $this->param_date_from;
    }

    public function setParamdatefromAttribute($value)
    {
        $date = date_create($value);
        $this->param_date_from = $date->format('Y/m/d').' 00:00:00';
        Log::debug('$this->param_date_from = '.$this->param_date_from);
    }

    public function setParamdatefromNoneditAttribute($value)
    {
        $date = date_create($value);
        $this->param_date_from = $date->format('Y/m/d H:i:s');
        Log::debug('$this->param_date_from = '.$this->param_date_from);
    }


    // 終了日付（23:59:59まで）
    public function getParamdatetoAttribute()
    {
        return $this->param_date_to;
    }

    public function setParamdatetoAttribute($value)
    {
        $date = date_create($value);
        $this->param_date_to = $date->format('Y/m/d').' 23:59:59';
        Log::debug('$this->param_date_to = '.$this->param_date_to);
    }

    public function setParamdatetoNoneditAttribute($value)
    {
        $date = date_create($value);
        $this->param_date_to = $date->format('Y/m/d H:i:s');
        Log::debug('$this->param_date_to = '.$this->param_date_to);
    }

    // 雇用形態
    public function getParamemploymentstatusAttribute()
    {
        return $this->param_employment_status;
    }

    public function setParamemploymentstatusAttribute($value)
    {
        $this->param_employment_status = $value;
    }

    // 部署
    public function getParamDepartmentcodeAttribute()
    {
        return $this->param_department_code;
    }

    public function setParamDepartmentcodeAttribute($value)
    {
        $this->param_department_code = $value;
    }

    // ユーザー
    public function getParamUsercodeAttribute()
    {
        return $this->param_user_code;
    }

    public function setParamUsercodeAttribute($value)
    {
        $this->param_user_code = $value;
    }

    // 打刻モード
    public function getParamModeAttribute()
    {
        return $this->param_mode;
    }

    public function setParamModeAttribute($value)
    {
        $this->param_mode = $value;
    }

    // 日付範囲配列
    public function getArrayrecordtimeAttribute()
    {
        return $this->array_record_time;
    }

    public function setArrayrecordtimeAttribute($valuefrom, $valueto)
    {
        $this->array_record_time = array();       //初期化
        $this->array_record_time = array($valuefrom, $valueto);
    }

    // メッセージ
    public function getMassegedataAttribute()
    {
        return $this->massegedata;
    }

    public function setMassegedataAttribute($value)
    {
        $this->massegedata = $value;
    }

    // 開始日付
    public function getParamStartDateAttribute()
    {
        return $this->param_start_date;
    }

    public function setParamStartDateAttribute($value)
    {
        $this->param_start_date = $value;
    }

    // 終了日付
    public function getParamEndDateAttribute()
    {
        return $this->param_end_date;
    }

    public function setParamEndDateAttribute($value)
    {
        $this->param_end_date = $value;
    }


    // --------------------- メソッド ------------------------------------------------------

    /**
     * 勤怠登録
     *
     * @return void
     */
    public function insertWorkTime(){
        DB::table($this->table)->insert(
            [
                'user_code' => $this->user_code,
                'department_code' => $this->department_code,
                'record_time' => $this->record_time,
                'mode' => $this->mode,
                'check_result' => $this->check_result,
                'check_max_time' => $this->check_max_time,
                'created_user' => $this->created_user,
                'created_at'=>$this->systemdate
            ]
        );
    }


    /**
     * 日次集計取得
     *
     * @return void
     */
    public function getDailyData(){
        $tasks = DB::table($this->table)
            ->join('users', 'work_times.user_code', '=', 'users.code')
            ->select(
                    'work_times.user_code',
                    'work_times.',
                    'work_times.end_date'
                    )
            // ->where('tasks.department_code',$department_code)
            ->limit(1)
            ->get();

        return $tasks;
    }

    /**
     * 日次労働時間取得事前チェック
     *
     *      指定したユーザー、部署、日付範囲内の労働時間計算のもとデータを取得するSQL
     *
     *      INPUT：
     *          ①テーブル：departments　指定部署内 and 削除=0
     *          ②テーブル：users　      指定ユーザー内 and 削除=0
     *          ③テーブル：work_times　 ユーザーand日付範囲内 and 削除=0
     *          ④①と②と③の結合          ①.ユーザー = ②.ユーザー and ②.ユーザー = ③.ユーザー
     *
     *      チェック方法：
     *          ①日付範囲指定必須チェック
     *          ②user_code範囲指定プロパティを事前設定（未設定有効）
     *          ③日付範囲指定プロパティを事前設定（未設定無効）
     *          ④メソッド：calcWorkingTimeDateを実行
     *s
     * @return sql取得結果
     */
    public function chkWorkingTimeData(){
        $this->massegedata = array();
        $result = true;

        // 日付範囲指定必須チェック
        if(isset($this->param_date_from) && isset($this->param_date_to)){
            // 日付範囲指定比較チェック
            $chkDateFrom = $this->getParamDatefromAttribute();
            $chkDateTo = $this->getParamDatetoAttribute();
            if($chkDateFrom <= $chkDateTo){
                $this->setArrayrecordtimeAttribute($chkDateFrom, $chkDateTo);
            } else {
                $this->massegedata[] = array(Config::get('const.RESPONCE_ITEM.message') =>Config::get('const.MSG_ERROR.not_between_workindate'));
                $result = false;
            }
        } else {
            $this->massegedata[] = array(Config::get('const.RESPONCE_ITEM.message') =>Config::get('const.MSG_ERROR.not_input_workindatefromto'));
            $result = false;
        }

        return $result;

    }

    /**
     * 日次労働時間取得
     *
     *      指定したユーザー、日付範囲内の労働時間計算のもとデータを取得するSQL
     *
     *      INPUT：
     *          ①テーブル：departments　部署範囲内 and 削除=0
     *          ②テーブル：users　      ユーザー範囲内 and 削除=0
     *          ③テーブル：work_times　 ユーザーand日付範囲内 and 削除=0
     *          ④①と②と③の結合          ①.ユーザー = ②.ユーザー and ②.ユーザー = ③.ユーザー
     *
     *      使用方法：
     *          ①department_code指定プロパティを事前設定（未設定有効）
     *          ②user_code指定プロパティを事前設定（未設定有効）
     *          ③日付範囲指定プロパティを事前設定（未設定無効）
     *          ④メソッド：calcWorkingTimeDateを実行
     *
     * @return sql取得結果
     */
    public function getWorkTimes($targetdatefrom, $targetdateto, $business_kubun){

        // 日次労働時間取得SQL作成
        // subquery1    work_times
        \DB::enableQueryLog();
        $subquery1 = DB::table($this->table)
            ->select(
                $this->table.'.user_code as user_code',
                $this->table.'.department_code as department_code',
                $this->table.'.record_time as record_datetime',
                $this->table.'.mode as mode',
                $this->table.'.check_result as check_result',
                $this->table.'.check_max_time as check_max_time',
                $this->table.'.is_deleted as is_deleted'
            )
            ->selectRaw('DATE_FORMAT(ifnull('.$this->table.".record_time,'".$targetdatefrom."'), '%Y') as record_year")
            ->selectRaw('DATE_FORMAT(ifnull('.$this->table.".record_time,'".$targetdatefrom."'), '%m') as record_month")
            ->selectRaw('DATE_FORMAT(ifnull('.$this->table.".record_time,'".$targetdatefrom."'), '%Y%m%d') as record_date")
            ->selectRaw('DATE_FORMAT('.$this->table.'.record_time'.",'%H%i%s') as record_time");

        $record_time = $this->getArrayrecordtimeAttribute();
        if(!empty($record_time)){
            $subquery1->where($this->table.'.record_time', '>=', $this->param_date_from);       //record_time範囲指定
            $subquery1->where($this->table.'.record_time', '<=', $this->param_date_to);         //record_time範囲指定
        }
        $subquery1->where($this->table.'.is_deleted', '=', 0);

        // subquery2    shift_informations
        $subquery2 = DB::table($this->table_shift_informations)
            ->select(
                $this->table_shift_informations.'.user_code as user_code',
                $this->table_shift_informations.'.department_code as department_code',
                $this->table_shift_informations.'.working_timetable_no as shift_no',
                $this->table_shift_informations.'.is_deleted as is_deleted'
                )
            ->selectRaw('DATE_FORMAT('.$this->table_shift_informations.'.target_date'.",'%Y%m%d') as target_date");
        $subquery2->where($this->table_shift_informations.'.is_deleted', '=', 0);

        // 適用期間日付の取得
        $apicommon = new ApiCommonController();
        // usersの最大適用開始日付subquery
        $subquery3 = $apicommon->getUserApplyTermSubquery($targetdateto);
        // departmentsの最大適用開始日付subquery
        $subquery4 = $apicommon->getDepartmentApplyTermSubquery($targetdateto);

        // mainqueryにsunqueryを組み込む
        // mainquery    users
        // subquery1    work_times
        // subquery2    shift_informations
        $mainquery = DB::table($this->table_users.' AS t1')
            ->select(
                't1.code as user_code',
                't1.name as user_name',
                't1.department_code as department_code',
                't5.name as department_name',
                't2.record_datetime as record_datetime',
                't2.record_year as record_year',
                't2.record_month as record_month',
                't2.record_date as record_date',
                't2.record_time as record_time',
                't1.employment_status as employment_status',
                't8.code_name as employment_status_name',
                't2.mode as mode',
                't2.check_result as check_result',
                't2.check_max_time as check_max_time',
                't3.weekday_kubun as weekday_kubun',
                't11.code_name as weekday_name',
                't3.business_kubun as business_kubun',
                't12.code_name as business_name',
                't3.holiday_kubun as holiday_kubun',
                't13.code_name as holiday_name',
                't4.closing as closing',
                't4.uplimit_time as uplimit_time',
                't4.statutory_uplimit_time as statutory_uplimit_time',
                't4.time_unit as time_unit',
                't4.time_rounding as time_rounding',
                't4.max_3month_total as max_3month_total',
                't4.max_6month_total as max_6month_total',
                't4.max_12month_total as max_12month_total',
                't4.beginning_month as beginning_month',
                't4.interval as interval',
                't4.year as year',
                't14.holiday_kubun as user_holiday_kubun',
                't15.code_name as user_holiday_name'
            );
        $mainquery
            ->selectRaw('ifnull(t9.shift_no, t6.no) as working_timetable_no ')
            ->selectRaw('CASE ifnull(t9.shift_no, 0) WHEN 0 THEN t6.name else t10.name end as working_timetable_name ')
            ->selectRaw('CASE ifnull(t9.shift_no, 0) WHEN 0 THEN t6.from_time else t10.from_time end as working_timetable_from_time ')
            ->selectRaw('CASE ifnull(t9.shift_no, 0) WHEN 0 THEN t6.to_time else t10.to_time end as working_timetable_to_time ')
            ->selectRaw("ifnull(t9.shift_no, 0) as shift_no ")
            ->selectRaw("ifnull(t10.name, '') as shift_name ")
            ->selectRaw("ifnull(t10.from_time, '') as shift_from_time ")
            ->selectRaw("ifnull(t10.to_time, '') as shift_to_time ")
            ->leftJoinSub($subquery1, 't2', function ($join) { 
                $join->on('t2.user_code', '=', 't1.code');
                $join->on('t2.department_code', '=', 't1.department_code')
                ->where('t1.is_deleted', '=', 0)
                ->where('t2.is_deleted', '=', 0);
            })
            ->leftJoinSub($subquery2, 't9', function ($join) { 
                $join->on('t9.user_code', '=', 't1.code');
                $join->on('t9.department_code', '=', 't1.department_code');
                $join->on('t9.target_date', '=', 't2.record_date')
                ->where('t1.is_deleted', '=', 0)
                ->where('t9.is_deleted', '=', 0);
            })
            ->leftJoinSub($subquery4, 't5', function ($join) { 
                $join->on('t5.code', '=', 't1.department_code')
                ->where('t1.is_deleted', '=', 0);
            })
            ->leftJoin('calendars as t3', function ($join) { 
                $join->on('t3.date', '=', 't2.record_date')
                ->where('t3.is_deleted', '=', 0);
            })
            ->leftJoin('settings as t4', function ($join) { 
                $join->on('t4.year', '=', 't2.record_year');
                $join->on('t4.fiscal_month', '=', 't2.record_month')
                ->where('t4.is_deleted', '=', 0);
            })
            ->leftJoin('working_timetables as t6', function ($join) { 
                $join->on('t6.no', '=', 't1.working_timetable_no')
                ->where('t6.working_time_kubun', '=', Config::get('const.C004.regular_working_time'))
                ->where('t1.is_deleted', '=', 0)
                ->where('t6.is_deleted', '=', 0);
            })
            ->leftJoin('generalcodes as t7', function ($join) { 
                $join->on('t7.code', '=', 't6.working_time_kubun')
                ->where('t7.identification_id', '=', Config::get('const.C004.value'))
                ->where('t6.is_deleted', '=', 0)
                ->where('t7.is_deleted', '=', 0);
            })
            ->leftJoin('generalcodes as t8', function ($join) { 
                $join->on('t8.code', '=', 't1.employment_status')
                ->where('t8.identification_id', '=', Config::get('const.C001.value'))
                ->where('t1.is_deleted', '=', 0)
                ->where('t8.is_deleted', '=', 0);
            })
            ->leftJoin('working_timetables as t10', function ($join) { 
                $join->on('t10.no', '=', 't9.shift_no')
                ->where('t10.working_time_kubun', '=', Config::get('const.C004.regular_working_time'))
                ->where('t10.is_deleted', '=', 0);
            })
            ->leftJoin('generalcodes as t11', function ($join) { 
                $join->on('t11.code', '=', 't3.weekday_kubun')
                ->where('t11.identification_id', '=', Config::get('const.C006.value'))
                ->where('t3.is_deleted', '=', 0)
                ->where('t11.is_deleted', '=', 0);
            })
            ->leftJoin('generalcodes as t12', function ($join) { 
                $join->on('t12.code', '=', 't3.business_kubun')
                ->where('t12.identification_id', '=', Config::get('const.C007.value'))
                ->where('t3.is_deleted', '=', 0)
                ->where('t12.is_deleted', '=', 0);
            })
            ->leftJoin('generalcodes as t13', function ($join) { 
                $join->on('t13.code', '=', 't3.holiday_kubun')
                ->where('t13.identification_id', '=', Config::get('const.C008.value'))
                ->where('t3.is_deleted', '=', 0)
                ->where('t13.is_deleted', '=', 0);
            })
            ->leftJoin('user_holiday_kubuns as t14', function ($join) { 
                $join->on('t14.working_date', '=', 't2.record_date');
                $join->on('t14.user_code', '=', 't1.code');
                $join->on('t14.department_code', '=', 't1.department_code')
                ->where('t1.is_deleted', '=', 0)
                ->where('t14.is_deleted', '=', 0);
            })
            ->leftJoin('generalcodes as t15', function ($join) { 
                $join->on('t15.code', '=', 't14.holiday_kubun')
                ->where('t15.identification_id', '=', Config::get('const.C013.value'))
                ->where('t14.is_deleted', '=', 0)
                ->where('t15.is_deleted', '=', 0);
            });

        if(!empty($this->param_employment_status)){
            $mainquery->where('t1.employment_status', $this->param_employment_status);  //　雇用形態指定
        }
        if(!empty($this->param_department_code)){
            $mainquery->where('t1.department_code', $this->param_department_code);      //department_code指定
        }
        if(!empty($this->param_user_code)){
            $mainquery->where('t1.code', $this->param_user_code);                       //user_code指定
        } else {
            $mainquery->where('t1.role','<',Config::get('const.C017.out_of_user'));
        }
        $mainquery
            ->JoinSub($subquery3, 't14', function ($join) { 
                $join->on('t14.code', '=', 't1.code');
                $join->on('t14.max_apply_term_from', '=', 't1.apply_term_from');
            });
        if ($business_kubun != Config::get('const.C007.basic')) {
            $mainquery->whereNotNull('t2.record_datetime');
        }
        $result = $mainquery
            ->where('t1.role', '<', 10)
            ->where('t1.is_deleted', '=', 0)
            ->orderBy('t1.department_code', 'asc')
            ->orderBy('t1.employment_status', 'asc')
            ->orderBy('t1.code', 'asc')
            ->orderBy('t2.record_date', 'asc')
            ->orderBy('t2.record_datetime', 'asc')
            ->get();
        \Log::debug(
            'sql_debug_log',
            [
                'getWorkTimes' => \DB::getQueryLog()
            ]
        );

        return $result;
    }

    /**
     * 前日の勤務状態取得
     *      開始日付直前の打刻モード、時刻を取得する
     * 
     * @return void
     */
    public function getBeforeDailyMaxData(){
        // 開始日付直前の打刻時刻を取得
        // sunquery1    work_times

        try{
            $subquery1 = DB::table($this->table)
                ->select(
                    $this->table.'.user_code',
                    $this->table.'.department_code',
                    $this->table.'.record_time')
                ->selectRaw('DATE_FORMAT('.$this->table.".record_time, '%Y%m%d') as record_date")
                ->where($this->table.'.is_deleted', '=', 0);

            $subquery2 = DB::table($this->table_user_holiday_kubuns)
                ->select(
                    $this->table_user_holiday_kubuns.'.working_date',
                    $this->table_user_holiday_kubuns.'.user_code',
                    $this->table_user_holiday_kubuns.'.department_code')
                ->selectRaw('ifnull('.$this->table_user_holiday_kubuns.'.holiday_kubun, 0) as holiday_kubun')
                ->where($this->table_user_holiday_kubuns.'.is_deleted', '=', 0);

            $subquery11 = $subquery1->toSql();

            $subquery12 = DB::table(DB::raw('('.$subquery11.') AS t1'))
                ->select(
                    't1.user_code',
                    't1.department_code',
                    't1.record_date',
                    't1.record_time',
                    't2.working_date')
                ->leftJoinSub($subquery2, 't2', function ($join) { 
                    $join->on('t2.working_date', '=', 't1.record_date');
                    $join->on('t2.user_code', '=', 't1.user_code');
                    $join->on('t2.department_code', '=', 't1.department_code')
                    ->where('t2.holiday_kubun', '>', 0);
                })
                ->whereNull('t2.working_date');

            $subquery = $subquery12->toSql();

            $subquery_max = DB::table(DB::raw('('.$subquery.') AS t3'))
                ->select(
                    't3.user_code',
                    't3.department_code')
                ->selectRaw('max(t3.record_time) as max_record_datetime')
                ->where('t3.user_code', '=', $this->param_user_code)
                ->where('t3.department_code', '=', $this->param_department_code)
                ->where('t3.record_time', '<', $this->param_date_from)
                ->groupBy('t3.user_code', 't3.department_code');

            $subquery_max->setBindings([
                0,
                0,
                0,
                $this->param_user_code,
                $this->param_department_code,
                $this->param_date_from]);
    
            $mainquery  = DB::table($this->table)
                ->select(
                    $this->table.'.mode as mode',
                    $this->table.'.record_time as record_datetime')
                ->JoinSub($subquery_max, 't3', function ($join) { 
                    $join->on('t3.user_code', '=', $this->table.'.user_code');
                    $join->on('t3.department_code', '=', $this->table.'.department_code');
                    $join->on('t3.max_record_datetime', '=', $this->table.'.record_time');
                })
                ->where($this->table.'.is_deleted', '=', 0);

            $mainquery->setBindings([
                0]);

            $result = $mainquery->get();
                
        }catch(\PDOException $pe){
            Log::error('method = getBeforeDailyMaxData '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('method = getBeforeDailyMaxData '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    
        return $result;
    }

    /**
     * 翌日の勤務状態取得
     *      開始日付直後の打刻モード、時刻を取得する
     * 
     * @return void
     */
    public function getAfterDailyMinData(){
        // 開始日付直前の打刻時刻を取得
        // sunquery1    work_times
        try{
            $subquery1 = DB::table($this->table)
                ->select(
                    $this->table.'.user_code',
                    $this->table.'.department_code',
                    $this->table.'.record_time')
                ->selectRaw('DATE_FORMAT('.$this->table.".record_time, '%Y%m%d') as record_date")
                ->where($this->table.'.is_deleted', '=', 0);

            $subquery2 = DB::table($this->table_user_holiday_kubuns)
                ->select(
                    $this->table_user_holiday_kubuns.'.working_date',
                    $this->table_user_holiday_kubuns.'.user_code',
                    $this->table_user_holiday_kubuns.'.department_code')
                ->selectRaw('ifnull('.$this->table_user_holiday_kubuns.'.holiday_kubun, 0) as holiday_kubun')
                ->where($this->table_user_holiday_kubuns.'.is_deleted', '=', 0);

            $subquery11 = $subquery1->toSql();

            $subquery12 = DB::table(DB::raw('('.$subquery11.') AS t1'))
                ->select(
                    't1.user_code',
                    't1.department_code',
                    't1.record_date',
                    't1.record_time',
                    't2.working_date')
                ->leftJoinSub($subquery2, 't2', function ($join) { 
                    $join->on('t2.working_date', '=', 't1.record_date');
                    $join->on('t2.user_code', '=', 't1.user_code');
                    $join->on('t2.department_code', '=', 't1.department_code')
                    ->where('t2.holiday_kubun', '>', 0);
                })
                ->whereNull('t2.working_date');

            $subquery = $subquery12->toSql();

            $subquery_max = DB::table(DB::raw('('.$subquery.') AS t3'))
                ->select(
                    't3.user_code',
                    't3.department_code')
                ->selectRaw('min(t3.record_time) as min_record_datetime')
                ->where('t3.user_code', '=', $this->param_user_code)
                ->where('t3.department_code', '=', $this->param_department_code)
                ->where('t3.record_time', '>', $this->param_date_from)
                ->groupBy('t3.user_code', 't3.department_code');

            $subquery_max->setBindings([
                0,
                0,
                0,
                $this->param_user_code,
                $this->param_department_code,
                $this->param_date_from]);
    
            $mainquery  = DB::table($this->table)
                ->select(
                    $this->table.'.mode as mode',
                    $this->table.'.record_time as record_datetime')
                ->JoinSub($subquery_max, 't3', function ($join) { 
                    $join->on('t3.user_code', '=', $this->table.'.user_code');
                    $join->on('t3.department_code', '=', $this->table.'.department_code');
                    $join->on('t3.min_record_datetime', '=', $this->table.'.record_time');
                })
                ->where($this->table.'.is_deleted', '=', 0);

            $mainquery->setBindings([
                0]);

            $result = $mainquery->get();
                
        }catch(\PDOException $pe){
            Log::error('method = getAfterDailyMinData '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('method = getAfterDailyMinData '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    
        return $result;

    }

    /**
     * 現在の勤務状態取得
     *      開始日付の打刻モード、時刻を取得する
     * 
     * @return void
     */
    public function getDailyMaxData(){
        // 開始日付直前の打刻時刻を取得
        // sunquery1    work_times
        $sunquery1 = DB::table($this->table)
            ->select(
                $this->table.'.user_code',
                $this->table.'.department_code',
                $this->table.'.is_deleted',
                DB::raw('MAX('.$this->table.'.record_time) as max_record_time')
                )
            ->where($this->table.'.user_code', '=', $this->param_user_code)
            ->where($this->table.'.department_code', '=', $this->param_department_code)
            ->where($this->table.'.record_time', '<', $this->param_date_from)
            ->groupBy($this->table.'.user_code', $this->table.'.department_code', $this->table.'.is_deleted');

        // mainqueryにsunqueryを組み込む
        // sunquery1    t1:work_times
        $mainquery = DB::table($this->table.' AS t1')
            ->select(
                't1.mode as mode',
                't1.record_time as record_datetime'
                )
            ->JoinSub($sunquery1, 't2', function ($join) { 
                $join->on('t2.user_code', '=', 't1.user_code');
                $join->on('t2.department_code', '=', 't1.department_code');
                $join->on('t2.max_record_time', '=', 't1.record_time')
                ->where('t2.is_deleted', '=', 0);
            })
            ->where('t1.is_deleted', '=', 0)
            ->get();


        return $mainquery;
    }

    /**
     * ユーザーの勤務時間取得
     *
     * @return $data
     */
    public function getUserDetails(){
        // users max(apply_term_from)
        $max_user_apply = DB::table($this->table_users)
            ->selectRaw('MAX(apply_term_from) as max_apply_term_from')
            ->where('is_deleted', '=', 0)
            ->where('code',$this->user_code)
            ->value('max_apply_term_from');
        
        $user_department_code = DB::table($this->table_users)
            ->where('code', $this->user_code)
            ->where('apply_term_from', $max_user_apply)
            ->where('is_deleted', 0)->value('department_code');

        // departments max(apply_term_from)
        $max_department_apply = DB::table($this->table_departments)
            ->selectRaw('MAX(apply_term_from) as max_apply_term_from')
            ->where('is_deleted', '=', 0)
            ->where('code',$user_department_code)
            ->value('max_apply_term_from');

        // users
        $subquery1 = DB::table($this->table_users)
            // ->selectRaw('MAX(apply_term_from) as max_apply_term_from')
            ->selectRaw('code as code')
            ->selectRaw('name as name')
            ->where('is_deleted', '=', 0)
            ->where('apply_term_from',$max_user_apply);
        
        // departments
        $subquery2 = DB::table($this->table_departments)
            // ->selectRaw('MAX(apply_term_from) as max_apply_term_from')
            ->selectRaw('code as code')
            ->selectRaw('name as name')
            ->where('is_deleted', '=', 0)
            ->where('apply_term_from',$max_department_apply);
    
        // \DB::enableQueryLog();
        $data = DB::table($this->table)
            // ->join('users','users.code','=',$this->table.'.user_code')
            // ->join('departments','departments.code','users.department_code')
            ->JoinSub($subquery1, 't1', function ($join) { 
                $join->on('t1.code', '=', $this->table.'.user_code');
                // $join->on('t1.max_apply_term_from', '=', $max_user_apply);
            })
            ->JoinSub($subquery2, 't2', function ($join) { 
                $join->on('t2.code', '=', $this->table.'.department_code');
                // $join->on('t2.max_apply_term_from', '=', $max_department_apply);
            })
            ->leftJoin('generalcodes as g', function ($join) { 
                $join->on('g.code', '=', $this->table.'.mode')
                ->where('g.identification_id', '=', Config::get('const.C005.value'));
            })
            ->select(
                $this->table.'.id',
                $this->table.'.user_code',
                $this->table.'.department_code',
                $this->table.'.record_time',
                $this->table.'.mode',
                't1.name as user_name',
                't2.name as d_name',
                'g.code_name'
            )
            ->where($this->table.'.user_code', $this->user_code)
            ->whereBetween('record_time', [$this->param_start_date,$this->param_end_date])
            ->where($this->table.'.is_deleted', 0)
            ->orderBy($this->table.'.record_time', 'asc')
            ->get();

        //  \Log::debug(
        //    'sql_debug_log',
        //    [
        //        'わかりやすい名称' => \DB::getQueryLog()
        //    ]
        //    );
        
        return $data;
    }

    /**
     * 論理削除
     *
     * @return void
     */
    public function delWorkTime(){
        DB::table($this->table)
            ->where('id', $this->id)
            ->where('is_deleted', 0)
            ->update([
                'is_deleted' => 1,
                'updated_at' => $this->systemdate
                ]);
    }

    /**
     * モード回数取得
     *
     * @return void
     */
    public function getModeCount(){
        $users = DB::table($this->table)
            ->where($this->table.'.user_code', '=', $this->param_user_code)
            ->where($this->table.'.department_code', '=', $this->param_department_code)
            ->where($this->table.'.mode', '<', $this->param_mode)
            ->where('is_deleted', 0)
            ->count();

        return $users;
    }

    /**
     * 警告打刻取得
     * 
     *  $targetdateは適用期間
     *
     * @return void
     */
    public function getAlertData($targetdate){
        // 日次労働時間取得SQL作成
        // subquery1    work_times
        \DB::enableQueryLog();
        $subquery1 = DB::table($this->table)
            ->selectRaw(
                'concat(DATE_FORMAT('.$this->table.".record_time,'%Y年%m月%d日'),'(',substring('月火水木金土日',convert(DATE_FORMAT(".$this->table.".record_time,'%w'),char),1),')') as record_date ");
        $subquery1
            ->addselect($this->table.'.user_code as user_code')
            ->addselect($this->table.'.department_code as department_code')
            ->addselect($this->table.'.record_time as record_time')
            ->addselect($this->table.'.mode as mode');
        $subquery1
            ->selectRaw('case check_result when null then 0 else check_result end as check_result')
            ->selectRaw('case check_max_time when null then 0 else check_max_time end as check_max_time');
        $record_time = $this->getArrayrecordtimeAttribute();
        if(!empty($record_time)){
            $subquery1->where($this->table.'.record_time', '>=', $this->param_date_from);       //record_time範囲指定
            $subquery1->where($this->table.'.record_time', '<=', $this->param_date_to);         //record_time範囲指定
        }
        $subquery1->where($this->table.'.is_deleted', '=', 0);

        // 適用期間日付の取得
        $apicommon = new ApiCommonController();
        // usersの最大適用開始日付subquery
        $subquery3 = $apicommon->getUserApplyTermSubquery($targetdate);
        // departmentsの最大適用開始日付subquery
        $subquery4 = $apicommon->getDepartmentApplyTermSubquery($targetdate);

        // mainqueryにsunqueryを組み込む
        $mainquery = DB::table($this->table_users.' AS t1')
            ->select(
                't1.code as user_code',
                't1.name as user_name',
                't1.employment_status as employment_status',
                't5.code_name as employment_status_name',
                't1.department_code as department_code',
                't3.name as department_name',
                't2.record_date as record_date',
                't2.record_time as record_time',
                't2.mode as mode',
                't6.code_name as mode_name',
                't2.check_result as check_result',
                't2.check_max_time as check_max_time');
        $mainquery
            ->selectRaw("case t7.code_name is null when 1 then case t8.code_name is null when 1 then null else t8.code_name end else case t8.code_name is null when 1 then t7.code_name else t7.code_name || ' ' ||t8.code_name end end as alert_memo")
            ->leftJoinSub($subquery1, 't2', function ($join) { 
                $join->on('t2.user_code', '=', 't1.code');
                $join->on('t2.department_code', '=', 't1.department_code')
                ->where('t1.is_deleted', '=', 0);
            })
            ->leftJoinSub($subquery4, 't3', function ($join) { 
                $join->on('t3.code', '=', 't1.department_code')
                ->where('t1.is_deleted', '=', 0);
            })
            ->leftJoin('generalcodes as t5', function ($join) { 
                $join->on('t5.code', '=', 't1.employment_status')
                ->where('t5.identification_id', '=', Config::get('const.C001.value'))
                ->where('t1.is_deleted', '=', 0)
                ->where('t5.is_deleted', '=', 0);
            })
            ->leftJoin('generalcodes as t6', function ($join) { 
                $join->on('t6.code', '=', 't2.mode')
                ->where('t6.identification_id', '=', Config::get('const.C005.value'))
                ->where('t6.is_deleted', '=', 0);
            })
            ->leftJoin('generalcodes as t7', function ($join) { 
                $join->on('t7.code', '=', 't2.check_result')
                ->where('t7.identification_id', '=', Config::get('const.C018.value'))
                ->where('t7.is_deleted', '=', 0);
            })
            ->leftJoin('generalcodes as t8', function ($join) { 
                $join->on('t8.code', '=', 't2.check_max_time')
                ->where('t8.identification_id', '=', Config::get('const.C018.value'))
                ->where('t8.is_deleted', '=', 0);
            });

        if(!empty($this->param_employment_status)){
            $mainquery->where('t1.employment_status', $this->param_employment_status);  //　雇用形態指定
        }
        if(!empty($this->param_department_code)){
            $mainquery->where('t1.department_code', $this->param_department_code);      //department_code指定
        }
        if(!empty($this->param_user_code)){
            $mainquery->where('t1.code', $this->param_user_code);                       //user_code指定
        } else {
            $mainquery->where('t1.role','<',Config::get('const.C017.out_of_user'));
        }
        $mainquery
            ->JoinSub($subquery3, 't4', function ($join) { 
                $join->on('t4.code', '=', 't1.code');
                $join->on('t4.max_apply_term_from', '=', 't1.apply_term_from');
            });
        $result = $mainquery
            ->where(function ($query) {
                $query->where('t2.check_result', '>', 0)
                      ->orWhere('t2.check_max_time', '>', 0);
            })
            ->where('t1.role', '<', 10)
            ->where('t1.is_deleted', '=', 0)
            ->orderBy('t1.department_code', 'asc')
            ->orderBy('t1.employment_status', 'asc')
            ->orderBy('t1.code', 'asc')
            ->orderBy('t2.record_date', 'asc')
            ->orderBy('t2.record_time', 'asc')
            ->get();
        \Log::debug(
            'sql_debug_log',
            [
                'getAlertData' => \DB::getQueryLog()
            ]
        );

        return $result;
    }

    
}
