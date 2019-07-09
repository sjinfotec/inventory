<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorkTime extends Model
{
    protected $table = 'work_times';
    protected $table_users = 'users';
    protected $guarded = array('id');

    //--------------- メンバー属性 -----------------------------------

    private $user_code;                     // ユーザーコード
    private $record_time;                   // 打刻時間
    private $mode;                          // 打刻モード
    private $created_user;                  // 作成ユーザー
    private $updated_user;                  // 修正ユーザー
    private $is_deleted;                    // 削除フラグ
    private $systemdate;

    // ユーザーコード
    public function getUsercodeAttribute()
    {
        return $this->user_code;
    }

    public function setUsercodeAttribute($value)
    {
        $this->user_code = $value;
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
        return date_format($this->record_time, '%Y%m%d');
    }

    // 打刻時刻（work_timesのrecord_timeの時刻部分）
    public function getRecordtimeTimeAttribute($value)
    {
        return date_format($this->record_time, '%H%i%s');
    }


    //--------------- パラメータ項目属性 -----------------------------------

    private $department_code_from;              // 開始部署
    private $department_code_to;                // 終了部署
    private $user_code_from;                    // 開始ユーザー
    private $user_code_to;                      // 終了ユーザー
    private $date_from;                         // 開始日付
    private $date_to;                           // 終了日付
    private $massegedata;                       // メッセージ

    // 開始部署
    public function getDepartmentcodefromAttribute()
    {
        return $this->department_code_from;
    }

    public function setDepartmentcodefromAttribute($value)
    {
        $this->department_code_from = $value;
    }


    // 終了部署
    public function getDepartmentcodetoAttribute()
    {
        return $this->department_code_to;
    }

    public function setDepartmentcodetoAttribute($value)
    {
        $this->department_code_to = $value;
    }

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

    // 開始日付
    public function getDatefromAttribute()
    {
        $date = date_create($this->date_from);
        return $date->format('Y/m/d').' 00:00:00';
    }

    public function setDatefromAttribute($value)
    {
        $this->date_from = $value;
    }


    // 終了日付
    public function getDatetoAttribute()
    {
        $date = date_create($this->date_to);
        return $date->format('Y/m/d').' 23:59:59';
    }

    public function setDatetoAttribute($value)
    {
        $this->date_to = $value;
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
     * 勤怠登録
     *
     * @return void
     */
    public function insertWorkTime(){
        DB::table($table)->insert(
            [
                'user_code' => $this->user_code,
                'record_time' => $this->systemdate,
                'mode' => $this->mode,
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
        $tasks = DB::table($table)
            ->join('users', 'work_times.user_code', '=', 'users.code')
            ->select(
                    'work_times.user_code',
                    'work_times.',
                    'work_times.end_date'
                    )
            // ->where('tasks.department_id',$department_id)
            ->limit(1)
            ->get();

        return $tasks;
    }

    /**
     * 日次労働時間取得事前チェック
     *
     *      指定したユーザー、日付範囲内の労働時間計算のもとデータを取得するSQL
     *
     *      INPUT：
     *          ①テーブル：departments　部署範囲内 and 削除=0
     *          ②テーブル：users　      ユーザー範囲内 and 削除=0
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
        $this->massegedata = "";

        // 日付範囲指定必須チェック
        $array_record_time = array();       //初期化
        /*if(isset($this->date_from) && isset($this->date_to)){
            if(isset($this->date_from <= isset($this->date_to)){
                // 日付範囲指定比較チェック
                $array_record_time = array($this->getDatefromAttribute(), $this->getDatetoAttribute());
            } else {
                //$this->massegedata .= "計算開始日付　＞　計算終了日付　となっています。";
                $result = false;
            }
        } else {
            //$this->massegedata .= "計算開始日付と計算終了日付は必ず入力してください。";
            $result = false;
        }*/
        $result = true;

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
     *          ①department_code範囲指定プロパティを事前設定（未設定有効）
     *          ②user_code範囲指定プロパティを事前設定（未設定有効）
     *          ③日付範囲指定プロパティを事前設定（未設定無効）
     *          ④メソッド：calcWorkingTimeDateを実行
     *
     * @return sql取得結果
     */
    private function getWorkTimes($array_record_time, $array_user){

        // department_code範囲指定配列
        $array_department = array();        //初期化
        if(isset($this->department_code_from) && isset($this->department_code_to)){
            $array_department = array($this->getDepartmentcodefromAttribute(),$this->getDepartmentcodetoAttribute());
        }

        // user_code範囲指定配列
        $array_user = array();              //初期化
        if(isset($this->user_code_from) && isset($this->user_code_to)){
            $array_user = array($this->getUsercodefromAttribute(),$this->getUsercodetoAttribute());
        }

        // 日次労働時間取得
        $result = $this->getWorkTimes($array_record_time, $array_user);
        return $result;

        // 日次労働時間取得SQL作成
        // sunquery1    work_times
        \DB::enableQueryLog();
        $sunquery1 = DB::table($this->table)
            ->select(
                $this->table.'.user_code',
                $this->table.'.record_time',
                $this->table.'.mode'
            );
        if(!empty($array_record_time)){
            $sunquery1->whereBetween($this->table.'.record_time', $array_record_time);     //record_time範囲指定
        }
        $sunquery1->where($this->table.'.is_deleted', '=', 0);

        // mainqueryにsunqueryを組み込む
        // sunquery1    t1:users
        // sunquery2    t2:work_times
        $mainquery = DB::table($this->table_users.' AS t1')
            ->select(
                't1.code',
                't1.department_code',
                't1.name',
                't2.record_time',
                't2.mode'
                )
            ->leftJoinSub($sunquery1, 't2', function ($join) { 
                $join->on('t1.code', '=', 't2.user_code');
            });
        if(!empty($array_user)){
            $mainquery->whereBetween('t1.code', $array_user);     //user_code範囲指定
        }
        $mainquery
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
