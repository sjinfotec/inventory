<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


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


    /**
     * 部署追加
     *
     * @return void
     */
    public function insertDepartment(){
        DB::table($this->table)->insert(
            [
                'apply_term_from' => $this->apply_term_from,
                'code' => $this->code,
                'name' => $this->name,
                'created_user'=>$this->created_user,
                'created_at'=>$this->created_at
            ]
        );
    }

    /**
     * 部署更新
     *
     * @return void
     */
    public function updateDepartment(){
        DB::table($this->table)
            ->where('id', $this->id)
            ->where('is_deleted', 0)
            ->update([
                'apply_term_from' => $this->apply_term_from,
                'name' => $this->name,
                'updated_at'=>$this->updated_at,
                'updated_user'=>$this->updated_user
            ]);
    }

    /**
     * 最大コード取得
     *
     * @return max_code
     */
    public function getMaxCode(){
        $max_code = DB::select($this->maxCodeSql());
        if(isset($max_code[0]->{'max_code'})){
            $max_code = $max_code[0]->{'max_code'};
        }else{
            $max_code = 0;
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
