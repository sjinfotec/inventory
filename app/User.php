<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use App\Http\Controllers\ApiCommonController;


class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','code', 'email', 'password'
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
        try {
            // usersの最大適用開始日付subquery
            // 適用期間日付の取得
            $apicommon = new ApiCommonController();
            $subquery2 = $apicommon->getUserApplyTermSubquery(null);
            $mainquery = DB::table('users')
                ->select(
                    'users.id',
                    'users.department_code as department_code',
                    'users.employment_status as employment_status',
                    'users.name as name',
                    'users.code as code',
                    'card_informations.card_idm as card_idm'
                )
                ->Join('card_informations', function ($join) { 
                    $join->on('card_informations.user_code', '=', 'users.code');
                    $join->on('card_informations.department_code', '=', 'users.department_code')
                    ->where('card_informations.is_deleted',0);
                });
            $mainquery
                ->JoinSub($subquery2, 't1', function ($join) { 
                    $join->on('t1.code', '=', 'users.code');
                    $join->on('t1.max_apply_term_from', '=', 'users.apply_term_from');
                });
            $mainquery
                ->where('card_informations.card_idm',$card_id)
                ->where('users.role',"<>",10)
                ->where('users.is_deleted',0);
            $data = $mainquery->get();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
        
        return $data;
    }

    /**
     * ボタン打刻用ユーザー取得
     *
     * @param [type] $card_id
     * @return void
     */
    public function getUserData($user_code){
        try {
            // usersの最大適用開始日付subquery
            // 適用期間日付の取得
            $apicommon = new ApiCommonController();
            $subquery2 = $apicommon->getUserApplyTermSubquery(null);
            $mainquery = DB::table('users')
                ->select(
                    'users.id',
                    'users.department_code as department_code',
                    'users.employment_status as employment_status',
                    'users.name as name',
                    'users.code as code'
                );
            $mainquery
                ->JoinSub($subquery2, 't1', function ($join) { 
                    $join->on('t1.code', '=', 'users.code');
                    $join->on('t1.max_apply_term_from', '=', 'users.apply_term_from');
                });
            $mainquery
                ->where('users.code',$user_code)
                ->where('users.role',"<>",10)
                ->where('users.is_deleted',0);
            $data = $mainquery->get();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
        
        return $data;
    }

    /**
     * 全ユーザー取得
     *
     * @return void
     */
    public function getNotRegistUser(){

        try {
            // usersの最大適用開始日付subquery
            // 適用期間日付の取得
            $apicommon = new ApiCommonController();
            $subquery1 = $apicommon->getUserApplyTermSubquery(null);
            // departmentsの最大適用開始日付subquery
            $subquery2 = $apicommon->getDepartmentApplyTermSubquery(null);

            $mainquery = DB::table('users as t1')
                ->select(
                    't1.code as user_code',
                    't1.name as user_name',
                    't1.department_code as department_code'
                    )
                ->selectRaw("IFNULL(t4.name,'') as department_name")
                ->leftJoin('card_informations as t2', function ($join) { 
                    $join->on('t2.user_code', '=', 't1.code');
                    $join->on('t2.department_code', '=', 't1.department_code')
                    ->where('t2.is_deleted',0);
                });
            $mainquery
                ->JoinSub($subquery1, 't3', function ($join) { 
                    $join->on('t3.code', '=', 't1.code');
                    $join->on('t3.max_apply_term_from', '=', 't1.apply_term_from');
                });
            $mainquery
                ->leftJoinSub($subquery2, 't4', function ($join) { 
                    $join->on('t4.code', '=', 't1.department_code')
                    ->where('t1.is_deleted', '=', 0);
                });
            $mainquery
                ->where('t1.role',"<>",10)
                ->where('t1.is_deleted',0)
                ->whereNull('t2.card_idm');      // whereNull 

            $data = $mainquery->get();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

        return $data;
    }

}
