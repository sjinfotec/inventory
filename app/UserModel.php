<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class UserModel extends Model
{
    protected $table = 'users';
    protected $guarded = array('id');

    //--------------- メンバー属性 -----------------------------------
    private $id;
    private $apply_term_from;                  
    private $code;                  
    private $department_code;                  
    private $name;                  
    private $kana;                  
    private $password;                  
    private $email;                  
    private $employment_status;                  
    private $working_timetable_no;
    private $is_deleted;                 // 削除フラグ
    private $updated_user;                 // 修正ユーザー
    private $created_user;                 // 作成ユーザー
    private $updated_at;                 // 修正日時
    private $created_at;                 // 作成日時

 
    public function getIdAttribute()
    {
        return $this->id;
    }

    public function setIdAttribute($value)
    {
        $this->id = $value;
    }

    public function getApplytermfromAttribute()
    {
        return $this->apply_term_from;
    }

    public function setApplytermfromAttribute($value)
    {
        $this->apply_term_from = $value;
    }
     
    public function getCodeAttribute()
    {
        return $this->code;
    }

    public function setCodeAttribute($value)
    {
        $this->code = $value;
    }
     
    public function getDepartmentcodeAttribute()
    {
        return $this->department_code;
    }

    public function setDepartmentcodeAttribute($value)
    {
        $this->department_code = $value;
    }

    public function getNameAttribute()
    {
        return $this->name;
    }

    public function setNameAttribute($value)
    {
        $this->name = $value;
    }
     
    public function getKanaAttribute()
    {
        return $this->kana;
    }

    public function setKanaAttribute($value)
    {
        $this->kana = $value;
    }

    public function getPasswordAttribute()
    {
        return $this->password;
    }

    public function setPasswordAttribute($value)
    {
        $this->password = $value;
    }

    public function getEmailAttribute()
    {
        return $this->email;
    }

    public function setEmailAttribute($value)
    {
        $this->email = $value;
    }

    public function getEmploymentstatusAttribute()
    {
        return $this->employment_status;
    }

    public function setEmploymentstatusAttribute($value)
    {
        $this->employment_status = $value;
    }

    public function getWorkingtimetablenoAttribute()
    {
        return $this->working_timetable_no;
    }

    public function setWorkingtimetablenoAttribute($value)
    {
        $this->working_timetable_no = $value;
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


    /**
     * ユーザー新規登録
     *
     * @return void
     */
    public function insertNewUser(){
        DB::table($this->table)->insert(
            [
                'apply_term_from' => $this->apply_term_from,
                'code' => $this->code,
                'employment_status' => $this->employment_status,
                'department_code' => $this->department_code,
                'name' => $this->name,
                'kana' => $this->kana,
                'working_timetable_no' => $this->working_timetable_no,
                'email' => $this->email,
                'password' => $this->password,
                'created_user'=>$this->created_user,
                'created_at'=>$this->created_at
            ]
        );
    }

    /**
     * ユーザー編集
     *
     * @return void
     */
    public function updateUser(){
        DB::table($this->table)
            ->where('id', $this->id)
            ->update([
                'apply_term_from' => $this->apply_term_from,
                'code' => $this->code,
                'department_code' => $this->department_code,
                'employment_status' => $this->employment_status,
                'name' => $this->name,
                'kana' => $this->kana,
                'working_timetable_no' => $this->working_timetable_no,
                'email' => $this->email,
                'updated_user'=>$this->updated_user,
                'updated_at' => $this->updated_at
                ]
            );
    }

    /**
     * ユーザー詳細取得
     *
     * @return void
     */
    public function getUserDetails(){
        $data = DB::table($this->table)
            ->select(
                $this->table.'.id',
                $this->table.'.apply_term_from',
                $this->table.'.code',
                $this->table.'.employment_status',
                $this->table.'.department_code',
                $this->table.'.name',
                $this->table.'.kana',
                $this->table.'.working_timetable_no',
                $this->table.'.email',
                $this->table.'.password'
            )
            ->where($this->table.'.code', $this->code)
            ->where($this->table.'.is_deleted', 0)
            ->get();

        return $data;
    }

    /**
     * パスワード変更
     * 
     * @return void
     */
    public function updatePassWord(){
        DB::table($this->table)
            ->where('code', $this->code)
            ->update([
                'password' => $this->password,
                'updated_user'=>$this->updated_user,
                'updated_at' => $this->updated_at
            ]);
    }

    /**
     * 論理削除
     *
     * @return void
     */
    public function updateIsDelete(){
        DB::table($this->table)
            ->where('id', $this->id)
            ->update(['is_deleted' => 1]);
    }

    
    /**
     * 削除
     *
     * @return void
     */
    public function delUserData(){
        DB::table($this->table)
            ->where('code', $this->code)
            ->where('is_deleted', 0)
            ->delete();
    }
}
