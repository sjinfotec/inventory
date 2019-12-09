<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\ApiCommonController;

class Demand extends Model
{
    protected $table = 'demands';
    protected $table_demand_details = 'demand_details';
    protected $table_users = 'users';
    protected $table_confirms = 'confirms';
    protected $table_generalcodes = 'generalcodes';

    private $no;                        // 申請番号
    private $doc_code;                  // 申請書類コード
    private $demand_now;                // 申請当日日付
    private $log_no;                    // 履歴番号
    private $seq;                       // 申請番号シーケンス
    private $status;                    // ステータス
    private $department_code;           // 申請者部署
    private $user_code;                 // 申請者
    private $demand_date;               // 申請日
    private $date_from;                 // 申請期間開始
    private $date_to;                   // 申請期間終了
    private $demand_reason;             // 申請理由
    private $before_after;              // 事前事後
    private $mail_result;               // メール送信結果
    private $mail_address;              // メール宛先
    private $nmail_department_code;     // メール宛先者部署
    private $nmail_user_code;           // メール宛先者
    private $nmail_seq;                 // 承認者シーケンス
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


    // 申請当日日付
    public function getDemandnowAttribute()
    {
        return $this->demand_now;
    }

    public function setDemandnowAttribute($value)
    {
        $this->demand_now = $value;
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


    // 履歴番号
    public function getLognoAttribute()
    {
        return $this->log_no;
    }

    public function setLognoAttribute($value)
    {
        $this->log_no = $value;
    }

    // 申請番号シーケンス
    public function getSeqAttribute()
    {
        return $this->seq;
    }

    public function setSeqAttribute($value)
    {
        $this->seq = $value;
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


    // 申請者部署
    public function getDepartmentcodeAttribute()
    {
        return $this->department_code;
    }

    public function setDepartmentcodeAttribute($value)
    {
        $this->department_code = $value;
    }


    // 申請者
    public function getUsercodeAttribute()
    {
        return $this->user_code;
    }

    public function setUsercodeAttribute($value)
    {
        $this->user_code = $value;
    }


    // 申請日
    public function getDemanddateAttribute()
    {
        return $this->demand_date;
    }

    public function setDemanddateAttribute($value)
    {
        $this->demand_date = $value;
    }


    // 申請期間開始
    public function getDatefromAttribute()
    {
        return $this->date_from;
    }

    public function setDatefromAttribute($value)
    {
        $this->date_from = $value;
    }


    // 申請期間終了
    public function getDatetoAttribute()
    {
        return $this->date_to;
    }

    public function setDatetoAttribute($value)
    {
        $this->date_to = $value;
    }


    // 申請理由
    public function getDemandreasonAttribute()
    {
        return $this->demand_reason;
    }

    public function setDemandreasonAttribute($value)
    {
        $this->demand_reason = $value;
    }


    // 事前事後
    public function getBeforeafterAttribute()
    {
        return $this->before_after;
    }

    public function setBeforeafterAttribute($value)
    {
        $this->before_after = $value;
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
    public function getNmailDepartmentCodeAttribute()
    {
        return $this->nmail_department_code;
    }

    public function setNmailDepartmentCodeAttribute($value)
    {
        $this->nmail_department_code = $value;
    }

    // メール宛先者
    public function getNmailUserCodeAttribute()
    {
        return $this->nmail_user_code;
    }

    public function setNmailUserCodeAttribute($value)
    {
        $this->nmail_user_code = $value;
    }

    // 承認者シーケンス
    public function getNmailseqAttribute()
    {
        return $this->nmail_seq;
    }

    public function setNmailseqAttribute($value)
    {
        $this->nmail_seq = $value;
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
    private $param_user_code;                   // 申請者
    private $param_doc_code;                    // 申請書類コード
    private $param_department_code;             // 申請者部署
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


    // 申請者部署
    public function getParamDepartmentcodeAttribute()
    {
        return $this->param_department_code;
    }

    public function setParamDepartmentcodeAttribute($value)
    {
        $this->param_department_code = $value;
    }


    // 申請者
    public function getParamUsercodeAttribute()
    {
        return $this->param_user_code;
    }

    public function setParamUsercodeAttribute($value)
    {
        $this->param_user_code = $value;
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

    // ------------- メソッド --------------

    /**
     * 検索
     *
     * @return void
     */
    public function getDemandList($targetdate){

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
                    $this->table.'.department_code',
                    $this->table.'.user_code');
            $subquery5
                ->selectRaw('MAX('.$this->table.'.log_no) as log_no')
                ->where($this->table.'.user_code', '=', $this->param_user_code)
                ->where($this->table.'.status', '<', Config::get('const.C028.unknown'))
                ->groupBy($this->table.'.no', $this->table.'.doc_code', $this->table.'.department_code', $this->table.'.user_code');

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
                    't7.code_name as doc_code_name',
                    't6.log_no',
                    't6.seq',
                    't6.status',
                    't10.code_name as status_name',
                    't6.demand_date',
                    't6.date_from',
                    't6.date_to'
                    );
            $mainquery
                ->selectRaw("DATE_FORMAT(t6.demand_date,'%Y年%m月%d日') as demand_date_name")
                ->selectRaw("DATE_FORMAT(t6.date_from,'%Y年%m月%d日') as date_from_name")
                ->selectRaw("DATE_FORMAT(t6.date_to,'%Y年%m月%d日') as date_to_name");
            $mainquery
                ->addselect('t6.demand_reason')
                ->addselect('t6.before_after')
                ->addselect('t8.code_name as before_after_name')
                ->addselect('t6.mail_result')
                ->addselect('t9.code_name as mail_result_name')
                ->addselect('t6.mail_address')
                ->addselect('t6.nmail_department_code')
                ->addselect('t11.name as nmail_department_name')
                ->addselect('t6.nmail_user_code')
                ->addselect('t13.name as nmail_user_name')
                ->addselect('t6.nmail_seq')
                ->addselect('t14.row_no as detail_row_no')
                ->addselect('t14.department_code as detail_department_code')
                ->addselect('t14.user_code as detail_user_code')
                ->addselect('t14.working_item as detail_working_item')
                ->addselect('t14.date_from as detail_date_from')
                ->addselect('t14.time_from as detail_time_from')
                ->addselect('t14.date_to as detail_date_to')
                ->addselect('t14.time_to as detail_time_to')
                ->addselect('t14.scheduled_time as detail_scheduled_time')
                ->addselect('t14.demand_reason as detail_demand_reason');
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
                    $join->on('t6.department_code', '=', 't5.department_code');
                    $join->on('t6.user_code', '=', 't5.user_code');
                    $join->on('t6.no', '=', 't5.no');
                    $join->on('t6.doc_code', '=', 't5.doc_code');
                    $join->on('t6.log_no', '=', 't5.log_no');
                })
                ->leftJoin($this->table_generalcodes.' as t7', function ($join) { 
                    $join->on('t7.code', '=', 't6.doc_code')
                    ->where('t7.identification_id', '=', Config::get('const.C026.value'))
                    ->where('t7.is_deleted', '=', 0);
                })
                ->leftJoin($this->table_generalcodes.' as t8', function ($join) { 
                    $join->on('t8.code', '=', 't6.before_after')
                    ->where('t8.identification_id', '=', Config::get('const.C029.value'))
                    ->where('t8.is_deleted', '=', 0);
                })
                ->leftJoin($this->table_generalcodes.' as t9', function ($join) { 
                    $join->on('t9.code', '=', 't6.mail_result')
                    ->where('t9.identification_id', '=', Config::get('const.C030.value'))
                    ->where('t9.is_deleted', '=', 0);
                })
                ->leftJoin($this->table_generalcodes.' as t10', function ($join) { 
                    $join->on('t10.code', '=', 't6.status')
                    ->where('t10.identification_id', '=', Config::get('const.C028.value'))
                    ->where('t10.is_deleted', '=', 0);
                })
                ->JoinSub($subquery4, 't11', function ($join) { 
                    $join->on('t11.code', '=', 't6.nmail_department_code');
                })
                ->JoinSub($subquery3, 't12', function ($join) { 
                    $join->on('t12.code', '=', 't6.nmail_user_code');
                })
                ->Join($this->table_users.' as t13', function ($join) { 
                    $join->on('t13.code', '=', 't6.nmail_user_code');
                    $join->on('t13.apply_term_from', '=', 't12.max_apply_term_from')
                    ->where('t13.is_deleted', '=', 0);
                })
                ->leftJoin($this->table_demand_details.' as t14', function ($join) { 
                    $join->on('t14.no', '=', 't6.no');
                    $join->on('t14.log_no', '=', 't6.log_no')
                    ->where('t14.is_deleted', '=', 0);
                });
            $mainquery
                ->where('t1.code', '=', $this->param_user_code);
        
            if (isset($this->param_doc_code)) {
                $mainquery
                    ->where('t5.doc_code', '=', $this->param_doc_code);
            }

            $mainquery
                ->where('t1.is_deleted', '=', 0)
                ->orderBy('t6.demand_date', 'desc')
                ->orderBy('t6.no', 'desc');

            if (isset($this->param_limit)) {
                $mainquery
                    ->limit($this->param_limit);
            }
            $result = $mainquery->get();

        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

        return $result;
    }

    /**
     * メールアドレス取得
     *
     * @return void
     */
    public function getMailAddress($targetdate){

        try {
            // 適用期間日付の取得
            $apicommon = new ApiCommonController();
            // usersの最大適用開始日付subquery
            $subquery3 = $apicommon->getUserApplyTermSubquery($targetdate);

            $subquery1 = DB::table($this->table)
                ->select(
                    $this->table.'.no as no');
            $subquery1
                ->selectRaw('MAX('.$this->table.'.log_no) as log_no')
                ->where($this->table.'.no', '=', $this->param_no)
                ->where($this->table.'.is_deleted', '=', 0)
                ->groupBy($this->table.'.no');

            // mainqueryにsunqueryを組み込む
            $mainquery = DB::table($this->table.' AS t1')
                ->select(
                    't1.no as no',
                    't4.confirm_department_code as confirm_department_code',
                    't4.user_code as user_code',
                    't4.main_sub as main_sub',
                    't4.seq as seq',
                    't6.email'
                    );
            $mainquery
                ->JoinSub($subquery1, 't2', function ($join) { 
                    $join->on('t2.no', '=', 't1.no');
                    $join->on('t2.log_no', '=', 't1.log_no');
                })
                ->Join($this->table_confirms.' as t3', function ($join) { 
                    $join->on('t3.department_code', '=', 't1.department_code');
                    $join->on('t3.confirm_department_code', '=', 't1.nmail_department_code');
                    $join->on('t3.user_code', '=', 't1.nmail_user_code');
                    $join->on('t3.seq', '=', 't1.nmail_seq');
                })
                ->Join($this->table_confirms.' as t4', function ($join) { 
                    $join->on('t4.department_code', '=', 't3.department_code');
                    $join->on('t4.seq', '=', 't3.seq');
                })
                ->JoinSub($subquery3, 't5', function ($join) { 
                    $join->on('t5.code', '=', 't4.user_code');
                })
                ->Join($this->table_users.' as t6', function ($join) { 
                    $join->on('t6.code', '=', 't5.code');
                    $join->on('t6.department_code', '=', 't4.confirm_department_code');
                    $join->on('t6.apply_term_from', '=', 't5.max_apply_term_from');
                });
            $mainquery
                ->where('t1.is_deleted', '=', 0)
                ->where('t3.is_deleted', '=', 0)
                ->where('t4.is_deleted', '=', 0)
                ->where('t6.is_deleted', '=', 0);
            $result = $mainquery->get();

        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
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

        try {
            $mainquery = DB::table($this->table)
                ->where($this->table.'.no', '=', $this->param_no)
                ->orderBy($this->table.'.log_no', 'desc')
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

        return $mainquery;
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
            $max = $mainquery
                ->where($this->table.'.demand_now', '=', $targetdate)
                ->where($this->table.'.is_deleted', '=', 0)
                ->max('seq');
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_maxget_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_maxget_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
        return $max;
    }

    /**
     * 申請番号最大履歴番号取得
     *
     * @return void
     */
    public function getMaxLogno($demandno){

        try {
            $max = DB::table($this->table)
                ->where($this->table.'.no', '=', $demandno)
                ->where($this->table.'.is_deleted', '=', 0)
                ->max('log_no');
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_maxget_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_maxget_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
        return $max;
    }

    /**
     * 申請登録
     *
     * @return void
     */
    public function insertDemand(){
        try {
            DB::table($this->table)->insert(
                [
                    'no' => $this->no,
                    'doc_code' => $this->doc_code,
                    'demand_now' => $this->demand_now,
                    'log_no' => $this->log_no,
                    'seq' => $this->seq,
                    'status' => $this->status,
                    'department_code' => $this->department_code,
                    'user_code' => $this->user_code,
                    'demand_date' => $this->demand_date,
                    'date_from' => $this->date_from,
                    'date_to' => $this->date_to,
                    'demand_reason' => $this->demand_reason,
                    'before_after' => $this->before_after,
                    'mail_result' => $this->mail_result,
                    'mail_address' => $this->mail_address,
                    'nmail_department_code' => $this->nmail_department_code,
                    'nmail_user_code' => $this->nmail_user_code,
                    'nmail_seq' => $this->nmail_seq,
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
