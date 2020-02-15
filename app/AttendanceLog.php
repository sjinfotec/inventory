<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\ApiCommonController;

class AttendanceLog extends Model
{
    protected $table = 'attendance_logs';

    private $department_code;                   // 部署コード
    private $employment_status;                 // 雇用形態
    private $user_code;                         // ユーザー
    private $working_date;                      // 日付
    private $mode;                              // 打刻モード
    private $record_time;                       // 打刻時間
    private $event_mode;                        // ＰＣイベントモード
    private $event_time;                        // ＰＣイベント時間
    private $created_user;    
    private $updated_user;                  
    private $created_at;                  
    private $updated_at;                  
    private $is_deleted;                  

    // 部署コード
    public function getDepartmentcodeAttribute()
    {
        return $this->department_code;
    }

    public function setDepartmentcodeAttribute($value)
    {
        $this->department_code = $value;
    }

    // 雇用形態
    public function getEmploymentstatusAttribute()
    {
        return $this->employment_status;
    }

    public function setEmploymentstatusAttribute($value)
    {
        $this->employment_status = $value;
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

    // 日付
    public function getWorkingdateAttribute()
    {
        return $this->working_date;
    }

    public function setWorkingdateAttribute($value)
    {
        $this->working_date = $value;
    }

    // 打刻モード
    public function getModeAttribute()
    {
        return $this->mode;
    }

    public function setModeAttribute($value)
    {
        $this->mode = $value;
    }

    // 打刻時間
    public function getRecordtimeAttribute()
    {
        return $this->record_time;
    }

    public function setRecordtimeAttribute($value)
    {
        $this->record_time = $value;
    }

    // ＰＣイベントモード
    public function getEventmodeAttribute()
    {
        return $this->event_mode;
    }

    public function setEventmodeAttribute($value)
    {
        $this->event_mode = $value;
    }


    // ＰＣイベント時間
    public function getEventtimeAttribute()
    {
        return $this->event_time;
    }

    public function setEventtimeAttribute($value)
    {
        $this->event_time = $value;
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

    private $param_department_code;             // 部署コード
    private $param_employment_status;           // 雇用形態
    private $param_user_code;                   // ユーザー
    private $param_working_date;                // 日付

    // 部署コード
    public function getParamdepartmentcodeAttribute()
    {
        return $this->param_department_code;
    }

    public function setParamdepartmentcodeAttribute($value)
    {
        $this->param_department_code = $value;
    }

    // 雇用形態
    public function getParamemploymentstatusAttribute()
    {
        return $this->param_employment_status;
    }

    public function setParamemploymentstatusAttribute($value)
    {
        $this->param_employment_status = $value;
    }


    // ユーザー
    public function getParamusercodeAttribute()
    {
        return $this->param_user_code;
    }

    public function setParamusercodeAttribute($value)
    {
        $this->param_user_code = $value;
    }


    // 日付
    public function getParamworkingdateAttribute()
    {
        return $this->param_working_date;
    }

    public function setParamworkingdateAttribute($value)
    {
        $this->param_working_date = $value;
    }

    /**
     * 検索
     *
     * @return void
     */
    public function getDemandList($targetdate, $situation){

        try {
            // 適用期間日付の取得
            $apicommon = new ApiCommonController();
            // usersの最大適用開始日付subquery
            $subquery3 = $apicommon->getUserApplyTermSubquery($targetdate);
            // departmentsの最大適用開始日付subquery
            $subquery4 = $apicommon->getDepartmentApplyTermSubquery($targetdate);

            $subquery5 = DB::table($this->table)
                ->select(
                    $this->table.'.no',
                    $this->table.'.doc_code',
                    $this->table.'.seq',
                    $this->table.'.department_code',
                    $this->table.'.user_code');
            $subquery5
                ->selectRaw('MAX('.$this->table.'.log_no) as log_no')
                ->where($this->table.'.user_code', '=', $this->param_user_code)
                ->groupBy($this->table.'.no',
                    $this->table.'.doc_code',
                    $this->table.'.seq',
                    $this->table.'.department_code',
                    $this->table.'.user_code');

            $subquery6 = DB::table($this->table_demands)
                ->select(
                    $this->table_demands.'.no',
                    $this->table_demands.'.doc_code',
                    $this->table_demands.'.nmail_department_code',
                    $this->table_demands.'.nmail_user_code',
                    $this->table_demands.'.nmail_seq');
            $subquery6
                ->selectRaw('MAX('.$this->table_demands.'.log_no) as log_no')
                ->where($this->table_demands.'.nmail_user_code', '=', $this->param_user_code)
                ->groupBy($this->table_demands.'.no',
                    $this->table_demands.'.doc_code',
                    $this->table_demands.'.nmail_department_code',
                    $this->table_demands.'.nmail_user_code',
                    $this->table_demands.'.nmail_seq');

            // mainqueryにsunqueryを組み込む
            $mainquery = DB::table($this->table_users.' AS t1')
                ->select(
                    't6.id as id',
                    't1.department_code as department_code',
                    't3.name as department_name',
                    't1.code as user_code',
                    't1.name as user_name',
                    't6.no',
                    't6.doc_code',
                    't9.code_name as doc_code_name',
                    't6.log_no',
                    't6.seq',
                    't6.status',
                    't12.code_name as status_name',
                    't8.demand_date',
                    't8.date_from',
                    't8.date_to'
                    );
            $mainquery
                ->selectRaw("DATE_FORMAT(t8.demand_date,'%Y年%m月%d日') as demand_date_name")
                ->selectRaw("DATE_FORMAT(t8.date_from,'%Y年%m月%d日') as date_from_name")
                ->selectRaw("DATE_FORMAT(t8.date_to,'%Y年%m月%d日') as date_to_name");
            $mainquery
                ->addselect('t8.demand_reason')
                ->addselect('t8.before_after')
                ->addselect('t10.code_name as before_after_name')
                ->addselect('t6.mail_result')
                ->addselect('t11.code_name as mail_result_name')
                ->addselect('t6.mail_address')
                ->addselect('t6.nmail_department_code')
                ->addselect('t13.name as nmail_department_name')
                ->addselect('t6.nmail_user_code')
                ->addselect('t15.name as nmail_user_name')
                ->addselect('t8.department_code')
                ->addselect('t17.name as demand_department_name')
                ->addselect('t8.user_code')
                ->addselect('t19.name as demand_user_name')
                ->addselect('t16.row_no as detail_row_no')
                ->addselect('t16.working_item as detail_working_item')
                ->addselect('t16.date_from as detail_date_from')
                ->addselect('t16.time_from as detail_time_from')
                ->addselect('t16.date_to as detail_date_to')
                ->addselect('t16.time_to as detail_time_to')
                ->addselect('t16.scheduled_time as detail_scheduled_time')
                ->addselect('t16.demand_reason as detail_demand_reason');
            $mainquery
                ->JoinSub($subquery4, 't3', function ($join) { 
                    $join->on('t3.code', '=', 't1.department_code');
                })
                ->JoinSub($subquery3, 't4', function ($join) { 
                    $join->on('t4.code', '=', 't1.code');
                    $join->on('t4.max_apply_term_from', '=', 't1.apply_term_from');
                })
                ->JoinSub($subquery5, 't5', function ($join) { 
                    $join->on('t5.department_code', '=', 't1.department_code');
                    $join->on('t5.user_code', '=', 't1.code');
                })
                ->Join($this->table.' as t6', function ($join) { 
                    $join->on('t6.no', '=', 't5.no');
                    $join->on('t6.seq', '=', 't5.seq');
                    $join->on('t6.log_no', '=', 't5.log_no');
                })
                ->JoinSub($subquery6, 't7', function ($join) { 
                    $join->on('t7.no', '=', 't6.no');
                    $join->on('t7.doc_code', '=', 't6.doc_code');
                    $join->on('t7.nmail_seq', '=', 't6.seq');
                })
                ->Join($this->table_demands.' as t8', function ($join) { 
                    $join->on('t8.no', '=', 't7.no');
                    $join->on('t8.nmail_department_code', '=', 't7.nmail_department_code');
                    $join->on('t8.nmail_user_code', '=', 't7.nmail_user_code');
                    $join->on('t8.nmail_seq', '=', 't7.nmail_seq');
                    $join->on('t8.log_no', '=', 't7.log_no');
                })
                ->leftJoin($this->table_generalcodes.' as t9', function ($join) { 
                    $join->on('t9.code', '=', 't6.doc_code')
                    ->where('t9.identification_id', '=', Config::get('const.C026.value'))
                    ->where('t9.is_deleted', '=', 0);
                })
                ->leftJoin($this->table_generalcodes.' as t10', function ($join) { 
                    $join->on('t10.code', '=', 't8.before_after')
                    ->where('t10.identification_id', '=', Config::get('const.C029.value'))
                    ->where('t10.is_deleted', '=', 0);
                })
                ->leftJoin($this->table_generalcodes.' as t11', function ($join) { 
                    $join->on('t11.code', '=', 't6.mail_result')
                    ->where('t11.identification_id', '=', Config::get('const.C030.value'))
                    ->where('t11.is_deleted', '=', 0);
                })
                ->leftJoin($this->table_generalcodes.' as t12', function ($join) { 
                    $join->on('t12.code', '=', 't6.status')
                    ->where('t12.identification_id', '=', Config::get('const.C028.value'))
                    ->where('t12.is_deleted', '=', 0);
                })
                ->JoinSub($subquery4, 't13', function ($join) { 
                    $join->on('t13.code', '=', 't8.nmail_department_code');
                })
                ->JoinSub($subquery3, 't14', function ($join) { 
                    $join->on('t14.code', '=', 't8.nmail_user_code');
                })
                ->Join($this->table_users.' as t15', function ($join) { 
                    $join->on('t15.code', '=', 't8.nmail_user_code');
                    $join->on('t15.apply_term_from', '=', 't14.max_apply_term_from')
                    ->where('t15.is_deleted', '=', 0);
                })
                ->leftJoin($this->table_demand_details.' as t16', function ($join) { 
                    $join->on('t16.no', '=', 't8.no');
                    $join->on('t16.log_no', '=', 't8.log_no');
                })
                ->JoinSub($subquery4, 't17', function ($join) { 
                    $join->on('t17.code', '=', 't8.department_code');
                })
                ->JoinSub($subquery3, 't18', function ($join) { 
                    $join->on('t18.code', '=', 't8.user_code');
                })
                ->Join($this->table_users.' as t19', function ($join) { 
                    $join->on('t19.code', '=', 't8.user_code');
                    $join->on('t19.apply_term_from', '=', 't18.max_apply_term_from')
                    ->where('t19.is_deleted', '=', 0);
                });
            $mainquery
                ->where('t1.code', '=', $this->param_user_code);
        
            if (isset($this->param_doc_code)) {
                $mainquery
                    ->where('t5.doc_code', '=', $this->param_doc_code);
            }
        
            if (isset($situation)) {
                if ($situation == Config::get('const.C031.approval_requesting')) {
                    $mainquery
                        ->whereBetween('t6.status', [Config::get('const.C028.applying') , Config::get('const.C028.approving')]);
                }
            }
            $mainquery
                ->where('t1.is_deleted', '=', 0)
                ->where('t15.is_deleted', '=', 0)
                ->where('t19.is_deleted', '=', 0)
                ->orderBy('t8.demand_date', 'asc')
                ->orderBy('t8.no', 'asc');

            if (isset($this->param_limit)) {
                $mainquery
                    ->limit($this->param_limit);
            }

            $results = $mainquery->get();
    
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
        return $results;
    }

    /**
     * 申請番号による取得
     *
     * @return void
     */
    public function getDemandfromNo(){

        try {
            $mainquery = DB::table($this->table)
                ->where($this->table.'.no', '=', $this->param_no);

            if (isset($this->param_user_code)) {
                $mainquery
                    ->where($this->table.'.user_code', '=', $this->param_user_code);
            }
            if (isset($this->param_seq)) {
                $mainquery
                    ->where($this->table.'.seq', '=', $this->param_seq);
            }
            $mainquery
                ->where($this->table.'.is_deleted', '=', 0)
                ->orderBy($this->table.'.log_no', 'desc');

            $results = $mainquery->get();
    
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
        return $results;
    }

    /**
     * 申請番号最大シーケンス取得
     *
     * @return void
     */
    public function getMaxSeq($targetdate){

        try {
            $mainquery = DB::table($this->table);
            if (isset($this->param_doc_code)) {
                $mainquery
                    ->where($this->table.'.doc_code', '=', $this->param_doc_code);
            }
            $mainquery
                ->where($this->table.'.demand_now', '=', $targetdate)
                ->where($this->table.'.is_deleted', '=', 0);

            $results = $mainquery>max('seq');

        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_maxget_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_maxget_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

        return $results;
    }

    /**
     * 承認登録
     *
     * @return void
     */
    public function insertApproval(){
        try {
            DB::table($this->table)->insert(
                [
                    'no' => $this->no,
                    'doc_code' => $this->doc_code,
                    'log_no' => $this->log_no,
                    'seq' => $this->seq,
                    'status' => $this->status,
                    'department_code' => $this->department_code,
                    'user_code' => $this->user_code,
                    'approval_date' => $this->approval_date,
                    'remand_reason' => $this->remand_reason,
                    'mail_result' => $this->mail_result,
                    'mail_address' => $this->mail_address,
                    'nmail_department_code' => $this->nmail_department_code,
                    'nmail_user_code' => $this->nmail_user_code,
                    'created_user' => $this->created_user,
                    'created_at'=>$this->created_at
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
}
