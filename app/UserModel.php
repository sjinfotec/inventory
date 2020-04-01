<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\ApiCommonController;
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
    private $official_position;
    private $password;                  
    private $email;                  
    private $mobile_email;                  // モバイル用アドレス                 
    private $employment_status;                  
    private $kill_from_date;
    private $working_timetable_no;
    private $management;                    // 勤怠管理対象
    private $roe;                           // 権限
    private $is_deleted;                    // 削除フラグ
    private $updated_user;                  // 修正ユーザー
    private $created_user;                  // 作成ユーザー
    private $updated_at;                    // 修正日時
    private $created_at;                    // 作成日時

 
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
 
    public function getOfficialpositionAttribute()
    {
        return $this->official_position;
    }

    public function setOfficialpositionAttribute($value)
    {
        $this->official_position = $value;
    }
 
    public function getKillfromdateAttribute()
    {
        return $this->kill_from_date;
    }

    public function setKillfromdateAttribute($value)
    {
        $this->kill_from_date = $value;
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

    public function getMobileEmailAttribute()
    {
        return $this->mobile_email;
    }

    public function setMobileEmailAttribute($value)
    {
        $this->mobile_email = $value;
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

    // ---------------- param --------------------------------
    private $param_code;                            // ユーザーCODE
    private $param_apply_term_from;                 // 適用期間開始
    private $param_killvalue;                       // 退職開始日を条件に含む(true)
     
    public function getParamcodeAttribute()
    {
        return $this->param_code;
    }

    public function setParamcodeAttribute($value)
    {
        $this->param_code = $value;
    }

    // 適用期間開始
    public function getParamapplytermfromAttribute()
    {
        return $this->param_apply_term_from;
    }

    public function setParamapplytermfromAttribute($value)
    {
        $this->param_apply_term_from = $value;
    }

    // 退職開始日を条件に含む
    public function getKillvalueAttribute()
    {
        return $this->param_killvalue;
    }

    public function setKillvalueAttribute($value)
    {
        $this->param_killvalue = $value;
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
                    'official_position' => $this->official_position,
                    'kill_from_date' => $this->kill_from_date,
                    'working_timetable_no' => $this->working_timetable_no,
                    'email' => $this->email,
                    'mobile_email' => $this->mobile_email,
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
                'official_position' => $this->official_position,
                'kill_from_date' => $this->kill_from_date,
                'working_timetable_no' => $this->working_timetable_no,
                'email' => $this->email,
                'mobile_email' => $this->mobile_email,
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
        // 適用期間日付の取得
        $dt = new Carbon();
        $target_date = $dt->format('Ymd');
        try {
            if(empty($this->param_apply_term_from)){
                $this->param_apply_term_from = $target_date;
            }
            // usersの最大適用開始日付subquery
            $subquery1 = DB::table($this->table)
                ->select('code as code')
                ->selectRaw('MAX(apply_term_from) as max_apply_term_from')
                ->where('apply_term_from', '<=',$this->param_apply_term_from)
                ->where('role', '<', Config::get('const.C017.admin_user'));

            if(!empty($this->param_killvalue)){
                if (!$this->param_killvalue) {
                    $subquery1->where('kill_from_date', '>',$this->param_apply_term_from);
                }
            } else {
                $subquery1->where('kill_from_date', '>',$this->param_apply_term_from);
            }
            $subquery1
                ->where('is_deleted', '=', 0)
                ->groupBy('code');
            // ICカード
            $subquery2 = DB::table($this->table_card_infomations)
                ->select('user_code', 'card_idm')
                ->where('is_deleted', '=', 0);
            $case_sql1 = "CASE t1.kill_from_date = ".Config::get('const.INIT_DATE.maxdate');
            $case_sql1 = $case_sql1." WHEN TRUE THEN NULL ELSE DATE_FORMAT(t1.kill_from_date, '%Y-%m-%d') END as kill_from_date";
            $case_sql2 = "CASE IFNULL(t2.max_apply_term_from,".Config::get('const.INIT_DATE.initdate').") = t1.apply_term_from ";
            $case_sql2 = $case_sql2." WHEN TRUE THEN 1";
            $case_sql2 = $case_sql2." ELSE CASE IFNULL(t2.max_apply_term_from,".Config::get('const.INIT_DATE.initdate').") < t1.apply_term_from ";
            $case_sql2 = $case_sql2."      WHEN TRUE THEN 2 ELSE 0 END ";
            $case_sql2 = $case_sql2." END  as result";
            $mainquery = DB::table($this->table.' AS t1')
                ->select(
                    't1.id',
                    't1.code',
                    't1.department_code',
                    't1.employment_status',
                    't1.name',
                    't1.kana',
                    't1.official_position',
                    't1.working_timetable_no',
                    't1.email',
                    't1.mobile_email',
                    't1.password',
                    't1.management',
                    't1.role'
                    )
                ->selectRaw("IFNULL(t3.card_idm,'') as card_idm")
                ->selectRaw("DATE_FORMAT(t1.apply_term_from, '%Y-%m-%d') as apply_term_from")
                ->selectRaw($case_sql1)
                ->selectRaw($case_sql2);
            $mainquery
                ->leftJoinSub($subquery1, 't2', function ($join) { 
                    $join->on('t2.code', '=', 't1.code');
                })
                ->leftJoinSub($subquery2, 't3', function ($join) { 
                    $join->on('t3.user_code', '=', 't1.code');
                });
            $results = $mainquery
                ->where('t1.code', $this->code)
                ->where('t1.is_deleted', 0)
                ->orderBy('t1.apply_term_from', 'desc')
                ->get();

            return $results;
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
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
            $mainQuery = DB::table($this->table);
            if (!empty($this->param_code)) {
                $mainQuery
                    ->where('code', $this->param_code);
            }
            $mainQuery
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

    /**
     * ログインID（同ログインID）チェック
     *
     * @return boolean
     */
    public function isExistsCode(){
        try {
            $is_exists = DB::table($this->table)
                ->where('code',$this->param_code)
                ->where('is_deleted',0)
                ->exists();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_exists_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_exists_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

        return $is_exists;
    }
}
