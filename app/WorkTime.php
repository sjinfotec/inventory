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
    private $department_id;                 // 部署コード
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

    // 部署コード
    public function getDepartmentcodeAttribute()
    {
        return $this->department_id;
    }

    public function setDepartmentcodeAttribute($value)
    {
        $this->department_id = $value;
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
        $this->massegedata = "";
        $result = true;

        // 日付範囲指定必須チェック
        if(isset($this->param_date_from) && isset($this->param_date_to)){
            // 日付範囲指定比較チェック
            $chkDateFrom = $this->getParamDatefromAttribute();
            $chkDateTo = $this->getParamDatetoAttribute();
            if($chkDateFrom <= $chkDateTo){
                $this->setArrayrecordtimeAttribute($chkDateFrom, $chkDateTo);
            } else {
                $this->massegedata .= "計算開始日付が計算終了日付より未来の日付になっています";
                $result = false;
            }
        } else {
            $this->massegedata .= "計算開始日付と計算終了日付は必ず入力してください。";
            $result = false;
        }

        return true;

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
     *          ①department_id指定プロパティを事前設定（未設定有効）
     *          ②user_code指定プロパティを事前設定（未設定有効）
     *          ③日付範囲指定プロパティを事前設定（未設定無効）
     *          ④メソッド：calcWorkingTimeDateを実行
     *
     * @return sql取得結果
     */
    public function getWorkTimes(){


        // 日次労働時間取得SQL作成
        // sunquery1    work_times
        //\DB::enableQueryLog();
        $sunquery1 = DB::table($this->table)
            ->select(
                $this->table.'.user_code',
                $this->table.'.department_id',
                $this->table.'.record_time',
                $this->table.'.mode'
            );

        $record_time = $this->getArrayrecordtimeAttribute();
        if(!empty($record_time)){
            $sunquery1->whereBetween($this->table.'.record_time', $record_time);     //record_time範囲指定
        }
        $sunquery1->where($this->table.'.is_deleted', '=', 0);

        // mainqueryにsunqueryを組み込む
        // sunquery1    t1:users
        // sunquery2    t2:work_times
        $mainquery = DB::table($this->table_users.' AS t1')
            ->select(
                't1.code as code',
                't1.department_id as department_id',
                't1.name as name',
                't2.record_time as record_time',
                't2.mode as mode'
                )
            ->leftJoinSub($sunquery1, 't2', function ($join) { 
                $join->on('t1.code', '=', 't2.user_code');
                $join->on('t1.department_id', '=', 't2.department_id');
            });
        if(!empty($this->param_user_code)){
            $mainquery->where('t1.code', $param_user_code);     //user_code指定
        }
        if(!empty($this->param_department_id)){
            $mainquery->where('t1.department_id', $param_department_id);     //department_id指定
        }
        $mainquery
            ->where('t1.is_deleted', '=', 0)
            ->orderBy('t1.code', 'asc')
            ->orderBy('t1.department_id', 'asc')
            ->orderBy('t2.mode', 'asc')
            ->get();
        $collections = collect($mainquery);
        $array = $collections->toArray();
        foreach ($mainquery as $result) {
            Log::debug('calcWorkingTimeDates.' +  $result->mode);
            // ユーザーの出勤・退勤・中抜・戻り時刻の確定処理
        }
        /*\Log::debug(
        'sql_debug_log',
        [
            'getWorkTimes' => \DB::getQueryLog()
        ]
        ); */

        return $mainquery;
    }

}
