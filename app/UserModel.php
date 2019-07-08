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

    private $code;                  
    private $department_code;                  
    private $name;                  
    private $kana;                  
    private $password;                  
    private $email;                  
    private $employment_status;                  
    private $working_timetable_no;                  
 
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
                'employment_status' => $this->password,
                'department_code' => $this->department_code,
                'name' => $this->name,
                'kana' => $this->kana,
                'working_timetable_no' => $this->working_timetable_no,
                'email' => $this->email,
                'password' => $this->password,
                'created_at'=>$systemdate
            ]
        );
    }
}
