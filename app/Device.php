<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class Device extends Model
{
    //
    //--------------- テーブル名 -----------------------------------
    protected $table = 'devices';

    //--------------- メンバー属性 -----------------------------------
    private $id;                              // ID
    private $code;                              // 機器コード
    private $name;                              // 機器名
    private $symbol;                              // 略号
    private $floor_pos;                              // 設置位置
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
    //機器コード
    public function getCodeAttribute()
    {
        return $this->code;
    }

    public function setCodeAttribute($value)
    {
        $this->code = $value;
    }
    //機器名
    public function getNameAttribute()
    {
        return $this->name;
    }

    public function setNameAttribute($value)
    {
        $this->name = $value;
    }
    //略号
    public function getSymbolAttribute()
    {
        return $this->symbol;
    }

    public function setSymbolAttribute($value)
    {
        $this->symbol = $value;
    }
    //設置位置
    public function getFloorposAttribute()
    {
        return $this->floor_pos;
    }

    public function setFloorposAttribute($value)
    {
        $this->floor_pos = $value;
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
    private $param_code;                              // 機器コード
    private $param_name;                              // 機器名
    private $param_symbol;                              // 略号
    private $param_floor_pos;                              // 設置位置
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
    //機器コード
    public function getParamCodeAttribute()
    {
        return $this->param_code;
    }

    public function setParamCodeAttribute($value)
    {
        $this->param_code = $value;
    }
    //機器名
    public function getParamNameAttribute()
    {
        return $this->param_name;
    }

    public function setParamNameAttribute($value)
    {
        $this->param_name = $value;
    }
    //略号
    public function getParamSymbolAttribute()
    {
        return $this->param_symbol;
    }

    public function setParamSymbolAttribute($value)
    {
        $this->param_symbol = $value;
    }
    //設置位置
    public function getParamFloorposAttribute()
    {
        return $this->param_floor_pos;
    }

    public function setParamFloorposAttribute($value)
    {
        $this->param_floor_pos = $value;
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

    /**
     * 機器情報取得
     *
     * @return void
     */
    public function getDevice(){
        try {
            $data = DB::table($this->table)
                ->select(
                    $this->table.'.id',
                    $this->table.'.code as code',
                    $this->table.'.name as name',
                    $this->table.'.symbol as symbol',
                    $this->table.'.floor_pos as floor_pos',
                    $this->table.'.is_deleted as is_deleted'
                );
            $result = $data->where('code', $this->param_code)
                ->get();
            return $result;
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
     * 機器情報存在チェック
     *
     * @return void
     */
    public function existsDevice(){
        try {
            // データが存在するか
            $result_exists = DB::table($this->table)
                ->where('code', $this->param_code)
                ->where('is_deleted', 0)
                ->exists();
            return $result_exists;
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
     * 機器情報登録
     *
     * @return void
     */
    public function insertDevice(){
        try {
            DB::table($this->table)->insert(
                [
                    'code' => $this->code,
                    'name' => $this->name,
                    'symbol' => $this->symbol,
                    'floor_pos' => $this->floor_pos,
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
     * 機器情報編集
     *
     * @return void
     */
    public function updateDevice(){
        try {
            DB::table($this->table)
            ->where('code', $this->param_code)
            ->update([
                'code' => $this->code,
                'name' => $this->name,
                'symbol' => $this->symbol,
                'updated_user'=>$this->updated_user,
                'updated_at'=>$this->updated_at
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
     * 機器情報論理削除
     *
     * @return void
     */
    public function deleteDevice(){
        try {
            DB::table($this->table)
            ->where('code', $this->param_code)
            ->update([
                'is_deleted'=>$this->is_deleted,
                'updated_user'=>$this->updated_user,
                'updated_at'=>$this->updated_at
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
