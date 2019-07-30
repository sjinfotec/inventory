<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

/**
 * テーブル：日次タイムレコード（working_time_date）のモデル
 *      アクセサ定義
 * @author      o.shindo
 * @version     1.00    20190629
*/
class WorkingTimedate extends Model
{
    protected $table = 'working_time_dates';
    protected $table_temp_working_time_dates = 'temp_working_time_dates';
    protected $table_work_times = 'work_times';
    protected $table_users = 'users';
    protected $table_departments = 'departments';
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
    private $created_user;                  // 作成ユーザー
    private $updated_user;                  // 修正ユーザー
    private $is_deleted;                    // 削除フラグ
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
        Log::DEBUG('$index = '.$index);
        Log::DEBUG('$value = '.$value);
        $this->array_attendance_time[$index] = $value;
        Log::DEBUG('$array_attendance_time = '.$this->array_attendance_time[$index]);
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
    public function getWorkingTimeDate(){


        // 日次労働時間取得SQL作成
        \DB::enableQueryLog();
        try{
            $mainquery = DB::table($this->table)
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
                    $this->table.'.fixedtime',
                    $this->table.'.created_user',
                    $this->table.'.updated_user',
                    $this->table.'.is_deleted');

            if(!empty($this->param_date_from) && !empty($this->param_date_to)){
                $date = date_create($this->param_date_from);
                $this->param_date_from = $date->format('Ymd');
                $date = date_create($this->param_date_to);
                $this->param_date_to = $date->format('Ymd');
                $mainquery->where($this->table.'.working_date', '>=', $this->param_date_from);          // 日付範囲指定
                $mainquery->where($this->table.'.working_date', '<=', $this->param_date_to);            // 日付範囲指定
            }
            
            if(!empty($this->param_employment_status)){
                $mainquery->where($this->table.'.employment_status', $this->param_employment_status);   //employment_status指定
            }
            
            if(!empty($this->param_user_code)){
                $mainquery->where($this->table.'.user_code', $this->param_user_code);                   //user_code指定
            }
            
            if(!empty($this->param_department_id)){
                $mainquery->where($this->table.'.department_id', $this->param_department_id);           //department_id指定
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
                'getWorkTimes' => \DB::getQueryLog()
            ]
        );
        
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
     *
     * @return sql取得結果
     */
    public function getWorkingTimeDateTimeFormat(){


        // 日次労働時間取得SQL作成
        \DB::enableQueryLog();
        try{

            $mainquery = DB::table($this->table)
                ->select(
                    $this->table.'.working_date',
                    $this->table.'.employment_status',
                    $this->table.'.department_id',
                    $this->table.'.user_code',
                    $this->table.'.ifnull(employment_status_name,'') as employment_status_name',
                    $this->table.'.ifnull(department_name,'') as department_name',
                    $this->table.'.ifnull(user_name,'') as user_name',
                    $this->table.'.working_timetable_no',
                    $this->table.'.ifnull(working_timetable_name,'') as working_timetable_name',
                    $this->table.'.employment_status');
            $case_where = "CASE ifnull({0},0) WHEN 0 THEN '00:00' ";
            $case_where .= "ELSE CONCAT(CONCAT(LPAD(TRUNCATE({0}, 0), 2, '0'),':'),LPAD(TRUNCATE((mod({0} * 100, 100) * 60) / 100, 0) , 2, '0')) ";
            $case_where .= ' END as {1}';
            $mainquery->selectRaw('DATE_FORMAT('.$this->table.'.attendance_time_1,'."'%H:%i'".')  as attendance_time_1')
                ->selectRaw('DATE_FORMAT('.$this->table.'.attendance_time_2,'."'%H:%i'".')  as attendance_time_2')
                ->selectRaw('DATE_FORMAT('.$this->table.'.attendance_time_3,'."'%H:%i'".')  as attendance_time_3')
                ->selectRaw('DATE_FORMAT('.$this->table.'.attendance_time_4,'."'%H:%i'".')  as attendance_time_4')
                ->selectRaw('DATE_FORMAT('.$this->table.'.attendance_time_5,'."'%H:%i'".')  as attendance_time_5')
                ->selectRaw('DATE_FORMAT('.$this->table.'.leaving_time_1,'."'%H:%i'".')  as leaving_time_1')
                ->selectRaw('DATE_FORMAT('.$this->table.'.leaving_time_2,'."'%H:%i'".')  as leaving_time_2')
                ->selectRaw('DATE_FORMAT('.$this->table.'.leaving_time_3,'."'%H:%i'".')  as leaving_time_3')
                ->selectRaw('DATE_FORMAT('.$this->table.'.leaving_time_4,'."'%H:%i'".')  as leaving_time_4')
                ->selectRaw('DATE_FORMAT('.$this->table.'.leaving_time_5,'."'%H:%i'".')  as leaving_time_5')
                ->selectRaw('DATE_FORMAT('.$this->table.'.missing_middle_time_1,'."'%H:%i'".')  as missing_middle_time_1')
                ->selectRaw('DATE_FORMAT('.$this->table.'.missing_middle_time_2,'."'%H:%i'".')  as missing_middle_time_2')
                ->selectRaw('DATE_FORMAT('.$this->table.'.missing_middle_time_3,'."'%H:%i'".')  as missing_middle_time_3')
                ->selectRaw('DATE_FORMAT('.$this->table.'.missing_middle_time_4,'."'%H:%i'".')  as missing_middle_time_4')
                ->selectRaw('DATE_FORMAT('.$this->table.'.missing_middle_time_5,'."'%H:%i'".')  as missing_middle_time_5')
                ->selectRaw('DATE_FORMAT('.$this->table.'.missing_middle_return_time_1,'."'%H:%i'".')  as missing_middle_return_time_1')
                ->selectRaw('DATE_FORMAT('.$this->table.'.missing_middle_return_time_2,'."'%H:%i'".')  as missing_middle_return_time_2')
                ->selectRaw('DATE_FORMAT('.$this->table.'.missing_middle_return_time_3,'."'%H:%i'".')  as missing_middle_return_time_3')
                ->selectRaw('DATE_FORMAT('.$this->table.'.missing_middle_return_time_4,'."'%H:%i'".')  as missing_middle_return_time_4')
                ->selectRaw('DATE_FORMAT('.$this->table.'.missing_middle_return_time_5,'."'%H:%i'".')  as missing_middle_return_time_5')
                ->selectRaw(str_replace('{1}', 'total_working_times', str_replace('{0}', $this->table.'.total_working_times', $case_where)))
                ->selectRaw(str_replace('{1}', 'regular_working_times', str_replace('{0}', $this->table.'.regular_working_times', $case_where)))
                ->selectRaw(str_replace('{1}', 'out_of_regular_working_times', str_replace('{0}', $this->table.'.out_of_regular_working_times', $case_where)))
                ->selectRaw(str_replace('{1}', 'overtime_hours', str_replace('{0}', $this->table.'.overtime_hours', $case_where)))
                ->selectRaw(str_replace('{1}', 'late_night_overtime_hours', str_replace('{0}', $this->table.'.late_night_overtime_hours', $case_where)))
                ->selectRaw(str_replace('{1}', 'legal_working_times', str_replace('{0}', $this->table.'.legal_working_times', $case_where)))
                ->selectRaw(str_replace('{1}', 'out_of_legal_working_times', str_replace('{0}', $this->table.'.out_of_legal_working_times', $case_where)))
                ->selectRaw(str_replace('{1}', 'not_employment_working_hours', str_replace('{0}', $this->table.'.not_employment_working_hours', $case_where)))
                ->selectRaw(str_replace('{1}', 'off_hours_working_hours', str_replace('{0}', $this->table.'.off_hours_working_hours', $case_where)));
            $mainquery
                ->addselect($this->table.'.working_status')
                ->addselect($this->table.'.ifnull(working_status_name,'') as working_status_name')
                ->addselect($this->table.'.ifnull(note,'') as note')
                ->addselect($this->table.'.late')
                ->addselect($this->table.'.leave_early')
                ->addselect($this->table.'.current_calc')
                ->addselect($this->table.'.to_be_confirmed')
                ->addselect($this->table.'.weekday_kubun')
                ->addselect($this->table.'.ifnull(weekday_name,'') as weekday_name')
                ->addselect($this->table.'.business_kubun')
                ->addselect($this->table.'.ifnull(business_name,'') as business_name')
                ->addselect($this->table.'.holiday_kubun')
                ->addselect($this->table.'.ifnull(holiday_name,'') as holiday_name')
                ->addselect($this->table.'.closing')
                ->addselect($this->table.'.uplimit_time')
                ->addselect($this->table.'.statutory_uplimit_time')
                ->addselect($this->table.'.time_unit')
                ->addselect($this->table.'.time_rounding')
                ->addselect($this->table.'.max_3month_total')
                ->addselect($this->table.'.max_6month_total')
                ->addselect($this->table.'.max_12month_total')
                ->addselect($this->table.'.beginning_month')
                ->addselect($this->table.'.working_interval')
                ->addselect($this->table.'.year')
                ->addselect($this->table.'.pattern')
                ->addselect($this->table.'.fixedtime')
                ->addselect($this->table.'.created_user')
                ->addselect($this->table.'.updated_user')
                ->addselect($this->table.'.is_deleted');
            
            $mainquery = $this->setWhereSql($mainquery);
            $result = $mainquery
                ->orderBy($this->table.'.working_date', 'asc')
                ->orderBy($this->table.'.employment_status', 'asc')
                ->orderBy($this->table.'.department_id', 'asc')
                ->orderBy($this->table.'.user_code', 'asc')
                ->get();
            
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
                'getWorkingTimeDateUserJoin' => \DB::getQueryLog()
            ]
        );
        
        return $result;
    }

    /**
     * 登録
     *
     * @return void
     */
    public function insertWorkingTimeDateFromTemp($array_subquery){
        try{
            DB::table($this->table)->insert($array_subquery);
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
     * 存在チェック
     *
     * @return boolean
     */
    public function isExistsWorkingTimeDate(){
        try{
            $mainquery = DB::table($this->table);

            $mainquery = $this->setWhereSql($mainquery);

            return $mainquery->exists();

        }catch(\PDOException $pe){
            Log::error(str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_exists_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error(str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_exists_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

    }

    /**
     * 削除
     *
     * @return void
     */
    public function delWorkingTimeDate(){
        try{
            $mainquery = DB::table($this->table);

            $mainquery = $this->setWhereSql($mainquery);
            
            $mainquery->delete();

        }catch(\PDOException $pe){
            Log::error(str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_delete_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error(str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_delete_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

    }
    
    /**
     * 条件設定（$this->tableのみ）
     *
     * @return query
     */
    public function setWhereSql($query){
        try{

            if(!empty($this->param_date_from) && !empty($this->param_date_to)){
                $date = date_create($this->param_date_from);
                $this->param_date_from = $date->format('Ymd');
                $date = date_create($this->param_date_to);
                $this->param_date_to = $date->format('Ymd');
                $query->where($this->table.'.working_date', '>=', $this->param_date_from);          // 日付範囲指定
                $query->where($this->table.'.working_date', '<=', $this->param_date_to);            // 日付範囲指定
            }
            
            if(!empty($this->param_employment_status)){
                $query->where($this->table.'.employment_status', $this->param_employment_status);   //employment_status指定
            }
            
            if(!empty($this->param_user_code)){
                $query->where($this->table.'.user_code', $this->param_user_code);                   //user_code指定
            }
            
            if(!empty($this->param_department_id)){
                $query->where($this->table.'.department_id', $this->param_department_id);           //department_id指定
            }

        }catch(\Exception $e){
            Log::error(str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_delete_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }


        return $query;

    }

}
