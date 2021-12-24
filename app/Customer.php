<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;


class Department extends Model
{
    protected $table = 'customers';
    //protected $table_users = 'users';
    protected $table_offices = 'offices';
    protected $guarded = array('id');


    private $id;    //
    private $code;  // 営業所コード
    private $name;  // 営業所名
    private $created_user;  // 作成ユーザー
    private $updated_user;  // 修正ユーザー
    private $created_at;    // 作成日時
    private $updated_at;    // 修正日時
    private $is_deleted;    // 削除フラグ


    //private $id;
    //private $account_id;                    // ログインユーザーのアカウント
    //private $apply_term_from;               // 適用期間開始
    //private $code;                          // 部署コード
    //private $name;                          // 部署名
    //private $kill_from_date;                // 廃止年月日
    //private $is_deleted;                    // 削除フラグ
    //private $created_user;                  // 作成ユーザー
    //private $updated_user;                  // 修正ユーザー
    //private $created_at;                    // 作成日時
    //private $updated_at;                    // 修正日時


    // ID
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

    // 適用期間開始
    public function getApplytermfromAttribute()
    {
        return $this->apply_term_from;
    }

    public function setApplytermfromAttribute($value)
    {
        $this->apply_term_from = $value;
    }

    // 部署コード
    public function getCodeAttribute()
    {
        return $this->code;
    }

    public function setCodeAttribute($value)
    {
        $this->code = $value;
    }

    // 部署名
    public function getNameAttribute()
    {
        return $this->name;
    }

    public function setNameAttribute($value)
    {
        $this->name = $value;
    }

    // 廃止年月日
    public function getKillfromdateAttribute()
    {
        return $this->kill_from_date;
    }

    public function setKillfromdateAttribute($value)
    {
        $this->kill_from_date = $value;
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

    // 作成日時
    public function getCreatedatAttribute()
    {
        return $this->created_at;
    }

    public function setCreatedatAttribute($value)
    {
        $this->created_at = $value;
    }

    // 修正日時
    public function getUpdatedatAttribute()
    {
        return $this->updated_at;
    }

    public function setUpdatedatAttribute($value)
    {
        $this->updated_at = $value;
    }

    // ---------------- param --------------------------------
    private $param_account_id;                    // ログインユーザーのアカウント
    private $param_apply_term_from;               // 適用期間開始
    private $param_code;                          // 部署コード
    private $param_killvalue;                     // 廃止開始日を条件に含む(true)

    // ログインユーザーのアカウント
    public function getParamAccountidAttribute()
    {
        return $this->param_account_id;
    }

    public function setParamAccountidAttribute($value)
    {
        $this->param_account_id = $value;
    }

    // 適用期間開始
    public function getParamapplytermfromAttribute()
    {
        return $this->param_apply_term_from;
    }

    public function setParamapplytermfromAttribute($value)
    {
        $this->param_apply_term_from = $value;
    }

    // 部署コード
    public function getParamcodeAttribute()
    {
        return $this->param_code;
    }

    public function setParamcodeAttribute($value)
    {
        $this->param_code = $value;
    }

    // 廃止開始日を条件に含む
    public function getKillvalueAttribute()
    {
        return $this->param_killvalue;
    }

    public function setKillvalueAttribute($value)
    {
        $this->param_killvalue = $value;
    }
    

    /**
     * 部署取得
     *
     * @return void
     */
    public function getDetails(){
        $details = new Collection();
        try {
            // 最大適用開始日付subquery
            $subquery = DB::table($this->table)
                ->select('code as code')
                ->selectRaw('MAX(apply_term_from) as max_apply_term_from')
                ->where('apply_term_from', '<=',$this->param_apply_term_from);
            if(!empty($this->param_killvalue)){
                if (!$this->param_killvalue) {
                    $subquery->where('kill_from_date', '>',$this->param_apply_term_from);
                }
            } else {
                $subquery->where('kill_from_date', '>',$this->param_apply_term_from);
            }
            $subquery
                ->where('account_id', '=', $this->param_account_id)
                ->where('is_deleted', '=', 0)
                ->groupBy('code');

            $case_sql1 = "CASE t1.kill_from_date = ".Config::get('const.INIT_DATE.maxdate');
            $case_sql1 = $case_sql1." WHEN TRUE THEN NULL ELSE DATE_FORMAT(t1.kill_from_date, '%Y-%m-%d') END as kill_from_date";
            $case_sql2 = "CASE t2.max_apply_term_from = t1.apply_term_from ";
            $case_sql2 = $case_sql2." WHEN TRUE THEN 1";
            $case_sql2 = $case_sql2." ELSE CASE t2.max_apply_term_from < t1.apply_term_from ";
            $case_sql2 = $case_sql2."      WHEN TRUE THEN 2 ELSE 0 END ";
            $case_sql2 = $case_sql2." END  as result";
            $mainquery = DB::table($this->table.' AS t1')
                ->select(
                    't1.id as id', 't1.account_id as account_id', 't1.code as code', 't1.name as name')
                ->selectRaw("DATE_FORMAT(t1.apply_term_from, '%Y-%m-%d') as apply_term_from")
                ->selectRaw($case_sql1)
                ->selectRaw($case_sql2)
                ->JoinSub($subquery, 't2', function ($join) { 
                    $join->on('t2.code', '=', 't1.code');
                });
            if(!empty($this->param_code)){
                $mainquery->where('t1.code', $this->param_code);
            }
            $details = $mainquery
                ->where('t1.account_id', '=', $this->param_account_id)
                ->where('t1.is_deleted', 0)
                ->orderBy('t1.apply_term_from', 'desc')
                ->get();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
        return $details;
    }

    /**
     * 部署名（同名）チェック
     *
     * @return boolean
     */
    public function isExistsName(){
        try {
            $is_exists = DB::table($this->table)
                ->where('account_id', '=', $this->param_account_id)
                ->where('name',$this->name)
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
     * 部署追加
     *
     * @return void
     */
    public function insertDepartment(){
        try {
            DB::table($this->table)->insert(
                [
                    'account_id' => $this->account_id,
                    'apply_term_from' => $this->apply_term_from,
                    'code' => $this->code,
                    'name' => $this->name,
                    'kill_from_date' => $this->kill_from_date,
                    'created_user'=>$this->created_user,
                    'created_at'=>$this->created_at
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
     * 部署更新
     *
     * @return void
     */
    public function updateDepartment(){
        try {
            DB::table($this->table)
                ->where('account_id', '=', $this->param_account_id)
                ->where('id', $this->id)
                ->where('is_deleted', 0)
                ->update([
                    'apply_term_from' => $this->apply_term_from,
                    'name' => $this->name,
                    'kill_from_date' => $this->kill_from_date,
                    'updated_at'=>$this->updated_at,
                    'updated_user'=>$this->updated_user
                ]);
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_update_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_update_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 最大コード取得
     *
     * @return max_code
     */
    public function getMaxCode(){
        try {
            $max_code = DB::select($this->maxCodeSql());
            if(isset($max_code[0]->{'max_code'})){
                $max_code = $max_code[0]->{'max_code'};
            }else{
                $max_code = 0;
            }
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_maxget_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_maxget_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
        return $max_code;
    }

    private function maxCodeSql(){
        $sql = "select";
        $sql .= " max(code) as max_code";
        $sql .= " from";
        $sql .= " departments";
        $sql .= " where";
        $sql .= " account_id = '".$this->param_account_id."'";
        $sql .= " and is_deleted = 0";

        return $sql;
    }

}
