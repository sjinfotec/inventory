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
    protected $table = 'working_time_date';
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

    private $user_code_from;                    // 開始ユーザー
    private $user_code_to;                      // 終了ユーザー
    private $date_to;                           // 終了計算日付
    private $date_from;                         // 開始計算日付

    // 開始ユーザー
    public function getUsercodefromAttribute()
    {
        return $this->user_code_from;
    }

    public function setUsercodefromAttribute($value)
    {
        $this->user_code_from = $value;
    }


    // 終了ユーザー
    public function getUsercodetoAttribute()
    {
        return $this->user_code_to;
    }

    public function setUsercodetoAttribute($value)
    {
        $this->user_code_to = $value;
    }


    // 開始計算日付
    public function getDatefromAttribute()
    {
        $date = new DateTime($this->date_from);
        return $date->format('Ymd');
    }

    public function setDatefromAttribute($value)
    {
        $this->date_from = $value;
    }


    // 終了計算日付
    public function getDatetoAttribute()
    {
        $date = new DateTime($this->date_to);
        return $date->format('Ymd');
    }

    public function setDatetoAttribute($value)
    {
        $this->date_to = $value;
    }


    // --------------------- メソッド ------------------------------------------------------


}