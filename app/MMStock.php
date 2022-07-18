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

	private $id;						// ID
	private $inv_id;					// inventoryID
	private $product_name;				// 商品名
	private $product_code;				// 商品ID
	private $unit;						// 単位
	private $quantity;					// 入数
	private $now_inventory;				// 現在在庫
	private $nbox;						// 箱数
	private $stock_now_inventory;		// 棚卸在庫
	private $stock_nbox;				// 棚卸箱数
	private $status;					// ステータス
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

	//商品ID
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

	// 商品ID
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

	// 棚卸月
	private $param_stock_month;
	public function getParamStockmonthAttribute(){
		return $this->param_stock_month;
	}
	public function setParamStockmonthAttribute($value)
	{
		$this->param_stock_month = $value;
	}






}
