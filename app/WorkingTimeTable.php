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
    protected $table_shift_informations = 'shift_informations';
    protected $table_calendar_setting_informations = 'calendar_setting_informations';

    private $id;
    private $no;                  
    private $name;                  
    private $working_time_kubun;                  
    private $from_time;                  
    private $to_time;                  
    private $ago_time_no;                  
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
     
    public function getAgotimenoAttribute()
    {
        return $this->ago_time_no;
    }

    public function setAgotimenoAttribute($value)
    {
        $this->ago_time_no = $value;
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

    private $param_no;                          // タイムテーブルNo
    private $param_date_from;                   // 開始日付
    private $param_date_to;                     // 終了日付
    private $param_employment_status;           // 雇用形態
    private $param_department_code;             // 部署
    private $param_user_code;                   // ユーザー
    private $param_apply_term_from;             // 適用期間開始
    private $param_ago_time_no;                 // 出勤時刻前の場合のリンクNO

    private $massegedata;                       // メッセージ


    // タイムテーブルNo
    public function getParamnoAttribute()
    {
        return $this->param_no;
    }

    public function setParamnoAttribute($value)
    {
        $this->param_no = $value;
    }

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

    // 適用期間開始
    public function getParamapplytermfromAttribute()
    {
        return $this->param_apply_term_from;
    }

    public function setParamapplytermfromAttribute($value)
    {
        $this->param_apply_term_from = $value;
    }

    // 出勤時刻前の場合のリンクNO
    public function getParamagotimenonoAttribute()
    {
        return $this->param_ago_time_no;
    }

    public function setParamagotimenonoAttribute($value)
    {
        $this->param_ago_time_no = $value;
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
                        ->where($this->table.'.no', '<', Config::get('const.C999_NAME.from_timetable_no'))
                        ->limit(1);
            $results = $mainquery->max('no');
            return $results;
    
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_maxget_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_maxget_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    
    }

    /**
     * full項目取得
     *
     * @return void
     */
    public function getAllTimeTables(){
        try {
            //
            $date_from = null;
            if ($this->param_date_from == "" || $this->param_date_from == null) {
                $date_from = new Carbon();
            } else {
                $date_from = new Carbon($this->param_date_from);
            }
            $target_date = $date_from->format('Ymd');
   
            $apicommon = new ApiCommonController();
            // working_timetablesの最大適用開始日付subquery
            $subquery2 = $apicommon->makeWorkingTimeTableApplyTermSql($target_date);
            // t2:working_timetables
            $sqlString = "";
            $sqlString .= $subquery2;
        
            // バインド
            $array_setBindingsStr = array();
            // subquery2
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = $target_date;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = Config::get('const.C999_NAME.from_timetable_no');
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
     * セレクト用データ取得
     *
     * @return void
     */
    public function getTimeTables(){
        try {
            $date_from = null;
            if ($this->param_date_from == "" || $this->param_date_from == null) {
                $date_from = new Carbon();
            } else {
                $date_from = new Carbon($this->param_date_from);
            }
            $target_date = $date_from->format('Ymd');
            $apicommon = new ApiCommonController();
            // working_timetablesの最大適用開始日付subquery
            $subquery2 = $apicommon->makeWorkingTimeTableApplyTermSql($target_date);
            // t2:working_timetables
            $sqlString = "";
            $sqlString .= $subquery2;
            $sqlString .= "  and ";
            $sqlString .= "  not exists ( ";
            $sqlString .= "    select ";
            $sqlString .= "      'X' ";
            $sqlString .= "    from ";
            $sqlString .= "      ".$this->table." as t3 ";
            $sqlString .= "    where ";
            $sqlString .= "      t1.ago_time_no = ? ";
            $sqlString .= "  ) ";
            $sqlString .= "  and t1.working_time_kubun = ? ";
            $sqlString .= " order by ";
            $sqlString .= "   t1.no ";
            // バインド
            $array_setBindingsStr = array();
            // subquery2
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = $target_date;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = Config::get('const.C999_NAME.from_timetable_no');
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = Config::get('const.C999_NAME.link_end_timetable_no');
            $array_setBindingsStr[] = Config::get('const.C004.out_of_regular_night_working_time');
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
                'ago_time_no' => $this->ago_time_no,
                'created_user' => $this->created_user,
                'created_at' => $this->created_at,
            ]);
    
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
                    'ago_time_no' => $this->ago_time_no,
                    'updated_user' => $this->updated_user,
                    'updated_at' => $this->updated_at,
                ]
            );
            return true;
    
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
     * 詳細取得
     *
     * @return void
     */
    public function getDetail(){
        // 適用期間日付の取得
        $dt = new Carbon();
        if (isset($this->param_apply_term_from)) {
            if ($this->param_apply_term_from != null && $this->param_apply_term_from != "") {
                $dt = new Carbon($this->param_apply_term_from);
            }
        }
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
                ->addselect('t1.ago_time_no')
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
                ->orderBy('t1.working_time_kubun', 'asc')
                ->orderBy('t1.id', 'asc')
                ->get();
            //                 ->whereNotNull('from_time')
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    
        return $results;
    }

    /**
     * 詳細取得（時間の丸め用）
     *
     * @return void
     */
    public function getWorkingTimeTableRound(){
        try {
            $sqlString = "";
            $sqlString .= "select ";
            $sqlString .= "  t1.id ";
            $sqlString .= "  , t1.no ";
            $sqlString .= "  , t1.name ";
            $sqlString .= "  , t1.working_time_kubun ";
            $sqlString .= "  , DATE_FORMAT(t1.from_time, '%H%i%s') as from_time ";
            $sqlString .= "  , DATE_FORMAT(t1.to_time, '%H%i%s') as to_time ";
            $sqlString .= "  , t1.ago_time_no ";
            $sqlString .= "from ";
            $sqlString .= $this->table." as t1 ";
            $sqlString .= "  inner join ( ";
            $sqlString .= "    select ";
            $sqlString .= "      no as no ";
            $sqlString .= "      , MAX(apply_term_from) as max_apply_term_from ";
            $sqlString .= "    from ";
            $sqlString .= $this->table;
            $sqlString .= "    where ";
            $sqlString .= "      apply_term_from <= ? ";
            $sqlString .= "      and is_deleted = ? ";
            $sqlString .= "    group by ";
            $sqlString .= "      no ";
            $sqlString .= "   ) as t2 ";
            $sqlString .= " on t2.no = t1.no ";
            $sqlString .= " and t2.max_apply_term_from = t1.apply_term_from ";
            $sqlString .= "where ";
            $sqlString .= "  t1.no < ? ";
            $sqlString .= "  and t1.working_time_kubun in (?,?) ";
            $sqlString .= "  and t1.from_time is not null ";
            $sqlString .= "  and t1.is_deleted = ? ";
            $sqlString .= "order by ";
            $sqlString .= "  t1.no asc ";
            $sqlString .= "  , t1.working_time_kubun asc ";
            $sqlString .= "  , t1.from_time asc ";
            // バインド
            $array_setBindingsStr = array();
            $array_setBindingsStr[] = $this->param_date_from;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = Config::get('const.C999_NAME.from_timetable_no');
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 2;
            $array_setBindingsStr[] = 0;
            $results = DB::select($sqlString, $array_setBindingsStr);

        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
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
                    't1.to_time as to_time',
                    't1.ago_time_no as ago_time_no'
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
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_error')).'$e');
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

    /**
     * 該当日付のタイムテーブル取得
     *  日付/部署/ユーザー  必須
     * 
     * @return void
     */
    public function getWorkingTimeTable(){
        try {
            //
            $dt = $this->param_date_from;
            $dt1 = new Carbon($dt);
            $target_today = $dt1->format('Y-m-d');
            $target_addtoday = $dt1->addDay()->format('Y-m-d');
   
            $apicommon = new ApiCommonController();
            // usersの最大適用開始日付subquery
            $subquery31 = $apicommon->makeUserApplyTermSql($this->param_date_to, Config::get('const.C025.admin_user'));
            // departmentsの最大適用開始日付subquery
            $subquery32 = $apicommon->makeDepartmentApplyTermSql($this->param_date_to, $this->param_date_to);
            // working_timetablesの最大適用開始日付subquery
            $subquery33 = $apicommon->makeWorkingTimeTableApplyTermSql($this->param_date_to);
            // shift_informationsの最大適用開始日付subquery
            $subquery34 = "";
            $subquery34 .= " select ";
            $subquery34 .= "   t1.date ";
            $subquery34 .= "   ,t1.user_code as user_code ";
            $subquery34 .= "   ,t1.department_code as department_code ";
            $subquery34 .= "   ,t1.working_timetable_no as working_timetable_no ";
            $subquery34 .= "   ,t1.is_deleted as is_deleted ";
            $subquery34 .= " from ";
            $subquery34 .= "   ".$this->table_calendar_setting_informations." as t1 ";

            // t33:working_timetables
            // t34:shift_informations
            // t35:shift_informationsに紐づくworking_timetables
            $sqlString = "";
            $sqlString .= " select ";
            $sqlString .= "   t1.code as user_code ";
            $sqlString .= "   ,t1.name as user_name ";
            $sqlString .= "   ,t32.code as department_code ";
            $sqlString .= "   ,t32.name as department_name ";
            $sqlString .= "   ,t34.working_timetable_no as working_timetable_no ";
            $sqlString .= "   ,t35.working_time_kubun as working_time_kubun ";
            $sqlString .= "   ,t35.name  as working_timetable_name ";
            $sqlString .= "   ,concat(?, ' ', t35.from_time) as working_timetable_from_record_time ";
            $sqlString .= "   ,date_format(t35.from_time, '%H:%i') as working_timetable_from_time ";
            $sqlString .= "   ,case t35.to_time < t35.from_time ";
            $sqlString .= "      when true then concat(?, ' ', t35.to_time)";
            $sqlString .= "      else concat(?, ' ', t35.to_time)";
            $sqlString .= "    end as working_timetable_to_record_time ";
            $sqlString .= "   ,date_format(t35.to_time, '%H:%i') as working_timetable_to_time ";
            $sqlString .= " from ";
            $sqlString .= "   ".$this->table_users." as t1 ";
            $sqlString .= "   inner join ( ";
            $sqlString .= "   ".$subquery31. " ";
            $sqlString .= "   ) as t31 ";
            $sqlString .= "   on t31.code = t1.code ";
            $sqlString .= "   and t31.max_apply_term_from = t1.apply_term_from ";
            $sqlString .= "   left join ( ";
            $sqlString .= "   ".$subquery32. " ";
            $sqlString .= "   ) as t32 ";
            $sqlString .= "   on t32.code = t1.department_code ";
            $sqlString .= "   left join ( ";
            $sqlString .= "   ".$subquery34. " ";
            $sqlString .= "   ) as t34 ";
            $sqlString .= "   on t34.user_code = t1.code ";
            $sqlString .= "   and t34.department_code = t1.department_code ";
            $sqlString .= "   and t34.date = ? ";
            $sqlString .= "   and t34.is_deleted = ? ";
            $sqlString .= "   left join ( ";
            $sqlString .= "   ".$subquery33. " ";
            $sqlString .= "   ) as t35 ";
            $sqlString .= "   on t35.no = t34.working_timetable_no ";
            $sqlString .= " where ";
            $sqlString .= "   ? = ? ";
            $sqlString .= "   and t1.kill_from_date >= ? ";
            $sqlString .= "   and t1.department_code = ? ";
            $sqlString .= "   and t1.code = ? ";
            $sqlString .= "   and t1.is_deleted = ? ";
        
            // バインド
            $array_setBindingsStr = array();
            //
            $array_setBindingsStr[] = $target_today;
            $array_setBindingsStr[] = $target_addtoday;
            $array_setBindingsStr[] = $target_today;
            // subquery31
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = $this->param_date_to;
            $array_setBindingsStr[] = Config::get('const.C025.admin_user');
            $array_setBindingsStr[] = 0;
            // subquery32
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = $this->param_date_to;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = $this->param_date_to;
            $array_setBindingsStr[] = 0;
            // subquery34
            $array_setBindingsStr[] = $this->param_date_from;
            $array_setBindingsStr[] = 0;
            // subquery33
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = $this->param_date_to;
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = Config::get('const.C999_NAME.from_timetable_no');
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;

            $array_setBindingsStr[] = $this->param_date_to;
            $array_setBindingsStr[] = $this->param_department_code;
            $array_setBindingsStr[] = $this->param_user_code;
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
     * 該当日付のタイムテーブル取得
     *  日付/部署/ユーザー  必須
     * 
     * @return void
     */
    // public function getWorkingTimeTable(){
    //     try {
    //         //
    //         $dt = $this->param_date_from;
    //         $dt1 = new Carbon($dt);
    //         $target_today = $dt1->format('Y-m-d');
    //         $target_addtoday = $dt1->addDay()->format('Y-m-d');
   
    //         $apicommon = new ApiCommonController();
    //         // usersの最大適用開始日付subquery
    //         $subquery31 = $apicommon->makeUserApplyTermSql($this->param_date_to, Config::get('const.C025.admin_user'));
    //         // departmentsの最大適用開始日付subquery
    //         $subquery32 = $apicommon->makeDepartmentApplyTermSql($this->param_date_to, $this->param_date_to);
    //         // working_timetablesの最大適用開始日付subquery
    //         $subquery33 = $apicommon->makeWorkingTimeTableApplyTermSql($this->param_date_to);
    //         // shift_informationsの最大適用開始日付subquery
    //         $subquery34 = "";
    //         $subquery34 .= " select ";
    //         $subquery34 .= "   t1.target_date ";
    //         $subquery34 .= "   ,t1.user_code as user_code ";
    //         $subquery34 .= "   ,t1.department_code as department_code ";
    //         $subquery34 .= "   ,t1.working_timetable_no as working_timetable_no ";
    //         $subquery34 .= "   ,t1.is_deleted as is_deleted ";
    //         $subquery34 .= " from ";
    //         $subquery34 .= "   ".$this->table_shift_informations." as t1 ";

    //         // t33:working_timetables
    //         // t34:shift_informations
    //         // t35:shift_informationsに紐づくworking_timetables
    //         $sqlString = "";
    //         $sqlString .= " select ";
    //         $sqlString .= "   t1.code as user_code ";
    //         $sqlString .= "   ,t1.name as user_name ";
    //         $sqlString .= "   ,t32.code as department_code ";
    //         $sqlString .= "   ,t32.name as department_name ";
    //         $sqlString .= "   ,case ifnull(t34.working_timetable_no,0) ";
    //         $sqlString .= "     when 0 then t33.no ";
    //         $sqlString .= "     else t35.no ";
    //         $sqlString .= "    end as working_timetable_no ";
    //         $sqlString .= "   ,case ifnull(t34.working_timetable_no, 0) ";
    //         $sqlString .= "     when 0 then t33.working_time_kubun ";
    //         $sqlString .= "     else t35.working_time_kubun ";
    //         $sqlString .= "     end as working_time_kubun ";
    //         $sqlString .= "   ,case ifnull(t34.working_timetable_no,0) ";
    //         $sqlString .= "     when 0 then t33.name ";
    //         $sqlString .= "     else t35.name ";
    //         $sqlString .= "    end as working_timetable_name ";
    //         $sqlString .= "   ,case ifnull(t34.working_timetable_no,0) ";
    //         $sqlString .= "     when 0 then concat(?, ' ', t33.from_time) ";
    //         $sqlString .= "     else concat(?, ' ', t35.from_time) ";
    //         $sqlString .= "    end as working_timetable_from_record_time ";
    //         $sqlString .= "   ,case ifnull(t34.working_timetable_no,0) ";
    //         $sqlString .= "      when 0 then date_format(t33.from_time, '%H:%i') ";
    //         $sqlString .= "      else date_format(t35.from_time, '%H:%i') ";
    //         $sqlString .= "    end as working_timetable_from_time ";
    //         $sqlString .= "   ,case ifnull(t34.working_timetable_no,0) ";
    //         $sqlString .= "     when 0 then ";
    //         $sqlString .= "       case t33.to_time < t33.from_time ";
    //         $sqlString .= "         when true then concat(?, ' ', t33.to_time)";
    //         $sqlString .= "         else concat(?, ' ', t33.to_time)";
    //         $sqlString .= "       end ";
    //         $sqlString .= "     else ";
    //         $sqlString .= "       case t35.to_time < t35.from_time ";
    //         $sqlString .= "         when true then concat(?, ' ', t35.to_time)";
    //         $sqlString .= "         else concat(?, ' ', t35.to_time)";
    //         $sqlString .= "       end ";
    //         $sqlString .= "    end as working_timetable_to_record_time ";
    //         $sqlString .= "   ,case ifnull(t34.working_timetable_no,0) ";
    //         $sqlString .= "      when 0 then date_format(t33.to_time, '%H:%i') ";
    //         $sqlString .= "      else date_format(t35.to_time, '%H:%i') ";
    //         $sqlString .= "    end as working_timetable_to_time ";
    //         $sqlString .= " from ";
    //         $sqlString .= "   ".$this->table_users." as t1 ";
    //         $sqlString .= "   inner join ( ";
    //         $sqlString .= "   ".$subquery31. " ";
    //         $sqlString .= "   ) as t31 ";
    //         $sqlString .= "   on t31.code = t1.code ";
    //         $sqlString .= "   and t31.max_apply_term_from = t1.apply_term_from ";
    //         $sqlString .= "   left join ( ";
    //         $sqlString .= "   ".$subquery32. " ";
    //         $sqlString .= "   ) as t32 ";
    //         $sqlString .= "   on t32.code = t1.department_code ";
    //         $sqlString .= "   left join ( ";
    //         $sqlString .= "   ".$subquery33. " ";
    //         $sqlString .= "   ) as t33 ";
    //         $sqlString .= "   on t33.no = t1.working_timetable_no ";
    //         $sqlString .= "   left join ( ";
    //         $sqlString .= "   ".$subquery34. " ";
    //         $sqlString .= "   ) as t34 ";
    //         $sqlString .= "   on t34.user_code = t1.code ";
    //         $sqlString .= "   and t34.department_code = t1.department_code ";
    //         $sqlString .= "   and t34.target_date = ? ";
    //         $sqlString .= "   and t34.is_deleted = ? ";
    //         $sqlString .= "   left join ( ";
    //         $sqlString .= "   ".$subquery33. " ";
    //         $sqlString .= "   ) as t35 ";
    //         $sqlString .= "   on t35.no = t34.working_timetable_no ";
    //         $sqlString .= "   and t35.working_time_kubun = t33.working_time_kubun ";
    //         $sqlString .= "   and t35.from_time = t33.from_time ";
    //         $sqlString .= " where ";
    //         $sqlString .= "   ? = ? ";
    //         $sqlString .= "   and t1.kill_from_date >= ? ";
    //         $sqlString .= "   and t1.department_code = ? ";
    //         $sqlString .= "   and t1.code = ? ";
    //         $sqlString .= "   and t1.is_deleted = ? ";
        
    //         // バインド
    //         $array_setBindingsStr = array();
    //         //
    //         $array_setBindingsStr[] = $target_today;
    //         $array_setBindingsStr[] = $target_today;
    //         $array_setBindingsStr[] = $target_addtoday;
    //         $array_setBindingsStr[] = $target_today;
    //         $array_setBindingsStr[] = $target_addtoday;
    //         $array_setBindingsStr[] = $target_today;
    //         // subquery31
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = $this->param_date_to;
    //         $array_setBindingsStr[] = Config::get('const.C025.admin_user');
    //         $array_setBindingsStr[] = 0;
    //         // subquery32
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = $this->param_date_to;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = $this->param_date_to;
    //         $array_setBindingsStr[] = 0;
    //         // subquery33
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = $this->param_date_to;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = 9999;
    //         $array_setBindingsStr[] = 0;
    //         // subquery34
    //         //
    //         $array_setBindingsStr[] = $this->param_date_from;
    //         $array_setBindingsStr[] = 0;
    //         // subquery33
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = $this->param_date_to;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = 9999;
    //         $array_setBindingsStr[] = 0;
    //         $array_setBindingsStr[] = 1;
    //         $array_setBindingsStr[] = 1;

    //         $array_setBindingsStr[] = $this->param_date_to;
    //         $array_setBindingsStr[] = $this->param_department_code;
    //         $array_setBindingsStr[] = $this->param_user_code;
    //         $array_setBindingsStr[] = 0;
    //         $result = DB::select($sqlString, $array_setBindingsStr);
        
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

    /**
     * タイムテーブル編集
     * @return void
     */
    public function edtWorkingTime(){
        try {
            //
            $dt = $this->param_date_from;
            $dt1 = new Carbon($dt);
            $target_today = $dt1->format('Y-m-d');
            $target_addtoday = $dt1->addDay()->format('Y-m-d');
   
            $apicommon = new ApiCommonController();
            // working_timetablesの最大適用開始日付subquery
            $subquery33 = $apicommon->makeWorkingTimeTableApplyTermSql($this->param_date_from);

            $sqlString = $subquery33;
            if (!empty($this->param_no)) {
                $sqlString .= "   and t1.no = ? ";
            }
            $sqlString .= "order by t1.no asc ";
            $sqlString .= ", t1.working_time_kubun asc ";
        
            // バインド
            $array_setBindingsStr = array();
            //
            // subquery33
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            if (!empty($this->param_date_from)) {
                $array_setBindingsStr[] = $this->param_date_from;
            }
            $array_setBindingsStr[] = 0;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = Config::get('const.C999_NAME.from_timetable_no');
            $array_setBindingsStr[] = 0;
            if (!empty($this->param_no)) {
                $array_setBindingsStr[] = $this->param_no;
            }
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

}
