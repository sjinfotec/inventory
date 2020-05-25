<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\ApiCommonController;

class CalendarSettingInformation extends Model
{
    protected $table = 'calendar_setting_informations';
 
    private $yearmonth;
    private $department_code;
    private $employment_status;
    private $user_code;
    private $setting_ptn;
    private $weekday_kubun;                  
    private $business_kubun;       
    private $holiday_kubun;     
    private $created_user;    
    private $updated_user;                  
    private $created_at;                  
    private $updated_at;                  
    private $is_deleted;                  
   
    // 年月
    public function getYearmonthAttribute()
    {
        return $this->yearmonth;
    }

    public function setYearmonthAttribute($value)
    {
        $this->yearmonth = $value;
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
   
    // 雇用形態
    public function getEmploymentstatusAttribute()
    {
        return $this->employment_status;
    }

    public function setEmploymentstatusAttribute($value)
    {
        $this->employment_status = $value;
    }

    // ユーザーコード
    public function getUsercodeAttribute()
    {
        return $this->user_code;
    }

    public function setUsercodeAttribute($value)
    {
        $this->user_code = $value;
    }

    // 設定パターン
    public function getSettingptnAttribute()
    {
        return $this->setting_ptn;
    }

    public function setSettingptnAttribute($value)
    {
        $this->setting_ptn = $value;
    }

    // 曜日区分
    public function getWeekdaykubunAttribute()
    {
        return $this->weekday_kubun;
    }

    public function setWeekdaykubunAttribute($value)
    {
        $this->weekday_kubun = $value;
    }

    // 営業日区分
    public function getBusinesskubunAttribute()
    {
        return $this->business_kubun;
    }

    public function setBusinesskubunAttribute($value)
    {
        if ($value == null) {$value=0;}
        $this->business_kubun = $value;
    }
     
    // 休暇区分
    public function getHolidaykubunAttribute()
    {
        return $this->holiday_kubun;
    }

    public function setHolidaykubunAttribute($value)
    {
        if ($value == null) {$value=0;}
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
     
    // 作成stamp
    public function getCreatedatAttribute()
    {
        return $this->created_at;
    }

    public function setCreatedatAttribute($value)
    {
        $this->created_at = $value;
    }
     
    // 修正stamp
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

    private $paramfromyearmonth;
    private $paramtoyearmonth;
    private $paramdepartmentcode;
    private $paramemploymentstatus;
    private $paramusercode;
    
    // 開始年月
    public function getParamfromyearmonthAttribute()
    {
        return $this->paramfromyearmonth;
    }

    public function setParamfromyearmonthAttribute($value)
    {
        $this->paramfromyearmonth = $value;
    }
     
    // 終了年月
    public function getParamtoyearmonthAttribute()
    {
        return $this->paramtoyearmonth;
    }

    public function setParamtoyearmonthAttribute($value)
    {
        $this->paramtoyearmonth = $value;
    }
     
    // 部署コード
    public function getParamdepartmentcodeAttribute()
    {
        return $this->paramdepartmentcode;
    }

    public function setParamdepartmentcodeAttribute($value)
    {
        $this->paramdepartmentcode = $value;
    }
     
    // 雇用形態
    public function getParamemploymentstatusAttribute()
    {
        return $this->paramemploymentstatus;
    }

    public function setParamemploymentstatusAttribute($value)
    {
        $this->paramemploymentstatus = $value;
    }
     
    // ユーザーコード
    public function getParamusercodeAttribute()
    {
        return $this->paramusercode;
    }

    public function setParamusercodeAttribute($value)
    {
        $this->paramusercode = $value;
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
                    'yearmonth' => $this->yearmonth,
                    'department_code' => $this->department_code,
                    'employment_status' => $this->employment_status,
                    'user_code' => $this->user_code,
                    'setting_ptn' => $this->setting_ptn,
                    'weekday_kubun' => $this->weekday_kubun,
                    'business_kubun' => $this->business_kubun,
                    'holiday_kubun' => $this->holiday_kubun,
                    'created_user' => $this->created_user,
                    'created_at' => $this->created_at,
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
    /**
     * 更新(UPDATE)
     *
     * @return void
     */
    public function update(){
        try {
            $mainquery = DB::table($this->table);
            if(!empty($this->paramfromyearmonth) && !empty($this->paramtoyearmonth)) {
                $mainquery
                    ->whereBetween($this->table.'.yearmonth', [$this->paramfromyearmonth, $this->paramtoyearmonth]);
            } else {
                if(!empty($this->paramfromyearmonth)) {
                    $mainquery
                        ->where($this->table.'.yearmonth',$this->paramfromyearmonth);
                }
            }
            if(!empty($this->paramdepartmentcode)) {
                $mainquery
                    ->where($this->table.'.department_code',$this->paramdepartmentcode);
            }
            if(!empty($this->paramemploymentstatus)) {
                $mainquery
                    ->where($this->table.'.employment_status',$this->paramemploymentstatus);
            }
            if(!empty($this->paramusercode)) {
                $mainquery
                    ->where($this->table.'.user_code',$this->paramusercode);
            }
            $mainquery
                ->where($this->table.'.is_deleted', 0)
                ->update([
                    'setting_ptn' => $this->setting_ptn,
                    'weekday_kubun' => $this->weekday_kubun,
                    'business_kubun' => $this->business_kubun,
                    'holiday_kubun' => $this->holiday_kubun,
                    'updated_user' => $this->updated_user,
                    'updated_at' => $this->updated_at
                    ]
                );
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_update_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_update_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 年月存在チェック
     *
     * @return boolean
     */
    public function isExistsYearmonth(){
        try {
            $mainquery = DB::table($this->table);
            if(!empty($this->paramfromyearmonth)) {
                $mainquery
                    ->where($this->table.'.yearmonth',$this->paramfromyearmonth);
            }
            if(!empty($this->paramdepartmentcode)) {
                $mainquery
                    ->where($this->table.'.department_code',$this->paramdepartmentcode);
            }
            if(!empty($this->paramemploymentstatus)) {
                $mainquery
                    ->where($this->table.'.employment_status',$this->paramemploymentstatus);
            }
            if(!empty($this->paramusercode)) {
                $mainquery
                    ->where($this->table.'.user_code',$this->paramusercode);
            }
            $is_exists = $mainquery
                ->where($this->table.'.is_deleted', 0)
                ->exists();
            return $is_exists;
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_exists_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_exists_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 年月削除（物理削除）
     *
     * @return void
     */
    public function delYearmonth(){
        try {
            $mainquery = DB::table($this->table);
            if(!empty($this->paramfromyearmonth)) {
                $mainquery
                    ->where($this->table.'.yearmonth',$this->paramfromyearmonth);
            }
            if(!empty($this->paramdepartmentcode)) {
                $mainquery
                    ->where($this->table.'.department_code',$this->paramdepartmentcode);
            }
            if(!empty($this->paramemploymentstatus)) {
                $mainquery
                    ->where($this->table.'.employment_status',$this->paramemploymentstatus);
            }
            if(!empty($this->paramusercode)) {
                $mainquery
                    ->where($this->table.'.user_code',$this->paramusercode);
            }
            $mainquery
                ->where($this->table.'.is_deleted', 0)
                ->delete();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_delete_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_delete_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 検索
     *
     * @return void
     */
    public function getCalenderInfo(){
        try {
            $mainquery = DB::table($this->table)
                ->select(
                    $this->table.'.yearmonth',
                    $this->table.'.department_code',
                    $this->table.'.employment_status',
                    $this->table.'.user_code',
                    $this->table.'.setting_ptn',
                    $this->table.'.weekday_kubun',
                    $this->table.'.business_kubun',
                    $this->table.'.holiday_kubun'
                )
            if(!empty($this->paramfromyearmonth) && !empty($this->paramtoyearmonth)) {
                $mainquery
                    ->whereBetween($this->table.'.yearmonth', [$this->paramfromyearmonth, $this->paramtoyearmonth]);
            } else {
                if(!empty($this->paramfromyearmonth)) {
                    $mainquery
                        ->where($this->table.'.yearmonth',$this->paramfromyearmonth);
                }
            }
            if(!empty($this->paramdepartmentcode)) {
                $mainquery
                    ->where($this->table.'.department_code',$this->paramdepartmentcode);
            }
            if(!empty($this->paramemploymentstatus)) {
                $mainquery
                    ->where($this->table.'.employment_status',$this->paramemploymentstatus);
            }
            if(!empty($this->paramusercode)) {
                $mainquery
                    ->where($this->table.'.user_code',$this->paramusercode);
            }
            $mainquery->where($this->table.'.is_deleted',0);
            $result = $mainquery->get();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }


        return $result;
    }
}
