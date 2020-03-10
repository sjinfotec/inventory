<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\ApiCommonController;
use Carbon\Carbon;


class Calendar extends Model
{
    protected $table = 'calendars';
    protected $table_users = 'users';
    protected $table_public_holidays = 'public_holidays';
    protected $table_generalcodes = 'generalcodes';
    protected $table_working_timetables = 'working_timetables';
 
    private $date;
    private $department_code;
    private $employment_status;
    private $user_code;
    private $weekday_kubun;                  
    private $business_kubun;       
    private $holiday_kubun;     
    private $created_user;    
    private $updated_user;                  
    private $created_at;                  
    private $updated_at;                  
    private $is_deleted;                  
   
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
    private $paramholidaykubun;
    
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

    // 曜日区分
    public function getParambusinesskubunAttribute()
    {
        return $this->parambusinesskubun;
    }

    public function setParambusinesskubunAttribute($value)
    {
        $this->parambusinesskubun = $value;
    }
     
    public function getParamholidaykubunAttribute()
    {
        return $this->paramholidaykubun;
    }

    public function setParamholidaykubunAttribute($value)
    {
        $this->paramholidaykubun = $value;
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
    public function updateCalendar(){
        try {
            $mainquery = DB::table($this->table);
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
            if(!empty($this->paramfromdate) && !empty($this->paramtodate)) {
                $mainquery
                    ->whereBetween($this->table.'.date', [$this->paramfromdate, $this->paramtodate]);
            } else {
                if(!empty($this->paramfromdate)) {
                    $mainquery
                        ->where($this->table.'.date',$this->paramfromdate);
                }
            }
            $mainquery
                ->where($this->table.'.is_deleted', 0)
                ->update([
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
     * date存在チェック
     *
     * @return boolean
     */
    public function isExistsDate(){
        try {
            $mainquery = DB::table($this->table);
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
            if(!empty($this->paramfromdate) && !empty($this->paramtodate)) {
                $mainquery
                    ->whereBetween($this->table.'.date', [$this->paramfromdate, $this->paramtodate]);
            } else {
                if(!empty($this->paramfromdate)) {
                    $mainquery
                        ->where($this->table.'.date',$this->paramfromdate);
                }
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
     * date削除（物理削除）
     *
     * @return void
     */
    public function delDate(){
        try {
            $mainquery = DB::table($this->table);
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
            if(!empty($this->paramfromdate) && !empty($this->paramtodate)) {
                $mainquery
                    ->whereBetween($this->table.'.date', [$this->paramfromdate, $this->paramtodate]);
            } else {
                if(!empty($this->paramfromdate)) {
                    $mainquery
                        ->where($this->table.'.date',$this->paramfromdate);
                }
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
                ->selectRaw("ifnull(t6.secound_code_name, '') as business_kubun_name")
                ->selectRaw("ifnull(t7.secound_code_name, '') as holiday_kubun_name");
            // join
            // 適用期間日付の取得
            $apicommon = new ApiCommonController();
            // usersの最大適用開始日付subquery
            $subquery1 = $apicommon->getUserApplyTermSubquery($this->paramtodate);
            // departmentsの最大適用開始日付subquery
            $subquery2 = $apicommon->getDepartmentApplyTermSubquery($this->paramtodate);
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
            // 休暇区分
            $mainquery
                ->leftJoin($this->table_generalcodes.' as t7', function ($join) { 
                    $join->on('t7.code', '=',  $this->table.'.holiday_kubun')
                    ->where('t7.identification_id', '=', Config::get('const.C008.value'))
                    ->where('t7.is_deleted', '=', 0);
            });
    
            Log::debug('$this->paramdepartmentcode = '.$this->paramdepartmentcode);
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

    /**
     * 検索
     *
     * @return void
     */
    public function getCalenderDate(){
        try {
            $mainquery = DB::table($this->table)
                ->select(
                    $this->table.'.date',
                    $this->table.'.user_code',
                    $this->table.'.weekday_kubun',
                    $this->table.'.business_kubun',
                    $this->table.'.holiday_kubun'
                )
                ->selectRaw(
                    "concat(
                        DATE_FORMAT(".$this->table.".date,'%Y年%m月%d日'),'(',substring('月火水木金土日',convert(".$this->table.".weekday_kubun+1,char),1),') '
                    , ifnull(".$this->table_public_holidays.".name, '')) as date_name ");

            $mainquery->leftJoin($this->table_public_holidays, function ($join) { 
                $join->on($this->table_public_holidays.'.date', '=', $this->table.'.date')
                    ->where($this->table_public_holidays.'.is_deleted',0);
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

    /**
     * シフト編集用検索
     *
     * @return void
     */
    public function getShiftCalenderDate($WorkingTime_name){
        try {
            $mainquery = DB::table($this->table)
                ->select(
                    $this->table.'.date as target_date',
                    $this->table.'.user_code as user_code')
                ->selectRaw(' null as department_code')    
                ->selectRaw(' 1 as working_timetable_no')    
                ->selectRaw(
                    "concat(
                        DATE_FORMAT(".$this->table.".date,'%Y年%m月%d日'),'(',substring('月火水木金土日',convert(".$this->table.".weekday_kubun+1,char),1),')') as date_name ");
            $mainquery->selectRaw("concat('<', '".$WorkingTime_name."', '> 勤務') as working_timetable_name");
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
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    
        return $data;
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
     * 1年分の削除
     *
     * @return void
     */
    public function delCalenderDateYear($fromdate, $todate){

        $result = true;

        try{
            $mainquery = DB::table($this->table);
            if(!empty($this->paramdepartmentcode)) {
                $mainquery
                    ->where($this->table.'.department_code',$this->paramdepartmentcode);
            }
            if(!empty($this->paramemploymentstatus)) {
                $mainquery
                    ->where($this->table.'.employment_status',$this->paramemploymentstatus);
            }
            if(!empty($this->paramusercode)) {
                $mainquery->where($this->table.'.user_code', "=", $this->paramusercode);
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
            $mainquery->delete();
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_delete_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_delete_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
        return $result;
    }

    /**
     * ユーザー登録
     *
     * @return void
     */
    public function storeByUser(){

        $result = true;
        try{
            if(empty($this->user_code)) {
                return false;
            }
            if(empty($this->created_user)) {
                return false;
            }
            if(empty($this->created_at)) {
                return false;
            }
            $this->setParamusercodeAttribute($this->user_code);
            $this->delDate();
            $results = $this->makeUserCalendar();
            $temp_array = array();
            foreach($results as $item) {
                $temp_collect = collect($item);
                $temp_array[] = $temp_collect->toArray();
            } 
            $this->insCalenderDateYear($temp_array);
            return $result;
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
     * 検索（insert用ALLユーザおよびuser_code付与）
     *
     * @return void
     */
    public function makeUserCalendar(){
        try {
            $mainquery = DB::table($this->table)
                ->select($this->table.'.date');
            $mainquery
                ->selectRaw($this->user_code.' as user_code');
            $mainquery
                ->addselect(
                    $this->table.'.weekday_kubun_',
                    $this->table.'.business_kubun',
                    $this->table.'.holiday_kubun'
                );
            $systemdate = Carbon::now();
            $mainquery
                ->selectRaw("'".$this->created_user."' as created_user")
                ->selectRaw('null as updated_user')
                ->selectRaw("'".$systemdate."' as created_at")
                ->selectRaw('null as updated_at')
                ->selectRaw('0 as is_deleted');
            $result = $mainquery->where($this->table.'.user_code', "=", Config::get('const.user_part.alluser'))->get();
            Log::debug('makeUserCalendar  '.$this->created_user);
            return $result;
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

}
