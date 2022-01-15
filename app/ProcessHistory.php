<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ProcessHistory extends Model
{
    //
    //--------------- テーブル名 -----------------------------------
    protected $table = 'process_histories';
    //--------------- メンバー属性 -----------------------------------
    private $id;                              // ID
    private $order_no;                              // 受注番号
    private $seq;                              // 連番
    private $process_seq;                              // 工程順番
    private $process_history_no;                              // 加工履歴No
    private $work_kind;                              // 作業種別
    private $device_code;                              // 機器コード
    private $user_code;                              // 作業者コード
    private $row_seq;                              // 行
    private $progress_no;                              // 工程NO
    private $process_history_time;                              // 加工時刻
    private $process_time_h;                              // 作業時間H
    private $process_time_m;                              // 作業時間M
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
    //工程順番
    public function getProcessseqAttribute()
    {
        return $this->process_seq;
    }

    public function setProcessseqAttribute($value)
    {
        $this->process_seq = $value;
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
    //機器コード
    public function getDevicecodeAttribute()
    {
        return $this->device_code;
    }

    public function setDevicecodeAttribute($value)
    {
        $this->device_code = $value;
    }
    //作業者コード
    public function getUsercodeAttribute()
    {
        return $this->user_code;
    }

    public function setUsercodeAttribute($value)
    {
        $this->user_code = $value;
    }
    //行
    public function getRowseqAttribute()
    {
        return $this->row_seq;
    }

    public function setRowseqAttribute($value)
    {
        $this->row_seq = $value;
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
    //加工時刻
    public function getProcesshistorytimeAttribute()
    {
        return $this->process_history_time;
    }

    public function setProcesshistorytimeAttribute($value)
    {
        $this->process_history_time = $value;
    }
    //作業時間H
    public function getProcessTimeHAttribute()
    {
        return $this->process_time_h;
    }

    public function setProcessTimeHAttribute($value)
    {
        $this->process_time_h = $value;
    }
    //作業時間M
    public function getProcessTimeMAttribute()
    {
        return $this->process_time_m;
    }

    public function setProcessTimeMAttribute($value)
    {
        $this->process_time_m = $value;
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
    private $param_process_seq;                              // 工程順番
    private $param_process_history_no;                              // 加工履歴No
    private $param_work_kind;                              // 作業種別
    private $param_device_code;                              // 機器コード
    private $param_user_code;                              // 作業者コード
    private $param_row_seq;                              // 行
    private $param_progress_no;                              // 工程NO
    private $param_process_history_time;                              // 加工時刻
    private $param_process_time_h;                              // 作業時間H
    private $param_process_time_m;                              // 作業時間M
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
    //工程順番
    public function getParamProcesseqAttribute()
    {
        return $this->param_process_seq;
    }

    public function setParamProcesseqAttribute($value)
    {
        $this->param_process_seq = $value;
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
    //機器コード
    public function getParamDevicecodeAttribute()
    {
        return $this->param_device_code;
    }

    public function setParamDevicecodeAttribute($value)
    {
        $this->param_device_code = $value;
    }
    //作業者コード
    public function getParamUsercodeAttribute()
    {
        return $this->param_user_code;
    }

    public function setParamUsercodeAttribute($value)
    {
        $this->param_user_code = $value;
    }
    //行
    public function getParamRowseqAttribute()
    {
        return $this->param_row_seq;
    }

    public function setParamRowseqAttribute($value)
    {
        $this->param_row_seq = $value;
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
    //加工時刻
    public function getParamProcesshistorytimeAttribute()
    {
        return $this->param_process_history_time;
    }

    public function setParamProcesshistorytimeAttribute($value)
    {
        $this->param_process_history_time = $value;
    }
    //作業時間H
    public function getParamProcessTimeHAttribute()
    {
        return $this->param_process_time_h;
    }

    public function setParamProcessTimeHAttribute($value)
    {
        $this->param_process_time_h = $value;
    }
    //作業時間M
    public function getParamProcessTimeMAttribute()
    {
        return $this->param_process_time_m;
    }

    public function setParamProcessTimeMAttribute($value)
    {
        $this->param_process_time_m = $value;
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
    /** 加工指示書／工程管理取得
     *
     * @return list customer
     */
    public function getProcesshistories(){
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
            $sqlString .= "  , t1.process_seq as process_seq" ;
            $sqlString .= "  , t1.process_history_no as process_history_no" ;
            $sqlString .= "  , t1.work_kind as work_kind" ;
            $sqlString .= "  , t1.device_code as device_code" ;
            $sqlString .= "  , t1.user_code as user_code" ;
            $sqlString .= "  , t1.row_seq as row_seq" ;
            $sqlString .= "  , t1.progress_no as progress_no" ;
            $sqlString .= "  , t1.process_history_time as process_history_time" ;
            $sqlString .= "  , t1.process_time_h as process_time_h" ;
            $sqlString .= "  , t1.process_time_m as process_time_m" ;
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
            if (!empty($this->param_user_code)) {
                $sqlString .= "    and t1.user_code = ?" ;
            }
            if (!empty($this->param_progress_no)) {
                $sqlString .= "    and t1.progress_no = ?" ;
            }
            $sqlString .= "  order by " ;
            $sqlString .= "   t1.order_no " ;
            $sqlString .= "   , t1.seq " ;
            $sqlString .= "   , t1.device_code " ;
            $sqlString .= "   , t1.user_code " ;
            $sqlString .= "   , t1.progress_no " ;
            $sqlString .= "   , t1.process_history_time " ;
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
            if (!empty($this->param_user_code)) {
                $array_setBindingsStr[] = $this->param_user_code;
            }
            if (!empty($this->param_progress_no)) {
                $array_setBindingsStr[] = $this->param_progress_no;
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
                    'process_seq' => $this->process_seq,
                    'process_history_no' => $this->process_history_no,
                    'work_kind' => $this->work_kind,
                    'device_code' => $this->device_code,
                    'user_code' => $this->user_code,
                    'row_seq' => $this->row_seq,
                    'progress_no' => $this->progress_no,
                    'process_history_time' => $this->process_history_time,
                    'process_time_h' => $this->process_time_h,
                    'process_time_m' => $this->process_time_m,
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
        }
    }

    /**
     * 編集（加工時間）
     *
     * @return void
     */
    public function updateProcessTime(){
        // Log::debug('mobile updateProcessTime $param_order_no = '.$this->param_order_no);
        // Log::debug('mobile updateProcessTime $param_seq = '.$this->param_seq);
        // Log::debug('mobile updateProcessTime $param_device_code = '.$this->param_device_code);
        // Log::debug('mobile updateProcessTime $param_user_code = '.$this->param_user_code);
        // Log::debug('mobile updateProcessTime $param_process_seq = '.$this->param_process_seq);
        // Log::debug('mobile updateProcessTime $param_process_history_no = '.$this->param_process_history_no);
        try {
            DB::table($this->table)
            ->where('order_no', $this->param_order_no)
            ->where('seq', $this->param_seq)
            ->where('device_code', $this->param_device_code)
            ->where('user_code', $this->param_user_code)
            ->where('process_seq', $this->param_process_seq)
            ->where('process_history_no', $this->param_process_history_no)
            ->update([
                'process_time_m' => $this->process_time_m,
                'process_time_h' => $this->process_time_h,
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
