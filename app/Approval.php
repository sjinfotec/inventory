<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\ApiCommonController;

class Approval extends Model
{
    protected $table = 'approvals';
    protected $table_demands = 'demands';
    protected $table_demand_details = 'demand_details';
    protected $table_users = 'users';
    protected $table_confirms = 'confirms';
    protected $table_generalcodes = 'generalcodes';

    private $no;                    // 申請番号
    private $doc_code;              // 申請書類コード
    private $seq;                   // 承認順番
    private $log_no;                // 履歴番号
    private $status;                // ステータス
    private $department_code;       // 承認者部署
    private $user_code;             // 承認者
    private $approval_date;         // 承認日または差し戻し日
    private $remand_reason;         // 差し戻し理由
    private $mail_result;           // メール送信結果
    private $mail_address;          // メール宛先
    private $nmail_department_code; // メール宛先者部署
    private $nmail_user_code;       // メール宛先者
    private $created_user;    
    private $updated_user;                  
    private $created_at;                  
    private $updated_at;                  
    private $is_deleted;                  


    // 申請番号
    public function getNoAttribute()
    {
        return $this->no;
    }

    public function setNoAttribute($value)
    {
        $this->no = $value;
    }


    // 申請書類コード
    public function getDoccodeAttribute()
    {
        return $this->doc_code;
    }

    public function setDoccodeAttribute($value)
    {
        $this->doc_code = $value;
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


    // 履歴番号
    public function getLognoAttribute()
    {
        return $this->log_no;
    }

    public function setLognoAttribute($value)
    {
        $this->log_no = $value;
    }


    // ステータス
    public function getStatusAttribute()
    {
        return $this->status;
    }

    public function setStatusAttribute($value)
    {
        $this->status = $value;
    }


    // 承認者部署
    public function getDepartmentcodeAttribute()
    {
        return $this->department_code;
    }

    public function setDepartmentcodeAttribute($value)
    {
        $this->department_code = $value;
    }


    // 承認者
    public function getUsercodeAttribute()
    {
        return $this->user_code;
    }

    public function setUsercodeAttribute($value)
    {
        $this->user_code = $value;
    }


    // 承認日または差し戻し日
    public function getApprovaldateAttribute()
    {
        return $this->approval_date;
    }

    public function setApprovaldateAttribute($value)
    {
        $this->approval_date = $value;
    }


    // 差し戻し理由
    public function getRemandreasonAttribute()
    {
        return $this->remand_reason;
    }

    public function setRemandreasonAttribute($value)
    {
        $this->remand_reason = $value;
    }


    // メール送信結果
    public function getMailresultAttribute()
    {
        return $this->mail_result;
    }

    public function setMailresultAttribute($value)
    {
        $this->mail_result = $value;
    }


    // メール宛先
    public function getMailaddressAttribute()
    {
        return $this->mail_address;
    }

    public function setMailaddressAttribute($value)
    {
        $this->mail_address = $value;
    }

    // メール宛先者部署
    public function getNmaildepartmentcodeAttribute()
    {
        return $this->nmail_department_code;
    }

    public function setNmaildepartmentcodeAttribute($value)
    {
        $this->nmail_department_code = $value;
    }

    // メール宛先者
    public function getNmailusercodeAttribute()
    {
        return $this->nmail_user_code;
    }
    public function setNmailusercodeAttribute($value)
    {
        $this->nmail_user_code = $value;
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

    private $param_no;                          // 申請番号
    private $param_user_code;                   // 承認者
    private $param_doc_code;                    // 申請書類コード
    private $param_department_code;             // 承認者部署
    private $param_seq;                         // 承認順番
    private $param_limit;                       // 取得件数最大

    // 申請番号
    public function getParamNoAttribute()
    {
        return $this->param_no;
    }

    public function setParamNoAttribute($value)
    {
        $this->param_no = $value;
    }

    // 申請書類コード
    public function getParamDoccodeAttribute()
    {
        return $this->param_doc_code;
    }

    public function setParamDoccodeAttribute($value)
    {
        $this->param_doc_code = $value;
    }


    // 承認者部署
    public function getParamDepartmentcodeAttribute()
    {
        return $this->param_department_code;
    }

    public function setParamDepartmentcodeAttribute($value)
    {
        $this->param_department_code = $value;
    }


    // 承認者
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


    // 取得件数最大
    public function getParamLimitAttribute()
    {
        return $this->param_limit;
    }

    public function setParamLimitAttribute($value)
    {
        $this->param_limit = $value;
    }

    /**
     * 検索
     *
     * @return void
     */
    public function getDemandList($targetdate, $situation){

        try {
            \DB::enableQueryLog();
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
            $result = $mainquery
                ->get();

            \Log::debug(
                'sql_debug_log',
                [
                    'getDemandList' => \DB::getQueryLog()
                ]
            );
    
        }catch(\PDOException $pe){
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            throw $e;
        }
        return $result;
    }

    /**
     * 申請番号による取得
     *
     * @return void
     */
    public function getDemandfromNo(){

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
            ->orderBy($this->table.'.log_no', 'desc')
            ->get();
        return $mainquery;
    }

    /**
     * 申請番号最大シーケンス取得
     *
     * @return void
     */
    public function getMaxSeq($targetdate){

        $mainquery = DB::table($this->table);
        if (isset($this->param_doc_code)) {
            $mainquery
                ->where($this->table.'.doc_code', '=', $this->param_doc_code);
        }
        $max = $mainquery
            ->where($this->table.'.demand_now', '=', $targetdate)
            ->where($this->table.'.is_deleted', '=', 0)
            ->max('seq');
        return $max;
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
            Log::error('method = getBeforeDailyMaxData '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_insert_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('method = getBeforeDailyMaxData '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_insert_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }
}
