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
    protected $table_users = 'users';
    protected $table_departments = 'departments';
    protected $table_generalcodes = 'generalcodes';
    //--------------- メンバー属性 -----------------------------------

    private $id;
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

    // ユーザーコード
    public function getIdAttribute()
    {
        return $this->id;
    }

    public function setIdAttribute($value)
    {
        $this->id = $value;
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
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_insert_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_insert_erorr')).'$e');
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
        try {
            $sqlString = "";
            $sqlString .= "select";
            $sqlString .= "  t3.code as user_code" ;
            $sqlString .= "  , t3.name as user_name ";
            $sqlString .= "  , t3.department_code as department_code ";
            $sqlString .= "  , t4.name as department_name ";
            $sqlString .= "  , t1.mode as mode ";
            $sqlString .= "  , t5.code_name as mode_name ";
            $sqlString .= "from ".$this->table.' as t1 ';
            $sqlString .= "  inner join ( ";
            $sqlString .= "    select ";
            $sqlString .= "      user_code ";
            $sqlString .= "      , department_code ";
            $sqlString .= "      , max(record_time) as record_time ";
            $sqlString .= "    from ".$this->table.' ';
            $sqlString .= "    where ";
            $sqlString .= "      ? = ? ";
            $sqlString .= "      and record_time between ? and ? ";
            $sqlString .= "    group by ";
            $sqlString .= "      user_code ";
            $sqlString .= "      , department_code ";
            $sqlString .= "  ) as t2 ";
            $sqlString .= "  on ";
            $sqlString .= "    t2.user_code = t1.user_code ";
            $sqlString .= "    and t2.department_code = t1.department_code ";
            $sqlString .= "    and t2.record_time = t1.record_time ";
            $sqlString .= "  inner join ( ";
            $sqlString .= "    select ";
            $sqlString .= "      t1.code ";
            $sqlString .= "      , t1.name ";
            $sqlString .= "      , t1.department_code  ";
            $sqlString .= "    from ";
            $sqlString .= "      ".$this->table_users." as t1 ";
            $sqlString .= "      inner join ( ";
            $sqlString .= "        select ";
            $sqlString .= "          code as code ";
            $sqlString .= "          , MAX(apply_term_from) as max_apply_term_from ";
            $sqlString .= "        from ";
            $sqlString .= "          ".$this->table_users." ";
            $sqlString .= "        where ";
            $sqlString .= "          ? = ? ";
            $sqlString .= "          and apply_term_from <= ? ";
            $sqlString .= "          and role <= ? ";
            $sqlString .= "          and is_deleted = ? ";
            $sqlString .= "        group by  ";
            $sqlString .= "          code ";
            $sqlString .= "      ) as t2 ";
            $sqlString .= "      on ";
            $sqlString .= "        t2.code = t1.code ";
            $sqlString .= "        and t2.max_apply_term_from = t1.apply_term_from ";
            $sqlString .= "  ) as t3 ";
            $sqlString .= "  on ";
            $sqlString .= "    t3.code = t1.user_code ";
            $sqlString .= "    and t3.department_code = t1.department_code ";
            $sqlString .= "  inner join ( ";
            $sqlString .= "    select ";
            $sqlString .= "      t1.code as code ";
            $sqlString .= "      , t1.name as name ";
            $sqlString .= "    from ";
            $sqlString .= "      ".$this->table_departments." as t1 ";
            $sqlString .= "      inner join ( ";
            $sqlString .= "        select ";
            $sqlString .= "          code as code ";
            $sqlString .= "          , MAX(apply_term_from) as max_apply_term_from ";
            $sqlString .= "        from ";
            $sqlString .= "          ".$this->table_departments." ";
            $sqlString .= "        where ";
            $sqlString .= "          ? = ? ";
            $sqlString .= "          and apply_term_from <= ? ";
            $sqlString .= "          and is_deleted = ? ";
            $sqlString .= "        group by ";
            $sqlString .= "          code ";
            $sqlString .= "     ) as t2 ";
            $sqlString .= "     on ";
            $sqlString .= "       t2.code = t1.code ";
            $sqlString .= "       and t2.max_apply_term_from = t1.apply_term_from ";
            $sqlString .= "   where ";
            $sqlString .= "     ? = ? ";
            $sqlString .= "     and t1.kill_from_date >= ? ";
            $sqlString .= "     and t1.is_deleted = ? ";
            $sqlString .= "  ) as t4 ";
            $sqlString .= "  on ";
            $sqlString .= "    t4.code = t1.department_code ";
            $sqlString .= "  inner join ".$this->table_generalcodes." as t5 ";
            $sqlString .= "  on ";
            $sqlString .= "    t5.identification_id = ? ";
            $sqlString .= "    and t5.code = t1.mode ";
            $sqlString .= "    and t5.is_deleted = ? ";
            $sqlString .= "order by ";
            $sqlString .= "  t1.mode ";
            $sqlString .= "  , t3.department_code ";
            $sqlString .= "  , t3.code ";
            // バインド
            // record_time 範囲
            $array_setBindingsStr = array();
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            $from_datetime = date_format(new Carbon($target_date), 'Y/m/d')." 00:00:00";
            $to_datetime = date_format(new Carbon($target_date), 'Y/m/d')." 23:59:59";
            $array_setBindingsStr[] = $from_datetime;
            $array_setBindingsStr[] = $to_datetime;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = $target_date;
            $array_setBindingsStr[] = Config::get('const.C017.admin_user');;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = $target_date;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = $target_date;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = Config::get('const.C012.value');
            $array_setBindingsStr[] = 0;
            $result = DB::select($sqlString, $array_setBindingsStr);
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

        return $result;
    }

}
