<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class ImportBackOrder extends Model
{
    //--------------- テーブル名 -----------------------------------
    protected $table = 'import_back_order';

    //--------------- メンバー属性 -----------------------------------
    private $id;                                        // ID
    private $order_date;                                // 受注日
    private $row_seq;                                   // 行
    private $drawing_no;                                // 図面番号
    private $order_no;                                  // 受注番号
    private $customer_name;                             // 客先
    private $model_number;                              // 型番
    private $product_name;                              // 品名
    private $quality_name;                              // 材質
    private $order_count;                               // 数量
    private $supply_date;                               // 納期
    private $order_kingaku;                             // 受注金額
    private $outline_name;                              // 摘要
    private $created_user;                              // 作成ユーザー
    private $updated_user;                              // 修正ユーザー
    private $created_at;                                // 作成日時
    private $updated_at;                                // 修正日時

    //--------------- メンバーアクセサ -----------------------------------
    //ID
    public function getIdAttribute()
    {
        return $this->id;
    }

    public function setIdAttribute($value)
    {
        $this->id = $value;
    }
    //受注日
    public function getOrderdateAttribute()
    {
        return $this->order_date;
    }

    public function setOrderdateAttribute($value)
    {
        $this->order_date = $value;
    }
    //行
    public function getRowseqAttribute()
    {
        return $this->row_seq;
    }

    public function setRowseqAttribute($value)
    {
        $this->row_seq = $value;
    }
    //図面番号
    public function getDrawingnoAttribute()
    {
        return $this->drawing_no;
    }

    public function setDrawingnoAttribute($value)
    {
        $this->drawing_no = $value;
    }
    //受注番号
    public function getOrdernoAttribute()
    {
        return $this->order_no;
    }

    public function setOrdernoAttribute($value)
    {
        $this->order_no = $value;
    }
    //客先
    public function getCustomernameAttribute()
    {
        return $this->customer_name;
    }

    public function setCustomernameAttribute($value)
    {
        $this->customer_name = $value;
    }
    //型番
    public function getModelnumberAttribute()
    {
        return $this->model_number;
    }

    public function setModelnumberAttribute($value)
    {
        $this->model_number = $value;
    }
    //品名
    public function getProductnameAttribute()
    {
        return $this->product_name;
    }

    public function setProductnameAttribute($value)
    {
        $this->product_name = $value;
    }
    //材質
    public function getQualitynameAttribute()
    {
        return $this->quality_name;
    }

    public function setQualitynameAttribute($value)
    {
        $this->quality_name = $value;
    }
    //数量
    public function getOrdercountAttribute()
    {
        return $this->order_count;
    }

    public function setOrdercountAttribute($value)
    {
        $this->order_count = $value;
    }
    //納期
    public function getSupplydateAttribute()
    {
        return $this->supply_date;
    }

    public function setSupplydateAttribute($value)
    {
        $this->supply_date = $value;
    }
    //受注金額
    public function getOrderkingakuAttribute()
    {
        return $this->order_kingaku;
    }

    public function setOrderkingakuAttribute($value)
    {
        $this->order_kingaku = $value;
    }
    //摘要
    public function getOutlinenameAttribute()
    {
        return $this->outline_name;
    }

    public function setOutlinenameAttribute($value)
    {
        $this->outline_name = $value;
    }
    //作成ユーザー
    public function getCreateduserAttribute()
    {
        return $this->created_user;
    }

    public function setCreateduserAttribute($value)
    {
        $this->created_user = $value;
    }
    //修正ユーザー
    public function getUpdateduserAttribute()
    {
        return $this->updated_user;
    }

    public function setUpdateduserAttribute($value)
    {
        $this->updated_user = $value;
    }
    //作成日時
    public function getCreatedatAttribute()
    {
        return $this->created_at;
    }

    public function setCreatedatAttribute($value)
    {
        $this->created_at = $value;
    }
    //修正日時
    public function getUpdatedatAttribute()
    {
        return $this->updated_at;
    }

    public function setUpdatedatAttribute($value)
    {
        $this->updated_at = $value;
    }


    //--------------- パラメータ項目属性 -----------------------------------
    private $param_id;                              // ID
    private $param_order_date;                              // 受注日
    private $param_row_seq;                              // 行
    private $param_drawing_no;                              // 図面番号
    private $param_order_no;                              // 受注番号
    private $param_customer_name;                              // 客先
    private $param_model_number;                              // 型番
    private $param_product_name;                              // 品名
    private $param_quality_name;                              // 材質
    private $param_order_count;                              // 数量
    private $param_supply_date;                              // 納期
    private $param_order_kingaku;                              // 受注金額
    private $param_outline_name;                              // 摘要
    private $param_created_user;                              // 作成ユーザー
    private $param_updated_user;                              // 修正ユーザー
    private $param_created_at;                              // 作成日時
    private $param_updated_at;                              // 修正日時

    //--------------- パラメータアクセサ -----------------------------------
    //ID
    public function getParamIdAttribute()
    {
        return $this->param_id;
    }
    
    public function setParamIdAttribute($value)
    {
        $this->param_id = $value;
    }
    //受注日
    public function getParamOrdernoAttribute()
    {
        return $this->param_order_no;
    }
    
    public function setParamOrdernoAttribute($value)
    {
        $this->param_order_no = $value;
    }
    //行
    public function getParamSeqAttribute()
    {
        return $this->param_seq;
    }
    
    public function setParamSeqAttribute($value)
    {
        $this->param_seq = $value;
    }
    //図面番号
    public function getParamOrderdateAttribute()
    {
        return $this->param_order_date;
    }
    
    public function setParamOrderdateAttribute($value)
    {
        $this->param_order_date = $value;
    }
    //受注番号
    public function getParamRowseqAttribute()
    {
        return $this->param_row_seq;
    }
    
    public function setParamRowseqAttribute($value)
    {
        $this->param_row_seq = $value;
    }
    //客先
    public function getParamDrawingnoAttribute()
    {
        return $this->param_drawing_no;
    }
    
    public function setParamDrawingnoAttribute($value)
    {
        $this->param_drawing_no = $value;
    }
    //型番
    public function getParamCustomernameAttribute()
    {
        return $this->param_customer_name;
    }
    
    public function setParamCustomernameAttribute($value)
    {
        $this->param_customer_name = $value;
    }
    //品名
    public function getParamModelnumberAttribute()
    {
        return $this->param_model_number;
    }
    
    public function setParamModelnumberAttribute($value)
    {
        $this->param_model_number = $value;
    }
    //材質
    public function getParamProductnameAttribute()
    {
        return $this->param_product_name;
    }
    
    public function setParamProductnameAttribute($value)
    {
        $this->param_product_name = $value;
    }
    //数量
    public function getParamQualitynameAttribute()
    {
        return $this->param_quality_name;
    }
    
    public function setParamQualitynameAttribute($value)
    {
        $this->param_quality_name = $value;
    }
    //納期
    public function getParamOrdercountAttribute()
    {
        return $this->param_order_count;
    }
    
    public function setParamOrdercountAttribute($value)
    {
        $this->param_order_count = $value;
    }
    //受注金額
    public function getParamSupplydateAttribute()
    {
        return $this->param_supply_date;
    }
    
    public function setParamSupplydateAttribute($value)
    {
        $this->param_supply_date = $value;
    }
    //摘要
    public function getParamOrderkingakuAttribute()
    {
        return $this->param_order_kingaku;
    }
    
    public function setParamOrderkingakuAttribute($value)
    {
        $this->param_order_kingaku = $value;
    }
    //作成ユーザー
    public function getParamOutlinenameAttribute()
    {
        return $this->param_outline_name;
    }
    
    public function setParamOutlinenameAttribute($value)
    {
        $this->param_outline_name = $value;
    }
    //修正ユーザー
    public function getParamCreateduserAttribute()
    {
        return $this->param_created_user;
    }
    
    public function setParamCreateduserAttribute($value)
    {
        $this->param_created_user = $value;
    }
    //作成日時
    public function getParamUpdateduserAttribute()
    {
        return $this->param_updated_user;
    }
    
    public function setParamUpdateduserAttribute($value)
    {
        $this->param_updated_user = $value;
    }
    //修正日時
    public function getParamCreatedatAttribute()
    {
        return $this->param_created_at;
    }
    
    public function setParamCreatedatAttribute($value)
    {
        $this->param_created_at = $value;
    }

    
    // ------------- メソッド --------------

    /**
     * import受注残登録
     *
     * @return void
     */
    public function insertArray($paramarraydata){
        try {
            DB::table($this->table)->insert($paramarraydata);
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
     * import受注残削除
     *
     * @return void
     */
    public function delAlldata(){
        try {
            $result = DB::table($this->table)->delete();
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


}
