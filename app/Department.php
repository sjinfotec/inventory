<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Department extends Model
{
    protected $table = 'departments';
    protected $table_users = 'users';
    protected $guarded = array('id');

    private $apply_term_from;               // 適用期間開始
    private $apply_term_to;                 // 適用期間終了
    private $name;                          // 部署名
    private $is_deleted;                    // 削除フラグ
    private $created_user;                  // 作成ユーザー
    private $updated_user;                  // 修正ユーザー
    private $created_at;                    // 作成日時
    private $updated_at;                    // 修正日時


    // 適用期間開始
    public function getApplytermfromAttribute()
    {
        return $this->apply_term_from;
    }

    public function setApplytermfromAttribute($value)
    {
        $this->apply_term_from = $value;
    }

    // 適用期間終了
    public function getApplytermtoAttribute()
    {
        return $this->apply_term_to;
    }

    public function setApplytermtoAttribute($value)
    {
        $this->apply_term_to = $value;
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


    public function insertDepartment(){
        DB::table($this->table)->insert(
            [
                'apply_term_from' => $this->apply_term_from,
                'apply_term_to' => $this->apply_term_to,
                'name' => $this->name,
                'created_at'=>$this->created_at
            ]
        );
    }

}
