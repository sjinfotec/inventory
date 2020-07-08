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
    protected $table_working_time_dates = 'working_time_dates';
    protected $table_calendars = 'calendars';
    protected $table_calendar_setting_informations = 'calendar_setting_informations';
    protected $table_generalcodes = 'generalcodes';
    protected $table_settings = 'settings';
    // protected $guarded = array('id');

    //--------------- メンバー属性 -----------------------------------

    private $id;
    private $user_code;                     // ユーザーコード
    private $department_code;               // 部署コード
    private $record_time;                   // 打刻時間
    private $mode;                          // 打刻モード
    private $user_holiday_kubuns_id;        // ユーザー休暇区分ID
    private $check_result;                  // 打刻チェック結果
    private $check_max_time;                // 打刻回数最大チェック結果
    private $check_interval;                // インターバルチェック結果
    private $is_editor;                     // 編集フラグ
    private $editor_department_code;        // 編集部署コード
    private $editor_user_code;              // 編集ユーザーコード
    private $created_user;                  // 作成ユーザー
    private $updated_user;                  // 修正ユーザー
    private $is_deleted;                    // 削除フラグ
    private $systemdate;
    private $positions;                     // 緯度経度

    // ID
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

    // ユーザー休暇区分ID
    public function getUserholidaykubunsidAttribute()
    {
        return $this->user_holiday_kubuns_id;
    }

    public function setUserholidaykubunsidAttribute($value)
    {
        $this->user_holiday_kubuns_id = $value;
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

    
    // インターバルチェック結果
    public function getCheckintervalAttribute()
    {
        return $this->check_interval;
    }

    public function setCheckintervalAttribute($value)
    {
        $this->check_interval = $value;
    }

    // 編集フラグ
    public function getIseditorAttribute()
    {
        return $this->is_editor;
    }

    public function setIseditorAttribute($value)
    {
        $this->is_editor = $value;
    }

    // 編集部署コード
    public function getEditordepartmentcodeAttribute()
    {
        return $this->editor_department_code;
    }

    public function setEditordepartmentcodeAttribute($value)
    {
        $this->editor_department_code = $value;
    }

    // 編集ユーザーコード
    public function getEditorusercodeAttribute()
    {
        return $this->editor_user_code;
    }

    public function setEditorusercodeAttribute($value)
    {
        $this->editor_user_code = $value;
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

    // 緯度経度
    public function getPositionsAttribute()
    {
        return $this->positions;
    }

    public function setPositionsAttribute($value)
    {
        $this->positions = $value;
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
    }

    public function setParamdatefromNoneditAttribute($value)
    {
        $date = date_create($value);
        $this->param_date_from = $date->format('Y/m/d H:i:s');
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
    }

    public function setParamdatetoNoneditAttribute($value)
    {
        $date = date_create($value);
        $this->param_date_to = $date->format('Y/m/d H:i:s');
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
        try {
            if ($this->check_result == "" || $this->check_result == null) { $this->check_result = 0; }
            if ($this->check_max_time == "" || $this->check_max_time == null) { $this->check_max_time = 0; }
            if ($this->check_interval == "" || $this->check_interval == null) { $this->check_interval = 0; }
            if(isset($this->positions)){
                DB::table($this->table)->insert(
                    [
                        'user_code' => $this->user_code,
                        'department_code' => $this->department_code,
                        'record_time' => $this->record_time,
                        'mode' => $this->mode,
                        'user_holiday_kubuns_id' => $this->user_holiday_kubuns_id,
                        'check_result' => $this->check_result,
                        'check_max_time' => $this->check_max_time,
                        'check_interval' => $this->check_interval,
                        'is_editor' => $this->is_editor,
                        'editor_department_code' => $this->editor_department_code,
                        'editor_user_code' => $this->editor_user_code,
                        'created_user' => $this->created_user,
                        'created_at'=>$this->systemdate,
                        'positions' => DB::raw("(GeomFromText('POINT(".$this->positions.")'))")
                    ]
                );
            }else{
                DB::table($this->table)->insert(
                    [
                        'user_code' => $this->user_code,
                        'department_code' => $this->department_code,
                        'record_time' => $this->record_time,
                        'mode' => $this->mode,
                        'user_holiday_kubuns_id' => $this->user_holiday_kubuns_id,
                        'check_result' => $this->check_result,
                        'check_max_time' => $this->check_max_time,
                        'check_interval' => $this->check_interval,
                        'is_editor' => $this->is_editor,
                        'editor_department_code' => $this->editor_department_code,
                        'editor_user_code' => $this->editor_user_code,
                        'created_user' => $this->created_user,
                        'created_at'=>$this->systemdate
                    ]
                );
            }
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_insert_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_insert_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }


    /**
     * 日次集計取得
     *
     * @return void
     */
    public function getDailyData(){
        try {
            $tasks = DB::table($this->table)
                ->join('users', 'work_times.user_code', '=', 'users.code')
                ->select(
                        'work_times.user_code',
                        'work_times.',
                        'work_times.end_date'
                        )
                ->limit(1)
                ->get();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

        return $tasks;
    }

    /**
     * 打刻モードデータ取得
     *
     * @return void
     */
    public function getModeInfo(){
        try {
            $mainquery = DB::table($this->table.' AS t1')
                ->select(
                    't1.id',
                    't1.record_time',
                    't1.mode',
                    't1.is_editor'
                );
                if(!empty($this->param_department_code)){
                    $mainquery->where('t1.department_code', $this->param_department_code);      //department_code指定
                }
                if(!empty($this->param_user_code)){
                    $mainquery->where('t1.user_code', $this->param_user_code);                  //user_code指定
                }
                if(!empty($this->param_date_from) && !empty($this->param_date_to)){
                    $mainquery->whereBetween('t1.record_time', [$this->param_date_from, $this->param_date_to]);
                }
                if(!empty($this->param_mode)){
                    $mainquery->where('t1.mode', $this->param_mode);                            //mode指定
                }
                $result = $mainquery
                    ->where('t1.is_deleted', '=', 0)
                    ->get();
                return $result;
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

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
     * 日次労働時間取得3
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

        try {
            // 日次労働時間取得SQL作成
            // subquery1    work_times
            $subquery1 = DB::table($this->table)
                ->select(
                    $this->table.'.id as record_datetime_id',
                    $this->table.'.user_code as user_code',
                    $this->table.'.department_code as department_code',
                    $this->table.'.record_time as record_datetime',
                    $this->table.'.mode as mode',
                    $this->table.'.user_holiday_kubuns_id',
                    $this->table.'.check_result as check_result',
                    $this->table.'.check_max_time as check_max_time',
                    $this->table.'.check_interval as check_interval',
                    $this->table.'.is_editor as is_editor',
                    $this->table.'.editor_department_code as editor_department_code',
                    $this->table.'.editor_user_code as editor_user_code',
                    $this->table.'.is_deleted as is_deleted'
                )
                ->selectRaw('DATE_FORMAT(ifnull('.$this->table.".record_time,'".$targetdatefrom."'), '%Y') as record_year")
                ->selectRaw('DATE_FORMAT(ifnull('.$this->table.".record_time,'".$targetdatefrom."'), '%m') as record_month")
                ->selectRaw('DATE_FORMAT(ifnull('.$this->table.".record_time,'".$targetdatefrom."'), '%Y%m%d') as record_date")
                ->selectRaw('DATE_FORMAT('.$this->table.'.record_time'.",'%H%i%s') as record_time")
                ->selectRaw('X(positions) as x_positions')
                ->selectRaw('Y(positions) as y_positions');

            $record_time = $this->getArrayrecordtimeAttribute();
            if(!empty($record_time)){
                $subquery1->where($this->table.'.record_time', '>=', $this->param_date_from);       //record_time範囲指定
                $subquery1->where($this->table.'.record_time', '<=', $this->param_date_to);         //record_time範囲指定
            }
            $subquery1->where($this->table.'.is_deleted', '=', 0);


            // 適用期間日付の取得
            $apicommon = new ApiCommonController();
            // usersの最大適用開始日付subquery
            $subquery3 = $apicommon->getUserApplyTermSubquery($targetdateto);
            // departmentsの最大適用開始日付subquery
            $subquery4 = $apicommon->getDepartmentApplyTermSubquery($targetdateto);
            // working_timetablesの最大適用開始日付subquery
            $subquery5 = $apicommon->getTimetableApplyTermSubquery($targetdateto);

            // mainqueryにsunqueryを組み込む
            // mainquery    users
            // subquery1    work_times
            // subquery2    shift_informations
            $mainquery = DB::table($this->table_users.' AS t1')
                ->select(
                    't2.record_datetime_id as record_datetime_id',
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
                    't2.user_holiday_kubuns_id',
                    't2.check_result as check_result',
                    't2.check_max_time as check_max_time',
                    't2.check_interval as check_interval',
                    't2.is_deleted as is_deleted',
                    't2.x_positions as x_positions',
                    't2.y_positions as y_positions',
                    't9.weekday_kubun as weekday_kubun',
                    't11.code_name as weekday_name',
                    't9.business_kubun as business_kubun',
                    't12.code_name as business_name',
                    't9.holiday_kubun as holiday_kubun',
                    't13.use_free_item as use_free_item',
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
                    't4.interval as interval1',
                    't4.year as year',
                    't9.date as user_working_date',
                    't15.code_name as user_holiday_name',
                    't15.description as user_holiday_description',
                    't2.is_editor as is_editor',
                    't2.editor_department_code as editor_department_code',
                    't2.editor_user_code as editor_user_code',
                    't16.name as editor_department_name',
                    't17.name as editor_user_code_name'
                );
            $mainquery
                ->selectRaw('ifnull(t9.holiday_kubun, 0) as user_holiday_kubun ')
                ->selectRaw('ifnull(t9.working_timetable_no, 0) as working_timetable_no ')
                ->selectRaw("ifnull(t10.name, '') as working_timetable_name ")
                ->selectRaw("ifnull(t10.from_time, '') as working_timetable_from_time ")
                ->selectRaw("ifnull(t10.to_time, '') as working_timetable_to_time ")
                ->selectRaw("ifnull(t10.ago_time_no, 0) as ago_time_no ")
                ->selectRaw("0 as shift_no ")
                ->selectRaw("'' as shift_name ")
                ->selectRaw("'' as shift_from_time ")
                ->selectRaw("'' as shift_to_time ")
                ->leftJoinSub($subquery1, 't2', function ($join) { 
                    $join->on('t2.user_code', '=', 't1.code');
                    $join->on('t2.department_code', '=', 't1.department_code')
                    ->where('t2.is_deleted', '=', 0)
                    ->where('t1.is_deleted', '=', 0);
                })
                ->leftJoin($this->table_calendar_setting_informations.' as t9', function ($join) { 
                    $join->on('t9.date', '=', 't2.record_date');
                    $join->on('t9.department_code', '=', 't1.department_code');
                    $join->on('t9.user_code', '=', 't1.code')
                    ->where('t9.is_deleted', '=', 0)
                    ->where('t1.is_deleted', '=', 0);
                })
                ->leftJoinSub($subquery4, 't5', function ($join) { 
                    $join->on('t5.code', '=', 't1.department_code')
                    ->where('t1.is_deleted', '=', 0);
                })
                ->leftJoin($this->table_settings.' as t4', function ($join) { 
                    $join->on('t4.year', '=', 't2.record_year');
                    $join->on('t4.fiscal_month', '=', 't2.record_month')
                    ->where('t4.is_deleted', '=', 0);
                })
                ->leftJoin($this->table_generalcodes.' as t8', function ($join) { 
                    $join->on('t8.code', '=', 't1.employment_status')
                    ->where('t8.identification_id', '=', Config::get('const.C001.value'))
                    ->where('t1.is_deleted', '=', 0)
                    ->where('t8.is_deleted', '=', 0);
                })
                ->leftJoinSub($subquery5, 't10', function ($join) { 
                    $join->on('t10.no', '=', 't9.working_timetable_no')
                    ->where('t10.working_time_kubun', '=', Config::get('const.C004.regular_working_time'));
                })
                ->leftJoin($this->table_generalcodes.' as t11', function ($join) { 
                    $join->on('t11.code', '=', 't9.weekday_kubun')
                    ->where('t11.identification_id', '=', Config::get('const.C006.value'))
                    ->where('t9.is_deleted', '=', 0)
                    ->where('t11.is_deleted', '=', 0);
                })
                ->leftJoin($this->table_generalcodes.' as t12', function ($join) { 
                    $join->on('t12.code', '=', 't9.business_kubun')
                    ->where('t12.identification_id', '=', Config::get('const.C007.value'))
                    ->where('t9.is_deleted', '=', 0)
                    ->where('t12.is_deleted', '=', 0);
                })
                ->leftJoin($this->table_generalcodes.' as t13', function ($join) { 
                    $join->on('t13.code', '=', 't9.holiday_kubun')
                    ->where('t13.identification_id', '=', Config::get('const.C013.value'))
                    ->where('t9.is_deleted', '=', 0)
                    ->where('t13.is_deleted', '=', 0);
                })
                ->leftJoin($this->table_generalcodes.' as t15', function ($join) { 
                    $join->on('t15.code', '=', 't9.holiday_kubun')
                    ->where('t15.identification_id', '=', Config::get('const.C013.value'))
                    ->where('t9.is_deleted', '=', 0)
                    ->where('t15.is_deleted', '=', 0);
                })
                ->leftJoinSub($subquery4, 't16', function ($join) { 
                    $join->on('t16.code', '=', 't2.editor_department_code');
                })
                ->leftJoin($this->table_users.' as t17', function ($join) { 
                    $join->on('t17.code', '=', 't2.editor_user_code')
                    ->where('t17.is_deleted', '=', 0);
                })
                ->leftJoinSub($subquery3, 't18', function ($join) { 
                    $join->on('t18.code', '=', 't17.code');
                    $join->on('t18.max_apply_term_from', '=', 't17.apply_term_from');
                })
                ->JoinSub($subquery3, 't19', function ($join) { 
                    $join->on('t19.code', '=', 't1.code');
                    $join->on('t19.max_apply_term_from', '=', 't1.apply_term_from');
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
                $mainquery->where('t1.management','<',Config::get('const.C017.admin_user'));
            }
            /*if ($business_kubun != Config::get('const.C007.basic')) {
                $mainquery->whereNotNull('t2.record_datetime');
            }*/
            $result = $mainquery
                ->where('t1.is_deleted', '=', 0)
                ->distinct()
                ->orderBy('t1.department_code', 'asc')
                ->orderBy('t1.employment_status', 'asc')
                ->orderBy('t1.code', 'asc')
                ->orderBy('t2.record_date', 'asc')
                ->orderBy('t2.record_datetime', 'asc')
                ->get();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

        return $result;
    }

    /**
     * 日次労働時間取得2
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
    // public function getWorkTimes($targetdatefrom, $targetdateto, $business_kubun){

    //     try {
    //         // 日次労働時間取得SQL作成
    //         // subquery1    work_times
    //         $subquery1 = DB::table($this->table)
    //             ->select(
    //                 $this->table.'.id as record_datetime_id',
    //                 $this->table.'.user_code as user_code',
    //                 $this->table.'.department_code as department_code',
    //                 $this->table.'.record_time as record_datetime',
    //                 $this->table.'.mode as mode',
    //                 $this->table.'.user_holiday_kubuns_id',
    //                 $this->table.'.check_result as check_result',
    //                 $this->table.'.check_max_time as check_max_time',
    //                 $this->table.'.check_interval as check_interval',
    //                 $this->table.'.is_editor as is_editor',
    //                 $this->table.'.editor_department_code as editor_department_code',
    //                 $this->table.'.editor_user_code as editor_user_code',
    //                 $this->table.'.is_deleted as is_deleted'
    //             )
    //             ->selectRaw('DATE_FORMAT(ifnull('.$this->table.".record_time,'".$targetdatefrom."'), '%Y') as record_year")
    //             ->selectRaw('DATE_FORMAT(ifnull('.$this->table.".record_time,'".$targetdatefrom."'), '%m') as record_month")
    //             ->selectRaw('DATE_FORMAT(ifnull('.$this->table.".record_time,'".$targetdatefrom."'), '%Y%m%d') as record_date")
    //             ->selectRaw('DATE_FORMAT('.$this->table.'.record_time'.",'%H%i%s') as record_time")
    //             ->selectRaw('X(positions) as x_positions')
    //             ->selectRaw('Y(positions) as y_positions');

    //         $record_time = $this->getArrayrecordtimeAttribute();
    //         if(!empty($record_time)){
    //             $subquery1->where($this->table.'.record_time', '>=', $this->param_date_from);       //record_time範囲指定
    //             $subquery1->where($this->table.'.record_time', '<=', $this->param_date_to);         //record_time範囲指定
    //         }
    //         $subquery1->where($this->table.'.is_deleted', '=', 0);


    //         // 適用期間日付の取得
    //         $apicommon = new ApiCommonController();
    //         // usersの最大適用開始日付subquery
    //         $subquery3 = $apicommon->getUserApplyTermSubquery($targetdateto);
    //         // departmentsの最大適用開始日付subquery
    //         $subquery4 = $apicommon->getDepartmentApplyTermSubquery($targetdateto);
    //         // working_timetablesの最大適用開始日付subquery
    //         $subquery5 = $apicommon->getTimetableApplyTermSubquery($targetdateto);

    //         // mainqueryにsunqueryを組み込む
    //         // mainquery    users
    //         // subquery1    work_times
    //         // subquery2    shift_informations
    //         $mainquery = DB::table($this->table_users.' AS t1')
    //             ->select(
    //                 't2.record_datetime_id as record_datetime_id',
    //                 't1.code as user_code',
    //                 't1.name as user_name',
    //                 't1.department_code as department_code',
    //                 't5.name as department_name',
    //                 't2.record_datetime as record_datetime',
    //                 't2.record_year as record_year',
    //                 't2.record_month as record_month',
    //                 't2.record_date as record_date',
    //                 't2.record_time as record_time',
    //                 't1.employment_status as employment_status',
    //                 't8.code_name as employment_status_name',
    //                 't2.mode as mode',
    //                 't2.user_holiday_kubuns_id',
    //                 't2.check_result as check_result',
    //                 't2.check_max_time as check_max_time',
    //                 't2.check_interval as check_interval',
    //                 't2.is_deleted as is_deleted',
    //                 't2.x_positions as x_positions',
    //                 't2.y_positions as y_positions',
    //                 't9.weekday_kubun as weekday_kubun',
    //                 't11.code_name as weekday_name',
    //                 't9.business_kubun as business_kubun',
    //                 't12.code_name as business_name',
    //                 't9.holiday_kubun as holiday_kubun',
    //                 't13.use_free_item as use_free_item',
    //                 't13.code_name as holiday_name',
    //                 't4.closing as closing',
    //                 't4.uplimit_time as uplimit_time',
    //                 't4.statutory_uplimit_time as statutory_uplimit_time',
    //                 't4.time_unit as time_unit',
    //                 't4.time_rounding as time_rounding',
    //                 't4.max_3month_total as max_3month_total',
    //                 't4.max_6month_total as max_6month_total',
    //                 't4.max_12month_total as max_12month_total',
    //                 't4.beginning_month as beginning_month',
    //                 't4.interval as interval1',
    //                 't4.year as year',
    //                 't9.date as user_working_date',
    //                 't15.code_name as user_holiday_name',
    //                 't15.description as user_holiday_description',
    //                 't2.is_editor as is_editor',
    //                 't2.editor_department_code as editor_department_code',
    //                 't2.editor_user_code as editor_user_code',
    //                 't16.name as editor_department_name',
    //                 't17.name as editor_user_code_name'
    //             );
    //         $mainquery
    //             ->selectRaw('ifnull(t9.holiday_kubun, 0) as user_holiday_kubun ')
    //             ->selectRaw('ifnull(t9.working_timetable_no, t6.no) as working_timetable_no ')
    //             ->selectRaw('CASE ifnull(t9.working_timetable_no, 0) WHEN 0 THEN t6.name else t10.name end as working_timetable_name ')
    //             ->selectRaw('CASE ifnull(t9.working_timetable_no, 0) WHEN 0 THEN t6.from_time else t10.from_time end as working_timetable_from_time ')
    //             ->selectRaw('CASE ifnull(t9.working_timetable_no, 0) WHEN 0 THEN t6.to_time else t10.to_time end as working_timetable_to_time ')
    //             ->selectRaw("ifnull(t9.working_timetable_no, 0) as shift_no ")
    //             ->selectRaw("ifnull(t10.name, '') as shift_name ")
    //             ->selectRaw("ifnull(t10.from_time, '') as shift_from_time ")
    //             ->selectRaw("ifnull(t10.to_time, '') as shift_to_time ")
    //             ->leftJoinSub($subquery1, 't2', function ($join) { 
    //                 $join->on('t2.user_code', '=', 't1.code');
    //                 $join->on('t2.department_code', '=', 't1.department_code')
    //                 ->where('t1.is_deleted', '=', 0)
    //                 ->where('t2.is_deleted', '=', 0);
    //             })
    //             ->leftJoin($this->table_calendar_setting_informations.' as t9', function ($join) { 
    //                 $join->on('t9.user_code', '=', 't1.code');
    //                 $join->on('t9.department_code', '=', 't1.department_code');
    //                 $join->on('t9.date', '=', 't2.record_date')
    //                 ->where('t1.is_deleted', '=', 0)
    //                 ->where('t9.is_deleted', '=', 0);
    //             })
    //             ->leftJoinSub($subquery4, 't5', function ($join) { 
    //                 $join->on('t5.code', '=', 't1.department_code')
    //                 ->where('t1.is_deleted', '=', 0);
    //             })
    //             ->leftJoin($this->table_settings.' as t4', function ($join) { 
    //                 $join->on('t4.year', '=', 't2.record_year');
    //                 $join->on('t4.fiscal_month', '=', 't2.record_month')
    //                 ->where('t4.is_deleted', '=', 0);
    //             })
    //             ->leftJoinSub($subquery5, 't6', function ($join) { 
    //                 $join->on('t6.no', '=', 't1.working_timetable_no')
    //                 ->where('t6.working_time_kubun', '=', Config::get('const.C004.regular_working_time'))
    //                 ->where('t1.is_deleted', '=', 0);
    //             })
    //             ->leftJoin($this->table_generalcodes.' as t7', function ($join) { 
    //                 $join->on('t7.code', '=', 't6.working_time_kubun')
    //                 ->where('t7.identification_id', '=', Config::get('const.C004.value'))
    //                 ->where('t7.is_deleted', '=', 0);
    //             })
    //             ->leftJoin($this->table_generalcodes.' as t8', function ($join) { 
    //                 $join->on('t8.code', '=', 't1.employment_status')
    //                 ->where('t8.identification_id', '=', Config::get('const.C001.value'))
    //                 ->where('t1.is_deleted', '=', 0)
    //                 ->where('t8.is_deleted', '=', 0);
    //             })
    //             ->leftJoinSub($subquery5, 't10', function ($join) { 
    //                 $join->on('t10.no', '=', 't9.working_timetable_no')
    //                 ->where('t10.working_time_kubun', '=', Config::get('const.C004.regular_working_time'));
    //             })
    //             ->leftJoin($this->table_generalcodes.' as t11', function ($join) { 
    //                 $join->on('t11.code', '=', 't9.weekday_kubun')
    //                 ->where('t11.identification_id', '=', Config::get('const.C006.value'))
    //                 ->where('t9.is_deleted', '=', 0)
    //                 ->where('t11.is_deleted', '=', 0);
    //             })
    //             ->leftJoin($this->table_generalcodes.' as t12', function ($join) { 
    //                 $join->on('t12.code', '=', 't9.business_kubun')
    //                 ->where('t12.identification_id', '=', Config::get('const.C007.value'))
    //                 ->where('t9.is_deleted', '=', 0)
    //                 ->where('t12.is_deleted', '=', 0);
    //             })
    //             ->leftJoin($this->table_generalcodes.' as t13', function ($join) { 
    //                 $join->on('t13.code', '=', 't9.holiday_kubun')
    //                 ->where('t13.identification_id', '=', Config::get('const.C013.value'))
    //                 ->where('t9.is_deleted', '=', 0)
    //                 ->where('t13.is_deleted', '=', 0);
    //             })
    //             ->leftJoin($this->table_generalcodes.' as t15', function ($join) { 
    //                 $join->on('t15.code', '=', 't9.holiday_kubun')
    //                 ->where('t15.identification_id', '=', Config::get('const.C013.value'))
    //                 ->where('t9.is_deleted', '=', 0)
    //                 ->where('t15.is_deleted', '=', 0);
    //             })
    //             ->leftJoinSub($subquery4, 't16', function ($join) { 
    //                 $join->on('t16.code', '=', 't2.editor_department_code');
    //             })
    //             ->leftJoin($this->table_users.' as t17', function ($join) { 
    //                 $join->on('t17.code', '=', 't2.editor_user_code')
    //                 ->where('t17.is_deleted', '=', 0);
    //             })
    //             ->leftJoinSub($subquery3, 't18', function ($join) { 
    //                 $join->on('t18.code', '=', 't17.code');
    //                 $join->on('t18.max_apply_term_from', '=', 't17.apply_term_from');
    //             })
    //             ->JoinSub($subquery3, 't19', function ($join) { 
    //                 $join->on('t19.code', '=', 't1.code');
    //                 $join->on('t19.max_apply_term_from', '=', 't1.apply_term_from');
    //             });

    //         if(!empty($this->param_employment_status)){
    //             $mainquery->where('t1.employment_status', $this->param_employment_status);  //　雇用形態指定
    //         }
    //         if(!empty($this->param_department_code)){
    //             $mainquery->where('t1.department_code', $this->param_department_code);      //department_code指定
    //         }
    //         if(!empty($this->param_user_code)){
    //             $mainquery->where('t1.code', $this->param_user_code);                       //user_code指定
    //         } else {
    //             $mainquery->where('t1.management','<',Config::get('const.C017.admin_user'));
    //         }
    //         /*if ($business_kubun != Config::get('const.C007.basic')) {
    //             $mainquery->whereNotNull('t2.record_datetime');
    //         }*/
    //         $result = $mainquery
    //             ->where('t1.is_deleted', '=', 0)
    //             ->distinct()
    //             ->orderBy('t1.department_code', 'asc')
    //             ->orderBy('t1.employment_status', 'asc')
    //             ->orderBy('t1.code', 'asc')
    //             ->orderBy('t2.record_date', 'asc')
    //             ->orderBy('t2.record_datetime', 'asc')
    //             ->get();
    //     }catch(\PDOException $pe){
    //         Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
    //         Log::error($pe->getMessage());
    //         throw $pe;
    //     }catch(\Exception $e){
    //         Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
    //         Log::error($e->getMessage());
    //         throw $e;
    //     }

    //     return $result;
    // }

    /**
     * 日次労働時間取得1
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
    // public function getWorkTimes($targetdatefrom, $targetdateto, $business_kubun){

    //     try {
    //         // 日次労働時間取得SQL作成
    //         // subquery1    work_times
    //         $subquery1 = DB::table($this->table)
    //             ->select(
    //                 $this->table.'.id as record_datetime_id',
    //                 $this->table.'.user_code as user_code',
    //                 $this->table.'.department_code as department_code',
    //                 $this->table.'.record_time as record_datetime',
    //                 $this->table.'.mode as mode',
    //                 $this->table.'.user_holiday_kubuns_id',
    //                 $this->table.'.check_result as check_result',
    //                 $this->table.'.check_max_time as check_max_time',
    //                 $this->table.'.check_interval as check_interval',
    //                 $this->table.'.is_editor as is_editor',
    //                 $this->table.'.editor_department_code as editor_department_code',
    //                 $this->table.'.editor_user_code as editor_user_code',
    //                 $this->table.'.is_deleted as is_deleted'
    //             )
    //             ->selectRaw('DATE_FORMAT(ifnull('.$this->table.".record_time,'".$targetdatefrom."'), '%Y') as record_year")
    //             ->selectRaw('DATE_FORMAT(ifnull('.$this->table.".record_time,'".$targetdatefrom."'), '%m') as record_month")
    //             ->selectRaw('DATE_FORMAT(ifnull('.$this->table.".record_time,'".$targetdatefrom."'), '%Y%m%d') as record_date")
    //             ->selectRaw('DATE_FORMAT('.$this->table.'.record_time'.",'%H%i%s') as record_time")
    //             ->selectRaw('X(positions) as x_positions')
    //             ->selectRaw('Y(positions) as y_positions');

    //         $record_time = $this->getArrayrecordtimeAttribute();
    //         if(!empty($record_time)){
    //             $subquery1->where($this->table.'.record_time', '>=', $this->param_date_from);       //record_time範囲指定
    //             $subquery1->where($this->table.'.record_time', '<=', $this->param_date_to);         //record_time範囲指定
    //         }
    //         $subquery1->where($this->table.'.is_deleted', '=', 0);

    //         // subquery2    shift_informations
    //         $subquery2 = DB::table($this->table_shift_informations)
    //             ->select(
    //                 $this->table_shift_informations.'.user_code as user_code',
    //                 $this->table_shift_informations.'.department_code as department_code',
    //                 $this->table_shift_informations.'.working_timetable_no as shift_no',
    //                 $this->table_shift_informations.'.target_date as target_date',
    //                 $this->table_shift_informations.'.is_deleted as is_deleted'
    //             );
    //         $subquery2->where($this->table_shift_informations.'.is_deleted', '=', 0);

    //         // 適用期間日付の取得
    //         $apicommon = new ApiCommonController();
    //         // usersの最大適用開始日付subquery
    //         $subquery3 = $apicommon->getUserApplyTermSubquery($targetdateto);
    //         // departmentsの最大適用開始日付subquery
    //         $subquery4 = $apicommon->getDepartmentApplyTermSubquery($targetdateto);
    //         // working_timetablesの最大適用開始日付subquery
    //         $subquery5 = $apicommon->getTimetableApplyTermSubquery($targetdateto);

    //         // mainqueryにsunqueryを組み込む
    //         // mainquery    users
    //         // subquery1    work_times
    //         // subquery2    shift_informations
    //         $mainquery = DB::table($this->table_users.' AS t1')
    //             ->select(
    //                 't2.record_datetime_id as record_datetime_id',
    //                 't1.code as user_code',
    //                 't1.name as user_name',
    //                 't1.department_code as department_code',
    //                 't5.name as department_name',
    //                 't2.record_datetime as record_datetime',
    //                 't2.record_year as record_year',
    //                 't2.record_month as record_month',
    //                 't2.record_date as record_date',
    //                 't2.record_time as record_time',
    //                 't1.employment_status as employment_status',
    //                 't8.code_name as employment_status_name',
    //                 't2.mode as mode',
    //                 't2.user_holiday_kubuns_id',
    //                 't2.check_result as check_result',
    //                 't2.check_max_time as check_max_time',
    //                 't2.check_interval as check_interval',
    //                 't2.is_deleted as is_deleted',
    //                 't2.x_positions as x_positions',
    //                 't2.y_positions as y_positions',
    //                 't3.weekday_kubun as weekday_kubun',
    //                 't11.code_name as weekday_name',
    //                 't3.business_kubun as business_kubun',
    //                 't12.code_name as business_name',
    //                 't3.holiday_kubun as holiday_kubun',
    //                 't13.use_free_item as use_free_item',
    //                 't13.code_name as holiday_name',
    //                 't4.closing as closing',
    //                 't4.uplimit_time as uplimit_time',
    //                 't4.statutory_uplimit_time as statutory_uplimit_time',
    //                 't4.time_unit as time_unit',
    //                 't4.time_rounding as time_rounding',
    //                 't4.max_3month_total as max_3month_total',
    //                 't4.max_6month_total as max_6month_total',
    //                 't4.max_12month_total as max_12month_total',
    //                 't4.beginning_month as beginning_month',
    //                 't4.interval as interval',
    //                 't4.year as year',
    //                 't14.holiday_kubun as user_holiday_kubun',
    //                 't14.working_date as user_working_date',
    //                 't15.code_name as user_holiday_name',
    //                 't15.description as user_holiday_description',
    //                 't2.is_editor as is_editor',
    //                 't2.editor_department_code as editor_department_code',
    //                 't2.editor_user_code as editor_user_code',
    //                 't16.name as editor_department_name',
    //                 't17.name as editor_user_code_name'
    //             );
    //         $mainquery
    //             ->selectRaw('ifnull(t9.shift_no, t6.no) as working_timetable_no ')
    //             ->selectRaw('CASE ifnull(t9.shift_no, 0) WHEN 0 THEN t6.name else t10.name end as working_timetable_name ')
    //             ->selectRaw('CASE ifnull(t9.shift_no, 0) WHEN 0 THEN t6.from_time else t10.from_time end as working_timetable_from_time ')
    //             ->selectRaw('CASE ifnull(t9.shift_no, 0) WHEN 0 THEN t6.to_time else t10.to_time end as working_timetable_to_time ')
    //             ->selectRaw("ifnull(t9.shift_no, 0) as shift_no ")
    //             ->selectRaw("ifnull(t10.name, '') as shift_name ")
    //             ->selectRaw("ifnull(t10.from_time, '') as shift_from_time ")
    //             ->selectRaw("ifnull(t10.to_time, '') as shift_to_time ")
    //             ->leftJoinSub($subquery1, 't2', function ($join) { 
    //                 $join->on('t2.user_code', '=', 't1.code');
    //                 $join->on('t2.department_code', '=', 't1.department_code')
    //                 ->where('t1.is_deleted', '=', 0)
    //                 ->where('t2.is_deleted', '=', 0);
    //             })
    //             ->leftJoinSub($subquery2, 't9', function ($join) { 
    //                 $join->on('t9.user_code', '=', 't1.code');
    //                 $join->on('t9.department_code', '=', 't1.department_code');
    //                 $join->on('t9.target_date', '=', 't2.record_date')
    //                 ->where('t1.is_deleted', '=', 0)
    //                 ->where('t9.is_deleted', '=', 0);
    //             })
    //             ->leftJoinSub($subquery4, 't5', function ($join) { 
    //                 $join->on('t5.code', '=', 't1.department_code')
    //                 ->where('t1.is_deleted', '=', 0);
    //             })
    //             ->leftJoin($this->table_calendars.' as t3', function ($join) use($targetdateto) { 
    //                 $join->on('t3.department_code', '=', 't1.department_code');
    //                 $join->on('t3.user_code', '=', 't1.code')
    //                 // $join->on('t3.date', '=', 't2.record_date')
    //                 ->where('t3.date', '=', $targetdateto)
    //                 ->where('t3.is_deleted', '=', 0);
    //             })
    //             ->leftJoin($this->table_settings.' as t4', function ($join) { 
    //                 $join->on('t4.year', '=', 't2.record_year');
    //                 $join->on('t4.fiscal_month', '=', 't2.record_month')
    //                 ->where('t4.is_deleted', '=', 0);
    //             })
    //             ->leftJoinSub($subquery5, 't6', function ($join) { 
    //                 $join->on('t6.no', '=', 't1.working_timetable_no')
    //                 ->where('t6.working_time_kubun', '=', Config::get('const.C004.regular_working_time'))
    //                 ->where('t1.is_deleted', '=', 0);
    //             })
    //             ->leftJoin($this->table_generalcodes.' as t7', function ($join) { 
    //                 $join->on('t7.code', '=', 't6.working_time_kubun')
    //                 ->where('t7.identification_id', '=', Config::get('const.C004.value'))
    //                 ->where('t7.is_deleted', '=', 0);
    //             })
    //             ->leftJoin($this->table_generalcodes.' as t8', function ($join) { 
    //                 $join->on('t8.code', '=', 't1.employment_status')
    //                 ->where('t8.identification_id', '=', Config::get('const.C001.value'))
    //                 ->where('t1.is_deleted', '=', 0)
    //                 ->where('t8.is_deleted', '=', 0);
    //             })
    //             ->leftJoinSub($subquery5, 't10', function ($join) { 
    //                 $join->on('t10.no', '=', 't9.shift_no')
    //                 ->where('t10.working_time_kubun', '=', Config::get('const.C004.regular_working_time'));
    //             })
    //             ->leftJoin($this->table_generalcodes.' as t11', function ($join) { 
    //                 $join->on('t11.code', '=', 't3.weekday_kubun')
    //                 ->where('t11.identification_id', '=', Config::get('const.C006.value'))
    //                 ->where('t3.is_deleted', '=', 0)
    //                 ->where('t11.is_deleted', '=', 0);
    //             })
    //             ->leftJoin($this->table_generalcodes.' as t12', function ($join) { 
    //                 $join->on('t12.code', '=', 't3.business_kubun')
    //                 ->where('t12.identification_id', '=', Config::get('const.C007.value'))
    //                 ->where('t3.is_deleted', '=', 0)
    //                 ->where('t12.is_deleted', '=', 0);
    //             })
    //             ->leftJoin($this->table_generalcodes.' as t13', function ($join) { 
    //                 $join->on('t13.code', '=', 't3.holiday_kubun')
    //                 ->where('t13.identification_id', '=', Config::get('const.C013.value'))
    //                 ->where('t3.is_deleted', '=', 0)
    //                 ->where('t13.is_deleted', '=', 0);
    //             })
    //             ->leftJoin($this->table_user_holiday_kubuns.' as t14', function ($join) { 
    //                 $join->on('t14.working_date', '=', 't2.record_date');
    //                 $join->on('t14.user_code', '=', 't1.code');
    //                 $join->on('t14.department_code', '=', 't1.department_code')
    //                 ->where('t1.is_deleted', '=', 0)
    //                 ->where('t14.is_deleted', '=', 0);
    //             })
    //             ->leftJoin($this->table_generalcodes.' as t15', function ($join) { 
    //                 $join->on('t15.code', '=', 't14.holiday_kubun')
    //                 ->where('t15.identification_id', '=', Config::get('const.C013.value'))
    //                 ->where('t14.is_deleted', '=', 0)
    //                 ->where('t15.is_deleted', '=', 0);
    //             })
    //             ->leftJoinSub($subquery4, 't16', function ($join) { 
    //                 $join->on('t16.code', '=', 't2.editor_department_code');
    //             })
    //             ->leftJoin($this->table_users.' as t17', function ($join) { 
    //                 $join->on('t17.code', '=', 't2.editor_user_code')
    //                 ->where('t17.is_deleted', '=', 0);
    //             })
    //             ->leftJoinSub($subquery3, 't18', function ($join) { 
    //                 $join->on('t18.code', '=', 't17.code');
    //                 $join->on('t18.max_apply_term_from', '=', 't17.apply_term_from');
    //             })
    //             ->JoinSub($subquery3, 't19', function ($join) { 
    //                 $join->on('t19.code', '=', 't1.code');
    //                 $join->on('t19.max_apply_term_from', '=', 't1.apply_term_from');
    //             });

    //         if(!empty($this->param_employment_status)){
    //             $mainquery->where('t1.employment_status', $this->param_employment_status);  //　雇用形態指定
    //         }
    //         if(!empty($this->param_department_code)){
    //             $mainquery->where('t1.department_code', $this->param_department_code);      //department_code指定
    //         }
    //         if(!empty($this->param_user_code)){
    //             $mainquery->where('t1.code', $this->param_user_code);                       //user_code指定
    //         } else {
    //             $mainquery->where('t1.management','<',Config::get('const.C017.admin_user'));
    //         }
    //         /*if ($business_kubun != Config::get('const.C007.basic')) {
    //             $mainquery->whereNotNull('t2.record_datetime');
    //         }*/
    //         $result = $mainquery
    //             ->where('t1.is_deleted', '=', 0)
    //             ->distinct()
    //             ->orderBy('t1.department_code', 'asc')
    //             ->orderBy('t1.employment_status', 'asc')
    //             ->orderBy('t1.code', 'asc')
    //             ->orderBy('t2.record_date', 'asc')
    //             ->orderBy('t2.record_datetime', 'asc')
    //             ->get();
    //     }catch(\PDOException $pe){
    //         Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
    //         Log::error($pe->getMessage());
    //         throw $pe;
    //     }catch(\Exception $e){
    //         Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
    //         Log::error($e->getMessage());
    //         throw $e;
    //     }

    //     return $result;
    // }

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
                
            // $subquery2 = DB::table($this->table_user_holiday_kubuns)
            //     ->select(
            //         $this->table_user_holiday_kubuns.'.working_date',
            //         $this->table_user_holiday_kubuns.'.user_code',
            //         $this->table_user_holiday_kubuns.'.department_code')
            //     ->selectRaw('ifnull('.$this->table_user_holiday_kubuns.'.holiday_kubun, 0) as holiday_kubun')
            //     ->where($this->table_user_holiday_kubuns.'.is_deleted', '=', 0);
            $subquery2 = DB::table($this->table_calendar_setting_informations)
                ->select(
                    $this->table_calendar_setting_informations.'.date',
                    $this->table_calendar_setting_informations.'.user_code',
                    $this->table_calendar_setting_informations.'.department_code')
                ->selectRaw('ifnull('.$this->table_calendar_setting_informations.'.holiday_kubun, 0) as holiday_kubun')
                ->where($this->table_calendar_setting_informations.'.is_deleted', '=', 0);

            $subquery11 = $subquery1->toSql();

            $subquery12 = DB::table(DB::raw('('.$subquery11.') AS t1'))
                ->select(
                    't1.user_code',
                    't1.department_code',
                    't1.record_date',
                    't1.record_time',
                    't2.date')
                ->leftJoinSub($subquery2, 't2', function ($join) { 
                    $join->on('t2.date', '=', 't1.record_date');
                    $join->on('t2.user_code', '=', 't1.user_code');
                    $join->on('t2.department_code', '=', 't1.department_code')
                    ->where('t2.holiday_kubun', '>', 0);
                });
                // ->whereNull('t2.working_date');      休日出勤の場合はあるためコメントアウト

            $subquery = $subquery12->toSql();

            $subquery_max = DB::table(DB::raw('('.$subquery.') AS t3'))
                ->select(
                    't3.user_code',
                    't3.department_code')
                ->selectRaw('max(t3.record_time) as max_record_datetime')
                ->where('t3.user_code', '=', $this->param_user_code)
                ->where('t3.department_code', '=', $this->param_department_code)
                ->where('t3.record_time', '<', $this->param_start_date)
                ->groupBy('t3.user_code', 't3.department_code');

            $subquery_max->setBindings([
                0,
                0,
                0,
                $this->param_user_code,
                $this->param_department_code,
                $this->param_start_date]);
    
            $mainquery  = DB::table($this->table)
                ->select(
                    $this->table.'.mode as mode',
                    $this->table.'.user_holiday_kubuns_id as user_holiday_kubuns_id',
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
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
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

            // $subquery2 = DB::table($this->table_user_holiday_kubuns)
            //     ->select(
            //         $this->table_user_holiday_kubuns.'.working_date',
            //         $this->table_user_holiday_kubuns.'.user_code',
            //         $this->table_user_holiday_kubuns.'.department_code')
            //     ->selectRaw('ifnull('.$this->table_user_holiday_kubuns.'.holiday_kubun, 0) as holiday_kubun')
            //     ->where($this->table_user_holiday_kubuns.'.is_deleted', '=', 0);
            $subquery2 = DB::table($this->table_calendar_setting_informations)
                ->select(
                    $this->table_calendar_setting_informations.'.date',
                    $this->table_calendar_setting_informations.'.user_code',
                    $this->table_calendar_setting_informations.'.department_code')
                ->selectRaw('ifnull('.$this->table_calendar_setting_informations.'.holiday_kubun, 0) as holiday_kubun')
                ->where($this->table_calendar_setting_informations.'.is_deleted', '=', 0);

            $subquery11 = $subquery1->toSql();

            $subquery12 = DB::table(DB::raw('('.$subquery11.') AS t1'))
                ->select(
                    't1.user_code',
                    't1.department_code',
                    't1.record_date',
                    't1.record_time',
                    't2.date')
                ->leftJoinSub($subquery2, 't2', function ($join) { 
                    $join->on('t2.date', '=', 't1.record_date');
                    $join->on('t2.user_code', '=', 't1.user_code');
                    $join->on('t2.department_code', '=', 't1.department_code')
                    ->where('t2.holiday_kubun', '>', 0);
                });
                // ->whereNull('t2.working_date');      休日出勤の場合はあるためコメントアウト

            $subquery = $subquery12->toSql();

            $subquery_max = DB::table(DB::raw('('.$subquery.') AS t3'))
                ->select(
                    't3.user_code',
                    't3.department_code')
                ->selectRaw('min(t3.record_time) as min_record_datetime')
                ->where('t3.user_code', '=', $this->param_user_code)
                ->where('t3.department_code', '=', $this->param_department_code)
                ->where('t3.record_time', '>', $this->param_start_date)
                ->groupBy('t3.user_code', 't3.department_code');

            $subquery_max->setBindings([
                0,
                0,
                0,
                $this->param_user_code,
                $this->param_department_code,
                $this->param_start_date]);
    
            $mainquery  = DB::table($this->table)
                ->select(
                    $this->table.'.mode as mode',
                    $this->table.'.user_holiday_kubuns_id as user_holiday_kubuns_id',
                    $this->table.'.record_time as record_datetime')
                ->JoinSub($subquery_max, 't3', function ($join) { 
                    $join->on('t3.user_code', '=', $this->table.'.user_code');
                    $join->on('t3.department_code', '=', $this->table.'.department_code');
                    $join->on('t3.min_record_datetime', '=', $this->table.'.record_time');
                })
                ->where($this->table.'.is_deleted', '=', 0);

            $mainquery->setBindings([0]);

            $result = $mainquery->get();
                
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
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
        try {
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
                ->where($this->table.'.record_time', '<', $this->param_date_from);
            if (!empty($this->param_mode)) {
                $sunquery1
                    ->where($this->table.'.mode', '=', $this->param_mode);
            }
            $sunquery1
                ->groupBy($this->table.'.user_code', $this->table.'.department_code', $this->table.'.is_deleted');

            // mainqueryにsunqueryを組み込む
            // sunquery1    t1:work_times
            $mainquery = DB::table($this->table.' AS t1')
                ->select(
                    't1.id as id',
                    't1.mode as mode',
                    't1.user_holiday_kubuns_id as user_holiday_kubuns_id',
                    't1.record_time as record_datetime',
                    't1.is_editor as is_editor'
                );
            $mainquery                
                ->selectRaw("DATE_FORMAT(t1.record_time,'%Y%m%d') as record_ymd");
            $mainquery                
                ->JoinSub($sunquery1, 't2', function ($join) { 
                    $join->on('t2.user_code', '=', 't1.user_code');
                    $join->on('t2.department_code', '=', 't1.department_code');
                    $join->on('t2.max_record_time', '=', 't1.record_time')
                    ->where('t2.is_deleted', '=', 0);
                })
                ->where('t1.is_deleted', '=', 0);
            $result = $mainquery->get();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }


        return $result;
    }

    /**
     * ユーザーの勤務時間取得
     *
     * @return $data
     */
    public function getUserDetails(){
        try {
            $apicommon = new ApiCommonController();
            $sqlString = "";
            $sqlString .= "select ";
            $sqlString .= "  t1.id as id ";
            $sqlString .= "  , t1.user_code as user_code ";
            $sqlString .= "  , t1.department_code as department_code ";
            $sqlString .= "  , t1.record_time as record_time ";
            $sqlString .= "  , t1.mode as mode ";
            $sqlString .= "  , t1.user_holiday_kubuns_id as user_holiday_kubuns_id ";
            $sqlString .= "  , t1.user_name as user_name ";
            $sqlString .= "  , t1.department_name as department_name ";
            $sqlString .= "  , t1.code_name as code_name ";
            $sqlString .= "  , t2.holiday_kubun as user_holiday_kbn ";
            $sqlString .= "  , t1.record_ymd as record_ymd ";
            $sqlString .= "  , t1.record_date as record_date ";
            $sqlString .= "  , t1.date as date ";
            $sqlString .= "  , t1.time as time ";
            $sqlString .= "  , t1.x_positions as x_positions ";
            $sqlString .= "  , t1.y_positions as y_positions ";
            $sqlString .= "  , case ifnull(t2.holiday_kubun, 0) ";
            $sqlString .= "      when ? then ?  ";
            $sqlString .= "      else ?  ";
            $sqlString .= "    end as kbn_flag ";
            $sqlString .= "from ( ";
            $sqlString .= "  select ";
            $sqlString .= "    t10.id as id ";
            $sqlString .= "    , t10.user_code as user_code ";
            $sqlString .= "    , t10.department_code as department_code ";
            $sqlString .= "    , t10.record_time as record_time ";
            $sqlString .= "    , t10.mode as mode ";
            $sqlString .= "    , t10.user_holiday_kubuns_id as user_holiday_kubuns_id ";
            $sqlString .= "    , t11.name as user_name ";
            $sqlString .= "    , t12.name as department_name ";
            $sqlString .= "    , t13.code_name as code_name ";
            $sqlString .= "    , DATE_FORMAT(t10.record_time, '%Y%m%d') as record_ymd ";
            $sqlString .= "    , DATE_FORMAT(t10.record_time, '%Y年%m月%d日') as record_date ";
            $sqlString .= "    , DATE_FORMAT(t10.record_time, '%Y/%m/%d') as date ";
            $sqlString .= "    , DATE_FORMAT(t10.record_time, '%H:%i') as time ";
            $sqlString .= "    , X(t10.positions) as x_positions ";
            $sqlString .= "    , Y(t10.positions) as y_positions ";
            $sqlString .= "  from ";
            $sqlString .= "  ".$this->table." as t10 ";
            $sqlString .= "    left join ".$this->table_users." as t11 ";
            $sqlString .= "    on ";
            $sqlString .= "      t11.code = t10.user_code ";
            $sqlString .= "      and t11.department_code = t10.department_code ";
            $sqlString .= "      and t11.is_deleted = ? ";
            $sqlString .= "    inner join ( ";
            $sqlString .= "      ".$apicommon->makeUserApplyTermSql($this->param_start_date, Config::get('const.C025.admin_user'))." ";
            $sqlString .= "    ) as t14 ";
            $sqlString .= "    on ";
            $sqlString .= "      t14.code = t11.code ";
            $sqlString .= "      and t14.max_apply_term_from = t11.apply_term_from ";
            $sqlString .= "    inner join ( ";
            $sqlString .= "      ".$apicommon->makeDepartmentApplyTermSql($this->param_start_date, $this->param_start_date)." ";
            $sqlString .= "    ) as t12 ";
            $sqlString .= "    on ";
            $sqlString .= "      t12.code = t11.department_code ";
            $sqlString .= "    left join ".$this->table_generalcodes." as t13 ";
            $sqlString .= "    on ";
            $sqlString .= "      t13.code = t10.mode ";
            $sqlString .= "      and t13.identification_id = ? ";
            $sqlString .= "      and t13.is_deleted = ? ";
            $sqlString .= "  where ";
            $sqlString .= "    ? = ? ";
            if (!empty($this->param_user_code)) {
                $sqlString .= "    and t10.user_code = ? ";
            }
            if (!empty($this->param_department_code)) {
                $sqlString .= "    and t10.department_code = ? ";
            }
            $sqlString .= "    and t10.record_time between ? and ? ";
            $sqlString .= "    and t10.is_deleted = ? ";
            $sqlString .= "  ) t1 ";
            $sqlString .= "  inner join ";
            $sqlString .= "    ".$this->table_calendar_setting_informations." as t2 ";
            $sqlString .= "    on ";
            $sqlString .= "      t2.date = t1.record_ymd ";
            $sqlString .= "      and t2.department_code = t1.department_code ";
            $sqlString .= "      and t2.user_code = t1.user_code ";
            $sqlString .= "      and t2.is_deleted = ? ";
            $sqlString .= "order by ";
            $sqlString .= "  t1.record_time asc ";

            // バインド
            $dt = null;
            if (isset($param_start_date)) {
                $dt = new Carbon($param_start_date);
            } else {
                $dt = new Carbon();
            }
            $target_start_date = $dt->format('Ymd');
            $dt = null;
            if (isset($param_end_date)) {
                $dt = new Carbon($param_end_date);
            } else {
                $dt = new Carbon();
            }
            $target_end_date = $dt->format('Ymd');

            $array_setBindingsStr = array();
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = $target_end_date;
            $array_setBindingsStr[] = Config::get('const.C025.admin_user');
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = $target_end_date;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = $target_end_date;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = Config::get('const.C005.value');
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            if (!empty($this->param_user_code)) {
                $array_setBindingsStr[] = $this->param_user_code;
            }
            if (!empty($this->param_department_code)) {
                $array_setBindingsStr[] = $this->param_department_code;
            }
            $array_setBindingsStr[] = $this->param_start_date;
            $array_setBindingsStr[] = $this->param_end_date;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 0;

            $result = DB::select($sqlString, $array_setBindingsStr);
            return $result;
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
        
    }

    /**
     * ユーザーの勤務時間取得
     *
     * @return $data
     */
    // public function getUserDetails(){
    //     try {
    //         // 適用期間日付の取得
    //         $apicommon = new ApiCommonController();
    //         // usersの最大適用開始日付subquery
    //         $subquery3 = $apicommon->getUserApplyTermSubquery($this->param_start_date);
    //         // departmentsの最大適用開始日付subquery
    //         $subquery4 = $apicommon->getDepartmentApplyTermSubquery($this->param_start_date);
    //         // working_timetablesの最大適用開始日付subquery
    //         $subquery5 = $apicommon->getTimetableApplyTermSubquery($this->param_start_date);
    //         // users max(apply_term_from)
    //         $dt = new Carbon($this->param_start_date);
    //         $target_date = $dt->format('Ymd');
        
    //         $data = DB::table($this->table.' as t1')
    //             ->leftJoin($this->table_users.' as t2', function ($join) { 
    //                 $join->on('t2.code', '=', 't1.user_code');
    //                 $join->on('t2.department_code', '=', 't1.department_code')
    //                 ->where('t2.is_deleted', '=', 0);
    //             })
    //             ->JoinSub($subquery3, 't3', function ($join) { 
    //                 $join->on('t3.code', '=', 't2.code');
    //                 $join->on('t3.max_apply_term_from', '=', 't2.apply_term_from');
    //             })
    //             ->JoinSub($subquery4, 't4', function ($join) { 
    //                 $join->on('t4.code', '=', 't1.department_code');
    //             })
    //             ->leftJoin($this->table_generalcodes.' as t5', function ($join) { 
    //                 $join->on('t5.code', '=','t1.mode')
    //                 ->where('t5.identification_id', '=', Config::get('const.C005.value'));
    //             })
    //             ->leftJoin($this->table_user_holiday_kubuns.' as t6', function ($join) { 
    //                 $join->on('t6.id', '=','t1.user_holiday_kubuns_id')
    //                 ->where('t6.is_deleted', '=', 0);
    //             })
    //             ->select(
    //                 't1.id',
    //                 't1.user_code',
    //                 't1.department_code',
    //                 't1.record_time',
    //                 't1.mode',
    //                 't1.user_holiday_kubuns_id as user_holiday_kubuns_id',
    //                 't2.name as user_name',
    //                 't4.name as department_name',
    //                 't5.code_name',
    //                 't6.holiday_kubun as user_holiday_kbn'
    //             )
    //             ->selectRaw("DATE_FORMAT(t1.record_time,'%Y%m%d') as record_ymd")
    //             ->selectRaw("DATE_FORMAT(t1.record_time,'%Y年%m月%d日') as record_date")
    //             ->selectRaw("DATE_FORMAT(t1.record_time,'%Y/%m/%d') as date")
    //             ->selectRaw("DATE_FORMAT(t1.record_time,'%H:%i') as time")
    //             ->selectRaw('X(t1.positions) as x_positions')
    //             ->selectRaw('Y(t1.positions) as y_positions')
    //             ->selectRaw("case ifnull(t6.holiday_kubun,0) when 0 then 0 else 1 end as kbn_flag")
    //             ->where('t1.user_code', $this->user_code)
    //             ->whereBetween('t1.record_time', [$this->param_start_date,$this->param_end_date])
    //             ->where('t1.is_deleted', 0)
    //             ->orderBy('t1.record_time_', 'asc')
    //             ->get();
    //     }catch(\PDOException $pe){
    //         Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
    //         Log::error($pe->getMessage());
    //         throw $pe;
    //     }catch(\Exception $e){
    //         Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
    //         Log::error($e->getMessage());
    //         throw $e;
    //     }
        
    //     return $data;
    // }

    /**
     * 論理削除（編集）
     *
     * @return void
     */
    public function delWorkTime(){
        try {
            DB::table($this->table)
                ->where('id', $this->id)
                ->where('is_deleted', 0)
                ->update([
                    'is_editor' => 1,
                    'editor_department_code' => $this->editor_department_code,
                    'editor_user_code' => $this->editor_user_code,
                    'is_deleted' => 1,
                    'updated_user' => $this->updated_user,
                    'updated_at' => $this->systemdate
                    ]);
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_update_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_update_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 論理削除
     *
     * @return void
     */
    public function delWorkTimeBysystem(){
        try {
            DB::table($this->table)
                ->where('id', $this->id)
                ->where('is_deleted', 0)
                ->update([
                    'is_deleted' => 1,
                    'updated_user' => 'system',
                    'updated_at' => $this->systemdate
                    ]);
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_update_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_update_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * モード回数取得
     *
     * @return void
     */
    public function getModeCount(){
        try {
            $users = DB::table($this->table)
                ->where($this->table.'.user_code', '=', $this->param_user_code)
                ->where($this->table.'.department_code', '=', $this->param_department_code)
                ->where($this->table.'.record_time', '<=', $this->param_date_from)
                ->where($this->table.'.mode', '=', $this->param_mode)
                ->where('is_deleted', 0)
                ->limit(5)
                ->count();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_count_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_count_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

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
        // if (Config::get('const.DEBUG_LEVEL') == Config::get('const.DEBUG_LEVEL_VALUE.DEBUG')) { \DB::enableQueryLog(); }

        try {
            // 日次労働時間取得SQL作成
            // subquery1    work_times
            $subquery1 = DB::table($this->table)
                ->selectRaw(
                    'DATE_FORMAT('.$this->table.".record_time, '%Y%m%d') as record_date ");
            $subquery1
                ->addselect($this->table.'.user_code as user_code')
                ->addselect($this->table.'.department_code as department_code')
                ->addselect($this->table.'.record_time as record_time')
                ->addselect($this->table.'.mode as mode')
                ->addselect($this->table.'.user_holiday_kubuns_id as user_holiday_kubuns_id');
            $subquery1
                ->selectRaw('case check_result when null then 0 else check_result end as check_result')
                ->selectRaw('case check_max_time when null then 0 else check_max_time end as check_max_time')
                ->selectRaw('case check_interval when null then 0 else check_interval end as check_interval');
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
                    't2.user_holiday_kubuns_id as user_holiday_kubuns_id',
                    't2.check_result as check_result',
                    't2.check_max_time as check_max_time',
                    't2.check_interval as unused_check_interval',
                    't9.check_interval as check_interval',
                    't9.note as note',
                    't10.code_name as check_interval_name')
                ->selectRaw(
                    "concat(DATE_FORMAT(t2.record_time,'%Y年%m月%d日'),'(',substring('月火水木金土日',convert(t11.weekday_kubun+1,char),1),')') as record_date_name ");
            $alert_memo = '';
            $alert_memo .= "case ifnull(t7.code_name,'') ";
            $alert_memo .= "  when '' then ";
            $alert_memo .= "    case ifnull(t8.code_name ,'')  ";
            $alert_memo .= "      when '' then ltrim(concat('',' ',t9.note)) ";
            $alert_memo .= "      else ltrim(concat(t8.code_name, ' ', t9.note)) ";
            $alert_memo .= "    end ";
            $alert_memo .= "  else ";
            $alert_memo .= "    case ifnull(t8.code_name ,'') ";
            $alert_memo .= "      when '' then ltrim(concat(t7.code_name, ' ', t9.note)) ";
            $alert_memo .= "      else ltrim(concat(t7.code_name, ' ', t8.code_name, ' ', t9.note)) ";
            $alert_memo .= "    end ";
            $alert_memo .= "end as alert_memo";
            $mainquery
                ->selectRaw($alert_memo)
                ->leftJoinSub($subquery1, 't2', function ($join) { 
                    $join->on('t2.user_code', '=', 't1.code');
                    $join->on('t2.department_code', '=', 't1.department_code')
                    ->where('t1.is_deleted', '=', 0);
                })
                ->leftJoinSub($subquery4, 't3', function ($join) { 
                    $join->on('t3.code', '=', 't1.department_code')
                    ->where('t1.is_deleted', '=', 0);
                })
                ->leftJoin($this->table_generalcodes.' as t5', function ($join) { 
                    $join->on('t5.code', '=', 't1.employment_status')
                    ->where('t5.identification_id', '=', Config::get('const.C001.value'))
                    ->where('t1.is_deleted', '=', 0)
                    ->where('t5.is_deleted', '=', 0);
                })
                ->leftJoin($this->table_generalcodes.' as t6', function ($join) { 
                    $join->on('t6.code', '=', 't2.mode')
                    ->where('t6.identification_id', '=', Config::get('const.C005.value'))
                    ->where('t6.is_deleted', '=', 0);
                })
                ->leftJoin($this->table_generalcodes.' as t7', function ($join) { 
                    $join->on('t7.code', '=', 't2.check_result')
                    ->where('t7.identification_id', '=', Config::get('const.C018.value'))
                    ->where('t7.is_deleted', '=', 0);
                })
                ->leftJoin($this->table_generalcodes.' as t8', function ($join) { 
                    $join->on('t8.code', '=', 't2.check_max_time')
                    ->where('t8.identification_id', '=', Config::get('const.C018.value'))
                    ->where('t8.is_deleted', '=', 0);
                })
                ->leftJoin($this->table_working_time_dates.' as t9', function ($join) { 
                    $join->on('t9.working_date', '=', 't2.record_date');
                    $join->on('t9.employment_status', '=', 't1.employment_status');
                    $join->on('t9.user_code', '=', 't1.code');
                    $join->on('t9.department_code', '=', 't1.department_code')
                    ->where('t9.is_deleted', '=', 0);
                })
                ->leftJoin($this->table_generalcodes.' as t10', function ($join) { 
                    $join->on('t10.code', '=', 't9.check_interval')
                    ->where('t10.identification_id', '=', Config::get('const.C018.value'))
                    ->where('t10.is_deleted', '=', 0);
                })
                // ->leftJoin($this->table_calendars.' as t11', function ($join) { 
                //     $join->on('t11.department_code', '=', 't1.department_code');
                //     $join->on('t11.user_code', '=', 't1.code');
                //     $join->on('t11.date', '=', 't2.record_date')
                //     ->where('t11.is_deleted', '=', 0);
                // });
                ->leftJoin($this->table_calendar_setting_informations.' as t11', function ($join) { 
                    $join->on('t11.department_code', '=', 't1.department_code');
                    $join->on('t11.user_code', '=', 't1.code');
                    $join->on('t11.date', '=', 't2.record_date')
                    ->where('t11.is_deleted', '=', 0);
                });

            if(!empty($this->param_employment_status)){
                $mainquery->where('t1.employment_status', $this->param_employment_status);      //　雇用形態指定
            }
            if(!empty($this->param_department_code)){
                $mainquery->where('t1.department_code', $this->param_department_code);          //department_code指定
            }
            if(!empty($this->param_user_code)){
                $mainquery->where('t1.code', $this->param_user_code);                           //user_code指定
            } else {
                $mainquery->where('t1.management','<',Config::get('const.C017.out_of_user'));
            }
            $mainquery
                ->JoinSub($subquery3, 't4', function ($join) { 
                    $join->on('t4.code', '=', 't1.code');
                    $join->on('t4.max_apply_term_from', '=', 't1.apply_term_from');
                });

            // 緊急はチェックなし
            $mainquery
                ->where(function ($query) {
                    $query->where('t2.check_result', '>', 0)
                        ->orWhere('t2.check_max_time', '>', 0)
                        ->orWhere('t9.note', '<>', '')
                        ->orWhere(function ($query) {
                            $query->Where('t2.mode', '=', Config::get('const.C005.attendance_time'))
                                ->Where('t9.check_interval', '>', 0);
                            });
                });
            if(!empty($this->param_user_code)){
                $mainquery
                    ->where('t1.management', '<', Config::get('const.C017.admin_user'));
            }
            $mainquery
                ->where('t1.is_deleted', '=', 0)
                ->orderBy('t2.record_date', 'asc')
                ->orderBy('t2.record_time', 'asc')
                ->orderBy('t1.department_code', 'asc')
                ->orderBy('t1.employment_status', 'asc')
                ->orderBy('t1.code', 'asc');

            $result = $mainquery->get();
            // if (Config::get('const.DEBUG_LEVEL') == Config::get('const.DEBUG_LEVEL_VALUE.DEBUG')) {
            //     \Log::debug('sql_debug_log', ['getAlertData' => \DB::getQueryLog()]);
            //     \DB::disableQueryLog();
            // }
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

        return $result;
    }

    /**
     * 日次警告打刻取得
     * 
     *  $targetdateは適用期間
     *
     * @return void
     */
    public function getdailyAlertData(){
        // if (Config::get('const.DEBUG_LEVEL') == Config::get('const.DEBUG_LEVEL_VALUE.DEBUG')) { \DB::enableQueryLog(); }

        try {
            $sqlString = "";
            $sqlString .= "select ";
            $sqlString .= "  t1.user_code as user_code ";
            $sqlString .= "  , t1.user_name as user_name ";
            $sqlString .= "  , t1.user_management as user_management ";
            $sqlString .= "  , t1.employment_status as employment_status ";
            $sqlString .= "  , t1.employment_status_name as employment_status_name ";
            $sqlString .= "  , t1.department_code as department_code ";
            $sqlString .= "  , t1.department_name as department_name ";
            $sqlString .= "  , t1.current_record_date as current_record_date ";
            $sqlString .= "  , DATE_FORMAT(t1.current_record_time, '%m月%d日 %H:%i')as current_record_time ";
            $sqlString .= "  , CONCAT( ";
            $sqlString .= "      DATE_FORMAT(t1.current_record_date, '%Y年%m月%d日'), '(', SUBSTRING('月火水木金土日', CONVERT(t2.weekday_kubun + 1, char), 1), ')' ";
            $sqlString .= "      ) as record_date_name ";
            $sqlString .= "  , t1.current_mode as current_mode ";
            $sqlString .= "  , t1.current_mode_name as current_mode_name ";
            $sqlString .= "  , t1.before_record_date as before_record_date ";
            $sqlString .= "  , DATE_FORMAT(t1.before_record_time, '%m月%d日 %H:%i')as before_record_time ";
            $sqlString .= "  , t1.before_mode as before_mode ";
            $sqlString .= "  , t1.before_mode_name as before_mode_name ";
            $sqlString .= "  , t1.hit_alert as hit_alert ";
            $sqlString .= "  , t1.interval_alaert as interval_alaert ";
            $sqlString .= "  , t1.holiday_alert as holiday_alert ";
            $sqlString .= "  , t2.business_kubun as business_kubun ";
            $sqlString .= "  from ";
            $sqlString .= "  ( ";
            $sqlString .= "    ( ";
            $sqlString .= "      select ";
            $sqlString .= "          t3.user_code as user_code ";
            $sqlString .= "          , t8.name as user_name ";
            $sqlString .= "          , t8.management as user_management ";
            $sqlString .= "          , t8.employment_status as employment_status ";
            $sqlString .= "         , t10.code_name as employment_status_name ";
            $sqlString .= "         , t3.department_code as department_code ";
            $sqlString .= "         , t9.name as department_name ";
            $sqlString .= "         , DATE_FORMAT(t6.record_time, '%Y%m%d') as current_record_date ";
            $sqlString .= "         , t6.record_time as current_record_time ";
            $sqlString .= "         , t6.mode as current_mode ";
            $sqlString .= "         , t12.code_name as current_mode_name ";
            // モード警告
            $sqlString .= "          , CASE IFNULL(t6.mode, 0 )  ";
            $sqlString .= "            WHEN 0 THEN 0  ";             // モードなし
            $sqlString .= "            WHEN 1 THEN  ";               // 出勤
            $sqlString .= "              CASE IFNULL(t5.mode, 0 )  ";
            $sqlString .= "              WHEN 0 THEN 0  ";
            $sqlString .= "              WHEN 2 THEN 0  ";           // 退勤
            $sqlString .= "              WHEN 32 THEN 0 ";           // 緊急収集終了
            $sqlString .= "              ELSE 1  ";
            $sqlString .= "              END  ";
            $sqlString .= "            WHEN 2 THEN ";                // 退勤
            $sqlString .= "              CASE IFNULL(t5.mode, 0)  ";
            $sqlString .= "              WHEN 0 THEN 0  ";
            $sqlString .= "              WHEN 1 THEN 0  ";           // 出勤
            $sqlString .= "              WHEN 12 THEN 0 ";           // 私用外出戻り
            $sqlString .= "              WHEN 22 THEN 0 ";           // 公用外出戻り
            $sqlString .= "              ELSE 1  ";
            $sqlString .= "              END  ";
            $sqlString .= "            WHEN 21 THEN ";               // 公用外出
            $sqlString .= "              CASE IFNULL(t5.mode, 0)  ";
            $sqlString .= "              WHEN 0 THEN 0  ";
            $sqlString .= "              WHEN 1 THEN 0  ";           // 出勤
            $sqlString .= "              WHEN 12 THEN 0 ";           // 私用外出戻り
            $sqlString .= "              WHEN 22 THEN 0 ";           // 公用外出戻り
            $sqlString .= "              WHEN 31 THEN 0 ";           // 緊急収集開始
            $sqlString .= "              ELSE 1  ";
            $sqlString .= "              END  ";
            $sqlString .= "            WHEN 22 THEN ";               // 公用外出戻り
            $sqlString .= "              CASE IFNULL(t5.mode, 0)  ";
            $sqlString .= "              WHEN 0 THEN 0  ";
            $sqlString .= "              WHEN 21 THEN 0 ";           // 公用外出
            $sqlString .= "              ELSE 1  ";
            $sqlString .= "              END  ";
            $sqlString .= "            WHEN 11 THEN ";               // 私用外出
            $sqlString .= "              CASE IFNULL(t5.mode, 0)  ";
            $sqlString .= "              WHEN 0 THEN 0  ";
            $sqlString .= "              WHEN 1 THEN 0  ";           // 出勤
            $sqlString .= "              WHEN 12 THEN 0 ";           // 私用外出戻り
            $sqlString .= "              WHEN 22 THEN 0 ";           // 公用外出戻り
            $sqlString .= "              WHEN 31 THEN 0 ";           // 緊急収集開始
            $sqlString .= "              ELSE 1  ";
            $sqlString .= "              END  ";
            $sqlString .= "            WHEN 12 THEN ";               // 私用外出戻り
            $sqlString .= "              CASE IFNULL(t5.mode, 0)  ";
            $sqlString .= "              WHEN 0 THEN 0  ";
            $sqlString .= "              WHEN 11 THEN 0 ";           // 私用外出
            $sqlString .= "              ELSE 1  ";
            $sqlString .= "              END  ";
            $sqlString .= "            WHEN 31 THEN ";               // 緊急収集開始
            $sqlString .= "              CASE IFNULL(t5.mode, 0)  ";
            $sqlString .= "              WHEN 0 THEN 0  ";
            $sqlString .= "              WHEN 2 THEN 0  ";           // 退勤
            $sqlString .= "              WHEN 32 THEN 0 ";           // 緊急収集終了
            $sqlString .= "              ELSE 1  ";
            $sqlString .= "              END  ";
            $sqlString .= "            WHEN 32 THEN ";               // 緊急収集終了
            $sqlString .= "              CASE IFNULL(t5.mode, 0)  ";
            $sqlString .= "              WHEN 0 THEN 0  ";
            $sqlString .= "              WHEN 12 THEN 0 ";           // 私用外出戻り
            $sqlString .= "              WHEN 22 THEN 0 ";           // 公用外出戻り
            $sqlString .= "              WHEN 31 THEN 0 ";           // 緊急収集開始
            $sqlString .= "              ELSE 1  ";
            $sqlString .= "              END  ";
            $sqlString .= "            END as hit_alert ";
            $sqlString .= "        , DATE_FORMAT(t5.record_time, '%Y%m%d') as before_record_date ";
            $sqlString .= "        , t5.record_time as before_record_time ";
            $sqlString .= "        , t5.mode as before_mode ";
            $sqlString .= "        , t11.code_name as before_mode_name ";
            $sqlString .= "        , TIMEDIFF(t6.record_time, t5.record_time) as diff_time ";
            // 出退勤interval警告
            $sqlString .= "        , CASE IFNULL(t6.mode, 0)  ";
            $sqlString .= "            WHEN 1 THEN ";
            $sqlString .= "              CASE IFNULL(t5.mode, 0)  ";
            $sqlString .= "              WHEN 2 THEN ";
            $sqlString .= "                CASE ";
            $sqlString .= "                WHEN IFNULL(TIMEDIFF(t6.record_time, t5.record_time), 0) < ?  ";
            $sqlString .= "                THEN 1  ";
            $sqlString .= "                ELSE 0  ";
            $sqlString .= "                END  ";
            $sqlString .= "            ELSE 0  ";
            $sqlString .= "            END  ";
            $sqlString .= "            ELSE 0  ";
            $sqlString .= "            END as interval_alaert ";
            // 出勤日警告
            $sqlString .= "        , 0 as holiday_alert  ";
            $sqlString .= "      from ";
            $sqlString .= "        ".$this->table." as t3  ";
            $sqlString .= "        left join (  ";
            $sqlString .= "          select ";
            $sqlString .= "            t1.user_code as user_code ";
            $sqlString .= "            , t1.department_code as department_code ";
            $sqlString .= "            , MAX(t1.record_time) as max_record_time ";
            $sqlString .= "            , t2.record_time as record_time  ";
            $sqlString .= "          from ";
            $sqlString .= "            ".$this->table." as t1  ";
            $sqlString .= "            inner join (  ";
            $sqlString .= "              select ";
            $sqlString .= "                t1.user_code as user_code ";
            $sqlString .= "                , t1.department_code as department_code ";
            $sqlString .= "                , t1.record_time as record_time  ";
            $sqlString .= "              from ";
            $sqlString .= "                ".$this->table." as t1  ";
            $sqlString .= "              where ";
            $sqlString .= "                t1.record_time between ? and ?  ";
            $sqlString .= "                and t1.is_deleted = ? ";
            $sqlString .= "            ) as t2  ";
            $sqlString .= "            on t2.user_code = t1.user_code  ";
            $sqlString .= "              and t2.department_code = t1.department_code  ";
            $sqlString .= "              and t2.record_time > t1.record_time  ";
            $sqlString .= "              and t1.is_deleted = ?  ";
            $sqlString .= "          group by ";
            $sqlString .= "            t1.user_code ";
            $sqlString .= "            , t1.department_code ";
            $sqlString .= "            , t2.record_time ";
            $sqlString .= "        ) as t4  ";
            $sqlString .= "        on t4.user_code = t3.user_code  ";
            $sqlString .= "          and t4.department_code = t3.department_code  ";
            $sqlString .= "        inner join (  ";
            $sqlString .= "          select ";
            $sqlString .= "            t1.user_code as user_code ";
            $sqlString .= "            , t1.department_code as department_code ";
            $sqlString .= "            , t1.record_time as record_time ";
            $sqlString .= "            , t1.mode as mode  ";
            $sqlString .= "          from ";
            $sqlString .= "            ".$this->table." as t1  ";
            $sqlString .= "          where ";
            $sqlString .= "            t1.record_time between ? and ?  ";
            $sqlString .= "            and t1.is_deleted = ? ";
            $sqlString .= "        ) as t5  ";
            $sqlString .= "        on t5.user_code = t4.user_code  ";
            $sqlString .= "          and t5.department_code = t4.department_code  ";
            $sqlString .= "          and t5.record_time = t4.max_record_time  ";
            $sqlString .= "        inner join (  ";
            $sqlString .= "          select ";
            $sqlString .= "            t1.user_code as user_code ";
            $sqlString .= "            , t1.department_code as department_code ";
            $sqlString .= "            , t1.record_time as record_time ";
            $sqlString .= "            , t1.mode as mode  ";
            $sqlString .= "          from ";
            $sqlString .= "            ".$this->table." as t1  ";
            $sqlString .= "          where ";
            $sqlString .= "            t1.record_time between ? and ?  ";
            $sqlString .= "            and t1.is_deleted = ? ";
            $sqlString .= "        ) as t6  ";
            $sqlString .= "        on t6.user_code = t4.user_code  ";
            $sqlString .= "          and t6.department_code = t4.department_code  ";
            $sqlString .= "          and t6.record_time = t4.record_time  ";
            $sqlString .= "        inner join (  ";
            $sqlString .= "          select ";
            $sqlString .= "            code as code ";
            $sqlString .= "            , MAX(apply_term_from) as max_apply_term_from  ";
            $sqlString .= "          from ";
            $sqlString .= "            ".$this->table_users."  ";
            $sqlString .= "          where ";
            $sqlString .= "            apply_term_from <= ? ";
            $sqlString .= "            and role < ? ";
            $sqlString .= "            and is_deleted = ? ";
            $sqlString .= "          group by ";
            $sqlString .= "            code ";
            $sqlString .= "        ) as t7  ";
            $sqlString .= "        on t7.code = t3.user_code  ";
            $sqlString .= "        inner join ".$this->table_users." as t8  ";
            $sqlString .= "        on t8.code = t7.code  ";
            $sqlString .= "          and t8.apply_term_from = t7.max_apply_term_from  ";
            $sqlString .= "          and t8.kill_from_date > ? ";
            $sqlString .= "          and t8.is_deleted = ? ";
            $sqlString .= "        left join (  ";
            $sqlString .= "          select ";
            $sqlString .= "            t1.code as code ";
            $sqlString .= "            , t1.name as name  ";
            $sqlString .= "          from ";
            $sqlString .= "            ".$this->table_departments." as t1  ";
            $sqlString .= "            inner join (  ";
            $sqlString .= "              select ";
            $sqlString .= "                code as code ";
            $sqlString .= "                , MAX(apply_term_from) as max_apply_term_from  ";
            $sqlString .= "              from ";
            $sqlString .= "                ".$this->table_departments."  ";
            $sqlString .= "              where ";
            $sqlString .= "                apply_term_from <= ? ";
            $sqlString .= "                and is_deleted = ? ";
            $sqlString .= "              group by ";
            $sqlString .= "                code ";
            $sqlString .= "            ) as t2  ";
            $sqlString .= "              on t1.code = t2.code  ";
            $sqlString .= "                and t1.apply_term_from = t2.max_apply_term_from  ";
            $sqlString .= "          where ";
            $sqlString .= "            t1.kill_from_date > ? ";
            $sqlString .= "            and t1.is_deleted = ? ";
            $sqlString .= "        ) as t9  ";
            $sqlString .= "        on t9.code = t3.department_code  ";
            $sqlString .= "        left join ".$this->table_generalcodes." as t10  ";
            $sqlString .= "        on t10.code = t8.employment_status  ";
            $sqlString .= "          and t10.identification_id = ? ";
            $sqlString .= "          and t10.is_deleted = ? ";
            $sqlString .= "        left join ".$this->table_generalcodes." as t11  ";
            $sqlString .= "        on t11.code = t5.mode  ";
            $sqlString .= "          and t11.identification_id = ? ";
            $sqlString .= "          and t11.is_deleted = ? ";
            $sqlString .= "        left join ".$this->table_generalcodes." as t12  ";
            $sqlString .= "        on t12.code = t6.mode  ";
            $sqlString .= "          and t12.identification_id = ? ";
            $sqlString .= "          and t12.is_deleted = ?  ";
            $sqlString .= "      where ";
            $sqlString .= "        t3.record_time between ? and ?  ";
            $sqlString .= "        and t3.is_deleted = ? ";
            $sqlString .= "    )  ";
            $sqlString .= "    union (  ";
            $sqlString .= "      select ";
            $sqlString .= "        t1.user_code as user_code ";
            $sqlString .= "        , t1.user_name as user_name ";
            $sqlString .= "        , t1.user_management as user_management ";
            $sqlString .= "        , t1.employment_status as employment_status ";
            $sqlString .= "        , t10.code_name as employment_status_name ";
            $sqlString .= "        , t1.department_code as department_code ";
            $sqlString .= "        , t9.name as department_name ";
            $sqlString .= "        , t2.calendars_date as current_record_date ";
            $sqlString .= "        , null as current_record_time ";
            $sqlString .= "        , null as current_mode ";
            $sqlString .= "        , null as current_mode_name ";
            $sqlString .= "        , ? as hit_alert ";
            $sqlString .= "        , t3.record_date as before_record_date ";
            $sqlString .= "        , null as before_record_time ";
            $sqlString .= "        , null as before_mode ";
            $sqlString .= "        , null as before_mode_name ";
            $sqlString .= "        , null as diff_time ";
            $sqlString .= "        , ? as interval_alaert ";
            $sqlString .= "        , ? as holiday_alert  ";
            $sqlString .= "      from ";
            $sqlString .= "        (  ";
            $sqlString .= "          select ";
            $sqlString .= "            t1.code as user_code ";
            $sqlString .= "            , t1.name as user_name ";
            $sqlString .= "            , t1.management as user_management ";
            $sqlString .= "            , t1.employment_status as employment_status ";
            $sqlString .= "            , t1.department_code as department_code ";
            $sqlString .= "            , t1.kill_from_date as kill_from_date ";
            $sqlString .= "          from ";
            $sqlString .= "            ".$this->table_users." as t1  ";
            $sqlString .= "            inner join (  ";
            $sqlString .= "              select ";
            $sqlString .= "                code as code ";
            $sqlString .= "                , MAX(apply_term_from) as max_apply_term_from  ";
            $sqlString .= "              from ";
            $sqlString .= "                ".$this->table_users."  ";
            $sqlString .= "              where ";
            $sqlString .= "                apply_term_from <= ? ";
            $sqlString .= "                and role < ? ";
            $sqlString .= "                and is_deleted = ? ";
            $sqlString .= "              group by ";
            $sqlString .= "                code ";
            $sqlString .= "            ) as t3  ";
            $sqlString .= "            on t3.code = t1.code  ";
            $sqlString .= "              and t3.max_apply_term_from = t1.apply_term_from  ";
            $sqlString .= "          where ";
            $sqlString .= "            t1.kill_from_date > ? ";
            $sqlString .= "        ) as t1  ";
            $sqlString .= "        inner join (  ";
            $sqlString .= "          select ";
            $sqlString .= "            t1.department_code as department_code ";
            $sqlString .= "            , t1.user_code as user_code ";
            $sqlString .= "            , DATE_FORMAT(t1.date, '%Y%m%d') as calendars_date  ";
            $sqlString .= "          from ";
            $sqlString .= "            ".$this->table_calendar_setting_informations." as t1 ";
            $sqlString .= "          where ";
            $sqlString .= "            t1.date between ? and ? ";
            $sqlString .= "            and  t1.business_kubun = ? ";
            $sqlString .= "            and  holiday_kubun = ? ";
            $sqlString .= "            and  is_deleted = ? ";
            $sqlString .= "        ) as t2  ";
            $sqlString .= "        on t2.department_code = t1.department_code ";
            $sqlString .= "          and t2.user_code = t1.user_code  ";
            $sqlString .= "        left join (  ";
            $sqlString .= "          select ";
            $sqlString .= "            t1.user_code as user_code ";
            $sqlString .= "            , t1.department_code as department_code ";
            $sqlString .= "            , DATE_FORMAT(t1.record_time, '%Y%m%d') as record_date  ";
            $sqlString .= "          from ";
            $sqlString .= "            ".$this->table." as t1 ";
            $sqlString .= "          where ";
            $sqlString .= "            t1.record_time between ? and ? ";
            $sqlString .= "        ) as t3  ";
            $sqlString .= "        on t3.user_code = t1.user_code  ";
            $sqlString .= "          and t3.department_code = t1.department_code  ";
            $sqlString .= "          and t3.record_date = t2.calendars_date  ";
            $sqlString .= "        left join (  ";
            $sqlString .= "          select ";
            $sqlString .= "            t1.code as code ";
            $sqlString .= "            , t1.name as name  ";
            $sqlString .= "          from ";
            $sqlString .= "            ".$this->table_departments." as t1  ";
            $sqlString .= "            inner join (  ";
            $sqlString .= "              select ";
            $sqlString .= "                code as code ";
            $sqlString .= "                , MAX(apply_term_from) as max_apply_term_from  ";
            $sqlString .= "              from ";
            $sqlString .= "                ".$this->table_departments."  ";
            $sqlString .= "              where ";
            $sqlString .= "                apply_term_from <= ? ";
            $sqlString .= "                and is_deleted = ? ";
            $sqlString .= "              group by ";
            $sqlString .= "                code ";
            $sqlString .= "            ) as t2  ";
            $sqlString .= "            on t2.code = t1.code  ";
            $sqlString .= "              and t2.max_apply_term_from =  t1.apply_term_from ";
            $sqlString .= "          where ";
            $sqlString .= "            t1.kill_from_date > ? ";
            $sqlString .= "            and t1.is_deleted = ? ";
            $sqlString .= "        ) as t9  ";
            $sqlString .= "        on t9.code = t1.department_code  ";
            $sqlString .= "        left join ".$this->table_generalcodes." as t10  ";
            $sqlString .= "        on t10.code = t1.employment_status  ";
            $sqlString .= "          and t10.identification_id = ? ";
            $sqlString .= "          and t10.is_deleted = ? ";
            $sqlString .= "      where ";
            $sqlString .= "        t2.calendars_date between ? and ?  ";
            $sqlString .= "        and t3.record_date is null ";
            $sqlString .= "    ) ";
            $sqlString .= "  ) as t1  ";
            $sqlString .= "  inner join ".$this->table_calendar_setting_informations." as t2  ";
            $sqlString .= "  on t2.department_code = t1.department_code  ";
            $sqlString .= "    and t2.user_code = t1.user_code  ";
            $sqlString .= "    and t2.date = t1.current_record_date ";
            // 条件
            $sqlString .= "where 1 = 1 ";
            if(!empty($this->param_employment_status)){
                $sqlString .= "and t1.employment_status = ? ";          // 雇用形態指定
            }
            if(!empty($this->param_department_code)){
                $sqlString .= "and t1.department_code = ? ";            //department_code指定
            }
            if(!empty($this->param_user_code)){
                $sqlString .= "and t1.user_code = ? ";                  //user_code指定
            } else {
                $sqlString .= "and t1.user_management <= ? ";
            }
            // $sqlString .= "and t2.business_kubun = ? ";
            $sqlString .= "and t2.is_deleted = ? ";
            $sqlString .= "and (
                                t1.hit_alert > ?
                                or t1.interval_alaert > ?
                                or t1.holiday_alert > ?
                            )";
            $sqlString .= "order by
                                t1.current_record_date desc
                                , t1.user_code asc
                                , t1.department_code asc
                                , t1.current_record_time asc";
            // バインド
            // インターバル時間取得
            $apicommon = new ApiCommonController();
            $interval_time = $apicommon->getIntevalMinute($this->param_end_date);
            $array_setBindingsStr = array();
            // 出退勤interval警告
            $array_setBindingsStr[] = (string)$interval_time;
            $array_setBindingsStr[] = (string)$this->param_date_from;
            $array_setBindingsStr[] = (string)$this->param_date_to;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = (string)$this->param_date_from;
            $array_setBindingsStr[] = (string)$this->param_date_to;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = (string)$this->param_date_from;
            $array_setBindingsStr[] = (string)$this->param_date_to;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = (string)$this->param_end_date;
            $array_setBindingsStr[] = (int)Config::get('const.C017.admin_user');
            $array_setBindingsStr[] = 0;
            // 月末
            $dt = new Carbon($this->param_end_date);
            $lastOfMonth = date_format($dt->endOfMonth(), 'Ymd');
            $array_setBindingsStr[] = (string)$lastOfMonth;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = (string)$this->param_end_date;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = (string)$lastOfMonth;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = (string)Config::get('const.C001.value');
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = (string)Config::get('const.C005.value');
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = (string)Config::get('const.C005.value');
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = (string)$this->param_date_from;
            $array_setBindingsStr[] = (string)$this->param_date_to;
            $array_setBindingsStr[] = 0;
            // union
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = (string)$this->param_end_date;
            $array_setBindingsStr[] = (int)Config::get('const.C017.admin_user');;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = (string)$lastOfMonth;
            $array_setBindingsStr[] = (string)$this->param_start_date;
            $array_setBindingsStr[] = (string)$this->param_end_date;
            $array_setBindingsStr[] = (int)Config::get('const.C007.basic');
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = (string)$this->param_date_from;
            $array_setBindingsStr[] = (string)$this->param_date_to;
            $array_setBindingsStr[] = (string)$this->param_end_date;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = (string)$lastOfMonth;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = (string)Config::get('const.C001.value');
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = (string)$this->param_start_date;
            $array_setBindingsStr[] = (string)$this->param_end_date;
            if(!empty($this->param_employment_status)) {
                $array_setBindingsStr[] = (int)$this->param_employment_status;
            }
            if(!empty($this->param_department_code)) {
                $array_setBindingsStr[] = (string)$this->param_department_code;
            }
            if(!empty($this->param_user_code)) {
                $array_setBindingsStr[] = (string)$this->param_user_code;
            } else {
                $array_setBindingsStr[] = (int)Config::get('const.C017.out_of_user');
            }
            // $array_setBindingsStr[] = Config::get('const.C007.basic');
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 0;
            $result = DB::select($sqlString, $array_setBindingsStr);

            Log::debug(' $result count = '.count($result));
            // if (Config::get('const.DEBUG_LEVEL') == Config::get('const.DEBUG_LEVEL_VALUE.DEBUG')) {
            //     \Log::debug('sql_debug_log', ['getdailyAlertData' => \DB::getQueryLog()]);
            //     \DB::disableQueryLog();
            // }
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

        return $result;
    }

    /**
     * 日次警告打刻取得
     * 
     *  $targetdateは適用期間
     *
     * @return void
     */
    // public function getdailyAlertData(){
    //     // if (Config::get('const.DEBUG_LEVEL') == Config::get('const.DEBUG_LEVEL_VALUE.DEBUG')) { \DB::enableQueryLog(); }

    //     try {
    //         $sqlString = "";
    //         $sqlString .= "select ";
    //         $sqlString .= "  t1.user_code as user_code ";
    //         $sqlString .= "  , t1.user_name as user_name ";
    //         $sqlString .= "  , t1.user_management as user_management ";
    //         $sqlString .= "  , t1.employment_status as employment_status ";
    //         $sqlString .= "  , t1.employment_status_name as employment_status_name ";
    //         $sqlString .= "  , t1.department_code as department_code ";
    //         $sqlString .= "  , t1.department_name as department_name ";
    //         $sqlString .= "  , t1.current_record_date as current_record_date ";
    //         $sqlString .= "  , DATE_FORMAT(t1.current_record_time, '%m月%d日 %H:%i')as current_record_time ";
    //         $sqlString .= "  , CONCAT( ";
    //         $sqlString .= "      DATE_FORMAT(t1.current_record_date, '%Y年%m月%d日'), '(', SUBSTRING('月火水木金土日', CONVERT(t2.weekday_kubun + 1, char), 1), ')' ";
    //         $sqlString .= "      ) as record_date_name ";
    //         $sqlString .= "  , t1.current_mode as current_mode ";
    //         $sqlString .= "  , t1.current_mode_name as current_mode_name ";
    //         $sqlString .= "  , t1.before_record_date as before_record_date ";
    //         $sqlString .= "  , DATE_FORMAT(t1.before_record_time, '%m月%d日 %H:%i')as before_record_time ";
    //         $sqlString .= "  , t1.before_mode as before_mode ";
    //         $sqlString .= "  , t1.before_mode_name as before_mode_name ";
    //         $sqlString .= "  , t1.hit_alert as hit_alert ";
    //         $sqlString .= "  , t1.interval_alaert as interval_alaert ";
    //         $sqlString .= "  , t1.holiday_alert as holiday_alert ";
    //         $sqlString .= "  , t2.business_kubun as business_kubun ";
    //         $sqlString .= "  from ";
    //         $sqlString .= "  ( ";
    //         $sqlString .= "    ( ";
    //         $sqlString .= "      select ";
    //         $sqlString .= "          t3.user_code as user_code ";
    //         $sqlString .= "          , t8.name as user_name ";
    //         $sqlString .= "          , t8.management as user_management ";
    //         $sqlString .= "          , t8.employment_status as employment_status ";
    //         $sqlString .= "         , t10.code_name as employment_status_name ";
    //         $sqlString .= "         , t3.department_code as department_code ";
    //         $sqlString .= "         , t9.name as department_name ";
    //         $sqlString .= "         , DATE_FORMAT(t6.record_time, '%Y%m%d') as current_record_date ";
    //         $sqlString .= "         , t6.record_time as current_record_time ";
    //         $sqlString .= "         , t6.mode as current_mode ";
    //         $sqlString .= "         , t12.code_name as current_mode_name ";
    //         // モード
    //         $sqlString .= "          , CASE IFNULL(t6.mode, ?)  ";
    //         $sqlString .= "            WHEN ? THEN ?  ";             // モードなし
    //         $sqlString .= "            WHEN ? THEN  ";               // 出勤
    //         $sqlString .= "              CASE IFNULL(t5.mode, ?)  ";
    //         $sqlString .= "              WHEN ? THEN ?  ";
    //         $sqlString .= "              WHEN ? THEN ?  ";           // 退勤
    //         $sqlString .= "              WHEN ? THEN ?  ";           // 緊急収集終了
    //         $sqlString .= "              ELSE ?  ";
    //         $sqlString .= "              END  ";
    //         $sqlString .= "            WHEN ? THEN ";                // 退勤
    //         $sqlString .= "              CASE IFNULL(t5.mode, ?)  ";
    //         $sqlString .= "              WHEN ? THEN ?  ";
    //         $sqlString .= "              WHEN ? THEN ?  ";           // 出勤
    //         $sqlString .= "              WHEN ? THEN ?  ";           // 私用外出戻り
    //         $sqlString .= "              WHEN ? THEN ?  ";           // 公用外出戻り
    //         $sqlString .= "              ELSE ?  ";
    //         $sqlString .= "              END  ";
    //         $sqlString .= "            WHEN ? THEN ";                // 公用外出
    //         $sqlString .= "              CASE IFNULL(t5.mode, ?)  ";
    //         $sqlString .= "              WHEN ? THEN ?  ";
    //         $sqlString .= "              WHEN ? THEN ?  ";           // 出勤
    //         $sqlString .= "              WHEN ? THEN ?  ";           // 私用外出戻り
    //         $sqlString .= "              WHEN ? THEN ?  ";           // 公用外出戻り
    //         $sqlString .= "              WHEN ? THEN ?  ";           // 緊急収集開始
    //         $sqlString .= "              ELSE ?  ";
    //         $sqlString .= "              END  ";
    //         $sqlString .= "            WHEN ? THEN ";                // 公用外出戻り
    //         $sqlString .= "              CASE IFNULL(t5.mode, ?)  ";
    //         $sqlString .= "              WHEN ? THEN ?  ";
    //         $sqlString .= "              WHEN ? THEN ?  ";           // 公用外出
    //         $sqlString .= "              ELSE ?  ";
    //         $sqlString .= "              END  ";
    //         $sqlString .= "            WHEN ? THEN ";                // 私用外出
    //         $sqlString .= "              CASE IFNULL(t5.mode, ?)  ";
    //         $sqlString .= "              WHEN ? THEN ?  ";
    //         $sqlString .= "              WHEN ? THEN ?  ";           // 出勤
    //         $sqlString .= "              WHEN ? THEN ?  ";           // 私用外出戻り
    //         $sqlString .= "              WHEN ? THEN ?  ";           // 公用外出戻り
    //         $sqlString .= "              WHEN ? THEN ?  ";           // 緊急収集開始
    //         $sqlString .= "              ELSE ?  ";
    //         $sqlString .= "              END  ";
    //         $sqlString .= "            WHEN ? THEN ";                // 私用外出戻り
    //         $sqlString .= "              CASE IFNULL(t5.mode, ?)  ";
    //         $sqlString .= "              WHEN ? THEN ?  ";
    //         $sqlString .= "              WHEN ? THEN ?  ";           // 私用外出
    //         $sqlString .= "              ELSE ?  ";
    //         $sqlString .= "              END  ";
    //         $sqlString .= "            WHEN ? THEN ";                // 緊急収集開始
    //         $sqlString .= "              CASE IFNULL(t5.mode, ?)  ";
    //         $sqlString .= "              WHEN ? THEN ?  ";
    //         $sqlString .= "              WHEN ? THEN ?  ";           // 退勤
    //         $sqlString .= "              WHEN ? THEN ?  ";           // 緊急収集終了
    //         $sqlString .= "              ELSE ?  ";
    //         $sqlString .= "              END  ";
    //         $sqlString .= "            WHEN ? THEN ";                // 緊急収集終了
    //         $sqlString .= "              CASE IFNULL(t5.mode, ?)  ";
    //         $sqlString .= "              WHEN ? THEN ?  ";
    //         $sqlString .= "              WHEN ? THEN ?  ";           // 私用外出戻り
    //         $sqlString .= "              WHEN ? THEN ?  ";           // 公用外出戻り
    //         $sqlString .= "              WHEN ? THEN ?  ";           // 緊急収集開始
    //         $sqlString .= "              ELSE ?  ";
    //         $sqlString .= "              END  ";
    //         $sqlString .= "            END as hit_alert ";
    //         $sqlString .= "        , DATE_FORMAT(t5.record_time, '%Y%m%d') as before_record_date ";
    //         $sqlString .= "        , t5.record_time as before_record_time ";
    //         $sqlString .= "        , t5.mode as before_mode ";
    //         $sqlString .= "        , t11.code_name as before_mode_name ";
    //         $sqlString .= "        , TIMEDIFF(t6.record_time, t5.record_time) as diff_time ";
    //         $sqlString .= "        , CASE IFNULL(t6.mode, ?)  ";
    //         $sqlString .= "            WHEN ? THEN ";
    //         $sqlString .= "              CASE IFNULL(t5.mode, ?)  ";
    //         $sqlString .= "              WHEN ? THEN ";
    //         $sqlString .= "                CASE ";
    //         $sqlString .= "                WHEN IFNULL(TIMEDIFF(t6.record_time, t5.record_time), ?) < ?  ";
    //         $sqlString .= "                THEN ?  ";
    //         $sqlString .= "                ELSE ?  ";
    //         $sqlString .= "                END  ";
    //         $sqlString .= "            ELSE ?  ";
    //         $sqlString .= "            END  ";
    //         $sqlString .= "            ELSE ?  ";
    //         $sqlString .= "            END as interval_alaert ";
    //         $sqlString .= "        , ? as holiday_alert  ";
    //         $sqlString .= "      from ";
    //         $sqlString .= "        ".$this->table." as t3  ";
    //         $sqlString .= "        left join (  ";
    //         $sqlString .= "          select ";
    //         $sqlString .= "            t1.user_code as user_code ";
    //         $sqlString .= "            , t1.department_code as department_code ";
    //         $sqlString .= "            , MAX(t1.record_time) as max_record_time ";
    //         $sqlString .= "            , t2.record_time as record_time  ";
    //         $sqlString .= "          from ";
    //         $sqlString .= "            ".$this->table." as t1  ";
    //         $sqlString .= "            inner join (  ";
    //         $sqlString .= "              select ";
    //         $sqlString .= "                t1.user_code as user_code ";
    //         $sqlString .= "                , t1.department_code as department_code ";
    //         $sqlString .= "                , t1.record_time as record_time  ";
    //         $sqlString .= "              from ";
    //         $sqlString .= "                ".$this->table." as t1  ";
    //         $sqlString .= "              where ";
    //         $sqlString .= "                t1.record_time between ? and ?  ";
    //         $sqlString .= "                and t1.is_deleted = ? ";
    //         $sqlString .= "            ) as t2  ";
    //         $sqlString .= "            on t2.user_code = t1.user_code  ";
    //         $sqlString .= "              and t2.department_code = t1.department_code  ";
    //         $sqlString .= "              and t2.record_time > t1.record_time  ";
    //         $sqlString .= "              and t1.is_deleted = ?  ";
    //         $sqlString .= "          group by ";
    //         $sqlString .= "            t1.user_code ";
    //         $sqlString .= "            , t1.department_code ";
    //         $sqlString .= "            , t2.record_time ";
    //         $sqlString .= "        ) as t4  ";
    //         $sqlString .= "        on t4.user_code = t3.user_code  ";
    //         $sqlString .= "          and t4.department_code = t3.department_code  ";
    //         $sqlString .= "        inner join (  ";
    //         $sqlString .= "          select ";
    //         $sqlString .= "            t1.user_code as user_code ";
    //         $sqlString .= "            , t1.department_code as department_code ";
    //         $sqlString .= "            , t1.record_time as record_time ";
    //         $sqlString .= "            , t1.mode as mode  ";
    //         $sqlString .= "          from ";
    //         $sqlString .= "            ".$this->table." as t1  ";
    //         $sqlString .= "          where ";
    //         $sqlString .= "            t1.record_time between ? and ?  ";
    //         $sqlString .= "            and t1.is_deleted = ? ";
    //         $sqlString .= "        ) as t5  ";
    //         $sqlString .= "        on t5.user_code = t4.user_code  ";
    //         $sqlString .= "          and t5.department_code = t4.department_code  ";
    //         $sqlString .= "          and t5.record_time = t4.max_record_time  ";
    //         $sqlString .= "        inner join (  ";
    //         $sqlString .= "          select ";
    //         $sqlString .= "            t1.user_code as user_code ";
    //         $sqlString .= "            , t1.department_code as department_code ";
    //         $sqlString .= "            , t1.record_time as record_time ";
    //         $sqlString .= "            , t1.mode as mode  ";
    //         $sqlString .= "          from ";
    //         $sqlString .= "            ".$this->table." as t1  ";
    //         $sqlString .= "          where ";
    //         $sqlString .= "            t1.record_time between ? and ?  ";
    //         $sqlString .= "            and t1.is_deleted = ? ";
    //         $sqlString .= "        ) as t6  ";
    //         $sqlString .= "        on t6.user_code = t4.user_code  ";
    //         $sqlString .= "          and t6.department_code = t4.department_code  ";
    //         $sqlString .= "          and t6.record_time = t4.record_time  ";
    //         $sqlString .= "        inner join (  ";
    //         $sqlString .= "          select ";
    //         $sqlString .= "            code as code ";
    //         $sqlString .= "            , MAX(apply_term_from) as max_apply_term_from  ";
    //         $sqlString .= "          from ";
    //         $sqlString .= "            ".$this->table_users."  ";
    //         $sqlString .= "          where ";
    //         $sqlString .= "            apply_term_from <= ? ";
    //         $sqlString .= "            and role < ? ";
    //         $sqlString .= "            and is_deleted = ? ";
    //         $sqlString .= "          group by ";
    //         $sqlString .= "            code ";
    //         $sqlString .= "        ) as t7  ";
    //         $sqlString .= "        on t7.code = t3.user_code  ";
    //         $sqlString .= "        inner join ".$this->table_users." as t8  ";
    //         $sqlString .= "        on t8.code = t7.code  ";
    //         $sqlString .= "          and t8.apply_term_from = t7.max_apply_term_from  ";
    //         $sqlString .= "          and t8.kill_from_date > ? ";
    //         $sqlString .= "          and t8.is_deleted = ? ";
    //         $sqlString .= "        left join (  ";
    //         $sqlString .= "          select ";
    //         $sqlString .= "            t1.code as code ";
    //         $sqlString .= "            , t1.name as name  ";
    //         $sqlString .= "          from ";
    //         $sqlString .= "            ".$this->table_departments." as t1  ";
    //         $sqlString .= "            inner join (  ";
    //         $sqlString .= "              select ";
    //         $sqlString .= "                code as code ";
    //         $sqlString .= "                , MAX(apply_term_from) as max_apply_term_from  ";
    //         $sqlString .= "              from ";
    //         $sqlString .= "                ".$this->table_departments."  ";
    //         $sqlString .= "              where ";
    //         $sqlString .= "                apply_term_from <= ? ";
    //         $sqlString .= "                and is_deleted = ? ";
    //         $sqlString .= "              group by ";
    //         $sqlString .= "                code ";
    //         $sqlString .= "            ) as t2  ";
    //         $sqlString .= "              on t1.code = t2.code  ";
    //         $sqlString .= "                and t1.apply_term_from = t2.max_apply_term_from  ";
    //         $sqlString .= "          where ";
    //         $sqlString .= "            t1.kill_from_date > ? ";
    //         $sqlString .= "            and t1.is_deleted = ? ";
    //         $sqlString .= "        ) as t9  ";
    //         $sqlString .= "        on t9.code = t3.department_code  ";
    //         $sqlString .= "        left join ".$this->table_generalcodes." as t10  ";
    //         $sqlString .= "        on t10.code = t8.employment_status  ";
    //         $sqlString .= "          and t10.identification_id = ? ";
    //         $sqlString .= "          and t10.is_deleted = ? ";
    //         $sqlString .= "        left join ".$this->table_generalcodes." as t11  ";
    //         $sqlString .= "        on t11.code = t5.mode  ";
    //         $sqlString .= "          and t11.identification_id = ? ";
    //         $sqlString .= "          and t11.is_deleted = ? ";
    //         $sqlString .= "        left join ".$this->table_generalcodes." as t12  ";
    //         $sqlString .= "        on t12.code = t6.mode  ";
    //         $sqlString .= "          and t12.identification_id = ? ";
    //         $sqlString .= "          and t12.is_deleted = ?  ";
    //         $sqlString .= "      where ";
    //         $sqlString .= "        t3.record_time between ? and ?  ";
    //         $sqlString .= "        and t3.is_deleted = ? ";
    //         $sqlString .= "    )  ";
    //         $sqlString .= "    union (  ";
    //         $sqlString .= "      select ";
    //         $sqlString .= "        t1.user_code as user_code ";
    //         $sqlString .= "        , t1.user_name as user_name ";
    //         $sqlString .= "        , t1.user_management as user_management ";
    //         $sqlString .= "        , t1.employment_status as employment_status ";
    //         $sqlString .= "        , t10.code_name as employment_status_name ";
    //         $sqlString .= "        , t1.department_code as department_code ";
    //         $sqlString .= "        , t9.name as department_name ";
    //         $sqlString .= "        , t2.calendars_date as current_record_date ";
    //         $sqlString .= "        , null as current_record_time ";
    //         $sqlString .= "        , null as current_mode ";
    //         $sqlString .= "        , null as current_mode_name ";
    //         $sqlString .= "        , ? as hit_alert ";
    //         $sqlString .= "        , t3.record_date as before_record_date ";
    //         $sqlString .= "        , null as before_record_time ";
    //         $sqlString .= "        , null as before_mode ";
    //         $sqlString .= "        , null as before_mode_name ";
    //         $sqlString .= "        , null as diff_time ";
    //         $sqlString .= "        , ? as interval_alaert ";
    //         $sqlString .= "        , ? as holiday_alert  ";
    //         $sqlString .= "      from ";
    //         $sqlString .= "        (  ";
    //         $sqlString .= "          select ";
    //         $sqlString .= "            t1.code as user_code ";
    //         $sqlString .= "            , t1.name as user_name ";
    //         $sqlString .= "            , t1.management as user_management ";
    //         $sqlString .= "            , t1.employment_status as employment_status ";
    //         $sqlString .= "            , t1.department_code as department_code ";
    //         $sqlString .= "            , t1.kill_from_date as kill_from_date ";
    //         $sqlString .= "          from ";
    //         $sqlString .= "            ".$this->table_users." as t1  ";
    //         $sqlString .= "            inner join (  ";
    //         $sqlString .= "              select ";
    //         $sqlString .= "                code as code ";
    //         $sqlString .= "                , MAX(apply_term_from) as max_apply_term_from  ";
    //         $sqlString .= "              from ";
    //         $sqlString .= "                ".$this->table_users."  ";
    //         $sqlString .= "              where ";
    //         $sqlString .= "                apply_term_from <= ? ";
    //         $sqlString .= "                and role < ? ";
    //         $sqlString .= "                and is_deleted = ? ";
    //         $sqlString .= "              group by ";
    //         $sqlString .= "                code ";
    //         $sqlString .= "            ) as t3  ";
    //         $sqlString .= "            on t3.code = t1.code  ";
    //         $sqlString .= "              and t3.max_apply_term_from = t1.apply_term_from  ";
    //         $sqlString .= "          where ";
    //         $sqlString .= "            t1.kill_from_date > ? ";
    //         $sqlString .= "        ) as t1  ";
    //         $sqlString .= "        inner join (  ";
    //         $sqlString .= "          select ";
    //         $sqlString .= "            t1.department_code as department_code ";
    //         $sqlString .= "            , t1.user_code as user_code ";
    //         $sqlString .= "            , DATE_FORMAT(t1.date, '%Y%m%d') as calendars_date  ";
    //         $sqlString .= "          from ";
    //         $sqlString .= "            ".$this->table_calendar_setting_informations." as t1 ";
    //         $sqlString .= "          where ";
    //         $sqlString .= "            t1.date between ? and ? ";
    //         $sqlString .= "            and  t1.business_kubun = ? ";
    //         $sqlString .= "            and  holiday_kubun = ? ";
    //         $sqlString .= "            and  is_deleted = ? ";
    //         $sqlString .= "        ) as t2  ";
    //         $sqlString .= "        on t2.department_code = t1.department_code ";
    //         $sqlString .= "          and t2.user_code = t1.user_code  ";
    //         $sqlString .= "        left join (  ";
    //         $sqlString .= "          select ";
    //         $sqlString .= "            t1.user_code as user_code ";
    //         $sqlString .= "            , t1.department_code as department_code ";
    //         $sqlString .= "            , DATE_FORMAT(t1.record_time, '%Y%m%d') as record_date  ";
    //         $sqlString .= "          from ";
    //         $sqlString .= "            ".$this->table." as t1 ";
    //         $sqlString .= "          where ";
    //         $sqlString .= "            t1.record_time between ? and ? ";
    //         $sqlString .= "        ) as t3  ";
    //         $sqlString .= "        on t3.user_code = t1.user_code  ";
    //         $sqlString .= "          and t3.department_code = t1.department_code  ";
    //         $sqlString .= "          and t3.record_date = t2.calendars_date  ";
    //         $sqlString .= "        left join (  ";
    //         $sqlString .= "          select ";
    //         $sqlString .= "            t1.code as code ";
    //         $sqlString .= "            , t1.name as name  ";
    //         $sqlString .= "          from ";
    //         $sqlString .= "            ".$this->table_departments." as t1  ";
    //         $sqlString .= "            inner join (  ";
    //         $sqlString .= "              select ";
    //         $sqlString .= "                code as code ";
    //         $sqlString .= "                , MAX(apply_term_from) as max_apply_term_from  ";
    //         $sqlString .= "              from ";
    //         $sqlString .= "                ".$this->table_departments."  ";
    //         $sqlString .= "              where ";
    //         $sqlString .= "                apply_term_from <= ? ";
    //         $sqlString .= "                and is_deleted = ? ";
    //         $sqlString .= "              group by ";
    //         $sqlString .= "                code ";
    //         $sqlString .= "            ) as t2  ";
    //         $sqlString .= "            on t2.code = t1.code  ";
    //         $sqlString .= "              and t2.max_apply_term_from =  t1.apply_term_from ";
    //         $sqlString .= "          where ";
    //         $sqlString .= "            t1.kill_from_date > ? ";
    //         $sqlString .= "            and t1.is_deleted = ? ";
    //         $sqlString .= "        ) as t9  ";
    //         $sqlString .= "        on t9.code = t1.department_code  ";
    //         $sqlString .= "        left join ".$this->table_generalcodes." as t10  ";
    //         $sqlString .= "        on t10.code = t1.employment_status  ";
    //         $sqlString .= "          and t10.identification_id = ? ";
    //         $sqlString .= "          and t10.is_deleted = ? ";
    //         $sqlString .= "      where ";
    //         $sqlString .= "        t2.calendars_date between ? and ?  ";
    //         $sqlString .= "        and t3.record_date is null ";
    //         $sqlString .= "    ) ";
    //         $sqlString .= "  ) as t1  ";
    //         $sqlString .= "  inner join ".$this->table_calendar_setting_informations." as t2  ";
    //         $sqlString .= "  on t2.department_code = t1.department_code  ";
    //         $sqlString .= "    and t2.user_code = t1.user_code  ";
    //         $sqlString .= "    and t2.date = t1.current_record_date ";
    //         // 条件
    //         $sqlString .= "where 1 = 1 ";
    //         if(!empty($this->param_employment_status)){
    //             $sqlString .= "and t1.employment_status = ? ";          //　雇用形態指定
    //         }
    //         if(!empty($this->param_department_code)){
    //             $sqlString .= "and t1.department_code = ? ";            //department_code指定
    //         }
    //         if(!empty($this->param_user_code)){
    //             $sqlString .= "and t1.user_code = ? ";                  //user_code指定
    //         } else {
    //             $sqlString .= "and t1.user_management <= ? ";
    //         }
    //         // $sqlString .= "and t2.business_kubun = ? ";
    //         $sqlString .= "and t2.is_deleted = ? ";
    //         $sqlString .= "and (
    //                             t1.hit_alert > ?
    //                             or t1.interval_alaert > ?
    //                             or t1.holiday_alert > ?
    //                         )";
    //         $sqlString .= "order by
    //                             t1.current_record_date desc
    //                             , t1.user_code asc
    //                             , t1.department_code asc";
    //         // バインド
    //         // インターバル時間取得
    //         $apicommon = new ApiCommonController();
    //         $interval_time = $apicommon->getIntevalMinute($this->param_end_date);
    //         $array_setBindingsStr = array();
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.attendance_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.leaving_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.emergency_return_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = Config::get('const.C005.leaving_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.attendance_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.missing_middle_return_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.public_going_out_return_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = Config::get('const.C005.public_going_out_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.attendance_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.missing_middle_return_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.public_going_out_return_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.emergency_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = Config::get('const.C005.public_going_out_return_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.public_going_out_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = Config::get('const.C005.missing_middle_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.attendance_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.missing_middle_return_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.public_going_out_return_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.emergency_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = Config::get('const.C005.missing_middle_return_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.missing_middle_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = Config::get('const.C005.emergency_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.leaving_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.emergency_return_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = Config::get('const.C005.emergency_return_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.missing_middle_return_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.public_going_out_return_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.emergency_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.attendance_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.leaving_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = $interval_time;
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = $this->param_date_from;
    //         $array_setBindingsStr[] = $this->param_date_to;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = $this->param_date_from;
    //         $array_setBindingsStr[] = $this->param_date_to;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = $this->param_date_from;
    //         $array_setBindingsStr[] = $this->param_date_to;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = $this->param_end_date;
    //         $array_setBindingsStr[] = Config::get('const.C017.admin_user');
    //         $array_setBindingsStr[] = 0;
    //         // 月末
    //         $dt = new Carbon($this->param_end_date);
    //         $lastOfMonth = date_format($dt->endOfMonth(), 'Ymd');
    //         $array_setBindingsStr[] = $lastOfMonth;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = $this->param_end_date;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = $lastOfMonth;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C001.value');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.value');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.value');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = $this->param_date_from;
    //         $array_setBindingsStr[] = $this->param_date_to;
    //         $array_setBindingsStr[] = 0;
    //         // union
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = $this->param_end_date;
    //         $array_setBindingsStr[] = Config::get('const.C017.admin_user');;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = $lastOfMonth;
    //         $array_setBindingsStr[] = $this->param_start_date;
    //         $array_setBindingsStr[] = $this->param_end_date;
    //         $array_setBindingsStr[] = Config::get('const.C007.basic');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = $this->param_date_from;
    //         $array_setBindingsStr[] = $this->param_date_to;
    //         $array_setBindingsStr[] = $this->param_end_date;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = $lastOfMonth;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C001.value');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = $this->param_start_date;
    //         $array_setBindingsStr[] = $this->param_end_date;
    //         if(!empty($this->param_employment_status)) {
    //             $array_setBindingsStr[] = $this->param_employment_status;
    //         }
    //         if(!empty($this->param_department_code)) {
    //             $array_setBindingsStr[] = $this->param_department_code;
    //         }
    //         if(!empty($this->param_user_code)) {
    //             $array_setBindingsStr[] = $this->param_user_code;
    //         } else {
    //             $array_setBindingsStr[] = Config::get('const.C017.out_of_user');
    //         }
    //         // $array_setBindingsStr[] = Config::get('const.C007.basic');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $result = DB::select($sqlString, $array_setBindingsStr);

    //         Log::debug(' $result count = '.count($result));
    //         // if (Config::get('const.DEBUG_LEVEL') == Config::get('const.DEBUG_LEVEL_VALUE.DEBUG')) {
    //         //     \Log::debug('sql_debug_log', ['getdailyAlertData' => \DB::getQueryLog()]);
    //         //     \DB::disableQueryLog();
    //         // }
    //     }catch(\PDOException $pe){
    //         Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
    //         Log::error($pe->getMessage());
    //         throw $pe;
    //     }catch(\Exception $e){
    //         Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
    //         Log::error($e->getMessage());
    //         throw $e;
    //     }

    //     return $result;
    // }

    /**
     * 日次警告打刻取得
     * 
     *  $targetdateは適用期間
     *
     * @return void
     */
    // public function getdailyAlertData(){
    //     // if (Config::get('const.DEBUG_LEVEL') == Config::get('const.DEBUG_LEVEL_VALUE.DEBUG')) { \DB::enableQueryLog(); }

    //     try {
    //         $sqlString = "";
    //         $sqlString = "";
    //         $sqlString .= "select ";
    //         $sqlString .= "  t1.user_code as user_code ";
    //         $sqlString .= "  , t1.user_name as user_name ";
    //         $sqlString .= "  , t1.user_management as user_management ";
    //         $sqlString .= "  , t1.employment_status as employment_status ";
    //         $sqlString .= "  , t1.employment_status_name as employment_status_name ";
    //         $sqlString .= "  , t1.department_code as department_code ";
    //         $sqlString .= "  , t1.department_name as department_name ";
    //         $sqlString .= "  , t1.current_record_date as current_record_date ";
    //         $sqlString .= "  , DATE_FORMAT(t1.current_record_time, '%m月%d日 %H:%i')as current_record_time ";
    //         $sqlString .= "  , CONCAT( ";
    //         $sqlString .= "      DATE_FORMAT(t1.current_record_date, '%Y年%m月%d日'), '(', SUBSTRING('月火水木金土日', CONVERT(t2.weekday_kubun + 1, char), ?), ')' ";
    //         $sqlString .= "      ) as record_date_name ";
    //         $sqlString .= "  , t1.current_mode as current_mode ";
    //         $sqlString .= "  , t1.current_mode_name as current_mode_name ";
    //         $sqlString .= "  , t1.before_record_date as before_record_date ";
    //         $sqlString .= "  , DATE_FORMAT(t1.before_record_time, '%m月%d日 %H:%i')as before_record_time ";
    //         $sqlString .= "  , t1.before_mode as before_mode ";
    //         $sqlString .= "  , t1.before_mode_name as before_mode_name ";
    //         $sqlString .= "  , t1.hit_alert as hit_alert ";
    //         $sqlString .= "  , t1.interval_alaert as interval_alaert ";
    //         $sqlString .= "  , t1.holiday_alert as holiday_alert ";
    //         $sqlString .= "  , t2.business_kubun as business_kubun ";
    //         $sqlString .= "  from ";
    //         $sqlString .= "  ( ";
    //         $sqlString .= "    ( ";
    //         $sqlString .= "      select ";
    //         $sqlString .= "          t3.user_code as user_code ";
    //         $sqlString .= "          , t8.name as user_name ";
    //         $sqlString .= "          , t8.management as user_management ";
    //         $sqlString .= "          , t8.employment_status as employment_status ";
    //         $sqlString .= "          , t10.code_name as employment_status_name ";
    //         $sqlString .= "          , t3.department_code as department_code ";
    //         $sqlString .= "          , t9.name as department_name ";
    //         $sqlString .= "          , DATE_FORMAT(t6.record_time, '%Y%m%d') as current_record_date ";
    //         $sqlString .= "          , t6.record_time as current_record_time ";
    //         $sqlString .= "          , t6.mode as current_mode ";
    //         $sqlString .= "          , t12.code_name as current_mode_name ";
    //         // モード
    //         $sqlString .= "          , CASE IFNULL(t6.mode, 0 ) 
    //                 WHEN 0 THEN 0 
    //                 WHEN 1 THEN CASE IFNULL(t5.mode, 0 ) 
    //                   WHEN 0 THEN 0 
    //                   WHEN 2 THEN 0 
    //                   WHEN 32 THEN 0 
    //                   ELSE 1 
    //                   END 
    //                 WHEN 2 THEN CASE IFNULL(t5.mode, 0 ) 
    //                   WHEN 0 THEN 0 
    //                   WHEN 1 THEN 0 
    //                   WHEN 12 THEN 0 
    //                   WHEN 22 THEN 0 
    //                   ELSE 1 
    //                   END 
    //                 WHEN 21 THEN CASE IFNULL(t5.mode, 0 ) 
    //                   WHEN 0 THEN 0 
    //                   WHEN 1 THEN 0 
    //                   WHEN 12 THEN 0 
    //                   WHEN 22 THEN 0 
    //                   WHEN 31 THEN 0 
    //                   ELSE 1 
    //                   END 
    //                 WHEN 22 THEN CASE IFNULL(t5.mode, 0 ) 
    //                   WHEN 0 THEN 0 
    //                   WHEN 21 THEN 0 
    //                   ELSE 1 
    //                   END 
    //                 WHEN 11 THEN CASE IFNULL(t5.mode, 0 ) 
    //                   WHEN 0 THEN 0 
    //                   WHEN 1 THEN 0 
    //                   WHEN 12 THEN 0 
    //                   WHEN 22 THEN 0 
    //                   WHEN 31 THEN 0 
    //                   ELSE 1 
    //                   END 
    //                 WHEN 12 THEN CASE IFNULL(t5.mode, 0 ) 
    //                   WHEN 0 THEN 0 
    //                   WHEN 11 THEN 0 
    //                   ELSE 1 
    //                   END 
    //         $sqlString .= "        WHEN 31 THEN CASE IFNULL(t5.mode,0 ) 
    //                   WHEN 0 THEN 0 
    //                   WHEN 2 THEN 0 
    //                   WHEN 32 THEN 0 
    //                   ELSE 1 
    //                   END 
    //                  WHEN 32 THEN CASE IFNULL(t5.mode, 0 ) 
    //                    WHEN 0 THEN 0 
    //                    WHEN 12 THEN 0 
    //                    WHEN 22 THEN 0 
    //                    WHEN 31 THEN 0 
    //                    ELSE 1 
    //                    END 
    //                  END as hit_alert ";
    //         $sqlString .= "                  , DATE_FORMAT(t5.record_time, '%Y%m%d') as before_record_date
    //               , t5.record_time as before_record_time
    //               , t5.mode as before_mode
    //               , t11.code_name as before_mode_name
    //               , TIMEDIFF(t6.record_time, t5.record_time) as diff_time
    //               , CASE IFNULL(t6.mode, 0 ) 
    //                 WHEN 1 THEN CASE IFNULL(t5.mode, 0 ) 
    //                   WHEN 2 THEN CASE 
    //                     WHEN IFNULL(TIMEDIFF(t6.record_time, t5.record_time), 0 ) < '00:00:00' 
    //                       THEN 1 
    //                     ELSE 0 
    //                     END 
    //                   ELSE 0 
    //                   END 
    //                 ELSE 0 
    //                 END as interval_alaert
    //               , 0 as holiday_alert 
    //             from
    //               work_times as t3 
    //               left join ( 
    //                 select
    //                   t1.user_code as user_code
    //                   , t1.department_code as department_code
    //                   , MAX(t1.record_time) as max_record_time
    //                   , t2.record_time as record_time 
    //                 from
    //                   work_times as t1 
    //                   inner join ( 
    //                     select
    //                       t1.user_code as user_code
    //                       , t1.department_code as department_code
    //                       , t1.record_time as record_time 
    //                     from
    //                       work_times as t1 
    //                     where
    //                       t1.record_time between '2020/04/24 00:00:00' and '2020/05/01 23:59:59' 
    //                       and t1.is_deleted = 0
    //                   ) as t2 
    //                     on t2.user_code = t1.user_code 
    //                     and t2.department_code = t1.department_code 
    //                     and t2.record_time > t1.record_time 
    //                     and t1.is_deleted = 0 
    //                 group by
    //                   t1.user_code
    //                   , t1.department_code
    //                   , t2.record_time
    //               ) as t4 
    //                 on t4.user_code = t3.user_code 
    //                 and t4.department_code = t3.department_code 
    //               inner join ( 
    //                 select
    //                   t1.user_code as user_code
    //                   , t1.department_code as department_code
    //                   , t1.record_time as record_time
    //                   , t1.mode as mode 
    //                 from
    //                   work_times as t1 
    //                 where
    //                   t1.record_time between '2020/04/24 00:00:00' and '2020/05/01 23:59:59' 
    //                   and t1.is_deleted = 0
    //               ) as t5 
    //                 on t5.user_code = t4.user_code 
    //                 and t5.department_code = t4.department_code 
    //                 and t5.record_time = t4.max_record_time 
    //               inner join ( 
    //                 select
    //                   t1.user_code as user_code
    //                   , t1.department_code as department_code
    //                   , t1.record_time as record_time
    //                   , t1.mode as mode 
    //                 from
    //                   work_times as t1 
    //                 where
    //                   t1.record_time between '2020/04/24 00:00:00' and '2020/05/01 23:59:59' 
    //                   and t1.is_deleted = 0
    //               ) as t6 
    //                 on t6.user_code = t4.user_code 
    //                 and t6.department_code = t4.department_code 
    //                 and t6.record_time = t4.record_time 
    //               inner join ( 
    //                 select
    //                   code as code
    //                   , MAX(apply_term_from) as max_apply_term_from 
    //                 from
    //                   users 
    //                 where
    //                   apply_term_from <= '20200501' 
    //                   and role < 10
    //                   and is_deleted = 0 
    //                 group by
    //                   code
    //               ) as t7 
    //                 on t7.code = t3.user_code 
    //               inner join users as t8 
    //                 on t8.code = t7.code 
    //                 and t8.apply_term_from = t7.max_apply_term_from 
    //                 and t8.kill_from_date > '20200531' 
    //                 and t8.is_deleted = 0 
    //               left join ( 
    //                 select
    //                   t1.code as code
    //                   , t1.name as name 
    //                 from
    //                   departments as t1 
    //                   inner join ( 
    //                     select
    //                       code as code
    //                       , MAX(apply_term_from) as max_apply_term_from 
    //                     from
    //                       departments 
    //                     where
    //                       apply_term_from <= '20200501' 
    //                       and is_deleted = 0 
    //                     group by
    //                       code
    //                   ) as t2 
    //                     on t1.code = t2.code 
    //                     and t1.apply_term_from = t2.max_apply_term_from 
    //                 where
    //                   t1.kill_from_date > '20200531' 
    //                   and t1.is_deleted = 0
    //               ) as t9 
    //                 on t9.code = t3.department_code 
    //               left join generalcodes as t10 
    //                 on t10.code = t8.employment_status 
    //                 and t10.identification_id = 'C001' 
    //                 and t10.is_deleted = 0
    //               left join generalcodes as t11 
    //                 on t11.code = t5.mode 
    //                 and t11.identification_id = 'C005' 
    //                 and t11.is_deleted = 0 
    //               left join generalcodes as t12 
    //                 on t12.code = t6.mode 
    //                 and t12.identification_id = 'C005' 
    //                 and t12.is_deleted = 0 
    //             where
    //               t3.record_time between '2020/04/24 00:00:00' and '2020/05/01 23:59:59' 
    //               and t3.is_deleted = 0
    //           ) 
    //           union ( 
    //             select
    //               t1.user_code as user_code
    //               , t1.user_name as user_name
    //               , t1.user_management as user_management
    //               , t1.employment_status as employment_status
    //               , t10.code_name as employment_status_name
    //               , t1.department_code as department_code
    //               , t9.name as department_name
    //               , t2.calendars_date as current_record_date
    //               , null as current_record_time
    //               , null as current_mode
    //               , null as current_mode_name
    //               , 0 as hit_alert
    //               , t3.record_date as before_record_date
    //               , null as before_record_time
    //               , null as before_mode
    //               , null as before_mode_name
    //               , null as diff_time
    //               , 0 as interval_alaert
    //               , 1 as holiday_alert 
    //             from
    //               ( 
    //                 select
    //                   t1.code as user_code
    //                   , t1.name as user_name
    //                   , t1.management as user_management
    //                   , t1.employment_status as employment_status
    //                   , t1.department_code as department_code
    //                   , t1.kill_from_date as kill_from_date 
    //                 from
    //                   users as t1 
    //                   inner join ( 
    //                     select
    //                       code as code
    //                       , MAX(apply_term_from) as max_apply_term_from 
    //                     from
    //                       users 
    //                     where
    //                       apply_term_from <= '20200501' 
    //                       and role <10 
    //                       and is_deleted = 0
    //                     group by
    //                       code
    //                   ) as t3 
    //                     on t3.code = t1.code 
    //                     and t3.max_apply_term_from = t1.apply_term_from 
    //                 where
    //                   t1.kill_from_date > '20200531'
    //               ) as t1 
    //               inner join ( 
    //                 select
    //                   t1.department_code as department_code
    //                   , t1.user_code as user_code
    //                   , DATE_FORMAT(t1.date, '%Y%m%d') as calendars_date 
    //                 from
    //                   calendar_setting_informations as t1 
    //                 where
    //                   t1.date between '20200424' and '20200501' 
    //                   and t1.business_kubun = '1' 
    //                   and holiday_kubun = 0 
    //                   and is_deleted = 0
    //               ) as t2 
    //                 on t2.department_code = t1.department_code 
    //                 and t2.user_code = t1.user_code 
    //               left join ( 
    //                 select
    //                   t1.user_code as user_code
    //                   , t1.department_code as department_code
    //                   , DATE_FORMAT(t1.record_time, '%Y%m%d') as record_date 
    //                 from
    //                   work_times as t1 
    //                 where
    //                   t1.record_time between '2020/04/24 00:00:00' and '2020/05/01 23:59:59'
    //               ) as t3 
    //                 on t3.user_code = t1.user_code 
    //                 and t3.department_code = t1.department_code 
    //                 and t3.record_date = t2.calendars_date 
    //               left join ( 
    //                 select
    //                   t1.code as code
    //                   , t1.name as name 
    //                 from
    //                   departments as t1 
    //                   inner join ( 
    //                     select
    //                       code as code
    //                       , MAX(apply_term_from) as max_apply_term_from 
    //                     from
    //                       departments 
    //                     where
    //                       apply_term_from <= '20200501' 
    //                       and is_deleted = 0 
    //                     group by
    //                       code
    //                   ) as t2 
    //                     on t2.code = t1.code 
    //                     and t2.max_apply_term_from = t1.apply_term_from 
    //                 where
    //                   t1.kill_from_date > '20200531' 
    //                   and t1.is_deleted = 0
    //               ) as t9 
    //                 on t9.code = t1.department_code 
    //               left join generalcodes as t10 
    //                 on t10.code = t1.employment_status 
    //                 and t10.identification_id = 'C001' 
    //                 and t10.is_deleted = 0 
    //             where
    //               t2.calendars_date between '20200424' and '20200501' 
    //               and t3.record_date is null
    //           )
    //         ) as t1 
    //         inner join calendar_setting_informations as t2 
    //           on t2.department_code = t1.department_code 
    //           and t2.user_code = t1.user_code 
    //           and t2.date = t1.current_record_date 
    //       where
    //         1 = 1 
    //         and t1.user_management <= 9 
    //         and t2.is_deleted = 0 
    //         and ( 
    //           t1.hit_alert > 0 
    //           or t1.interval_alaert > 0 
    //           or t1.holiday_alert > 0
    //         ) 
    //       order by
    //         t1.current_record_date desc
    //         , t1.user_code asc
    //         , t1.department_code asc";
    //         // バインド
    //         // インターバル時間取得
    //         $apicommon = new ApiCommonController();
    //         $interval_time = $apicommon->getIntevalMinute($this->param_end_date);
    //         $array_setBindingsStr = array();
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.attendance_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.leaving_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.emergency_return_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = Config::get('const.C005.leaving_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.attendance_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.missing_middle_return_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.public_going_out_return_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = Config::get('const.C005.public_going_out_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.attendance_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.missing_middle_return_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.public_going_out_return_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.emergency_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = Config::get('const.C005.public_going_out_return_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.public_going_out_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = Config::get('const.C005.missing_middle_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.attendance_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.missing_middle_return_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.public_going_out_return_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.emergency_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = Config::get('const.C005.missing_middle_return_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = Config::get('const.C005.missing_middle_time');
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 1;
    //         // $array_setBindingsStr[] = Config::get('const.C005.emergency_time');
    //         // $array_setBindingsStr[] = 0;
    //         // $array_setBindingsStr[] = 0;
    //         // $array_setBindingsStr[] = 0;
    //         // $array_setBindingsStr[] = Config::get('const.C005.leaving_time');
    //         // $array_setBindingsStr[] = 0;
    //         // $array_setBindingsStr[] = Config::get('const.C005.emergency_return_time');
    //         // $array_setBindingsStr[] = 0;
    //         // $array_setBindingsStr[] = 1;
    //         // $array_setBindingsStr[] = Config::get('const.C005.emergency_return_time');
    //         // $array_setBindingsStr[] = 0;
    //         // $array_setBindingsStr[] = 0;
    //         // $array_setBindingsStr[] = 0;
    //         // $array_setBindingsStr[] = Config::get('const.C005.missing_middle_return_time');
    //         // $array_setBindingsStr[] = 0;
    //         // $array_setBindingsStr[] = Config::get('const.C005.public_going_out_return_time');
    //         // $array_setBindingsStr[] = 0;
    //         // $array_setBindingsStr[] = Config::get('const.C005.emergency_time');
    //         // $array_setBindingsStr[] = 0;
    //         // $array_setBindingsStr[] = 1;
    // //         $array_setBindingsStr[] = 0;
    // //         $array_setBindingsStr[] = Config::get('const.C005.attendance_time');
    // //         $array_setBindingsStr[] = 0;
    // //         $array_setBindingsStr[] = Config::get('const.C005.leaving_time');
    // //         $array_setBindingsStr[] = 0;
    // //         $array_setBindingsStr[] = $interval_time;
    // //         $array_setBindingsStr[] = 1;
    // //         $array_setBindingsStr[] = 0;
    // //         $array_setBindingsStr[] = 0;
    // //         $array_setBindingsStr[] = 0;
    // //         $array_setBindingsStr[] = 0;
    // //         $array_setBindingsStr[] = $this->param_date_from;
    // //         $array_setBindingsStr[] = $this->param_date_to;
    // //         $array_setBindingsStr[] = 0;
    // //         $array_setBindingsStr[] = 0;
    // //         $array_setBindingsStr[] = $this->param_date_from;
    // //         $array_setBindingsStr[] = $this->param_date_to;
    // //         $array_setBindingsStr[] = 0;
    // //         $array_setBindingsStr[] = $this->param_date_from;
    // //         $array_setBindingsStr[] = $this->param_date_to;
    // //         $array_setBindingsStr[] = 0;
    // //         $array_setBindingsStr[] = $this->param_end_date;
    // //         $array_setBindingsStr[] = Config::get('const.C017.admin_user');
    // //         $array_setBindingsStr[] = 0;
    // $result = DB::select($sqlString, $array_setBindingsStr);
    //         Log::debug(' $result count = '.count($result));
    //         // if (Config::get('const.DEBUG_LEVEL') == Config::get('const.DEBUG_LEVEL_VALUE.DEBUG')) {
    //         //     \Log::debug('sql_debug_log', ['getdailyAlertData' => \DB::getQueryLog()]);
    //         //     \DB::disableQueryLog();
    //         // }
    //     }catch(\PDOException $pe){
    //         Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
    //         Log::error($pe->getMessage());
    //         throw $pe;
    //     }catch(\Exception $e){
    //         Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
    //         Log::error($e->getMessage());
    //         throw $e;
    //     }

    //     return $result;
    // }


    /**
     * 日次警告打刻取得
     * 
     *  $targetdateは適用期間
     *
     * @return void
     */
    // public function getdailyAlertData($targetdate){
    //     // if (Config::get('const.DEBUG_LEVEL') == Config::get('const.DEBUG_LEVEL_VALUE.DEBUG')) { \DB::enableQueryLog(); }

    //     try {
    //         // 日次労働時間取得SQL作成
    //         // 適用期間日付の取得
    //         $apicommon = new ApiCommonController();
    //         // usersの最大適用開始日付subquery
    //         $subquery5 = $apicommon->getUserApplyTermSubquery($targetdate);
    //         // departmentsの最大適用開始日付subquery
    //         $subquery6 = $apicommon->getDepartmentApplyTermSubquery($targetdate);
    //         // ---------------- unionquery1 打刻ミス ----------------------------
    //         // subquery1    work_times
    //         $subquery1 = DB::table($this->table.' AS t1')
    //             ->select(
    //                 't1.user_code as user_code',
    //                 't1.department_code as department_code');
    //         $subquery1
    //             ->selectRaw(
    //                 'MAX(t1.record_time) as max_record_time');
    //         $subquery1
    //             ->addselect('t2.record_time as record_time');
    //         // subquery2    work_times
    //         $subquery2 = DB::table($this->table.' AS t1')
    //             ->select(
    //                 't1.user_code as user_code',
    //                 't1.department_code as department_code',
    //                 't1.record_time as record_time');
    //         if(!empty($this->param_date_from) && !empty($this->param_date_to)){
    //             $subquery2->where('t1.record_time', '>=', $this->param_date_from);             // 日付範囲指定
    //             $subquery2->where('t1.record_time', '<=', $this->param_date_to);               // 日付範囲指定
    //         }
    //         $subquery2
    //             ->where('t1.is_deleted', '=', 0);
    //         $subquery1
    //             ->JoinSub($subquery2, 't2', function ($join) { 
    //                 $join->on('t2.user_code', '=', 't1.user_code');
    //                 $join->on('t2.department_code', '=', 't1.department_code');
    //                 $join->on('t2.record_time', '>', 't1.record_time')
    //                 ->where('t1.is_deleted', '=', 0);
    //             })
    //             ->groupBy('t1.user_code', 't1.department_code', 't2.record_time');
    //         // subquery3    work_times
    //         $subquery3 = DB::table($this->table.' AS t1')
    //             ->select(
    //                 't1.user_code as user_code',
    //                 't1.department_code as department_code',
    //                 't1.record_time as record_time',
    //                 't1.mode as mode')
    //             ->where('t1.is_deleted', '=', 0);
    //         // subquery4    work_times
    //         $subquery4 = DB::table($this->table.' AS t1')
    //             ->select(
    //                 't1.user_code as user_code',
    //                 't1.department_code as department_code',
    //                 't1.record_time as record_time',
    //                 't1.mode as mode')
    //             ->where('t1.is_deleted', '=', 0);
        
    //         // ---------------- unionquery2 打刻忘れ ----------------------------
    //         // subquery7    users
    //         $subquery7 = DB::table($this->table_users.' AS t1')
    //             ->select(
    //                 't1.code as user_code',
    //                 't1.name as user_name',
    //                 't1.management as user_management',
    //                 't1.employment_status as employment_status',
    //                 't1.department_code as department_code');
    //         $subquery7
    //             ->selectRaw(
    //                 "DATE_FORMAT(".$this->table_calendars.".date,'%Y%m%d') as date");
    //         $subquery7
    //             ->JoinSub($subquery5, 't3', function ($join) { 
    //                 $join->on('t3.code', '=', 't1.code');
    //                 $join->on('t3.max_apply_term_from', '=', 't1.apply_term_from');
    //             });
    //         $subquery7->crossJoin($this->table_calendars); 
    //         $subquery8 = $subquery7->toSql();
    //         // subquery9    work_times
    //         $subquery9 = DB::table($this->table.' AS t1')
    //             ->select(
    //                 't1.user_code as user_code',
    //                 't1.department_code as department_code');
    //         $subquery9
    //             ->selectRaw(
    //                 "DATE_FORMAT(t1.record_time,'%Y%m%d') as record_date");

    //         $unionquery2 = DB::table(DB::raw('('.$subquery8.') AS t1'))
    //             ->select(
    //                 't1.user_code as user_code',
    //                 't1.user_name as user_name',
    //                 't1.user_management as user_management',
    //                 't1.employment_status as employment_status',
    //                 't10.code_name as employment_status_name',
    //                 't1.department_code as department_code',
    //                 't9.name as department_name',
    //                 't1.date as current_record_date');
    //         $unionquery2
    //             ->selectRaw('null as current_record_time')
    //             ->selectRaw('null as current_mode')
    //             ->selectRaw('null as current_mode_name')
    //             ->selectRaw('0 as hit_alert');
    //         $unionquery2
    //             ->addselect('t2.record_date as before_record_date');
    //         $unionquery2
    //             ->selectRaw('null as before_record_time')
    //             ->selectRaw('null as before_mode')
    //             ->selectRaw('null as before_mode_name')
    //             ->selectRaw('null as diff_time')
    //             ->selectRaw('0 as interval_alaert')
    //             ->selectRaw('1 as holiday_alert');
    //         $unionquery2
    //             ->leftJoinSub($subquery9, 't2', function ($join) { 
    //                 $join->on('t2.user_code', '=', 't1.user_code');
    //                 $join->on('t2.department_code', '=', 't1.department_code');
    //                 $join->on('t2.record_date', '=', 't1.date');
    //             })
    //             ->leftJoinSub($subquery6, 't9', function ($join) { 
    //                 $join->on('t9.code', '=', 't1.department_code');
    //             })
    //             ->leftJoin($this->table_generalcodes.' as t10', function ($join) { 
    //                 $join->on('t10.code', '=', 't1.employment_status')
    //                 ->where('t10.identification_id', '=', Config::get('const.C001.value'))
    //                 ->where('t10.is_deleted', '=', 0);
    //             });
        
    //         if(!empty($this->param_start_date) && !empty($this->param_end_date)){
    //             $unionquery2->where('t1.date', '>=', $this->param_start_date);             // 日付範囲指定
    //             $unionquery2->where('t1.date', '<=', $this->param_end_date);               // 日付範囲指定
    //         }
    //         $unionquery2
    //             ->whereNull('t2.record_date');

    //         // ---------------- unionquery ----------------------------
    //         $unionquery1 = DB::table($this->table.' AS t3')
    //             ->select(
    //                 't3.user_code as user_code',
    //                 't8.name as user_name',
    //                 't8.management as user_management',
    //                 't8.employment_status as employment_status',
    //                 't10.code_name as employment_status_name',
    //                 't3.department_code as department_code',
    //                 't9.name as department_name'
    //             );
    //         $unionquery1
    //             ->selectRaw(
    //                 "DATE_FORMAT(t6.record_time,'%Y%m%d') as current_record_date");
    //         $unionquery1
    //             ->addselect(
    //                 "t6.record_time as current_record_time",
    //                 "t6.mode as current_mode",
    //                 "t12.code_name as current_mode_name"
    //             );
    //         $case_sql1 = "CASE IFNULL(t6.mode, 0) ";
    //         $case_sql1 .= "WHEN 0 THEN 0 ";
    //         $case_sql1 .= "WHEN 1 THEN ";
    //         $case_sql1 .= "  CASE IFNULL(t5.mode, 0) ";
    //         $case_sql1 .= "    WHEN 0 THEN 0 ";
    //         $case_sql1 .= "    WHEN 2 THEN 0 ";
    //         $case_sql1 .= "    ELSE 1 ";
    //         $case_sql1 .= "  END ";
    //         $case_sql1 .= "WHEN 2 THEN ";
    //         $case_sql1 .= "  CASE IFNULL(t5.mode, 0) ";
    //         $case_sql1 .= "    WHEN 0 THEN 0 ";
    //         $case_sql1 .= "    WHEN 1 THEN 0 ";
    //         $case_sql1 .= "    WHEN 22 THEN 0 ";
    //         $case_sql1 .= "    WHEN 12 THEN 0 ";
    //         $case_sql1 .= "    ELSE 1 ";
    //         $case_sql1 .= "  END ";
    //         $case_sql1 .= "WHEN 21 THEN ";
    //         $case_sql1 .= "  CASE IFNULL(t5.mode, 0) ";
    //         $case_sql1 .= "    WHEN 0 THEN 0 ";
    //         $case_sql1 .= "    WHEN 1 THEN 0 ";
    //         $case_sql1 .= "    WHEN 22 THEN 0 ";
    //         $case_sql1 .= "    WHEN 12 THEN 0 ";
    //         $case_sql1 .= "    ELSE 1 ";
    //         $case_sql1 .= "  END ";
    //         $case_sql1 .= "WHEN 22 THEN ";
    //         $case_sql1 .= "  CASE IFNULL(t5.mode, 0) ";
    //         $case_sql1 .= "    WHEN 0 THEN 0 ";
    //         $case_sql1 .= "    WHEN 21 THEN 0 ";
    //         $case_sql1 .= "    ELSE 1 ";
    //         $case_sql1 .= "  END ";
    //         $case_sql1 .= "WHEN 11 THEN ";
    //         $case_sql1 .= "  CASE IFNULL(t5.mode, 0) ";
    //         $case_sql1 .= "    WHEN 0 THEN 0 ";
    //         $case_sql1 .= "    WHEN 1 THEN 0 ";
    //         $case_sql1 .= "    WHEN 22 THEN 0 ";
    //         $case_sql1 .= "    WHEN 12 THEN 0 ";
    //         $case_sql1 .= "    ELSE 1 ";
    //         $case_sql1 .= "  END ";
    //         $case_sql1 .= "WHEN 12 THEN ";
    //         $case_sql1 .= "  CASE IFNULL(t5.mode, 0) ";
    //         $case_sql1 .= "    WHEN 0 THEN 0 ";
    //         $case_sql1 .= "    WHEN 11 THEN 0 ";
    //         $case_sql1 .= "    ELSE 1 ";
    //         $case_sql1 .= "  END ";
    //         $case_sql1 .= "END as hit_alert ";
    //         $unionquery1
    //             ->selectRaw($case_sql1)
    //             ->selectRaw("DATE_FORMAT(t5.record_time, '%Y%m%d') as before_record_date");
    //         $unionquery1
    //             ->addselect('t5.record_time as before_record_time')
    //             ->addselect('t5.mode as before_mode')
    //             ->addselect('t11.code_name as before_mode_name');
    //         // インターバル時間取得
    //         $interval_time = $apicommon->getIntevalMinute($targetdate);
    //         $case_sql2 = "CASE IFNULL(t6.mode, 0) ";
    //         $case_sql2 .= "WHEN 1 THEN ";
    //         $case_sql2 .= "  CASE IFNULL(t5.mode, 0) ";
    //         $case_sql2 .= "  WHEN 2 THEN ";
    //         $case_sql2 .= "    CASE ";
    //         $case_sql2 .= "    WHEN IFNULL(TIMEDIFF(t6.record_time, t5.record_time), 0) < '".$interval_time."' THEN 1 ";
    //         $case_sql2 .= "    ELSE 0 ";
    //         $case_sql2 .= "    END ";
    //         $case_sql2 .= "  ELSE 0 ";
    //         $case_sql2 .= "  END ";
    //         $case_sql2 .= "ELSE 0 ";
    //         $case_sql2 .= "END as interval_alaert ";
    //         $unionquery1
    //             ->selectRaw('TIMEDIFF(t6.record_time, t5.record_time) as diff_time')
    //             ->selectRaw($case_sql2)
    //             ->selectRaw('0 as holiday_alert ');
    //         $unionquery1
    //             ->leftJoinSub($subquery1, 't4', function ($join) { 
    //                 $join->on('t4.user_code', '=', 't3.user_code');
    //                 $join->on('t4.department_code', '=', 't3.department_code');
    //             })
    //             ->JoinSub($subquery3, 't5', function ($join) { 
    //                 $join->on('t5.user_code', '=', 't4.user_code');
    //                 $join->on('t5.department_code', '=', 't4.department_code');
    //                 $join->on('t5.record_time', '=', 't4.max_record_time');
    //             })
    //             ->JoinSub($subquery4, 't6', function ($join) { 
    //                 $join->on('t6.user_code', '=', 't4.user_code');
    //                 $join->on('t6.department_code', '=', 't4.department_code');
    //                 $join->on('t6.record_time', '=', 't4.record_time');
    //             })
    //             ->JoinSub($subquery5, 't7', function ($join) { 
    //                 $join->on('t7.code', '=', 't3.user_code');
    //             })
    //             ->Join($this->table_users.' as t8', function ($join) { 
    //                 $join->on('t8.code', '=', 't7.code');
    //                 $join->on('t8.apply_term_from', '=', 't7.max_apply_term_from')
    //                 ->where('t8.is_deleted', '=', 0);
    //             })
    //             ->leftJoinSub($subquery6, 't9', function ($join) { 
    //                 $join->on('t9.code', '=', 't3.department_code');
    //             })
    //             ->leftJoin($this->table_generalcodes.' as t10', function ($join) { 
    //                 $join->on('t10.code', '=', 't8.employment_status')
    //                 ->where('t10.identification_id', '=', Config::get('const.C001.value'))
    //                 ->where('t10.is_deleted', '=', 0);
    //             })
    //             ->leftJoin($this->table_generalcodes.' as t11', function ($join) { 
    //                 $join->on('t11.code', '=', 't5.mode')
    //                 ->where('t11.identification_id', '=', Config::get('const.C005.value'))
    //                 ->where('t11.is_deleted', '=', 0);
    //             })
    //             ->leftJoin($this->table_generalcodes.' as t12', function ($join) { 
    //                 $join->on('t12.code', '=', 't6.mode')
    //                 ->where('t12.identification_id', '=', Config::get('const.C005.value'))
    //                 ->where('t12.is_deleted', '=', 0);
    //             });
    //         if(!empty($this->param_date_from) && !empty($this->param_date_to)){
    //             $unionquery1->where('t3.record_time', '>=', $this->param_date_from);             // 日付範囲指定
    //             $unionquery1->where('t3.record_time', '<=', $this->param_date_to);               // 日付範囲指定
    //         }
    //         $unionquery1
    //             ->where('t3.is_deleted', '=', 0)
    //             ->union($unionquery2);

    //         $unionquery1_sql= $unionquery1->toSql();

    //         // ---------------- mainquery ----------------------------
    //         $mainquery = DB::table(DB::raw('('.$unionquery1_sql.') as t1'))
    //             ->select(
    //                 't1.user_code as user_code',
    //                 't1.user_name as user_name',
    //                 't1.user_management as user_management',
    //                 't1.employment_status as employment_status',
    //                 't1.employment_status_name as employment_status_name',
    //                 't1.department_code as department_code',
    //                 't1.department_name as department_name',
    //                 't1.current_record_date as current_record_date',
    //                 't1.current_record_time as current_record_time'
    //             );
    //         $mainquery
    //             ->selectRaw(
    //                 "CONCAT(DATE_FORMAT(t1.current_record_date, '%Y年%m月%d日'), '(', SUBSTRING('月火水木金土日', CONVERT(t2.weekday_kubun + 1, char), 1), ')') as record_date_name"
    //             );
    //         $mainquery
    //             ->addselect(
    //                 't1.current_mode as current_mode',
    //                 't1.current_mode_name as current_mode_name',
    //                 't1.before_record_date as before_record_date',
    //                 't1.before_record_time as before_record_time',
    //                 't1.before_mode as before_mode',
    //                 't1.before_mode_name as before_mode_name',
    //                 't1.hit_alert as hit_alert',
    //                 't1.interval_alaert as interval_alaert',
    //                 't1.holiday_alert as holiday_alert',
    //                 't2.business_kubun as business_kubun');
    //         // ここのjoinで->where('t2.business_kubun', '=', 1)としたいがバインド変数にうまくバインドされないため、予備もとで判断する
    //         $mainquery
    //             ->leftJoin($this->table_calendars.' as t2', function ($join) { 
    //                 $join->on('t2.department_code', '=', 't1.department_code');
    //                 $join->on('t2.user_code', '=', 't1.user_code');
    //                 $join->on('t2.date', '=', 't1.current_record_date');
    //             });
    //         if(!empty($this->param_employment_status)){
    //             $mainquery->where('t1.employment_status', $this->param_employment_status);      //　雇用形態指定
    //         }
    //         if(!empty($this->param_department_code)){
    //             $mainquery->where('t1.department_code', $this->param_department_code);          //department_code指定
    //         }
    //         if(!empty($this->param_user_code)){
    //             $mainquery->where('t1.user_code', $this->param_user_code);                           //user_code指定
    //         } else {
    //             $mainquery->where('t1.user_management','<',Config::get('const.C017.out_of_user'));
    //         }
    //         $mainquery
    //             ->Where('t2.business_kubun', '=', Config::get('const.C007.basic'))
    //             ->Where('t2.is_deleted', '=', 0)
    //             ->where(function ($query) {
    //                 $query->where('t1.hit_alert', '>', 0)
    //                     ->orWhere('t1.interval_alaert', '>', 0)
    //                     ->orWhere('t1.holiday_alert', '>', '0');
    //             })
    //             ->orderBy('t1.current_record_date', 'asc')
    //             ->orderBy('t1.user_code', 'asc')
    //             ->orderBy('t1.department_code_', 'asc');
                
    //         $array_setBindingsStr = array();
    //         $cnt = 0;
    //         if(!empty($this->param_date_from) && !empty($this->param_date_to)){
    //             $cnt += 1;
    //             $array_setBindingsStr[] = array($cnt=>$this->param_date_from);
    //             $cnt += 1;
    //             $array_setBindingsStr[] = array($cnt=>$this->param_date_to);
    //         }
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>0);
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>0);
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>0);
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>0);
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>$targetdate);
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>10);
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>0);
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>0);
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>$targetdate);
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>$targetdate);
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>0);
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>0);
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>Config::get('const.C001.value'));
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>0);
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>Config::get('const.C005.value'));
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>0);
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>Config::get('const.C005.value'));
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>0);
    //         if(!empty($this->param_date_from) && !empty($this->param_date_to)){
    //             $cnt += 1;
    //             $array_setBindingsStr[] = array($cnt=>$this->param_date_from);
    //             $cnt += 1;
    //             $array_setBindingsStr[] = array($cnt=>$this->param_date_to);
    //         }
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>0);
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>$targetdate);
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>Config::get('const.C017.admin_user'));
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>0);
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>$targetdate);
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>$targetdate);
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>0);
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>0);
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>Config::get('const.C001.value'));
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>0);
    //         if(!empty($this->param_start_date) && !empty($this->param_end_date)){
    //             $cnt += 1;
    //             $array_setBindingsStr[] = array($cnt=>$this->param_start_date);
    //             $cnt += 1;
    //             $array_setBindingsStr[] = array($cnt=>$this->param_end_date);
    //         }
    //         if(!empty($this->param_employment_status)){
    //             $cnt += 1;
    //             $array_setBindingsStr[] = array($cnt=>$this->param_employment_status);
    //         }
    //         if(!empty($this->param_department_code)){
    //             $cnt += 1;
    //             $array_setBindingsStr[] = array($cnt=>$this->param_department_code);
    //         }
    //         if(!empty($this->param_user_code)){
    //             $cnt += 1;
    //             $array_setBindingsStr[] = array($cnt=>$this->param_user_code);
    //         } else {
    //             $cnt += 1;
    //             $array_setBindingsStr[] = array($cnt=>Config::get('const.C017.out_of_user'));
    //         }
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>Config::get('const.C007.basic'));
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>0);
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>0);
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>0);
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array($cnt=>0);

    //         if (count($array_setBindingsStr) > 0) {
    //             $mainquery->setBindings($array_setBindingsStr);
    //         }

    //         $result = $mainquery->get();


    //         // if (Config::get('const.DEBUG_LEVEL') == Config::get('const.DEBUG_LEVEL_VALUE.DEBUG')) {
    //         //     \Log::debug('sql_debug_log', ['getdailyAlertData' => \DB::getQueryLog()]);
    //         //     \DB::disableQueryLog();
    //         // }
    //     }catch(\PDOException $pe){
    //         Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
    //         Log::error($pe->getMessage());
    //         throw $pe;
    //     }catch(\Exception $e){
    //         Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
    //         Log::error($e->getMessage());
    //         throw $e;
    //     }

    //     return $result;
    // }
    
}
