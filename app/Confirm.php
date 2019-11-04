<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ApiCommonController;
use Carbon\Carbon;

class Confirm extends Model
{
    protected $table = 'confirms';
    protected $table_users = 'users';
    protected $table_departments = 'departments';
 
    private $id;                  
    private $department_code;                  
    private $confirm_department_code;                  
    private $user_code;
    private $seq;    
    private $main_sub;                  
    private $created_user;    
    private $updated_user;                  
    private $created_at;                  
    private $updated_at;                  
    private $is_deleted;                  

    // ID
    public function getIdAttribute()
    {
        return $this->id;
    }
    public function setIdAttribute($value)
    {
        $this->id = $value;
    }

    // 部署
    public function getDepartmentcodeAttribute()
    {
        return $this->department_code;
    }
    public function setDepartmentcodeAttribute($value)
    {
        $this->department_code = $value;
    }

    // 承認部署
    public function getConfirmDepartmentcodeAttribute()
    {
        return $this->confirm_department_code;
    }
    public function setConfirmDepartmentcodeAttribute($value)
    {
        $this->confirm_department_code = $value;
    }

    // ユーザー
    public function getUsercodeAttribute()
    {
        return $this->user_code;
    }
    public function setUsercodeAttribute($value)
    {
        $this->user_code = $value;
    }

    // 承認順番
    public function getSeqAttribute()
    {
        return $this->seq;
    }
    public function setSeqAttribute($value)
    {
        $this->seq = $value;
    }

    // 正副区分
    public function getMainsubAttribute()
    {
        return $this->main_sub;
    }
    public function setMainsubAttribute($value)
    {
        $this->main_sub = $value;
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
     
    // 作成時間
    public function getCreatedatAttribute()
    {
        return $this->created_at;
    }
    public function setCreatedatAttribute($value)
    {
        $this->created_at = $value;
    }
     
    // 修正時間
    public function getUpdatedatAttribute()
    {
        return $this->updated_at;
    }
    public function setUpdatedatAttribute($value)
    {
        $this->updated_at = $value;
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

    // -------------------------- param ---------------------------------------
    private $param_department_code;
    private $param_confirm_department_code;
    private $param_user_code;
    private $param_seq;
    private $param_main_sub;

    // 部署
    public function getParamDepartmentcodeAttribute()
    {
        return $this->param_department_code;
    }
    public function setParamDepartmentcodeAttribute($value)
    {
        $this->param_department_code = $value;
    }

    // 承認部署
    public function getParamConfirmdepartmentcodeAttribute()
    {
        return $this->param_confirm_department_code;
    }
    public function setParamConfirmdepartmentcodeAttribute($value)
    {
        $this->param_confirm_department_code = $value;
    }

    // 承認ユーザー
    public function getParamUsercodeAttribute()
    {
        return $this->param_user_code;
    }
    public function setParamUsercodeAttribute($value)
    {
        $this->param_user_code = $value;
    }

    // 承認順番
    public function getParamSeqAttribute()
    {
        return $this->param_seq;
    }
    public function setParamSeqAttribute($value)
    {
        $this->param_seq = $value;
    }

    // 正副
    public function getParamMainsubAttribute()
    {
        return $this->param_main_sub;
    }
    public function setParamMainsubAttribute($value)
    {
        $this->param_main_sub = $value;
    }

    /**
     * 取得(SELECT)
     *
     * @return void
     */
    public function selectConfirm(){
        try {
            $mainquery = DB::table($this->table)
                ->select(
                    $this->table.'.id',
                    $this->table.'.department_code',
                    $this->table.'.confirm_department_code',
                    $this->table.'.user_code',
                    $this->table.'.seq',
                    $this->table.'.main_sub'
                );
            if (isset($this->param_department_code)) {
                $mainquery->where($this->table.'.department_code', $this->param_department_code);
            }
            if (isset($this->param_confirm_department_code)) {
                $mainquery->where($this->table.'.confirm_department_code', $this->param_confirm_department_code);
            }
            if (isset($this->param_user_code)) {
                $mainquery->where($this->table.'.user_code', $this->param_user_code);
            }
            if (isset($this->param_seq)) {
                if ($this->param_seq == Config::get('const.CONFIRM_SEQ.final_confirm')) {
                    $mainquery->where($this->table.'.seq', $this->param_seq);
                } else {
                    $mainquery->whereNotIn($this->table.'.seq', [Config::get('const.CONFIRM_SEQ.final_confirm')]);
                }
            }
            if (isset($this->param_main_sub)) {
                $mainquery->where($this->table.'.main_sub', $this->param_main_sub);
            }
            $result = $mainquery
                ->where($this->table.'.is_deleted', 0)
                ->orderBy($this->table.'.department_code')
                ->orderBy($this->table.'.id')
                ->get();
        }catch(\PDOException $pe){
            Log::error('method = selectConfirm '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('method = selectConfirm '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

        return $result;
    }

    /**
     * 取得(SELECT)
     *
     * @return void
     */
    public function selectConfirmList($targetdate){
        try {
            // 適用期間日付の取得
            $apicommon = new ApiCommonController();
            // usersの最大適用開始日付subquery
            $subquery3 = $apicommon->getUserApplyTermSubquery($targetdate);
            // departmentsの最大適用開始日付subquery
            $subquery4 = $apicommon->getDepartmentApplyTermSubquery($targetdate);

            $list_name = 'CASE '.$this->table.'.main_sub'.' WHEN 1 THEN ';
            $list_name .= "CONCAT('＜正＞',t3.name,'：',t2.name) ";
            $list_name .= "ELSE CONCAT('＜副＞',t3.name,'：',t2.name) ";
            $list_name .= "END as list_name ";
            $mainquery = DB::table($this->table)
                ->select(
                    $this->table.'.user_code'
                );
            $mainquery
                ->selectRaw($list_name);
            $mainquery
                ->Join($this->table_users.' as t2', function ($join) { 
                    $join->on('t2.code', '=', $this->table.'.user_code');
                    $join->on('t2.department_code', '=', $this->table.'.confirm_department_code')
                    ->where('t2.is_deleted', '=', 0);
                })
                ->Join($this->table_departments.' as t3', function ($join) { 
                    $join->on('t3.code', '=', $this->table.'.confirm_department_code')
                    ->where('t3.is_deleted', '=', 0);
                })
                ->JoinSub($subquery3, 't4', function ($join) { 
                    $join->on('t4.code', '=', $this->table.'.user_code');
                    $join->on('t4.max_apply_term_from', '=', 't2.apply_term_from');
                })
                ->JoinSub($subquery4, 't5', function ($join) { 
                    $join->on('t5.code', '=', $this->table.'.confirm_department_code');
                });
            if (isset($this->param_seq)) {
                if ($this->param_seq == Config::get('const.CONFIRM_SEQ.final_confirm')) {
                    $mainquery->where($this->table.'.seq', Config::get('const.CONFIRM_SEQ.final_confirm'));
                } elseif ($this->param_seq == Config::get('const.CONFIRM_SEQ.not_final_confirm')) {
                    $mainquery->where($this->table.'.seq', "<", Config::get('const.CONFIRM_SEQ.final_confirm'));
                }
            }
            if (isset($this->param_main_sub)) {
                if ($this->param_main_sub == Config::get('const.C027.main')) {
                    $mainquery->where($this->table.'.main_sub', Config::get('const.C027.main'));
                } elseif ($this->param_main_sub == Config::get('const.C027.sub')) {
                    $mainquery->where($this->table.'.main_sub', Config::get('const.C027.sub'));
                }
            }
            $result = $mainquery
                ->where($this->table.'.is_deleted', 0)
                ->orderBy($this->table.'.seq')
                ->orderBy($this->table.'.main_sub')
                ->get();
        }catch(\PDOException $pe){
            Log::error('method = selectConfirm '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('method = selectConfirm '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

        return $result;
    }

    /**
     * 登録(INSERT)
     *
     * @return void
     */
    public function insertConfirm(){
        try {
            DB::table($this->table)->insert(
                [
                    'department_code' => $this->department_code,
                    'confirm_department_code' => $this->confirm_department_code,
                    'user_code' => $this->user_code,
                    'seq' => $this->seq,
                    'main_sub' => $this->main_sub,
                    'created_user' => $this->created_user,
                    'created_at' => $this->created_at
                    ]
            );
        }catch(\PDOException $pe){
            Log::error('method = insertConfirm '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_insert_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('method = insertConfirm '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_insert_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 修正(UPDATE)
     *
     * @return void
     */
    public function updateConfirm(){
        try {
            $mainquery = DB::table($this->table);
            if (isset($this->param_department_code)) {
                $mainquery->where($this->table.'.department_code', $this->param_department_code);
            }
            if (isset($this->param_seq)) {
                if ($this->param_seq == Config::get('const.CONFIRM_SEQ.final_confirm')) {
                    $mainquery->where($this->table.'.seq', $this->param_seq);
                } else {
                    $mainquery->whereNotIn($this->table.'.seq', [Config::get('const.CONFIRM_SEQ.final_confirm')]);
                }
            }
            $mainquery->update([
                'department_code' => $this->department_code,
                'user_code' => $this->user_code,
                'seq' => $this->seq,
                'main_sub' => $this->main_sub,
                'updated_user'=>$this->updated_user,
                'updated_at' => $this->updated_at
                ]
            );
        }catch(\PDOException $pe){
            Log::error('method = updateConfirm '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_update_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('method = updateConfirm '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_update_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 削除(DELETE)
     *
     * @return void
     */
    public function deleteConfirm(){
        try {
            $mainquery = DB::table($this->table);
            if (isset($this->param_department_code)) {
                $mainquery->where($this->table.'.department_code', $this->param_department_code);
            }
            if (isset($this->param_seq)) {
                if ($this->param_seq == Config::get('const.CONFIRM_SEQ.final_confirm')) {
                    $mainquery->where($this->table.'.seq', $this->param_seq);
                } else {
                    $mainquery->whereNotIn($this->table.'.seq', [Config::get('const.CONFIRM_SEQ.final_confirm')]);
                }
            }
            $mainquery->delete();
        }catch(\PDOException $pe){
            Log::error('method = deleteConfirm '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_delete_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('method = deleteConfirm '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_delete_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

}
