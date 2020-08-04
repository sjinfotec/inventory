<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;

class GeneralCodes extends Model
{
    protected $table = 'generalcodes';
    protected $guarded = array('id');

    //--------------- メンバー属性 -----------------------------------

    private $id;
    private $identification_id;             // 識別
    private $code;                          // コード
    private $sort_seq;                      // 並び順
    private $identification_name;           // 識別名
    private $description;                   // 説明
    private $physical_name;                 // 物理名称
    private $code_name;                     // 項目名
    private $secound_code_name;             // 項目名略称
    private $use_free_item;                 // 用途フリー項目
    private $created_user;                  // 作成ユーザー
    private $updated_user;                  // 修正ユーザー
    private $created_at;                    // 作成日時
    private $updated_at;                    // 修正日時
    private $is_deleted;                    // 削除フラグ


    // ID
    public function getIdAttribute()
    {
        return $this->id;
    }

    public function setIdAttribute($value)
    {
        $this->id = $value;
    }

    // 識別
    public function getIdentificationidAttribute()
    {
        return $this->identification_id;
    }

    public function setIdentificationidAttribute($value)
    {
        $this->identification_id = $value;
    }


    // コード
    public function getCodeAttribute()
    {
        return $this->code;
    }

    public function setCodeAttribute($value)
    {
        $this->code = $value;
    }


    // 並び順
    public function getSortseqAttribute()
    {
        return $this->sort_seq;
    }

    public function setSortseqAttribute($value)
    {
        $this->sort_seq = $value;
    }


    // 識別名
    public function getIdentificationnameAttribute()
    {
        return $this->identification_name;
    }

    public function setIdentificationnameAttribute($value)
    {
        $this->identification_name = $value;
    }


    // 説明
    public function getDescriptionAttribute()
    {
        return $this->description;
    }

    public function setDescriptionAttribute($value)
    {
        $this->description = $value;
    }


    // 物理名称
    public function getPhysicalnameAttribute()
    {
        return $this->physical_name;
    }

    public function setPhysicalnameAttribute($value)
    {
        $this->physical_name = $value;
    }


    // 項目名
    public function getCodenameAttribute()
    {
        return $this->code_name;
    }

    public function setCodenameAttribute($value)
    {
        $this->code_name = $value;
    }


    // 項目名
    public function getSecoundcodenameAttribute()
    {
        return $this->secound_code_name;
    }

    public function setSecoundcodenameAttribute($value)
    {
        $this->secound_code_name = $value;
    }


    // 項目名
    public function getUsefreeitemAttribute()
    {
        return $this->use_free_item;
    }

    public function setUsefreeitemAttribute($value)
    {
        $this->use_free_item = $value;
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


    // 削除フラグ
    public function getIsdeletedAttribute()
    {
        return $this->is_deleted;
    }

    public function setIsdeletedAttribute($value)
    {
        $this->is_deleted = $value;
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

    //--------------- パラメータ項目属性 -----------------------------------

    private $param_identification_id;           // 識別
    private $param_array_identification_id;     // 識別（array）
    private $param_code;                        // コード
    private $param_code_name;                   // コード名称


    // 識別
    public function getParamidentificationidAttribute()
    {
        return $this->param_identification_id;
    }

    public function setParamidentificationidAttribute($value)
    {
        $this->param_identification_id = $value;
    }

    // 識別（array）
    public function getParamarrayidentificationidAttribute()
    {
        return $this->param_array_identification_id;
    }

    public function setParamarrayidentificationidAttribute($value)
    {
        $this->param_array_identification_id = $value;
    }

    // コード
    public function getParamcodeAttribute()
    {
        return $this->param_code;
    }

    public function setParamcodeAttribute($value)
    {
        $this->param_code = $value;
    }

    // コード名称
    public function getParamcodenameAttribute()
    {
        return $this->param_code_name;
    }

    public function setParamcodenameAttribute($value)
    {
        $this->param_code_name = $value;
    }

    //--------------- コード値属性 -----------------------------------

    private $codes;           // 全コード取得結果

    // 全コード取得結果
    public function getGeneralcodes()
    {
        return $this->codes;
    }

    public function setGeneralcodes($value)
    {
        $this->codes = $value;
    }

    // --------------------- メソッド ------------------------------------------------------


    /**
     * コンストラクタ
     *
     */
    public function __construct() {

        try {
            $this->codes = $this->getGeneralcode();
        }catch(\PDOException $pe){
            $this->codes = new Collection();
        }catch(\Exception $e){
            $this->codes = new Collection();
        }

    }

    /**
     * コード取得
     *
     * @return 取得結果
     */
    public function getGeneralcode(){
        try {
            $data = DB::table($this->table)
                ->select(
                    $this->table.'.id',
                    $this->table.'.identification_id as identification_id',
                    $this->table.'.code as code',
                    $this->table.'.sort_seq as sort_seq',
                    $this->table.'.identification_name as identification_name',
                    $this->table.'.description as description',
                    $this->table.'.physical_name as physical_name',
                    $this->table.'.code_name as code_name',
                    $this->table.'.secound_code_name as secound_code_name',
                    $this->table.'.use_free_item as use_free_item',
                    $this->table.'.is_deleted as is_deleted'
                );
            if (isset($this->param_identification_id)) {
                $data->where($this->table.'.identification_id',$this->param_identification_id);
            } else {
                if (isset($this->param_array_identification_id)) {
                    $data->whereIn($this->table.'.identification_id',$this->param_array_identification_id);
                }
            }
            if (isset($this->param_code)) {
                $data->where($this->table.'.code',$this->param_code);
            }
            $result = $data->where($this->table.'.is_deleted',0)
                ->orderBy($this->table.'.identification_id', 'asc')
                ->orderBy($this->table.'.sort_seq', 'asc')
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
        
        return $result;
    }

    /**
     * コード名称チェック
     *
     * @return boolean
     */
    public function isExistsName(){
        try {
            $is_exists = DB::table($this->table)
                ->where('identification_id',$this->param_identification_id)
                ->where('code_name',$this->param_code_name)
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
     * 最大コード取得
     *
     * @return max_code
     */
    public function getMaxCode(){
        try {
            $max_code = DB::select($this->maxCodeSql());
            if(isset($max_code[0]->{'max_code'})){
                $max_code = $max_code[0]->{'max_code'};
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

    private function maxCodeSql(){
        $sql = "select";
        $sql .= " max(CAST(code AS SIGNED)) as max_code";
        $sql .= " from";
        $sql .= " ".$this->table;
        $sql .= " where";
        $sql .= "   identification_id = '".$this->param_identification_id."' ";
        $sql .= "   and is_deleted = 0";

        return $sql;
    }

    /**
     * 最大seq取得
     *
     * @return max_seq
     */
    public function getMaxSeq(){
        try {
            $max_seq = DB::select($this->maxSeqSql());
            if(isset($max_seq[0]->{'max_seq'})){
                $max_seq = $max_seq[0]->{'max_seq'};
            }else{
                $max_seq = 0;
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
        return $max_seq;
    }

    private function maxSeqSql(){
        $sql = "select";
        $sql .= " max(CAST(sort_seq AS SIGNED)) as max_seq";
        $sql .= " from";
        $sql .= " ".$this->table;
        $sql .= " where";
        $sql .= "   identification_id = '".$this->param_identification_id."' ";
        $sql .= "   and is_deleted = 0";

        return $sql;
    }

    /**
     * 追加
     *
     * @return void
     */
    public function insertGeneral(){
        try {
            DB::table($this->table)->insert(
                [
                    'identification_id' => $this->identification_id,
                    'code' => $this->code,
                    'sort_seq' => $this->sort_seq,
                    'identification_name' => $this->identification_name,
                    'description' => $this->description,
                    'physical_name' => $this->physical_name,
                    'code_name' => $this->code_name,
                    'secound_code_name' => $this->secound_code_name,
                    'use_free_item' => $this->use_free_item,
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
     * 更新
     *
     * @return void
     */
    public function updateGeneral(){
        try {
            DB::table($this->table)
                ->where('id', $this->id)
                ->where('is_deleted', 0)
                ->update([
                    'code_name' => $this->code_name,
                    'secound_code_name' => $this->secound_code_name,
                    'use_free_item' => $this->use_free_item,
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
     * 更新
     *
     * @return void
     */
    public function updateGeneralIsdelete(){
        try {
            DB::table($this->table)
                ->where('id', $this->id)
                ->update([
                    'is_deleted' => 1,
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

}
