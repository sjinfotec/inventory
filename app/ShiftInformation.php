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

    private $param_department_code;                         // 部署コード
    private $param_employment_status;                       // 雇用形態
    private $param_user_code;                               // ユーザーコード
    private $param_working_timetable_no;                    // タイムテーブルNO
    private $param_max_apply_term_from;                     // 最大有効期間
    private $param_fromdate;                                // 開始日付
    private $param_todate;                                  // 終了日付

    // 部署コード
    public function getParamdepartmentcodeAttribute()
    {
        return $this->param_department_code;
    }

    public function setParamdepartmentcodeAttribute($value)
    {
        $this->param_department_code = $value;
    }

    // 雇用形態
    public function getParamemploymentstatusAttribute()
    {
        return $this->param_employment_status;
    }

    public function setParamemploymentstatusAttribute($value)
    {
        $this->param_employment_status = $value;
    }

    // ユーザーコード
    public function getParamusercodeAttribute()
    {
        return $this->param_user_code;
    }

    public function setParamusercodeAttribute($value)
    {
        $this->param_user_code = $value;
    }
    
    // タイムテーブルNO
    public function getParamWorkingtimetablenoAttribute()
    {
        return $this->param_working_timetable_no;
    }

    public function setParamWorkingtimetablenoAttribute($value)
    {
        $this->param_working_timetable_no = $value;
    }

    // 開始日付
    public function getParamMaxApplyTermfromAttribute()
    {
        return $this->param_max_apply_term_from;
    }

    public function setParamMaxApplyTermfromAttribute($value)
    {
        $this->param_max_apply_term_from = $value;
    }

    // 開始日付
    public function getParamfromdateAttribute()
    {
        return $this->param_fromdate;
    }

    public function setParamfromdateAttribute($value)
    {
        $this->param_fromdate = $value;
    }

    // 終了日付
    public function getParamtodateAttribute()
    {
        return $this->param_todate;
    }

    public function setParamtodateAttribute($value)
    {
        $this->param_todate = $value;
    }

    /**
     * ユーザーシフト取得
     *
     * @return void
     */
    public function getUserShift(){
        try {
            $sqlString = "";
            $sqlString .= "select ";
            $sqlString .= "    t1.department_code as department_code ";
            $sqlString .= "    , t1.employment_status as employment_status ";
            $sqlString .= "    , t1.user_code as user_code ";
            $sqlString .= "    , t1.date as date ";
            $sqlString .= "    , t1.date_name as date_name ";
            $sqlString .= "    , t1.working_timetable_no as working_timetable_no ";
            $sqlString .= "    , t2.name as working_timetable_name ";
            $sqlString .= "from ( ";
            $sqlString .= "    select ";
            $sqlString .= "        t1.department_code as department_code ";
            $sqlString .= "        , t1.employment_status as employment_status ";
            $sqlString .= "        , t1.code as user_code ";
            $sqlString .= "        , t2.date as date ";
            $sqlString .= "        , concat(DATE_FORMAT(t2.date, '%Y年%m月%d日('),  substring('月火水木金土日', convert(t2.weekday_kubun + 1, char), 1), ')') as date_name ";
            $sqlString .= "        , case ifnull(t3.working_timetable_no, 0) ";
            $sqlString .= "          when 0 then t1.working_timetable_no ";
            $sqlString .= "          else t3.working_timetable_no ";
            $sqlString .= "          end as working_timetable_no  ";
            $sqlString .= "    from ";
            $sqlString .= "        ".$this->table_users." as t1 ";
            $sqlString .= "        left join ( ";
            $sqlString .= "            select ";
            $sqlString .= "            department_code ";
            $sqlString .= "            , user_code ";
            $sqlString .= "            , date ";
            $sqlString .= "            , weekday_kubun ";
            $sqlString .= "            from ";
            $sqlString .= "            ".$this->table_calendars." ";
            $sqlString .= "            where ";
            $sqlString .= "                date between ? AND ? ";
            $sqlString .= "                and is_deleted = ? ";
            $sqlString .= "        ) t2 ";
            $sqlString .= "        on t2.department_code = t1.department_code ";
            $sqlString .= "            and t2.user_code = t1.code ";
            $sqlString .= "        left join ( ";
            $sqlString .= "            select ";
            $sqlString .= "                department_code     ";       
            $sqlString .= "                , user_code ";
            $sqlString .= "                , target_date ";
            $sqlString .= "                , working_timetable_no as working_timetable_no ";
            $sqlString .= "            from ";
            $sqlString .= "                 ".$this->table." ";
            $sqlString .= "            where ";
            $sqlString .= "                is_deleted = ? ";
            $sqlString .= "        ) as t3 ";
            $sqlString .= "        on t3.department_code = t1.department_code ";
            $sqlString .= "            and t3.user_code = t1.code ";
            $sqlString .= "            and t3.target_date = t2.date ";
            $sqlString .= "        inner join ( ";
            $sqlString .= "            select ";
            $sqlString .= "                code as code     ";       
            $sqlString .= "                , MAX(apply_term_from) as max_apply_term_from ";
            $sqlString .= "            from ";
            $sqlString .= "                ".$this->table_users." ";
            $sqlString .= "            where ";
            $sqlString .= "                apply_term_from <= ? ";
            $sqlString .= "                and role < ? ";
            $sqlString .= "                and is_deleted = ? ";
            $sqlString .= "            group by ";
            $sqlString .= "                code ";
            $sqlString .= "        ) as t4 ";
            $sqlString .= "        on t4.code = t1.code ";
            $sqlString .= "           and  t4.max_apply_term_from = t1.apply_term_from ";
            $sqlString .= "    where ";
            $sqlString .= "        t1.is_deleted = ? ";
            $sqlString .= "    ) t1 ";
            $sqlString .= "    inner join ( ";
            $sqlString .= "        select ";
            $sqlString .= "            t2.department_code as department_code ";
            $sqlString .= "            , t2.user_code as user_code ";
            $sqlString .= "            , t2.date as date ";
            $sqlString .= "            , t1.no as no ";
            $sqlString .= "            , t1.name as name ";
            $sqlString .= "        from ";
            $sqlString .= "            ".$this->table_working_timetables." t1 ";
            $sqlString .= "            inner join ( ";
            $sqlString .= "                select ";
            $sqlString .= "                    t1.department_code as department_code ";
            $sqlString .= "                    , t1.user_code as user_code ";
            $sqlString .= "                    , t1.date as date ";
            $sqlString .= "                    , t2.no as no ";
            $sqlString .= "                    , MAX(t2.apply_term_from) as max_apply_term_from  ";
            $sqlString .= "                from ";
            $sqlString .= "                    ".$this->table_calendars." as t1 ";
            $sqlString .= "                    left join (  ";
            $sqlString .= "                        select ";
            $sqlString .= "                            apply_term_from as apply_term_from  ";
            $sqlString .= "                            , no as no ";
            $sqlString .= "                        from ";
            $sqlString .= "                            ".$this->table_working_timetables." ";
            $sqlString .= "                        where ";
            $sqlString .= "                            no not in (?)  ";
            $sqlString .= "                            and is_deleted = ?  ";
            $sqlString .= "                        group by ";
            $sqlString .= "                            apply_term_from ";
            $sqlString .= "                            , no ";
            $sqlString .= "                    ) as t2  ";
            $sqlString .= "                    on t2.apply_term_from <= t1.date  ";
            $sqlString .= "                where ";
            $sqlString .= "                    t1.date between ? and ? ";
            $sqlString .= "                    and t1.is_deleted = ?  ";
            $sqlString .= "                group by ";
            $sqlString .= "                    t1.department_code ";
            $sqlString .= "                    , t1.user_code ";
            $sqlString .= "                    , t1.date ";
            $sqlString .= "                    , t2.no ";
            $sqlString .= "                    ) t2  ";
            $sqlString .= "            on t2.no = t1.no  ";
            $sqlString .= "            and t2.max_apply_term_from = t1.apply_term_from  ";
            $sqlString .= "        where ";
            $sqlString .= "            t1.is_deleted = 0  ";
            $sqlString .= "        group by ";
            $sqlString .= "            t2.department_code ";
            $sqlString .= "            , t2.user_code ";
            $sqlString .= "            , t2.date ";
            $sqlString .= "            , t1.no ";
            $sqlString .= "            , t1.name ";
            $sqlString .= "        ) t2  ";
            $sqlString .= "    on t1.department_code = t2.department_code  ";
            $sqlString .= "        and t1.user_code = t2.user_code  ";
            $sqlString .= "        and t1.date = t2.date  ";
            $sqlString .= "        and t1.working_timetable_no = t2.no ";
            $sqlString .= "where ? = ? ";
            if(!empty($this->param_department_code)) {
                $sqlString .= "and t1.department_code = ? ";
            }
            if(!empty($this->param_employment_status)) {
                $sqlString .= "and t1.employment_status = ? ";
            }
            if(!empty($this->param_user_code)) {
                $sqlString .= "and t1.user_code = ? ";
            }
            if(!empty($this->param_working_timetable_no)) {
                $sqlString .= "and t1.working_timetable_no = ? ";
            }
            $sqlString .= "order by t1.date asc ";
            $array_setBindingsStr = array();
            $array_setBindingsStr[] = $this->param_fromdate;
            $array_setBindingsStr[] = $this->param_todate;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = $this->param_fromdate;
            $array_setBindingsStr[] = (int)Config::get('const.C017.admin_user');
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 9999;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = $this->param_fromdate;
            $array_setBindingsStr[] = $this->param_todate;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            if(!empty($this->param_department_code)) {
                $array_setBindingsStr[] = $this->param_department_code;
            }
            if(!empty($this->param_employment_status)) {
                $array_setBindingsStr[] = $this->param_employment_status;
            }
            if(!empty($this->param_user_code)) {
                $array_setBindingsStr[] = $this->param_user_code;
            }
            if (!empty($this->param_working_timetable_no)) {
                $array_setBindingsStr[] = $this->param_working_timetable_no;
            }
            $result = DB::select($sqlString, $array_setBindingsStr);
    
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
     * ユーザーシフト取得
     *
     * @return void
     */
    // public function getUserShift(){
    //     try {
    //         $subquery1 = DB::table($this->table_working_timetables)
    //             ->select('no as no', 'apply_term_from as apply_term_from')
    //             ->where('is_deleted', '=', ':is_deleted1')
    //             ->groupBy('no', 'apply_term_from');

    //         $subquery2 = DB::table($this->table_calendars.' as t1')
    //             ->select(
    //                 't2.no as no',
    //                 't1.date as date',
    //                 't1.user_code as user_code'
    //             );
    //         $subquery2
    //             ->selectRaw('MAX(t2.apply_term_from) as max_apply_term_from')
    //             ->leftJoinSub($subquery1, 't2', function ($join) { 
    //                 $join->on('t2.apply_term_from', '<=', 't1.date');
    //             })
    //             ->where('t2.no', '<>', ':t2_no');
    //         if(!empty($this->start_target_date && !empty($this->end_target_date))){
    //             $subquery2->whereBetween('t1.date', [':start_target_date', ':end_target_date']);
    //         }
    //         $subquery2
    //             ->where('t1.is_deleted', '=', ':is_deleted2')
    //             ->groupBy('t2.no', 't1.date', 't1.user_code');

    //         $subquery3 = DB::table($this->table_calendars.' as t1')
    //             ->select('t1.date as date', 't1.user_code as user_code', 't1.weekday_kubun as weekday_kubun', 't2.no as no', 't3.name as name')
    //             ->JoinSub($subquery2,'t2', function ($join) { 
    //                 $join->on('t2.date', '=', 't1.date');
    //                 $join->on('t1.user_code', '=', 't2.user_code');
    //             });
    //         $subquery3
    //             ->join($this->table_working_timetables.' as t3', function ($join) { 
    //                 $join->on('t3.no', '=', 't2.no');
    //                 $join->on('t3.apply_term_from', '=', 't2.max_apply_term_from')
    //                 ->where('t1.is_deleted', '=', ':is_deleted3');
    //             });
    //         $subquery3->groupBy('t1.date', 't1.user_code', 't1.weekday_kubun', 't2.no', 't3.name');
    //         $subquery31 = $subquery3->toSql();
    //         $subquery4 = DB::table($this->table.' as t2')
    //             ->select(
    //                 't2.department_code as department_code',
    //                 't2.working_timetable_no as working_timetable_no',
    //                 't2.user_code as user_code',
    //                 't2.target_date as target_date');
    //         if(!empty($this->user_code)) {
    //             $subquery4
    //                 ->where('t2.user_code', '=', ':t2_user_code')
    //                 ->where('t2.is_deleted', '=', ':t2_is_deleted1');
    //         } else {
    //             $subquery4
    //                 ->where('t2.is_deleted', '=', ':t2_is_deleted2');
    //         }
    //         $subquery41 = $subquery4->toSql();

    //         $mainquery = DB::table(DB::raw('('.$subquery31.') AS t1'))
    //             ->select('t1.date as date')
    //             ->selectRaw(
    //                 "concat(
    //                     DATE_FORMAT(t1.date,'%Y年%m月%d日'),'(',substring('月火水木金土日',convert(t1.weekday_kubun+1,char),1),')') as date_name ")
    //             ->addselect('t1.no as no')
    //             ->addselect('t1.name as working_timetable_name')
    //             ->addselect('t2.department_code as department_code')
    //             ->addselect('t2.working_timetable_no as working_timetable_no')
    //             ->addselect('t2.user_code as user_code')
    //             ->leftJoin(DB::raw('('.$subquery41.') AS t2'), function ($join) { 
    //                 $join->on('t2.target_date', '=', 't1.date');
    //                 $join->on('t2.user_code', '=', 't1.user_code');
    //             });

    //         if (!empty($this->param_working_timetable_no)) {
    //             $mainquery
    //                 ->whereColumn('t1.no', '=', 't2.working_timetable_no')
    //                 ->where('t1.no', '=', ':t1_no1');
    //         } else {
    //             $mainquery
    //                 ->where(function ($query) {
    //                     $query->whereColumn('t1.no', '=', 't2.working_timetable_no');
    //                     })->orwhere(function ($query) {
    //                             $query->Where('t1.no', '=', ':t1_no')
    //                                     ->WhereNull('t2.working_timetable_no');
    //                     });
    //         }
    //         $mainquery->orderBy('t1.date', 'asc');

    //         $array_setBindingsStr = array();
    //         $cnt = 0;
    //         $cnt += 1;
    //         $array_setBindingsStr[] = array([$cnt=>0]);
    //         $cnt += 1;
    //         $array_setBindingsStr[] =  array([$cnt=>9999]);
    //         if(!empty($this->start_target_date && !empty($this->end_target_date))){
    //             $cnt += 1;
    //             $array_setBindingsStr[] = array([$cnt=>$this->start_target_date]);
    //             $cnt += 1;
    //             $array_setBindingsStr[] = array([$cnt=>$this->end_target_date]);
    //         }
    //         $cnt += 1;
    //         $array_setBindingsStr[] =  array([$cnt=>0]);
    //         $cnt += 1;
    //         $array_setBindingsStr[] =  array([$cnt=>0]);
    //         if(!empty($this->user_code)) {
    //             $cnt += 1;
    //             $array_setBindingsStr[] =  array([$cnt=>$this->user_code]);
    //             $cnt += 1;
    //             $array_setBindingsStr[] =  array([$cnt=>0]);
    //         } else {
    //             $cnt += 1;
    //             $array_setBindingsStr[] =  array([$cnt=>0]);
    //         }
    //         if (!empty($this->param_working_timetable_no)) {
    //             $cnt += 1;
    //             $array_setBindingsStr[] =  array([$cnt=>$this->param_working_timetable_no]);
    //         } else {
    //             $cnt += 1;
    //             $array_setBindingsStr[] =  array([$cnt=>1]);
    //         }
    //         $mainquery->setBindings($array_setBindingsStr);
    //         $result = $mainquery->get();
    
    //     }catch(\PDOException $pe){
    //         Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
    //         Log::error($pe->getMessage());
    //         throw $pe;
    //     }catch(\Exception $e){
    //         Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
    //         Log::error($e->getMessage());
    //         throw $e;
    //     }
    //     return $result;
    // }

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
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_exists_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_exists_erorr')).'$e');
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
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_delete_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_delete_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

}
