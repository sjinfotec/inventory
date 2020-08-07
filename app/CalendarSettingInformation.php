<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\ApiCommonController;
use Carbon\Carbon;


class CalendarSettingInformation extends Model
{
    protected $table = 'calendar_setting_informations';
    protected $table_users = 'users';
    protected $table_public_holidays = 'public_holidays';
    protected $table_generalcodes = 'generalcodes';
    protected $table_working_timetables = 'working_timetables';
 
    private $id;
    private $date;
    private $department_code;
    private $employment_status;
    private $user_code;
    private $weekday_kubun;                  
    private $business_kubun;       
    private $working_timetable_no;     
    private $holiday_kubun;     
    private $created_user;    
    private $updated_user;                  
    private $created_at;                  
    private $updated_at;                  
    private $is_deleted;                  
   
    // ID
    public function getIdAttribute()
    {
        return $this->id;
    }

    public function setIdAttribute($value)
    {
        $this->id = $value;
    }
   
    // 日付
    public function getDateAttribute()
    {
        return $this->date;
    }

    public function setDateAttribute($value)
    {
        $this->date = $value;
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
     
    // タイムテーブルNo
    public function getWorkingtimetablenoAttribute()
    {
        return $this->working_timetable_no;
    }

    public function setWorkingtimetablenoAttribute($value)
    {
        if ($value == null) {$value=0;}
        $this->working_timetable_no = $value;
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

    private $paramfromdate;
    private $paramtodate;
    private $paramdepartmentcode;
    private $paramemploymentstatus;
    private $paramusercode;
    private $parambusinesskubun;       
    private $paramworkingtimetableno;     
    private $paramholidaykubun;     
    private $paramlimit;
    
    // 開始日付
    public function getParamfromdateAttribute()
    {
        return $this->paramfromdate;
    }

    public function setParamfromdateAttribute($value)
    {
        $this->paramfromdate = $value;
    }
     
    // 終了日付
    public function getParamtodateAttribute()
    {
        return $this->paramtodate;
    }

    public function setParamtodateAttribute($value)
    {
        $this->paramtodate = $value;
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

    // 営業日区分
    public function getParambusinesskubunAttribute()
    {
        return $this->parambusinesskubun;
    }

    public function setParambusinesskubunAttribute($value)
    {
        $this->parambusinesskubun = $value;
    }
     
    // 休暇区分
    public function getParamholidaykubunAttribute()
    {
        return $this->paramholidaykubun;
    }

    public function setParamholidaykubunAttribute($value)
    {
        $this->paramholidaykubun = $value;
    }
     
    // タイムテーブルNO
    public function getParamworkingtimetablenoAttribute()
    {
        return $this->paramworkingtimetableno;
    }

    public function setParamworkingtimetablenoAttribute($value)
    {
        $this->paramworkingtimetableno = $value;
    }
     
    // 件数limit
    public function getParamlimitAttribute()
    {
        return $this->paramlimit;
    }

    public function setParamlimitAttribute($value)
    {
        $this->paramlimit = $value;
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
                    'department_code' => $this->department_code,
                    'employment_status' => $this->employment_status,
                    'user_code' => $this->user_code,
                    'weekday_kubun' => $this->weekday_kubun,
                    'business_kubun' => $this->business_kubun,
                    'working_timetable_no' => $this->working_timetable_no,
                    'holiday_kubun' => $this->holiday_kubun,
                    'created_user' => $this->created_user,
                    'created_at' => $this->created_at,
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
     * 登録（サブクエリ）
     *
     * @return void
     */
    public function insCalenderDateYear($array_subquery){
        try{
            DB::table($this->table)->insert($array_subquery);
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
     * 更新(UPDATE)
     *
     * @return void
     */
    public function updateCalendar() {
        try {
            $mainquery = DB::table($this->table);
            if(!empty($this->paramfromdate) && !empty($this->paramtodate)) {
                $mainquery
                    ->whereBetween($this->table.'.date', [$this->paramfromdate, $this->paramtodate]);
            } else {
                if(!empty($this->paramfromdate)) {
                    $mainquery
                        ->where($this->table.'.date',$this->paramfromdate);
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
            $array_update_item = array();
            if(!empty($this->weekday_kubun) || $this->weekday_kubun === 0) {
                $array_update_item['weekday_kubun'] = $this->weekday_kubun;
            }
            if(!empty($this->business_kubun) || $this->business_kubun === 0) {
                $array_update_item['business_kubun'] = $this->business_kubun;
            }
            if(!empty($this->holiday_kubun) || $this->holiday_kubun === 0) {
                $array_update_item['holiday_kubun'] = $this->holiday_kubun;
            }
            if(!empty($this->working_timetable_no) || $this->working_timetable_no === 0) {
                $array_update_item['working_timetable_no'] = $this->working_timetable_no;
            }
            if(count($array_update_item) > 0) {
                $array_update_item['updated_user'] = $this->updated_user;
                $array_update_item['updated_at'] = $this->updated_at;
                $mainquery
                    ->where($this->table.'.is_deleted', 0)
                    ->update($array_update_item);
            }

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
     * 存在チェック（日付固定）
     *
     * @return boolean
     */
    public function isExists(){
        try {
            $mainquery = DB::table($this->table);
            if(!empty($this->paramfromdate)) {
                $mainquery
                    ->where($this->table.'.date',$this->paramfromdate);
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
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_exists_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_exists_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 存在チェック（日付範囲）
     *
     * @return boolean
     */
    public function isExistsDate(){
        try {
            $mainquery = DB::table($this->table);
            if(!empty($this->paramfromdate)) {
                $mainquery
                    ->where($this->table.'.date', '>=', $this->paramfromdate);
            }
            if(!empty($this->paramtodate)) {
                $mainquery
                    ->where($this->table.'.date', '<=', $this->paramtodate);
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
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_exists_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_exists_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 年月削除（物理削除）
     *
     * @return void
     */
    public function delDate(){
        try {
            $mainquery = DB::table($this->table);
            if(!empty($this->paramfromdate) && !empty($this->paramtodate)) {
                $mainquery
                    ->whereBetween($this->table.'.date', [$this->paramfromdate, $this->paramtodate]);
            } else {
                if(!empty($this->paramfromdate)) {
                    $mainquery
                        ->where($this->table.'.date',$this->paramfromdate);
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
                ->delete();
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
     * 検索
     *
     * @return void
     */
    public function getCalenderInfo(){
        try {
            $mainquery = DB::table($this->table)
                ->select(
                    $this->table.'.id',
                    $this->table.'.date',
                    $this->table.'.department_code',
                    $this->table.'.employment_status',
                    $this->table.'.user_code',
                    $this->table.'.weekday_kubun',
                    $this->table.'.business_kubun',
                    $this->table.'.working_timetable_no',
                    $this->table.'.holiday_kubun'
                );
            if(!empty($this->paramfromdate) && !empty($this->paramtodate)) {
                $mainquery
                    ->whereBetween($this->table.'.date', [$this->paramfromdate, $this->paramtodate]);
            } else {
                if(!empty($this->paramfromdate)) {
                    $mainquery
                        ->where($this->table.'.date',$this->paramfromdate);
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
            $result = null;
            if(empty($this->paramlimit)) {
                $result = $mainquery->get();
            } else {
                $result = $mainquery->limit($this->paramlimit)->get();
            }
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
     * 検索
     *
     * @return void
     */
    // public function getHolidayinfo(){
    //     try {
    //         $mainquery = DB::table($this->table)
    //             ->select(
    //                 $this->table.'.holiday_kubun'
    //             );
    //         $mainquery
    //             ->selectRaw('t1.code_name as holiday_name')
    //             ->selectRaw('t1.use_free_item as use_free_item');
    //         $mainquery
    //             ->leftJoin($this->table_generalcodes.' as t1', function ($join) { 
    //                 $join->on('t1.code', '=',  $this->table.'.holiday_kubun')
    //                 ->where('t1.identification_id', '=', Config::get('const.C013.value'))
    //                 ->where('t1.is_deleted', '=', 0);
    //         });
    //         if(!empty($this->paramfromdate) && !empty($this->paramtodate)) {
    //             $mainquery
    //                 ->whereBetween($this->table.'.date', [$this->paramfromdate, $this->paramtodate]);
    //         } else {
    //             if(!empty($this->paramfromdate)) {
    //                 $mainquery
    //                     ->where($this->table.'.date',$this->paramfromdate);
    //             }
    //         }
    //         if(!empty($this->paramdepartmentcode)) {
    //             $mainquery
    //                 ->where($this->table.'.department_code',$this->paramdepartmentcode);
    //         }
    //         if(!empty($this->paramemploymentstatus)) {
    //             $mainquery
    //                 ->where($this->table.'.employment_status',$this->paramemploymentstatus);
    //         }
    //         if(!empty($this->paramusercode)) {
    //             $mainquery
    //                 ->where($this->table.'.user_code',$this->paramusercode);
    //         }
    //         $mainquery->where($this->table.'.is_deleted',0);
    //         $result = null;
    //         if(empty($this->paramlimit)) {
    //             $result = $mainquery->get();
    //         } else {
    //             $result = $mainquery->limit($this->paramlimit)->get();
    //         }
    //         return $result;
    //     }catch(\PDOException $pe){
    //         Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
    //         Log::error($pe->getMessage());
    //         throw $pe;
    //     }catch(\Exception $e){
    //         Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
    //         Log::error($e->getMessage());
    //         throw $e;
    //     }
    // }
    public function getHolidayinfo(){
        try {
            $casesql = "select ";
            $casesql .= "{0} from ".$this->table_generalcodes." ";
            $casesql .= "where ";
            $casesql .= "  ? = ? ";
            $casesql .= "  and {1} = {2} ";
            $casesql .= "  and identification_id = ? ";
            $casesql .= "  and is_deleted = ? ";
            $casesql_holiday_name0 = str_replace('{0}', 'code_name', $casesql);
            $casesql_holiday_name1 = str_replace('{1}', 'code', $casesql_holiday_name0);
            $casesql_holiday_name2 = str_replace('{2}', 't1.holiday_kubun', $casesql_holiday_name1);
            $casesql_use_free_item0 = str_replace('{0}', 'use_free_item', $casesql);
            $casesql_use_free_item1 = str_replace('{1}', 'code', $casesql_use_free_item0);
            $casesql_use_free_item2 = str_replace('{2}', 't1.holiday_kubun', $casesql_use_free_item1);
            
            $sqlString = "";
            $sqlString .= "select ";
            $sqlString .= "  t1.holiday_kubun as holiday_kubun ";
            $sqlString .= "  , case ifnull(t1.holiday_kubun, null) ";
            $sqlString .= "    when null then null ";
            $sqlString .= "    else (";
            $sqlString .= "    ".$casesql_holiday_name2;
            $sqlString .= "    ) end as holiday_name ";
            $sqlString .= "  , case ifnull(t1.holiday_kubun, null) ";
            $sqlString .= "    when null then null ";
            $sqlString .= "    else (";
            $sqlString .= "    ".$casesql_use_free_item2;
            $sqlString .= "    ) end as use_free_item ";
            $sqlString .= "  from ";
            $sqlString .= "  ".$this->table." as t1 ";
            $sqlString .= "  where ";
            $sqlString .= "    ? = ? ";
            if(!empty($this->paramfromdate) && !empty($this->paramtodate)) {
                $sqlString .= "    and t1.date between ? and ? ";
            } else {
                if(!empty($this->paramfromdate)) {
                    $sqlString .= "    and t1.date = ? ";
                }
            }
            if(!empty($this->paramdepartmentcode)) {
                $sqlString .= "    and t1.department_code = ? ";
            }
            if(!empty($this->paramusercode)) {
                $sqlString .= "    and t1.user_code = ? ";
            }
            $sqlString .= "    and t1.is_deleted = ? ";

            // バインド
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = Config::get('const.C013.value');
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = Config::get('const.C013.value');
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            if(!empty($this->paramfromdate) && !empty($this->paramtodate)) {
                $array_setBindingsStr[] = $this->paramfromdate;
                $array_setBindingsStr[] = $this->paramtodate;
            } else {
                if(!empty($this->paramfromdate)) {
                    $array_setBindingsStr[] = $this->paramfromdate;
                }
            }
            if(!empty($this->paramdepartmentcode)) {
                $array_setBindingsStr[] = $this->paramdepartmentcode;
            }
            if(!empty($this->paramusercode)) {
                $array_setBindingsStr[] = $this->paramusercode;
            }
            $array_setBindingsStr[] = 0;

            $result = DB::select($sqlString, $array_setBindingsStr);
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
     * 検索
     *
     * @return void
     */
    public function getDetail(){
        try {
            $mainquery = DB::table($this->table)
                ->select(
                    $this->table.'.date',
                    $this->table.'.department_code',
                    $this->table.'.employment_status',
                    $this->table.'.user_code',
                    $this->table.'.weekday_kubun',
                    $this->table.'.business_kubun',
                    $this->table.'.working_timetable_no',
                    $this->table.'.holiday_kubun');
            $mainquery
                ->selectRaw('t1.name as user_name')
                ->selectRaw('t3.name as department_name')
                ->selectRaw('t5.code_name as employment_name')
                ->selectRaw(
                    "concat(
                        DATE_FORMAT(".$this->table.".date,'%e日'),'(',substring('月火水木金土日',convert(".$this->table.".weekday_kubun+1,char),1),')') as date_name "
                    )
                ->selectRaw(
                    "concat(
                        DATE_FORMAT(".$this->table.".date,'%c月%e日'),'(',substring('月火水木金土日',convert(".$this->table.".weekday_kubun+1,char),1),')') as md_name "
                    )
                ->selectRaw("ifnull(t4.name, '') as public_holidays_name")
                ->selectRaw("ifnull(t6.code_name, '') as business_kubun_name")
                ->selectRaw("ifnull(t6.secound_code_name, '') as business_kubun_secound_name")
                ->selectRaw("ifnull(t7.use_free_item, '') as use_free_item")
                ->selectRaw("ifnull(t8.name, '') as working_timetable_name")
                ->selectRaw("ifnull(t7.code_name, '') as holiday_kubun_name")
                ->selectRaw("ifnull(t7.secound_code_name, '') as holiday_kubun_secound_name");
            // join
            // 適用期間日付の取得
            $apicommon = new ApiCommonController();
            // usersの最大適用開始日付subquery
            $subquery1 = $apicommon->getUserApplyTermSubquery($this->paramtodate);
            // departmentsの最大適用開始日付subquery
            $subquery2 = $apicommon->getDepartmentApplyTermSubquery($this->paramtodate);
            // working_timetablesの最大適用開始日付subquery
            $subquery3 = $apicommon->getTimetableApplyTermSubquery($this->paramtodate);
            $mainquery
                ->leftJoin($this->table_users.' as t1', function ($join) { 
                    $join->on('t1.code', '=', $this->table.'.user_code');
                    $join->on('t1.department_code', '=', $this->table.'.department_code');
                    $join->on('t1.employment_status', '=', $this->table.'.employment_status')
                    ->where('t1.is_deleted',0)
                    ->where($this->table.'.is_deleted',0);
            });
            $mainquery
                ->JoinSub($subquery1, 't2', function ($join) { 
                    $join->on('t2.code', '=', 't1.code');
                    $join->on('t2.max_apply_term_from', '=', 't1.apply_term_from');
            });
            $mainquery
                ->leftJoinSub($subquery2, 't3', function ($join) { 
                    $join->on('t3.code', '=', $this->table.'.department_code')
                    ->where($this->table.'.is_deleted',0);
            });
            // 祝日
            $mainquery
                ->leftJoin($this->table_public_holidays.' as t4', function ($join) { 
                    $join->on('t4.date', '=', $this->table.'.date')
                    ->where('t4.is_deleted',0);
            });
            // 雇用形態
            $mainquery
                ->leftJoin($this->table_generalcodes.' as t5', function ($join) { 
                    $join->on('t5.code', '=',  $this->table.'.employment_status')
                    ->where('t5.identification_id', '=', Config::get('const.C001.value'))
                    ->where('t5.is_deleted', '=', 0);
            });
            // 出勤区分
            $mainquery
                ->leftJoin($this->table_generalcodes.' as t6', function ($join) { 
                    $join->on('t6.code', '=',  $this->table.'.business_kubun')
                    ->where('t6.identification_id', '=', Config::get('const.C007.value'))
                    ->where('t6.is_deleted', '=', 0);
            });
            // 休暇区分 C008→C013
            $mainquery
                ->leftJoin($this->table_generalcodes.' as t7', function ($join) { 
                    $join->on('t7.code', '=',  $this->table.'.holiday_kubun')
                    ->where('t7.identification_id', '=', Config::get('const.C013.value'))
                    ->where('t7.is_deleted', '=', 0);
            });
            // タイムテーブル
            $mainquery
                ->leftJoinSub($subquery3, 't8', function ($join) { 
                    $join->on('t8.no', '=', $this->table.'.working_timetable_no')
                    ->where('t8.working_time_kubun',Config::get('const.C004.out_of_regular_night_working_time'))
                    ->where($this->table.'.is_deleted',0);
            });
    
            if(!empty($this->paramdepartmentcode)) {
                $mainquery
                    ->where($this->table.'.department_code',$this->paramdepartmentcode);
            }
            if(!empty($this->paramemploymentstatus)) {
                $mainquery
                    ->where($this->table.'.employment_status',$this->paramemploymentstatus);
            }
            if(!empty($this->paramusercode)){
                $mainquery->where($this->table.'.user_code','=', $this->paramusercode);
            }
            if(!empty($this->paramfromdate) && !empty($this->paramtodate)) {
                $mainquery
                    ->whereBetween($this->table.'.date', [$this->paramfromdate, $this->paramtodate]);
            } else {
                if(!empty($this->paramfromdate)) {
                    $mainquery
                        ->where($this->table.'.date',$this->paramfromdate);
                }
            }
            if(!empty($this->parambusinesskubun)){
                $mainquery->where($this->table.'.business_kubun',$this->parambusinesskubun);
            }
            if(!empty($this->paramholidaykubun)){
                $mainquery->where($this->table.'.holiday_kubun',$this->paramholidaykubun);
            }
            $mainquery->where($this->table.'.is_deleted',0);
            $mainquery
                ->orderBy($this->table.'.department_code', 'asc')
                ->orderBy($this->table.'.employment_status', 'asc')
                ->orderBy($this->table.'.user_code', 'asc')
                ->orderBy($this->table.'.date', 'asc');
            $result = $mainquery->get();

        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }


        return $result;
    }

    /**
     * 検索
     *
     * @return void
     */
    public function getShiftDetail(){
        try {
            $apicommon = new ApiCommonController();
            // usersの最大適用開始日付subquery
            $subquery1 = $apicommon->makeUserApplyTermSql($this->paramtodate, Config::get('const.C025.admin_user'));
            // departmentsの最大適用開始日付subquery
            $subquery2 = $apicommon->makeDepartmentApplyTermSql($this->paramtodate, $this->paramtodate);
            // working_timetablesの最大適用開始日付subquery
            $subquery3 = $apicommon->makeWorkingTimeTableApplyTermSql($this->paramtodate);

            $sqlString = "";
            $sqlString .= "select ";
            $sqlString .= "  t1.department_code as department_code ";
            $sqlString .= "  , t1.employment_status as employment_status ";
            $sqlString .= "  , t1.code as user_code ";
            $sqlString .= "  , t1.name as user_name ";
            $sqlString .= "  , t3.name as department_name ";
            $sqlString .= "  , t4.code_name as employment_name ";
            $sqlString .= "  , t5.date as date ";
            $sqlString .= "  , case ifnull(t6.date, 0) ";
            $sqlString .= "    when 0 then 0 ";
            $sqlString .= "    else 1 ";
            $sqlString .= "    end as date_null ";
            $sqlString .= "  , substring(  ";
            $sqlString .= "      '月火水木金土日' ";
            $sqlString .= "      , convert(t5.weekday_kubun + 1, char) ";
            $sqlString .= "      , 1 ";
            $sqlString .= "    ) as week_name ";
            $sqlString .= "  , concat(  ";
            $sqlString .= "      DATE_FORMAT(t5.date, '%e日') ";
            $sqlString .= "      , '(' ";
            $sqlString .= "      , substring(  ";
            $sqlString .= "        '月火水木金土日' ";
            $sqlString .= "        , convert(t5.weekday_kubun + 1, char) ";
            $sqlString .= "        , 1 ";
            $sqlString .= "      )  ";
            $sqlString .= "      , ')' ";
            $sqlString .= "    ) as date_name ";
            $sqlString .= "    , concat(  ";
            $sqlString .= "      DATE_FORMAT(t5.date, '%c月%e日') ";
            $sqlString .= "      , '(' ";
            $sqlString .= "      , substring(  ";
            $sqlString .= "        '月火水木金土日' ";
            $sqlString .= "        , convert(t5.weekday_kubun + 1, char) ";
            $sqlString .= "        , 1 ";
            $sqlString .= "      )  ";
            $sqlString .= "      , ')' ";
            $sqlString .= "    ) as md_name ";
            $sqlString .= "  , t5.weekday_kubun as weekday_kubun ";
            $sqlString .= "  , t6.business_kubun as business_kubun ";
            $sqlString .= "  , t6.working_timetable_no as working_timetable_no ";
            $sqlString .= "  , t6.holiday_kubun as holiday_kubun ";
            $sqlString .= "  , ifnull(t7.name, '') as public_holidays_name ";
            $sqlString .= "  , ifnull(t8.code_name, '') as business_kubun_name ";
            $sqlString .= "  , ifnull(t8.secound_code_name, '') as business_kubun_secound_name ";
            $sqlString .= "  , ifnull(t9.use_free_item, '') as use_free_item ";
            $sqlString .= "  , ifnull(t10.name, '') as working_timetable_name ";
            $sqlString .= "  , ifnull(t9.code_name, '') as holiday_kubun_name ";
            $sqlString .= "  , ifnull(t9.secound_code_name, '') as holiday_kubun_secound_name ";
            $sqlString .= "  , case ifnull(t6.business_kubun, 0)  ";
            $sqlString .= "    when 0 then 0 ";
            $sqlString .= "    when ".Config::get('const.C007.legal_holoday')." then 0 ";
            $sqlString .= "    when ".Config::get('const.C007.legal_out_holoday')." then 0 ";
            $sqlString .= "    else "; 
            $sqlString .= "      case ifnull(t6.holiday_kubun, 0) "; 
            $sqlString .= "      when 0 then ";
            $sqlString .= "        case substring(ifnull(t9.use_free_item, '00'), 2, 1)  ";
            $sqlString .= "        when '0' then ";
            $sqlString .= "          case ifnull(t10.from_time, '')  ";
            $sqlString .= "          when '' then 0  ";
            $sqlString .= "          else ";
            $sqlString .= "            case ifnull(t10.to_time, '')  ";
            $sqlString .= "            when '' then 0  ";
            $sqlString .= "            else";
            $sqlString .= "              case t10.from_time <= t10.to_time  ";
            $sqlString .= "              when true then 1  ";
            $sqlString .= "              else 0  ";
            $sqlString .= "              end  ";
            $sqlString .= "            end  ";
            $sqlString .= "          end  ";
            $sqlString .= "        end  ";
            $sqlString .= "      else ";
            $sqlString .= "        case ifnull(t6.holiday_kubun, 0)  ";
            $sqlString .= "        when ".Config::get('const.C013.morning_off')." then ";
            $sqlString .= "          case ifnull(t10.to_time, '')  ";
            $sqlString .= "          when '' then 0  ";
            $sqlString .= "          else ";
            $sqlString .= "            case t10.from_time <= t10.to_time  ";
            $sqlString .= "            when true then 0.5 ";
            $sqlString .= "            else 0  ";
            $sqlString .= "            end  ";
            $sqlString .= "          end  ";
            $sqlString .= "        when ".Config::get('const.C013.afternoon_off')." then ";
            $sqlString .= "          case ifnull(t10.to_time, '')  ";
            $sqlString .= "          when '' then 0  ";
            $sqlString .= "          else ";
            $sqlString .= "            case t10.from_time <= t10.to_time  ";
            $sqlString .= "            when true then 0.5 ";
            $sqlString .= "            else 0  ";
            $sqlString .= "            end  ";
            $sqlString .= "          end  ";
            $sqlString .= "        else 0  ";
            $sqlString .= "        end  ";
            $sqlString .= "      end  ";
            $sqlString .= "    end as regular_day_cnt ";
            $sqlString .= "  , case ifnull(t6.business_kubun, 0)  ";
            $sqlString .= "    when 0 then 0 ";
            $sqlString .= "    when ".Config::get('const.C007.legal_holoday')." then 0 ";
            $sqlString .= "    when ".Config::get('const.C007.legal_out_holoday')." then 0 ";
            $sqlString .= "    else ";
            $sqlString .= "      case ifnull(t6.holiday_kubun, 0)  ";
            $sqlString .= "      when 0 then ";
            $sqlString .= "        case substring(ifnull(t9.use_free_item, '00'), 2, 1)  ";
            $sqlString .= "        when '0' then ";
            $sqlString .= "          case ifnull(t10.from_time, '')  ";
            $sqlString .= "          when '' then 0  ";
            $sqlString .= "          else ";
            $sqlString .= "            case ifnull(t10.to_time, '')  ";
            $sqlString .= "            when '' then 0  ";
            $sqlString .= "            else ";
            $sqlString .= "              case t10.from_time > t10.to_time  ";
            $sqlString .= "              when true then 1  ";
            $sqlString .= "              else 0  ";
            $sqlString .= "              end  ";
            $sqlString .= "            end  ";
            $sqlString .= "          end  ";
            $sqlString .= "        end  ";
            $sqlString .= "      else ";
            $sqlString .= "        case ifnull(t6.holiday_kubun, 0)  ";
            $sqlString .= "        when ".Config::get('const.C013.morning_off')." then ";
            $sqlString .= "          case ifnull(t10.to_time, '')  ";
            $sqlString .= "          when '' then 0  ";
            $sqlString .= "          else ";
            $sqlString .= "            case t10.from_time > t10.to_time  ";
            $sqlString .= "            when true then 0.5 ";
            $sqlString .= "            else 0  ";
            $sqlString .= "            end  ";
            $sqlString .= "          end  ";
            $sqlString .= "        when ".Config::get('const.C013.afternoon_off')." then ";
            $sqlString .= "          case ifnull(t10.to_time, '')  ";
            $sqlString .= "          when '' then 0  ";
            $sqlString .= "          else ";
            $sqlString .= "            case t10.from_time > t10.to_time  ";
            $sqlString .= "            when true then 0.5 ";
            $sqlString .= "            else 0  ";
            $sqlString .= "            end  ";
            $sqlString .= "          end  ";
            $sqlString .= "        else 0  ";
            $sqlString .= "        end  ";
            $sqlString .= "      end  ";
            $sqlString .= "    end as night_day_cnt ";
            $sqlString .= "  , case ifnull(t6.holiday_kubun, 0)  ";
            $sqlString .= "    when ".Config::get('const.C013.paid_holiday')." then 1 ";
            $sqlString .= "    when ".Config::get('const.C013.morning_off')." then 0.5 ";
            $sqlString .= "    when ".Config::get('const.C013.afternoon_off')." then 0.5 ";
            $sqlString .= "    else 0  ";
            $sqlString .= "    end as paid_holiday_cnt ";
            $sqlString .= "  , case ifnull(t11.total_working_times, 0)  ";
            $sqlString .= "    when 0 then 0  ";
            $sqlString .= "    else TRUNCATE (t11.total_working_times, 0)  ";
            $sqlString .= "    end as total_working_times_h ";
            $sqlString .= "  , case ifnull(t11.total_working_times, 0)  ";
            $sqlString .= "    when 0 then 0  ";
            $sqlString .= "    else TRUNCATE (  ";
            $sqlString .= "      (mod(t11.total_working_times * 100, 100) * 60) / 100 ";
            $sqlString .= "    , 0 ";
            $sqlString .= "    )  ";
            $sqlString .= "    end as total_working_times_m ";
            $sqlString .= "  , case ifnull(t11.regular_working_times, 0)  ";
            $sqlString .= "    when 0 then 0  ";
            $sqlString .= "    else TRUNCATE (t11.regular_working_times, 0)  ";
            $sqlString .= "    end as regular_working_times_h ";
            $sqlString .= "  , case ifnull(t11.regular_working_times, 0)  ";
            $sqlString .= "    when 0 then 0  ";
            $sqlString .= "    else TRUNCATE (  ";
            $sqlString .= "      (mod(t11.regular_working_times * 100, 100) * 60) / 100 ";
            $sqlString .= "    , 0 ";
            $sqlString .= "    )  ";
            $sqlString .= "    end as regular_working_times_m  ";
            $sqlString .= "  from ";
            $sqlString .= "    ".$this->table_users." t1 ";
            $sqlString .= "    inner join ( ";
            $sqlString .= "      ".$subquery1." ";
            $sqlString .= "    ) as t2 ";
            $sqlString .= "      on t2.code = t1.code ";
            $sqlString .= "      and t2.max_apply_term_from = t1.apply_term_from ";
            $sqlString .= "    left join (  ";
            $sqlString .= "      ".$subquery2." ";
            $sqlString .= "    ) as t3 ";
            $sqlString .= "      on t3.code = t1.department_code ";
            $sqlString .= "      and t1.is_deleted = ? ";
            $sqlString .= "    left join ".$this->table_generalcodes." as t4 ";
            $sqlString .= "      on t4.code = t1.employment_status ";
            $sqlString .= "      and t4.identification_id = ? ";
            $sqlString .= "      and t4.is_deleted = ? ";
            $sqlString .= "    cross join ";
            $sqlString .= "      ( ";
            $sqlString .= "      select ";
            $sqlString .= "        max(t1.date) as date ";
            $sqlString .= "        , t1.weekday_kubun as weekday_kubun ";
            $sqlString .= "      from ";
            $sqlString .= "       ".$this->table." as t1 ";
            $sqlString .= "      where ";
            $sqlString .= "        ? = ? ";
            if(!empty($this->paramfromdate) && !empty($this->paramtodate)) {
                $sqlString .= "      and t1.date between ? and ? ";
            } else {
                if(!empty($this->paramfromdate)) {
                    $sqlString .= "      and t1.date = ? ";
                }
            }
            $sqlString .= "        and t1.is_deleted = ? ";
            $sqlString .= "      group by ";
            $sqlString .= "        t1.date ";
            $sqlString .= "        , t1.weekday_kubun ";
            $sqlString .= "      ) t5 ";
            $sqlString .= "    left join ".$this->table." as t6 ";
            $sqlString .= "      on t6.date = t5.date ";
            $sqlString .= "      and t6.department_code = t1.department_code ";
            $sqlString .= "      and t6.user_code = t1.code ";
            $sqlString .= "      and t6.is_deleted = ? ";
            $sqlString .= "    left join ".$this->table_public_holidays." as t7 ";
            $sqlString .= "      on t7.date = t6.date ";
            $sqlString .= "      and t7.is_deleted = ? ";
            $sqlString .= "    left join ".$this->table_generalcodes." as t8 ";
            $sqlString .= "      on t8.code = t6.business_kubun ";
            $sqlString .= "      and t8.identification_id = ? ";
            $sqlString .= "      and t8.is_deleted = ? ";
            $sqlString .= "    left join ".$this->table_generalcodes." as t9 ";
            $sqlString .= "      on t9.code = t6.holiday_kubun  ";
            $sqlString .= "      and t9.identification_id = ? ";
            $sqlString .= "      and t9.is_deleted = ? ";
            $sqlString .= "      left join (  ";
            // ------------ min,max使用のため$subquery3使わず start --------------
            $sqlString .= "        select ";
            $sqlString .= "          t1.no as no ";
            $sqlString .= "          , t1.name as name ";
            $sqlString .= "          , t1.ago_time_no as ago_time_no ";
            $sqlString .= "          , t1.working_time_kubun as working_time_kubun  ";
            $sqlString .= "          , min(t1.from_time) as from_time ";
            $sqlString .= "          , min(t1.to_time) as to_time ";
            $sqlString .= "        from ";
            $sqlString .= "          ".$this->table_working_timetables." as t1  ";
            $sqlString .= "          inner join (  ";
            $sqlString .= "            select ";
            $sqlString .= "              no as no ";
            $sqlString .= "              , MAX(apply_term_from) as max_apply_term_from  ";
            $sqlString .= "            from ";
            $sqlString .= "              ".$this->table_working_timetables."  ";
            $sqlString .= "            where ";
            $sqlString .= "              ? = ? ";
            $sqlString .= "              and apply_term_from <= ? ";
            $sqlString .= "              and is_deleted = ? ";
            $sqlString .= "            group by ";
            $sqlString .= "              no ";
            $sqlString .= "          ) as t2  ";
            $sqlString .= "            on t1.no = t2.no ";
            $sqlString .= "            and t1.apply_term_from = t2.max_apply_term_from ";
            $sqlString .= "        where ";
            $sqlString .= "          ? = ? ";
            $sqlString .= "          and t1.from_time is not null ";
            $sqlString .= "          and t1.working_time_kubun = ? ";
            $sqlString .= "         and t1.is_deleted = ? ";
            $sqlString .= "        group by ";
            $sqlString .= "          t1.no ";
            $sqlString .= "          , t1.name ";
            $sqlString .= "          , t1.ago_time_no ";
            $sqlString .= "          , t1.working_time_kubun ";
            // ------------ min,max使用のため$subquery3使わず end --------------
            $sqlString .= "      ) as t10 ";
            $sqlString .= "        on t10.no = t6.working_timetable_no  ";
            $sqlString .= "        and t6.is_deleted = ? ";
            $sqlString .= "    left join working_time_dates as t11 ";
            $sqlString .= "      on t11.working_date = t6.date ";
            $sqlString .= "      and t11.department_code = t1.department_code ";
            $sqlString .= "      and t11.user_code = t1.code ";
            $sqlString .= "      and t11.is_deleted = ? ";
            $sqlString .= "    where ";
            $sqlString .= "      ? = ? ";
            if(!empty($this->paramdepartmentcode)) {
                $sqlString .= "      and t1.department_code = ? ";
            }
            if(!empty($this->paramemploymentstatus)) {
                $sqlString .= "      and t1.employment_status = ? ";
            }
            if(!empty($this->paramusercode)){
                $sqlString .= "      and t1.code = ? ";
            }
            // if(!empty($this->paramfromdate) && !empty($this->paramtodate)) {
            //     $sqlString .= "      and t1.date between ? and ? ";
            // } else {
            //     if(!empty($this->paramfromdate)) {
            //         $sqlString .= "      and t1.date = ? ";
            //     }
            // }
            if(!empty($this->parambusinesskubun)){
                $sqlString .= "      and t6.business_kubun = ? ";
            }
            if(!empty($this->paramholidaykubun)){
                $sqlString .= "      and t6.holiday_kubun = ? ";
            }
            if(!empty($this->paramworkingtimetableno)){
                $sqlString .= "      and t6.working_timetable_no = ? ";
            }
            $sqlString .= "      and t1.is_deleted = ? ";
            $sqlString .= "  order by ";
            $sqlString .= "    t1.department_code asc ";
            $sqlString .= "    , t1.employment_status asc ";
            $sqlString .= "    , t1.code asc ";
            $sqlString .= "    , t5.date asc ";

            // バインド
            $dt = null;
            if (isset($this->paramfromdate)) {
                $dt = new Carbon($this->paramfromdate);
            } else {
                $dt = new Carbon();
            }
            $target_start_date = $dt->format('Ymd');
            $dt = null;
            if (isset($this->paramtodate)) {
                $dt = new Carbon($this->paramtodate);
            } else {
                $dt = new Carbon();
            }
            $target_end_date = $dt->format('Ymd');

            $array_setBindingsStr = array();
            // $subquery1
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = $target_end_date;
            $array_setBindingsStr[] = Config::get('const.C025.admin_user');
            $array_setBindingsStr[] = 0;
            // $subquery2
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = $target_end_date;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = $target_end_date;
            $array_setBindingsStr[] = 0;
            // t4
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = Config::get('const.C001.value');;
            $array_setBindingsStr[] = 0;
            // cross join
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            if(!empty($this->paramfromdate) && !empty($this->paramtodate)) {
                $array_setBindingsStr[] = $target_start_date;
                $array_setBindingsStr[] = $target_end_date;
            } else {
                if(!empty($this->paramfromdate)) {
                    $array_setBindingsStr[] = $target_start_date;
                }
            }
            $array_setBindingsStr[] = 0;
            // t6
            $array_setBindingsStr[] = 0;
            // t7
            $array_setBindingsStr[] = 0;
            // t8
            $array_setBindingsStr[] = Config::get('const.C007.value');
            $array_setBindingsStr[] = 0;
            // t9
            $array_setBindingsStr[] = Config::get('const.C013.value');
            $array_setBindingsStr[] = 0;

            // $subquery3
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = $target_end_date;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = Config::get('const.C004.regular_working_time');
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 0;

            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            if(!empty($this->paramdepartmentcode)) {
                $array_setBindingsStr[] = $this->paramdepartmentcode;
            }
            if(!empty($this->paramemploymentstatus)) {
                $array_setBindingsStr[] = $this->paramemploymentstatus;
            }
            if(!empty($this->paramusercode)){
                $array_setBindingsStr[] = $this->paramusercode;
            }
            // if(!empty($this->paramfromdate) && !empty($this->paramtodate)) {
            //     $array_setBindingsStr[] = $target_start_date;
            //     $array_setBindingsStr[] = $target_end_date;
            // } else {
            //     if(!empty($this->paramfromdate)) {
            //         $array_setBindingsStr[] = $target_start_date;
            //     }
            // }
            if(!empty($this->parambusinesskubun)){
                $array_setBindingsStr[] = $this->parambusinesskubun;
            }
            if(!empty($this->paramholidaykubun)){
                $array_setBindingsStr[] = $this->paramholidaykubun;
            }
            if(!empty($this->paramworkingtimetableno)){
                $array_setBindingsStr[] = $this->paramworkingtimetableno;
            }
            $array_setBindingsStr[] = 0;

            $result = DB::select($sqlString, $array_setBindingsStr);
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


        return $result;
    }

    /**
     * 1年分の検索
     *
     * @return void
     */
    public function getCalenderDateYear($ptn, $formdata){

        try {
            // table_generalcodesはダミーテーブルとして使用
            $result = true;

            $subquery1 = DB::table('information_schema.COLUMNS')
                ->selectRaw('@num := @num + 1');

            $subquery2 = DB::table($this->table_generalcodes)
                ->selectRaw('0 as generate_series')
                ->whereRaw("(@num := 1 - 1) * 0")
                ->unionAll($subquery1)
                ->limit(366)
                ->toSql();

            $subquery3 = DB::table(DB::raw('('.$subquery2.') AS t1'))
                ->selectRaw("date_format(date_add('".$this->paramfromdate."', interval t1.generate_series day), '%Y%m%d') as dt")
                ->selectRaw("weekday(date_format(date_add('".$this->paramfromdate."', interval t1.generate_series day), '%Y%m%d')) as we")
                ->toSql();
        
            $subquery4 = DB::table($this->table_generalcodes)
                ->selectRaw("date_format(date ('".$this->paramtodate."'), '%Y%m%d') as dt")
                ->limit(1)
                ->toSql();
        
            $subquery5 = DB::table($this->table_public_holidays)
                ->selectRaw("date_format(".$this->table_public_holidays.".date, '%Y%m%d') as public_holidays_date")
                ->where($this->table_public_holidays.'.is_deleted', '=', 0);

            if ($ptn == Config::get('const.CALENDAR_PTN.ptn1')) {
                $case_sql = ' case ifnull(t3.public_holidays_date, 0) ';
                $case_sql .= ' when 0 then ';
                $case_sql .= '   case t2.we ';
                $case_sql .= '     when 5 then 3 ';
                $case_sql .= '     when 6 then 2 ';
                $case_sql .= '     else 1 ';
                $case_sql .= '   end ';
                $case_sql .= ' else 3 ';
                $case_sql .= ' end as business_kubun ';
            } else {
                $case_sql = ' case t2.we ';
                for ($i=0 ; $i<7 ; $i++) {
                    if ($formdata['initptn_business'][$i] == null || $formdata['initptn_business'][$i] == "") {
                        $case_sql .= ' when '.$i.' then 0';
                    } else {
                        $case_sql .= ' when '.$i.' then '.$formdata['initptn_business'][$i];
                    }
                }
                $case_sql .= " else 0 ";
                $case_sql .= ' end as business_kubun ';
            }

            $mainquery = DB::table(DB::raw('('.$subquery3.') AS t2'))
                ->select('t2.dt as date');
            $mainquery
                ->selectRaw("'".$this->department_code."' as department_code")
                ->selectRaw($this->employment_status.' as employment_status')
                ->selectRaw("'".$this->user_code."' as user_code");
            $mainquery
                ->addselect('t2.we as weekday_kubun');
            $mainquery
                ->selectRaw($case_sql);
                if ($ptn == Config::get('const.CALENDAR_PTN.ptn1')) {
                    $mainquery
                        ->selectRaw('0 as holiday_kubun');
                } else {
                    $case_sql = ' case t2.we ';
                    for ($i=0 ; $i<7 ; $i++) {
                        if ($formdata['initptn_holiday'][$i] == null || $formdata['initptn_holiday'][$i] == "") {
                            $case_sql .= ' when '.$i.' then 0';
                        } else {
                            $case_sql .= ' when '.$i.' then '.$formdata['initptn_holiday'][$i];
                        }
                    }
                    $case_sql .= " else 0 ";
                    $case_sql .= ' end as holiday_kubun ';
                    $mainquery
                        ->selectRaw($case_sql);
                }
                $systemdate = Carbon::now();
                $data = $mainquery            
                    ->selectRaw("'".$this->working_timetable_no."' as working_timetable_no")
                    ->selectRaw("'".$this->created_user."' as created_user")
                    ->selectRaw('null as updated_user')
                    ->selectRaw("'".$systemdate."' as created_at")
                    ->selectRaw('null as updated_at')
                    ->leftJoinSub($subquery5, 't3', function ($join) { 
                        $join->on('t3.public_holidays_date', '=', 't2.dt');
                    })
                    ->where('t2.dt', '<=', DB::raw('('.$subquery4.')'))
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
     * 更新（共通）
     *
     * @return boolean
     */
    public function updateCommon($array_update){
        try {
            $mainquery = DB::table($this->table);
            if(!empty($this->paramfromdate)) {
                $mainquery
                    ->where($this->table.'.date', '>=', $this->paramfromdate);
            }
            if(!empty($this->paramtodate)) {
                $mainquery
                    ->where($this->table.'.date', '<=', $this->paramtodate);
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
