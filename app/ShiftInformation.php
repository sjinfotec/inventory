<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;


class ShiftInformation extends Model
{
    protected $table = 'shift_informations';
    protected $table_users = 'users';
    protected $table_calendars = 'calendars';
    protected $table_working_timetables = 'working_timetables';

    private $working_timetable_no; 
    private $user_code;
    private $department_code;                  
    private $target_date;        
    private $start_target_date;        
    private $end_target_date;
    private $created_at;                  
    private $updated_at;         
                     
     
    public function getWorkingtimetablenoAttribute()
    {
        return $this->working_timetable_no;
    }

    public function setWorkingtimetablenoAttribute($value)
    {
        $this->working_timetable_no = $value;
    }        
     
    public function getUsercodeAttribute()
    {
        return $this->user_code;
    }

    public function setUsercodeAttribute($value)
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
     
    public function getTargetdateAttribute()
    {
        return $this->target_date;
    }

    public function setTargetdateAttribute($value)
    {
        $this->target_date = $value;
    }
     
    public function getStarttargetdateAttribute()
    {
        return $this->start_target_date;
    }

    public function setStarttargetdateAttribute($value)
    {
        $date = date_create($value);
        $this->start_target_date = date_format($date, 'Ymd');
    }

     
    public function getEndtargetdateAttribute()
    {
        return $this->end_target_date;
    }

    public function setEndtargetdateAttribute($value)
    {
        $date = date_create($value);
        $this->end_target_date = date_format($date, 'Ymd');
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

    //--------------- パラメータ項目属性 -----------------------------------

    private $param_max_apply_term_from;                   // 最大有効期間

    // 開始日付
    public function getParamMaxApplyTermfromAttribute()
    {
        return $this->param_max_apply_term_from;
    }

    public function setParamMaxApplyTermfromAttribute($value)
    {
        $this->param_max_apply_term_from = $value;
    }

    /**
     * ユーザーシフト取得
     *
     * @return void
     */
    public function getUserShift(){
        try {
            \DB::enableQueryLog();
            $subquery1 = DB::table($this->table_working_timetables)
                ->select('no as no')
                ->selectRaw('MAX(apply_term_from) as max_apply_term_from')
                ->where('is_deleted', '=', 0)
                ->groupBy('no');
    
            $subquery2 = DB::table($this->table_working_timetables.' as t1')
                ->select(
                    't1.no as no',
                    't1.name as name'
                );
            $subquery2
                ->JoinSub($subquery1, 't2', function ($join) { 
                    $join->on('t1.apply_term_from', '=', 't2.max_apply_term_from');
                })
                ->where('t1.is_deleted', '=', 0)
                ->groupBy('t1.no', 't1.name');

            $mainquery = DB::table($this->table.' as t1')
                ->select( 
                    't1.target_date as target_date',
                    't1.department_code as department_code',
                    't1.user_code as user_code',
                    't1.working_timetable_no as working_timetable_no'
                )
                ->selectRaw(
                    "concat(
                        DATE_FORMAT(t3.date,'%Y年%m月%d日'),'(',substring('月火水木金土日',convert(t3.weekday_kubun+1,char),1),')') as date_name ")
                ->selectRaw("concat('<',t2.name,'> 勤務') as working_timetable_name")
                ->JoinSub($subquery2,'t2', function ($join) { 
                    $join->on('t2.no', '=', 't1.working_timetable_no');
                });
            $mainquery
                ->join($this->table_calendars.' as t3', function ($join) { 
                    $join->on('t3.date', '=', 't1.target_date');
                });

            if(!empty($this->start_target_date && !empty($this->end_target_date))){
                $mainquery->whereBetween('t1.target_date', [$this->start_target_date, $this->end_target_date]);
            }
            if(!empty($this->user_code)){
                $mainquery->where('t1.user_code', '=', $this->user_code);
            }
            $mainquery
                ->where('t2.no', '<>', 9999)
                ->where('t1.is_deleted', '=', 0)
                ->where('t3.is_deleted', '=', 0)
                ->orderBy('t1.target_date','asc');
            $result = $mainquery->get();
    
        
            \Log::debug(
                'sql_debug_log',
                [
                    'getAlertData' => \DB::getQueryLog()
                ]
                );
                \DB::disableQueryLog();
        }catch(\PDOException $pe){
            Log::error(str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error(str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
        return $result;
    }

    /**
     * シフト登録
     *
     * @return void
     */
    public function insertUserShift(){
        try {
            DB::table($this->table)->insert(
                [
                    'user_code' => $this->user_code,
                    'department_code' => $this->department_code,
                    'working_timetable_no' => $this->working_timetable_no,
                    'target_date' => $this->target_date,
                    'created_at' => $this->created_at,
                ]
            );
        }catch(\PDOException $pe){
            Log::error(str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error(str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * シフト存在チェック
     *
     * @return boolean
     */
    public function isExistsShiftInfo(){
        try {
            $is_exists = DB::table($this->table)
                ->where('user_code',$this->user_code)
                ->where('department_code', $this->department_code)
                ->whereBetween('target_date', [$this->start_target_date,$this->end_target_date])
                ->exists();
        }catch(\PDOException $pe){
            Log::error(str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error(str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
        return $is_exists;
}

    /**
     * シフト削除
     *
     * @return void
     */
    public function delShiftInfo(){
        try {
            DB::table($this->table)
            ->where('user_code', $this->user_code)
            ->where('department_code', $this->department_code)
            ->whereBetween('target_date', [$this->start_target_date,$this->end_target_date])
            ->delete();
        }catch(\PDOException $pe){
            Log::error(str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error(str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

}
