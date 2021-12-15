<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BackOrder extends Model
{
    //--------------- テーブル名 -----------------------------------
    protected $table = 'back_order';
    protected $table_customers = 'customers';
    protected $table_products = 'products';

    //--------------- メンバー属性 -----------------------------------
    private $id;                              // ID
    private $order_no;                              // 受注番号
    private $out_seq;                                   // 出力順
    private $seq;                              // 連番
    private $order_date;                              // 受注日
    private $row_seq;                              // 行
    private $drawing_no;                              // 図面番号
    private $customer_name;                              // 客先
    private $model_number;                              // 型番
    private $product_name;                              // 品名
    private $quality_name;                              // 材質
    private $order_count;                              // 数量
    private $supply_date;                              // 納期
    private $order_kingaku;                              // 受注金額
    private $outline_name;                              // 摘要
    private $unit_price;                                // 単価
    private $is_update;                                 // 指示書登録フラグ
    private $is_deleted;                                // 削除フラグ
    private $created_user;                              // 作成ユーザー
    private $updated_user;                              // 修正ユーザー
    private $created_at;                              // 作成日時
    private $updated_at;                              // 修正日時
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
    //単価
    public function getUnitpriceAttribute()
    {
        return $this->unit_price;
    }

    public function setUnitpriceAttribute($value)
    {
        $this->unit_price = $value;
    }
    //指示書登録フラグ
    public function getIsUpdateAttribute()
    {
        return $this->is_update;
    }
    public function setIsUpdateAttribute($value)
    {
        $this->is_update = $value;
    }
    //削除フラグ
    public function getIsDeletedAttribute()
    {
        return $this->is_deleted;
    }

    public function setIsDeletedAttribute($value)
    {
        $this->is_deleted = $value;
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
    private $param_out_seq;                                   // 出力順
    private $param_order_no;                              // 受注番号
    private $param_seq;                              // 連番
    private $param_order_date;                              // 受注日
    private $param_order_date_from;                         // 受注日（開始）
    private $param_order_date_to;                           // 受注日（終了）
    private $param_row_seq;                              // 行
    private $param_drawing_no;                              // 図面番号
    private $param_customer_name;                              // 客先
    private $param_model_number;                              // 型番
    private $param_product_name;                              // 品名
    private $param_quality_name;                              // 材質
    private $param_order_count;                              // 数量
    private $param_supply_date;                              // 納期
    private $param_order_kingaku;                              // 受注金額
    private $param_outline_name;                              // 摘要
    private $param_unit_price;                                   // 単価
    private $param_is_update;                                   // 指示書登録フラグ
    private $param_is_deleted;                                  // 削除フラグ
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
    //連番
    public function getParamSeqAttribute()
    {
        return $this->param_seq;
    }

    public function setParamSeqAttribute($value)
    {
        $this->param_seq = $value;
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
        return $this->param_order_date_from;
    }

    public function setParamOrderdateToAttribute($value)
    {
        $this->param_order_date_from = $value;
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
    //客先
    public function getParamCustomernameAttribute()
    {
        return $this->param_customer_name;
    }

    public function setParamCustomernameAttribute($value)
    {
        $this->param_customer_name = $value;
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
    //品名
    public function getParamProductnameAttribute()
    {
        return $this->param_product_name;
    }

    public function setParamProductnameAttribute($value)
    {
        $this->param_product_name = $value;
    }
    //材質
    public function getParamQualitynameAttribute()
    {
        return $this->param_quality_name;
    }

    public function setParamQualitynameAttribute($value)
    {
        $this->param_quality_name = $value;
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
    //納期
    public function getParamSupplydateAttribute()
    {
        return $this->param_supply_date;
    }

    public function setParamSupplydateAttribute($value)
    {
        $this->param_supply_date = $value;
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
    //摘要
    public function getParamOutlinenameAttribute()
    {
        return $this->param_outline_name;
    }
    public function setParamOutlinenameAttribute($value)
    {
        $this->param_outline_name = $value;
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
    //指示書登録フラグ
    public function getParamIsUpdateAttribute()
    {
        return $this->param_is_update;
    }
    public function setParamIsUpdateAttribute($value)
    {
        $this->param_is_update = $value;
    }
    //削除フラグ
    public function getParamIsDeletedAttribute()
    {
        return $this->param_is_deleted;
    }

    public function setParamIsDeletedAttribute($value)
    {
        $this->param_is_deleted = $value;
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

    
    // ------------- メソッド --------------

    /**
     * 取得
     *
     * @return void
     */
    public function getData(){
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
            $sqlString .= "  t1.out_seq as out_seq" ;
            $sqlString .= "  , t1.order_no as order_no" ;
            $sqlString .= "  , t1.seq as seq" ;
            $sqlString .= "  , t1.order_date as order_date" ;
            $sqlString .= "  , t1.row_seq as row_seq" ;
            $sqlString .= "  , t1.drawing_no as drawing_no" ;
            $sqlString .= "  , t1.customer_name as customer_name" ;
            $sqlString .= "  , t1.model_number as model_number" ;
            $sqlString .= "  , t1.product_name as product_name" ;
            $sqlString .= "  , t1.quality_name as quality_name" ;
            $sqlString .= "  , t1.order_count as order_count" ;
            $sqlString .= "  , t1.supply_date as supply_date" ;
            $sqlString .= "  , t1.order_kingaku as order_kingaku" ;
            $sqlString .= "  , t1.outline_name as outline_name" ;
            $sqlString .= "  , t1.unit_price as unit_price" ;
            $sqlString .= "  , t1.is_update as is_update" ;
            $sqlString .= "  , t2.code as customer_code" ;
            $sqlString .= "  , t2.office_code as office_code" ;
            $sqlString .= "  , t3.code as product_code" ;
            $sqlString .= "  , t3.processes_code as processes_code" ;
            $sqlString .= "  from" ;
            $sqlString .= "  ".$this->table." as t1" ;
            $sqlString .= "  left outer join" ;
            $sqlString .= "  ".$this->table_customers." as t2 " ;
            $sqlString .= "  on" ;
            $sqlString .= "    t1.customer_name = t2.name ";
            $sqlString .= "    and t2.is_deleted = 0 " ;
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
            $sqlString .= "    t1.product_name = t3.name ";
            $sqlString .= "    and t3.is_deleted = 0 " ;
            $sqlString .= "  where" ;
            $sqlString .= "    ? = ?" ;
            if (!empty($this->param_order_date_from)) {
                $sqlString .= "    and t1.order_date >= ?" ;
            }
            if (!empty($this->param_order_date_to)) {
                $sqlString .= "    and t1.order_date <= ?" ;
            }
            if (!empty($this->param_is_update)) {
                $sqlString .= "    and t1.is_update = ?" ;
            }
            // $sqlString .= "  group by order_no, t1.order_date " ;
            $sqlString .= "  order by order_no, t1.order_date " ;
            // バインド
            $array_setBindingsStr = array();
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            if (!empty($this->param_order_date_from)) {
                $array_setBindingsStr[] = $this->param_order_date_from;
            }
            if (!empty($this->param_order_date_to)) {
                $array_setBindingsStr[] = $this->param_order_date_to;
            }
            if (!empty($this->param_is_update)) {
                $array_setBindingsStr[] = $this->param_is_update;
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
     * 受注残取得
     *
     * @return void
     */
    public function getTablelist(){
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
            $sqlString .= "  t1.out_seq as out_seq" ;
            $sqlString .= "  , t1.order_no as order_no" ;
            $sqlString .= "  , t1.seq as seq" ;
            $sqlString .= "  , t1.row_seq as row_seq" ;
            $sqlString .= "  , t1.drawing_no as drawing_no" ;
            $sqlString .= "  , date_format(t1.order_date,'%Y年%m月%d日') as order_date" ;
            $sqlString .= "  , date_format(t1.supply_date,'%Y年%m月%d日') as supply_date" ;
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
            $sqlString .= "  , t1.unit_price as unit_price" ;
            $sqlString .= "  , t1.back_order_quality_name as back_order_quality_name" ;
            $sqlString .= "  , t1.material_cost as material_cost" ;
            $sqlString .= "  , t1.material_office_code as material_office_code" ;
            $sqlString .= "  , t1.material_customer_code as material_customer_code" ;
            $sqlString .= "  , t1.heat_process as heat_process" ;
            $sqlString .= "  , t1.heat_cost as heat_cost" ;
            $sqlString .= "  , t1.outsourcing_office_code as outsourcing_office_code" ;
            $sqlString .= "  , t1.outsourcing_customer_code as outsourcing_customer_code" ;
            $sqlString .= "  , t1.outsourcing_cost as outsourcing_cost" ;
            $sqlString .= "  , t2.progress_no as progress_no" ;
            $sqlString .= "  , t2.product_processes_code as product_processes_code" ;
            $sqlString .= "  , t2.product_processes_detail_no as product_processes_detail_no" ;
            $sqlString .= "  , t2.device_code as device_code" ;
            $sqlString .= "  , t2.department_code as department_code" ;
            $sqlString .= "  , t2.users_code as users_code" ;
            $sqlString .= "  , t2.process_history_no as process_history_no" ;
            $sqlString .= "  , t2.process_time_m as process_time_m" ;
            $sqlString .= "  , t2.process_time_h as process_time_h" ;
            $sqlString .= "  , t2.setup_history_no as setup_history_no" ;
            $sqlString .= "  , t2.setup_time_m as setup_time_m" ;
            $sqlString .= "  , t2.setup_time_h as setup_time_h" ;
            $sqlString .= "  , t2.complete_date as complete_date" ;
            $sqlString .= "  , t2.qr_code as qr_code" ;
            $sqlString .= "  , t2.process_time_h as process_time_h" ;
            $sqlString .= "  , t3.name as office_name" ;
            $sqlString .= "  , t4.name as customer_name" ;
            $sqlString .= "  , t5.name as product_name" ;
            $sqlString .= "  , t6.name as material_office_name" ;
            $sqlString .= "  , t7.name as material_customer_name" ;
            $sqlString .= "  , t8.name as outsourcing_office_name" ;
            $sqlString .= "  , t9.name as outsourcing_customer_name" ;
            $sqlString .= "  , t10.name as product_process_name" ;
            $sqlString .= "  , t11.name as device_name" ;
            $sqlString .= "  , t12.name as department_name" ;
            $sqlString .= "  , t13.name as user_name" ;
            $sqlString .= "  from" ;
            $sqlString .= "  ".$this->table_progress_headers." as t1" ;
            $sqlString .= "    inner join" ;
            $sqlString .= "      ".$this->table_progress_details." as t2" ;
            $sqlString .= "    on" ;
            $sqlString .= "      t1.order_no = t2.order_no" ;
            $sqlString .= "      and t1.seq = t2.seq" ;
            $sqlString .= "      and t1.is_deleted = 0" ;
            $sqlString .= "      and t2.is_deleted = 0" ;
            $sqlString .= "    left outer join" ;
            $sqlString .= "      ".$this->table_offices." as t3 " ;
            $sqlString .= "    on" ;
            $sqlString .= "      t1.office_code = t3.code ";
            $sqlString .= "      and t3.is_deleted = 0 " ;
            $sqlString .= "    left outer join " ;
            $sqlString .= "      ".$this->table_customers." as t4 " ;
            $sqlString .= "    on" ;
            $sqlString .= "      t1.office_code = t4.office_code ";
            $sqlString .= "      and t1.customer_code = t4.code ";
            $sqlString .= "      and t4.is_deleted = 0 " ;
            $sqlString .= "    left outer join " ;
            $sqlString .= "      ".$this->table_products." as t5 " ;
            $sqlString .= "    on" ;
            $sqlString .= "      t1.product_code = t5.code ";
            $sqlString .= "      and t1.processes_code = t5.processes_code ";
            $sqlString .= "      and t5.is_deleted = 0 " ;
            $sqlString .= "    left outer join" ;
            $sqlString .= "      ".$this->table_offices." as t6 " ;
            $sqlString .= "    on" ;
            $sqlString .= "      t1.material_office_code = t6.code ";
            $sqlString .= "      and t6.is_deleted = 0"  ;
            $sqlString .= "    left outer join " ;
            $sqlString .= "      ".$this->table_customers." as t7 " ;
            $sqlString .= "    on" ;
            $sqlString .= "      t1.material_office_code = t7.office_code ";
            $sqlString .= "      and t1.material_customer_code = t7.code ";
            $sqlString .= "      and t7.is_deleted = 0 " ;
            $sqlString .= "    left outer join" ;
            $sqlString .= "      ".$this->table_offices." as t8 " ;
            $sqlString .= "    on" ;
            $sqlString .= "      t1.outsourcing_office_code = t8.code ";
            $sqlString .= "      and t8.is_deleted = 0 " ;
            $sqlString .= "    left outer join " ;
            $sqlString .= "      ".$this->table_customers." as t9 " ;
            $sqlString .= "    on" ;
            $sqlString .= "      t1.outsourcing_office_code = t9.office_code ";
            $sqlString .= "      and t1.outsourcing_customer_code = t9.code ";
            $sqlString .= "      and t9.is_deleted = 0 " ;
            $sqlString .= "    left outer join" ;
            $sqlString .= "      ".$this->table_product_processes." as t10 " ;
            $sqlString .= "    on" ;
            $sqlString .= "      t2.product_processes_code = t10.code ";
            $sqlString .= "      and t2.product_processes_detail_no = t10.detail_no ";
            $sqlString .= "      and t10.is_deleted = 0 " ;
            $sqlString .= "    left outer join " ;
            $sqlString .= "      ".$this->table_devices." as t11 " ;
            $sqlString .= "    on" ;
            $sqlString .= "      t2.device_code = t11.code ";
            $sqlString .= "      and t11.is_deleted = 0 " ;
            $sqlString .= "    left outer join " ;
            $sqlString .= "      ".$this->table_departments." as t12 " ;
            $sqlString .= "    on" ;
            $sqlString .= "      t2.department_code = t12.code ";
            $sqlString .= "      and t12.is_deleted = 0 " ;
            $sqlString .= "    left outer join " ;
            $sqlString .= "      ".$this->table_users." as t13 " ;
            $sqlString .= "    on" ;
            $sqlString .= "      t2.department_code = t13.department_code ";
            $sqlString .= "      and t2.users_code = t13.code ";
            $sqlString .= "      and t13.is_deleted = 0 " ;
            $sqlString .= "  where" ;
            $sqlString .= "    ? = ?" ;
            if (!empty($this->param_order_date_from)) {
                $sqlString .= "    and t1.supply_date >= ?" ;
            }
            if (!empty($this->param_order_date_to)) {
                $sqlString .= "    and t1.supply_date <= ?" ;
            }
            $sqlString .= "group by" ;
            $sqlString .= "  t1.supply_date" ;
            $sqlString .= " , t1.order_no" ;
            $sqlString .= "order by" ;
            $sqlString .= "  t1.supply_date asc" ;
            $sqlString .= " , t1.order_no asc" ;
            $sqlString .= " , t2.progress_no asc" ;
            // バインド
            $array_setBindingsStr = array();
            $array_setBindingsStr[] = 1;
            $array_setBindingsStr[] = 1;
            if (!empty($params_target_from_date)) {
                $array_setBindingsStr[] = $params_target_from_date;
            }
            if (!empty($params_target_to_date)) {
                $array_setBindingsStr[] = $params_target_to_date;
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
     * 指示書登録済み確認
     *
     * @return void
     */
    public function getIsUpdate(){
        $this->array_messagedata = array();
        $result = true;
        try {
            $mainQuery = DB::table($this->table)->whereRaw(' 1 = 1 ');
            if (!empty($param_order_date_from)) {
                $mainQuery->where('order_date', '>=', $this->param_order_date_from);
            }
            if (!empty($param_order_date_to)) {
                $mainQuery->where('order_date', '<=', $this->param_order_date_to);
            }
            // 数値=0も判定するためisset
            if (isset($this->param_is_update)) {
                $mainQuery->where('is_update', '=', $this->param_is_update);
            }
            $result = $mainQuery->exists();

            return $result;
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
     * 受注残登録
     *      存在しない場合のみ
     *
     * @return void
     */
    public function insert(){
        try {
            DB::table($this->table)->insert(
                [
                    'out_seq' => $this->out_seq,
                    'order_no' => $this->order_no,
                    'seq' => $this->seq,
                    'order_date' => $this->order_date,
                    'row_seq' => $this->row_seq,
                    'drawing_no' => $this->drawing_no,
                    'customer_name' => $this->customer_name,
                    'model_number' => $this->model_number,
                    'product_name' => $this->product_name,
                    'quality_name' => $this->quality_name,
                    'order_count' => $this->order_count,
                    'supply_date' => $this->supply_date,
                    'order_kingaku' => $this->order_kingaku,
                    'outline_name' => $this->outline_name,
                    'unit_price' => $this->unit_price,
                    'is_update' => $this->is_update,
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
     * 受注残更新
     *      指示書登録フラグの更新
     *
     * @return void
     */
    public function updateIsupdate(){
        try {
            $mainQuery = DB::table($this->table);
            if (!empty($this->param_order_no)) {
                $mainQuery->where('order_no', '=', $this->param_order_no);
            }
            if (!empty($this->param_seq)) {
                $mainQuery->where('seq', '=', $this->param_seq);
            }
            $mainQuery->update(['is_update' => $this->is_update]);
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
    
}
