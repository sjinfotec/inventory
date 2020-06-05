<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\ApiCommonController;
use Carbon\Carbon;

class AttendanceLog extends Model
{
    protected $table = 'attendance_logs';
    protected $table_users = 'users';
    protected $table_work_times = 'work_times';
    protected $table_generalcodes = 'generalcodes';

    private $id;                                // id
    private $department_code;                   // 部署コード
    private $employment_status;                 // 雇用形態
    private $user_code;                         // ユーザー
    private $working_date;                      // 日付
    private $event_mode;                        // ＰＣイベントモード
    private $event_time;                        // ＰＣイベント時間
    private $difference_reason;                 // 差異理由
    private $created_user;    
    private $updated_user;                  
    private $created_at;                  
    private $updated_at;                  
    private $is_deleted;                  

    // id
    public function getIdAttribute()
    {
        return $this->id;
    }

    public function setIdAttribute($value)
    {
        $this->id = $value;
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

    // ユーザー
    public function getUsercodeAttribute()
    {
        return $this->user_code;
    }

    public function setUsercodeAttribute($value)
    {
        $this->user_code = $value;
    }

    // 日付
    public function getWorkingdateAttribute()
    {
        return $this->working_date;
    }

    public function setWorkingdateAttribute($value)
    {
        $this->working_date = $value;
    }

    // ＰＣイベントモード
    public function getEventmodeAttribute()
    {
        return $this->event_mode;
    }

    public function setEventmodeAttribute($value)
    {
        $this->event_mode = $value;
    }


    // ＰＣイベント時間
    public function getEventtimeAttribute()
    {
        return $this->event_time;
    }

    public function setEventtimeAttribute($value)
    {
        $this->event_time = $value;
    }

    // 差異理由
    public function getDifferencereasonAttribute()
    {
        return $this->difference_reason;
    }

    public function setDifferencereasonAttribute($value)
    {
        $this->difference_reason = $value;
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
     
    // 作成時間
    public function getCreatedatAttribute()
    {
        return $this->created_at;
    }
    public function setCreatedatAttribute($value)
    {
        $this->created_at = $value;
    }
     
    // 修正時間
    public function getUpdatedatAttribute()
    {
        return $this->updated_at;
    }
    public function setUpdatedatAttribute($value)
    {
        $this->updated_at = $value;
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

    // ------------- implements --------------

    private $param_id;                          // id
    private $param_department_code;             // 部署コード
    private $param_employment_status;           // 雇用形態
    private $param_user_code;                   // ユーザー
    private $param_working_date;                // 日付
    private $param_event_mode;                  // ＰＣイベントモード
    private $param_event_time;                  // ＰＣイベント時間
    private $param_working_date_from;           // 開始日付
    private $param_working_date_to;             // 終了日付

    // id
    public function getParamidAttribute()
    {
        return $this->param_id;
    }

    public function setParamidAttribute($value)
    {
        $this->param_id = $value;
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

    // 雇用形態
    public function getParamemploymentstatusAttribute()
    {
        return $this->param_employment_status;
    }

    public function setParamemploymentstatusAttribute($value)
    {
        $this->param_employment_status = $value;
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


    // 日付
    public function getParamworkingdateAttribute()
    {
        return $this->param_working_date;
    }

    public function setParamworkingdateAttribute($value)
    {
        $this->param_working_date = $value;
    }


    // 開始日付
    public function getParamworkingdatefromAttribute()
    {
        return $this->param_working_date_from;
    }

    public function setParamworkingdatefromAttribute($value)
    {
        $this->param_working_date_from = $value;
    }


    // 終了日付
    public function getParamworkingdatetoAttribute()
    {
        return $this->param_working_date_to;
    }

    public function setParamworkingdatetoAttribute($value)
    {
        $this->param_working_date_to = $value;
    }

    // ＰＣイベントモード
    public function getParameventmodeAttribute()
    {
        return $this->param_event_mode;
    }

    public function setParameventmodeAttribute($value)
    {
        $this->param_event_mode = $value;
    }

    // ＰＣイベント時間
    public function getParameventtimeAttribute()
    {
        return $this->param_event_time;
    }

    public function setParameventtimeAttribute($value)
    {
        $this->param_event_time = $value;
    }

    /**
     * 検索
     *
     * @return void
     */
    public function getAttendanceLogList($targetdate){

        try {
            $dt = null;
            if (isset($targetdate)) {
                $dt = new Carbon($targetdate);
            } else {
                $dt = new Carbon();
            }
            $targetdate = $dt->format('Ymd');
            // 適用期間日付の取得
            $apicommon = new ApiCommonController();
            // usersの最大適用開始日付subquery
            $subquery3 = $apicommon->getUserApplyTermSubquery($targetdate);
            // departmentsの最大適用開始日付subquery
            $subquery4 = $apicommon->getDepartmentApplyTermSubquery($targetdate);

            // ---------------- unionquery1 打刻 ----------------------------
            // unionquery1    work_times
            $unionquery1 = DB::table($this->table_users.' AS t1')
                ->selectRaw("null as id");
            $unionquery1
                ->addSelect('t1.department_code as department_code')
                ->addSelect('t4.name as department_name')
                ->addSelect('t1.employment_status as employment_status')
                ->addSelect('t5.code_name as employment_name')
                ->addSelect('t1.code as user_code')
                ->addSelect('t1.name as user_name')
                ->addSelect('t2.mode as mode')
                ->addSelect('t2.record_time as record_time')
                ->addSelect('t1.management as user_management');
            $unionquery1
                ->selectRaw("DATE_FORMAT(t2.record_time, '%Y%m%d') as working_date")
                ->selectRaw("null as difference_reason");
            $unionquery1
                ->join($this->table_work_times.' as t2', function ($join) { 
                    $join->on('t2.user_code', '=', 't1.code');
                    $join->on('t2.department_code', '=', 't1.department_code')
                    ->where('t2.is_deleted', '=', 0);
                });
            $unionquery1
                ->JoinSub($subquery3, 't3', function ($join) { 
                    $join->on('t3.code', '=', 't1.code');
                    $join->on('t3.max_apply_term_from', '=', 't1.apply_term_from');
                });
            $unionquery1
                ->JoinSub($subquery4, 't4', function ($join) { 
                    $join->on('t4.code', '=', 't1.department_code');
                });
            $unionquery1
                ->leftJoin($this->table_generalcodes.' as t5', function ($join) { 
                    $join->on('t5.code', '=', 't1.employment_status')
                    ->where('t5.identification_id', '=', Config::get('const.C001.value'))
                    ->where('t5.is_deleted', '=', 0);
                });

            // ---------------- unionquery2 イベント ----------------------------
            // unionquery2    attendance_logs
            $unionquery2 = DB::table($this->table_users.' AS t1')
                ->select(
                    't2.id',
                    't1.department_code as department_code',
                    't4.name as department_name',
                    't1.employment_status as employment_status',
                    't5.code_name as employment_name',
                    't1.code as user_code',
                    't1.name as user_name',
                    't2.event_mode as mode',
                    't2.event_time as record_time',
                    't1.management as user_management',
                    't2.working_date as working_date',
                    't2.difference_reason as difference_reason'
                );
            $unionquery2
                ->join($this->table.' as t2', function ($join) { 
                    $join->on('t2.user_code', '=', 't1.code');
                    $join->on('t2.department_code', '=', 't1.department_code')
                    ->where('t2.is_deleted', '=', 0);
                })
                ->JoinSub($subquery3, 't3', function ($join) { 
                    $join->on('t3.code', '=', 't1.code');
                    $join->on('t3.max_apply_term_from', '=', 't1.apply_term_from');
                })
                ->JoinSub($subquery4, 't4', function ($join) { 
                    $join->on('t4.code', '=', 't1.department_code');
                })
                ->leftJoin($this->table_generalcodes.' as t5', function ($join) { 
                    $join->on('t5.code', '=', 't1.employment_status')
                    ->where('t5.identification_id', '=', Config::get('const.C001.value'))
                    ->where('t5.is_deleted', '=', 0);
                });

            // unionquery1にunionquery2を組み込む
            $unionquery1
                ->union($unionquery2);
            $unionquery1_sql= $unionquery1->toSql();

            // ---------------- mainquery ----------------------------
            $mainquery = DB::table(DB::raw('('.$unionquery1_sql.') as t1'))
                ->select(
                    't1.id',
                    't1.department_code as department_code',
                    't1.department_name as department_name',
                    't1.employment_status as employment_status',
                    't1.employment_name as employment_name',
                    't1.user_code as user_code',
                    't1.user_name as user_name',
                    't1.mode as mode',
                    't1.working_date as working_date',
                    't1.record_time as record_time',
                    't1.difference_reason as difference_reason',
                    't1.user_management as user_management'
                );
            $mainquery
                ->selectRaw("DATE_FORMAT(t1.record_time,'%H:%i') as scan_time")
                ->selectRaw("DATE_FORMAT(t1.working_date, '%m月%d日') as working_date_name");
            if(!empty($this->param_department_code)){
                $mainquery->where('t1.department_code', $this->param_department_code);              //department_code指定
            }
            if(!empty($this->param_employment_status)){
                $mainquery->where('t1.employment_status', $this->param_employment_status);          // 雇用形態指定
            }
            if(!empty($this->param_user_code)){
                $mainquery->where('t1.user_code', $this->param_user_code);                          //user_code指定
            } else {
                $mainquery->where('t1.user_management','<=',Config::get('const.C017.out_of_user'));
            }
            if(!empty($this->param_working_date_from) && !empty($this->param_working_date_to)){
                $mainquery->where('t1.working_date', '>=', $this->param_working_date_from);         // 日付範囲指定
                $mainquery->where('t1.working_date', '<=', $this->param_working_date_to);           // 日付範囲指定
            }

            $mainquery
                ->orderBy('t1.department_code', 'asc')
                ->orderBy('t1.user_code', 'asc')
                ->orderBy('t1.working_date', 'asc')
                ->orderBy('t1.record_time', 'asc');

            $cnt = 0;
            $array_setBindingsStr = array();
            $cnt += 1;
            $array_setBindingsStr[] = array($cnt=>0);
            // 適用期間日付の取得
            $dt = null;
            if (isset($targetdate)) {
                $dt = new Carbon($targetdate);
            } else {
                $dt = new Carbon();
            }
            $target_date = $dt->format('Ymd');
            $cnt += 1;
            $array_setBindingsStr[] = array($cnt=>$target_date);
            $cnt += 1;
            $array_setBindingsStr[] = array($cnt=>Config::get('const.C017.admin_user'));
            $cnt += 1;
            $array_setBindingsStr[] = array($cnt=>0);
            $cnt += 1;
            $array_setBindingsStr[] = array($cnt=>$target_date);
            $cnt += 1;
            $array_setBindingsStr[] = array($cnt=>0);
            $cnt += 1;
            $array_setBindingsStr[] = array($cnt=>$target_date);
            $cnt += 1;
            $array_setBindingsStr[] = array($cnt=>0);
            $cnt += 1;
            $array_setBindingsStr[] = array($cnt=>Config::get('const.C001.value'));
            $cnt += 1;
            $array_setBindingsStr[] = array($cnt=>0);
            $cnt += 1;
            $array_setBindingsStr[] = array($cnt=>0);
            $cnt += 1;
            $array_setBindingsStr[] = array($cnt=>$target_date);
            $cnt += 1;
            $array_setBindingsStr[] = array($cnt=>Config::get('const.C017.admin_user'));
            $cnt += 1;
            $array_setBindingsStr[] = array($cnt=>0);
            $cnt += 1;
            $array_setBindingsStr[] = array($cnt=>$target_date);
            $cnt += 1;
            $array_setBindingsStr[] = array($cnt=>0);
            $cnt += 1;
            $array_setBindingsStr[] = array($cnt=>$target_date);
            $cnt += 1;
            $array_setBindingsStr[] = array($cnt=>0);
            $cnt += 1;
            $array_setBindingsStr[] = array($cnt=>Config::get('const.C001.value'));
            $cnt += 1;
            $array_setBindingsStr[] = array($cnt=>0);

            if(!empty($this->param_department_code)){
                $cnt += 1;
                $array_setBindingsStr[] = array($cnt=>$this->param_department_code);
            }
            if(!empty($this->param_employment_status)){
                $cnt += 1;
                $array_setBindingsStr[] = array($cnt=>$this->param_employment_status);
            }
            if(!empty($this->param_user_code)){
                $cnt += 1;
                $array_setBindingsStr[] = array($cnt=>$this->param_user_code);
            } else {
                $cnt += 1;
                $array_setBindingsStr[] = array($cnt=>Config::get('const.C017.out_of_user'));
            }
            if(!empty($this->param_working_date_from) && !empty($this->param_working_date_to)){
                $cnt += 1;
                $array_setBindingsStr[] = array($cnt=>$this->param_working_date_from);
                $cnt += 1;
                $array_setBindingsStr[] = array($cnt=>$this->param_working_date_to);
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
     * 存在チェック
     *
     * @return boolean
     */
    public function isExist(){
        try {
            $mainquery = DB::table($this->table);
            if (!empty($this->param_department_code)) {
                $mainquery
                    ->where('department_code', '=', $this->param_department_code);
            }
            if (!empty($this->param_employment_status)) {
                $mainquery
                    ->where('employment_status', '=', $this->param_employment_status);
            }
            if (!empty($this->param_user_code)) {
                $mainquery
                    ->where('user_code', '=', $this->param_user_code);
            }
            if(!empty($this->param_working_date_from) && !empty($this->param_working_date_to)){
                $mainquery->where('working_date', '>=', $this->param_working_date_from);         // 日付範囲指定
                $mainquery->where('working_date', '<=', $this->param_working_date_to);           // 日付範囲指定
            }
            if (!empty($this->param_event_mode)) {
                $mainquery
                    ->where('event_mode', '=', $this->param_event_mode);
            }
            if (!empty($this->param_event_time)) {
                $mainquery
                    ->where('event_time', '=', $this->param_event_time);
            }
            $is_exists =$mainquery
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
     * 登録
     *
     * @return void
     */
    public function store(){
        try {
            DB::table($this->table)->insert(
                [
                    'department_code' => $this->department_code,
                    'employment_status' => $this->employment_status,
                    'user_code' => $this->user_code,
                    'working_date' => $this->working_date,
                    'event_mode' => $this->event_mode,
                    'event_time' => $this->event_time,
                    'difference_reason' => $this->difference_reason,
                    'created_user' => $this->created_user,
                    'created_at'=>$this->created_at
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
     * 更新（IDによる差異理由の更新）
     *
     * @return void
     */
    public function updReasonFromID(){
        try {
            DB::table($this->table)
                ->where('id', $this->param_id)
                ->update(
                [
                    'difference_reason' => $this->difference_reason,
                    'updated_user' => $this->updated_user,
                    'updated_at'=>$this->updated_at
                ]
            );
        
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
     * 更新（ＰＣイベントモードとＰＣイベント時間）
     *
     * @return boolean
     */
    public function updateEvent(){
        try {
            $mainquery = DB::table($this->table);
            if (!empty($this->param_department_code)) {
                $mainquery
                    ->where('department_code', '=', $this->param_department_code);
            }
            if (!empty($this->param_employment_status)) {
                $mainquery
                    ->where('employment_status', '=', $this->param_employment_status);
            }
            if (!empty($this->param_user_code)) {
                $mainquery
                    ->where('user_code', '=', $this->param_user_code);
            }
            if(!empty($this->param_working_date)) {
                $mainquery->where('working_date', '=', $this->param_working_date);
            }
            if(!empty($this->param_event_mode)) {
                $mainquery->where('event_mode', '=', $this->param_event_mode);
            }
            if(!empty($this->param_event_time)) {
                $mainquery->where('event_time', '=', $this->param_event_time);
            }
            $array_update = ['event_mode' => $this->event_mode, 'event_time' => $this->event_time ];
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

    /**
     * 物理削除
     *
     * @return boolean
     */
    public function delete(){
        try {
            $mainquery = DB::table($this->table);
            if (!empty($this->param_department_code)) {
                $mainquery
                    ->where('department_code', '=', $this->param_department_code);
            }
            if (!empty($this->param_employment_status)) {
                $mainquery
                    ->where('employment_status', '=', $this->param_employment_status);
            }
            if (!empty($this->param_user_code)) {
                $mainquery
                    ->where('user_code', '=', $this->param_user_code);
            }
            if(!empty($this->param_working_date_from) && !empty($this->param_working_date_to)){
                $mainquery->where('working_date', '>=', $this->param_working_date_from);         // 日付範囲指定
                $mainquery->where('working_date', '<=', $this->param_working_date_to);           // 日付範囲指定
            }
            $is_exists =$mainquery
                ->where('is_deleted',0)
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

        return $is_exists;
    }

    /**
     * 最大登録日付取得
     *
     * @return boolean
     */
    public function getWorkingdate(){
        try {
            $mainquery = DB::table($this->table);
            if (!empty($this->param_department_code)) {
                $mainquery
                    ->where('department_code', '=', $this->param_department_code);
            }
            if (!empty($this->param_employment_status)) {
                $mainquery
                    ->where('employment_status', '=', $this->param_employment_status);
            }
            if (!empty($this->param_user_code)) {
                $mainquery
                    ->where('user_code', '=', $this->param_user_code);
            }
            if(!empty($this->param_working_date_from) && !empty($this->param_working_date_to)){
                $mainquery->where('working_date', '>=', $this->param_working_date_from);         // 日付範囲指定
                $mainquery->where('working_date', '<=', $this->param_working_date_to);           // 日付範囲指定
            }
            $maxdate =$mainquery
                ->where('is_deleted',0)
                ->max('working_date');
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_delete_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_delete_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }

        return $maxdate;
    }
}
