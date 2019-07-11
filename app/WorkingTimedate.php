<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * テーブル：日次タイムレコード（working_time_date）のモデル
 *      アクセサ定義
 * @author      o.shindo
 * @version     1.00    20190629
*/
class WorkingTimedate extends Model
{
    protected $table = 'working_time_dates';
    protected $table_users = 'users';
    protected $table_work_times = 'work_times';
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

    private $user_code;                 // ユーザーCODE

    // ユーザーCODE
    public function getUsercodeAttribute()
    {
        return $this->user_code;
    }

    public function setUsercodeAttribute($value)
    {
        $this->user_code = $value;
    }

    private $leaworking_timetable_noving_time;                 // タイムテーブルNo

    // タイムテーブルNo
    public function getLeaworkingtimetablenovingtimeAttribute()
    {
        return $this->leaworking_timetable_noving_time;
    }

    public function setLeaworkingtimetablenovingtimeAttribute($value)
    {
        $this->leaworking_timetable_noving_time = $value;
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

    private $working_time_style;                 // 雇用形態

    // 雇用形態
    public function getWorkingtimestyleAttribute()
    {
        return $this->working_time_style;
    }

    public function setWorkingtimestyleAttribute($value)
    {
        $this->working_time_style = $value;
    }

    private $working_time_style_name;                 // 雇用形態名称

    // 雇用形態名称
    public function getWorkingtimestylenameAttribute()
    {
        return $this->working_time_style_name;
    }

    public function setWorkingtimestylenameAttribute($value)
    {
        $this->working_time_style_name = $value;
    }

    private $attendance_time;                 // 出勤時刻

    // 出勤時刻
    public function getAttendancetimeAttribute()
    {
        return $this->attendance_time;
    }

    public function setAttendancetimeAttribute($value)
    {
        $this->attendance_time = $value;
    }

    private $leaving_time;                 // 退勤時刻

    // 退勤時刻
    public function getLeavingtimeAttribute()
    {
        return $this->leaving_time;
    }

    public function setLeavingtimeAttribute($value)
    {
        $this->leaving_time = $value;
    }

    private $missing_middle_time;                 // 中抜時刻

    // 中抜時刻
    public function getMissingmiddletimeAttribute()
    {
        return $this->missing_middle_time;
    }

    public function setMissingmiddletimeAttribute($value)
    {
        $this->missing_middle_time = $value;
    }

    private $missing_middle_return_time;                 // 中抜戻り時刻

    // 中抜戻り時刻
    public function getMissingmiddlereturntimeAttribute()
    {
        return $this->missing_middle_return_time;
    }

    public function setMissingmiddlereturntimeAttribute($value)
    {
        $this->missing_middle_return_time = $value;
    }

    private $regular_working_times;                 // 所定労働時間

    // 所定労働時間
    public function getRegularworkingtimesAttribute()
    {
        return $this->regular_working_times;
    }

    public function setRegularworkingtimesAttribute($value)
    {
        $this->regular_working_times = $value;
    }

    private $out_of_regular_working_times;                 // 所定外労働時間

    // 所定外労働時間
    public function getOutofregularworkingtimesAttribute()
    {
        return $this->out_of_regular_working_times;
    }

    public function setOutofregularworkingtimesAttribute($value)
    {
        $this->out_of_regular_working_times = $value;
    }

    private $out_of_regular_night_working_times;                 // 所定外深夜労働時間

    // 所定外深夜労働時間
    public function getOutofregularnightworkingtimesAttribute()
    {
        return $this->out_of_regular_night_working_times;
    }

    public function setOutofregularnightworkingtimesAttribute($value)
    {
        $this->out_of_regular_night_working_times = $value;
    }

    private $out_of_regular_total_working_times;                 // 所定外合計労働時間

    // 所定外合計労働時間
    public function getOutofregulartotalworkingtimesAttribute()
    {
        return $this->out_of_regular_total_working_times;
    }

    public function setOutofregulartotalworkingtimesAttribute($value)
    {
        $this->out_of_regular_total_working_times = $value;
    }

    private $not_employment_time;                 // 不就労時間

    // 不就労時間
    public function getNotemploymenttimeAttribute()
    {
        return $this->not_employment_time;
    }

    public function setNotemploymenttimeAttribute($value)
    {
        $this->not_employment_time = $value;
    }

    private $statutory_working_times;                 // 法定労働時間

    // 法定労働時間
    public function getStatutoryworkingtimesAttribute()
    {
        return $this->statutory_working_times;
    }

    public function setStatutoryworkingtimesAttribute($value)
    {
        $this->statutory_working_times = $value;
    }

    private $out_of_statutory_working_times;                 // 法定外労働時間

    // 法定外労働時間
    public function getOutofstatutoryworkingtimesAttribute()
    {
        return $this->out_of_statutory_working_times;
    }

    public function setOutofstatutoryworkingtimesAttribute($value)
    {
        $this->out_of_statutory_working_times = $value;
    }

    private $prescribed_outside_total_month;                 // 所定外累計（月）

    // 所定外累計（月）
    public function getPrescribedoutsidetotalmonthAttribute()
    {
        return $this->prescribed_outside_total_month;
    }

    public function setPrescribedoutsidetotalmonthAttribute($value)
    {
        $this->prescribed_outside_total_month = $value;
    }

    private $remaining_prescribed_outside_month;                 // 所定外累計（月）

    // 所定外累計（月）
    public function getRemainingprescribedoutsidemonthAttribute()
    {
        return $this->remaining_prescribed_outside_month;
    }

    public function setRemainingprescribedoutsidemonthAttribute($value)
    {
        $this->remaining_prescribed_outside_month = $value;
    }

    private $leave_item_code;                 // 休暇項目コード

    // 休暇項目コード
    public function getLeaveitemcodeAttribute()
    {
        return $this->leave_item_code;
    }

    public function setLeaveitemcodeAttribute($value)
    {
        $this->leave_item_code = $value;
    }

    private $leave_item_name;                 // 休暇項目名称

    // 休暇項目名称
    public function getLeaveitemnameAttribute()
    {
        return $this->leave_item_name;
    }

    public function setLeaveitemnameAttribute($value)
    {
        $this->leave_item_name = $value;
    }

    private $fixedtime;                 // 確定 1:確定

    // 確定 1:確定
    public function getFixedtimeAttribute()
    {
        return $this->fixedtime;
    }

    public function setFixedtimeAttribute($value)
    {
        $this->fixedtime = $value;
    }

    private $created_user;                 // 作成ユーザー

    // 作成ユーザー
    public function getCreateduserAttribute()
    {
        return $this->created_user;
    }

    public function setCreateduserAttribute($value)
    {
        $this->created_user = $value;
    }

    private $updated_user;                 // 修正ユーザー

    // 修正ユーザー
    public function getUpdateduserAttribute()
    {
        return $this->updated_user;
    }

    public function setUpdateduserAttribute($value)
    {
        $this->updated_user = $value;
    }

    private $is_deleted;                 // 削除フラグ

    // 削除フラグ
    public function getIsdeletedAttribute()
    {
        return $this->is_deleted;
    }

    public function setIsdeletedAttribute($value)
    {
        $this->is_deleted = $value;
    }


    //--------------- パラメータ項目属性 -----------------------------------

    private $param_user_code;                   // ユーザー
    private $param_department_id;               // 部署
    private $param_date_from;                   // 開始日付
    private $param_date_to;                     // 終了日付

    private $array_record_time;                 // 日付範囲配列
    private $massegedata;                       // メッセージ


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
        return $date->format('Y/m/d').' 00:00:00';
    }

    public function setParamdatefromAttribute($value)
    {
        $this->param_date_from = $value;
    }


    // 終了日付
    public function getParamdatetoAttribute()
    {
        $date = date_create($this->param_date_to);
        return $date->format('Y/m/d').' 23:59:59';
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
    public function getWorkingTimeDates(){


        // 日次労働時間取得SQL作成
        \DB::enableQueryLog();
        $mainquery = DB::table($this->table)
            ->select(
                $this->table.'.working_date',
                $this->table.'.department_id',
                $this->table.'.department_name',
                $this->table.'.user_code',
                $this->table.'.working_timetable_no',
                $this->table.'.working_timetable_name',
                $this->table.'.working_time_style',
                $this->table.'.working_time_style_name',
                $this->table.'.attendance_time',
                $this->table.'.leaving_time',
                $this->table.'.missing_middle_time',
                $this->table.'.missing_middle_return_time',
                $this->table.'.regular_working_times',
                $this->table.'.out_of_regular_working_times',
                $this->table.'.out_of_regular_night_working_times',
                $this->table.'.out_of_regular_total_working_times',
                $this->table.'.not_employment_time',
                $this->table.'.statutory_working_times',
                $this->table.'.out_of_statutory_working_times',
                $this->table.'.leave_item_code',
                $this->table.'.leave_item_name',
                $this->table.'.fixedtime',
                $this->table.'.is_deleted');

        if(!empty($this->param_user_code)){
            $mainquery->where($this->table.'.user_code', $this->param_user_code);               //user_code指定
        }
        
        if(!empty($this->param_department_id)){
            $mainquery->where($this->table.'.department_id', $this->param_department_id);       //department_id指定
        }

        $record_time = $this->getArrayrecordtimeAttribute();
        if(!empty($record_time)){
            $mainquery->whereBetween($this->table.'.record_time', $record_time);       //record_time範囲指定
        }
        $mainquery->where('t1.is_deleted', '=', 0)
            ->get();

        \Log::debug(
            'sql_debug_log',
            [
                'getWorkTimes' => \DB::getQueryLog()
            ]
        );
        
        return $mainquery;
    }

}
