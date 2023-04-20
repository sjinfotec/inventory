<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class InventoryA extends Model
{
    protected $table = 'inventory_a';

    private $id;
    private $charge;              // 担当
    private $order_no;            // 受注番号
    private $company_name;        // 会社名
    private $company_id;          // 会社ID
    private $product_name;        // 商品名
    private $product_id;          // 商品ID
    private $unit;                // 単位
    private $quantity;            // 入数
    private $receipt_day;         // 入庫日
    private $order_quantity;      // 発注数
    private $receipt;             // 入庫数
    private $delivery_day;        // 出庫日
    private $delivery;            // 出庫数
    private $now_inventory;       // 現在在庫
    private $nbox;                // 箱数
    private $dnum;                // 出庫No
    private $rnum;                // 残りNo
    private $shipping_address;    // 発送先
    private $remarks;             // 備考
    private $status;              // ステータス--最新/履歴
    private $order_info;          // 発注情報--預かり/在庫
    private $other1;              // その他マーク
    private $marks;               // マーク/グループ
    private $created_user;        // 作成ユーザー
    private $updated_user;        // 修正ユーザー
    private $created_at;          // 作成日時
    private $updated_at;          // 修正日時
    private $is_deleted;          // 削除フラグ
    
    // ID
    public function getIdAttribute(){ return $this->id;}
    public function setIdAttribute($value){  $this->id = $value;}
    // 担当
    public function getChargeAttribute(){ return $this->charge;}
    public function setChargeAttribute($value){  $this->charge = $value;}
    // 受注番号
    public function getOrdernoAttribute(){ return $this->order_no;}
    public function setOrdernoAttribute($value){  $this->order_no = $value;}
    // 会社名
    public function getCompanynameAttribute(){ return $this->company_name;}
    public function setCompanynameAttribute($value){  $this->company_name = $value;}
    // 会社ID
    public function getCompanyidAttribute(){ return $this->company_id;}
    public function setCompanyidAttribute($value){  $this->company_id = $value;}
    // 商品名
    public function getProductnameAttribute(){ return $this->product_name;}
    public function setProductnameAttribute($value){  $this->product_name = $value;}
    // 商品ID
    public function getProductidAttribute(){ return $this->product_id;}
    public function setProductidAttribute($value){  $this->product_id = $value;}
    // 単位
    public function getUnitAttribute(){ return $this->unit;}
    public function setUnitAttribute($value){  $this->unit = $value;}
    // 入数
    public function getQuantityAttribute(){ return $this->quantity;}
    public function setQuantityAttribute($value){  $this->quantity = $value;}
    // 入庫日
    public function getReceiptdayAttribute(){ return $this->receipt_day;}
    public function setReceiptdayAttribute($value){  $this->receipt_day = $value;}
    // 発注数
    public function getOrderquantityAttribute(){ return $this->order_quantity;}
    public function setOrderquantityAttribute($value){  $this->order_quantity = $value;}
    // 入庫数
    public function getReceiptAttribute(){ return $this->receipt;}
    public function setReceiptAttribute($value){  $this->receipt = $value;}
    // 出庫日
    public function getDeliverydayAttribute(){ return $this->delivery_day;}
    public function setDeliverydayAttribute($value){  $this->delivery_day = $value;}
    // 出庫数
    public function getDeliveryAttribute(){ return $this->delivery;}
    public function setDeliveryAttribute($value){  $this->delivery = $value;}
    // 現在在庫
    public function getNowinventoryAttribute(){ return $this->now_inventory;}
    public function setNowinventoryAttribute($value){  $this->now_inventory = $value;}
    // 箱数
    public function getNboxAttribute(){ return $this->nbox;}
    public function setNboxAttribute($value){  $this->nbox = $value;}
    // 出庫No
    public function getDnumAttribute(){ return $this->dnum;}
    public function setDnumAttribute($value){  $this->dnum = $value;}
    // 残りNo
    public function getRnumAttribute(){ return $this->rnum;}
    public function setRnumAttribute($value){  $this->rnum = $value;}
    // 発送先
    public function getShippingaddressAttribute(){ return $this->shipping_address;}
    public function setShippingaddressAttribute($value){  $this->shipping_address = $value;}
    // 備考
    public function getRemarksAttribute(){ return $this->remarks;}
    public function setRemarksAttribute($value){  $this->remarks = $value;}
    // ステータス--最新/履歴
    public function getStatusAttribute(){ return $this->status;}
    public function setStatusAttribute($value){  $this->status = $value;}
    // 発注情報--預かり/在庫
    public function getOrderinfoAttribute(){ return $this->order_info;}
    public function setOrderinfoAttribute($value){  $this->order_info = $value;}
    // その他マーク
    public function getOther1Attribute(){ return $this->other1;}
    public function setOther1Attribute($value){  $this->other1 = $value;}
    // マーク/グループ
    public function getMarksAttribute(){ return $this->marks;}
    public function setMarksAttribute($value){  $this->marks = $value;}
    // 作成ユーザー
    public function getCreateduserAttribute(){ return $this->created_user;}
    public function setCreateduserAttribute($value){  $this->created_user = $value;}
    // 修正ユーザー
    public function getUpdateduserAttribute(){ return $this->updated_user;}
    public function setUpdateduserAttribute($value){  $this->updated_user = $value;}
    // 作成日時
    public function getCreatedatAttribute(){ return $this->created_at;}
    public function setCreatedatAttribute($value){  $this->created_at = $value;}
    // 修正日時
    public function getUpdatedatAttribute(){ return $this->updated_at;}
    public function setUpdatedatAttribute($value){  $this->updated_at = $value;}
    // 削除フラグ
    public function getIsdeletedAttribute(){ return $this->is_deleted;}
    public function setIsdeletedAttribute($value){  $this->is_deleted = $value;}
    // 
    //public function getAttribute(){ return $this->;}
    //public function setAttribute($value){  $this-> = $value;}


    // ------------- implements --------------
 
    // エディットID
    private $param_edit_id;
    public function getParamEditidAttribute(){ return $this->param_edit_id;}
    public function setParamEditidAttribute($value){  $this->param_edit_id = $value;}
    // 商品ID
    private $param_product_id;
    public function getParamProductidAttribute(){ return $this->param_product_id;}
    public function setParamProductidAttribute($value){  $this->param_product_id = $value;}
    // ステータス
    private $param_status;
    public function getParamStatusAttribute(){ return $this->param_status;}
    public function setParamStatusAttribute($value){  $this->param_status = $value;}

    // ------------- 順序・正逆・検索 --------------
    // 発注情報
    private $param_order_info;
    public function getParamOrderinfoAttribute(){ return $this->param_order_info;}
    public function setParamOrderinfoAttribute($value){  $this->param_order_info = $value;}
    // 受注番号
    private $param_order_no;
    public function getParamOrdernoAttribute(){ return $this->param_order_no;}
    public function setParamOrdernoAttribute($value){  $this->param_order_no = $value;}
    // 会社ID
    private $param_company_id;
    public function getParamCompanyidAttribute(){ return $this->param_company_id;}
    public function setParamCompanyidAttribute($value){  $this->param_company_id = $value;}
    // 商品ID 2
    private $param_product_id2;
    public function getParamProductid2Attribute(){ return $this->param_product_id2;}
    public function setParamProductid2Attribute($value){  $this->param_product_id2 = $value;}
    // 正逆
    private $param_orderfr;
    public function getParamOrderfrAttribute(){ return $this->param_orderfr;}
    public function setParamOrderfrAttribute($value){  $this->param_orderfr = $value;}
    // 会社名
    private $param_company_name;
    public function getParamCompanynameAttribute(){ return $this->param_company_name;}
    public function setParamCompanynameAttribute($value){  $this->param_company_name = $value;}
    // 商品名
    private $param_product_name;
    public function getParamProductnameAttribute(){ return $this->param_product_name;}
    public function setParamProductnameAttribute($value){  $this->param_product_name = $value;}


    // 入庫日
    private $param_receipt_day;
    public function getParamReceiptdayAttribute(){ return $this->param_receipt_day;}
    public function setParamReceiptdayAttribute($value){  $this->param_receipt_day = $value;}
    // 出庫日
    private $param_delivery_day;
    public function getParamDeliverydayAttribute(){ return $this->param_delivery_day;}
    public function setParamDeliverydayAttribute($value){  $this->param_delivery_day = $value;}
    




    // ログインユーザーのアカウント 未使用
    private $param_account_id;
    public function getParamAccountidAttribute(){ return $this->param_account_id;}
    public function setParamAccountidAttribute($value){  $this->param_account_id = $value;}

    // ------------- メソッド --------------

    /**
     * 登録
     *
     * @return void
     */
    public function insertDataA($upkind){
        try {
            $re_data = [];
            if($upkind == 3) {
            $this->company_id = DB::table($this->table)->max('company_id') + 1;
            $this->product_id = DB::table($this->table)->max('product_id') + 1;
            $this->order_info = 'a';
            //$this->created_user = 'system';
            }
            if($upkind == 2) {
                $this->product_id = DB::table($this->table)->max('product_id') + 1;
                $this->order_info = 'a';
                //$this->created_user = 'system';
            }
            $this->now_inventory = isset($this->now_inventory) ? $this->now_inventory : "";
            $this->nbox = isset($this->nbox) ? $this->nbox : "";
    
            $id = DB::table($this->table)->insertGetId(
                [
                    'charge' => $this->charge,
                    'order_no' => $this->order_no,
                    'company_name' => $this->company_name,
                    'company_id' => $this->company_id,
                    'product_name' => $this->product_name,
                    'product_id' => $this->product_id,
                    'unit' => $this->unit,
                    'quantity' => $this->quantity,
                    'receipt_day' => $this->receipt_day,
                    'order_quantity' => $this->order_quantity,
                    'receipt' => $this->receipt,
                    'delivery_day' => $this->delivery_day,
                    'delivery' => $this->delivery,
                    'now_inventory' => $this->now_inventory,
                    'nbox' => $this->nbox,
                    'dnum' => $this->dnum,
                    'rnum' => $this->rnum,
                    'shipping_address' => $this->shipping_address,
                    'remarks' => $this->remarks,
                    'status' => 'newest',
                    'order_info' => $this->order_info,
                    'other1' => $this->other1,
                    'marks' => $this->marks,
                    'created_user' => $this->created_user,
                    'created_at' => $this->created_at,
                    'updated_at' => NULL
    
                ]
            );

            if($upkind == 1){
                DB::table($this->table)
                ->where('id', $this->id)
                ->update([
                    'status' => '',
                ]);

            }
            $re_data['id'] = $id;
            $re_data['product_id'] = $this->product_id;
            $re_data['product_name'] = $this->product_name;
            return $re_data;

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
    public function updateDataA($upkind){
        try {
            if($upkind == 4)    {
                //削除マーク
                DB::table($this->table)
                ->where('id', $this->id)
                ->update([
                    'status' => 'del',
                ]);
                $re_data['id'] = $this->id;
                $re_data['product_id'] = $this->product_id;
                $re_data['product_name'] = $this->product_name;
                return $re_data;

            }
            elseif($upkind == 5)    {
                //削除マーク→newest（戻す）
                DB::table($this->table)
                ->where('id', $this->id)
                ->update([
                    'status' => 'newest',
                ]);
                $re_data['id'] = $this->id;
                $re_data['product_id'] = $this->product_id;
                $re_data['product_name'] = $this->product_name;
                return $re_data;

            }
            else {

            DB::table($this->table)
                ->where('id', $this->id)
                ->update([
                    'charge' => $this->charge,
                    'order_no' => $this->order_no,
                    'company_name' => $this->company_name,
                    'company_id' => $this->company_id,
                    'product_name' => $this->product_name,
                    'product_id' => $this->product_id,
                    'unit' => $this->unit,
                    'quantity' => $this->quantity,
                    'receipt_day' => $this->receipt_day,
                    'order_quantity' => $this->order_quantity,
                    'receipt' => $this->receipt,
                    'delivery_day' => $this->delivery_day,
                    'delivery' => $this->delivery,
                    'now_inventory' => $this->now_inventory,
                    'nbox' => $this->nbox,
                    'dnum' => $this->dnum,
                    'rnum' => $this->rnum,
                    'shipping_address' => $this->shipping_address,
                    'remarks' => $this->remarks,
                    'status' => $this->status,
                    'order_info' => $this->order_info,
                    'other1' => $this->other1,
                    'marks' => $this->marks,
                    'updated_user'=>$this->updated_user,
                    'updated_at'=>$this->updated_at,
                    'is_deleted' =>$this->is_deleted
                ]);

                $re_data['id'] = $this->id;
                $re_data['product_id'] = $this->product_id;
                $re_data['product_name'] = $this->product_name;
                return $re_data;
            }

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
    public function getDataInvA(){
        $message = "ログ出力 getDataInvA";
        //echo '<pre>' . var_export($message, true) . '</pre>';
        //Log::debug("debug --".$message);
        /*
        Log::info("this->param_edit_id -- ".$this->param_edit_id);
        Log::info("this->param_product_id -- ".$this->param_product_id);
        Log::info("this->param_order_info -- ".$this->param_order_info);
        Log::info("this->param_order_no -- ".$this->param_order_no);
        Log::info("this->param_company_id -- ".$this->param_company_id);
        Log::info("this->param_product_id2 -- ".$this->param_product_id2);
        Log::info("this->param_orderfr -- ".$this->param_orderfr);
        */

        try {
            $data = DB::table($this->table)
            ->select(

                'id',
                'charge',
                'order_no',
                'company_name',
                'company_id',
                'product_name',
                'product_id',
                'unit',
                'quantity',
                'receipt_day',
                'order_quantity',
                'receipt',
                'delivery_day',
                'delivery',
                'now_inventory',
                'nbox',
                'dnum',
                'rnum',
                'shipping_address',
                'remarks',
                'status',
                'order_info',
                'other1',
                'marks',
                'created_user',
                'updated_user',
                'created_at',
                'updated_at',
                'is_deleted'

            );
            //$data->where('account_id',$this->param_account_id);
            if(!empty($this->param_edit_id)){
                $data->where('id',$this->param_edit_id);
            }
            elseif(!empty($this->param_product_id)){
                $data->where('product_id',$this->param_product_id)
                ->orderBy('id', 'DESC');
            }
            elseif(!empty($this->param_status)){
                $data->where('status',$this->param_status)
                ->orderBy('id');
            }
            else {
                $data->where('status','newest')
                ->orderBy('id', 'DESC');
            }
            // 順番変更 正順逆順
            if(isset($this->param_order_no)){
                Log::info("isset this->param_order_no -- ".$this->param_order_no);
                if($this->param_order_no == 1) {
                    $data->orderBy('order_no');
                }
                if($this->param_order_no == 2) {
                    $data->orderBy('order_no', 'DESC');
                }
            }
            elseif(isset($this->param_company_id)){
                Log::info("isset this->param_company_id -- ".$this->param_company_id);
                if($this->param_company_id == 1) {
                    $data->orderBy('company_name');
                }
                if($this->param_company_id == 2) {
                    $data->orderBy('company_name', 'DESC');
                }
            }
            elseif(isset($this->param_product_id2)){
                Log::info("isset this->param_product_id2 -- ".$this->param_product_id2);
                if($this->param_product_id2 == 1) {
                    $data->orderBy('product_name');
                }
                if($this->param_product_id2 == 2) {
                    $data->orderBy('product_name', 'DESC');
                }
            }
            if(isset($this->param_receipt_day)){
                Log::info("isset this->param_receipt_day -- ".$this->param_receipt_day);
                if($this->param_receipt_day == 1) {
                    $data->orderBy('receipt_day');
                }
                if($this->param_receipt_day == 2) {
                    $data->orderBy('receipt_day', 'DESC');
                }
            }
            if(isset($this->param_delivery_day)){
                Log::info("isset this->param_delivery_day -- ".$this->param_delivery_day);
                if($this->param_delivery_day == 1) {
                    $data->orderBy('delivery_day');
                }
                if($this->param_delivery_day == 2) {
                    $data->orderBy('delivery_day', 'DESC');
                }
            }

            
            $result = $data
            //->where('status','newest')
            
            ->get();
            //$result = $data->get();

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
     * 検索SEARCH取得
     *
     * @return void
     */
    public function getSearchA(){

        try {
            $result = "";
            $data = DB::table($this->table)
            ->select(

                'id',
                'charge',
                'order_no',
                'company_name',
                'company_id',
                'product_name',
                'product_id',
                'unit',
                'quantity',
                'receipt_day',
                'order_quantity',
                'receipt',
                'delivery_day',
                'delivery',
                'now_inventory',
                'nbox',
                'dnum',
                'rnum',
                'shipping_address',
                'remarks',
                'status',
                'order_info',
                'other1',
                'marks',
                'created_user',
                'updated_user',
                'created_at',
                'updated_at',
                'is_deleted'

            );
            if(!empty($this->param_order_no)){
                //Log::info("getSearchA this->params_order_no -- ".$this->params_order_no);
                //Log::info("getSearchA this->param_order_no -- ".$this->param_order_no);
                $data->where('order_no', $this->param_order_no)
                //->where('status','newest')
                ->orderBy('id', 'DESC');
            
                $result = $data
                ->get();
            }
            if(!empty($this->param_product_name)){
                $str = "%".$this->param_product_name."%";
				//if(empty($this->param_shistory)) $matchThese['status'] = 'newest';
				$matchThese['is_deleted'] = 0;
                Log::info("getSearchA this->param_product_name -- ".$str);
                $data->where('product_name','LIKE', $str)
                ->where($matchThese)
                ->orderBy('id', 'DESC');
            
                $result = $data
                ->get();
            }
            if(!empty($this->param_company_name)){
                $str = "%".$this->param_company_name."%";
                //Log::info("getSearchA this->param_company_name -- ".$str);
                $data->where('company_name','LIKE', $str)
                ->where('status','newest')
                ->orderBy('id', 'DESC');
            
                $result = $data
                ->get();
            }

            /*
            if ($result->isEmpty()) {
                $result = "";param_product_name
            } 
            */
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
     * miniデータ取得
     *
     * @return void
     */
    public function getDataMiniInvA(){

        try {
            $data = DB::table($this->table)
            ->select(
                [
                    'id as inv_id',
                    'order_no',
                    'company_name',
                    'company_id',
                    'product_name',
                    'product_id',
                    'unit',
                    'quantity',
                    'now_inventory',
                    'nbox',
                    'status',
                    'order_info',
                    'created_user',
                    'created_at',
                ]
            );
                $data->where('status','newest');
                //->orderBy('id', 'DESC');
                
            
                $result = $data
                ->get();
            

            if ($result->isEmpty()) {
                $result = "";
            } 
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
     * 存在チェック
     *
     * @return boolean
     */
    public function isExistsInfo(){
        try {
            $mainQuery = DB::table($this->table);
            $mainQuery->where('account_id',$this->param_account_id);
            $is_exists = $mainQuery->where('is_deleted',0)
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
     * 削除
     *
     * @return void
     */
    public function delData($delkind){
        try {
            $mainQuery = DB::table($this->table);
            //$mainQuery->where('account_id',$this->param_account_id);
            //$result = $mainQuery->where('status','del')->delete();
			if($delkind === "one") {
	            $mainQuery->where('id', $this->id);
			}
			elseif($delkind === "all") {
	            $mainQuery->where('product_id', $this->product_id);
			}
			$result = $mainQuery
            ->delete();
			$re_data['id'] = $this->id;
            return $re_data;

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
