<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * テーブル：temp日次集計タイムレコード（temp_calc_workingtimes）のモデル
 *      アクセサ定義
 * @author      o.shindo
 * @version     1.00    20190629
*/
class TempCalcWorkingTime extends Model
{
    protected $table = 'temp_calc_workingtimes';
    protected $guarded = array('id');

    //--------------- 項目属性 -----------------------------------

    private $working_date;                 // 日付

    // 日付
    public function getWorkingdateAttribute()
    {
        return $this->working_date;
    }

    public function setWorkingdateAttribute($value)
    {
        $this->working_date = $value;
    }

    private $employment_status;                 // 雇用形態

    // 雇用形態
    public function getEmploymentstatusAttribute()
    {
        return $this->employment_status;
    }

    public function setEmploymentstatusAttribute($value)
    {
        $this->employment_status = $value;
    }

    private $department_id;                 // 部署ID

    // 部署ID
    public function getDepartmentidAttribute()
    {
        return $this->department_id;
    }

    public function setDepartmentidAttribute($value)
    {
        $this->department_id = $value;
    }

    private $user_code;                 // ユーザー

    // ユーザー
    public function getUsercodeAttribute()
    {
        return $this->user_code;
    }

    public function setUsercodeAttribute($value)
    {
        $this->user_code = $value;
    }

    private $employment_status_name;                 // 雇用形態名称

    // 雇用形態名称
    public function getEmploymentstatusnameAttribute()
    {
        return $this->employment_status_name;
    }

    public function setEmploymentstatusnameAttribute($value)
    {
        $this->employment_status_name = $value;
    }

    private $department_name;                 // 部署名称

    // 部署名称
    public function getDepartmentnameAttribute()
    {
        return $this->department_name;
    }

    public function setDepartmentnameAttribute($value)
    {
        $this->department_name = $value;
    }

    private $user_name;                 // ユーザー名称

    // ユーザー名称
    public function getUsernameAttribute()
    {
        return $this->user_name;
    }

    public function setUsernameAttribute($value)
    {
        $this->user_name = $value;
    }

    private $working_timetable_no;                 // タイムテーブルNo

    // タイムテーブルNo
    public function getWorkingtimetablenoAttribute()
    {
        return $this->working_timetable_no;
    }

    public function setWorkingtimetablenoAttribute($value)
    {
        $this->working_timetable_no = $value;
    }

    private $working_timetable_name;                 // タイムテーブル名称

    // タイムテーブル名称
    public function getWorkingtimetablenameAttribute()
    {
        return $this->working_timetable_name;
    }

    public function setWorkingtimetablenameAttribute($value)
    {
        $this->working_timetable_name = $value;
    }

    private $working_timetable_from_time;                 // タイムテーブル開始時刻

    // タイムテーブル開始時刻
    public function getWorkingtimetablefromtimeAttribute()
    {
        return $this->working_timetable_from_time;
    }

    public function setWorkingtimetablefromtimeAttribute($value)
    {
        $this->working_timetable_from_time = $value;
    }

    private $working_timetable_to_time;                 // タイムテーブル終了時刻

    // タイムテーブル終了時刻
    public function getWorkingtimetabletotimeAttribute()
    {
        return $this->working_timetable_to_time;
    }

    public function setWorkingtimetabletotimeAttribute($value)
    {
        $this->working_timetable_to_time = $value;
    }

    private $shift_no;                 // シフトタイムテーブルNo

    // シフトタイムテーブルNo
    public function getShiftnoAttribute()
    {
        return $this->shift_no;
    }

    public function setShiftnoAttribute($value)
    {
        $this->shift_no = $value;
    }

    private $shift_name;                 // シフトタイムテーブル名称

    // シフトタイムテーブル名称
    public function getShiftnameAttribute()
    {
        return $this->shift_name;
    }

    public function setShiftnameAttribute($value)
    {
        $this->shift_name = $value;
    }

    private $shift_from_time;                 // シフトタイムテーブル開始時刻

    // シフトタイムテーブル開始時刻
    public function getShiftfromtimeAttribute()
    {
        return $this->shift_from_time;
    }

    public function setShiftfromtimeAttribute($value)
    {
        $this->shift_from_time = $value;
    }

    private $shift_to_time;                 // シフトタイムテーブル終了時刻

    // シフトタイムテーブル終了時刻
    public function getShifttotimeAttribute()
    {
        return $this->shift_to_time;
    }

    public function setShifttotimeAttribute($value)
    {
        $this->shift_to_time = $value;
    }

    private $mode;                 // 打刻モード

    // 打刻モード
    public function getModeAttribute()
    {
        return $this->mode;
    }

    public function setModeAttribute($value)
    {
        $this->mode = $value;
    }

    private $record_datetime;                 // 打刻日時

    // 打刻日時
    public function getRecorddatetimeAttribute()
    {
        return $this->record_datetime;
    }

    public function setRecorddatetimeAttribute($value)
    {
        $this->record_datetime = $value;
    }

    private $record_year;                 // 打刻年

    // 打刻年
    public function getRecordyearAttribute()
    {
        return $this->record_year;
    }

    public function setRecordyearAttribute($value)
    {
        $this->record_year = $value;
    }

    private $record_month;                 // 打刻月

    // 打刻月
    public function getRecordmonthAttribute()
    {
        return $this->record_month;
    }

    public function setRecordmonthAttribute($value)
    {
        $this->record_month = $value;
    }

    private $record_date;                 // 打刻年月日

    // 打刻年月日
    public function getRecorddateAttribute()
    {
        return $this->record_date;
    }

    public function setRecorddateAttribute($value)
    {
        $this->record_date = $value;
    }

    private $record_time;                 // 打刻時刻

    // 打刻時刻
    public function getRecordtimeAttribute()
    {
        return $this->record_time;
    }

    public function setRecordtimeAttribute($value)
    {
        $this->record_time = $value;
    }

    private $working_status;                 // 勤務状態

    // 勤務状態
    public function getWorkingstatusAttribute()
    {
        return $this->working_status;
    }

    public function setWorkingstatusAttribute($value)
    {
        $this->working_status = $value;
    }

    private $note;                 // メモ

    // メモ
    public function getNoteAttribute()
    {
        return $this->note;
    }

    public function setNoteAttribute($value)
    {
        $this->note = $value;
    }

    private $late;                 // 遅刻有無

    // 遅刻有無
    public function getLateAttribute()
    {
        return $this->late;
    }

    public function setLateAttribute($value)
    {
        $this->late = $value;
    }

    private $Leave_early;                 // 早退有無

    // 早退有無
    public function getLeaveearlyAttribute()
    {
        return $this->Leave_early;
    }

    public function setLeaveearlyAttribute($value)
    {
        $this->Leave_early = $value;
    }

    private $current_calc;                 // 当日計算有無

    // 当日計算有無
    public function getCurrentcalcAttribute()
    {
        return $this->current_calc;
    }

    public function setCurrentcalcAttribute($value)
    {
        $this->current_calc = $value;
    }

    private $to_be_confirmed;                 // 要確認有無

    // 要確認有無
    public function getTobeconfirmedAttribute()
    {
        return $this->to_be_confirmed;
    }

    public function setTobeconfirmedAttribute($value)
    {
        $this->to_be_confirmed = $value;
    }

    private $weekday_kubun;                 // 曜日区分

    // 曜日区分
    public function getWeekdaykubunAttribute()
    {
        return $this->weekday_kubun;
    }

    public function setWeekdaykubunAttribute($value)
    {
        $this->weekday_kubun = $value;
    }

    private $weekday_name;                 // 曜日名称

    // 曜日名称
    public function getWeekdaynameAttribute()
    {
        return $this->weekday_name;
    }

    public function setWeekdaynameAttribute($value)
    {
        $this->weekday_name = $value;
    }

    private $business_kubun;                 // 営業日区分

    // 営業日区分
    public function getBusinesskubunAttribute()
    {
        return $this->business_kubun;
    }

    public function setBusinesskubunAttribute($value)
    {
        $this->business_kubun = $value;
    }

    private $business_name;                 // 営業日名称

    // 営業日名称
    public function getBusinessnameAttribute()
    {
        return $this->business_name;
    }

    public function setBusinessnameAttribute($value)
    {
        $this->business_name = $value;
    }

    private $holiday_kubun;                 // 休暇区分

    // 休暇区分
    public function getHolidaykubunAttribute()
    {
        return $this->holiday_kubun;
    }

    public function setHolidaykubunAttribute($value)
    {
        $this->holiday_kubun = $value;
    }

    private $holiday_name;                 // 休暇名称

    // 休暇名称
    public function getHolidaynameAttribute()
    {
        return $this->holiday_name;
    }

    public function setHolidaynameAttribute($value)
    {
        $this->holiday_name = $value;
    }

    private $closing;                 // 締日

    // 締日
    public function getClosingAttribute()
    {
        return $this->closing;
    }

    public function setClosingAttribute($value)
    {
        $this->closing = $value;
    }

    private $uplimit_time;                 // 上限残業時間

    // 上限残業時間
    public function getUplimittimeAttribute()
    {
        return $this->uplimit_time;
    }

    public function setUplimittimeAttribute($value)
    {
        $this->uplimit_time = $value;
    }

    private $statutory_uplimit_time;                 // 法定上限残業時間

    // 法定上限残業時間
    public function getStatutoryuplimittimeAttribute()
    {
        return $this->statutory_uplimit_time;
    }

    public function setStatutoryuplimittimeAttribute($value)
    {
        $this->statutory_uplimit_time = $value;
    }

    private $time_unit;                 // 時間単位

    // 時間単位
    public function getTimeunitAttribute()
    {
        return $this->time_unit;
    }

    public function setTimeunitAttribute($value)
    {
        $this->time_unit = $value;
    }

    private $time_rounding;                 // 時間の丸め

    // 時間の丸め
    public function getTimeroundingAttribute()
    {
        return $this->time_rounding;
    }

    public function setTimeroundingAttribute($value)
    {
        $this->time_rounding = $value;
    }

    private $max_3month_total;                 // ３ヶ月累計

    // ３ヶ月累計
    public function getMax3MonthtotalAttribute()
    {
        return $this->max_3month_total;
    }

    public function setMax3MonthtotalAttribute($value)
    {
        $this->max_3month_total = $value;
    }

    private $max_6month_total;                 // ６ヶ月累計

    // ６ヶ月累計
    public function getMax6MonthtotalAttribute()
    {
        return $this->max_6month_total;
    }

    public function setMax6MonthtotalAttribute($value)
    {
        $this->max_6month_total = $value;
    }

    private $max_12month_total;                 // １年間累計

    // １年間累計
    public function getMax12MonthtotalAttribute()
    {
        return $this->max_12month_total;
    }

    public function setMax12MonthtotalAttribute($value)
    {
        $this->max_12month_total = $value;
    }

    private $beginning_month;                 // 期首月

    // 期首月
    public function getBeginningmonthAttribute()
    {
        return $this->beginning_month;
    }

    public function setBeginningmonthAttribute($value)
    {
        $this->beginning_month = $value;
    }

    private $working_interval;                 // 勤務間インターバル

    // 勤務間インターバル
    public function getWorkingintervalAttribute()
    {
        return $this->working_interval;
    }

    public function setWorkingintervalAttribute($value)
    {
        $this->working_interval = $value;
    }

    private $year;                 // 年

    // 年
    public function getYearAttribute()
    {
        return $this->year;
    }

    public function setYearAttribute($value)
    {
        $this->year = $value;
    }


    //--------------- パラメータ項目属性 -----------------------------------

    private $param_date_from;                   // 開始日付（00:00:00から）
    private $param_date_to;                     // 終了日付（23:59:59まで）
    private $param_employment_status;           // 雇用形態
    private $param_department_id;               // 部署
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
        return $this->param_department_id;
    }

    public function setParamDepartmentcodeAttribute($value)
    {
        $this->param_department_id = $value;
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
     * 登録
     *
     * @return void
     */
    public function insertTempCalcWorkingtimes(){
        DB::table($table)->insert(
            [
                'working_date' => $this->working_date,
                'employment_status' => $this->employment_status,
                'department_id' => $this->department_id,
                'user_code' => $this->user_code,
                'employment_status_name' => $this->employment_status_name,
                'department_name' => $this->department_name,
                'user_name' => $this->user_name,
                'working_timetable_no' => $this->working_timetable_no,
                'working_timetable_name' => $this->working_timetable_name,
                'working_timetable_from_time' => $this->working_timetable_from_time,
                'working_timetable_to_time' => $this->working_timetable_to_time,
                'shift_no' => $this->shift_no,
                'shift_name' => $this->shift_name,
                'shift_from_time' => $this->shift_from_time,
                'shift_to_time' => $this->shift_to_time,
                'mode' => $this->mode,
                'record_datetime' => $this->record_datetime,
                'record_year' => $this->record_year,
                'record_month' => $this->record_month,
                'record_date' => $this->record_date,
                'record_time' => $this->record_time,
                'attendance_time' => $this->attendance_time,
                'leaving_time' => $this->leaving_time,
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
                'working_interval' => $this->working_interval,
                'year' => $this->year
            ]
        );
    }

    /**
     * 削除
     *
     * @return void
     */
    public function delTempCalcWorkingtimes(){
        $mainquery = DB::table($this->table);

        if(!empty($this->param_date_from) && !empty($this->param_date_to)){
            $date = date_create($this->param_date_from);
            $this->param_date_from = $date->format('Ymd');
            $date = date_create($this->param_date_to);
            $this->param_date_to = $date->format('Ymd');
            $mainquery->where($this->table.'.working_date', '>=', $this->param_date_from);          // 日付範囲指定
            $mainquery->where($this->table.'.working_date', '<=', $this->param_date_to);            // 日付範囲指定
        }
        if(!empty($this->param_employment_status)){
            $mainquery->where($this->table.'.employment_status', $this->param_employment_status);   //　雇用形態指定
        }
        if(!empty($this->param_department_id)){
            $mainquery->where($this->table.'.department_id', $this->param_department_id);           // department_id指定
        }
        if(!empty($this->param_user_code)){
            $mainquery->where($this->table.'.user_code', $this->param_user_code);                   // user_code指定
        }
        
        $mainquery->delete();
    }

}
