<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class Progressdetaile extends Model
{
    //
    //--------------- テーブル名 -----------------------------------
    protected $table = 'progress_details';
    //--------------- メンバー属性 -----------------------------------
    private $id;                              // ID
    private $order_no;                              // 受注番号
    private $seq;                              // 連番
    private $progress_no;                              // 工程NO
    private $product_processes_code;                              // 工程コード
    private $product_processes_detail_no;                              // 工程行NO
    private $device_code;                              // 機器コード
    private $department_code;                              // 部署コード
    private $users_code;                              // 社員コード
    private $process_history_no;                              // 加工履歴No
    private $work_kind;                              // 作業種別
    private $process_time_m;                              // 加工連番合計（分）
    private $process_time_h;                              // 加工連番合計（時）
    private $setup_history_no;                              // 段取り履歴No
    private $setup_time_m;                              // 段取り連番合計（分）
    private $setup_time_h;                              // 段取り連番合計（時）
    private $complete_date;                              // 完了日
    private $qr_code;                              // QRコード
    private $created_user;                              // 作成ユーザー
    private $updated_user;                              // 修正ユーザー
    private $created_at;                              // 作成日時
    private $updated_at;                              // 修正日時
    private $is_deleted;                              // 削除フラグ

    //--------------- メンバーアクセサ -----------------------------------
    //ID
    public function getIdAttribute()
    {
        return $this->id;
    }

    public function setIdAttribute($value)
    {
        $this->id = $value;
    }
    //受注番号
    public function getOrdernoAttribute()
    {
        return $this->order_no;
    }

    public function setOrdernoAttribute($value)
    {
        $this->order_no = $value;
    }
    //連番
    public function getSeqAttribute()
    {
        return $this->seq;
    }

    public function setSeqAttribute($value)
    {
        $this->seq = $value;
    }
    //工程NO
    public function getProgressnoAttribute()
    {
        return $this->progress_no;
    }

    public function setProgressnoAttribute($value)
    {
        $this->progress_no = $value;
    }
    //工程コード
    public function getProductprocessescodeAttribute()
    {
        return $this->product_processes_code;
    }

    public function setProductprocessescodeAttribute($value)
    {
        $this->product_processes_code = $value;
    }
    //工程行NO
    public function getProductprocessesdetailnoAttribute()
    {
        return $this->product_processes_detail_no;
    }

    public function setProductprocessesdetailnoAttribute($value)
    {
        $this->product_processes_detail_no = $value;
    }
    //機器コード
    public function getDevicecodeAttribute()
    {
        return $this->device_code;
    }

    public function setDevicecodeAttribute($value)
    {
        $this->device_code = $value;
    }
    //部署コード
    public function getDepartmentcodeAttribute()
    {
        return $this->department_code;
    }

    public function setDepartmentcodeAttribute($value)
    {
        $this->department_code = $value;
    }
    //社員コード
    public function getUserscodeAttribute()
    {
        return $this->users_code;
    }

    public function setUserscodeAttribute($value)
    {
        $this->users_code = $value;
    }
    //加工履歴No
    public function getProcesshistorynoAttribute()
    {
        return $this->process_history_no;
    }

    public function setProcesshistorynoAttribute($value)
    {
        $this->process_history_no = $value;
    }
    //作業種別
    public function getWorkkindAttribute()
    {
        return $this->work_kind;
    }

    public function setWorkkindAttribute($value)
    {
        $this->work_kind = $value;
    }
    //加工連番合計（分）
    public function getProcesstimemAttribute()
    {
        return $this->process_time_m;
    }

    public function setProcesstimemAttribute($value)
    {
        $this->process_time_m = $value;
    }
    //加工連番合計（時）
    public function getProcesstimehAttribute()
    {
        return $this->process_time_h;
    }

    public function setProcesstimehAttribute($value)
    {
        $this->process_time_h = $value;
    }
    //段取り履歴No
    public function getSetuphistorynoAttribute()
    {
        return $this->setup_history_no;
    }

    public function setSetuphistorynoAttribute($value)
    {
        $this->setup_history_no = $value;
    }
    //段取り連番合計（分）
    public function getSetuptimemAttribute()
    {
        return $this->setup_time_m;
    }

    public function setSetuptimemAttribute($value)
    {
        $this->setup_time_m = $value;
    }
    //段取り連番合計（時）
    public function getSetuptimehAttribute()
    {
        return $this->setup_time_h;
    }

    public function setSetuptimehAttribute($value)
    {
        $this->setup_time_h = $value;
    }
    //完了日
    public function getCompletedateAttribute()
    {
        return $this->complete_date;
    }

    public function setCompletedateAttribute($value)
    {
        $this->complete_date = $value;
    }
    //QRコード
    public function getQrcodeAttribute()
    {
        return $this->qr_code;
    }

    public function setQrcodeAttribute($value)
    {
        $this->qr_code = $value;
    }
    //作成ユーザー
    public function getCreateduserAttribute()
    {
        return $this->created_user;
    }

    public function setCreateduserAttribute($value)
    {
        $this->created_user = $value;
    }
    //修正ユーザー
    public function getUpdateduserAttribute()
    {
        return $this->updated_user;
    }

    public function setUpdateduserAttribute($value)
    {
        $this->updated_user = $value;
    }
    //作成日時
    public function getCreatedatAttribute()
    {
        return $this->created_at;
    }

    public function setCreatedatAttribute($value)
    {
        $this->created_at = $value;
    }
    //修正日時
    public function getUpdatedatAttribute()
    {
        return $this->updated_at;
    }

    public function setUpdatedatAttribute($value)
    {
        $this->updated_at = $value;
    }
    //削除フラグ
    public function getIsdeletedAttribute()
    {
        return $this->is_deleted;
    }

    public function setIsdeletedAttribute($value)
    {
        $this->is_deleted = $value;
    }

    //--------------- パラメータ項目属性 -----------------------------------
    private $param_id;                              // ID
    private $param_order_no;                              // 受注番号
    private $param_seq;                              // 連番
    private $param_progress_no;                              // 工程NO
    private $param_product_processes_code;                              // 工程コード
    private $param_product_processes_detail_no;                              // 工程行NO
    private $param_device_code;                              // 機器コード
    private $param_department_code;                              // 部署コード
    private $param_users_code;                              // 社員コード
    private $param_process_history_no;                              // 加工履歴No
    private $param_work_kind;                              // 作業種別
    private $param_process_time_m;                              // 加工連番合計（分）
    private $param_process_time_h;                              // 加工連番合計（時）
    private $param_setup_history_no;                              // 段取り履歴No
    private $param_setup_time_m;                              // 段取り連番合計（分）
    private $param_setup_time_h;                              // 段取り連番合計（時）
    private $param_complete_date;                              // 完了日
    private $param_qr_code;                              // QRコード
    private $param_created_user;                              // 作成ユーザー
    private $param_updated_user;                              // 修正ユーザー
    private $param_created_at;                              // 作成日時
    private $param_updated_at;                              // 修正日時
    private $param_is_deleted;                              // 削除フラグ

    //--------------- パラメータアクセサ -----------------------------------
    //ID
    public function getParamIdAttribute()
    {
        return $this->param_id;
    }

    public function setParamIdAttribute($value)
    {
        $this->param_id = $value;
    }
    //受注番号
    public function getParamOrdernoAttribute()
    {
        return $this->param_order_no;
    }

    public function setParamOrdernoAttribute($value)
    {
        $this->param_order_no = $value;
    }
    //連番
    public function getParamSeqAttribute()
    {
        return $this->param_seq;
    }

    public function setParamSeqAttribute($value)
    {
        $this->param_seq = $value;
    }
    //工程NO
    public function getParamProgressnoAttribute()
    {
        return $this->param_progress_no;
    }

    public function setParamProgressnoAttribute($value)
    {
        $this->param_progress_no = $value;
    }
    //工程コード
    public function getParamProductprocessescodeAttribute()
    {
        return $this->param_product_processes_code;
    }

    public function setParamProductprocessescodeAttribute($value)
    {
        $this->param_product_processes_code = $value;
    }
    //工程行NO
    public function getParamProductprocessesdetailnoAttribute()
    {
        return $this->param_product_processes_detail_no;
    }

    public function setParamProductprocessesdetailnoAttribute($value)
    {
        $this->param_product_processes_detail_no = $value;
    }
    //機器コード
    public function getParamDevicecodeAttribute()
    {
        return $this->param_device_code;
    }

    public function setParamDevicecodeAttribute($value)
    {
        $this->param_device_code = $value;
    }
    //部署コード
    public function getParamDepartmentcodeAttribute()
    {
        return $this->param_department_code;
    }

    public function setParamDepartmentcodeAttribute($value)
    {
        $this->param_department_code = $value;
    }
    //社員コード
    public function getParamUserscodeAttribute()
    {
        return $this->param_users_code;
    }

    public function setParamUserscodeAttribute($value)
    {
        $this->param_users_code = $value;
    }
    //加工履歴No
    public function getParamProcesshistorynoAttribute()
    {
        return $this->param_process_history_no;
    }

    public function setParamProcesshistorynoAttribute($value)
    {
        $this->param_process_history_no = $value;
    }
    //作業種別
    public function getParamWorkkindAttribute()
    {
        return $this->param_work_kind;
    }

    public function setParamWorkkindAttribute($value)
    {
        $this->param_work_kind = $value;
    }
    //加工連番合計（分）
    public function getParamProcesstimemAttribute()
    {
        return $this->param_process_time_m;
    }

    public function setParamProcesstimemAttribute($value)
    {
        $this->param_process_time_m = $value;
    }
    //加工連番合計（時）
    public function getParamProcesstimehAttribute()
    {
        return $this->param_process_time_h;
    }

    public function setParamProcesstimehAttribute($value)
    {
        $this->param_process_time_h = $value;
    }
    //段取り履歴No
    public function getParamSetuphistorynoAttribute()
    {
        return $this->param_setup_history_no;
    }

    public function setParamSetuphistorynoAttribute($value)
    {
        $this->param_setup_history_no = $value;
    }
    //段取り連番合計（分）
    public function getParamSetuptimemAttribute()
    {
        return $this->param_setup_time_m;
    }

    public function setParamSetuptimemAttribute($value)
    {
        $this->param_setup_time_m = $value;
    }
    //段取り連番合計（時）
    public function getParamSetuptimehAttribute()
    {
        return $this->param_setup_time_h;
    }

    public function setParamSetuptimehAttribute($value)
    {
        $this->param_setup_time_h = $value;
    }
    //完了日
    public function getParamCompletedateAttribute()
    {
        return $this->param_complete_date;
    }

    public function setParamCompletedateAttribute($value)
    {
        $this->param_complete_date = $value;
    }
    //QRコード
    public function getParamQrcodeAttribute()
    {
        return $this->param_qr_code;
    }

    public function setParamQrcodeAttribute($value)
    {
        $this->param_qr_code = $value;
    }
    //作成ユーザー
    public function getParamCreateduserAttribute()
    {
        return $this->param_created_user;
    }

    public function setParamCreateduserAttribute($value)
    {
        $this->param_created_user = $value;
    }
    //修正ユーザー
    public function getParamUpdateduserAttribute()
    {
        return $this->param_updated_user;
    }

    public function setParamUpdateduserAttribute($value)
    {
        $this->param_updated_user = $value;
    }
    //作成日時
    public function getParamCreatedatAttribute()
    {
        return $this->param_created_at;
    }

    public function setParamCreatedatAttribute($value)
    {
        $this->param_created_at = $value;
    }
    //修正日時
    public function getParamUpdatedatAttribute()
    {
        return $this->param_updated_at;
    }

    public function setParamUpdatedatAttribute($value)
    {
        $this->param_updated_at = $value;
    }
    //削除フラグ
    public function getParamIsdeletedAttribute()
    {
        return $this->param_is_deleted;
    }

    public function setParamIsdeletedAttribute($value)
    {
        $this->param_is_deleted = $value;
    }


    // ---------------------- メソッド ------------------------------
    /** 加工指示書／工程管理取得
     *
     * @return list customer
     */
    public function getProductdetaile(){
        $this->array_messagedata = array();
        $details = new Collection();
        $result = true;
        try {

            // ログインユーザの権限取得
            $user = Auth::user();
            $login_user_code = $user->code;
            $login_account_id = $user->account_id;
            $sqlString = "";
            $sqlString .= "select" ;
            $sqlString .= "  t1.order_no as order_no" ;
            $sqlString .= "  , t1.seq as seq" ;
            $sqlString .= "  , t1.progress_no as progress_no" ;
            $sqlString .= "  , t1.product_processes_code as product_processes_code" ;
            $sqlString .= "  , t1.product_processes_detail_no as product_processes_detail_no" ;
            $sqlString .= "  , t1.device_code as device_code" ;
            $sqlString .= "  , t1.department_code as department_code" ;
            $sqlString .= "  , t1.users_code as users_code" ;
            $sqlString .= "  , t1.process_history_no as process_history_no" ;
            $sqlString .= "  , t1.work_kind as work_kind" ;
            $sqlString .= "  , t1.process_time_m as process_time_m" ;
            $sqlString .= "  , t1.process_time_h as process_time_h" ;
            $sqlString .= "  , t1.setup_history_no as setup_history_no" ;
            $sqlString .= "  , t1.setup_time_m as setup_time_m" ;
            $sqlString .= "  , t1.setup_time_h as setup_time_h" ;
            $sqlString .= "  , t1.complete_date as complete_date" ;
            $sqlString .= "  , t1.qr_code as qr_code" ;
            $sqlString .= "  , t1.created_user as created_user" ;
            $sqlString .= "  , t1.updated_user as updated_user" ;
            $sqlString .= "  , t1.created_at as created_at" ;
            $sqlString .= "  , t1.updated_at as updated_at" ;
            $sqlString .= "  from" ;
            $sqlString .= "  ".$this->table." as t1" ;
            $sqlString .= "  where" ;
            $sqlString .= "    ? = ?" ;
            if (!empty($this->param_order_no)) {
                $sqlString .= "    and t1.order_no = ?" ;
            }
            if (!empty($this->param_seq)) {
                $sqlString .= "    and t1.seq = ?" ;
            }
            if (!empty($this->param_device_code)) {
                $sqlString .= "    and t1.device_code = ?" ;
            }
            if (!empty($this->param_users_code)) {
                $sqlString .= "    and t1.users_code = ?" ;
            }
            // バインド
            $array_setBindingsStr = array();
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            if (!empty($this->param_order_no)) {
                $array_setBindingsStr[] = $this->param_order_no;
            }
            if (!empty($this->param_seq)) {
                $array_setBindingsStr[] = $this->param_seq;
            }
            if (!empty($this->param_device_code)) {
                $array_setBindingsStr[] = $this->param_device_code;
            }
            if (!empty($this->param_users_code)) {
                $array_setBindingsStr[] = $this->param_users_code;
            }
            $details = DB::select($sqlString, $array_setBindingsStr);
            return $details;
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_product_processes, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_product_processes, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /** 
     *  登録
     */
    public function insert(){
        try {
            DB::table($this->table)->insert(
                [
                    'order_no' => $this->order_no,
                    'seq' => $this->seq,
                    'progress_no' => $this->progress_no,
                    'product_processes_code' => $this->product_processes_code,
                    'product_processes_detail_no' => $this->product_processes_detail_no,
                    'device_code' => $this->device_code,
                    'department_code' => $this->department_code,
                    'users_code' => $this->users_code,
                    'process_history_no' => $this->process_history_no,
                    'work_kind' => $this->work_kind,
                    'process_time_m' => $this->process_time_m,
                    'process_time_h' => $this->process_time_h,
                    'setup_history_no' => $this->setup_history_no,
                    'setup_time_m' => $this->setup_time_m,
                    'setup_time_h' => $this->setup_time_h,
                    'complete_date' => $this->complete_date,
                    'qr_code' => $this->qr_code,
                    'created_user'=>$this->created_user,
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

    /**
     * 編集
     *
     * @return void
     */
    public function updateDetail(){
        try {
            DB::table($this->table)
            ->where('order_no', $this->param_order_no)
            ->where('seq', $this->param_seq)
            ->where('device_code', $this->param_device_code)
            ->where('users_code', $this->param_users_code)
            ->update([
                'progress_no' => $this->apply_term_from,
                'product_processes_code' => $this->product_processes_code,
                'product_processes_detail_no' => $this->product_processes_detail_no,
                'department_code' => $this->department_code,
                'process_history_no' => $this->process_history_no,
                'work_kind' => $this->work_kind,
                'process_time_m' => $this->process_time_m,
                'process_time_h' => $this->process_time_h,
                'setup_history_no' => $this->setup_history_no,
                'setup_time_m' => $this->setup_time_m,
                'setup_time_h' => $this->setup_time_h,
                'complete_date' => $this->complete_date,
                'qr_code' => $this->qr_code,
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

}
