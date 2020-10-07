<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class ProcessLock extends Model
{
/**
 * テーブル：排他制御（ProcessLock）のモデル
 *      アクセサ定義
 * @author      o.shindo
 * @version     1.00    20201001
*/
class Setting extends Model
{
    protected $table = 'process_locks';
    protected $guarded = array('id');

    //--------------- 項目属性 -----------------------------------
    private $account_id;                    // アカウントID
    private $process_id;                    // プロセスID
    private $process_lock_user;             // 実行ロックユーザーコード
    private $process_lock_status;           // 実行ステータス
    private $created_user;                  // 作成ユーザー
    private $updated_user;                  // 修正ユーザー
    private $created_at;                    // 作成日次
    private $updated_at;                    // 更新日時
    private $is_deleted;                    // 削除フラグ

    // アカウントID
    public function getAccountidAttribute()
    {
        return $this->account_id;
    }

    public function setAccountidAttribute($value)
    {
        $this->account_id = $value;
    }

    // プロセスID
    public function getProcessidAttribute()
    {
        return $this->process_id;
    }

    public function setProcessidAttribute($value)
    {
        $this->process_id = $value;
    }

    // 実行ロックユーザーコード
    public function getProcesslockuserAttribute()
    {
        return $this->process_lock_user;
    }

    public function setProcesslockuserAttribute($value)
    {
        $this->process_lock_user = $value;
    }

    // 実行ステータス
    public function getProcesslockstatusAttribute()
    {
        return $this->process_lock_status;
    }

    public function setProcesslockstatusAttribute($value)
    {
        $this->process_lock_status = $value;
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


    // 削除フラグ
    public function getIsdeletedAttribute()
    {
        return $this->is_deleted;
    }

    public function setIsdeletedAttribute($value)
    {
        $this->is_deleted = $value;
    }
     
    // 作成時刻
    public function getCreatedatAttribute()
    {
        return $this->created_at;
    }

    public function setCreatedatAttribute($value)
    {
        $this->created_at = $value;
    }

    // 修正時刻
    public function getUpdatedatAttribute()
    {
        return $this->updated_at;
    }

    public function setUpdatedatAttribute($value)
    {
        $this->updated_at = $value;
    }

    //--------------- パラメータ項目属性 -----------------------------------

    private $param_account_id;                  // アカウントID
    private $param_process_id;                  // プロセスID
    private $param_process_lock_user;           // 実行ロックユーザーコード

    private $massegedata;                       // メッセージ

    // アカウントID
    public function getParamAccountidAttribute()
    {
        return $this->param_account_id;
    }

    public function setParamAccountidAttribute($value)
    {
        $this->param_account_id = $value;
    }

    // プロセスID
    public function getParamProcessidAttribute()
    {
        return $this->param_process_id;
    }

    public function setParamProcessidAttribute($value)
    {
        $this->param_process_id = $value;
    }

    // 実行ロックユーザーコード
    public function getParamProcesslockuserAttribute()
    {
        return $this->param_process_lock_user;
    }

    public function setParamProcesslockuserAttribute($value)
    {
        $this->param_process_lock_user = $value;
    }

    // メッセージ
    public function getMassegedataAttribute()
    {
        return $this->massegedata;
    }

    public function setMassegedataAttribute($value)
    {
        $this->massegedata = $value;
    }


    // --------------------- メソッド ------------------------------------------------------

    /**
     * 排他制御取得
     *
     *
     * @return sql取得結果
     */
    public function getProcessLockDatas(){

        try {
            // 取得SQL作成
            $mainquery = DB::table($this->table)
                ->select(
                    $this->table.'.account_id',
                    $this->table.'.process_id',
                    $this->table.'.process_lock_user',
                    $this->table.'.process_lock_status'
                );

            $mainquery->where($this->table.'.account_id', $this->param_account_id);                         // アカウントID
            if(!empty($this->param_process_id)){
                $mainquery->where($this->table.'.process_id', $this->param_process_id);                     // プロセスID
            }
            
            if(!empty($this->param_process_lock_user)){
                $mainquery->where($this->table.'.process_lock_user', $this->param_process_lock_user);       // 実行ロックユーザーコード
            }
            
            $data = $mainquery
                ->where($this->table.'.is_deleted', '=', 0)
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

        return $data;
    }

    /**
     * 排他制御登録
     *
     *
     * @return sql取得結果
     */
    public function insProcessLockDatas(){

        try {
            DB::table($this->table)->insert(
                [
                    'account_id' => $this->account_id,
                    'process_id' => $this->process_id,
                    'process_lock_user' => $this->process_lock_user,
                    'process_lock_status' => $this->process_lock_status,
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

    /**
     * 排他制御更新
     *
     *
     * @return sql取得結果
     */
    public function updProcessLockDatas(){

        try {
            $mainExecute = DB::table($this->table)
            ->where('account_id', $this->param_account_id);
            if(!empty($this->param_process_id)){
                $mainExecute->where($this->table.'.process_id', $this->param_process_id);                       // プロセスID
            }
            
            if(!empty($this->param_process_lock_user)){
                $mainExecute->where($this->table.'.process_lock_user', $this->param_process_lock_user);         // 実行ロックユーザーコード
            }

            $mainExecute->update([
                'process_id' => $this->process_id,
                'process_lock_user' => $this->process_lock_user,
                'process_lock_status' => $this->process_lock_status,
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
     * 排他制御論理削除
     *
     *
     * @return sql取得結果
     */
    public function delProcessLockDatas(){

        try {
            $mainExecute = DB::table($this->table)
            ->where('account_id', $this->param_account_id);
            if(!empty($this->param_process_id)){
                $mainExecute->where($this->table.'.process_id', $this->param_process_id);                       // プロセスID
            }
            
            if(!empty($this->param_process_lock_user)){
                $mainExecute->where($this->table.'.process_lock_user', $this->param_process_lock_user);         // 実行ロックユーザーコード
            }

            $mainExecute->update([
                'is_deleted' => 1,
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
