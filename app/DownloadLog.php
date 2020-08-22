<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class DownloadLog extends Model
{
    //
    protected $table = 'download_logs';

    private $account_id;                        // アカウントID
    private $downloadfile_no;                   // ダウンロードファイル番号
    private $downloadfile_date;                 // 最新ダウンロード日付
    private $downloadfile_time;                 // 最新ダウンロード時刻
    private $downloadfile_name;                 // ファイル名
    private $downloadfile_cnt;                  // ダウンロード回数
    private $downloadfile_account_id;           // 最新ダウンロードアカウントID
    private $created_user;                      // 作成ユーザー
    private $updated_user;                      // 修正ユーザー
    private $created_at;                        // 作成日時
    private $updated_at;                        // 修正日時

    // アカウントID
    public function getAccountidAttribute()
    {
        return $this->account_id;
    }

    public function setAccountidAttribute($value)
    {
        $this->account_id = $value;
    }

    // ダウンロードファイル番号
    public function getDownloadfilenoAttribute()
    {
        return $this->downloadfile_no;
    }

    public function setDownloadfilenoAttribute($value)
    {
        $this->downloadfile_no = $value;
    }

    // 最新ダウンロード日付
    public function getDownloadfiledateAttribute()
    {
        return $this->downloadfile_date;
    }

    public function setDownloadfiledateAttribute($value)
    {
        $this->downloadfile_date = $value;
    }

    // 最新ダウンロード時刻
    public function getDownloadfiletimeAttribute()
    {
        return $this->downloadfile_time;
    }

    public function setDownloadfiletimeAttribute($value)
    {
        $this->downloadfile_time = $value;
    }

    // ファイル名
    public function getDownloadfilenameAttribute()
    {
        return $this->downloadfile_name;
    }

    public function setDownloadfilenameAttribute($value)
    {
        $this->downloadfile_name = $value;
    }

    // ダウンロード回数
    public function getDownloadfilecntAttribute()
    {
        return $this->downloadfile_cnt;
    }

    public function setDownloadfilecntAttribute($value)
    {
        $this->downloadfile_cnt = $value;
    }

    // 最新ダウンロードアカウントID
    public function getDownloadfileaccountidAttribute()
    {
        return $this->downloadfile_account_id;
    }

    public function setDownloadfileaccountidAttribute($value)
    {
        $this->downloadfile_account_id = $value;
    }
     
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


    // 作成日時 
    public function getCreatedatAttribute()
    {
        return $this->created_at;
    }

    public function setCreatedatAttribute($value)
    {
        $this->created_at = $value;
    }


    // 修正日時 
    public function getUpdatedatAttribute()
    {
        return $this->updated_at;
    }

    public function setUpdatedatAttribute($value)
    {
        $this->updated_at = $value;
    }

    
    private $param_account_id;              // アカウントID
    private $param_downloadfile_no;         // ダウンロードファイル番号
    private $param_downloadfile_date;       // 最新ダウンロード日付
    private $param_downloadfile_time;       // 最新ダウンロード時刻
    private $param_downloadfile_cnt;        // ダウンロード回数
    private $param_downloadfile_name;       // ファイル名
    
    // アカウントID
    public function getParamAccountidAttribute()
    {
        return $this->param_account_id;
    }

    public function setParamAccountidAttribute($value)
    {
        $this->param_account_id = $value;
    }
    
    // ダウンロードファイル番号
    public function getParamDownlodfilenoAttribute()
    {
        return $this->param_downloadfile_no;
    }

    public function setParamDownlodfilenoAttribute($value)
    {
        $this->param_downloadfile_no = $value;
    }
    
    // 最新ダウンロード日付
    public function getParamDownlodfiledateAttribute()
    {
        return $this->param_downloadfile_date;
    }

    public function setParamDownlodfiledateAttribute($value)
    {
        $this->param_downloadfile_date = $value;
    }
    
    // 最新ダウンロード時刻
    public function getParamDownlodfiletimeAttribute()
    {
        return $this->param_downloadfile_time;
    }

    public function setParamDownlodfiletimeAttribute($value)
    {
        $this->param_downloadfile_time = $value;
    }
    
    // ダウンロード回数
    public function getParamDownlodfilecntAttribute()
    {
        return $this->param_downloadfile_cnt;
    }

    public function setParamDownlodfilecntAttribute($value)
    {
        $this->param_downloadfile_cnt = $value;
    }
    
    // ファイル名
    public function getParamDownlodfilenameAttribute()
    {
        return $this->param_downloadfile_name;
    }

    public function setParamDownlodfilenameAttribute($value)
    {
        $this->param_downloadfile_name = $value;
    }

    /**
     * ダウンロード履歴登録
     *
     * @return void
     */
    public function insertDownloadlog(){
        try {
            DB::table($this->table)->insert(
                [
                    'account_id' => $this->account_id,
                    'downloadfile_no' => $this->downloadfile_no,
                    'downloadfile_date' => $this->downloadfile_date,
                    'downloadfile_time' => $this->downloadfile_time,
                    'downloadfile_name' => $this->downloadfile_name,
                    'downloadfile_cnt' => $this->downloadfile_cnt,
                    'downloadfile_account_id' => $this->downloadfile_account_id,
                    'created_user' => $this->created_user,
                    'created_at' => $this->created_at
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
     * ダウンロード履歴更新
     *
     * @return void
     */
    public function updDownloadlog($array_update){
        try {
            $mainquery = DB::table($this->table);
            if (!empty($this->param_account_id)) {
                $mainquery->where('account_id',$this->param_account_id);
            }
            if ($this->param_downloadfile_no != null && $this->param_downloadfile_no != "" && $this->param_downloadfile_no > 0) {
                $mainquery->where('downloadfile_no',$this->param_downloadfile_no);
            }
            if (!empty($this->param_downloadfile_date)) {
                $mainquery->where('downloadfile_date',$this->param_downloadfile_date);
            }
            if (!empty($this->param_downloadfile_time)) {
                $mainquery->where('downloadfile_time',$this->param_downloadfile_time);
            }
            if (!empty($this->param_downloadfile_name)) {
                $mainquery->where('downloadfile_name',$this->param_downloadfile_name);
            }
            $result =$mainquery
                ->where('is_deleted',0)
                ->update($array_update);
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

    /**
     * ダウンロード履歴削除
     *
     * @return void
     */
    public function delDownloadlog(){
        try {
            $mainquery = DB::table($this->table);
            if (!empty($this->param_account_id)) {
                $mainquery->where('account_id',$this->param_account_id);
            }
            if ($this->param_downloadfile_no != null && $this->param_downloadfile_no != "" && $this->param_downloadfile_no > 0) {
                $mainquery->where('downloadfile_no',$this->param_downloadfile_no);
            }
            if (!empty($this->param_downloadfile_date)) {
                $mainquery->where('downloadfile_date',$this->param_downloadfile_date);
            }
            if (!empty($this->param_downloadfile_time)) {
                $mainquery->where('downloadfile_time',$this->param_downloadfile_time);
            }
            if (!empty($this->param_downloadfile_name)) {
                $mainquery->where('downloadfile_name',$this->param_downloadfile_name);
            }
            $mainquery->update([
                'is_deleted' => 1,
                'updated_user' => $this->updated_user,
                'updated_at' => $this->systemdate
            ]);
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

    /**
     * ダウンロード履歴取得
     *
     * @return void
     */
    public function getDownloadlog(){
        try {
            $data = DB::table($this->table)
            ->select(
                    'account_id',
                    'downloadfile_no',
                    'downloadfile_date',
                    'downloadfile_time',
                    'downloadfile_name',
                    'downloadfile_cnt',
                    'downloadfile_account_id'
            );
            if (!empty($this->param_account_id)) {
                $data->where('account_id',$this->param_account_id);
            }
            if ($this->param_downloadfile_no != null && $this->param_downloadfile_no != "" && $this->param_downloadfile_no > 0) {
                $data->where('downloadfile_no',$this->param_downloadfile_no);
            }
            if (!empty($this->param_downloadfile_date)) {
                $data->where('downloadfile_date',$this->param_downloadfile_date);
            }
            if (!empty($this->param_downloadfile_time)) {
                $data->where('downloadfile_time',$this->param_downloadfile_time);
            }
            if (!empty($this->param_downloadfile_name)) {
                $data->where('downloadfile_name',$this->param_downloadfile_name);
            }
            $result = $data->where('is_deleted',0)
                ->get();
            return $result;
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
     * ダウンロード履歴存在チェック
     *
     * @return boolean
     */
    public function isExistsLogs(){
        try {
            $mainQuery = DB::table($this->table);
            if (!empty($this->param_account_id)) {
                $mainQuery->where('account_id', $this->param_account_id);
            }
            if ($this->param_downloadfile_no != null && $this->param_downloadfile_no != "") {
                $mainQuery->where('downloadfile_no',$this->param_downloadfile_no);
            }
            if (!empty($this->param_downloadfile_date)) {
                $mainQuery->where('downloadfile_date',$this->param_downloadfile_date);
            }
            if (!empty($this->param_downloadfile_time)) {
                $mainQuery->where('downloadfile_time',$this->param_downloadfile_time);
            }
            if (!empty($this->param_downloadfile_name)) {
                $mainQuery->where('downloadfile_name',$this->param_downloadfile_name);
            }
            if ($this->param_downloadfile_cnt != null && $this->param_downloadfile_cnt != "") {
                $mainQuery->where('downloadfile_cnt', ">", $this->param_downloadfile_cnt);
            }
            $is_exists = $mainQuery
                ->where('is_deleted',0)
                ->exists();
            return $is_exists;

        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_exists_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_exists_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

    }
}
