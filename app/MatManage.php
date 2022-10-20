<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class MatManage extends Model
{
    protected $table = 'matmanage';

	private $id;					// ID
	private $mdate;					// 日付
	private $department;			// 部署
	private $charge;				// 担当
	private $product_name;			// 商品名
	private $product_code;			// 商品CODE
	private $product_number;		// 商品Number
	private $unit;					// 単位
	private $quantity;				// 入数
	private $receipt;				// 入庫数
	private $delivery;				// 出庫数
	private $now_inventory;			// 現在在庫
	private $nbox;					// 箱数
	private $order_address;			// 発注先
	private $unit_price;			// 単価
	private $total;					// 合計
	private $remarks;				// 備考
	private $note;					// メモ/ノート
	private $status;				// ステータス--最新/履歴
	private $marks;					// マーク
	private $created_user;			// 作成ユーザー
	private $updated_user;			// 修正ユーザー
	private $created_at;			// 作成時間
	private $updated_at;			// 修正時間
	private $is_deleted;			// 削除フラグ

	//ID
	public function getIdAttribute()
	{
		return $this->id;
	}
	public function setIdAttribute($value)
	{
		$this->id = $value;
	}

	//日付
	public function getMdateAttribute()
	{
		return $this->mdate;
	}
	public function setMdateAttribute($value)
	{
		$this->mdate = $value;
	}

	//部署
	public function getDepartmentAttribute()
	{
		return $this->department;
	}
	public function setDepartmentAttribute($value)
	{
		$this->department = $value;
	}

	//担当
	public function getChargeAttribute()
	{
		return $this->charge;
	}
	public function setChargeAttribute($value)
	{
		$this->charge = $value;
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

	//商品Number
	public function getProductnumberAttribute()
	{
		return $this->product_number;
	}
	public function setProductnumberAttribute($value)
	{
		$this->product_number = $value;
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

	//入庫数
	public function getReceiptAttribute()
	{
		return $this->receipt;
	}
	public function setReceiptAttribute($value)
	{
		$this->receipt = $value;
	}

	//出庫数
	public function getDeliveryAttribute()
	{
		return $this->delivery;
	}
	public function setDeliveryAttribute($value)
	{
		$this->delivery = $value;
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

	//発注先
	public function getOrderaddressAttribute()
	{
		return $this->order_address;
	}
	public function setOrderaddressAttribute($value)
	{
		$this->order_address = $value;
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

	//合計
	public function getTotalAttribute()
	{
		return $this->total;
	}
	public function setTotalAttribute($value)
	{
		$this->total = $value;
	}

	//備考
	public function getRemarksAttribute()
	{
		return $this->remarks;
	}
	public function setRemarksAttribute($value)
	{
		$this->remarks = $value;
	}

	//メモ/ノート
	public function getNoteAttribute()
	{
		return $this->note;
	}
	public function setNoteAttribute($value)
	{
		$this->note = $value;
	}

	//ステータス--最新/履歴
	public function getStatusAttribute()
	{
		return $this->status;
	}
	public function setStatusAttribute($value)
	{
		$this->status = $value;
	}

	//マーク
	public function getMarksAttribute()
	{
		return $this->marks;
	}
	public function setMarksAttribute($value)
	{
		$this->marks = $value;
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

	// マーク（部署グループ a b）
	private $param_marks;
	public function getParamMarksAttribute()
	{
		return $this->param_marks;
	}
	public function setParamMarksAttribute($value)
	{
		$this->param_marks = $value;
	}
	
	// 削除フラグ
	private $param_is_deleted;
	public function getParamIsdeletedAttribute()
	{
		return $this->param_is_deleted;
	}
	public function setParamIsdeletedAttribute($value)
	{
		$this->param_is_deleted = $value;
	}


	// ------------- 順序・正逆・検索 --------------
    // 商品
    private $param_product_name;
    public function getParamProductnameAttribute(){ return $this->param_product_name;}
    public function setParamProductnameAttribute($value){  $this->param_product_name = $value;}
    // 分類
    private $param_product_number;
    public function getParamProductnumberAttribute(){ return $this->param_product_number;}
    public function setParamProductnumberAttribute($value){  $this->param_product_number = $value;}
    // 正逆
    private $param_orderfr;
    public function getParamOrderfrAttribute(){ return $this->param_orderfr;}
    public function setParamOrderfrAttribute($value){  $this->param_orderfr = $value;}
    // 部署
    private $param_department;
    public function getParamDepartmentAttribute(){ return $this->param_department;}
    public function setParamDepartmentAttribute($value){  $this->param_department = $value;}
    // 担当
    private $param_charge;
    public function getParamChargeAttribute(){ return $this->param_charge;}
    public function setParamChargeAttribute($value){  $this->param_charge = $value;}
    // 履歴検索チェック
    private $param_shistory;
    public function getParamSHistoryAttribute(){ return $this->param_shistory;}
    public function setParamSHistoryAttribute($value){  $this->param_shistory = $value;}

    // 日付
    private $param_mdate;
    public function getParamMdateAttribute(){ return $this->param_mdate;}
    public function setParamMdateAttribute($value){  $this->param_mdate = $value;}


    // ------------- メソッド --------------




    /**
     * 登録
     *
     * @return void
     */
    public function insertData($upkind){
        try {
            $re_data = [];
            if($upkind == 3) {
                $this->product_code = DB::table($this->table)->max('product_code') + 1;
            }
            if($upkind == 2) {
                $this->product_code = DB::table($this->table)->max('product_code') + 1;
                //$this->created_user = 'system';
            }
            $this->now_inventory = isset($this->now_inventory) ? $this->now_inventory : "";
            $this->nbox = isset($this->nbox) ? $this->nbox : "";

    
            $id = DB::table($this->table)->insertGetId(
                [

					'mdate' => $this->mdate,
					'department' => $this->department,
					'charge' => $this->charge,
					'product_name' => $this->product_name,
					'product_code' => $this->product_code,
					'product_number' => $this->product_number,
					'unit' => $this->unit,
					'quantity' => $this->quantity,
					'receipt' => $this->receipt,
					'delivery' => $this->delivery,
					'now_inventory' => $this->now_inventory,
					'nbox' => $this->nbox,
					'order_address' => $this->order_address,
					'unit_price' => $this->unit_price,
					'total' => $this->total,
					'remarks' => $this->remarks,
					'note' => $this->note,
					'status' => 'newest',
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
            $re_data['product_code'] = $this->product_code;
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
    public function updateData($upkind){
        try {
            if($upkind == 4)    {
                //削除マーク'status' => 'del'
                DB::table($this->table)
                //->where('id', $this->id)
                ->where('product_code', $this->product_code)
                ->update([
                    'is_deleted' => '1',
                ]);

            }
            elseif($upkind == 5)    {
                //削除マーク→newest（戻す）'status' => 'newest'
                DB::table($this->table)
                //->where('id', $this->id)
                ->where('product_code', $this->product_code)
                ->update([
                    'is_deleted' => '0',
                ]);
                $re_data['id'] = $this->id;
                $re_data['product_code'] = $this->product_code;
                $re_data['product_name'] = $this->product_name;
                return $re_data;

            }
            else {

            DB::table($this->table)
                ->where('id', $this->id)
                ->update([

					'mdate' => $this->mdate,
					'department' => $this->department,
					'charge' => $this->charge,
					'product_name' => $this->product_name,
					'product_code' => $this->product_code,
					'product_number' => $this->product_number,
					'unit' => $this->unit,
					'quantity' => $this->quantity,
					'receipt' => $this->receipt,
					'delivery' => $this->delivery,
					'now_inventory' => $this->now_inventory,
					'nbox' => $this->nbox,
					'order_address' => $this->order_address,
					'unit_price' => $this->unit_price,
					'total' => $this->total,
					'remarks' => $this->remarks,
					'note' => $this->note,
					'status' => $this->status,
					'marks' => $this->marks,
					'updated_user' => $this->updated_user,
					'updated_at' => $this->updated_at,
					'is_deleted' => $this->is_deleted


                ]);
            }
            $re_data['id'] = $this->id;
            $re_data['product_code'] = $this->product_code;
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
    public function getDataMM(){
        //$message = "ログ出力 getDataMM";
        //Log::info("this->param_edit_id -- ".$this->param_edit_id);
        //Log::info("this->param_product_code -- ".$this->param_product_code);

        try {
            $data = DB::table($this->table)
            ->select(

				'id',
				'mdate',
				'department',
				'charge',
				'product_name',
				'product_code',
				'product_number',
				'unit',
				'quantity',
				'receipt',
				'delivery',
				'now_inventory',
				'nbox',
				'order_address',
				'unit_price',
				'total',
				'remarks',
				'note',
				'status',
				'marks',
				'created_user',
				'updated_user',
				'created_at',
				'updated_at',
				'is_deleted',
				


            );
            $data->selectRaw('
                FLOOR(now_inventory / quantity)  as calc_nbox,
                (now_inventory mod quantity)  as calc_nbox_mod
            ');


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
                $data->where('status','newest')
				//->orderBy('product_code')
				->orderBy('id', 'DESC');
            }

            if(!empty($this->param_marks)){
                $data->where('marks',$this->param_marks);
            }
            if(!empty($this->param_is_deleted)){
                $data->where('is_deleted',$this->param_is_deleted);
            }
			else {
				$data->where('is_deleted', 0);
			}


            // 順番変更 正順逆順
            if(isset($this->param_product_name)){
                //Log::info("isset this->param_company_id -- ".$this->param_company_id);
                if($this->param_product_name == 1) {
                    $data->orderBy('product_name');
                }
                if($this->param_product_name == 2) {
                    $data->orderBy('product_name', 'DESC');
                }
            }
            elseif(isset($this->param_product_code)){
                //Log::info("isset this->param_product_code -- ".$this->param_product_code);
                if($this->param_product_code == 1) {
                    $data->orderBy('product_name');
                }
                if($this->param_product_code == 2) {
                    $data->orderBy('product_name', 'DESC');
                }
            }
            elseif(isset($this->param_mdate)){
                //Log::info("isset this->param_mdate -- ".$this->param_mdate);
                if($this->param_mdate == 1) {
                    $data->orderBy('mdate');
                }
                if($this->param_mdate == 2) {
                    $data->orderBy('mdate', 'DESC');
                }
            }
            elseif(isset($this->param_charge)){
                //Log::info("isset this->param_order_day -- ".$this->param_order_day);
                if($this->param_charge == 1) {
                    $data->orderBy('charge');
                }
                if($this->param_charge == 2) {
                    $data->orderBy('charge', 'DESC');
                }
            }

            $result1 = $data
            ->get();
			
			//$result = array_merge($result1, $result2);

            return $result1;

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
    public function getDataMMtotal(){
        //$message = "ログ出力 getDataMM";
        //Log::info("this->param_edit_id -- ".$this->param_edit_id);
        //Log::info("this->param_product_code -- ".$this->param_product_code);

        try {

			/*

            $datasum = DB::table($this->table)
			->selectRaw('
			sum(total) as totals
			');

            $datasum->where('status','newest')
			->orderBy('id', 'DESC');

            if(!empty($this->param_marks)){
                $datasum->where('marks',$this->param_marks);
            }
            $result2 = $datasum
            ->get();

			*/

			
			//$result = array_merge($result1, $result2);


            $sqlString = "";
            $sqlString .= "select ";
            $sqlString .= "sum(total) as totals";
            $sqlString .= " from ";
            $sqlString .= " ".$this->table." ";
            $sqlString .= " where ";
			$sqlString .= " status = 'newest' ";
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


			//$sqlString3 = "select sum(total) as totals from matmanage where status = 'newest' and marks = 'b'";
            //$data3 = DB::select($sqlString3);

            //$result = array_merge($data1, $data2);

            $result = $data2;

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
    public function getSearch(){

        try {
            $result = "";
            $data = DB::table($this->table)
            ->select(

				'id',
				'mdate',
				'department',
				'charge',
				'product_name',
				'product_code',
				'product_number',
				'unit',
				'quantity',
				'receipt',
				'delivery',
				'now_inventory',
				'nbox',
				'order_address',
				'unit_price',
				'total',
				'remarks',
				'note',
				'status',
				'marks',
				'created_user',
				'updated_user',
				'created_at',
				'updated_at',
				'is_deleted',

			);
            if(!empty($this->param_department)){
                $data->where('department', $this->param_department)
                ->where('status','newest')
				->where('is_deleted', 0)
                ->orderBy('id', 'DESC');
                //Log::info("getSearch this->param_department -- ".$this->param_department." / this->param_marks -- ".$this->param_marks);

                $result = $data
                ->get();
            }
            if(!empty($this->param_charge)){
                $data->where('charge', $this->param_charge)
                ->where('status','newest')
                ->where('marks', $this->param_marks)
				->where('is_deleted', 0)
                ->orderBy('id', 'DESC');
            
                $result = $data
                ->get();
            }
            if(!empty($this->param_product_name)){
                $str = "%".$this->param_product_name."%";
				if(empty($this->param_shistory)) $matchThese['status'] = 'newest';
				$matchThese['is_deleted'] = 0;
                //Log::info("getSearchA this->param_company_name -- ".$str);
                $data->where('product_name','LIKE', $str)
                ->where($matchThese)
                ->orderBy('id');
            
                $result = $data
                ->get();
            }

            if(!empty($this->param_product_number)){
                $str = "%".$this->param_product_number."%";
				if(empty($this->param_shistory)) $matchThese['status'] = 'newest';
				$matchThese['is_deleted'] = 0;
	            $data->where('product_number','LIKE', $str)
                ->where($matchThese)
                ->orderBy('id');
	
                $result = $data
                ->get();
            }



    			//->selectRaw('SUM(total) AS totals')		DB::raw('SUM(total) as total_s')
				//$data->select(DB::raw('SUM(total) as total_s'))->where('status','newest');

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

            if(!empty($this->param_department)){
                $data->where('department', $this->param_department)
                ->where('status','newest')
				->where('is_deleted', 0)
                ->orderBy('id', 'DESC');

                $result = $data
                ->get();
            }
            if(!empty($this->param_charge)){
                $data->where('charge', $this->param_charge)
                ->where('status','newest')
                ->where('marks', $this->param_marks)
				->where('is_deleted', 0)
                ->orderBy('id', 'DESC');
            
                $result = $data
                ->get();
            }
            if(!empty($this->param_product_name)){
                $str = "%".$this->param_product_name."%";
				if(empty($this->param_shistory)) $matchThese['status'] = 'newest';
				$matchThese['is_deleted'] = 0;
                //Log::info("getSearchA this->param_company_name -- ".$str);
                $data->where('product_name','LIKE', $str)
                ->where($matchThese)
                ->orderBy('id');
				//Log::info("getSearchTotal in -- this->param_product_name = ".$this->param_product_name."");
            
                $result = $data
                ->get();
            }

            if(!empty($this->param_product_number)){
                $str = "%".$this->param_product_number."%";
				if(empty($this->param_shistory)) $matchThese['status'] = 'newest';
				$matchThese['is_deleted'] = 0;
	            $data->where('product_number','LIKE', $str)
                ->where($matchThese)
                ->orderBy('id');
				//Log::info("getSearchTotal in -- this->param_product_number = ".$this->param_product_number."");
	
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
    public function getDataMiniMM(){

        try {
            $data = DB::table($this->table)
            ->select(
                [
                    'id as inv_id',
                    'product_name',
                    'product_code',
					'product_number',
					'unit',
                    'quantity',
                    'now_inventory',
                    'nbox',
                    'status',
					'marks',
                    'created_user',
                    'created_at',
                ]
            );
                $data->where('status', 'newest');
                $data->where('marks', $this->param_marks);
				$data->where('is_deleted', 0);
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
	            $mainQuery->where('product_code', $this->product_code);
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
