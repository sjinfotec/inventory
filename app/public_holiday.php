<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;



/**
 * テーブル：設定マスタ（public_holidays）のモデル
 *      アクセサ定義
 * @author      o.shindo
 * @version     1.00    20190921
*/
class public_holiday extends Model
{
    protected $table = 'public_holidays';
 
    private $date;                  
    private $weekday_kubun;                  
    private $name;
    private $created_user;    
    private $updated_user;                  
    private $created_at;                  
    private $updated_at;                  
    private $is_deleted;                  
   

    public function getDateAttribute()
    {
        return $this->date;
    }

    public function setDateAttribute($value)
    {
        $this->date = $value;
    }

    public function getWeekdaykubunAttribute()
    {
        return $this->weekday_kubun;
    }

    public function setWeekdaykubunAttribute($value)
    {
        $this->weekday_kubun = $value;
    }

    public function getNameAttribute()
    {
        return $this->name;
    }

    public function setNameAttribute($value)
    {
        $this->name = $value;
    }

    public function getCreateduserAttribute()
    {
        return $this->created_user;
    }

    public function setCreateduserAttribute($value)
    {
        $this->created_user = $value;
    }
  
    public function getUpdateduserAttribute()
    {
        return $this->updated_user;
    }

    public function setUpdateduserAttribute($value)
    {
        $this->updated_user = $value;
    }
     
    public function getCreatedatAttribute()
    {
        return $this->created_at;
    }

    public function setCreatedatAttribute($value)
    {
        $this->created_at = $value;
    }
     
    public function getUpdatedatAttribute()
    {
        return $this->updated_at;
    }

    public function setUpdatedatAttribute($value)
    {
        $this->updated_at = $value;
    }
     
    public function getIsdeletedAttribute()
    {
        return $this->is_deleted;
    }

    public function setIsdeletedAttribute($value)
    {
        $this->is_deleted = $value;
    }

    /**
     * 登録(INSERT)
     *
     * @return void
     */
    public function insert(){
        try {
            DB::table($this->table)->insert(
                [
                    'date' => $this->date,
                    'weekday_kubun' => $this->weekday_kubun,
                    'name' => $this->name,
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
}
