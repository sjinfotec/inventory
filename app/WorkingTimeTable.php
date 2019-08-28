<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;


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
     * セレクト用データ取得
     *
     * @return void
     */
    public function getTimeTables(){
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

        $data = DB::table($this->table)
            ->JoinSub($subquery, 't1', function ($join) { 
                $join->on('t1.no', '=', $this->table.'.no');
                $join->on('t1.max_apply_term_from', '=', $this->table.'.apply_term_from');
            })
            ->select($this->table.'.no',$this->table.'.name',$this->table.'.apply_term_from')
            ->whereNotIn($this->table.'.no', [$code_name])
            ->groupBy($this->table.'.no',$this->table.'.name',$this->table.'.apply_term_from')
            ->orderby($this->table.'.no','asc')
            ->get();
    
        return $data;
    }

    /**
     * 登録(INSERT)
     *
     * @return void
     */
    public function insert(){
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
            ]
        );
    }

    /**
     * 修正(UPDATE)
     *
     * @return void
     */
    public function updateDetail(){
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
    }

    /**
     * 詳細取得
     *
     * @return void
     */
    public function getDetail(){
        $data = DB::table($this->table)
            ->select(
                $this->table.'.id',
                $this->table.'.apply_term_from',
                $this->table.'.no',
                $this->table.'.name',
                $this->table.'.working_time_kubun',
                $this->table.'.from_time',
                $this->table.'.to_time',
                $this->table.'.created_user',
                $this->table.'.updated_user'
            )
            ->where($this->table.'.no', $this->no)
            ->where($this->table.'.is_deleted', 0)
            ->get();

        return $data;
    }

    /**
     * 論理削除
     *
     * @return void
     */
    public function updateIsDelete(){
        DB::table($this->table)
            ->where('no', $this->no)
            ->where('apply_term_from', $this->apply_term_from)
            ->update(['is_deleted' => 1]);
    }

    /**
     * 取得
     *
     * @return void
     */
    public function getWorkingTimeTableJoin(){
        \DB::enableQueryLog();
        // sunquery1    日次タイムレコード
        $sunquery1 = DB::table($this->table_temp_calc_workingtimes)
            ->select(
                $this->table_temp_calc_workingtimes.'.working_timetable_no as working_timetable_no'
            );

            if(!empty($this->param_date_from) && !empty($this->param_date_to)){
                $date = date_create($this->param_date_from);
                $this->param_date_from = $date->format('Ymd');
                $date = date_create($this->param_date_to);
                $this->param_date_to = $date->format('Ymd');
                $sunquery1->where($this->table_temp_calc_workingtimes.'.working_date', '>=', $this->param_date_from);             // 日付範囲指定
                $sunquery1->where($this->table_temp_calc_workingtimes.'.working_date', '<=', $this->param_date_to);               // 日付範囲指定
            }
            if(!empty($this->param_employment_status)){
                $sunquery1->where($this->table_temp_calc_workingtimes.'.employment_status', $this->param_employment_status);      //　雇用形態指定
            }
            if(!empty($this->param_department_code)){
                $sunquery1->where($this->table_temp_calc_workingtimes.'.department_code', $this->param_department_code);          // department_code指定
            }
            if(!empty($this->param_user_code)){
                $sunquery1->where($this->table_temp_calc_workingtimes.'.user_code', $this->param_user_code);                      // user_code指定
            }
            $sunquery1->groupBy($this->table_temp_calc_workingtimes.'.working_timetable_no');

        $mainquery = DB::table($this->table.' AS t1')
            ->select(
                't1.no as no',
                't1.name as name',
                't1.working_time_kubun as working_time_kubun',
                't1.from_time as from_time',
                't1.to_time as to_time'
                )
            ->JoinSub($sunquery1, 't2', function ($join) { 
                $join->on('t2.working_timetable_no', '=', 't1.no');
            });
        
        $results = $mainquery
            ->orderBy('t1.no','asc')
            ->orderBy('t1.working_time_kubun','asc')
            ->get();
        \Log::debug(
            'sql_debug_log',
            [
                'getWorkingTimeTableJoin' => \DB::getQueryLog()
            ]
        );
    
        return $results;
    }

}
