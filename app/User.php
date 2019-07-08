<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','code', 'email', 'password',
   ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

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
     * カードに紐づいたユーザー取得
     *
     * @param [type] $card_id
     * @return void
     */
    public function getUserCardData($card_id){
        $data = DB::table('users')
            ->join('card_informations','users.code','=','card_informations.user_code')
            ->select(
                'users.id',
                'users.department_code as department_code',
                'users.name',
                'users.code',
                'card_informations.card_idm'
            )
            ->where('card_informations.card_idm',$card_id)
            ->where('users.is_deleted',0)
            ->get();

        return $data;
    }

    /**
     * 全ユーザー取得
     *
     * @return void
     */
    public function getNotRegistUser(){
        $data = DB::table('users')
            ->leftjoin('card_informations','users.code','=','card_informations.user_code')
            ->select(
                'users.id',
                'users.name',
                'users.code',
                'card_informations.card_idm'
            )
            ->where('users.is_deleted',0)
            ->get();

        return $data;
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
