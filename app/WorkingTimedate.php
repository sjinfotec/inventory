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

    private $working_time_style;                 // 労働時間形態

    // 労働時間形態
    public function getWorkingtimestyleAttribute()
    {
        return $this->working_time_style;
    }

    public function setWorkingtimestyleAttribute($value)
    {
        $this->working_time_style = $value;
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

    private $legal_working_times;                 // 法定労働時間

    // 法定労働時間
    public function getLegalworkingtimesAttribute()
    {
        return $this->legal_working_times;
    }

    public function setLegalworkingtimesAttribute($value)
    {
        $this->legal_working_times = $value;
    }

    private $out_of_legal_working_times;                 // 法定外労働時間

    // 法定外労働時間
    public function getOutoflegalworkingtimesAttribute()
    {
        return $this->out_of_legal_working_times;
    }

    public function setOutoflegalworkingtimesAttribute($value)
    {
        $this->out_of_legal_working_times = $value;
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
    private $param_department_code;             // 部署
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
        return $this->param_department_code;
    }

    public function setParamDepartmentcodeAttribute($value)
    {
        $this->param_department_code = $value;
    }


    // 開始日付
    public function getDatefromAttribute()
    {
        $date = date_create($this->param_date_from);
        return $date->format('Y/m/d').' 00:00:00';
    }

    public function setDatefromAttribute($value)
    {
        $this->param_date_from = $value;
    }


    // 終了日付
    public function getDatetoAttribute()
    {
        $date = date_create($this->param_date_to);
        return $date->format('Y/m/d').' 23:59:59';
    }

    public function setDatetoAttribute($value)
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
    public function getWorkingTimeDates(){


        // 日次労働時間取得SQL作成
        \DB::enableQueryLog();
        $mainquery = DB::table($this->table)
            ->select(
                $this->table.'.working_date',
                $this->table.'.department_code',
                $this->table.'.department_name',
                $this->table.'.mode'
            ->where('t1.is_deleted', '=', 0)
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
