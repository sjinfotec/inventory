<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class CardInformation extends Model
{
    protected $table = 'card_informations';
    protected $table_users = '$table_users';
    protected $guarded = array('id');

    private $user_code;
    private $department_code;
    private $card_idm;
    private $created_user;
    private $updated_user;
    private $systemdate;

    public function getUserCodeAttribute()
    {
        return $this->user_code;
    }

    public function setUserCodeAttribute($value)
    {
        $this->user_code = $value;
    }

    public function getDepartmentcodeAttribute()
    {
        return $this->department_code;
    }

    public function setDepartmentcodeAttribute($value)
    {
        $this->department_code = $value;
    }

    public function getCardIdmAttribute()
    {
        return $this->card_idm;
    }

    public function setCardIdmAttribute($value)
    {
        $this->card_idm = $value;
    }

    public function getCreatedUserAttribute()
    {
        return $this->created_user;
    }

    public function setCreatedUserAttribute($value)
    {
        $this->created_user = $value;
    }

    public function getUpdatedUserAttribute()
    {
        return $this->updated_user;
    }

    public function setUpdatedUserAttribute($value)
    {
        $this->updated_user = $value;
    }

    public function getSystemDateAttribute()
    {
        return $this->systemdate;
    }

    public function setSystemDateAttribute($value)
    {
        $this->systemdate = $value;
    }


    /**
     * カード情報を持たないユーザーを取得
     *
     * @param [type] $card_idm
     * @return boolean
     */
    public function isCardInfoExists(){
        try {
            $data = DB::table($this->table)
                ->join($this->table_users, function ($join) {
                    $join->on($this->table_users.'.code', '=', $this->table.'.user_code');
                    $join->on($this->table_users.'.department_code', '=', $this->table.'.department_code');
                })
                ->where($this->table.'.card_idm',$this->card_idm)
                ->where($this->table_users.'.is_deleted',0)
                ->where($this->table.'.is_deleted',0)
                ->exists();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_exists_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_exists_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

        return $data;
    }

    public function insertCardInfo(){
        try {
            DB::table($this->table.'')->insert(
                [
                    'user_code' => $this->user_code,
                    'department_code' => $this->department_code,
                    'card_idm' => $this->card_idm,
                    'created_user'=>$this->created_user,
                    'created_at'=>$this->systemdate
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
