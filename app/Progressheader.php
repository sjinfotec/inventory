<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\BackOrder;

class Progressheader extends Model
{
    //--------------- テーブル名 -----------------------------------
    protected $table = 'progress_headers';
    protected $table_progress_details = 'progress_details';
    protected $table_process_histories = 'process_histories';
    protected $table_back_order = 'back_order';
    protected $table_products = 'products';
    protected $table_customers = 'customers';
    protected $table_devices = 'devices';
    protected $table_users = 'users';

    //--------------- メンバー属性 -----------------------------------
    private $id;                              // ID
    private $out_seq;                              // 出力順
    private $order_no;                              // 受注番号
    private $seq;                              // 連番
    private $row_seq;                              // 行
    private $drawing_no;                              // 図面番号
    private $order_date;                              // 受注日
    private $order_kingaku;                              // 受注金額
    private $save_sheet;                              // 保存シート
    private $supply_date;                              // 納期
    private $office_code;                              // 営業所コード
    private $customer_code;                              // 顧客コード
    private $back_order_customer_name;                              // 受注残客先
    private $order_count;                              // 数量
    private $model_number;                              // 型番
    private $product_code;                              // 品名コード
    private $processes_code;                              // 工程コード
    private $back_order_product_name;                              // 受注残品名
    private $unit_price;                              // 単価
    private $outline_name;                              // 摘要
    private $back_order_quality_name;                              // 材質
    private $material_cost;                              // 材料費
    private $material_office_code;                              // 素材納入営業所コード
    private $material_customer_code;                              // 素材納入顧客コード
    private $material_customer_name;                         // 素材納入元
    private $heat_process;                              // 熱処理
    private $heat_cost;                              // 熱処理費
    private $outsourcing_office_code;                              // 外注営業所コード
    private $outsourcing_customer_code;                              // 外注顧客コード
    private $outsourcing_cost;                              // 外注費
    private $created_user;                              // 作成ユーザー
    private $updated_user;                              // 修正ユーザー
    private $created_at;                              // 作成日時
    private $updated_at;                              // 修正日時
    private $is_deleted;                              // 削除フラグ

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
    //出力順
    public function getOutseqAttribute()
    {
        return $this->out_seq;
    }

    public function setOutseqAttribute($value)
    {
        $this->out_seq = $value;
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
    //連番
    public function getSeqAttribute()
    {
        return $this->seq;
    }

    public function setSeqAttribute($value)
    {
        $this->seq = $value;
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
    //受注日
    public function getOrderdateAttribute()
    {
        return $this->order_date;
    }

    public function setOrderdateAttribute($value)
    {
        $this->order_date = $value;
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
    //保存シート
    public function getSavesheetAttribute()
    {
        return $this->save_sheet;
    }

    public function setSavesheetAttribute($value)
    {
        $this->save_sheet = $value;
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
    //営業所コード
    public function getOfficecodeAttribute()
    {
        return $this->office_code;
    }

    public function setOfficecodeAttribute($value)
    {
        $this->office_code = $value;
    }
    //顧客コード
    public function getCustomercodeAttribute()
    {
        return $this->customer_code;
    }

    public function setCustomercodeAttribute($value)
    {
        $this->customer_code = $value;
    }
    //受注残客先
    public function getBackordercustomernameAttribute()
    {
        return $this->back_order_customer_name;
    }

    public function setBackordercustomernameAttribute($value)
    {
        $this->back_order_customer_name = $value;
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
    //型番
    public function getModelnumberAttribute()
    {
        return $this->model_number;
    }

    public function setModelnumberAttribute($value)
    {
        $this->model_number = $value;
    }
    //品名コード
    public function getProductcodeAttribute()
    {
        return $this->product_code;
    }

    public function setProductcodeAttribute($value)
    {
        $this->product_code = $value;
    }
    //工程コード
    public function getProcessescodeAttribute()
    {
        return $this->processes_code;
    }

    public function setProcessescodeAttribute($value)
    {
        $this->processes_code = $value;
    }
    //受注残品名
    public function getBackorderproductnameAttribute()
    {
        return $this->back_order_product_name;
    }

    public function setBackorderproductnameAttribute($value)
    {
        $this->back_order_product_name = $value;
    }
    //単価
    public function getUnitpriceAttribute()
    {
        return $this->unit_price;
    }

    public function setUnitpriceAttribute($value)
    {
        if (isset($value)) {
            $this->unit_price = $value;
        } else {
            $this->unit_price = 0;
        }
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
    //材質
    public function getBackorderqualitynameAttribute()
    {
        return $this->back_order_quality_name;
    }

    public function setBackorderqualitynameAttribute($value)
    {
        $this->back_order_quality_name = $value;
    }
    //材料費
    public function getMaterialcostAttribute()
    {
        if (isset($value)) {
            $this->material_cost = $value;
        } else {
            $this->material_cost = 0;
        }
    }

    public function setMaterialcostAttribute($value)
    {
        $this->material_cost = $value;
    }
    //素材納入営業所コード
    public function getMaterialofficecodeAttribute()
    {
        return $this->material_office_code;
    }

    public function setMaterialofficecodeAttribute($value)
    {
        $this->material_office_code = $value;
    }
    //素材納入顧客コード
    public function getMaterialcustomercodeAttribute()
    {
        return $this->material_customer_code;
    }

    public function setMaterialcustomercodeAttribute($value)
    {
        $this->material_customer_code = $value;
    }
    // 素材納入元
    public function getMaterialcustomernameAttribute()
    {
        return $this->material_customer_name;
    }

    public function setMaterialcustomernameAttribute($value)
    {
        $this->material_customer_name = $value;
    }
    //熱処理
    public function getHeatprocessAttribute()
    {
        return $this->heat_process;
    }

    public function setHeatprocessAttribute($value)
    {
        $this->heat_process = $value;
    }
    //熱処理費
    public function getHeatcostAttribute()
    {
        return $this->heat_cost;
    }

    public function setHeatcostAttribute($value)
    {
        if (isset($value)) {
            $this->heat_cost = $value;
        } else {
            $this->heat_cost = 0;
        }
    }
    //外注営業所コード
    public function getOutsourcingofficecodeAttribute()
    {
        return $this->outsourcing_office_code;
    }

    public function setOutsourcingofficecodeAttribute($value)
    {
        $this->outsourcing_office_code = $value;
    }
    //外注顧客コード
    public function getOutsourcingcustomercodeAttribute()
    {
        return $this->outsourcing_customer_code;
    }

    public function setOutsourcingcustomercodeAttribute($value)
    {
        $this->outsourcing_customer_code = $value;
    }
    //外注費
    public function getOutsourcingcostAttribute()
    {
        return $this->outsourcing_cost;
    }

    public function setOutsourcingcostAttribute($value)
    {
        if (isset($value)) {
            $this->outsourcing_cost = $value;
        } else {
            $this->outsourcing_cost = 0;
        }
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
    //削除フラグ
    public function getIsdeletedAttribute()
    {
        return $this->is_deleted;
    }

    public function setIsdeletedAttribute($value)
    {
        $this->is_deleted = $value;
    }


    //--------------- パラメータ項目属性 -----------------------------------
    private $param_id;                              // ID
    private $param_out_seq;                              // 出力順
    private $param_order_no;                              // 受注番号
    private $param_seq;                              // 連番
    private $param_row_seq;                              // 行
    private $param_drawing_no;                              // 図面番号
    private $param_order_date;                              // 受注日
    private $param_order_date_from;                         // 受注日（開始）
    private $param_order_date_to;                           // 受注日（終了）
    private $param_order_kingaku;                              // 受注金額
    private $param_save_sheet;                              // 保存シート
    private $param_supply_date;                              // 納期
    private $param_supply_date_from;                         // 納期（開始）
    private $param_supply_date_to;                           // 納期（終了）
    private $param_office_code;                              // 営業所コード
    private $param_customer_code;                              // 顧客コード
    private $param_back_order_customer_name;                              // 受注残客先
    private $param_order_count;                              // 数量
    private $param_model_number;                              // 型番
    private $param_product_code;                              // 品名コード
    private $param_processes_code;                              // 工程コード
    private $param_back_order_product_name;                              // 受注残品名
    private $param_unit_price;                              // 単価
    private $param_outline_name;                              // 摘要
    private $param_back_order_quality_name;                              // 材質
    private $param_material_cost;                              // 材料費
    private $param_material_office_code;                              // 素材納入営業所コード
    private $param_material_customer_code;                              // 素材納入顧客コード
    private $param_material_customer_name;                              // 素材納入元
    private $param_heat_process;                              // 熱処理
    private $param_heat_cost;                              // 熱処理費
    private $param_outsourcing_office_code;                              // 外注営業所コード
    private $param_outsourcing_customer_code;                              // 外注顧客コード
    private $param_outsourcing_cost;                              // 外注費
    private $param_created_user;                              // 作成ユーザー
    private $param_updated_user;                              // 修正ユーザー
    private $param_created_at;                              // 作成日時
    private $param_updated_at;                              // 修正日時
    private $param_is_deleted;                              // 削除フラグ
    private $param_device_code;                              // 機器コード（devices）
    private $param_user_code;                              // 社員コード（users）

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
    //出力順
    public function getParamOutseqAttribute()
    {
        return $this->param_out_seq;
    }
    
    public function setParamOutseqAttribute($value)
    {
        $this->param_out_seq = $value;
    }
    //受注番号
    public function getParamOrdernoAttribute()
    {
        return $this->param_order_no;
    }
    
    public function setParamOrdernoAttribute($value)
    {
        $this->param_order_no = $value;
    }
    //受注金額
    public function getParamOrderkingakuAttribute()
    {
        return $this->param_order_kingaku;
    }

    public function setParamOrderkingakuAttribute($value)
    {
        $this->param_order_kingaku = $value;
    }
    //保存シート
    public function getParamSavesheetAttribute()
    {
        return $this->param_save_sheet;
    }

    public function setParamSavesheetAttribute($value)
    {
        $this->param_save_sheet = $value;
    }
    //連番
    public function getParamSeqAttribute()
    {
        return $this->param_seq;
    }
    
    public function setParamSeqAttribute($value)
    {
        $this->param_seq = $value;
    }
    //行
    public function getParamRowseqAttribute()
    {
        return $this->param_row_seq;
    }
    
    public function setParamRowseqAttribute($value)
    {
        $this->param_row_seq = $value;
    }
    //図面番号
    public function getParamDrawingnoAttribute()
    {
        return $this->param_drawing_no;
    }
    
    public function setParamDrawingnoAttribute($value)
    {
        $this->param_drawing_no = $value;
    }
    //受注日
    public function getParamOrderdateAttribute()
    {
        return $this->param_order_date;
    }
    
    public function setParamOrderdateAttribute($value)
    {
        $this->param_order_date = $value;
    }
    //受注日（開始）
    public function getParamOrderdateFromAttribute()
    {
        return $this->param_order_date_from;
    }

    public function setParamOrderdateFromAttribute($value)
    {
        $this->param_order_date_from = $value;
    }
    //受注日（終了）
    public function getParamOrderdateToAttribute()
    {
        return $this->param_order_date_to;
    }

    public function setParamOrderdateToAttribute($value)
    {
        $this->param_order_date_to = $value;
    }
    //納期
    public function getParamSupplydateAttribute()
    {
        return $this->param_supply_date;
    }
    
    public function setParamSupplydateAttribute($value)
    {
        $this->param_supply_date = $value;
    }
    //納期日（開始）
    public function getParamSupplydateFromAttribute()
    {
        return $this->param_supply_date_from;
    }

    public function setParamSupplydateFromAttribute($value)
    {
        $this->param_supply_date_from = $value;
    }
    //納期日（終了）
    public function getParamSupplydateToAttribute()
    {
        return $this->param_supply_date_to;
    }

    public function setParamSupplydateToAttribute($value)
    {
        $this->param_supply_date_to = $value;
    }
    //営業所コード
    public function getParamOfficecodeAttribute()
    {
        return $this->param_office_code;
    }
    
    public function setParamOfficecodeAttribute($value)
    {
        $this->param_office_code = $value;
    }
    //顧客コード
    public function getParamCustomercodeAttribute()
    {
        return $this->param_customer_code;
    }
    
    public function setParamCustomercodeAttribute($value)
    {
        $this->param_customer_code = $value;
    }
    //受注残客先
    public function getParamBackordercustomernameAttribute()
    {
        return $this->param_back_order_customer_name;
    }
    
    public function setParamBackordercustomernameAttribute($value)
    {
        $this->param_back_order_customer_name = $value;
    }
    //数量
    public function getParamOrdercountAttribute()
    {
        return $this->param_order_count;
    }
    
    public function setParamOrdercountAttribute($value)
    {
        $this->param_order_count = $value;
    }
    //型番
    public function getParamModelnumberAttribute()
    {
        return $this->param_model_number;
    }
    
    public function setParamModelnumberAttribute($value)
    {
        $this->param_model_number = $value;
    }
    //品名コード
    public function getParamProductcodeAttribute()
    {
        return $this->param_product_code;
    }
    
    public function setParamProductcodeAttribute($value)
    {
        $this->param_product_code = $value;
    }
    //工程コード
    public function getParamProcessescodeAttribute()
    {
        return $this->param_processes_code;
    }
    
    public function setParamProcessescodeAttribute($value)
    {
        $this->param_processes_code = $value;
    }
    //受注残品名
    public function getParamBackorderproductnameAttribute()
    {
        return $this->param_back_order_product_name;
    }
    
    public function setParamBackorderproductnameAttribute($value)
    {
        $this->param_back_order_product_name = $value;
    }
    //単価
    public function getParamUnitpriceAttribute()
    {
        return $this->param_unit_price;
    }
    
    public function setParamUnitpriceAttribute($value)
    {
        $this->param_unit_price = $value;
    }
    //摘要
    public function getParamOutlinenameAttribute()
    {
        return $this->param_outline_name;
    }
    
    public function setParamOutlinenameAttribute($value)
    {
        $this->param_outline_name = $value;
    }
    //材質
    public function getParamBackorderqualitynameAttribute()
    {
        return $this->param_back_order_quality_name;
    }
    
    public function setParamBackorderqualitynameAttribute($value)
    {
        $this->param_back_order_quality_name = $value;
    }
    //材料費
    public function getParamMaterialcostAttribute()
    {
        return $this->param_material_cost;
    }
    
    public function setParamMaterialcostAttribute($value)
    {
        $this->param_material_cost = $value;
    }
    //素材納入営業所コード
    public function getParamMaterialofficecodeAttribute()
    {
        return $this->param_material_office_code;
    }
    
    public function setParamMaterialofficecodeAttribute($value)
    {
        $this->param_material_office_code = $value;
    }
    //素材納入顧客コード
    public function getParamMaterialcustomercodeAttribute()
    {
        return $this->param_material_customer_code;
    }
    
    public function setParamMaterialcustomercodeAttribute($value)
    {
        $this->param_material_customer_code = $value;
    }
    //素材納入元
    public function getParamMaterialcustomernameAttribute()
    {
        return $this->param_material_customer_name;
    }
    
    public function setParamMaterialcustomernameAttribute($value)
    {
        $this->param_material_customer_name = $value;
    }
    //熱処理
    public function getParamHeatprocessAttribute()
    {
        return $this->param_heat_process;
    }
    
    public function setParamHeatprocessAttribute($value)
    {
        $this->param_heat_process = $value;
    }
    //熱処理費
    public function getParamHeatcostAttribute()
    {
        return $this->param_heat_cost;
    }
    
    public function setParamHeatcostAttribute($value)
    {
        $this->param_heat_cost = $value;
    }
    //外注営業所コード
    public function getParamOutsourcingofficecodeAttribute()
    {
        return $this->param_outsourcing_office_code;
    }
    
    public function setParamOutsourcingofficecodeAttribute($value)
    {
        $this->param_outsourcing_office_code = $value;
    }
    //外注顧客コード
    public function getParamOutsourcingcustomercodeAttribute()
    {
        return $this->param_outsourcing_customer_code;
    }
    
    public function setParamOutsourcingcustomercodeAttribute($value)
    {
        $this->param_outsourcing_customer_code = $value;
    }
    //外注費
    public function getParamOutsourcingcostAttribute()
    {
        return $this->param_outsourcing_cost;
    }
    
    public function setParamOutsourcingcostAttribute($value)
    {
        $this->param_outsourcing_cost = $value;
    }
    //作成ユーザー
    public function getParamCreateduserAttribute()
    {
        return $this->param_created_user;
    }
    
    public function setParamCreateduserAttribute($value)
    {
        $this->param_created_user = $value;
    }
    //修正ユーザー
    public function getParamUpdateduserAttribute()
    {
        return $this->param_updated_user;
    }
    
    public function setParamUpdateduserAttribute($value)
    {
        $this->param_updated_user = $value;
    }
    //作成日時
    public function getParamCreatedatAttribute()
    {
        return $this->param_created_at;
    }
    
    public function setParamCreatedatAttribute($value)
    {
        $this->param_created_at = $value;
    }
    //修正日時
    public function getParamUpdatedatAttribute()
    {
        return $this->param_updated_at;
    }
    
    public function setParamUpdatedatAttribute($value)
    {
        $this->param_updated_at = $value;
    }
    //削除フラグ
    public function getParamIsdeletedAttribute()
    {
        return $this->param_is_deleted;
    }
    
    public function setParamIsdeletedAttribute($value)
    {
        $this->param_is_deleted = $value;
    }
    //機器コード（devices）
    public function getParamDevicecodeAttribute()
    {
        return $this->param_device_code;
    }
    
    public function setParamDevicecodeAttribute($value)
    {
        $this->param_device_code = $value;
    }
    //社員コード（users）
    public function getParamUsercodeAttribute()
    {
        return $this->param_user_code;
    }
    
    public function setParamUsercodeAttribute($value)
    {
        $this->param_user_code = $value;
    }
    


    // ---------------------- メソッド ------------------------------
    /** 加工指示書／工程管理取得
     *
     * @return list customer
     */
    public function getProductheader(){
        $this->array_messagedata = array();
        $details = new Collection();
        $result = true;
        try {

            // ログインユーザの権限取得
            $user = Auth::user();
            $login_user_code = $user->code;
            $login_account_id = $user->account_id;
            $sqlString = "";
            $sqlString .= "select" ;
            $sqlString .= "  t1.order_no as order_no" ;
            $sqlString .= "  , t1.out_seq as out_seq" ;
            $sqlString .= "  , t1.seq as max_seq" ;
            $sqlString .= "  , t1.row_seq as row_seq" ;
            $sqlString .= "  , t1.drawing_no as drawing_no" ;
            $sqlString .= "  , t1.order_date as order_date" ;
            $sqlString .= "  , t1.order_kingaku as order_kingaku" ;
            $sqlString .= "  , t1.save_sheet as save_sheet" ;
            $sqlString .= "  , t1.supply_date as supply_date" ;
            $sqlString .= "  , date_format(t1.order_date,'%Y年%m月%d日') as order_date_name" ;
            $sqlString .= "  , date_format(t1.supply_date,'%Y年%m月%d日') as supply_date_name" ;
            $sqlString .= "  , t1.office_code as office_code" ;
            $sqlString .= "  , t1.customer_code as customer_code" ;
            $sqlString .= "  , t1.back_order_customer_name as back_order_customer_name" ;
            $sqlString .= "  , t1.order_count as order_count" ;
            $sqlString .= "  , t1.model_number as model_number" ;
            $sqlString .= "  , t1.product_code as product_code" ;
            $sqlString .= "  , t1.processes_code as processes_code" ;
            $sqlString .= "  , t1.back_order_product_name as back_order_product_name" ;
            $sqlString .= "  , t1.unit_price as unit_price" ;
            $sqlString .= "  , t1.outline_name as outline_name" ;
            $sqlString .= "  , t1.back_order_quality_name as back_order_quality_name" ;
            $sqlString .= "  , t1.material_cost as material_cost" ;
            $sqlString .= "  , t1.material_office_code as material_office_code" ;
            $sqlString .= "  , t1.material_customer_code as material_customer_code" ;
            $sqlString .= "  , t1.material_customer_name as material_customer_name" ;
            $sqlString .= "  , t1.heat_process as heat_process" ;
            $sqlString .= "  , t1.heat_cost as heat_cost" ;
            $sqlString .= "  , t1.outsourcing_office_code as outsourcing_office_code" ;
            $sqlString .= "  , t1.outsourcing_customer_code as outsourcing_customer_code" ;
            $sqlString .= "  , t1.outsourcing_cost as outsourcing_cost" ;
            $sqlString .= "  , case ifnull(t3.name ,'') when '' then t1.back_order_product_name else t3.name end as product_name" ;
            $sqlString .= "  , case ifnull(t4.name ,'') when '' then t1.back_order_customer_name else t4.name end as customer_name" ;
            $sqlString .= "  from" ;
            $sqlString .= "  ".$this->table." as t1" ;
            $sqlString .= "  left outer join" ;
            $sqlString .= "    ( select ";
            $sqlString .= "        code ";
            $sqlString .= "        , min(processes_code) as processes_code ";
            $sqlString .= "        , name ";
            $sqlString .= "        , is_deleted ";
            $sqlString .= "      from ";
            $sqlString .= "        ".$this->table_products." as t1 ";
            $sqlString .= "      group by ";
            $sqlString .= "         code ";
            $sqlString .= "    ) t3 ";
            $sqlString .= "  on" ;
            $sqlString .= "    t1.product_code = t3.code ";
            $sqlString .= "    and t1.product_code is not null " ;
            $sqlString .= "    and t3.is_deleted = 0 " ;
            $sqlString .= "  left outer join ";
            $sqlString .= "    ".$this->table_customers." as t4 ";
            $sqlString .= "  on ";
            $sqlString .= "    t1.office_code = t4.office_code";
            $sqlString .= "    and t1.customer_code = t4.code";
            $sqlString .= "    and t1.office_code is not null " ;
            $sqlString .= "    and t1.customer_code is not null " ;
            $sqlString .= "    and t4.is_deleted = 0 ";
            $sqlString .= "  where" ;
            $sqlString .= "    ? = ?" ;
            Log::debug('getProductheader = '.$this->param_order_date_from);
            if (!empty($this->param_supply_date_from)) {
                $sqlString .= "    and t1.supply_date = ?" ;
            }
            if (!empty($this->param_supply_date_to)) {
                $sqlString .= "    and t1.supply_date <= ?" ;
            }
            if (!empty($this->param_office_code)) {
                $sqlString .= "    and t1.office_code = ?" ;
            }
            if (!empty($this->param_customer_code)) {
                $sqlString .= "    and t1.customer_code = ?" ;
            }
            if (!empty($this->param_order_no)) {
                $sqlString .= "    and t1.order_no = ?" ;
            }
            if (!empty($this->param_drawing_no)) {
                $sqlString .= "    and t1.drawing_no = ?" ;
            }
            // $sqlString .= "  group by t1.order_no, t1.order_date " ;
            // $sqlString .= "  order by t1.order_no, t1.order_date desc " ;
            $sqlString .= "  order by t1.out_seq " ;
            // バインド
            $array_setBindingsStr = array();
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            if (!empty($this->param_supply_date_from)) {
                $array_setBindingsStr[] = $this->param_supply_date_from;
            }
            if (!empty($this->param_supply_date_to)) {
                $array_setBindingsStr[] = $this->param_supply_date_to;
            }
            if (!empty($this->param_office_code)) {
                $array_setBindingsStr[] = $this->param_office_code;
            }
            if (!empty($this->param_customer_code)) {
                $array_setBindingsStr[] = $this->param_customer_code;
            }
            if (!empty($this->param_order_no)) {
                $array_setBindingsStr[] = $this->param_order_no;
            }
            if (!empty($this->param_drawing_no)) {
                $array_setBindingsStr[] = $this->param_drawing_no;
            }
            $details = DB::select($sqlString, $array_setBindingsStr);
            return $details;
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_product_processes, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_product_processes, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /** 
     *  登録
     */
    public function insert(){
        try {
            DB::table($this->table)->insert(
                [
                    'out_seq' => $this->out_seq,
                    'order_no' => $this->order_no,
                    'seq' => $this->seq,
                    'row_seq' => $this->row_seq,
                    'drawing_no' => $this->drawing_no,
                    'order_date' => $this->order_date,
                    'order_kingaku' => $this->order_kingaku,
                    'save_sheet' => $this->save_sheet,
                    'supply_date' => $this->supply_date,
                    'office_code' => $this->office_code,
                    'customer_code' => $this->customer_code,
                    'back_order_customer_name' => $this->back_order_customer_name,
                    'order_count' => $this->order_count,
                    'model_number' => $this->model_number,
                    'product_code' => $this->product_code,
                    'processes_code' => $this->processes_code,
                    'back_order_product_name' => $this->back_order_product_name,
                    'unit_price' => $this->unit_price,
                    'outline_name' => $this->outline_name,
                    'back_order_quality_name' => $this->back_order_quality_name,
                    'material_cost' => $this->material_cost,
                    'material_office_code' => $this->material_office_code,
                    'material_customer_code' => $this->material_customer_code,
                    'material_customer_name' => $this->material_customer_name,
                    'heat_process' => $this->heat_process,
                    'heat_cost' => $this->heat_cost,
                    'outsourcing_office_code' => $this->outsourcing_office_code,
                    'outsourcing_customer_code' => $this->outsourcing_customer_code,
                    'outsourcing_cost' => $this->outsourcing_cost,
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
     *  更新
     */
    public function updateHeader(){
        try {
            DB::table($this->table)
            ->where('order_no', $this->param_order_no)
            ->where('out_seq', $this->param_out_seq)
            ->update([
                'seq' => $this->seq,
                'row_seq' => $this->row_seq,
                'drawing_no' => $this->drawing_no,
                'order_date' => $this->order_date,
                'order_kingaku' => $this->order_kingaku,
                'save_sheet' => $this->save_sheet,
                'supply_date' => $this->supply_date,
                'office_code' => $this->office_code,
                'customer_code' => $this->customer_code,
                'back_order_customer_name' => $this->back_order_customer_name,
                'order_count' => $this->order_count,
                'model_number' => $this->model_number,
                'product_code' => $this->product_code,
                'processes_code' => $this->processes_code,
                'back_order_product_name' => $this->back_order_product_name,
                'unit_price' => $this->unit_price,
                'outline_name' => $this->outline_name,
                'back_order_quality_name' => $this->back_order_quality_name,
                'material_cost' => $this->material_cost,
                'material_office_code' => $this->material_office_code,
                'product_code' => $this->product_code,
                'material_customer_code' => $this->material_customer_code,
                'material_customer_name' => $this->material_customer_name,
                'heat_process' => $this->heat_process,
                'heat_cost' => $this->heat_cost,
                'outsourcing_office_code' => $this->outsourcing_office_code,
                'outsourcing_customer_code' => $this->outsourcing_customer_code,
                'outsourcing_cost' => $this->outsourcing_cost,
                'updated_user'=>$this->updated_user,
                'updated_at' => $this->updated_at
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

    /** 加工指示書／工程管理取得（モバイル表示用）
     *
     * @return list customer
     */
    public function getProductheaderM(){
        $this->array_messagedata = array();
        $details = new Collection();
        $result = true;
        try {

            // ログインユーザの権限取得
            $user = Auth::user();
            $login_user_code = $user->code;
            $login_account_id = $user->account_id;
            $sqlString = "";
            $sqlString .= "select" ;
            $sqlString .= "  t1.order_no as order_no" ;
            $sqlString .= "  , MAX(t1.seq) as max_seq" ;
            $sqlString .= "  , t1.row_seq as row_seq" ;
            $sqlString .= "  , t1.drawing_no as drawing_no" ;
            $sqlString .= "  , t1.order_date as order_date" ;
            $sqlString .= "  , t1.order_kingaku as order_kingaku" ;
            $sqlString .= "  , t1.save_sheet as save_sheet" ;
            $sqlString .= "  , t1.supply_date as supply_date" ;
            $sqlString .= "  , date_format(t1.order_date,'%Y年%m月%d日') as order_date_name" ;
            $sqlString .= "  , date_format(t1.supply_date,'%Y年%m月%d日') as supply_date_name" ;
            $sqlString .= "  , t1.office_code as office_code" ;
            $sqlString .= "  , t1.customer_code as customer_code" ;
            $sqlString .= "  , t1.back_order_customer_name as back_order_customer_name" ;
            $sqlString .= "  , t1.order_count as order_count" ;
            $sqlString .= "  , t1.model_number as model_number" ;
            $sqlString .= "  , t1.product_code as product_code" ;
            $sqlString .= "  , t1.processes_code as processes_code" ;
            $sqlString .= "  , t1.back_order_product_name as back_order_product_name" ;
            $sqlString .= "  , t1.unit_price as unit_price" ;
            $sqlString .= "  , t1.outline_name as outline_name" ;
            $sqlString .= "  , t1.back_order_quality_name as back_order_quality_name" ;
            $sqlString .= "  , t1.material_cost as material_cost" ;
            $sqlString .= "  , t1.material_office_code as material_office_code" ;
            $sqlString .= "  , t1.material_customer_code as material_customer_code" ;
            $sqlString .= "  , t1.material_customer_name as material_customer_name" ;
            $sqlString .= "  , t1.heat_process as heat_process" ;
            $sqlString .= "  , t1.heat_cost as heat_cost" ;
            $sqlString .= "  , t1.outsourcing_office_code as outsourcing_office_code" ;
            $sqlString .= "  , t1.outsourcing_customer_code as outsourcing_customer_code" ;
            $sqlString .= "  , t1.outsourcing_cost as outsourcing_cost" ;
            $sqlString .= "  , case ifnull(t3.name ,'') when '' then t1.back_order_product_name else t3.name end as product_name" ;
            $sqlString .= "  , case ifnull(t4.name ,'') when '' then t1.back_order_customer_name else t4.name end as customer_name" ;
            $sqlString .= "  , ( select name from ".$this->table_devices." where code = ".$this->param_device_code." ) as device_name" ;
            $sqlString .= "  , ( select name from ".$this->table_users." where code = ".$this->param_user_code." ) as user_name" ;
            $sqlString .= "  , case ifnull(t5.work_kind ,'') when '' then '".Config::get('const.WORKKINDS.init')."'" ;
            $sqlString .= "    else t5.work_kind end as work_kind" ;
            $sqlString .= "  , ifnull(t5.process_time_h ,0) as process_time_h ";
            $sqlString .= "  , ifnull(t5.process_time_m ,0) as process_time_m ";
            $sqlString .= "  from" ;
            $sqlString .= "  ".$this->table." as t1" ;
            $sqlString .= "  left outer join" ;
            $sqlString .= "    ( select ";
            $sqlString .= "        t1.code ";
            $sqlString .= "        , min(t1.processes_code) as processes_code ";
            $sqlString .= "        , t1.name ";
            $sqlString .= "        , t1.is_deleted ";
            $sqlString .= "      from ";
            $sqlString .= "        ".$this->table_products." as t1 ";
            $sqlString .= "      group by ";
            $sqlString .= "         t1.code ";
            $sqlString .= "    ) t3 ";
            $sqlString .= "  on" ;
            $sqlString .= "    t1.product_code = t3.code ";
            $sqlString .= "    and t1.product_code is not null " ;
            $sqlString .= "    and t3.is_deleted = 0 " ;
            $sqlString .= "  left outer join ";
            $sqlString .= "    ".$this->table_customers." as t4 ";
            $sqlString .= "  on ";
            $sqlString .= "    t1.office_code = t4.office_code";
            $sqlString .= "    and t1.customer_code = t4.code";
            $sqlString .= "    and t1.office_code is not null " ;
            $sqlString .= "    and t1.customer_code is not null " ;
            $sqlString .= "    and t4.is_deleted = 0 ";
            $sqlString .= "  left outer join (  ";
            $sqlString .= "      select ";
            $sqlString .= "        t1.order_no ";
            $sqlString .= "        , t1.seq as seq ";
            $sqlString .= "        , t1.device_code as device_code ";
            $sqlString .= "        , t1.user_code as user_code ";
            $sqlString .= "        , t1.process_history_no as process_history_no ";
            $sqlString .= "        , t1.work_kind as work_kind ";
            $sqlString .= "        , t1.process_time_h as process_time_h ";
            $sqlString .= "        , t1.process_time_m as process_time_m ";
            $sqlString .= "        , t1.is_deleted as is_deleted  ";
            $sqlString .= "      from process_histories as t1 ";
            $sqlString .= "        inner join (  ";
            $sqlString .= "          select ";
            $sqlString .= "            t1.order_no ";
            $sqlString .= "            , t1.seq as seq ";
            $sqlString .= "            , t1.device_code as device_code ";
            $sqlString .= "            , t1.user_code as user_code ";
            $sqlString .= "            , max(t1.process_history_no) as process_history_no ";
            $sqlString .= "          from ";
            $sqlString .= "            process_histories as t1  ";
            $sqlString .= "          where ";
            $sqlString .= "            t1.is_deleted = 0 ";
            $sqlString .= "          group by ";
            $sqlString .= "            t1.order_no ";
            $sqlString .= "            , t1.seq ";
            $sqlString .= "            , t1.device_code ";
            $sqlString .= "            , t1.user_code ";
            $sqlString .= "        ) t2 ";
            $sqlString .= "        on t1.order_no = t2.order_no  ";
            $sqlString .= "        and t1.seq = t2.seq  ";
            $sqlString .= "        and t1.device_code = t2.device_code  ";
            $sqlString .= "        and t1.user_code = t2.user_code  ";
            $sqlString .= "        and t1.process_history_no = t2.process_history_no  ";
            $sqlString .= "        and t1.is_deleted = 0  ";
            $sqlString .= "    where ";
            $sqlString .= "      1 = 1  ";
            $sqlString .= "      and t1.device_code = '".$this->param_device_code."'";
            $sqlString .= "      and t1.user_code = '".$this->param_user_code."'";
            $sqlString .= "      and t1.is_deleted = 0  ";
            $sqlString .= "    ) t5  ";
            $sqlString .= "  on" ;
            $sqlString .= "    t1.order_no = t5.order_no ";
            $sqlString .= "    and t1.seq = t5.seq ";
            $sqlString .= "    and t5.is_deleted = 0 " ;
            $sqlString .= "  where" ;
            $sqlString .= "    ? = ?" ;
            if (!empty($this->param_order_no)) {
                $sqlString .= "    and t1.order_no = ?" ;
            }
            if (!empty($this->param_seq)) {
                $sqlString .= "    and t1.seq = ?" ;
            }
            $sqlString .= "  group by t1.order_no, t1.order_date" ;
            $sqlString .= "  order by t1.order_no, t1.order_date desc" ;
            // バインド
            $array_setBindingsStr = array();
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            if (!empty($this->param_order_no)) {
                $array_setBindingsStr[] = $this->param_order_no;
            }
            if (!empty($this->param_seq)) {
                $array_setBindingsStr[] = $this->param_seq;
            }
            $details = DB::select($sqlString, $array_setBindingsStr);
            return $details;
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_product_processes, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_product_processes, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

}
