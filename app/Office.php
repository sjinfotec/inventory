<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;


class Office extends Model
{
    protected $table = 'offices';
    protected $table_users = 'users';
    protected $guarded = array('id');

    private $id;
    private $account_id;                    // ログインユーザーのアカウント
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

    // ログインユーザーのアカウント
    public function getAccountidAttribute()
    {
        return $this->account_id;
    }

    public function setAccountidAttribute($value)
    {
        $this->account_id = $value;
    }

    // 営業所コード
    public function getCodeAttribute()
    {
        return $this->code;
    }

    public function setCodeAttribute($value)
    {
        $this->code = $value;
    }

    // 営業所名
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
    private $param_account_id;                    // ログインユーザーのアカウント
    private $param_code;                          // 営業所コード

    // ログインユーザーのアカウント
    public function getParamAccountidAttribute()
    {
        return $this->param_account_id;
    }

    public function setParamAccountidAttribute($value)
    {
        $this->param_account_id = $value;
    }

    // 営業所コード
    public function getParamcodeAttribute()
    {
        return $this->param_code;
    }

    public function setParamcodeAttribute($value)
    {
        $this->param_code = $value;
    }

    

    /**
     * 営業所取得
     *
     * @return void
     */
    public function getDetails(){
        $details = new Collection();
        try {

            $mainquery = DB::table($this->table.' AS t1')
                ->select(
                    't1.id as id', 't1.code as code', 't1.name as name'
                );
            if(!empty($this->param_code)){
                $mainquery->where('t1.code', $this->param_code);
            }
            $details = $mainquery
                ->where('t1.is_deleted', 0)
                ->orderBy('t1.code', 'desc')
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
     * 同名チェック
     *
     * @return boolean
     */
    public function isExistsName(){
        try {
            $is_exists = DB::table($this->table)
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
     * 新規追加
     *
     * @return void
     */
    public function insertOffice(){
        try {
            DB::table($this->table)->insert(
                [
                    'code' => $this->code,
                    'name' => $this->name,
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
     * 更新
     *
     * @return void
     */
    public function updateOffice(){
        try {
            DB::table($this->table)
                ->where('id', $this->id)
                ->where('is_deleted', 0)
                ->update([
                    'name' => $this->name,
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
     * 削除フラグ
     *
     * @return void
     */
    public function delOffice(){
        try {
            DB::table($this->table)
                ->where('id', $this->id)
                ->update([
                    'is_deleted' => 1,
                    'updated_at' => $this->updated_at,
                    'updated_user' => $this->updated_user
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
        $sql .= " offices";
        //$sql .= " where";
        //$sql .= " account_id = '".$this->param_account_id."'";
        //$sql .= " and is_deleted = 0";
        //$sql .= " where";
        //$sql .= " is_deleted = 0";

        return $sql;
    }

}
