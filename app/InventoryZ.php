<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class InventoryZ extends Model
{
    protected $table = 'inventory_z';

    private $id;	
    private $charge;		    	// 担当
    private $order_no;		    	// 受注番号
    private $company_name;		    // 会社名
    private $company_id;	    	// 会社ID
    private $product_name;		    // 商品名
    private $product_id;	    	// 商品ID
    private $unit;				    // 単位
    private $quantity;		    	// 入数
    private $supply_day;		    // 納入日
    private $supply_quantity;	    // 納入数
    private $order_day;		    	// 発注日
    private $order_quantity;	    // 発注数
    private $now_inventory;		    // 現在在庫
    private $nbox;				    // 箱数
    private $order_address;	    	// 発注先
    private $unit_price;			// 単価
    private $total;				    // 合計
    private $remarks;		    	// 備考
    private $note;				    // メモ/ノート
    private $status;				// ステータス--最新/履歴
    private $order_info;			// 発注情報--預かり/在庫
    private $other1;				// その他マーク
    private $marks;                 // マーク/グループ
    private $created_user;		    // 作成ユーザー
    private $updated_user;		    // 修正ユーザー
    private $created_at;			// 作成日時
    private $updated_at;			// 修正日時
    private $is_deleted;			// 削除フラグ
    
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
    // 納入日
    public function getSupplydayAttribute(){ return $this->supply_day;}
    public function setSupplydayAttribute($value){  $this->supply_day = $value;}
    // 納入数
    public function getSupplyquantityAttribute(){ return $this->supply_quantity;}
    public function setSupplyquantityAttribute($value){  $this->supply_quantity = $value;}
    // 発注日
    public function getOrderdayAttribute(){ return $this->order_day;}
    public function setOrderdayAttribute($value){  $this->order_day = $value;}
    // 発注数
    public function getOrderquantityAttribute(){ return $this->order_quantity;}
    public function setOrderquantityAttribute($value){  $this->order_quantity = $value;}
    // 現在在庫
    public function getNowinventoryAttribute(){ return $this->now_inventory;}
    public function setNowinventoryAttribute($value){  $this->now_inventory = $value;}
    // 箱数
    public function getNboxAttribute(){ return $this->nbox;}
    public function setNboxAttribute($value){  $this->nbox = $value;}
    // 発注先
    public function getOrderaddressAttribute(){ return $this->order_address;}
    public function setOrderaddressAttribute($value){  $this->order_address = $value;}
    // 単価
    public function getUnitpriceAttribute(){ return $this->unit_price;}
    public function setUnitpriceAttribute($value){  $this->unit_price = $value;}
    // 合計
    public function getTotalAttribute(){ return $this->total;}
    public function setTotalAttribute($value){  $this->total = $value;}
    // 備考
    public function getRemarksAttribute(){ return $this->remarks;}
    public function setRemarksAttribute($value){  $this->remarks = $value;}
    // メモ/ノート
    public function getNoteAttribute(){ return $this->note;}
    public function setNoteAttribute($value){  $this->note = $value;}
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
    private $params_order_info;
    public function getParamOrderinfoAttribute(){ return $this->params_order_info;}
    public function setParamOrderinfoAttribute($value){  $this->params_order_info = $value;}
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

    // 納入日
    private $param_supply_day;
    public function getParamSupplydayAttribute(){ return $this->param_supply_day;}
    public function setParamSupplydayAttribute($value){  $this->param_supply_day = $value;}
    // 発注日
    private $param_order_day;
    public function getParamOrderdayAttribute(){ return $this->param_order_day;}
    public function setParamOrderdayAttribute($value){  $this->param_order_day = $value;}
    // 商品名
    private $param_product_name;
    public function getParamProductnameAttribute(){ return $this->param_product_name;}
    public function setParamProductnameAttribute($value){  $this->param_product_name = $value;}


    // ------------- メソッド --------------

    /**
     * 登録
     *
     * @return void
     */
    public function insertDataZ($upkind){
        try {
            $re_data = [];
            if($upkind == 3) {
                $this->company_id = DB::table($this->table)->max('company_id') + 1;
                $this->product_id = DB::table($this->table)->max('product_id') + 1;
                $this->order_info = 'z';
            }
            if($upkind == 2) {
                $this->product_id = DB::table($this->table)->max('product_id') + 1;
                $this->order_info = 'z';
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
                    'supply_day' => $this->supply_day,
                    'supply_quantity' => $this->supply_quantity,
                    'order_day' => $this->order_day,
                    'order_quantity' => $this->order_quantity,
                    'now_inventory' => $this->now_inventory,
                    'nbox' => $this->nbox,
                    'order_address' => $this->order_address,
                    'unit_price' => $this->unit_price,
                    'total' => $this->total,
                    'remarks' => $this->remarks,
                    'note' => $this->note,
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
            //Log::info("insertDataZ in id = ".$id);
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
    public function updateDataZ($upkind){
        try {
            if($upkind == 4)    {
                //削除マーク
                DB::table($this->table)
                ->where('id', $this->id)
                ->update([
                    'status' => 'del',
                ]);

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
                    'supply_day' => $this->supply_day,
                    'supply_quantity' => $this->supply_quantity,
                    'order_day' => $this->order_day,
                    'order_quantity' => $this->order_quantity,
                    'now_inventory' => $this->now_inventory,
                    'nbox' => $this->nbox,
                    'order_address' => $this->order_address,
                    'unit_price' => $this->unit_price,
                    'total' => $this->total,
                    'remarks' => $this->remarks,
                    'note' => $this->note,
                    'status' => $this->status,
                    'order_info' => $this->order_info,
                    'other1' => $this->other1,
                    'marks' => $this->marks,
                    'updated_user'=>$this->updated_user,
                    'updated_at'=>$this->updated_at,
                    'is_deleted' =>$this->is_deleted
                ]);
            }
            $re_data['id'] = $this->id;
            $re_data['product_id'] = $this->product_id;
            $re_data['product_name'] = $this->product_name;
            return $re_data;


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
    public function getDataInvZ(){
        //$message = "ログ出力 getDataInvA";
        //Log::info("this->param_edit_id -- ".$this->param_edit_id);
        //Log::info("this->param_product_id -- ".$this->param_product_id);

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
                'supply_day',
                'supply_quantity',
                'order_day',
                'order_quantity',
                'now_inventory',
                'nbox',
                'order_address',
                'unit_price',
                'total',
                'remarks',
                'note',
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
            $data->selectRaw('
                FLOOR(now_inventory / quantity)  as calc_nbox,
                (now_inventory mod quantity)  as calc_nbox_mod
            ');


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
            elseif(isset($this->param_supply_day)){
                Log::info("isset this->param_supply_day -- ".$this->param_supply_day);
                if($this->param_supply_day == 1) {
                    $data->orderBy('supply_day');
                }
                if($this->param_supply_day == 2) {
                    $data->orderBy('supply_day', 'DESC');
                }
            }
            elseif(isset($this->param_order_day)){
                Log::info("isset this->param_order_day -- ".$this->param_order_day);
                if($this->param_order_day == 1) {
                    $data->orderBy('order_day');
                }
                if($this->param_order_day == 2) {
                    $data->orderBy('order_day', 'DESC');
                }
            }

            $result = $data
            ->get();

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
     * 取得totals
     *
     * @return void
     */
    public function getDataInvtotal(){
        //$message = "ログ出力 getDataMM";
        //Log::info("this->param_edit_id -- ".$this->param_edit_id);
        //Log::info("this->param_product_code -- ".$this->param_product_code);

        try {

			

            $datasum = DB::table($this->table)
			->selectRaw('
			sum(total) as totals
			');

            $datasum->where('status','newest')
			->orderBy('id', 'DESC');

            $result = $datasum
            ->get();

			

			
			//$result = array_merge($result1, $result2);


            $sqlString = "";
            $sqlString .= "select ";
            $sqlString .= "sum(total) as totals";
            $sqlString .= " from ";
            $sqlString .= " ".$this->table." ";
            $sqlString .= " where ";
			$sqlString .= " status = 'newest' ";
            /*
            if (!empty($this->param_marks)) {
                $sqlString .= " AND marks = ? ";
            }
            if (!empty($this->param_is_deleted)) {
                $sqlString .= " AND is_deleted = ? ";
            }
            // バインド
            $array_setBindingsStr = array();
            if (!empty($this->param_marks)) {
                $array_setBindingsStr[] = $this->param_marks;
            }
            if(!empty($this->param_is_deleted)){
                $array_setBindingsStr[] = $this->param_is_deleted;
            }
			else {
                $array_setBindingsStr[] = 0;
			}
            
            $data2 = DB::select($sqlString, $array_setBindingsStr);
            */


			//$sqlString3 = "select sum(total) as totals from matmanage where status = 'newest' and marks = 'b'";
            //$data3 = DB::select($sqlString3);

            //$result = array_merge($data1, $data2);

            //$result = $data2;

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
    public function getSearchZ(){

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
                'supply_day',
                'supply_quantity',
                'order_day',
                'order_quantity',
                'now_inventory',
                'nbox',
                'order_address',
                'unit_price',
                'total',
                'remarks',
                'note',
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
                $result = "";
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
     * 検索SEARCH取得Total
     *
     * @return void
     */
    public function getSearchTotal(){
		//Log::info("getSearchTotal in -- this->param_department = ".$this->param_department." / this->param_marks = ".$this->param_marks);

        try {
            $result = "";
			$data = DB::table($this->table)
            ->select(DB::raw('SUM(total) as total_s'));

            if(!empty($this->param_order_no)){
                $data->where('order_no', $this->param_order_no)
                //->where('status','newest')
                ->orderBy('id', 'DESC');
            
                $result = $data
                ->get();
            }
            if(!empty($this->param_product_name)){
                $str = "%".$this->param_product_name."%";
				//if(empty($this->param_shistory)) $matchThese['status'] = 'newest';
                $matchThese['status'] = 'newest';
				$matchThese['is_deleted'] = 0;
                Log::info("getSearchA this->param_product_name -- ".$str);
                $data->where('product_name','LIKE', $str)
                ->where($matchThese)
                ->orderBy('id');
            
                $result = $data
                ->get();
            }
            if(!empty($this->param_company_name)){
                $str = "%".$this->param_company_name."%";
                //Log::info("getSearchA this->param_company_name -- ".$str);
                $data->where('company_name','LIKE', $str)
                ->where('status','newest')
                ->orderBy('id');
            
                $result = $data
                ->get();
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
     * miniデータ取得
     *
     * @return void
     */
    public function getDataMiniInvZ(){

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
            //$mainQuery->where('account_id',$this->param_account_id);
            $is_exists = $mainQuery->where('status',0)
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
     * 削除 // 未使用
     *
     * @return void
     */
    public function delDataZxxx(){
        try {
            $mainQuery = DB::table($this->table);
            //$mainQuery->where('account_id',$this->param_account_id);
            $result = $mainQuery->where('status','del')->delete();
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
