<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class StockA extends Model
{
    protected $table = 'stock_a';
    protected $table_inva = 'inventory_a';
    protected $table_invz = 'inventory_z';

    private $id;
    private $inv_id;              // inventoryID
    private $charge;              // 担当
    private $order_no;            // 受注番号
    private $company_name;        // 会社名
    private $company_id;          // 会社ID
    private $product_name;        // 商品名
    private $product_id;          // 商品ID
    private $unit;                // 単位
    private $quantity;            // 入数
    private $now_inventory;       // 現在在庫
    private $nbox;                // 箱数
    private $stock_now_inventory;       // 棚卸在庫
    private $stock_nbox;                // 棚卸箱数
	private $unit_price;				// 単価
	private $total;	        			// 合計
	private $remarks;		    		// 備考
    private $status;              // ステータス
    private $order_info;          // 発注情報
    private $stock_month;         // 棚卸月
    private $created_user;        // 作成ユーザー
    private $updated_user;        // 修正ユーザー
    private $created_at;          // 作成日時
    private $updated_at;          // 修正日時
    
    // ID
    public function getIdAttribute(){ return $this->id;}
    public function setIdAttribute($value){  $this->id = $value;}
    // inventoryID
    public function getInvidAttribute(){ return $this->inv_id;}
    public function setInvidAttribute($value){  $this->inv_id = $value;}
    // 担当 inventory update用
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
    // 現在在庫
    public function getNowinventoryAttribute(){ return $this->now_inventory;}
    public function setNowinventoryAttribute($value){  $this->now_inventory = $value;}
    // 箱数
    public function getNboxAttribute(){ return $this->nbox;}
    public function setNboxAttribute($value){  $this->nbox = $value;}
    // 棚卸在庫
    public function getStocknowinventoryAttribute(){ return $this->stock_now_inventory;}
    public function setStocknowinventoryAttribute($value){  $this->stock_now_inventory = $value;}
    // 棚卸箱数
    public function getStocknboxAttribute(){ return $this->stock_nbox;}
    public function setStocknboxAttribute($value){  $this->stock_nbox = $value;}
	// 単価
	public function getUnitpriceAttribute()
	{
		return $this->unit_price;
	}
	public function setUnitpriceAttribute($value)
	{
		$this->unit_price = $value;
	}

	// 合計
	public function getTotalAttribute()
	{
		return $this->total;
	}
	public function setTotalAttribute($value)
	{
		$this->total = $value;
	}

	// 備考
	public function getRemarksAttribute()
	{
		return $this->remarks;
	}
	public function setRemarksAttribute($value)
	{
		$this->remarks = $value;
	}
    // ステータス
    public function getStatusAttribute(){ return $this->status;}
    public function setStatusAttribute($value){  $this->status = $value;}
    // 発注情報
    public function getOrderinfoAttribute(){ return $this->order_info;}
    public function setOrderinfoAttribute($value){  $this->order_info = $value;}
    // 棚卸月
    public function getStockmonthAttribute(){ return $this->stock_month;}
    public function setStockmonthAttribute($value){  $this->stock_month = $value;}
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
    // 棚卸月
    private $param_stock_month;
    public function getParamStockmonthAttribute(){ return $this->param_stock_month;}
    public function setParamStockmonthAttribute($value){  $this->param_stock_month = $value;}
    // 在庫数setParamMmnowinventoryAttribute
	private $param_now_inventory;
	public function getParamNowinventoryAttribute(){
		return $this->param_now_inventory;
	}
	public function setParamNowinventoryAttribute($value)
	{
		$this->param_now_inventory = $value;
	}
    // 在庫箱数setParamNboxAttribute
	private $param_nbox;
	public function getParamNboxAttribute(){
		return $this->param_nbox;
	}
	public function setParamNboxAttribute($value)
	{
		$this->param_nbox = $value;
	}
    // old在庫数
	private $param_old_stock_now_inventory;
	public function getParamOldstockNowinventoryAttribute(){
		return $this->param_old_stock_now_inventory;
	}
	public function setParamOldstockNowinventoryAttribute($value)
	{
		$this->param_old_stock_now_inventory = $value;
	}
    // old在庫箱数
	private $param_old_stock_nbox;
	public function getParamOldctockNboxAttribute(){
		return $this->param_old_stock_nbox;
	}
	public function setParamOldctockNboxAttribute($value)
	{
		$this->param_old_stock_nbox = $value;
	}


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
    public function insertDataStockA($upkind){
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
            
            $id = DB::table($this->table)->insertGetId(
                [
                    'inv_id' => $this->inv_id,
                    'order_no' => $this->order_no,
                    'company_name' => $this->company_name,
                    'company_id' => $this->company_id,
                    'product_name' => $this->product_name,
                    'product_id' => $this->product_id,
                    'unit' => $this->unit,
                    'quantity' => $this->quantity,
                    'now_inventory' => $this->now_inventory,
                    'nbox' => $this->nbox,
                    'stock_now_inventory' => $this->stock_now_inventory,
                    'stock_nbox' => $this->stock_nbox,
                    'status' => $this->status,
                    'order_info' => $this->order_info,
                    'stock_month' => $this->stock_month,
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
            $re_data['inv_id'] = $this->inv_id;
            $re_data['now_inventory'] = $this->now_inventory;
            $re_data['nbox'] = $this->nbox;
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
    public function updateDataStockA($upkind){
        try {
            $total = $this->stock_now_inventory > 0 ? $this->stock_now_inventory * $this->unit_price : "0";
            if($upkind == 4)    {
                //削除マーク
                DB::table($this->table)
                ->where('id', $this->id)
                ->update([
                    'status' => 'del',
                    'updated_user'=>$this->updated_user,
                    'updated_at'=>$this->updated_at
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
                    'updated_user'=>$this->updated_user,
                    'updated_at'=>$this->updated_at
                ]);
                $re_data['id'] = $this->id;
                $re_data['product_id'] = $this->product_id;
                $re_data['product_name'] = $this->product_name;
                return $re_data;

            }
            elseif($upkind == 6)    {
                //棚卸更新 [0-9]{1,}一桁以上の数値
                $ptr = "/[0-9]{1,}/";
                if(empty($this->stock_now_inventory) && !empty($this->stock_nbox)) {
                    $match_result1 = preg_match_all($ptr,$this->stock_nbox,$matches_stock_nbox);
                    $matches_stock_nbox00 = isset($matches_stock_nbox[0][0]) ? $matches_stock_nbox[0][0] : 0 ;
                    $matches_stock_nbox01 = isset($matches_stock_nbox[0][1]) ? $matches_stock_nbox[0][1] : 0 ;
                    $this->stock_now_inventory = $matches_stock_nbox00 * $this->quantity + $matches_stock_nbox01;
                    //$this->stock_now_inventory = $this->stock_nbox * $this->quantity;
                }
                if(!empty($this->stock_now_inventory) && empty($this->stock_nbox)) {
                    $match_result2 = preg_match($ptr,$this->stock_now_inventory,$matches_stock_now_inventory);
                    $matches_stock_now_inventory0 = isset($matches_stock_now_inventory[0]) ? $matches_stock_now_inventory[0] : 0 ;
                    $floor_stock_nbox = floor($matches_stock_now_inventory0 / $this->quantity);
                    $amari_stock_nbox = $matches_stock_now_inventory0 % $this->quantity;
                    $amari_num = $amari_stock_nbox == 0 ? "" : "+".$amari_stock_nbox;
                    $this->stock_nbox = $floor_stock_nbox . $amari_num;
                    //$this->stock_nbox = $this->stock_now_inventory / $this->quantity;
                }
                DB::table($this->table)
                ->where('id', $this->id)
                ->update([
                    'stock_now_inventory' => $this->stock_now_inventory,
                    'stock_nbox' => $this->stock_nbox,
                    'unit_price' => $this->unit_price,
                    'total' => $total,
                    'remarks' => $this->remarks,
                    'status' => 'stockup',
                    'updated_user'=>$this->updated_user,
                    'updated_at'=>$this->updated_at
                ]);

                //$snini = $this->stock_now_inventory - $this->param_now_inventory;
                //Log::debug("Stock updateDataStock snini gettype -> ".gettype($snini));
                //Log::debug("Stock updateDataStock nbox val stock -> ".$this->stock_nbox." : nbox-> ".$this->nbox. " : p_nbox-> ".$this->param_nbox. " : p_old_nbox-> ".$this->param_old_stock_nbox);
                //Log::debug("Stock updateDataStock now_inventory val stock_now -> ".$this->stock_now_inventory." : now -> ".$this->now_inventory. " : param_now -> ".$this->param_now_inventory. " : p_old_now -> ".$this->param_old_stock_now_inventory);
                //if(!empty($snini)) {
                if($this->stock_now_inventory !== $this->param_old_stock_now_inventory) {
                        //Log::debug("Stock updateDataStock snini !empty in ");
                    if($this->order_info == "a") {

                        $result_tableinva = DB::table($this->table_inva)
                        ->select('charge','dnum','rnum','shipping_address','remarks','created_user')
                        ->where('product_id', $this->product_id)
                        ->where('status', 'newest')->get();
                        $this->charge = $result_tableinva[0]->charge;
                        $this->dnum = $result_tableinva[0]->dnum;
                        $this->rnum = $result_tableinva[0]->rnum;
                        $this->shipping_address = $result_tableinva[0]->shipping_address;
                        $this->remarks = $result_tableinva[0]->remarks;
                        $this->created_user = $result_tableinva[0]->created_user;
                        $this->remarks = "※{$this->stock_month}棚卸修正\n".$this->remarks;

                        DB::table($this->table_inva)
                        ->where('product_id', $this->product_id)
                        ->where('status', 'newest')
                        ->update([
                            'status' => '',
                        ]);
                        // ->where('id', $this->inv_id)

                        $id = DB::table($this->table_inva)->insertGetId(
                            [
                                'charge' => $this->charge,
                                'order_no' => $this->order_no,
                                'company_name' => $this->company_name,
                                'company_id' => $this->company_id,
                                'product_name' => $this->product_name,
                                'product_id' => $this->product_id,
                                'unit' => $this->unit,
                                'quantity' => $this->quantity,
                                'now_inventory' => $this->stock_now_inventory,
                                'nbox' => $this->stock_nbox,
                                'dnum' => $this->dnum,
                                'rnum' => $this->rnum,
                                'shipping_address' => $this->shipping_address,
                                'remarks' => $this->remarks,
                                'status' => 'newest',
                                'order_info' => $this->order_info,
                                'other1' => $this->other1,
                                'marks' => $this->marks,
                                'created_user' => $this->created_user,
                                'updated_user'=> $this->updated_user,
                                'created_at' => $this->created_at,
                                'updated_at'=> NULL
                
                            ]
                        );

                    }

                    if($this->order_info == "z") {
                        $result_tableinvz = DB::table($this->table_invz)
                        ->select('charge','order_address','remarks','note','other1','created_user')
                        ->where('product_id', $this->product_id)
                        ->where('status', 'newest')->get();
                        $this->charge = $result_tableinvz[0]->charge;
                        $this->order_address = $result_tableinvz[0]->order_address;
                        $this->remarks = $result_tableinvz[0]->remarks;
                        $this->note = $result_tableinvz[0]->note;
                        $this->other1 = $result_tableinvz[0]->other1;
                        $this->created_user = $result_tableinvz[0]->created_user;
                        $this->remarks = "※{$this->stock_month}棚卸修正\n".$this->remarks;

                        
                        DB::table($this->table_invz)
                        ->where('product_id', $this->product_id)
                        ->where('status', 'newest')
                        ->update([
                            'status' => '',
                        ]);
                        

                        $id = DB::table($this->table_invz)->insertGetId(
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
                                'now_inventory' => $this->stock_now_inventory,
                                'nbox' => $this->stock_nbox,
                                'order_address' => $this->order_address,
                                'unit_price' => $this->unit_price,
                                'total' => $this->unit_price * $this->stock_now_inventory,
                                'remarks' => $this->remarks,
                                'note' => $this->note,
                                'status' => 'newest',
                                'order_info' => $this->order_info,
                                'other1' => $this->other1,
                                'marks' => $this->marks,
                                'created_user' => $this->created_user,
                                'updated_user'=> $this->updated_user,
                                'updated_at'=> NULL
                            
                            ]
                        );

                    }

                }

                $re_data['id'] = $this->id;
                $re_data['stock_now_inventory'] = $this->stock_now_inventory;
                $re_data['stock_nbox'] = $this->stock_nbox;
                $re_data['product_id'] = $this->product_id;
                $re_data['product_name'] = $this->product_name;
                return $re_data;

            }
            else {

            DB::table($this->table)
                ->where('id', $this->id)
                ->update([
                    'inv_id' => $this->inv_id,
                    'order_no' => $this->order_no,
                    'company_name' => $this->company_name,
                    'company_id' => $this->company_id,
                    'product_name' => $this->product_name,
                    'product_id' => $this->product_id,
                    'unit' => $this->unit,
                    'quantity' => $this->quantity,
                    'now_inventory' => $this->now_inventory,
                    'nbox' => $this->nbox,
                    'stock_now_inventory' => $this->stock_now_inventory,
                    'stock_nbox' => $this->stock_nbox,
                    'unit_price' => $this->unit_price,
                    'total' => $total,
                    'remarks' => $this->remarks,
                    'status' => $this->status,
                    'order_info' => $this->order_info,
                    'stock_month' => $this->stock_month,
                    'created_user' => $this->created_user,
                    'updated_user'=>$this->updated_user,
                    'updated_at'=>$this->updated_at
                ]);

                $re_data['id'] = $this->id;
                $re_data['inv_id'] = $this->inv_id;
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
     * 取得!!
     *
     * @return void
     */
    public function getDataStock(){

        try {

            $sqlcolumn = "";
            $columnStr = Array();
            $columnStr[] = " t1.id AS id ";
            //$columnStr[] = " t1.inv_id AS inv_id ";
            $columnStr[] = " (
                CASE 
                    WHEN t1.order_info='a' THEN t2.id 
                    WHEN t1.order_info='z' THEN t3.id 
                    ELSE  null 
                END
                ) as inv_id ";
            $columnStr[] = " (
                CASE 
                    WHEN t1.order_info='a' THEN t2.charge 
                    WHEN t1.order_info='z' THEN t3.charge 
                    ELSE  null 
                END
                ) as charge ";
            $columnStr[] = " t1.order_no AS order_no ";
            $columnStr[] = " t1.company_name AS company_name ";
            $columnStr[] = " t1.company_id AS company_id ";
            //$columnStr[] = " t1.product_name AS product_name ";
            $columnStr[] = " (
                CASE 
                    WHEN t1.order_info='a' THEN t2.product_name 
                    WHEN t1.order_info='z' THEN t3.product_name 
                    ELSE  null 
                END
                ) as product_name ";
            $columnStr[] = " t1.product_id AS product_id ";
            $columnStr[] = " t1.unit AS unit ";
            $columnStr[] = " t1.quantity AS quantity ";

            /* now_inventory,nbox,をt1(stock)にすると作成時の値に固定。 t2、t3(inventoryAZ)はリアルタイム在庫による変動制。 */
            /*
            $columnStr[] = " (
                CASE 
                    WHEN t1.order_info='a' THEN t2.now_inventory 
                    WHEN t1.order_info='z' THEN t3.now_inventory 
                    ELSE  null 
                END
                ) as now_inventory ";
            $columnStr[] = " (
                CASE 
                    WHEN t1.order_info='a' THEN t2.nbox 
                    WHEN t1.order_info='z' THEN t3.nbox 
                    ELSE  null 
                END
                ) as nbox ";

            $columnStr[] = " (
                CASE 
                    WHEN t1.order_info='a' THEN t1.stock_now_inventory - t2.now_inventory 
                    WHEN t1.order_info='z' THEN t1.stock_now_inventory - t3.now_inventory 
                    ELSE  null 
                END
                ) as cal_now_inventory ";
            $columnStr[] = " (
                CASE 
                    WHEN t1.order_info='a' THEN t1.stock_nbox - t2.nbox 
                    WHEN t1.order_info='z' THEN t1.stock_nbox - t3.nbox 
                    ELSE  null 
                END
                ) as cal_nbox ";
            */
            
            $columnStr[] = " t1.now_inventory AS now_inventory ";
            $columnStr[] = " t1.nbox AS nbox ";
            $columnStr[] = " t1.stock_now_inventory - t1.now_inventory AS cal_now_inventory ";
            $columnStr[] = " ROUND(t1.stock_nbox - t1.nbox, 2) AS cal_nbox ";

            


            $columnStr[] = " t1.stock_now_inventory AS stock_now_inventory ";
            $columnStr[] = " t1.stock_nbox AS stock_nbox ";
            $columnStr[] = " t1.stock_now_inventory AS old_stock_now_inventory ";
            $columnStr[] = " t1.stock_nbox AS old_stock_nbox ";


            $columnStr[] = " (
                CASE 
                    WHEN t1.order_info='z' THEN t3.unit_price 
                    ELSE  null 
                END
                ) as unit_price ";
               
            $columnStr[] = " (
                CASE 
                    WHEN t1.order_info='z' THEN t3.unit_price * t1.stock_now_inventory 
                    ELSE  null 
                END
                ) as cal_total_price ";




            $columnStr[] = " t1.status AS status ";
            $columnStr[] = " t1.order_info AS order_info ";
            $columnStr[] = " t1.stock_month AS stock_month ";
            $columnStr[] = " t1.created_user AS created_user ";
            $columnStr[] = " t1.updated_user AS updated_user ";
            $columnStr[] = " t1.created_at AS created_at ";
            $columnStr[] = " t1.updated_at AS updated_at ";
            $sqlcolumn = implode(",", $columnStr);

            $sqlString = "";
            $sqlString .= "select ";
            $sqlString .= $sqlcolumn;
            $sqlString .= " from ";
            $sqlString .= " ".$this->table." as t1 ";

            $sqlString .= " left outer join ";
            $sqlString .= " ".$this->table_inva." as t2 ";
            $sqlString .= " on ";
            //$sqlString .= " t1.inv_id = t2.id ";
            $sqlString .= " t1.company_id = t2.company_id ";
            $sqlString .= " and t1.product_id = t2.product_id ";
            $sqlString .= " and t1.order_info = t2.order_info ";
            $sqlString .= " and t2.status = 'newest' ";


            $sqlString .= " left outer join ";
            $sqlString .= " ".$this->table_invz." as t3 ";
            $sqlString .= " on ";
            //$sqlString .= " t1.inv_id = t3.id ";
            $sqlString .= " t1.company_id = t3.company_id ";
            $sqlString .= " and t1.product_id = t3.product_id ";
            $sqlString .= " and t1.order_info = t3.order_info ";
            $sqlString .= " and t3.status = 'newest' ";


            $sqlString .= " where ";
            //$sqlString .= "    ? = ? ";
            if (!empty($this->param_stock_month)) {
                $sqlString .= " t1.stock_month = ? ";
            }
            

            // 順番変更 正順逆順
            if(isset($this->param_order_no)){
                //Log::info("isset this->param_order_no -- ".$this->param_order_no);
                if($this->param_order_no == 1) {
                    //$data->orderBy('order_no');
                    $sqlString .= " order by t1.order_no ";
                }
                if($this->param_order_no == 2) {
                    //$data->orderBy('order_no', 'DESC');
                    $sqlString .= " order by t1.order_no DESC ";
                }
            }
            elseif(isset($this->param_company_id)){
                //Log::info("isset this->param_company_id -- ".$this->param_company_id);
                if($this->param_company_id == 1) {
                    //$data->orderBy('company_name');
                    $sqlString .= " order by t1.company_name ";
                }
                if($this->param_company_id == 2) {
                    //$data->orderBy('company_name', 'DESC');
                    $sqlString .= " order by t1.company_name DESC ";
                }
            }
            elseif(isset($this->param_product_id2)){
                //Log::info("isset this->param_product_id2 -- ".$this->param_product_id2);
                if($this->param_product_id2 == 1) {
                    //$data->orderBy('product_name');
                    $sqlString .= " order by t1.product_name ";
                }
                if($this->param_product_id2 == 2) {
                    //$data->orderBy('product_name', 'DESC');
                    $sqlString .= " order by t1.product_name DESC ";
                }
            }
            else {
                $sqlString .= " order by ";
                $sqlString .= " t1.id DESC ";
    
            }


            //$sqlString .= " t1.inv_id asc ";
            //$sqlString .= " t1.status asc ";
            //$sqlString .= " t1.order_info asc ";
            // バインド
            $array_setBindingsStr = array();
            //$array_setBindingsStr[] = 1;
            //$array_setBindingsStr[] = 1;
            if (!empty($this->param_stock_month)) {
                $array_setBindingsStr[] = $this->param_stock_month;
            }
            $data1 = DB::select($sqlString, $array_setBindingsStr);

            /*
            $sqlString = "";
            $sqlString .= "select ";
            $sqlString .= $sqlcolumn;
            $sqlString .= " from ";
            $sqlString .= " ".$this->table." as t1 ";
            $sqlString .= " inner join ";
            $sqlString .= " ".$this->table_invz." as t2 ";
            $sqlString .= " on ";
            $sqlString .= " t1.inv_id = t2.id ";
            $sqlString .= " and t1.order_info = t2.order_info ";
            $sqlString .= " where ";
            if (!empty($this->param_stock_month)) {
                $sqlString .= " t1.stock_month = ? ";
            }
            $sqlString .= " order by ";
            $sqlString .= "  t1.inv_id asc ";
            // バインド
            $array_setBindingsStr = array();
            if (!empty($this->param_stock_month)) {
                $array_setBindingsStr[] = $this->param_stock_month;
            }
            $data2 = DB::select($sqlString, $array_setBindingsStr);

            $result = array_merge($data1, $data2);
            */

            $result = $data1;

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

  

    




}
