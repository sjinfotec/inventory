<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\ApiCommonController;
use Carbon\Carbon;

class WorkTimeLog extends Model
{
    protected $table = 'work_time_logs';
    protected $table_work_times = 'work_times';
    protected $table_users = 'users';
    protected $table_departments = 'departments';
    protected $table_generalcodes = 'generalcodes';
    protected $table_user_holiday_kubuns = 'user_holiday_kubuns';
    protected $table_working_timetables = 'working_timetables';
    protected $table_calendar_setting_informations = 'calendar_setting_informations';

    //--------------- メンバー属性 -----------------------------------

    private $id;
    private $account_id;                    // ログインユーザーのアカウント
    private $user_code;                     // ユーザーコード
    private $department_code;               // 部署コード
    private $employment_status;             // 雇用形態
    private $record_time;                   // 打刻時間
    private $mode;                          // 打刻モード
    private $card_idm;                      // カードＩＤ
    private $created_user;                  // 作成ユーザー
    private $updated_user;                  // 修正ユーザー
    private $is_deleted;                    // 削除フラグ
    private $systemdate;

    // ユーザーID
    public function getIdAttribute()
    {
        return $this->id;
    }

    public function setIdAttribute($value)
    {
        $this->id = $value;
    }

    // ログインユーザーのアカウント
    public function getAccountidAttribute()
    {
        return $this->account_id;
    }

    public function setAccountidAttribute($value)
    {
        $this->account_id = $value;
    }

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
        return $this->department_code;
    }

    public function setDepartmentcodeAttribute($value)
    {
        $this->department_code = $value;
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


    // カードＩＤ
    public function getCardidmAttribute()
    {
        return $this->card_idm;
    }

    public function setCardidmAttribute($value)
    {
        $this->card_idm = $value;
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

    // ------------- implements --------------

    private $param_id;                          // id
    private $param_account_id;                  // ログインユーザーのアカウント
    private $param_user_code;                   // ユーザー
    private $param_department_code;             // 部署コード
    private $param_employment_status;           // 雇用形態
    private $param_record_time_from;            // 開始打刻時間
    private $param_record_time_to;              // 終了打刻時間
    private $param_mode;                        // モード

    // id
    public function getParamidAttribute()
    {
        return $this->param_id;
    }

    public function setParamidAttribute($value)
    {
        $this->param_id = $value;
    }

    // ログインユーザーのアカウント
    public function getParamAccountidAttribute()
    {
        return $this->param_account_id;
    }

    public function setParamAccountidAttribute($value)
    {
        $this->param_account_id = $value;
    }

    // ユーザー
    public function getParamusercodeAttribute()
    {
        return $this->param_user_code;
    }

    public function setParamusercodeAttribute($value)
    {
        $this->param_user_code = $value;
    }

    // 部署コード
    public function getParamdepartmentcodeAttribute()
    {
        return $this->param_department_code;
    }

    public function setParamdepartmentcodeAttribute($value)
    {
        $this->param_department_code = $value;
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

    // 開始打刻時間
    public function getParamrecordtimefromAttribute()
    {
        return $this->param_record_time_from;
    }

    public function setParamrecordtimefromAttribute($value)
    {
        $date = date_create($value);
        $this->param_record_time_from = $date->format('Y/m/d').' 00:00:00';
    }

    public function setParamrecordtimefromnoneditAttribute($value)
    {
        $date = date_create($value);
        $this->param_record_time_from = $date->format('Y/m/d H:i:s');
    }

    // 終了打刻時間
    public function getParamrecordtimetoAttribute()
    {
        return $this->param_record_time_to;
    }

    public function setParamrecordtimetoAttribute($value)
    {
        $date = date_create($value);
        $this->param_record_time_to = $date->format('Y/m/d').' 23:59:59';
    }

    public function setParamrecordtimetononeditAttribute($value)
    {
        $date = date_create($value);
        $this->param_record_time_to = $date->format('Y/m/d H:i:s');
    }

    // モード
    public function getParammodeAttribute()
    {
        return $this->param_mode;
    }

    public function setParammodeAttribute($value)
    {
        $this->param_mode = $value;
    }

    // --------------------- メソッド ------------------------------------------------------

    /**
     * 勤怠登録
     *
     * @return void
     */
    public function insertWorkTimeLog(){
        try {
            DB::table($this->table)->insert(
                [
                    'user_code' => $this->user_code,
                    'department_code' => $this->department_code,
                    'employment_status' => $this->employment_status,
                    'record_time' => $this->record_time,
                    'mode' => $this->mode,
                    'card_idm' => $this->card_idm,
                    'created_user' => $this->created_user,
                    'created_at'=>$this->systemdate
                ]
            );
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
     * 勤怠情報取得
     * 
     *  $targetdateは適用期間
     *
     * @return void
     */
    public function getWorkinTimeLog($target_date){
        // $this->tableを$this->table_work_timesに変更 20200406
        try {
            $sqlString = "";
            $sqlString .= "select";
            $sqlString .= "  t1.user_code as user_code" ;
            $sqlString .= "  , t1.user_name as user_name ";
            $sqlString .= "  , t1.department_code as department_code ";
            $sqlString .= "  , t1.department_name as department_name ";
            $sqlString .= "  , t1.mode as mode ";
            $sqlString .= "  , t1.record_time_name as record_time_name ";
            $sqlString .= "  , t1.record_time as record_time ";
            $sqlString .= "  , t1.x_positions as x_positions ";
            $sqlString .= "  , t1.y_positions as y_positions ";
            $sqlString .= "  , t1.holiday_kubun as holiday_kubun ";
            $sqlString .= "  , t1.mode_name as mode_name ";
            $sqlString .= "  , t1.mode_sub_name as mode_sub_name ";
            $sqlString .= "  , t1.holiday_kubun_name as holiday_kubun_name ";
            $sqlString .= "  from (";
            $sqlString .= "  select";
            $sqlString .= "    t1.code as user_code" ;
            $sqlString .= "    , t1.name as user_name ";
            $sqlString .= "    , t1.department_code as department_code ";
            $sqlString .= "    , t5.name as department_name ";
            $sqlString .= "    , t3.mode as mode ";
            $sqlString .= "    , case ifnull(t3.record_time,'') ";
            $sqlString .= "      when '' then '-' ";
            $sqlString .= "      else ";
            $sqlString .= "        case DATE_FORMAT(t3.record_time, '%H:%i') ";
            $sqlString .= "        when '00:00' then '-' ";
            $sqlString .= "        else ";
            $sqlString .= "          DATE_FORMAT(t3.record_time, '%m/%d %H:%i') ";
            $sqlString .= "        end ";
            $sqlString .= "      end as record_time_name ";
            $sqlString .= "    , t3.record_time as record_time ";
            $sqlString .= "    , t3.x_positions as x_positions ";
            $sqlString .= "    , t3.y_positions as y_positions ";
            $sqlString .= "    , t4.holiday_kubun as holiday_kubun ";
            $sqlString .= "    , case ifnull(t3.mode,'') ";
            $sqlString .= "      when '' then '-' ";
            $sqlString .= "      else ";
            $sqlString .= "        (select code_name from ".$this->table_generalcodes." where identification_id = ? and code = t3.mode and is_deleted = ?) ";
            $sqlString .= "      end as mode_name  ";
            $sqlString .= "    , case ifnull(t3.mode,'') ";
            $sqlString .= "      when '' then '未出勤' ";
            $sqlString .= "      else ";
            $sqlString .= "        (select code_name from ".$this->table_generalcodes." where identification_id = ? and code = t3.mode and is_deleted = ?) ";
            $sqlString .= "      end as mode_sub_name  ";
            $sqlString .= "    , case ifnull(t4.holiday_kubun,'') ";
            $sqlString .= "      when '' then '' ";
            $sqlString .= "      else ";
            $sqlString .= "        (select code_name from ".$this->table_generalcodes." where identification_id = ? and code = t4.holiday_kubun and is_deleted = ?) ";
            $sqlString .= "      end as holiday_kubun_name  ";
            $sqlString .= "      ,ifnull(t1.is_deleted,0) as t1_is_deleted ";
            $sqlString .= "      ,ifnull(t3.is_deleted,0) as t3_is_deleted ";
            $sqlString .= "  from ";
            $sqlString .= "  ".$this->table_users." as t1 ";
            $sqlString .= "    inner join ( ";
            $sqlString .= "      select ";
            $sqlString .= "        code as code ";
            $sqlString .= "        , MAX(apply_term_from) as max_apply_term_from ";
            $sqlString .= "      from ";
            $sqlString .= "      ".$this->table_users." ";
            $sqlString .= "      where ";
            $sqlString .= "        ? = ? ";
            $sqlString .= "        and apply_term_from <= ? ";
            $sqlString .= "        and kill_from_date >  ? ";
            $sqlString .= "        and role < ? ";
            $sqlString .= "        and is_deleted = ? ";
            $sqlString .= "      group by ";
            $sqlString .= "        code ";
            $sqlString .= "    ) as t2 ";
            $sqlString .= "    on ";
            $sqlString .= "      t2.code = t1.code ";
            $sqlString .= "      and t2.max_apply_term_from = t1.apply_term_from ";
            $sqlString .= "    left join ( ";
            $sqlString .= "      select ";
            $sqlString .= "        t1.user_code ";
            $sqlString .= "        , t1.department_code ";
            $sqlString .= "        , t1.record_time ";
            $sqlString .= "        , t1.mode ";
            $sqlString .= "        , t1.user_holiday_kubuns_id ";
            $sqlString .= "        , X(t1.positions) as x_positions ";
            $sqlString .= "        , Y(t1.positions) as y_positions ";
            $sqlString .= "        , t1.is_deleted ";
            $sqlString .= "      from ";
            $sqlString .= "        ".$this->table_work_times." as t1 ";
            $sqlString .= "        inner join ( ";
            $sqlString .= "          select ";
            $sqlString .= "            user_code as user_code ";
            $sqlString .= "            , department_code as department_code ";
            $sqlString .= "            , max(record_time) as record_time ";
            $sqlString .= "          from ";
            $sqlString .= "            ".$this->table_work_times." ";
            $sqlString .= "          where ";
            $sqlString .= "            ? = ? ";
            $sqlString .= "            and record_time between ? and ? ";
            $sqlString .= "            and is_deleted = ? ";
            $sqlString .= "          group by  ";
            $sqlString .= "            user_code ";
            $sqlString .= "            , department_code ";
            $sqlString .= "        ) as t2 ";
            $sqlString .= "        on ";
            $sqlString .= "          t2.user_code = t1.user_code ";
            $sqlString .= "          and t2.department_code = t1.department_code ";
            $sqlString .= "          and t2.record_time = t1.record_time ";
            $sqlString .= "    ) as t3 ";
            $sqlString .= "    on ";
            $sqlString .= "      t3.user_code = t1.code ";
            $sqlString .= "      and t3.department_code = t1.department_code ";
            $sqlString .= "    left join ".$this->table_calendar_setting_informations." as t4 ";        // user_holiday_kubuns
            $sqlString .= "    on ";
            $sqlString .= "      t4.date = ? ";
            $sqlString .= "      and t4.user_code = t1.code ";
            $sqlString .= "      and t4.department_code = t1.department_code ";
            $sqlString .= "      and t4.is_deleted = ? ";
            $sqlString .= "    inner join ( ";
            $sqlString .= "      select ";
            $sqlString .= "        t1.code as code ";
            $sqlString .= "        , t1.name as name ";
            $sqlString .= "      from ";
            $sqlString .= "        ".$this->table_departments." as t1 ";
            $sqlString .= "        inner join ( ";
            $sqlString .= "          select ";
            $sqlString .= "            code as code ";
            $sqlString .= "            , MAX(apply_term_from) as max_apply_term_from ";
            $sqlString .= "          from ";
            $sqlString .= "            ".$this->table_departments." ";
            $sqlString .= "          where ";
            $sqlString .= "            ? = ? ";
            $sqlString .= "            and apply_term_from <= ? ";
            $sqlString .= "            and is_deleted = ? ";
            $sqlString .= "          group by ";
            $sqlString .= "            code ";
            $sqlString .= "         ) as t2 ";
            $sqlString .= "         on ";
            $sqlString .= "           t2.code = t1.code ";
            $sqlString .= "           and t2.max_apply_term_from = t1.apply_term_from ";
            $sqlString .= "       where ";
            $sqlString .= "         ? = ? ";
            $sqlString .= "         and t1.kill_from_date >= ? ";
            $sqlString .= "         and t1.is_deleted = ? ";
            $sqlString .= "    ) as t5 ";
            $sqlString .= "    on ";
            $sqlString .= "      t5.code = t1.department_code ";
            $sqlString .= ") as t1 ";
            $sqlString .= "where ";
            $sqlString .= "  t1.t1_is_deleted = ? ";
            $sqlString .= "  and t1.t3_is_deleted = ? ";
            $sqlString .= "order by ";
            $sqlString .= "  t1.department_code ";
            $sqlString .= "  , t1.user_code ";
            $sqlString .= "  , t1.mode ";
            // バインド
            // record_time 範囲
            $array_setBindingsStr = array();
            $array_setBindingsStr[] = Config::get('const.C005.value');
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = Config::get('const.C035.value');
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = Config::get('const.C013.value');
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = $target_date;
            $array_setBindingsStr[] = $target_date;
            $array_setBindingsStr[] = Config::get('const.C017.admin_user');;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            $from_datetime = date_format(new Carbon($target_date), 'Y/m/d')." 00:00:00";
            $to_datetime = date_format(new Carbon($target_date), 'Y/m/d')." 23:59:59";
            $array_setBindingsStr[] = $from_datetime;
            $array_setBindingsStr[] = $to_datetime;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = $target_date;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = $target_date;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = $target_date;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 0;
            $result = DB::select($sqlString, $array_setBindingsStr);
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

        return $result;
    }

    /**
     * worktimelog情報取得
     *
     * @return boolean
     */
    public function getDetails(){
        try {
            $mainquery = DB::table($this->table);
            if (!empty($this->param_department_code)) {
                $mainquery
                    ->where('department_code', '=', $this->param_department_code);
            }
            if (!empty($this->param_employment_status)) {
                $mainquery
                    ->where('employment_status', '=', $this->param_employment_status);
            }
            if (!empty($this->param_user_code)) {
                $mainquery
                    ->where('user_code', '=', $this->param_user_code);
            }
            if(!empty($this->param_record_time_from)){
                $mainquery->where('record_time', '>=', $this->param_record_time_from);      // 日付範囲指定
            }
            if(!empty($this->param_record_time_to)){
                $mainquery->where('record_time', '<=', $this->param_record_time_to);        // 日付範囲指定
            }
            if (!empty($this->param_mode)) {
                $mainquery
                    ->where('mode', '=', $this->param_mode);
            }
            $result = $mainquery
                ->where('is_deleted',0)
                ->get();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_exists_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_exists_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

        return $result;
    }

    /**
     * 存在チェック
     *
     * @return boolean
     */
    public function isExist(){
        try {
            $mainquery = DB::table($this->table);
            if (!empty($this->param_department_code)) {
                $mainquery
                    ->where('department_code', '=', $this->param_department_code);
            }
            if (!empty($this->param_employment_status)) {
                $mainquery
                    ->where('employment_status', '=', $this->param_employment_status);
            }
            if (!empty($this->param_user_code)) {
                $mainquery
                    ->where('user_code', '=', $this->param_user_code);
            }
            if(!empty($this->param_record_time_from)){
                $mainquery->where('record_time', '>=', $this->param_record_time_from);      // 日付範囲指定
            }
            if(!empty($this->param_record_time_to)){
                $mainquery->where('record_time', '<=', $this->param_record_time_to);        // 日付範囲指定
            }
            if (!empty($this->param_mode)) {
                $mainquery
                    ->where('mode', '=', $this->param_mode);
            }
            $is_exists =$mainquery
                ->where('is_deleted',0)
                ->exists();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_exists_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_exists_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

        return $is_exists;
    }

    /**
     * 更新（共通）
     *
     * @return boolean
     */
    public function updateCommon($array_update){
        try {
            $mainquery = DB::table($this->table);
            if (!empty($this->param_department_code)) {
                $mainquery
                    ->where('department_code', '=', $this->param_department_code);
            }
            if (!empty($this->param_employment_status)) {
                $mainquery
                    ->where('employment_status', '=', $this->param_employment_status);
            }
            if (!empty($this->param_user_code)) {
                $mainquery
                    ->where('user_code', '=', $this->param_user_code);
            }
            if(!empty($this->param_record_time_from)){
                $mainquery->where('record_time', '>=', $this->param_record_time_from);      // 日付範囲指定
            }
            if(!empty($this->param_record_time_to)){
                $mainquery->where('record_time', '<=', $this->param_record_time_to);        // 日付範囲指定
            }
            if (!empty($this->param_mode)) {
                $mainquery
                    ->where('mode', '=', $this->param_mode);
            }
            $result =$mainquery
                ->where('is_deleted',0)
                ->update($array_update);
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_update_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_update_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

        return $result;
    }

}
