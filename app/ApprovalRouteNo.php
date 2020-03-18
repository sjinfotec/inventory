<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\ApiCommonController;

class ApprovalRouteNo extends Model
{
    protected $table = 'approval_route_nos';
    protected $table_approval_authorizers = 'approval_authorizers';
    protected $table_users = 'users';
    protected $table_departments = 'departments';

    private $id;                                // id
    private $approval_route_no;                 // 承認ルート番号
    private $apply_department_code;             // 適用部署
    private $name;                              // 承認ルート名称
    private $created_user;    
    private $updated_user;                  
    private $created_at;                  
    private $updated_at;                  
    private $is_deleted;                  

    // id
    public function getIdAttribute()
    {
        return $this->id;
    }

    public function setIdAttribute($value)
    {
        $this->id = $value;
    }

    // 承認ルート番号
    public function getApprovalroutenoAttribute()
    {
        return $this->approval_route_no;
    }

    public function setApprovalroutenoAttribute($value)
    {
        $this->approval_route_no = $value;
    }

    // 適用部署
    public function getApplydepartmentcodeAttribute()
    {
        return $this->apply_department_code;
    }

    public function setApplydepartmentcodeAttribute($value)
    {
        $this->apply_department_code = $value;
    }

    // 承認ルート名称
    public function getNameAttribute()
    {
        return $this->name;
    }

    public function setNameAttribute($value)
    {
        $this->name = $value;
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

    // ------------- implements --------------

    private $param_id;                          // id
    private $param_approval_route_no;           // 承認ルート番号
    private $param_apply_department_code;       // 適用部署

    // id
    public function getParamidAttribute()
    {
        return $this->param_id;
    }

    public function setParamidAttribute($value)
    {
        $this->param_id = $value;
    }

    // 承認ルート番号
    public function getParamapprovalroutenoAttribute()
    {
        return $this->param_approval_route_no;
    }

    public function setParamapprovalroutenoAttribute($value)
    {
        $this->param_approval_route_no = $value;
    }

    // 適用部署
    public function getParamapplydepartmentcodeAttribute()
    {
        return $this->param_apply_department_code;
    }

    public function setParamapplydepartmentcodeAttribute($value)
    {
        $this->param_apply_department_code = $value;
    }

    // ------------- method --------------

    /**
     * 検索
     *
     * @return void
     */
    public function getList(){

        try {
            $mainquery = DB::table($this->table)
                ->select(
                    $this->table.'.approval_route_no',
                    $this->table.'.apply_department_code',
                    $this->table.'.name'
                );
            if(!empty($this->param_approval_route_no)) {
                $mainquery
                    ->where($this->table.'.approval_route_no',$this->param_approval_route_no);
            }
            if(!empty($this->param_apply_department_code)) {
                $mainquery
                    ->where($this->table.'.apply_department_code',$this->param_apply_department_code);
            }
            $mainquery
                ->orderBy($this->table.'.approval_route_no', 'asc');
            $result = $mainquery->get();
            return $result;

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
     * 明細検索
     *
     * @return void
     */
    public function getDetail(){

        try {
            $mainquery = DB::table($this->table)
                ->select(
                    $this->table.'.approval_route_no',
                    $this->table.'.apply_department_code',
                    $this->table.'.name',
                    't1.seq',
                    't1.main_sub',
                    't1.approval_department_code',
                    't1.approval_user_code');
            $mainquery
                ->selectRaw('t2.name asapply_department_name')
                ->selectRaw('t3.name as approval_department_name')
                ->selectRaw('t4.name as approval_user_name')
                ->selectRaw('t6.code_name as employment_name');
            // join
            // 適用期間日付の取得
            $apicommon = new ApiCommonController();
            // usersの最大適用開始日付subquery
            $subquery1 = $apicommon->getUserApplyTermSubquery($this->paramtodate);
            // departmentsの最大適用開始日付subquery
            $subquery2 = $apicommon->getDepartmentApplyTermSubquery($this->paramtodate);
            // 明細
            $mainquery
                ->Join($this->table_approval_authorizers.' as t1', function ($join) { 
                    $join->on('t1.approval_route_no', '=', $this->table.'.approval_route_no')
                    ->where('t1.is_deleted',0);
            });
            // 適用部署名
            $mainquery
                ->leftJoinSub($subquery2, 't2', function ($join) { 
                    $join->on('t2.code', '=', $this->table.'.apply_department_code')
                    ->where($this->table.'.is_deleted',0);
            });
            // 承認者部署名
            $mainquery
                ->leftJoinSub($subquery2, 't3', function ($join) { 
                    $join->on('t3.code', '=', $this->table.'.approval_department_code')
                    ->where($this->table.'.is_deleted',0);
            });
            // 承認者名
            $mainquery
                ->leftJoin($this->table_users.' as t4', function ($join) { 
                    $join->on('t4.department_code', '=', 't1.approval_department_code');
                    $join->on('t4.code', '=', 't1.approval_user_code')
                    ->where('t4.is_deleted', '=', 0);
                })
                ->JoinSub($subquery3, 't5', function ($join) { 
                    $join->on('t5.code', '=', 't4.code');
                    $join->on('t5.max_apply_term_from', '=', 't4.apply_term_from');
                });
            // 雇用形態
            $mainquery
                ->leftJoin($this->table_generalcodes.' as t6', function ($join) { 
                    $join->on('t6.code', '=',  't4.employment_status')
                    ->where('t6.identification_id', '=', Config::get('const.C001.value'))
                    ->where('t6.is_deleted', '=', 0);
            });
        
            // // 承認ルート番号
            if(!empty($this->param_approval_route_no)) {
                $mainquery
                    ->where($this->table.'.approval_route_no',$this->param_approval_route_no);
            }
            // // 適用部署
            if(!empty($this->param_apply_department_code)) {
                $mainquery
                    ->where($this->table.'.apply_department_code',$this->param_apply_department_code);
            }
            $mainquery->where($this->table.'.is_deleted',0);
            $mainquery
                ->orderBy($this->table.'.approval_route_no', 'asc');
            $result = $mainquery->get();
            return $result;

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
}
