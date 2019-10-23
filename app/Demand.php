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
    protected $table_users = 'users';
    protected $table_generalcodes = 'generalcodes';

    private $no;                        // 申請番号
    private $doc_code;                  // 申請書類コード
    private $log_no;                    // 履歴番号
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

    // ------------- implements --------------

    private $param_department_code;             // 申請者部署
    private $param_user_code;                   // 申請者
    private $param_doc_code;                    // 申請書類コード
    private $param_limit;                       // 取得件数最大

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
                't1.department_code as department_code',
                't3.name as department_name',
                't1.code as user_code',
                't1.name as user_name',
                't6.no',
                't6.doc_code',
                't7.code_name as doc_code_name',
                't6.log_no',
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
            ->addselect('t6.mail_address');
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
                $join->on('t6.department_code', '=', 't1.department_code');
                $join->on('t6.user_code', '=', 't1.code');
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
            });
        $mainquery
            ->where('t1.code', '=', $this->param_user_code);
    
        if (isset($this->param_doc_code)) {
            $mainquery
                ->where('t5.doc_code', '=', $this->param_doc_code);
        }
        $mainquery
            ->where('t1.is_deleted', '=', 0)
            ->orderBy('t6.demand_date', 'desc');

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
        return $result;
    }
    
}
