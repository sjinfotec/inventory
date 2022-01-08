<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\ApiCommonController;
use Carbon\Carbon;

class Customer extends Model
{
    protected $table = 'customers';
    //protected $table_users = 'users';
    protected $table_offices = 'offices';
    protected $guarded = array('id');

    private $id;            //
    private $office_code;   // 営業所コード
    private $code;          // 顧客コード
    private $name;          // 顧客名
    private $created_user;  // 作成ユーザー
    private $updated_user;  // 修正ユーザー
    private $created_at;    // 作成日時
    private $updated_at;    // 修正日時
    private $is_deleted;    // 削除フラグ

    // ID
    public function getIdAttribute()
    {
        return $this->id;
    }

    public function setIdAttribute($value)
    {
        $this->id = $value;
    }

    // 営業所コード
    public function getOfficecodeAttribute()
    {
        return $this->office_code;
    }

    public function setOfficecodeAttribute($value)
    {
        $this->office_code = $value;
    }

    // 顧客コード
    public function getCodeAttribute()
    {
        return $this->code;
    }

    public function setCodeAttribute($value)
    {
        $this->code = $value;
    }

    // 顧客名
    public function getNameAttribute()
    {
        return $this->name;
    }

    public function setNameAttribute($value)
    {
        $this->name = $value;
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

    // 作成日時
    public function getCreatedatAttribute()
    {
        return $this->created_at;
    }

    public function setCreatedatAttribute($value)
    {
        $this->created_at = $value;
    }

    // 修正日時
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



    // ---------------- param --------------------------------
    private $param_office_code;   // 営業所コード
    private $param_code;          // 顧客コード
    private $param_name;          // 顧客名


    // 営業所コード
    public function getParamofficecodeAttribute()
    {
        return $this->param_office_code;
    }

    public function setParamofficecodeAttribute($value)
    {
        $this->param_office_code = $value;
    }


    // 顧客コード
    public function getParamcodeAttribute()
    {
        return $this->param_code;
    }

    public function setParamcodeAttribute($value)
    {
        $this->param_code = $value;
    }

    // 顧客名
    public function getParamnameAttribute()
    {
        return $this->param_name;
    }
    
    public function setParamnameAttribute($value)
    {
        $this->param_name = $value;
    }
    

    /**
     * 顧客新規登録
     *
     * @return void
     */
    public function insertNewCustomer(){
        try {
                DB::table($this->table)->insert(
                    [
                        //'id' => $this->id,
                        'office_code' => $this->office_code,
                        'code' => $this->code,
                        'name' => $this->name,
                        'created_user'=> $this->created_user,
                        'created_at'=> $this->created_at
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
     * 顧客編集
     *
     * @return void
     */
    public function updateCustomer(){
        try {
            Log::debug('Customer updateCustomer office_code = '.$this->office_code);
            Log::debug('Customer updateCustomer code = '.$this->code);
            Log::debug('Customer updateCustomer id = '.$this->id);
            DB::table($this->table)
            ->where('id', $this->id)
            ->update([
                'id' => $this->id,
                'office_code' => $this->office_code,
                'code' => $this->code,
                'name' => $this->name,
                'updated_user'=>$this->updated_user,
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
     * 取得
     *
     * @return void
     */
    public function getCustomerDetails(){
        $dt = new Carbon();
        $target_date = $dt->format('Ymd');
        try {
/*
            $subquery
                ->where('is_deleted', '=', 0)
                ->groupBy('code');
*/                
            $mainquery = DB::table($this->table.' AS t1')
                ->select(
                    't1.id',
                    't1.office_code',
                    't1.code',
                    't1.name'
                );
            if (!empty($this->code)) {
                $mainquery
                    ->where('t1.code', $this->code);
            }
            $results = $mainquery
                ->where('t1.office_code', $this->office_code)
                ->where('t1.is_deleted', 0)
                ->orderBy('t1.code', 'desc')
                ->get();

            return $results;
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











     public function getDetails(){
        $details = new Collection();    //リスト形式でデータを格納
        try {
/*
            $subquery = DB::table($this->table)
                ->select('code as code');
            $subquery
                ->where('office_code', '=', $this->param_office_code)
                ->where('code', '=', $this->param_code)
                ->where('is_deleted', '=', 0)
                ->groupBy('code');
*/
            $case_sql1 = "CASE t1.kill_from_date = ".Config::get('const.INIT_DATE.maxdate');
            $case_sql1 = $case_sql1." WHEN TRUE THEN NULL ELSE DATE_FORMAT(t1.kill_from_date, '%Y-%m-%d') END as kill_from_date";
            $case_sql2 = "CASE t2.max_apply_term_from = t1.apply_term_from ";
            $case_sql2 = $case_sql2." WHEN TRUE THEN 1";
            $case_sql2 = $case_sql2." ELSE CASE t2.max_apply_term_from < t1.apply_term_from ";
            $case_sql2 = $case_sql2."      WHEN TRUE THEN 2 ELSE 0 END ";
            $case_sql2 = $case_sql2." END  as result";
            $case_sql1 = "";
            $case_sql2 = "";
            $mainquery = DB::table($this->table.' AS t1')
                ->select(
                    't1.id as id', 't1.office_code as office_code', 't1.code as code', 't1.name as name')
                ->selectRaw($case_sql1)
                ->selectRaw($case_sql2);
            if(!empty($this->param_code)){
                $mainquery->where('t1.code', $this->param_code);
            }
            $details = $mainquery
                ->where('office_code', '=', $this->param_office_code)
                ->where('code', '=', $this->param_code)
                ->where('t1.is_deleted', 0)
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
        return $details;
    }

    /**
     * 同CODEチェック
     *
     * @return boolean
     */
    public function isExistsCode(){
        try {
            $is_exists = DB::table($this->table)
                ->where('code',$this->code)
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
     * 同名チェック
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
     * 新規追加用顧客コード取得
     *
     * @return boolean
     */
    public function isNewCode(){
        try {
            $is_newcode = DB::table($this->table)
                ->select('MAX(code) AS max_code')
                ->where('office_code',$this->office_code)
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

        return $is_newcode;
    }




    /**
     * 営業所追加
     *
     * @return void
     */
    public function insertOffice(){
        try {
            DB::table($this->table)->insert(
                [
                    'code' => $this->code,
                    'name' => $this->name,
                    'created_user'=>$this->created_user,
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
     * 営業所更新
     *
     * @return void
     */
    public function updateOffice(){
        try {
            DB::table($this->table)
                ->where('id', $this->id)
                ->where('is_deleted', 0)
                ->update([
                    'name' => $this->name,
                    'updated_at'=>$this->updated_at,
                    'updated_user'=>$this->updated_user
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
     * 最大コード取得
     *
     * @return max_code
     */
    public function getMaxCode($value){
        Log::debug('Customer getMaxCode office_code = '.$value);
        try {
            $max_code = DB::select($this->maxCodeSql($value));
            if(isset($max_code[0]->{'max_code'})){
                $max_code = $max_code[0]->{'max_code'};
                Log::debug('Customer getMaxCode max_code = '.$max_code);
            }else{
                $max_code = 0;
            }
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_maxget_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_maxget_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
        return $max_code;
    }

    private function maxCodeSql($value){
        $sql = "select";
        $sql .= " max(code) as max_code";
        $sql .= " from";
        $sql .= " customers";
        $sql .= " where";
        $sql .= " office_code = '".$value."'";
        //$sql .= " and is_deleted = 0";

        return $sql;
    }

}
