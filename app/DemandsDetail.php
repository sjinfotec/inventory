<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\ApiCommonController;

class DemandsDetail extends Model
{
    protected $table = 'demand_details';
    protected $table_demands = 'demands';
    protected $table_users = 'users';
    protected $table_generalcodes = 'generalcodes';

    private $account_id;                // ログインユーザーのアカウント
    private $no;                        // 申請番号
    private $doc_code;                  // 申請書類コード
    private $log_no;                    // 履歴番号
    private $row_no;                    // 行番号
    private $department_code;           // 部署
    private $user_code;                 // 氏名
    private $working_item;              // 作業項目
    private $date_from;                 // 作業期間開始日付
    private $time_from;                 // 作業期間開始時刻
    private $date_to;                   // 作業期間終了日付
    private $time_to;                   // 作業期間終了時刻
    private $scheduled_time;            // 予定時間
    private $demand_reason;             // 申請理由
    private $created_user;    
    private $updated_user;                  
    private $created_at;                  
    private $updated_at;                  
    private $is_deleted;                  


    // ログインユーザーのアカウント
    public function getAccountidAttribute()
    {
        return $this->account_id;
    }

    public function setAccountidAttribute($value)
    {
        $this->account_id = $value;
    }

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


    // 行番号
    public function getRownoAttribute()
    {
        return $this->row_no;
    }

    public function setRownoAttribute($value)
    {
        $this->row_no = $value;
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


    // 氏名
    public function getUsercodeAttribute()
    {
        return $this->user_code;
    }

    public function setUsercodeAttribute($value)
    {
        $this->user_code = $value;
    }


    // 作業項目
    public function getWorkingitemAttribute()
    {
        return $this->working_item;
    }

    public function setWorkingitemAttribute($value)
    {
        $this->working_item = $value;
    }


    // 作業期間開始日付
    public function getDatefromAttribute()
    {
        return $this->date_from;
    }

    public function setDatefromAttribute($value)
    {
        $this->date_from = $value;
    }


    // 作業期間開始時刻
    public function getTimefromAttribute()
    {
        return $this->time_from;
    }

    public function setTimefromAttribute($value)
    {
        $this->time_from = $value;
    }


    // 作業期間終了日付
    public function getDatetoAttribute()
    {
        return $this->date_to;
    }

    public function setDatetoAttribute($value)
    {
        $this->date_to = $value;
    }


    // 作業期間終了時刻
    public function getTimetoAttribute()
    {
        return $this->time_to;
    }

    public function setTimetoAttribute($value)
    {
        $this->time_to = $value;
    }

    // 予定時間
    public function getScheduledtimeAttribute()
    {
        return $this->scheduled_time;
    }

    public function setScheduledtimeAttribute($value)
    {
        $this->scheduled_time = $value;
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

    private $param_account_id;                  // ログインユーザーのアカウント
    private $param_no;                          // 申請番号
    private $param_log_no;                      // 履歴番号
    private $param_department_code;             // 申請者部署
    private $param_user_code;                   // 申請者
    private $param_doc_code;                    // 申請書類コード
    private $param_limit;                       // 取得件数最大

    // ログインユーザーのアカウント
    public function getParamAccountidAttribute()
    {
        return $this->param_account_id;
    }

    public function setParamAccountidAttribute($value)
    {
        $this->param_account_id = $value;
    }

    // 申請番号
    public function getParamNoAttribute()
    {
        return $this->param_no;
    }

    public function setParamNoAttribute($value)
    {
        $this->param_no = $value;
    }

    // 履歴番号
    public function getParamLognoAttribute()
    {
        return $this->param_log_no;
    }

    public function setParamLognoAttribute($value)
    {
        $this->param_log_no = $value;
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
     * 申請取得
     *
     * @return void
     */
    public function getDemandDetailfromNo(){
        try {
            DB::table($this->table)
                ->where($this->table.'.no', '=', $this->param_no)
                ->where($this->table.'.log_no', '=', $this->param_log_no)
                ->where($this->table.'.is_deleted', '=', 0)
                ->orderBy($this->table.'.row_no', 'asc')
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
    }

    /**
     * 申請登録
     *
     * @return void
     */
    public function insertDemandDetail(){
        try {
            DB::table($this->table)->insert(
                [
                    'no' => $this->no,
                    'doc_code' => $this->doc_code,
                    'log_no' => $this->log_no,
                    'row_no' => $this->row_no,
                    'department_code' => $this->department_code,
                    'user_code' => $this->user_code,
                    'working_item' => $this->working_item,
                    'date_from' => $this->date_from,
                    'time_from' => $this->time_from,
                    'date_to' => $this->date_to,
                    'time_to' => $this->time_to,
                    'scheduled_time' => $this->scheduled_time,
                    'demand_reason' => $this->demand_reason,
                    'created_user' => $this->created_user,
                    'created_at'=>$this->created_at
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
    
}
