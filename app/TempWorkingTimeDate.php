<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TempWorkingTimeDate extends Model
{
    protected $table = 'temp_working_time_dates';
    protected $table_users = 'users';
    protected $table_departments = 'departments';
    protected $table_generalcodes = 'generalcodes';
    protected $guarded = array('id');

    //--------------- 項目属性 -----------------------------------

    private $working_date;                  // 日付
    private $employment_status;             // 雇用形態
    private $department_id;                 // 部署ID
    private $user_code;                     // ユーザー
    private $employment_status_name;        // 雇用形態名称
    private $department_name;               // 部署名称
    private $user_name;                     // ユーザー名称
    private $working_timetable_no;          // タイムテーブルNo
    private $working_timetable_name;        // タイムテーブル名称
    private $array_attendance_time = [null,null,null,null,null];                // 出勤時刻
    private $array_leaving_time = [null,null,null,null,null];                   // 退勤時刻
    private $array_missing_middle_time = [null,null,null,null,null];            // 中抜け時刻
    private $array_missing_middle_return_time = [null,null,null,null,null];     // 中抜け戻り時刻
    private $total_working_times;           // 合計勤務時間
    private $regular_working_times;         // 所定労働時間
    private $out_of_regular_working_times;  // 所定外労働時間
    private $overtime_hours;                // 残業時間
    private $late_night_overtime_hours;     // 深夜残業時間
    private $legal_working_times;           // 法定労働時間
    private $out_of_legal_working_times;    // 法定外労働時間
    private $not_employment_working_hours;  // 未就労労働時間
    private $off_hours_working_hours;       // 時間外労働時間
    private $working_status;                // 勤務状態
    private $working_status_name;           // 勤務状態名称
    private $note;                          // メモ
    private $late;                          // 遅刻有無
    private $leave_early;                   // 早退有無
    private $current_calc;                  // 当日計算有無
    private $to_be_confirmed;               // 要確認有無
    private $weekday_kubun;                 // 曜日区分
    private $weekday_name;                  // 曜日名称
    private $business_kubun;                // 営業日区分
    private $business_name;                 // 営業日名称
    private $holiday_kubun;                 // 休暇区分
    private $holiday_name;                  // 休暇名称
    private $closing;                       // 締日
    private $uplimit_time;                  // 上限残業時間
    private $statutory_uplimit_time;        // 法定上限残業時間
    private $time_unit;                     // 時間単位
    private $time_rounding;                 // 時間の丸め
    private $max_3month_total;              // ３ヶ月累計
    private $max_6month_total;              // ６ヶ月累計
    private $max_12month_total;             // １年間累計
    private $beginning_month;               // 期首月
    private $working_interval;              // 勤務間インターバル
    private $year;                          // 年
    private $pattern;                       // 打刻パターン
    private $fixedtime;                     // 確定
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
    public function getDepartmentidAttribute()
    {
        return $this->department_id;
    }

    public function setDepartmentidAttribute($value)
    {
        $this->department_id = $value;
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


    // 出勤時刻
    public function getAttendancetimeAttribute($index)
    {
        return $this->array_attendance_time[$index];
    }

    public function setAttendancetimeAttribute($index, $value)
    {
        $this->array_attendance_time[$index] = $value;
    }

    // 退勤時刻
    public function getLeavingtimeAttribute($index)
    {
        return $this->array_leaving_time[$index];
    }

    public function setLeavingtimeAttribute($index, $value)
    {
        $this->array_leaving_time[$index] = $value;
    }

    // 中抜け時刻
    public function getMissingmiddletimeAttribute($index)
    {
        return $this->array_missing_middle_time[$index];
    }

    public function setMissingmiddletimeAttribute($index, $value)
    {
        $this->array_missing_middle_time[$index] = $value;
    }

    // 中抜け戻り時刻
    public function getMissingmiddlereturntimeAttribute($index)
    {
        return $this->array_missing_middle_return_time[$index];
    }

    public function setMissingmiddlereturntimeAttribute($index, $value)
    {
        $this->array_missing_middle_return_time[$index] = $value;
    }

    // 合計勤務時間
    public function getTotalworkingtimesAttribute()
    {
        return $this->total_working_times;
    }

    public function setTotalworkingtimesAttribute($value)
    {
        $this->total_working_times = $value;
    }


    // 所定労働時間
    public function getRegularworkingtimesAttribute()
    {
        return $this->regular_working_times;
    }

    public function setRegularworkingtimesAttribute($value)
    {
        $this->regular_working_times = $value;
    }


    // 所定外労働時間
    public function getOutofregularworkingtimesAttribute()
    {
        return $this->out_of_regular_working_times;
    }

    public function setOutofregularworkingtimesAttribute($value)
    {
        $this->out_of_regular_working_times = $value;
    }


    // 残業時間
    public function getOvertimehoursAttribute()
    {
        return $this->overtime_hours;
    }

    public function setOvertimehoursAttribute($value)
    {
        $this->overtime_hours = $value;
    }


    // 深夜残業時間
    public function getLatenightovertimehoursAttribute()
    {
        return $this->late_night_overtime_hours;
    }

    public function setLatenightovertimehoursAttribute($value)
    {
        $this->late_night_overtime_hours = $value;
    }


    // 法定労働時間
    public function getLegalworkingtimesAttribute()
    {
        return $this->legal_working_times;
    }

    public function setLegalworkingtimesAttribute($value)
    {
        $this->legal_working_times = $value;
    }


    // 法定外労働時間
    public function getOutoflegalworkingtimesAttribute()
    {
        return $this->out_of_legal_working_times;
    }

    public function setOutoflegalworkingtimesAttribute($value)
    {
        $this->out_of_legal_working_times = $value;
    }


    // 未就労労働時間
    public function getNotemploymentworkinghoursAttribute()
    {
        return $this->not_employment_working_hours;
    }

    public function setNotemploymentworkinghoursAttribute($value)
    {
        $this->not_employment_working_hours = $value;
    }


    // 時間外労働時間
    public function getOffhoursworkinghoursAttribute()
    {
        return $this->off_hours_working_hours;
    }

    public function setOffhoursworkinghoursAttribute($value)
    {
        $this->off_hours_working_hours = $value;
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


    // 勤務間インターバル
    public function getWorkingintervalAttribute()
    {
        return $this->working_interval;
    }

    public function setWorkingintervalAttribute($value)
    {
        $this->working_interval = $value;
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


    // 確定
    public function getFixedtimeAttribute()
    {
        return $this->fixedtime;
    }

    public function setFixedtimeAttribute($value)
    {
        $this->fixedtime = $value;
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

    private $param_employment_status;           // 雇用形態
    private $param_user_code;                   // ユーザー
    private $param_department_id;               // 部署
    private $param_date_from;                   // 開始日付
    private $param_date_to;                     // 終了日付

    private $array_record_time;                 // 日付範囲配列
    private $massegedata;                       // メッセージ


    // 雇用形態
    public function getParamEmploymentStatusAttribute()
    {
        return $this->param_employment_status;
    }

    public function setParamEmploymentStatusAttribute($value)
    {
        $this->param_employment_status = $value;
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

    // 部署
    public function getParamDepartmentcodeAttribute()
    {
        return $this->param_department_id;
    }

    public function setParamDepartmentcodeAttribute($value)
    {
        $this->param_department_id = $value;
    }


    // 開始日付
    public function getParamdatefromAttribute()
    {
        $date = date_create($this->param_date_from);
        return $date->format('Ymd');
    }

    public function setParamdatefromAttribute($value)
    {
        $this->param_date_from = $value;
    }


    // 終了日付
    public function getParamdatetoAttribute()
    {
        $date = date_create($this->param_date_to);
        return $date->format('Ymd');
    }

    public function setParamdatetoAttribute($value)
    {
        $this->param_date_to = $value;
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
     * 日次労働時間取得
     *
     *      指定したユーザー、日付範囲内の労働時間計算のもとデータを取得するSQL
     *
     *      INPUT：
     *          ①テーブル：departments　部署範囲内 and 削除=0
     *          ②テーブル：users　      ユーザー範囲内 and 削除=0
     *          ③テーブル：work_times　 ユーザーand日付範囲内 and 削除=0
     *
     * @return sql取得結果
     */
    public function getTempWorkingTimeDateUserJoin(){


        // 日次労働時間取得SQL作成
        \DB::enableQueryLog();
        try{
            $subquery1 = DB::table($this->table)
                ->select(
                    $this->table.'.working_date',
                    $this->table.'.employment_status',
                    $this->table.'.department_id',
                    $this->table.'.user_code',
                    $this->table.'.employment_status_name',
                    $this->table.'.department_name',
                    $this->table.'.user_name',
                    $this->table.'.working_timetable_no',
                    $this->table.'.working_timetable_name',
                    $this->table.'.attendance_time_1',
                    $this->table.'.attendance_time_2',
                    $this->table.'.attendance_time_3',
                    $this->table.'.attendance_time_4',
                    $this->table.'.attendance_time_5',
                    $this->table.'.leaving_time_1',
                    $this->table.'.leaving_time_2',
                    $this->table.'.leaving_time_3',
                    $this->table.'.leaving_time_4',
                    $this->table.'.leaving_time_5',
                    $this->table.'.missing_middle_time_1',
                    $this->table.'.missing_middle_time_2',
                    $this->table.'.missing_middle_time_3',
                    $this->table.'.missing_middle_time_4',
                    $this->table.'.missing_middle_time_5',
                    $this->table.'.missing_middle_return_time_1',
                    $this->table.'.missing_middle_return_time_2',
                    $this->table.'.missing_middle_return_time_3',
                    $this->table.'.missing_middle_return_time_4',
                    $this->table.'.missing_middle_return_time_5',
                    $this->table.'.total_working_times',
                    $this->table.'.regular_working_times',
                    $this->table.'.out_of_regular_working_times',
                    $this->table.'.overtime_hours',
                    $this->table.'.late_night_overtime_hours',
                    $this->table.'.legal_working_times',
                    $this->table.'.out_of_legal_working_times',
                    $this->table.'.not_employment_working_hours',
                    $this->table.'.off_hours_working_hours',
                    $this->table.'.working_status',
                    $this->table.'.working_status_name',
                    $this->table.'.note',
                    $this->table.'.late',
                    $this->table.'.leave_early',
                    $this->table.'.current_calc',
                    $this->table.'.to_be_confirmed',
                    $this->table.'.weekday_kubun',
                    $this->table.'.weekday_name',
                    $this->table.'.business_kubun',
                    $this->table.'.business_name',
                    $this->table.'.holiday_kubun',
                    $this->table.'.holiday_name',
                    $this->table.'.closing',
                    $this->table.'.uplimit_time',
                    $this->table.'.statutory_uplimit_time',
                    $this->table.'.time_unit',
                    $this->table.'.time_rounding',
                    $this->table.'.max_3month_total',
                    $this->table.'.max_6month_total',
                    $this->table.'.max_12month_total',
                    $this->table.'.beginning_month',
                    $this->table.'.working_interval',
                    $this->table.'.year',
                    $this->table.'.pattern',
                    $this->table.'.fixedtime');

            if(!empty($this->param_date_from) && !empty($this->param_date_to)){
                $date = date_create($this->param_date_from);
                $this->param_date_from = $date->format('Ymd');
                $date = date_create($this->param_date_to);
                $this->param_date_to = $date->format('Ymd');
                $subquery1->where($this->table.'.working_date', '>=', $this->param_date_from);          // 日付範囲指定
                $subquery1->where($this->table.'.working_date', '<=', $this->param_date_to);            // 日付範囲指定
            }

            $mainquery = DB::table($this->table_users.' AS t1')
                ->selectRaw('(case when t2.working_date is not null then t2.working_date else '.$this->param_date_from.' end) as working_date');
            $mainquery->addselect('t1.employment_status')
                ->addselect('t1.department_id')
                ->addselect('t1.code as user_code')
                ->addselect('t4.code_name as employment_status_name')
                ->addselect('t3.name as department_name')
                ->addselect('t1.name as user_name')
                ->addselect('t2.working_timetable_no')
                ->addselect('t2.working_timetable_name');
            $mainquery->addselect('t1.employment_status')
                ->selectRaw('(case when t2.attendance_time_1 is not null then t2.attendance_time_1 else null end) as attendance_time_1')
                ->selectRaw('(case when t2.attendance_time_2 is not null then t2.attendance_time_2 else null end) as attendance_time_2')
                ->selectRaw('(case when t2.attendance_time_3 is not null then t2.attendance_time_3 else null end) as attendance_time_3')
                ->selectRaw('(case when t2.attendance_time_4 is not null then t2.attendance_time_4 else null end) as attendance_time_4')
                ->selectRaw('(case when t2.attendance_time_5 is not null then t2.attendance_time_5 else null end) as attendance_time_5')
                ->selectRaw('(case when t2.leaving_time_1 is not null then t2.leaving_time_1 else null end) as leaving_time_1')
                ->selectRaw('(case when t2.leaving_time_2 is not null then t2.leaving_time_2 else null end) as leaving_time_2')
                ->selectRaw('(case when t2.leaving_time_3 is not null then t2.leaving_time_3 else null end) as leaving_time_3')
                ->selectRaw('(case when t2.leaving_time_4 is not null then t2.leaving_time_4 else null end) as leaving_time_4')
                ->selectRaw('(case when t2.leaving_time_5 is not null then t2.leaving_time_5 else null end) as leaving_time_5')
                ->selectRaw('(case when t2.missing_middle_time_1 is not null then t2.missing_middle_time_1 else null end) as missing_middle_time_1')
                ->selectRaw('(case when t2.missing_middle_time_2 is not null then t2.missing_middle_time_2 else null end) as missing_middle_time_2')
                ->selectRaw('(case when t2.missing_middle_time_3 is not null then t2.missing_middle_time_3 else null end) as missing_middle_time_3')
                ->selectRaw('(case when t2.missing_middle_time_4 is not null then t2.missing_middle_time_4 else null end) as missing_middle_time_4')
                ->selectRaw('(case when t2.missing_middle_time_5 is not null then t2.missing_middle_time_5 else null end) as missing_middle_time_5')
                ->selectRaw('(case when t2.missing_middle_return_time_1 is not null then t2.missing_middle_return_time_1 else null end) as missing_middle_return_time_1')
                ->selectRaw('(case when t2.missing_middle_return_time_2 is not null then t2.missing_middle_return_time_2 else null end) as missing_middle_return_time_2')
                ->selectRaw('(case when t2.missing_middle_return_time_3 is not null then t2.missing_middle_return_time_3 else null end) as missing_middle_return_time_3')
                ->selectRaw('(case when t2.missing_middle_return_time_4 is not null then t2.missing_middle_return_time_4 else null end) as missing_middle_return_time_4')
                ->selectRaw('(case when t2.missing_middle_return_time_5 is not null then t2.missing_middle_return_time_5 else null end) as missing_middle_return_time_5');
            $mainquery->addselect('t2.total_working_times')
                ->addselect('t2.regular_working_times')
                ->addselect('t2.out_of_regular_working_times')
                ->addselect('t2.overtime_hours')
                ->addselect('t2.late_night_overtime_hours')
                ->addselect('t2.legal_working_times')
                ->addselect('t2.out_of_legal_working_times')
                ->addselect('t2.not_employment_working_hours')
                ->addselect('t2.off_hours_working_hours')
                ->addselect('t2.working_status')
                ->addselect('t5.code_name as working_status_name')
                ->addselect('t2.note')
                ->addselect('t2.late')
                ->addselect('t2.leave_early')
                ->addselect('t2.current_calc')
                ->addselect('t2.to_be_confirmed')
                ->addselect('t2.weekday_kubun')
                ->addselect('t2.weekday_name')
                ->addselect('t2.business_kubun')
                ->addselect('t2.business_name')
                ->addselect('t2.holiday_kubun')
                ->addselect('t2.holiday_name')
                ->addselect('t2.closing')
                ->addselect('t2.uplimit_time')
                ->addselect('t2.statutory_uplimit_time')
                ->addselect('t2.time_unit')
                ->addselect('t2.time_rounding')
                ->addselect('t2.max_3month_total')
                ->addselect('t2.max_6month_total')
                ->addselect('t2.max_12month_total')
                ->addselect('t2.beginning_month')
                ->addselect('t2.working_interval')
                ->addselect('t2.year')
                ->addselect('t2.pattern')
                ->addselect('t2.fixedtime');
            $mainquery->selectRaw(Auth::user()->id.' as created_user');
            $mainquery->selectRaw('null as updated_user');
            $mainquery->leftJoinSub($subquery1, 't2', function ($join) { 
                    $join->on('t2.user_code', '=', 't1.code');
                    $join->on('t2.department_id', '=', 't1.department_id');
                    $join->on('t2.employment_status', '=', 't1.employment_status');
                })
                ->leftJoin($this->table_departments.' as t3', function ($join) { 
                    $join->on('t3.id', '=', 't1.department_id')
                    ->where('t3.is_deleted', '=', 0);
                })
                ->leftJoin($this->table_generalcodes.' as t4', function ($join) { 
                    $join->on('t4.code', '=', 't1.employment_status')
                    ->where('t4.identification_id', '=', Config::get('const.C001.value'))
                    ->where('t4.is_deleted', '=', 0);
                })
                ->leftJoin($this->table_generalcodes.' as t5', function ($join) { 
                    $join->on('t5.code', '=', 't2.working_status')
                    ->where('t5.identification_id', '=', Config::get('const.C005.value'))
                    ->where('t5.is_deleted', '=', 0);
                });
                        
            if(!empty($this->param_employment_status)){
                $mainquery->where('t1.employment_status', $this->param_employment_status);   //employment_status指定
            }
            
            if(!empty($this->param_user_code)){
                $mainquery->where('t1.code', $this->param_user_code);                       //user_code指定
            }
            
            if(!empty($this->param_department_id)){
                $mainquery->where('t1.department_id', $this->param_department_id);          //department_id指定
            }
            $result = $mainquery->where('t1.is_deleted', '=', 0)->get();
            
        }catch(\PDOException $pe){
            Log::error(str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error(str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

        \Log::debug(
            'sql_debug_log',
            [
                'getTempWorkingTimeDateUserJoin' => \DB::getQueryLog()
            ]
        );
        
        return $result;
    }

    /**
     * 登録
     *
     * @return void
     */
    public function insertTempWorkingTimeDate(){
        try{
            DB::table($this->table)->insert(
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
                    'attendance_time_1' => $this->array_attendance_time[0],
                    'attendance_time_2' => $this->array_attendance_time[1],
                    'attendance_time_3' => $this->array_attendance_time[2],
                    'attendance_time_4' => $this->array_attendance_time[3],
                    'attendance_time_5' => $this->array_attendance_time[4],
                    'leaving_time_1' => $this->array_leaving_time[0],
                    'leaving_time_2' => $this->array_leaving_time[1],
                    'leaving_time_3' => $this->array_leaving_time[2],
                    'leaving_time_4' => $this->array_leaving_time[3],
                    'leaving_time_5' => $this->array_leaving_time[4],
                    'missing_middle_time_1' => $this->array_missing_middle_time[0],
                    'missing_middle_time_2' => $this->array_missing_middle_time[1],
                    'missing_middle_time_3' => $this->array_missing_middle_time[2],
                    'missing_middle_time_4' => $this->array_missing_middle_time[3],
                    'missing_middle_time_5' => $this->array_missing_middle_time[4],
                    'missing_middle_return_time_1' => $this->array_missing_middle_return_time[0],
                    'missing_middle_return_time_2' => $this->array_missing_middle_return_time[1],
                    'missing_middle_return_time_3' => $this->array_missing_middle_return_time[2],
                    'missing_middle_return_time_4' => $this->array_missing_middle_return_time[3],
                    'missing_middle_return_time_5' => $this->array_missing_middle_return_time[4],
                    'total_working_times' => $this->total_working_times,
                    'regular_working_times' => $this->regular_working_times,
                    'out_of_regular_working_times' => $this->out_of_regular_working_times,
                    'overtime_hours' => $this->overtime_hours,
                    'late_night_overtime_hours' => $this->late_night_overtime_hours,
                    'legal_working_times' => $this->legal_working_times,
                    'out_of_legal_working_times' => $this->out_of_legal_working_times,
                    'not_employment_working_hours' => $this->not_employment_working_hours,
                    'off_hours_working_hours' => $this->off_hours_working_hours,
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
                    'working_interval' => $this->working_interval,
                    'year' => $this->year,
                    'pattern' => $this->pattern,
                    'fixedtime' => $this->fixedtime,
                    'created_at'=>$this->systemdate
                ]
            );
        }catch(\PDOException $pe){
            Log::error(str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_insert_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error(str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_insert_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 削除
     *
     * @return void
     */
    public function delTempWorkingTimeDate(){
        try{
            $mainquery = DB::table($this->table)->truncate();
        }catch(\PDOException $pe){
            Log::error(str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_delete_erorr')));
            Log::error($pe->getMessage());
            throw $pe;
        }

    }

}
