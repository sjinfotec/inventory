<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\ApiCommonController;

class CardInformation extends Model
{
    protected $table = 'card_informations';
    protected $table_users = 'users';
    protected $guarded = array('id');

    private $account_id;                        // ログインユーザーのアカウント
    private $user_code;
    private $department_code;
    private $card_idm;
    private $created_user;
    private $updated_user;
    private $systemdate;

    // ログインユーザーのアカウント
    public function getAccountidAttribute()
    {
        return $this->account_id;
    }

    public function setAccountidAttribute($value)
    {
        $this->account_id = $value;
    }

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

    // ------------- implements --------------

    private $param_id;                          // id
    private $param_account_id;                  // ログインユーザーのアカウント
    private $param_department_code;             // 部署コード
    private $param_user_code;                   // ユーザー

    // id
    public function getParamidAttribute()
    {
        return $this->param_id;
    }

    public function setParamidAttribute($value)
    {
        $this->param_id = $value;
    }

    // ログインユーザーのアカウント
    public function getParamAccountidAttribute()
    {
        return $this->param_account_id;
    }

    public function setParamAccountidAttribute($value)
    {
        $this->param_account_id = $value;
    }

    // 部署コード
    public function getParamdepartmentcodeAttribute()
    {
        return $this->param_department_code;
    }

    public function setParamdepartmentcodeAttribute($value)
    {
        $this->param_department_code = $value;
    }

    // ユーザー
    public function getParamusercodeAttribute()
    {
        return $this->param_user_code;
    }

    public function setParamusercodeAttribute($value)
    {
        $this->param_user_code = $value;
    }


    /**
     * カード情報を持たないユーザーを取得
     *
     * @param [type] $card_idm
     * @return boolean
     */
    public function isCardInfoExists(){
        try {
            // usersの最大適用開始日付subquery
            // 適用期間日付の取得
            $apicommon = new ApiCommonController();
            $subquery2 = $apicommon->getUserApplyTermSubquery(null, $this->param_account_id);
            $mainquery = DB::table($this->table)
                ->join($this->table_users, function ($join) {
                    $join->on($this->table_users.'.code', '=', $this->table.'.user_code');
                    $join->on($this->table_users.'.department_code', '=', $this->table.'.department_code');
                });
            $mainquery
                ->JoinSub($subquery2, 't1', function ($join) { 
                    $join->on('t1.code', '=', $this->table_users.'.code');
                    $join->on('t1.max_apply_term_from', '=', $this->table_users.'.apply_term_from');
                });
            $mainquery
                ->where($this->table.'.account_id',$this->param_account_id)
                ->where($this->table.'.card_idm',$this->card_idm)
                ->where($this->table_users.'.is_deleted',0)
                ->where($this->table.'.is_deleted',0);
            $data = $mainquery->exists();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_exists_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_exists_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

        return $data;
    }

    /**
     * 存在チェック
     *
     * @return boolean
     */
    public function isCardExists(){
        try {
            $mainquery = DB::table($this->table);
            if(!empty($this->param_department_code)) {
                $mainquery
                    ->where($this->table.'.department_code',$this->param_department_code);
            }
            if(!empty($this->param_user_code)) {
                $mainquery
                    ->where($this->table.'.user_code',$this->param_user_code);
            }
            $is_exists = $mainquery
                ->where($this->table.'.is_deleted', 0)
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

    public function insertCardInfo(){
        try {
            Log::debug('insertCardInfo $this->account_id = '.$this->account_id );
            Log::debug('insertCardInfo $this->user_code = '.$this->user_code );
            Log::debug('insertCardInfo $this->department_code = '.$this->department_code );
            Log::debug('insertCardInfo $this->card_idm = '.$this->card_idm );
            Log::debug('insertCardInfo $this->created_user = '.$this->created_user );
            Log::debug('insertCardInfo $this->systemdate = '.$this->systemdate );
            DB::table($this->table.'')->insert(
                [
                    'account_id' => $this->account_id,
                    'user_code' => $this->user_code,
                    'department_code' => $this->department_code,
                    'card_idm' => $this->card_idm,
                    'created_user'=>$this->created_user,
                    'created_at'=>$this->systemdate
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
     * 更新（共通）
     *
     * @return boolean
     */
    public function updateCardCommon($array_update){
        try {
            $mainquery = DB::table($this->table);
            $mainquery->where($this->table.'.account_id',$this->param_account_id);
            if(!empty($this->param_department_code)) {
                $mainquery
                    ->where($this->table.'.department_code',$this->param_department_code);
            }
            if(!empty($this->param_user_code)) {
                $mainquery
                    ->where($this->table.'.user_code',$this->param_user_code);
            }
            $result =$mainquery
                ->where('is_deleted',0)
                ->update($array_update);
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_update_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_update_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

        return $result;
    }

}
