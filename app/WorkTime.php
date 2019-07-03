<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    private $user_code_from;                    // 開始ユーザー
    private $user_code_to;                      // 終了ユーザー
    private $date_from;                         // 開始日付
    private $date_to;                           // 終了日付

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
        $date = new DateTime($this->date_from);
        return $date->format('Y/m/d H:i:s');
    }

    public function setDatefromAttribute($value)
    {
        $this->date_from = $value;
    }


    // 終了日付
    public function getDatetoAttribute()
    {
        $date = new DateTime($this->date_to);
        return $date->format('Y/m/d H:i:s');
    }

    public function setDatetoAttribute($value)
    {
        $this->date_to = $value;
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
     * 日次労働時間取得SQL
     *
     *      指定したユーザー、日付範囲内の労働時間計算のもとデータを取得するSQL
     *
     *      INPUT：
     *          ①テーブル：users　      ユーザー範囲内 and 削除=0
     *          ②テーブル：work_times　 ユーザーand日付範囲内 and 削除=0
     *          ③①と②の結合             ①.ユーザー = ②.ユーザー
     *
     *      使用方法：
     *          ①user_code範囲指定プロパティを事前設定（未設定有効）
     *          ②メソッド：calcWorkingTimeDateを実行
     *
     * @return sql取得結果
     */
    public function getWorkingTimeData(){

        // 日付範囲指定必須チェック
        $array_record_time = array();     //初期化
        if(isset($this->date_from) && isset($this->date_to)){
            $array_record_time = array($this->$date_from,$this->$date_to);
        } else {
            $result = null;
        }


        // user_code範囲指定配列
        $array_user = array();     //初期化
        if(isset($this->user_code_from) && isset($this->user_code_to)){
            $array_user = array($this->$user_code_from,$this->$user_code_to);
        }

        // 日次労働時間取得SQL作成
        // sunquery1    users
        $usersSql = DB::table($this->$table_users)
            ->select(
                $this->$table_users + '.code',
                $this->$table_users + '.department_code',
                $this->$table_users + '.name'
            );
        if(!empty($array_user)){
            $usersSql->whereBetween($this->$table_users + '.code', $array_user);     //user_code範囲指定
        }
        $usersSql->where($this->$table_users + '.is_deleted', '=', 0);
        $usersSql->toSql();
        
        // sunquery2    work_times
        $worktimesSql = DB::table($this->$table)
            ->select(
                $this->$table + '.user_code',
                $this->$table + '.record_time',
                $this->$table + '.mode'
            );
        if(!empty($array_user)){
            $worktimesSql->whereBetween($this->$table + '.user_code', $array_user);     //user_code範囲指定
        }
        if(!empty($array_record_time)){
            $worktimesSql->whereBetween($this->$table + '.record_time', $array_record_time);     //record_time範囲指定
        }
        $worktimesSql->where($this->$table + '.is_deleted', '=', 0);
        $worktimesSql->toSql();


        // mainqueryにsunqueryを組み込む
        // sunquery1    t1:users
        $maintask = DB::table(DB::raw('('.$usersSql.') AS t1'));
        // sunquery2    t2:work_times
        $maintask->leftjoin(DB::raw('('.$worktimesSql.') AS t2'), 't2.user_code', '=', 't1.code')
            ->select(
                    't1.code',
                    't1.department_code',
                    't1.name',
                    't2.record_time',
                    't2.mode'
                    )
            ->get();

        return $maintask;
    }

}
