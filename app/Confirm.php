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
    protected $table_generalcodes = 'generalcodes';
 
    private $id;                  
    private $account_id;                        // ログインユーザーのアカウント
    private $confirm_no;                  
    private $main_sub;                  
    private $seq;    
    private $confirm_department_code;                  
    private $user_department_code;                  
    private $user_code;
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

    // ログインユーザーのアカウント
    public function getAccountidAttribute()
    {
        return $this->account_id;
    }

    public function setAccountidAttribute($value)
    {
        $this->account_id = $value;
    }

    // 承認ルート番号
    public function getConfirmnoAttribute()
    {
        return $this->confirm_no;
    }
    public function setConfirmnoAttribute($value)
    {
        $this->confirm_no = $value;
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

    // ルート適用部署
    public function getConfirmDepartmentcodeAttribute()
    {
        return $this->confirm_department_code;
    }
    public function setConfirmDepartmentcodeAttribute($value)
    {
        $this->confirm_department_code = $value;
    }

    // ユーザー部署
    public function getUserdepartmentcodeAttribute()
    {
        return $this->user_department_code;
    }
    public function setUserdepartmentcodeAttribute($value)
    {
        $this->user_department_code = $value;
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
    private $param_account_id;                  // ログインユーザーのアカウント
    private $param_confirm_no;
    private $param_user_department_code;
    private $param_confirm_department_code;
    private $param_user_code;
    private $param_seq;
    private $param_main_sub;

    // ログインユーザーのアカウント
    public function getParamAccountidAttribute()
    {
        return $this->param_account_id;
    }

    public function setParamAccountidAttribute($value)
    {
        $this->param_account_id = $value;
    }

    // 承認ルート番号
    public function getParamConfirmnoAttribute()
    {
        return $this->param_confirm_no;
    }
    public function setParamConfirmnoAttribute($value)
    {
        $this->param_confirm_no = $value;
    }

    // ユーザー部署
    public function getParamUserepartmentcodeAttribute()
    {
        return $this->param_user_department_code;
    }
    public function setParamUserepartmentcodeAttribute($value)
    {
        $this->param_user_department_code = $value;
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
    public function selectConfirm($targetdate){
        try {
            // 適用期間日付の取得
            $apicommon = new ApiCommonController();
            // usersの最大適用開始日付subquery
            $subquery3 = $apicommon->getUserApplyTermSubquery($targetdate, $this->param_account_id);
            $subquery1 = DB::table($this->table_users.' as t1')
                ->select('t1.account_id', 't1.code', 't1.name', 't1.department_code')
                ->JoinSub($subquery3, 't2', function ($join) { 
                    $join->on('t2.account_id', '=', 't1.account_id');
                    $join->on('t2.code', '=', 't1.code');
                    $join->on('t2.max_apply_term_from', '=', 't1.apply_term_from')
                    ->where('t2.account_id', '=', $this->param_account_id)
                    ->where('t1.is_deleted', '=', 0);
                })
                ->where('t1.is_deleted', '=', 0);
            // departmentsの最大適用開始日付subquery
            $subquery4 = $apicommon->getDepartmentApplyTermSubquery($targetdate, $this->param_account_id);

            $mainquery = DB::table($this->table.' AS t1')
                ->select(
                    't1.id',
                    't1.confirm_no',
                    't1.seq',
                    't1.main_sub',
                    't1.confirm_department_code',
                    't1.user_department_code',
                    't1.user_code',
                    't1.created_user',
                    't1.updated_user',
                    't1.created_at',
                    't1.updated_at',
                    't2.code_name as main_sub_name',
                    't3.name as confirm_department_name',
                    't4.name as user_department_name',
                    't5.name as user_name'
                );
            $mainquery
                ->leftJoin($this->table_generalcodes.' as t2', function ($join) { 
                    $join->on('t2.code', '=', 't1.main_sub')
                    ->where('t2.identification_id', '=', Config::get('const.C027.value'))
                    ->where('t2.is_deleted', 0);
                });
            $mainquery
                ->leftJoinSub($subquery4, 't3', function ($join) { 
                    $join->on('t3.account_id', '=', 't1.account_id');
                    $join->on('t3.code', '=', 't1.confirm_department_code')
                    ->where('t3.account_id', '=', $this->param_account_id)
                    ->where('t1.is_deleted', '=', 0);
                });
            $mainquery
                ->leftJoinSub($subquery4, 't4', function ($join) { 
                    $join->on('t4.account_id', '=', 't1.account_id');
                    $join->on('t4.code', '=', 't1.user_department_code')
                    ->where('t4.account_id', '=', $this->param_account_id)
                    ->where('t1.is_deleted', '=', 0);
                });
            $mainquery
                ->leftJoinSub($subquery1, 't5', function ($join) { 
                    $join->on('t4.account_id', '=', 't1.account_id');
                    $join->on('t5.department_code', '=', 't1.user_department_code');
                    $join->on('t5.code', '=', 't1.user_code')
                    ->where('t5.account_id', '=', $this->param_account_id)
                    ->where('t1.is_deleted', '=', 0);
                });
            if (isset($this->param_confirm_no)) {
                $mainquery->where('t1.confirm_no', $this->param_confirm_no);
            }
            if (isset($this->param_user_department_code)) {
                $mainquery->where('t1.user_department_code', $this->param_user_department_code);
            }
            if (isset($this->param_confirm_department_code)) {
                $mainquery->where('t1.confirm_department_code', $this->param_confirm_department_code);
            }
            if (isset($this->param_user_code)) {
                $mainquery->where('t1.user_code', $this->param_user_code);
            }
            if (isset($this->param_seq)) {
                if ($this->param_seq == Config::get('const.CONFIRM_SEQ.final_confirm')) {
                    $mainquery->where('t1.seq', $this->param_seq);
                } else {
                    $mainquery->whereNotIn('t1.seq', [Config::get('const.CONFIRM_SEQ.final_confirm')]);
                }
            }
            if (isset($this->param_main_sub)) {
                $mainquery->where('t1.main_sub', $this->param_main_sub);
            }
            $result = $mainquery
                ->where('t1.account_id', $this->param_account_id)
                ->where('t1.is_deleted', 0)
                ->orderBy('t1.confirm_no')
                ->orderBy('t1.seq')
                ->orderBy('t1.main_sub')
                ->get();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
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
            $subquery3 = $apicommon->getUserApplyTermSubquery($targetdate, $this->param_account_id);
            // departmentsの最大適用開始日付subquery
            $subquery4 = $apicommon->getDepartmentApplyTermSubquery($targetdate, $this->param_account_id);

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
                    $join->on('t2.account_id', '=', $this->table.'.account_id');
                    $join->on('t2.code', '=', $this->table.'.user_code');
                    $join->on('t2.department_code', '=', $this->table.'.confirm_department_code')
                    ->where('t2.account_id', '=', $this->param_account_id);
                    ->where('t2.is_deleted', '=', 0);
                })
                ->Join($this->table_departments.' as t3', function ($join) { 
                    $join->on('t3.account_id', '=', $this->table.'.account_id');
                    $join->on('t3.code', '=', $this->table.'.confirm_department_code')
                    ->where('t3.account_id', '=', $this->param_account_id);
                    ->where('t3.is_deleted', '=', 0);
                })
                ->JoinSub($subquery3, 't4', function ($join) { 
                    $join->on('t4.account_id', '=', $this->table.'.account_id');
                    $join->on('t4.code', '=', $this->table.'.user_code');
                    $join->on('t4.max_apply_term_from', '=', 't2.apply_term_from')
                    ->where('t4.account_id', '=', $this->param_account_id);
                })
                ->JoinSub($subquery4, 't5', function ($join) { 
                    $join->on('t5.account_id', '=', $this->table.'.account_id');
                    $join->on('t5.code', '=', $this->table.'.confirm_department_code')
                    ->where('t5.account_id', '=', $this->param_account_id);
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
                ->where($this->table.'.account_id', $this->table.'.account_id')
                ->where($this->table.'.is_deleted', 0)
                ->orderBy($this->table.'.seq')
                ->orderBy($this->table.'.main_sub')
                ->get();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    
        return $result;
    }

    /**
     * 取得(MAXNO)
     *
     * @return void
     */
    public function getMaxNo(){
        try {
            $maxno = DB::table($this->table)
                ->selectRaw('IFNULL(MAX('.$this->table.'.confirm_no), 0) AS MAXNO')
                ->where($this->table.'.is_deleted', 0)
                ->get();
            return $maxno;
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
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
                    'confirm_no' => $this->confirm_no,
                    'seq' => $this->seq,
                    'main_sub' => $this->main_sub,
                    'confirm_department_code' => $this->confirm_department_code,
                    'user_department_code' => $this->user_department_code,
                    'user_code' => $this->user_code,
                    'created_user' => $this->created_user,
                    'created_at' => $this->created_at,
                    'updated_user'=>$this->updated_user,
                    'updated_at' => $this->updated_at
                ]
            );
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_insert_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_insert_error')).'$e');
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
            if (isset($this->param_user_department_code)) {
                $mainquery->where($this->table.'.user_department_code', $this->param_user_department_code);
            }
            if (isset($this->param_seq)) {
                if ($this->param_seq == Config::get('const.CONFIRM_SEQ.final_confirm')) {
                    $mainquery->where($this->table.'.seq', $this->param_seq);
                } else {
                    $mainquery->whereNotIn($this->table.'.seq', [Config::get('const.CONFIRM_SEQ.final_confirm')]);
                }
            }
            $mainquery->update([
                'user_department_code' => $this->user_department_code,
                'user_code' => $this->user_code,
                'seq' => $this->seq,
                'main_sub' => $this->main_sub,
                'updated_user'=>$this->updated_user,
                'updated_at' => $this->updated_at
                ]
            );
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_update_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_update_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 論理削除(DELETE)
     *
     * @return void
     */
    public function deleteConfirm(){
        try {
            $mainquery = DB::table($this->table);
            if (isset($this->param_confirm_no)) {
                $mainquery->where($this->table.'.confirm_no', $this->param_confirm_no);
            }
            if (isset($this->param_seq)) {
                if ($this->param_seq == Config::get('const.CONFIRM_SEQ.final_confirm')) {
                    $mainquery->where($this->table.'.seq', $this->param_seq);
                } else {
                    $mainquery->whereNotIn($this->table.'.seq', [Config::get('const.CONFIRM_SEQ.final_confirm')]);
                }
            }
            $mainquery->update([
                'is_deleted' => 1
                ]
            );
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_delete_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_delete_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

}
