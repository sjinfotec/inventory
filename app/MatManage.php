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
	private $product_id;			// 商品ID
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

	//商品ID
	public function getProductidAttribute()
	{
		return $this->product_id;
	}
	public function setProductidAttribute($value)
	{
		$this->product_id = $value;
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

	// 商品ID
	private $param_product_id;
	public function getParamProductidAttribute()
	{
		return $this->param_product_id;
	}
		public function setParamProductidAttribute($value)
	{
	$this->param_product_id = $value;}

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








}
