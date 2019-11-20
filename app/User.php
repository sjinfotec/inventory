<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

    /**
     * カードに紐づいたユーザー取得
     *
     * @param [type] $card_id
     * @return void
     */
    public function getUserCardData($card_id){
        \DB::enableQueryLog();
        $data = DB::table('users')
            ->select(
                'users.id',
                'users.department_code as department_code',
                'users.employment_status',
                'users.name',
                'users.code',
                'card_informations.card_idm'
            )
            ->Join('card_informations', function ($join) { 
                $join->on('card_informations.user_code', '=', 'users.code');
                $join->on('card_informations.department_code', '=', 'users.department_code')
                ->where('card_informations.is_deleted',0);
            })
            ->where('card_informations.card_idm',$card_id)
            ->where('users.role',"<>",10)
            ->where('users.is_deleted',0)
            ->get();
        \Log::debug(
            'sql_debug_log',
            [
                'getUserCardData' => \DB::getQueryLog()
            ]
            );
        
        return $data;
    }

    /**
     * 全ユーザー取得
     *
     * @return void
     */
    public function getNotRegistUser(){

        $sunquery1 = DB::table('users as t1')
            ->select(
                't1.code as user_code',
                't1.department_code as department_code',
                't2.card_idm'
            )
            ->leftJoin('card_informations as t2', function ($join) { 
                $join->on('t2.user_code', '=', 't1.code');
                $join->on('t2.department_code', '=', 't1.department_code')
                ->where('t2.is_deleted',0);
            })
            ->where('t1.role',"<>",10)
            ->where('t1.is_deleted',0);

        $mainquery = DB::table('users as t3')
            ->select(
                't3.code as user_code',
                't3.name as user_name',
                't3.department_code as department_code',
                't5.name as department_name'
                )
            ->leftJoinSub($sunquery1, 't4', function ($join) { 
                $join->on('t4.user_code', '=', 't3.code');
                $join->on('t4.department_code', '=', 't3.department_code');
            })
            ->leftJoin('departments as t5', function ($join) { 
                $join->on('t5.code', '=', 't3.department_code')
                ->where('t5.is_deleted',0);
            })
            ->where('t3.role',"<>",10)
            ->where('t3.is_deleted',0)
            ->whereNull('t4.card_idm');      // whereNull 

        $data = $mainquery->get();

        return $data;
    }

}
