<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;


class Department extends Model
{
    protected $table = 'departments';
    protected $table_users = 'users';
    protected $guarded = array('id');

    private $id;
    private $apply_term_from;               // 適用期間開始
    private $code;                          // 部署コード
    private $name;                          // 部署名
    private $is_deleted;                    // 削除フラグ
    private $created_user;                  // 作成ユーザー
    private $updated_user;                  // 修正ユーザー
    private $created_at;                    // 作成日時
    private $updated_at;                    // 修正日時


    // ID
    public function getIdAttribute()
    {
        return $this->id;
    }

    public function setIdAttribute($value)
    {
        $this->id = $value;
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
    private $param_apply_term_from;               // 適用期間開始
    private $param_code;                          // 部署コード

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

    /**
     * 部署名取得
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
                ->where('apply_term_from', '<=',$this->param_apply_term_from)
                ->where('is_deleted', '=', 0)
                ->groupBy('code');
            $mainquery = DB::table($this->table.' AS t1')
                ->select(
                    't1.id', 't1.code', 't1.name')
                ->selectRaw("DATE_FORMAT(t1.apply_term_from, '%Y-%m-%d') as apply_term_from")
                ->selectRaw("CASE t2.max_apply_term_from = t1.apply_term_from WHEN TRUE THEN 1 ELSE 0 END as result")
                ->leftJoinSub($subquery, 't2', function ($join) { 
                    $join->on('t2.code', '=', 't1.code');
                });
            $details = $mainquery
                ->where('t1.code', $this->param_code)
                ->where('t1.is_deleted', 0)
                ->orderBy('t1.apply_term_from', 'desc')
                ->get();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
        Log::debug('$details count = '.count($details));
        return $details;
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
                    'apply_term_from' => $this->apply_term_from,
                    'code' => $this->code,
                    'name' => $this->name,
                    'created_user'=>$this->created_user,
                    'created_at'=>$this->created_at
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
     * 部署更新
     *
     * @return void
     */
    public function updateDepartment(){
        try {
            DB::table($this->table)
                ->where('id', $this->id)
                ->where('is_deleted', 0)
                ->update([
                    'apply_term_from' => $this->apply_term_from,
                    'name' => $this->name,
                    'updated_at'=>$this->updated_at,
                    'updated_user'=>$this->updated_user
                ]);
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_update_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_update_erorr')).'$e');
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
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_maxget_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_maxget_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
        return $max_code;
    }

    private function maxCodeSql(){
        $sql = "select";
        $sql .= " max(CAST(code AS SIGNED)) as max_code";
        $sql .= " from";
        $sql .= " departments";
        $sql .= " where";
        $sql .= " is_deleted = 0";

        return $sql;
    }

}
