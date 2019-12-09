<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;


class UserModel extends Model
{
    protected $table = 'users';
    protected $table_card_infomations = 'card_informations';
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
    private $management;                    // 勤怠管理対象
    private $roe;                           // 権限
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
        if ($value == 0) {
            $value = 1;
        }
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

    // 勤怠管理対象
    public function getManagementAttribute()
    {
        return $this->management;
    }

    public function setManagementAttribute($value)
    {
        $this->management = $value;
    }

    // 権限
    public function getRoleAttribute()
    {
        return $this->role;
    }

    public function setRoleAttribute($value)
    {
        $this->role = $value;
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
        try {
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
                    'created_at'=>$this->created_at,
                    'management' => $this->management,
                    'role' => $this->role
                ]
            );
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_insert_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_insert_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * ユーザー編集
     *
     * @return void
     */
    public function updateUser(){
        try {
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
                'updated_at' => $this->updated_at,
                'management' => $this->management,
                'role' => $this->role
                ]
            );
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_update_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_update_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * ユーザー詳細取得
     *
     * @return void
     */
    public function getUserDetails(){
        try {
            $subquery = DB::table($this->table_card_infomations)
                ->selectRaw('id')
                ->selectRaw('user_code')
                ->selectRaw('card_idm')
                ->where('is_deleted', '=', 0);
    
            $data = DB::table($this->table)
                ->leftJoinSub($subquery, 't1', function ($join) { 
                    $join->on('t1.user_code', '=', $this->table.'.code');
                })
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
                    $this->table.'.password',
                    $this->table.'.management',
                    $this->table.'.role',
                    't1.card_idm'
                )
                ->where($this->table.'.code', $this->code)
                ->where($this->table.'.is_deleted', 0)
                ->get();
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
     * パスワード変更
     * 
     * @return void
     */
    public function updatePassWord(){
        try {
            DB::table($this->table)
            ->where('code', $this->code)
            ->update([
                'password' => $this->password,
                'updated_user'=>$this->updated_user,
                'updated_at' => $this->updated_at
            ]);
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_update_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_update_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 論理削除
     *
     * @return void
     */
    public function updateIsDelete(){
        try {
            DB::table($this->table)
                ->where('id', $this->id)
                ->update(['is_deleted' => 1]);
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_update_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_update_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    
    /**
     * 削除
     *
     * @return void
     */
    public function delUserData(){
        try {
            DB::table($this->table)
                ->where('code', $this->code)
                ->where('is_deleted', 0)
                ->delete();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_delete_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_delete_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }
}
