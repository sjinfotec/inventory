<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;


class GeneralCodes extends Model
{
    protected $table = 'generalcodes';
    protected $guarded = array('id');

    //--------------- メンバー属性 -----------------------------------

    private $identification_id;             // 識別

    // 識別
    public function getIdentificationidAttribute()
    {
        return $this->identification_id;
    }

    public function setIdentificationidAttribute($value)
    {
        $this->identification_id = $value;
    }

    private $code;                          // コード

    // コード
    public function getCodeAttribute()
    {
        return $this->code;
    }

    public function setCodeAttribute($value)
    {
        $this->code = $value;
    }

    private $sort_seq;                      // 並び順

    // 並び順
    public function getSortseqAttribute()
    {
        return $this->sort_seq;
    }

    public function setSortseqAttribute($value)
    {
        $this->sort_seq = $value;
    }

    private $identification_name;           // 識別名

    // 識別名
    public function getIdentificationnameAttribute()
    {
        return $this->identification_name;
    }

    public function setIdentificationnameAttribute($value)
    {
        $this->identification_name = $value;
    }

    private $description;                   // 説明

    // 説明
    public function getDescriptionAttribute()
    {
        return $this->description;
    }

    public function setDescriptionAttribute($value)
    {
        $this->description = $value;
    }

    private $physical_name;                 // 物理名称

    // 物理名称
    public function getPhysicalnameAttribute()
    {
        return $this->physical_name;
    }

    public function setPhysicalnameAttribute($value)
    {
        $this->physical_name = $value;
    }

    private $code_name;                     // 項目名

    // 項目名
    public function getCodenameAttribute()
    {
        return $this->code_name;
    }

    public function setCodenameAttribute($value)
    {
        $this->code_name = $value;
    }

    private $secound_code_name;             // 項目名略称

    // 項目名
    public function getSecoundcodenameAttribute()
    {
        return $this->secound_code_name;
    }

    public function setSecoundcodenameAttribute($value)
    {
        $this->secound_code_name = $value;
    }

    private $use_free_item;                 // 用途フリー項目

    // 項目名
    public function getUsefreeitemAttribute()
    {
        return $this->use_free_item;
    }

    public function setUsefreeitemAttribute($value)
    {
        $this->use_free_item = $value;
    }

    private $created_user;                  // 作成ユーザー

    // 作成ユーザー
    public function getCreateduserAttribute()
    {
        return $this->created_user;
    }

    public function setCreateduserAttribute($value)
    {
        $this->created_user = $value;
    }

    private $updated_user;                  // 修正ユーザー

    // 修正ユーザー
    public function getUpdateduserAttribute()
    {
        return $this->updated_user;
    }

    public function setUpdateduserAttribute($value)
    {
        $this->updated_user = $value;
    }

    private $is_deleted;                    // 削除フラグ

    // 削除フラグ
    public function getIsdeletedAttribute()
    {
        return $this->is_deleted;
    }

    public function setIsdeletedAttribute($value)
    {
        $this->is_deleted = $value;
    }

    //--------------- パラメータ項目属性 -----------------------------------

    private $param_identification_id;           // 識別
    private $param_array_identification_id;     // 識別（array）
    private $param_code;                        // コード


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

}
