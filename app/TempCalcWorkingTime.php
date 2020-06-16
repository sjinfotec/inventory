<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

/**
 * テーブル：temp日次集計タイムレコード（temp_calc_workingtimes）のモデル
 *      アクセサ定義
 * @author      o.shindo
 * @version     1.00    20190629
*/
class TempCalcWorkingTime extends Model
{
    protected $table = 'temp_calc_workingtimes';
    protected $table_working_timetables = 'working_timetables';
    protected $table_generalcodes = 'generalcodes';
    protected $guarded = array('id');

    //--------------- 項目属性 -----------------------------------

    private $working_date;                      // 日付
    private $employment_status;                 // 雇用形態
    private $department_code;                   // 部署ID
    private $user_code;                         // ユーザー
    private $seq;                               // 順位
    private $employment_status_name;            // 雇用形態名称
    private $department_name;                   // 部署名称
    private $user_name;                         // ユーザー名称
    private $working_timetable_no;              // タイムテーブルNo
    private $working_timetable_name;            // タイムテーブル名称
    private $working_timetable_from_time;       // タイムテーブル開始時刻
    private $working_timetable_to_time;         // タイムテーブル終了時刻
    private $shift_no;                          // シフトタイムテーブルNo
    private $shift_name;                        // シフトタイムテーブル名称
    private $shift_from_time;                   // シフトタイムテーブル開始時刻
    private $shift_to_time;                     // シフトタイムテーブル終了時刻
    private $mode;                              // 打刻モード
    private $record_datetime;                   // 打刻日時
    private $record_year;                       // 打刻年
    private $record_month;                      // 打刻月
    private $record_date;                       // 打刻年月日
    private $record_time;                       // 打刻時刻
    private $working_status;                    // 勤務状態
    private $working_status_name;               // 勤務状態名称
    private $note;                              // メモ
    private $late;                              // 遅刻有無
    private $leave_early;                       // 早退有無
    private $current_calc;                      // 当日計算有無
    private $to_be_confirmed;                   // 要確認有無
    private $weekday_kubun;                     // 曜日区分
    private $weekday_name;                      // 曜日名称
    private $business_kubun;                    // 営業日区分
    private $business_name;                     // 営業日名称
    private $holiday_kubun;                     // 休暇区分
    private $holiday_name;                      // 休暇名称
    private $closing;                           // 締日
    private $uplimit_time;                      // 上限残業時間
    private $statutory_uplimit_time;            // 法定上限残業時間
    private $time_unit;                         // 時間単位
    private $time_rounding;                     // 時間の丸め
    private $max_3month_total;                  // ３ヶ月累計
    private $max_6month_total;                  // ６ヶ月累計
    private $max_12month_total;                 // １年間累計
    private $beginning_month;                   // 期首月
    private $year;                              // 年
    private $pattern;                           // 打刻パターン
    private $check_result;                      // 打刻チェック結果
    private $check_max_times;                   // 打刻回数最大チェック結果
    private $check_interval;                    // インターバルチェック結果
    private $work_times_id;                     // 打刻時刻テーブルID
    private $editor_department_code;            // 編集部署コード
    private $editor_department_name;            // 編集部署名
    private $editor_user_code;                  // 編集ユーザーコード
    private $editor_user_name;                  // 編集ユーザー名
    private $positions;                         // 位置情報
    private $systemdate;

    // 日付
    public function getWorkingdateAttribute()
    {
        return $this->working_date;
    }

    public function setWorkingdateAttribute($value)
    {
        $this->working_date = $value;
    }


    // 雇用形態
    public function getEmploymentstatusAttribute()
    {
        return $this->employment_status;
    }

    public function setEmploymentstatusAttribute($value)
    {
        $this->employment_status = $value;
    }


    // 部署ID
    public function getDepartmentcodeAttribute()
    {
        return $this->department_code;
    }

    public function setDepartmentcodeAttribute($value)
    {
        $this->department_code = $value;
    }


    // ユーザー
    public function getUsercodeAttribute()
    {
        return $this->user_code;
    }

    public function setUsercodeAttribute($value)
    {
        $this->user_code = $value;
    }


    // 順位
    public function getSeqAttribute()
    {
        return $this->seq;
    }

    public function setSeqAttribute($value)
    {
        $this->seq = $value;
    }


    // 雇用形態名称
    public function getEmploymentstatusnameAttribute()
    {
        return $this->employment_status_name;
    }

    public function setEmploymentstatusnameAttribute($value)
    {
        $this->employment_status_name = $value;
    }


    // 部署名称
    public function getDepartmentnameAttribute()
    {
        return $this->department_name;
    }

    public function setDepartmentnameAttribute($value)
    {
        $this->department_name = $value;
    }


    // ユーザー名称
    public function getUsernameAttribute()
    {
        return $this->user_name;
    }

    public function setUsernameAttribute($value)
    {
        $this->user_name = $value;
    }


    // タイムテーブルNo
    public function getWorkingtimetablenoAttribute()
    {
        return $this->working_timetable_no;
    }

    public function setWorkingtimetablenoAttribute($value)
    {
        $this->working_timetable_no = $value;
    }


    // タイムテーブル名称
    public function getWorkingtimetablenameAttribute()
    {
        return $this->working_timetable_name;
    }

    public function setWorkingtimetablenameAttribute($value)
    {
        $this->working_timetable_name = $value;
    }


    // タイムテーブル開始時刻
    public function getWorkingtimetablefromtimeAttribute()
    {
        return $this->working_timetable_from_time;
    }

    public function setWorkingtimetablefromtimeAttribute($value)
    {
        $this->working_timetable_from_time = $value;
    }


    // タイムテーブル終了時刻
    public function getWorkingtimetabletotimeAttribute()
    {
        return $this->working_timetable_to_time;
    }

    public function setWorkingtimetabletotimeAttribute($value)
    {
        $this->working_timetable_to_time = $value;
    }


    // シフトタイムテーブルNo
    public function getShiftnoAttribute()
    {
        return $this->shift_no;
    }

    public function setShiftnoAttribute($value)
    {
        $this->shift_no = $value;
    }


    // シフトタイムテーブル名称
    public function getShiftnameAttribute()
    {
        return $this->shift_name;
    }

    public function setShiftnameAttribute($value)
    {
        $this->shift_name = $value;
    }


    // シフトタイムテーブル開始時刻
    public function getShiftfromtimeAttribute()
    {
        return $this->shift_from_time;
    }

    public function setShiftfromtimeAttribute($value)
    {
        $this->shift_from_time = $value;
    }


    // シフトタイムテーブル終了時刻
    public function getShifttotimeAttribute()
    {
        return $this->shift_to_time;
    }

    public function setShifttotimeAttribute($value)
    {
        $this->shift_to_time = $value;
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


    // 打刻日時
    public function getRecorddatetimeAttribute()
    {
        return $this->record_datetime;
    }

    public function setRecorddatetimeAttribute($value)
    {
        $this->record_datetime = $value;
    }


    // 打刻年
    public function getRecordyearAttribute()
    {
        return $this->record_year;
    }

    public function setRecordyearAttribute($value)
    {
        $this->record_year = $value;
    }


    // 打刻月
    public function getRecordmonthAttribute()
    {
        return $this->record_month;
    }

    public function setRecordmonthAttribute($value)
    {
        $this->record_month = $value;
    }


    // 打刻年月日
    public function getRecorddateAttribute()
    {
        return $this->record_date;
    }

    public function setRecorddateAttribute($value)
    {
        $this->record_date = $value;
    }


    // 打刻時刻
    public function getRecordtimeAttribute()
    {
        return $this->record_time;
    }

    public function setRecordtimeAttribute($value)
    {
        $this->record_time = $value;
    }


    // 勤務状態
    public function getWorkingstatusAttribute()
    {
        return $this->working_status;
    }

    public function setWorkingstatusAttribute($value)
    {
        $this->working_status = $value;
    }


    // 勤務状態名称
    public function getWorkingstatusnameAttribute()
    {
        return $this->working_status_name;
    }

    public function setWorkingstatusnameAttribute($value)
    {
        $this->working_status_name = $value;
    }


    // メモ
    public function getNoteAttribute()
    {
        return $this->note;
    }

    public function setNoteAttribute($value)
    {
        $this->note = $value;
    }


    // 遅刻有無
    public function getLateAttribute()
    {
        return $this->late;
    }

    public function setLateAttribute($value)
    {
        $this->late = $value;
    }


    // 早退有無
    public function getLeaveearlyAttribute()
    {
        return $this->leave_early;
    }

    public function setLeaveearlyAttribute($value)
    {
        $this->leave_early = $value;
    }


    // 当日計算有無
    public function getCurrentcalcAttribute()
    {
        return $this->current_calc;
    }

    public function setCurrentcalcAttribute($value)
    {
        $this->current_calc = $value;
    }


    // 要確認有無
    public function getTobeconfirmedAttribute()
    {
        return $this->to_be_confirmed;
    }

    public function setTobeconfirmedAttribute($value)
    {
        $this->to_be_confirmed = $value;
    }


    // 曜日区分
    public function getWeekdaykubunAttribute()
    {
        return $this->weekday_kubun;
    }

    public function setWeekdaykubunAttribute($value)
    {
        $this->weekday_kubun = $value;
    }


    // 曜日名称
    public function getWeekdaynameAttribute()
    {
        return $this->weekday_name;
    }

    public function setWeekdaynameAttribute($value)
    {
        $this->weekday_name = $value;
    }


    // 営業日区分
    public function getBusinesskubunAttribute()
    {
        return $this->business_kubun;
    }

    public function setBusinesskubunAttribute($value)
    {
        $this->business_kubun = $value;
    }


    // 営業日名称
    public function getBusinessnameAttribute()
    {
        return $this->business_name;
    }

    public function setBusinessnameAttribute($value)
    {
        $this->business_name = $value;
    }


    // 休暇区分
    public function getHolidaykubunAttribute()
    {
        return $this->holiday_kubun;
    }

    public function setHolidaykubunAttribute($value)
    {
        $this->holiday_kubun = $value;
    }


    // 休暇名称
    public function getHolidaynameAttribute()
    {
        return $this->holiday_name;
    }

    public function setHolidaynameAttribute($value)
    {
        $this->holiday_name = $value;
    }


    // 締日
    public function getClosingAttribute()
    {
        return $this->closing;
    }

    public function setClosingAttribute($value)
    {
        $this->closing = $value;
    }


    // 上限残業時間
    public function getUplimittimeAttribute()
    {
        return $this->uplimit_time;
    }

    public function setUplimittimeAttribute($value)
    {
        $this->uplimit_time = $value;
    }


    // 法定上限残業時間
    public function getStatutoryuplimittimeAttribute()
    {
        return $this->statutory_uplimit_time;
    }

    public function setStatutoryuplimittimeAttribute($value)
    {
        $this->statutory_uplimit_time = $value;
    }


    // 時間単位
    public function getTimeunitAttribute()
    {
        return $this->time_unit;
    }

    public function setTimeunitAttribute($value)
    {
        $this->time_unit = $value;
    }


    // 時間の丸め
    public function getTimeroundingAttribute()
    {
        return $this->time_rounding;
    }

    public function setTimeroundingAttribute($value)
    {
        $this->time_rounding = $value;
    }


    // ３ヶ月累計
    public function getMax3MonthtotalAttribute()
    {
        return $this->max_3month_total;
    }

    public function setMax3MonthtotalAttribute($value)
    {
        $this->max_3month_total = $value;
    }


    // ６ヶ月累計
    public function getMax6MonthtotalAttribute()
    {
        return $this->max_6month_total;
    }

    public function setMax6MonthtotalAttribute($value)
    {
        $this->max_6month_total = $value;
    }


    // １年間累計
    public function getMax12MonthtotalAttribute()
    {
        return $this->max_12month_total;
    }

    public function setMax12MonthtotalAttribute($value)
    {
        $this->max_12month_total = $value;
    }


    // 期首月
    public function getBeginningmonthAttribute()
    {
        return $this->beginning_month;
    }

    public function setBeginningmonthAttribute($value)
    {
        $this->beginning_month = $value;
    }


    // 年
    public function getYearAttribute()
    {
        return $this->year;
    }

    public function setYearAttribute($value)
    {
        $this->year = $value;
    }


    // 打刻パターン
    public function getPatternAttribute()
    {
        return $this->pattern;
    }

    public function setPatternAttribute($value)
    {
        $this->pattern = $value;
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
    public function getCheckmaxtimesAttribute()
    {
        return $this->check_max_times;
    }

    public function setCheckmaxtimesAttribute($value)
    {
        $this->check_max_times = $value;
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

    // 位置情報
    public function getPositionsAttribute()
    {
        return $this->positions;
    }

    public function setPositionsAttribute($value)
    {
        $this->positions = $value;
    }

    // 打刻時刻テーブルID
    public function getWorktimesidAttribute()
    {
        return $this->work_times_id;
    }

    public function setWorktimesidAttribute($value)
    {
        $this->work_times_id = $value;
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

    // 編集部署名
    public function getEditordepartmentnameAttribute()
    {
        return $this->editor_department_name;
    }

    public function setEditordepartmentnameAttribute($value)
    {
        $this->editor_department_name = $value;
    }

    // 編集ユーザー名
    public function getEditorusernameAttribute()
    {
        return $this->editor_user_name;
    }

    public function setEditorusernameAttribute($value)
    {
        $this->editor_user_name = $value;
    }


    public function getSystemDateAttribute()
    {
        return $this->systemdate;
    }

    public function setSystemDateAttribute($value)
    {
        $this->systemdate = $value;
    }


    //--------------- パラメータ項目属性 -----------------------------------

    private $param_date_from;                   // 開始日付（00:00:00から）
    private $param_date_to;                     // 終了日付（23:59:59まで）
    private $param_employment_status;           // 雇用形態
    private $param_department_code;             // 部署
    private $param_user_code;                   // ユーザー
    private $array_record_time;                 // 日付範囲配列
    private $massegedata;                       // メッセージ


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


    // 終了日付（23:59:59まで）
    public function getParamdatetoAttribute()
    {
        return $this->param_date_to;
    }

    public function setParamdatetoAttribute($value)
    {
        $date = date_create($this->param_date_to);
        $this->param_date_to = $date->format('Y/m/d').' 23:59:59';
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


    // --------------------- メソッド ------------------------------------------------------

    /**
     * 取得
     *
     * @return void
     */
    public function getTempCalcWorkingtime(){
        try{
            $mainquery = DB::table($this->table.' AS t1')
            ->select(
                't1.working_date as working_date',
                't1.employment_status as employment_status',
                't1.department_code as department_code',
                't1.user_code as user_code',
                't1.seq as seq',
                't1.employment_status_name as employment_status_name',
                't1.department_name as department_name',
                't1.user_name as user_name',
                't1.working_timetable_no as working_timetable_no',
                't1.working_timetable_name as working_timetable_name',
                't1.working_timetable_from_time as working_timetable_from_time',
                't1.working_timetable_to_time as working_timetable_to_time',
                't1.shift_no as shift_no',
                't1.shift_name as shift_name',
                't1.shift_from_time as shift_from_time',
                't1.shift_to_time as shift_to_time',
                't1.mode as mode',
                't1.record_datetime as record_datetime',
                't1.record_year as record_year',
                't1.record_month as record_month',
                't1.record_date as record_date',
                't1.record_time as record_time',
                't1.working_status as working_status',
                't1.working_status_name as working_status_name',
                't1.note as note',
                't1.late as late',
                't1.leave_early as leave_early',
                't1.current_calc as current_calc',
                't1.to_be_confirmed as to_be_confirmed',
                't1.weekday_kubun as weekday_kubun',
                't1.weekday_name as weekday_name',
                't1.business_kubun as business_kubun',
                't1.business_name as business_name',
                't1.holiday_kubun as holiday_kubun',
                't1.holiday_name as holiday_name',
                't2.use_free_item as use_free_item',
                't2.description as holiday_description',
                't1.closing as closing',
                't1.uplimit_time as uplimit_time',
                't1.statutory_uplimit_time as statutory_uplimit_time',
                't1.time_unit as time_unit',
                't1.time_rounding as time_rounding',
                't1.max_3month_total as max_3month_total',
                't1.max_6month_total as max_6month_total',
                't1.max_12month_total as max_12month_total',
                't1.beginning_month as beginning_month',
                't1.year as year',
                't1.pattern as pattern',
                't1.check_result as check_result',
                't1.check_max_times as check_max_times',
                't1.check_interval as check_interval',
                't1.work_times_id as work_times_id',
                't1.editor_department_code as editor_department_code',
                't1.editor_department_name as editor_department_name',
                't1.editor_user_code as editor_user_code',
                't1.editor_user_name as editor_user_name'
            );
            $mainquery
                ->selectRaw('X(t1.positions) as x_positions')
                ->selectRaw('Y(t1.positions) as y_positions');
            $mainquery
                ->leftJoin($this->table_generalcodes.' as t2', function ($join) { 
                    $join->on('t2.code', '=', 't1.holiday_kubun')
                    ->where('t2.identification_id', '=', Config::get('const.C013.value'))
                    ->where('t1.is_deleted', '=', 0)
                    ->where('t2.is_deleted', '=', 0);
                });

            if(!empty($this->param_date_from) && !empty($this->param_date_to)){
                $date = date_create($this->param_date_from);
                $this->param_date_from = $date->format('Ymd');
                $date = date_create($this->param_date_to);
                $this->param_date_to = $date->format('Ymd');
                $mainquery->where('t1.working_date', '>=', $this->param_date_from);             // 日付範囲指定
                $mainquery->where('t1.working_date', '<=', $this->param_date_to);               // 日付範囲指定
            }
            if(!empty($this->param_employment_status)){
                $mainquery->where('t1.employment_status', $this->param_employment_status);      //　雇用形態指定
            }
            if(!empty($this->param_department_code)){
                $mainquery->where('t1.department_code', $this->param_department_code);          // department_code指定
            }
            if(!empty($this->param_user_code)){
                $mainquery->where('t1.user_code', $this->param_user_code);                      // user_code指定
            }
            
            $results = $mainquery
                ->orderBy('t1.working_date','asc')
                ->orderBy('t1.department_code','asc')
                ->orderBy('t1.user_code','asc')
                ->orderBy('t1.seq','asc')
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
        return $results;
    }

    /**
     * 登録
     *
     * @return void
     */
    public function insertTempCalcWorkingtime(){
        try{
            if (isset($this->positions)) {
                DB::table($this->table)->insert(
                    [
                        'working_date' => $this->working_date,
                        'employment_status' => $this->employment_status,
                        'department_code' => $this->department_code,
                        'user_code' => $this->user_code,
                        'seq' => $this->seq,
                        'employment_status_name' => $this->employment_status_name,
                        'department_name' => $this->department_name,
                        'user_name' => $this->user_name,
                        'working_timetable_no' => $this->working_timetable_no,
                        'working_timetable_name' => $this->working_timetable_name,
                        'working_timetable_from_time' => (strlen($this->working_timetable_from_time) > 0) ? $this->working_timetable_from_time : null,
                        'working_timetable_to_time' => (strlen($this->working_timetable_to_time) > 0) ? $this->working_timetable_to_time : null,
                        'shift_no' => $this->shift_no,
                        'shift_name' => $this->shift_name,
                        'shift_from_time' => (strlen($this->shift_from_time) > 0) ? $this->shift_from_time : null,
                        'shift_to_time' => (strlen($this->shift_to_time) > 0) ? $this->shift_to_time : null,
                        'mode' => $this->mode,
                        'record_datetime' => (strlen($this->record_datetime) > 0) ? $this->record_datetime : null,
                        'record_year' => $this->record_year,
                        'record_month' => $this->record_month,
                        'record_date' => $this->record_date,
                        'record_time' => $this->record_time,
                        'working_status' => $this->working_status,
                        'working_status_name' => $this->working_status_name,
                        'note' => $this->note,
                        'late' => $this->late,
                        'leave_early' => $this->leave_early,
                        'current_calc' => $this->current_calc,
                        'to_be_confirmed' => $this->to_be_confirmed,
                        'weekday_kubun' => $this->weekday_kubun,
                        'weekday_name' => $this->weekday_name,
                        'business_kubun' => $this->business_kubun,
                        'business_name' => $this->business_name,
                        'holiday_kubun' => $this->holiday_kubun,
                        'holiday_name' => $this->holiday_name,
                        'closing' => $this->closing,
                        'uplimit_time' => $this->uplimit_time,
                        'statutory_uplimit_time' => $this->statutory_uplimit_time,
                        'time_unit' => $this->time_unit,
                        'time_rounding' => $this->time_rounding,
                        'max_3month_total' => $this->max_3month_total,
                        'max_6month_total' => $this->max_6month_total,
                        'max_12month_total' => $this->max_12month_total,
                        'beginning_month' => $this->beginning_month,
                        'year' => $this->year,
                        'pattern' => $this->pattern,
                        'check_result' => $this->check_result,
                        'check_max_times' => $this->check_max_times,
                        'check_interval' => $this->check_interval,
                        'positions' => DB::raw("(GeomFromText('POINT(".$this->positions.")'))"),
                        'work_times_id' => $this->work_times_id,
                        'editor_department_code' => $this->editor_department_code,
                        'editor_department_name' => $this->editor_department_name,
                        'editor_user_code' => $this->editor_user_code,
                        'editor_user_name' => $this->editor_user_name,
                        'created_at'=>$this->systemdate
                    ]
                );
            } else {
                DB::table($this->table)->insert(
                    [
                        'working_date' => $this->working_date,
                        'employment_status' => $this->employment_status,
                        'department_code' => $this->department_code,
                        'user_code' => $this->user_code,
                        'seq' => $this->seq,
                        'employment_status_name' => $this->employment_status_name,
                        'department_name' => $this->department_name,
                        'user_name' => $this->user_name,
                        'working_timetable_no' => $this->working_timetable_no,
                        'working_timetable_name' => $this->working_timetable_name,
                        'working_timetable_from_time' => (strlen($this->working_timetable_from_time) > 0) ? $this->working_timetable_from_time : null,
                        'working_timetable_to_time' => (strlen($this->working_timetable_to_time) > 0) ? $this->working_timetable_to_time : null,
                        'shift_no' => $this->shift_no,
                        'shift_name' => $this->shift_name,
                        'shift_from_time' => (strlen($this->shift_from_time) > 0) ? $this->shift_from_time : null,
                        'shift_to_time' => (strlen($this->shift_to_time) > 0) ? $this->shift_to_time : null,
                        'mode' => $this->mode,
                        'record_datetime' => (strlen($this->record_datetime) > 0) ? $this->record_datetime : null,
                        'record_year' => $this->record_year,
                        'record_month' => $this->record_month,
                        'record_date' => $this->record_date,
                        'record_time' => $this->record_time,
                        'working_status' => $this->working_status,
                        'working_status_name' => $this->working_status_name,
                        'note' => $this->note,
                        'late' => $this->late,
                        'leave_early' => $this->leave_early,
                        'current_calc' => $this->current_calc,
                        'to_be_confirmed' => $this->to_be_confirmed,
                        'weekday_kubun' => $this->weekday_kubun,
                        'weekday_name' => $this->weekday_name,
                        'business_kubun' => $this->business_kubun,
                        'business_name' => $this->business_name,
                        'holiday_kubun' => $this->holiday_kubun,
                        'holiday_name' => $this->holiday_name,
                        'closing' => $this->closing,
                        'uplimit_time' => $this->uplimit_time,
                        'statutory_uplimit_time' => $this->statutory_uplimit_time,
                        'time_unit' => $this->time_unit,
                        'time_rounding' => $this->time_rounding,
                        'max_3month_total' => $this->max_3month_total,
                        'max_6month_total' => $this->max_6month_total,
                        'max_12month_total' => $this->max_12month_total,
                        'beginning_month' => $this->beginning_month,
                        'year' => $this->year,
                        'pattern' => $this->pattern,
                        'check_result' => $this->check_result,
                        'check_max_times' => $this->check_max_times,
                        'check_interval' => $this->check_interval,
                        'positions' => null,
                        'work_times_id' => $this->work_times_id,
                        'editor_department_code' => $this->editor_department_code,
                        'editor_department_name' => $this->editor_department_name,
                        'editor_user_code' => $this->editor_user_code,
                        'editor_user_name' => $this->editor_user_name,
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
     * 削除
     *
     * @return void
     */
    public function delTempCalcWorkingtime(){
        try{
            $mainquery = DB::table($this->table)->truncate();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_delete_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_delete_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

}
