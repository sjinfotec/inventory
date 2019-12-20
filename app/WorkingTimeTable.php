<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ApiCommonController;
use Carbon\Carbon;


class WorkingTimeTable extends Model
{
    protected $table = 'working_timetables';
    protected $table_users = 'users';
    protected $table_temp_calc_workingtimes = 'temp_calc_workingtimes';
    protected $table_generalcodes = 'generalcodes';
    // protected $guarded = array('id');

    private $id;
    private $no;                  
    private $name;                  
    private $working_time_kubun;                  
    private $from_time;                  
    private $to_time;                  
    private $created_user;                  
    private $updated_user;                  
    private $created_at;                  
    private $updated_at;                  
    private $is_deleted;
    private $apply_term_from;                 // 適用期間開始


    public function getIdAttribute()
    {
        return $this->id;
    }

    public function setIdAttribute($value)
    {
        $this->id = $value;
    }

    public function getNoAttribute()
    {
        return $this->no;
    }

    public function setNoAttribute($value)
    {
        $this->no = $value;
    }
    
    public function getNameAttribute()
    {
        return $this->name;
    }

    public function setNameAttribute($value)
    {
        $this->name = $value;
    }
     
    public function getWorkingtimekubunAttribute()
    {
        return $this->working_time_kubun;
    }

    public function setWorkingtimekubunAttribute($value)
    {
        $this->working_time_kubun = $value;
    }
     
    public function getFromtimeAttribute()
    {
        return $this->from_time;
    }

    public function setFromtimeAttribute($value)
    {
        $this->from_time = $value;
    }
     
    public function getTotimeAttribute()
    {
        return $this->to_time;
    }

    public function setTotimeAttribute($value)
    {
        $this->to_time = $value;
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

    // 適用期間開始
    public function getApplytermfromAttribute()
    {
        return $this->apply_term_from;
    }

    public function setApplytermfromAttribute($value)
    {
        $this->apply_term_from = $value;
    }

    //--------------- パラメータ項目属性 -----------------------------------

    private $param_date_from;                   // 開始日付
    private $param_date_to;                     // 終了日付
    private $param_employment_status;           // 雇用形態
    private $param_department_code;             // 部署
    private $param_user_code;                   // ユーザー

    private $massegedata;                       // メッセージ


    // 開始日付
    public function getParamdatefromAttribute()
    {
        return $this->param_date_from;
    }

    public function setParamdatefromAttribute($value)
    {
        $this->param_date_from = $value;
    }


    // 終了日付
    public function getParamdatetoAttribute()
    {
        return $this->param_date_to;
    }

    public function setParamdatetoAttribute($value)
    {
        $this->param_date_to = $value;
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

    // 部署
    public function getParamDepartmentcodeAttribute()
    {
        return $this->param_department_code;
    }

    public function setParamDepartmentcodeAttribute($value)
    {
        $this->param_department_code = $value;
    }

    // ユーザー
    public function getParamUsercodeAttribute()
    {
        return $this->param_user_code;
    }

    public function setParamUsercodeAttribute($value)
    {
        $this->param_user_code = $value;
    }

    // メッセージ
    public function getMassegedataAttribute()
    {
        return $this->massegedata;
    }

    public function setMassegedataAttribute($value)
    {
        $this->massegedata = $value;
    }

    /**
     * 最大番号取得
     *
     * @return void
     */
    public function getMaxNo(){
        try {
            $mainquery = DB::table($this->table)
                        ->where($this->table.'.no', '<', 9999)
                        ->limit(1);
            $results = $mainquery->max('no');
            return $results;
    
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_maxget_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_maxget_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    
    }

    /**
     * セレクト用データ取得
     *
     * @return void
     */
    public function getTimeTables(){
        try {
            $code_name = DB::table($this->table_generalcodes)
            ->where($this->table_generalcodes.'.identification_id', 'C999')
            ->where($this->table_generalcodes.'.is_deleted', '0')
            ->where($this->table_generalcodes.'.code', '1')
            ->value('code_name');

            // no nameでgroup by した際　noが同じでnameが異なるものが別れて表示してしまう対策
            $subquery = DB::table($this->table)
                ->selectRaw('MAX(apply_term_from) as max_apply_term_from')
                ->selectRaw('no as no')
                ->where('is_deleted', '=', 0)
                ->groupBy('no');

            $mainquery = DB::table($this->table)
                ->JoinSub($subquery, 't1', function ($join) { 
                    $join->on('t1.no', '=', $this->table.'.no');
                    $join->on('t1.max_apply_term_from', '=', $this->table.'.apply_term_from');
                })
                ->select($this->table.'.no',$this->table.'.name',$this->table.'.apply_term_from')
                ->whereNotIn($this->table.'.no', [$code_name])
                ->groupBy($this->table.'.no',$this->table.'.name',$this->table.'.apply_term_from')
                ->orderby($this->table.'.no','asc');
            $results = $mainquery->get();
            return $results;
    
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

    /**
     * 登録(INSERT)
     *
     * @return void
     */
    public function insert(){
        try {
            DB::table($this->table)->insert(
            [
                'apply_term_from' => $this->apply_term_from,
                'no' => $this->no,
                'name' => $this->name,
                'working_time_kubun' => $this->working_time_kubun,
                'from_time' => $this->from_time,
                'to_time' => $this->to_time,
                'created_user' => $this->created_user,
                'created_at' => $this->created_at,
            ]);
    
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
     * 修正(UPDATE)
     *
     * @return void
     */
    public function updateDetail(){
        try {
            DB::table($this->table)
            ->where($this->table.'.id', $this->id)
            ->where($this->table.'.is_deleted', 0)
            ->update(
                [
                    'apply_term_from' => $this->apply_term_from,
                    'name' => $this->name,
                    'working_time_kubun' => $this->working_time_kubun,
                    'from_time' => $this->from_time,
                    'to_time' => $this->to_time,
                    'updated_user' => $this->updated_user,
                    'updated_at' => $this->updated_at,
                ]
            );
            return true;
    
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
     * 詳細取得
     *
     * @return void
     */
    public function getDetail(){
        // 適用期間日付の取得
        $dt = new Carbon();
        $target_date = $dt->format('Ymd');
        try {
            // usersの最大適用開始日付subquery
            $subquery = DB::table($this->table)
                ->select('no as no')
                ->selectRaw('MAX(apply_term_from) as max_apply_term_from')
                ->where('apply_term_from', '<=',$target_date)
                ->where('is_deleted', '=', 0)
                ->groupBy('no');
            $case_sql2 = "CASE t2.max_apply_term_from = t1.apply_term_from ";
            $case_sql2 = $case_sql2." WHEN TRUE THEN 1";
            $case_sql2 = $case_sql2." ELSE CASE t2.max_apply_term_from < t1.apply_term_from ";
            $case_sql2 = $case_sql2."      WHEN TRUE THEN 2 ELSE 0 END ";
            $case_sql2 = $case_sql2." END  as result";
            $mainquery = DB::table($this->table.' AS t1')
                ->select(
                    't1.id', 't1.no', 't1.name', 't1.working_time_kubun')
                ->selectRaw("DATE_FORMAT(t1.apply_term_from, '%Y-%m-%d') as apply_term_from")
                ->selectRaw("DATE_FORMAT(t1.from_time, '%H:%i') as from_time")
                ->selectRaw("DATE_FORMAT(t1.to_time, '%H:%i') as to_time")
                ->selectRaw($case_sql2)
                ->addselect('t1.created_user')
                ->addselect('t1.updated_user');
            $mainquery
                ->leftJoinSub($subquery, 't2', function ($join) { 
                    $join->on('t2.no', '=', 't1.no');
                });
            $results = $mainquery
                ->where('t1.no', $this->no)
                ->where('t1.is_deleted', 0)
                ->orderBy('t1.apply_term_from', 'desc')
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
    
        return $results;
    }

    /**
     * 論理削除
     *
     * @return void
     */
    public function updateIsDelete(){
        try {
            DB::table($this->table)
            ->where('id', $this->id)
            ->update(['is_deleted' => 1]);

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
     * 取得
     *  日付範囲指定は必須とする $subquery5をmeinqueryとしてバインドするため　20191128
     * @return void
     */
    public function getWorkingTimeTableJoin(){
        // subquery1    日次タイムレコード
        try {
            $subquery1 = DB::table($this->table_temp_calc_workingtimes)
                ->select(
                    $this->table_temp_calc_workingtimes.'.working_timetable_no as working_timetable_no'
                );

                if(!empty($this->param_date_from) && !empty($this->param_date_to)){
                    $date = date_create($this->param_date_from);
                    $this->param_date_from = $date->format('Ymd');
                    $date = date_create($this->param_date_to);
                    $this->param_date_to = $date->format('Ymd');
                    $subquery1->where($this->table_temp_calc_workingtimes.'.working_date', '>=', $this->param_date_from);             // 日付範囲指定
                    $subquery1->where($this->table_temp_calc_workingtimes.'.working_date', '<=', $this->param_date_to);               // 日付範囲指定
                }
                if(!empty($this->param_employment_status)){
                    $subquery1->where($this->table_temp_calc_workingtimes.'.employment_status', $this->param_employment_status);      //　雇用形態指定
                }
                if(!empty($this->param_department_code)){
                    $subquery1->where($this->table_temp_calc_workingtimes.'.department_code', $this->param_department_code);          // department_code指定
                }
                if(!empty($this->param_user_code)){
                    $subquery1->where($this->table_temp_calc_workingtimes.'.user_code', $this->param_user_code);                      // user_code指定
                }
                $subquery1->groupBy($this->table_temp_calc_workingtimes.'.working_timetable_no');
            $subquery11 = $subquery1->toSql();

            // working_timetablesの最大適用開始日付subquery
            $apicommon = new ApiCommonController();
            $subquery5 = $apicommon->getTimetableApplyTermSubquery($this->param_date_to);
            $subquery51 = $subquery5->toSql();
            // ---------------- mainquery ----------------------------
            $mainquery = DB::table(DB::raw('('.$subquery51.') as t1'))
                ->select(
                    't1.no as no',
                    't1.name as name',
                    't1.working_time_kubun as working_time_kubun',
                    't1.from_time as from_time',
                    't1.to_time as to_time'
                )
                ->Join(DB::raw('('.$subquery11.') AS t2'), function ($join) { 
                    $join->on('t2.working_timetable_no', '=', 't1.no');
                });

            $results = $mainquery
                ->orderBy('t1.no','asc')
                ->orderBy('t1.working_time_kubun','asc');

            $array_setBindingsStr = array();
            $cnt = 0;
            $cnt += 1;
            $array_setBindingsStr[] = array($cnt=>$this->param_date_to);
            $cnt += 1;
            $array_setBindingsStr[] = array($cnt=>0);
            $cnt += 1;
            $array_setBindingsStr[] = array($cnt=>0);
            if(!empty($this->param_date_from) && !empty($this->param_date_to)){
                $cnt += 1;
                $array_setBindingsStr[] = array($cnt=>$this->param_date_from);
                $cnt += 1;
                $array_setBindingsStr[] = array($cnt=>$this->param_date_to);
            }
            if(!empty($this->param_employment_status)){
                $cnt += 1;
                $array_setBindingsStr[] = array($cnt=>$this->param_employment_status);
            }
            if(!empty($this->param_department_code)){
                $cnt += 1;
                $array_setBindingsStr[] = array($cnt=>$this->param_department_code);
            }
            if(!empty($this->param_user_code)){
                $cnt += 1;
                $array_setBindingsStr[] = array($cnt=>$this->param_user_code);
            }
            if (count($array_setBindingsStr) > 0) {
                $mainquery->setBindings($array_setBindingsStr);
            }
            $results = $mainquery->get();
    
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
        
        return $results;
    }

    /**
     * タイムテーブル名（同名）チェック用
     *
     * @return boolean
     */
    public function isExistsName(){
        try {
            $is_exists = DB::table($this->table)
                ->where('name',$this->name)
                ->where('is_deleted',0)
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

}
