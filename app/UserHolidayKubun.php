<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

/**
 * テーブル：個人休暇（user_holiday_kubuns）のモデル
 *      アクセサ定義
 * @author      o.shindo
 * @version     1.00    20190629
*/
class UserHolidayKubun extends Model
{
    protected $table = 'user_holiday_kubuns';
    protected $guarded = array('id');


    //--------------- 項目属性 -----------------------------------

    private $working_date;                  // 日付
    private $department_code;               // 部署コード
    private $user_code;                     // ユーザー
    private $holiday_kubun;                 // 休暇区分
    private $created_user;                  // 作成ユーザー
    private $updated_user;                  // 修正ユーザー
    private $is_deleted;
    private $systemdate;

    // 日付
    public function getWorkingdateAttribute()
    {
        return $this->working_date;
    }

    public function setWorkingdateAttribute($value)
    {
        $this->working_date = $value;
    }


    // 部署コード
    public function getDepartmentcodeAttribute()
    {
        return $this->department_code;
    }

    public function setDepartmentcodeAttribute($value)
    {
        $this->department_code = $value;
    }


    // ユーザー
    public function getUsercodeAttribute()
    {
        return $this->user_code;
    }

    public function setUsercodeAttribute($value)
    {
        $this->user_code = $value;
    }


    // 休暇区分
    public function getHolidaykubunAttribute()
    {
        return $this->holiday_kubun;
    }

    public function setHolidaykubunAttribute($value)
    {
        $this->holiday_kubun = $value;
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


    public function getSystemDateAttribute()
    {
        return $this->systemdate;
    }

    public function setSystemDateAttribute($value)
    {
        $this->systemdate = $value;
    }


    //--------------- パラメータ項目属性 -----------------------------------

    private $param_user_code;                   // ユーザー
    private $param_department_code;             // 部署
    private $param_date_from;                   // 開始日付
    private $param_date_to;                     // 終了日付

    // ユーザー
    public function getParamUsercodeAttribute()
    {
        return $this->param_user_code;
    }

    public function setParamUsercodeAttribute($value)
    {
        $this->param_user_code = $value;
    }

    // 部署
    public function getParamDepartmentcodeAttribute()
    {
        return $this->param_department_code;
    }

    public function setParamDepartmentcodeAttribute($value)
    {
        $this->param_department_code = $value;
    }


    // 開始日付
    public function getParamdatefromAttribute()
    {
        $date = date_create($this->param_date_from);
        return $date->format('Ymd');
    }

    public function setParamdatefromAttribute($value)
    {
        $this->param_date_from = $value;
    }


    // 終了日付
    public function getParamdatetoAttribute()
    {
        $date = date_create($this->param_date_to);
        return $date->format('Ymd');
    }

    public function setParamdatetoAttribute($value)
    {
        $this->param_date_to = $value;
    }

    
    // --------------------- メソッド ------------------------------------------------------

    /**
     * 取得
     *
     * @return list
     */
    public function getDetail(){
        try {
            $mainquery = DB::table($this->table)
            ->where('working_date', $this->param_date_from)
            ->where('user_code', $this->param_user_code);

            if(!empty($this->param_department_code)){
                $mainquery->where('department_code', $this->param_department_code);          // department_code指定
            }
            $datas = $mainquery->where('is_deleted', 0)->get();

            return $datas;
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
     * 登録
     *
     * @return void
     */
    public function insertKbn(){
        try {
            DB::table($this->table)->insert(
                [
                    'working_date' => $this->working_date,
                    'department_code' => $this->department_code,
                    'user_code' => $this->user_code,
                    'holiday_kubun' => $this->holiday_kubun,
                    'created_user' => $this->created_user,
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
     * 論理削除
     *
     * @return void
     */
    public function delKbn(){
        try {
            $mainquery = DB::table($this->table)
            ->where('working_date', $this->param_date_from)
            ->where('user_code', $this->param_user_code);

            if(!empty($this->param_department_code)){
                $mainquery->where('department_code', $this->param_department_code);          // department_code指定
            }
            $mainquery
                ->where('is_deleted', 0)
                ->update([
                    'is_deleted' => 1,
                    'updated_at' => $this->systemdate
                ]);
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
     * 存在チェック
     *
     * @return boolean
     */
    public function isExistsKbn(){
        try {
            $mainquery = DB::table($this->table)
            ->where('working_date', $this->param_date_from)
            ->where('user_code', $this->param_user_code);

            if(!empty($this->param_department_code)){
                $mainquery->where('department_code', $this->param_department_code);          // department_code指定
            }
            $is_exists = $mainquery->exists();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_exists_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_exists_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

        return $is_exists;
    }


}
