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
    private $code;                  
    private $department_code;                  
    private $name;                  
    private $kana;                  
    private $password;                  
    private $email;                  
    private $employment_status;                  
    private $working_timetable_no;                  
 
    public function getIdAttribute()
    {
        return $this->id;
    }

    public function setIdAttribute($value)
    {
        $this->id = $value;
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

    /**
     * ユーザー新規登録
     *
     * @return void
     */
    public function insertNewUser(){
        $systemdate = Carbon::now();
        DB::table('users')->insert(
            [
                'code' => $this->code,
                'employment_status' => $this->employment_status,
                'department_id' => $this->department_code,
                'name' => $this->name,
                'kana' => $this->kana,
                'working_timetable_no' => $this->working_timetable_no,
                'email' => $this->email,
                'password' => $this->password,
                'created_at'=>$systemdate
            ]
        );
    }

    /**
     * ユーザー編集
     *
     * @return void
     */
    public function updateUser(){
        $systemdate = Carbon::now();
        DB::table($this->table)
            ->where('id', $this->id)
            ->update([
                'code' => $this->code,
                'department_id' => $this->department_code,
                'employment_status' => $this->employment_status,
                'name' => $this->name,
                'kana' => $this->kana,
                'working_timetable_no' => $this->working_timetable_no,
                'email' => $this->email,
                'updated_at' => $systemdate
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
                $this->table.'.code',
                $this->table.'.employment_status',
                $this->table.'.department_id',
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
        $systemdate = Carbon::now();
        DB::table($this->table)
            ->where('code', $this->code)
            ->update([
                'password' => $this->password,
                'updated_at' => $systemdate
                ]);
    }

    /**
     * 論理削除
     *
     * @return void
     */
    public function updateIsDelete(){
        DB::table($this->table)
            ->where('code', $this->code)
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
