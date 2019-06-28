<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

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

}
