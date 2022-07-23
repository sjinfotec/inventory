<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class MMStock extends Model
{
    protected $table = 'mmstock';
    protected $table_mm = 'matmanage';

	private $id;						// ID
	private $inv_id;					// inventoryID
	private $product_name;				// 商品名
	private $product_code;				// 商品CODE
	private $unit;						// 単位
	private $quantity;					// 入数
	private $now_inventory;				// 現在在庫
	private $nbox;						// 箱数
	private $stock_now_inventory;		// 棚卸在庫
	private $stock_nbox;				// 棚卸箱数
	private $status;					// ステータス
	private $marks;						// マークグループ
	private $stock_month;				// 棚卸月
	private $created_user;				// 作成ユーザー
	private $updated_user;				// 修正ユーザー
	private $created_at;				// 作成時間
	private $updated_at;				// 修正時間
	private $is_deleted;				// 削除フラグ

	//ID
	public function getIdAttribute()
	{
		return $this->id;
	}
	public function setIdAttribute($value)
	{
		$this->id = $value;
	}

	//inventoryID
	public function getInvidAttribute()
	{
		return $this->inv_id;
	}
	public function setInvidAttribute($value)
	{
		$this->inv_id = $value;
	}

	//商品名
	public function getProductnameAttribute()
	{
		return $this->product_name;
	}
	public function setProductnameAttribute($value)
	{
		$this->product_name = $value;
	}

	//商品CODE
	public function getProductcodeAttribute()
	{
		return $this->product_code;
	}
	public function setProductcodeAttribute($value)
	{
		$this->product_code = $value;
	}

	//単位
	public function getUnitAttribute()
	{
		return $this->unit;
	}
	public function setUnitAttribute($value)
	{
		$this->unit = $value;
	}

	//入数
	public function getQuantityAttribute()
	{
		return $this->quantity;
	}
	public function setQuantityAttribute($value)
	{
		$this->quantity = $value;
	}

	//現在在庫
	public function getNowinventoryAttribute()
	{
		return $this->now_inventory;
	}
	public function setNowinventoryAttribute($value)
	{
		$this->now_inventory = $value;
	}

	//箱数
	public function getNboxAttribute()
	{
		return $this->nbox;
	}
	public function setNboxAttribute($value)
	{
		$this->nbox = $value;
	}

	//棚卸在庫
	public function getStocknowinventoryAttribute()
	{
		return $this->stock_now_inventory;
	}
	public function setStocknowinventoryAttribute($value)
	{
		$this->stock_now_inventory = $value;
	}

	//棚卸箱数
	public function getStocknboxAttribute()
	{
		return $this->stock_nbox;
	}
	public function setStocknboxAttribute($value)
	{
		$this->stock_nbox = $value;
	}

	//ステータス
	public function getStatusAttribute()
	{
		return $this->status;
	}
	public function setStatusAttribute($value)
	{
		$this->status = $value;
	}

	//マーク グループ
	public function getMarksAttribute()
	{
		return $this->marks;
	}
	public function setMarksAttribute($value)
	{
		$this->marks = $value;
	}

	//棚卸月
	public function getStockmonthAttribute()
	{
		return $this->stock_month;
	}
	public function setStockmonthAttribute($value)
	{
		$this->stock_month = $value;
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

	//作成時間
	public function getCreatedatAttribute()
	{
		return $this->created_at;
	}
	public function setCreatedatAttribute($value)
	{
		$this->created_at = $value;
	}

	//修正時間
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


    // ------------- param --------------
 
	// エディットID
	private $param_edit_id;
	public function getParamEditidAttribute()
	{
		return $this->param_edit_id;
	}
		public function setParamEditidAttribute($value)
	{
	$this->param_edit_id = $value;
	}

	// 商品CODE
	private $param_product_code;
	public function getParamProductcodeAttribute()
	{
		return $this->param_product_code;
	}
		public function setParamProductcodeAttribute($value)
	{
	$this->param_product_code = $value;}

	// ステータス
	private $param_status;
	public function getParamStatusAttribute()
	{
		return $this->param_status;
	}
	public function setParamStatusAttribute($value)
	{
		$this->param_status = $value;
	}

	// マーク グループ
	private $param_marks;
	public function getParamMarksAttribute()
	{
		return $this->param_marks;
	}
	public function setParamMarksAttribute($value)
	{
		$this->param_marks = $value;
	}


	// 棚卸月
	private $param_stock_month;
	public function getParamStockmonthAttribute(){
		return $this->param_stock_month;
	}
	public function setParamStockmonthAttribute($value)
	{
		$this->param_stock_month = $value;
	}



    // ------------- メソッド --------------

    /**
     * 登録
     *
     * @return void
     */
    public function insertDataStock($upkind){
        try {
            $re_data = [];
            if($upkind == 3) {
            $this->company_id = DB::table($this->table)->max('company_id') + 1;
            $this->product_code = DB::table($this->table)->max('product_code') + 1;
            $this->order_info = 'a';
            //$this->created_user = 'system';
            }
            if($upkind == 2) {
                $this->product_code = DB::table($this->table)->max('product_code') + 1;
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
                    'product_code' => $this->product_code,
                    'product_number' => $this->product_number,
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
    public function updateDataStock($upkind){
        try {
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
                $re_data['product_code'] = $this->product_code;
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
                $re_data['product_code'] = $this->product_code;
                $re_data['product_name'] = $this->product_name;
                return $re_data;

            }
            elseif($upkind == 6)    {
                //棚卸更新
                DB::table($this->table)
                ->where('id', $this->id)
                ->update([
                    'stock_now_inventory' => $this->stock_now_inventory,
                    'stock_nbox' => $this->stock_nbox,
                    'status' => 'stockup',
                    'updated_user'=>$this->updated_user,
                    'updated_at'=>$this->updated_at
                ]);
                $re_data['id'] = $this->id;
                $re_data['stock_now_inventory'] = $this->stock_now_inventory;
                $re_data['stock_nbox'] = $this->stock_nbox;
                $re_data['product_code'] = $this->product_code;
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
                    'product_code' => $this->product_code,
                    'product_number' => $this->product_number,
                    'unit' => $this->unit,
                    'quantity' => $this->quantity,
                    'now_inventory' => $this->now_inventory,
                    'nbox' => $this->nbox,
                    'stock_now_inventory' => $this->stock_now_inventory,
                    'stock_nbox' => $this->stock_nbox,
                    'status' => $this->status,
                    'order_info' => $this->order_info,
                    'stock_month' => $this->stock_month,
                    'updated_user'=>$this->updated_user,
                    'updated_at'=>$this->updated_at
                ]);

                $re_data['id'] = $this->id;
                $re_data['inv_id'] = $this->inv_id;
                $re_data['product_code'] = $this->product_code;
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
                    WHEN t1.marks='a' THEN t2.id 
                    WHEN t1.marks='b' THEN t2.id 
                    ELSE  null 
                END
                ) as inv_id ";
            //$columnStr[] = " t1.product_name AS product_name ";
            $columnStr[] = " (
                CASE 
                    WHEN t1.marks='a' THEN t2.product_name 
                    WHEN t1.marks='b' THEN t2.product_name 
                    ELSE  null 
                END
                ) as product_name ";
            $columnStr[] = " t1.product_code AS product_code ";
            $columnStr[] = " t1.product_number AS product_number ";
            $columnStr[] = " t1.unit AS unit ";
            $columnStr[] = " t1.quantity AS quantity ";

            
            $columnStr[] = " (
                CASE 
                    WHEN t1.marks='a' THEN t2.now_inventory 
                    WHEN t1.marks='b' THEN t2.now_inventory 
                    ELSE  null 
                END
                ) as now_inventory ";
            $columnStr[] = " (
                CASE 
                    WHEN t1.marks='a' THEN t2.nbox 
                    WHEN t1.marks='b' THEN t2.nbox 
                    ELSE  null 
                END
                ) as nbox ";

            $columnStr[] = " (
                CASE 
                    WHEN t1.marks='a' THEN t1.stock_now_inventory - t2.now_inventory 
                    WHEN t1.marks='b' THEN t1.stock_now_inventory - t2.now_inventory 
                    ELSE  null 
                END
                ) as cal_now_inventory ";
            $columnStr[] = " (
                CASE 
                    WHEN t1.marks='a' THEN t1.stock_nbox - t2.nbox 
                    WHEN t1.marks='b' THEN t1.stock_nbox - t2.nbox 
                    ELSE  null 
                END
                ) as cal_nbox ";
            
            /*
            $columnStr[] = " t1.now_inventory AS now_inventory ";
            $columnStr[] = " t1.nbox AS nbox ";
            $columnStr[] = " t1.stock_now_inventory - t1.now_inventory AS cal_now_inventory ";
            $columnStr[] = " t1.stock_nbox - t1.nbox AS cal_nbox ";
            */

            $columnStr[] = " t1.stock_now_inventory AS stock_now_inventory ";
            $columnStr[] = " t1.stock_nbox AS stock_nbox ";

            $columnStr[] = " t1.status AS status ";
            $columnStr[] = " t1.marks AS marks ";
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
            $sqlString .= " ".$this->table_mm." as t2 ";
            $sqlString .= " on ";
            //$sqlString .= " t1.inv_id = t2.id ";
            $sqlString .= " t1.product_code = t2.product_code ";
            $sqlString .= " and t1.marks = t2.marks ";
            $sqlString .= " and t2.status = 'newest' ";

			/*
            $sqlString .= " left outer join ";
            $sqlString .= " ".$this->table_invz." as t3 ";
            $sqlString .= " on ";
            //$sqlString .= " t1.inv_id = t3.id ";
            $sqlString .= " t1.company_id = t3.company_id ";
            $sqlString .= " and t1.product_code = t3.product_code ";
            $sqlString .= " and t1.order_info = t3.order_info ";
            $sqlString .= " and t3.status = 'newest' ";
			*/

            $sqlString .= " where ";
            //$sqlString .= "    ? = ? ";
            if (!empty($this->param_stock_month)) {
                $sqlString .= " t1.stock_month = ? ";
            }
            if (!empty($this->param_marks)) {
                $sqlString .= " and t1.marks = '".$this->param_marks."' ";
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
            elseif(isset($this->param_product_code)){
                //Log::info("isset this->param_product_code -- ".$this->param_product_code);
                if($this->param_product_code == 1) {
                    //$data->orderBy('product_name');
                    $sqlString .= " order by t1.product_name ";
                }
                if($this->param_product_code == 2) {
                    //$data->orderBy('product_name', 'DESC');
                    $sqlString .= " order by t1.product_name DESC ";
                }
            }
            else {
                $sqlString .= " order by ";
                $sqlString .= " t1.id asc ";
    
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



    

    
    /**
     * 取得
     *
     * @return void
     */
    public function getDataStockMM(){
        $message = "ログ出力 getDataStock";
        //echo '<pre>' . var_export($message, true) . '</pre>';
        //Log::debug("debug --".$message);
        /*
        Log::info("this->param_edit_id -- ".$this->param_edit_id);
        Log::info("this->param_product_code -- ".$this->param_product_code);
        Log::info("this->param_order_info -- ".$this->param_order_info);
        Log::info("this->param_order_no -- ".$this->param_order_no);
        Log::info("this->param_company_id -- ".$this->param_company_id);
        Log::info("this->param_orderfr -- ".$this->param_orderfr);
        */

        try {
            $data = DB::table($this->table)
            ->select(
                [
                    'id',
                    'inv_id',
                    'order_no',
                    'company_name',
                    'company_id',
                    'product_name',
                    'product_code',
                    'product_number',
                    'unit',
                    'quantity',
                    'now_inventory AS stock_now_inventory',
                    'nbox AS stock_nbox',
                    'status',
                    'order_info',
                    'stock_month',
                    'created_user',
                    'updated_user',
                    'created_at',
                    'updated_at'
                ]
            );
            //$data->where('account_id',$this->param_account_id);
            if(!empty($this->param_edit_id)){
                $data->where('id',$this->param_edit_id);
            }
            elseif(!empty($this->param_product_code)){
                $data->where('product_code',$this->param_product_code)
                ->orderBy('id', 'DESC');
            }
            elseif(!empty($this->param_status)){
                $data->where('status',$this->param_status)
                ->orderBy('id');
            }
            else {
                $data->where('stock_month',$this->param_stock_month);
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
            elseif(isset($this->param_product_code)){
                Log::info("isset this->param_product_code -- ".$this->param_product_code);
                if($this->param_product_code == 1) {
                    $data->orderBy('product_name');
                }
                if($this->param_product_code == 2) {
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
    public function getSearchStock(){

        try {
            $data = DB::table($this->table)
            ->select(

                'id',
                'inv_id',
                'order_no',
                'company_name',
                'company_id',
                'product_name',
                'product_code',
                'unit',
                'quantity',
                'now_inventory',
                'nbox',
                'stock_now_inventory',
                'stock_nbox',
                'status',
                'order_info',
                'stock_month',
                'created_user',
                'updated_user',
                'created_at',
                'updated_at'

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
    public function delDataStock(){
        try {
            
            $mainQuery = DB::table($this->table);
            $mainQuery->where('account_id',$this->param_account_id);
            $result = $mainQuery->where('is_deleted',0)->delete();
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
