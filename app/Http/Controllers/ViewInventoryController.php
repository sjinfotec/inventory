<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
//use App\Http\Requests\StoreCompanyInfoPost;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\InventoryA;
use App\InventoryZ;
use App\Http\Controllers\ApiCommonController;

class ViewInventoryController extends Controller
{

    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
    	//Log::debug('ViewInventoryController index in GET[order_info] = '.$_GET["order_info"]);
        // リダイレクト
        // return redirect()->route('edit_work_order.edithome', [
        //     'order_no' => $_GET["order_no"],
        //     'seq' => $_GET["seq"]
        // ]);
    	if (isset($_GET["order_info"]) && $_GET["order_info"] == 1) {
            $rv_order_info = !empty($_GET["order_info"]) ? $_GET['order_info'] : "";
            $rv_order_no = !empty($_GET["order_no"]) ? $_GET['order_no'] : "";
            $rv_company_id = !empty($_GET["company_id"]) ? $_GET['company_id'] : "";
            $rv_product_id2 = !empty($_GET["product_id2"]) ? $_GET['product_id2'] : "";
            $rv_receipt_day = !empty($_GET["receipt_day"]) ? $_GET['receipt_day'] : "";
            $rv_delivery_day = !empty($_GET["delivery_day"]) ? $_GET['delivery_day'] : "";
            $rv_orderfr = !empty($_GET["orderfr"]) ? $_GET['orderfr'] : "";

	        return view('view_inventory_a', [
            	'order_info' => $rv_order_info,
            	'order_no' => $rv_order_no,
            	'company_id' => $rv_company_id,
            	'product_id2' => $rv_product_id2,
            	'receipt_day' => $rv_receipt_day,
            	'delivery_day' => $rv_delivery_day,
            	'orderfr' => $rv_orderfr
	        ]);
	    }
    	elseif (isset($_GET["order_info"]) && $_GET["order_info"] == 2) {
            $rv_order_info = !empty($_GET["order_info"]) ? $_GET['order_info'] : "";
            $rv_order_no = !empty($_GET["order_no"]) ? $_GET['order_no'] : "";
            $rv_company_id = !empty($_GET["company_id"]) ? $_GET['company_id'] : "";
            $rv_product_id2 = !empty($_GET["product_id2"]) ? $_GET['product_id2'] : "";
            $rv_supply_day = !empty($_GET["supply_day"]) ? $_GET['supply_day'] : "";
            $rv_order_day = !empty($_GET["order_day"]) ? $_GET['order_day'] : "";
            $rv_orderfr = !empty($_GET["orderfr"]) ? $_GET['orderfr'] : "";

            return view('view_inventory_z', [
            	'order_info' => $rv_order_info,
            	'order_no' => $rv_order_no,
            	'company_id' => $rv_company_id,
            	'product_id2' => $rv_product_id2,
            	'supply_day' => $rv_supply_day,
            	'order_day' => $rv_order_day,
            	'orderfr' => $rv_orderfr
	        ]);
	    }
        else {
	        //$authusers = Auth::user();
            return view('home'
            );

    	} 

    }

    public function inventorya()
    {
        return view('view_inventory_a');
    }

    public function inventoryz()
    {
        return view('view_inventory_z'
        );
    }

    public function inventorydust()
    {
        return view('view_inventory_dust'
        );
    }


    public function search()
    {
    
        return view('view_inventory_a'
        );
    }






    /**
     * 新規登録
     *
     * @param Request $request
     * @return void
     */
    public function storeA(Request $request){
        $this->array_messagedata = array();
        $result = true;
        try {
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            if (!isset($params['form'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "form", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $details = $params['form'];
            $re_data = $this->insertA($details);
            if (!isset($re_data['id'])) {
                $result = false;
            }

            return response()->json(
                ['result' => $result, 'id' => $re_data['id'], 'product_id' => $re_data['product_id'], 'product_name' => $re_data['product_name'],
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * INSERT
     *
     * @param [type] $inputs
     * @return void
     */
    private function insertA($details){
        $inventory_a = new InventoryA();
        $systemdate = Carbon::now();
        //$user = Auth::user();
        //$login_user_code = $user->code;
        //$login_account_id = $user->account_id;
        //$apply_term_from = Config::get('const.INIT_DATE.initdate');

        DB::beginTransaction();
        try{
            //$inventory_a->setParamAccountidAttribute($login_account_id);
            //$inventory_a->setAccountidAttribute($login_account_id);
            $inventory_a->setChargeAttribute($details['charge']);
            $inventory_a->setOrdernoAttribute($details['order_no']);
            $inventory_a->setCompanynameAttribute($details['company_name']);
            $inventory_a->setCompanyidAttribute($details['company_id']);
            $inventory_a->setProductnameAttribute($details['product_name']);
            $inventory_a->setProductidAttribute($details['product_id']);
            $inventory_a->setUnitAttribute($details['unit']);
            $inventory_a->setQuantityAttribute($details['quantity']);
            $inventory_a->setReceiptdayAttribute($details['receipt_day']);
            $inventory_a->setOrderquantityAttribute($details['order_quantity']);
            $inventory_a->setReceiptAttribute($details['receipt']);
            $inventory_a->setDeliverydayAttribute($details['delivery_day']);
            $inventory_a->setDeliveryAttribute($details['delivery']);
            $inventory_a->setNowinventoryAttribute($details['now_inventory']);
            $inventory_a->setNboxAttribute($details['nbox']);
            $inventory_a->setDnumAttribute($details['dnum']);
            $inventory_a->setRnumAttribute($details['rnum']);
            $inventory_a->setShippingaddressAttribute($details['shipping_address']);
            $inventory_a->setRemarksAttribute($details['remarks']);
            $inventory_a->setStatusAttribute($details['status']);
            $inventory_a->setOrderinfoAttribute($details['order_info']);
            $inventory_a->setOther1Attribute($details['other1']);
            $inventory_a->setMarksAttribute($details['marks']);
            $inventory_a->setCreateduserAttribute($details['charge']);
            $inventory_a->setCreatedatAttribute($systemdate);
            $inventory_a->setIsdeletedAttribute($details['is_deleted']);
            /*
            $is_exists = $inventory_a->isExistsInfo();
            if($is_exists){
                $inventory_a->delDataA();
            }
            */

            $re_data = $inventory_a->insertDataA(3);   //引数 3 ＝ 新規登録
            DB::commit();
            return $re_data;

        }catch(\PDOException $pe){
            Log::error($pe->getMessage());
            DB::rollBack();
            throw $pe;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
            throw $e;
        }

    }



    /**
     * z新規登録
     *
     * @param Request $request
     * @return void
     */
    public function storeZ(Request $request){
        global $id ;
        $this->array_messagedata = array();
        $result = true;
        try {
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            if (!isset($params['form'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "form", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $details = $params['form'];
            $re_data = $this->insertZ($details);
            if (!isset($re_data['id'])) {
                $result = false;
            }

            return response()->json(
                ['result' => $result, 'id' => $re_data['id'], 'product_id' => $re_data['product_id'], 'product_name' => $re_data['product_name'],
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * zINSERT
     *
     * @param [type] $inputs
     * @return void
     */
    private function insertZ($details){
        $inventory_z = new InventoryZ();
        $systemdate = Carbon::now();
 
        DB::beginTransaction();
        try{
            $inventory_z->setChargeAttribute($details['charge']);
            $inventory_z->setOrdernoAttribute($details['order_no']);
            $inventory_z->setCompanynameAttribute($details['company_name']);
            $inventory_z->setCompanyidAttribute($details['company_id']);
            $inventory_z->setProductnameAttribute($details['product_name']);
            $inventory_z->setProductidAttribute($details['product_id']);
            $inventory_z->setUnitAttribute($details['unit']);
            $inventory_z->setQuantityAttribute($details['quantity']);
            $inventory_z->setSupplydayAttribute($details['supply_day']);
            $inventory_z->setSupplyquantityAttribute($details['supply_quantity']);
            $inventory_z->setOrderdayAttribute($details['order_day']);
            $inventory_z->setOrderquantityAttribute($details['order_quantity']);
            $inventory_z->setNowinventoryAttribute($details['now_inventory']);
            $inventory_z->setNboxAttribute($details['nbox']);
            $inventory_z->setOrderaddressAttribute($details['order_address']);
            $inventory_z->setUnitpriceAttribute($details['unit_price']);
            $inventory_z->setTotalAttribute($details['total']);
            $inventory_z->setRemarksAttribute($details['remarks']);
            $inventory_z->setNoteAttribute($details['note']);
            $inventory_z->setStatusAttribute($details['status']);
            $inventory_z->setOrderinfoAttribute($details['order_info']);
            $inventory_z->setOther1Attribute($details['other1']);
            $inventory_z->setMarksAttribute($details['marks']);
            $inventory_z->setCreateduserAttribute($details['charge']);
            $inventory_z->setCreatedatAttribute($systemdate);
            $inventory_z->setIsdeletedAttribute($details['is_deleted']);
            
            /*
            $is_exists = $inventory_a->isExistsInfo();
            if($is_exists){
                $inventory_a->delDataA();
            }
            */

            $re_data = $inventory_z->insertDataZ(3);   //引数 3 ＝ 新規登録
            //Log::info("insertZ in inventory_z = ".$inventory_z);

            DB::commit();
            return $re_data;

        }catch(\PDOException $pe){
            Log::error($pe->getMessage());
            DB::rollBack();
            throw $pe;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
            throw $e;
        }

    }




    /**
     * 更新ボタン押下 
     *
     * @param Request $request
     * @return response
     */
    public function fixA(Request $request){
        $this->array_messagedata = array();
        $details = array();
        $result = true;
        try {
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            if (!isset($params['details'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "details", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $details = $params['details'];
            $upkind = $params['upkind'];
            $re_data = $this->updateA($details,$upkind);
            return response()->json(
                ['result' => $result, 'id' => $re_data['id'], 'product_id' => $re_data['product_id'], 'product_name' => $re_data['product_name'],
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }

        return $response;
    }

    /**
     * ステートボタン押下 
     *
     * @param Request $request
     * @return response
     */
    public function statusA(Request $request){
        $this->array_messagedata = array();
        $details = array();
        $result = true;
        try {
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            $details = $params['details'];
            //$details['id'] = $params['edit_id'];
            $upkind = $params['upkind'];
            $re_data = $this->updateA($details,$upkind);
            return response()->json(
                ['result' => $result, 'id' => $re_data['id'], 'product_id' => $re_data['product_id'], 'product_name' => $re_data['product_name'],
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }

        return $response;
    }



    /**
     * 更新
     *
     * @param [type] $details
     * @return boolean
     */
    private function updateA($details,$upkind){
        $systemdate = Carbon::now();
        $inventory_a = new InventoryA();
        //$user = Auth::user();
        //$login_user_code = $user->code;
        //$login_account_id = $user->account_id;

        DB::beginTransaction();
        try{
            //$carbon = new Carbon($details['apply_term_from']);
            //$from = $carbon->copy()->format('Ymd');
            //$inventory_a->setApplytermfromAttribute($from);

            $inventory_a->setIdAttribute($details['id']);   
            $inventory_a->setChargeAttribute($details['charge']);
            $inventory_a->setOrdernoAttribute($details['order_no']);
            $inventory_a->setCompanynameAttribute($details['company_name']);
            $inventory_a->setCompanyidAttribute($details['company_id']);
            $inventory_a->setProductnameAttribute($details['product_name']);
            $inventory_a->setProductidAttribute($details['product_id']);
            $inventory_a->setUnitAttribute($details['unit']);
            $inventory_a->setQuantityAttribute($details['quantity']);
            $inventory_a->setReceiptdayAttribute($details['receipt_day']);
            $inventory_a->setOrderquantityAttribute($details['order_quantity']);
            $inventory_a->setReceiptAttribute($details['receipt']);
            $inventory_a->setDeliverydayAttribute($details['delivery_day']);
            $inventory_a->setDeliveryAttribute($details['delivery']);
            $inventory_a->setNowinventoryAttribute($details['now_inventory']);
            $inventory_a->setNboxAttribute($details['nbox']);
            $inventory_a->setDnumAttribute($details['dnum']);
            $inventory_a->setRnumAttribute($details['rnum']);
            $inventory_a->setShippingaddressAttribute($details['shipping_address']);
            $inventory_a->setRemarksAttribute($details['remarks']);
            $inventory_a->setStatusAttribute($details['status']);
            $inventory_a->setOrderinfoAttribute($details['order_info']);
            $inventory_a->setOther1Attribute($details['other1']);
            $inventory_a->setMarksAttribute($details['marks']);
            $inventory_a->setCreateduserAttribute($details['charge']);
            $inventory_a->setUpdateduserAttribute($details['charge']);
            $inventory_a->setCreatedatAttribute($systemdate);
            $inventory_a->setUpdatedatAttribute($systemdate);
            $inventory_a->setIsdeletedAttribute($details['is_deleted']);
            
            //if ($details['id'] == "" || $details['id'] == null) {
            if ($upkind == 1 || $upkind == 2 ) {
                    //$inventory_a->setAccountidAttribute($login_account_id);
                $re_data = $inventory_a->insertDataA($upkind);
            } else {
                //$inventory_a->setParamAccountidAttribute($login_account_id);
                $re_data = $inventory_a->updateDataA($upkind);
            }
            DB::commit();
            return $re_data;

        }catch(\PDOException $pe){
            DB::rollBack();
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * z更新ボタン押下 
     *
     * @param Request $request
     * @return response
     */
    public function fixZ(Request $request){
        $this->array_messagedata = array();
        $details = array();
        $result = true;
        try {
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            if (!isset($params['details'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "details", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $details = $params['details'];
            $upkind = $params['upkind'];
            $re_data = $this->updateZ($details,$upkind);
            return response()->json(
                ['result' => $result, 'id' => $re_data['id'], 'product_id' => $re_data['product_id'], 'product_name' => $re_data['product_name'],
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }

        return $response;
    }

    /**
     * zステートボタン押下 
     *
     * @param Request $request
     * @return response
     */
    public function statusZ(Request $request){
        $this->array_messagedata = array();
        $details = array();
        $result = true;
        try {
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            $details = $params['details'];
            $upkind = $params['upkind'];
            $re_data = $this->updateZ($details,$upkind);
            return response()->json(
                ['result' => $result, 'id' => $re_data['id'], 'product_id' => $re_data['product_id'], 'product_name' => $re_data['product_name'],
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }

        return $response;
    }


    /**
     * z更新
     *
     * @param [type] $details
     * @return boolean
     */
    private function updateZ($details,$upkind){
        $systemdate = Carbon::now();
        $inventory_z = new InventoryZ();
        //$user = Auth::user();
        //$login_user_code = $user->code;
        //$login_account_id = $user->account_id;

        DB::beginTransaction();
        try{
            //$carbon = new Carbon($details['apply_term_from']);
            //$from = $carbon->copy()->format('Ymd');
            //$inventory_a->setApplytermfromAttribute($from);

            $inventory_z->setIdAttribute($details['id']);   
            $inventory_z->setChargeAttribute($details['charge']);
            $inventory_z->setOrdernoAttribute($details['order_no']);
            $inventory_z->setCompanynameAttribute($details['company_name']);
            $inventory_z->setCompanyidAttribute($details['company_id']);
            $inventory_z->setProductnameAttribute($details['product_name']);
            $inventory_z->setProductidAttribute($details['product_id']);
            $inventory_z->setUnitAttribute($details['unit']);
            $inventory_z->setQuantityAttribute($details['quantity']);
            $inventory_z->setSupplydayAttribute($details['supply_day']);
            $inventory_z->setSupplyquantityAttribute($details['supply_quantity']);
            $inventory_z->setOrderdayAttribute($details['order_day']);
            $inventory_z->setOrderquantityAttribute($details['order_quantity']);
            $inventory_z->setNowinventoryAttribute($details['now_inventory']);
            $inventory_z->setNboxAttribute($details['nbox']);
            $inventory_z->setOrderaddressAttribute($details['order_address']);
            $inventory_z->setUnitpriceAttribute($details['unit_price']);
            $inventory_z->setTotalAttribute($details['total']);
            $inventory_z->setRemarksAttribute($details['remarks']);
            $inventory_z->setNoteAttribute($details['note']);
            $inventory_z->setStatusAttribute($details['status']);
            $inventory_z->setOrderinfoAttribute($details['order_info']);
            $inventory_z->setOther1Attribute($details['other1']);
            $inventory_z->setMarksAttribute($details['marks']);
            $inventory_z->setCreateduserAttribute($details['charge']);
            $inventory_z->setUpdateduserAttribute($details['charge']);
            $inventory_z->setCreatedatAttribute($systemdate);
            $inventory_z->setUpdatedatAttribute($systemdate);
            $inventory_z->setIsdeletedAttribute($details['is_deleted']);
            
            //if ($details['id'] == "" || $details['id'] == null) {
            if ($upkind == 1 || $upkind == 2 ) {
                    //$inventory_a->setAccountidAttribute($login_account_id);
                $re_data = $inventory_z->insertDataZ($upkind);
            } else {
                //$inventory_a->setParamAccountidAttribute($login_account_id);
                $re_data = $inventory_z->updateDataZ($upkind);
            }
            DB::commit();
            return $re_data;
            //$msg = "GET order_info = ".$_GET['order_info']."<br>\n";

        }catch(\PDOException $pe){
            DB::rollBack();
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }





    /**
     * 取得（request）
     *
     * @return void
     */
    public function getDataA(Request $request){
        /*
        $params_order_info1 = $request->order_info;
        $params_order_info2 = $request->input('order_info');
        $params_order_info3 = $request->query('order_info');
        Log::info("ViewInventoryController getDataAFunc paramsget = ".$params_order_info1);
        Log::info("ViewInventoryController getDataAFunc paramsget = ".$params_order_info2);
        Log::info("ViewInventoryController getDataAFunc paramsget = ".$params_order_info3);
        $message = "ログ viewinventorycontroll";
        */
        //echo '<pre>' . var_export($message, true) . '</pre>';
        //Log::debug("debug --".$message);
        $this->array_messagedata = array();
        try {
            $details = $this->getDataAFunc($request);
            return response()->json(
                ['result' => true, 'details' => $details,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 取得
     *
     * @return void
     */
    public function getDataAFunc($request){

        $this->array_messagedata = array();
        //$details = new Collection();
        //$user = Auth::user();
        //$login_user_code = $user->code;
        //$login_account_id = $user->account_id;
        $result = true;
        try {

            // パラメータセット
            $params = array();
            $params_order_info = null;
            $params_order_no = null;
            $params_company_id = null;
            $params_product_id2 = null;
            $params_receipt_day = null;
            $params_delivery_day= null;
            $params_orderfr = null;

            /*
            Log::info("ViewInventoryController getDataAFunc param_order_info = ".$param_order_info);
            Log::info("ViewInventoryController getDataAFunc params_order_no = ".$params_order_no);
            Log::info("ViewInventoryController getDataAFunc params_company_id = ".$params_company_id);
            Log::info("ViewInventoryController getDataAFunc params_product_id2 = ".$params_product_id2);
            Log::info("ViewInventoryController getDataAFunc params_orderfr = ".$params_orderfr);
            $params_order_info1 = $request->order_info;
            $params_order_info2 = $request->input('order_info');
            $params_order_info3 = $request->query('order_info');
            Log::info("ViewInventoryController getDataAFunc paramsget = ".$params_order_info1);
            Log::info("ViewInventoryController getDataAFunc paramsget = ".$params_order_info2);
            Log::info("ViewInventoryController getDataAFunc paramsget = ".$params_order_info3);
            */
            /*
            $paramsget = $request->only(['order_info', 'order_no', 'company_id', 'product_id2', 'orderfr']);
            if (isset($paramsget)) {
                if (isset($paramsget['order_info'])) {
                    $params_order_info = $paramsget['order_info'];
                }
                if (isset($paramsget['order_no'])) {
                    $params_order_no = $paramsget['order_no'];
                }
                if (isset($paramsget['company_id'])) {
                    $params_company_id = $paramsget['company_id'];
                }
                if (isset($paramsget['product_id2'])) {
                    $params_product_id2 = $paramsget['product_id2'];
                }
                if (isset($paramsget['orderfr'])) {
                    $params_orderfr = $paramsget['orderfr'];
                }
            }
            */

            $inventory_a = new InventoryA();
            if (isset($request->keyparams)) {
                $params = $request->keyparams;
                if (!empty($params['order_info'])) {
                    $params_order_info = $params['order_info'];
                    $inventory_a->setParamOrderinfoAttribute($params_order_info);
                }
                if (!empty($params['order_no'])) {
                    $params_order_no = $params['order_no'];
                    $inventory_a->setParamOrdernoAttribute($params_order_no);
                }
                if (!empty($params['company_id'])) {
                    $params_company_id = $params['company_id'];
                    $inventory_a->setParamCompanyidAttribute($params_company_id);
                }
                if (!empty($params['product_id2'])) {
                    $params_product_id2 = $params['product_id2'];
                    $inventory_a->setParamProductid2Attribute($params_product_id2);
                }
                if (!empty($params['receipt_day'])) {
                    $params_receipt_day = $params['receipt_day'];
                    $inventory_a->setParamReceiptdayAttribute($params_receipt_day);
                }
                if (!empty($params['delivery_day'])) {
                    $params_delivery_day = $params['delivery_day'];
                    $inventory_a->setParamDeliverydayAttribute($params_delivery_day);
                }
                if (!empty($params['orderfr'])) {
                    $params_orderfr = $params['orderfr'];
                    $inventory_a->setParamOrderfrAttribute($params_orderfr);
                }
            }

            //$inventory_a->setParamEditidAttribute($edit_id);
            $details =  $inventory_a->getDataInvA();

            return $details;
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }


    /** 検索SEARCH取得
     *
     * @return list results
     */
    public function getDataAsearch(Request $request){
        //Log::debug("getDataAone in ");
        $this->array_messagedata = array();
        $s_order_no = "";
        $s_company_name = "";
        $s_product_name = "";
        $s_history = "";
        $result = true;
        try {
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => null,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            //Log::debug("getDataAsearch params[s_order_no] = ".$params['s_order_no']);
            if (!isset($params['s_order_no']) && !isset($params['s_company_name']) && !isset($params['s_product_name'])) {
                //Log::debug("getDataAsearch isset params = ".$params['s_product_name']);
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => null,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $s_order_no = isset($params['s_order_no']) ? $params['s_order_no'] : "";
            $s_company_name = isset($params['s_company_name']) ? $params['s_company_name'] : "";
            $s_product_name = isset($params['s_product_name']) ? $params['s_product_name'] : "";
            $s_history = isset($params['s_history']) ? $params['s_history'] : "";
            //Log::debug("getDataAsearch s_product_name = ".$s_product_name);
            $inventory_a = new InventoryA();
            if(isset($s_order_no))      $inventory_a->setParamOrdernoAttribute($s_order_no);
            if(isset($s_company_name))  $inventory_a->setParamCompanynameAttribute($s_company_name);
            if(isset($s_product_name))  $inventory_a->setParamProductnameAttribute($s_product_name);
            if(isset($s_history))      $inventory_a->setParamSHistoryAttribute($s_history);
            $details =  $inventory_a->getSearchA();

            return response()->json(
                [
                    'result' => $result, 
                    'details' => $details, 
                    's_order_no' => $s_order_no, 
                    's_company_name' => $s_company_name, 
                    's_product_name' => $s_product_name,
                    's_history' => $s_history,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata
                ]
            );
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }



    /** 詳細取得
     *
     * @return list results
     */
    public function getDataAone(Request $request){
        //Log::debug("getDataAone in ");
        $this->array_messagedata = array();
        $edit_id = "";
        $product_id = "";
        $result = true;
        try {
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => null,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            //Log::debug("getDataAone params[edit_id] = ".$params['edit_id']);
            //Log::debug("getDataAone params[product_id] = ".$params['product_id']);
            if (!isset($params['edit_id'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "edit_id", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => null,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $edit_id = $params['edit_id'];
            //Log::debug("getDataAone edit_id = ".$edit_id);
            $inventory_a = new InventoryA();
            $inventory_a->setParamEditidAttribute($edit_id);
            $details =  $inventory_a->getDataInvA();

            
            $product_id = $params['product_id'];
            //Log::debug("getDataAone product_id = ".$product_id);
            if(isset($product_id)) {
                $inventory_a2 = new InventoryA();
                $inventory_a2->setParamProductidAttribute($product_id);
                $details2 =  $inventory_a2->getDataInvA();
            }  else {
                $details2 = "";
            }
        


            return response()->json(
                ['result' => $result, 'details' => $details, 'details2' => $details2, 'edit_id' => $edit_id, 'product_id' => $product_id,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }


    /**
     * Z取得（request）
     *
     * @return void
     */
    public function getDataZ(Request $request){
        $this->array_messagedata = array();
        try {
            $details = $this->getDataZFunc($request);
            $totals = $this->getDataTotalFunc($request);
            return response()->json(
                ['result' => true, 'details' => $details, 'totals' => $totals,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * Z取得
     *
     * @return void
     */
    public function getDataZFunc($request){
        $this->array_messagedata = array();
        //$details = new Collection();
        $result = true;
        try {
            // パラメータセット
            $params = array();
            $params_order_info = null;
            $params_order_no = null;
            $params_company_id = null;
            $params_product_id2 = null;
            $params_supply_day = null;
            $params_order_day = null;
            $params_orderfr = null;

            $inventory_z = new InventoryZ();

            if (isset($request->keyparams)) {
                $params = $request->keyparams;
                if (!empty($params['order_info'])) {
                    $params_order_info = $params['order_info'];
                    $inventory_z->setParamOrderinfoAttribute($params_order_info);
                }
                if (!empty($params['order_no'])) {
                    $params_order_no = $params['order_no'];
                    $inventory_z->setParamOrdernoAttribute($params_order_no);
                }
                if (!empty($params['company_id'])) {
                    $params_company_id = $params['company_id'];
                    $inventory_z->setParamCompanyidAttribute($params_company_id);
                }
                if (!empty($params['product_id2'])) {
                    $params_product_id2 = $params['product_id2'];
                    $inventory_z->setParamProductid2Attribute($params_product_id2);
                }
                if (!empty($params['supply_day'])) {
                    $params_supply_day = $params['supply_day'];
                    $inventory_z->setParamSupplydayAttribute($params_supply_day);
                }
                if (!empty($params['order_day'])) {
                    $params_order_day = $params['order_day'];
                    $inventory_z->setParamOrderdayAttribute($params_order_day);
                }
                if (!empty($params['orderfr'])) {
                    $params_orderfr = $params['orderfr'];
                    $inventory_z->setParamOrderfrAttribute($params_orderfr);
                }
            }

            $details =  $inventory_z->getDataInvZ();

            return $details;
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }


    /**
     * 取得total
     *
     * @return void
     */
    public function getDataTotalFunc($request){
        $this->array_messagedata = array();
        //$details = new Collection();
        $result = true;
        try {
            // パラメータセット
            $params = array();
            $params_marks = null;

            $inventory_z = new InventoryZ();

            if (isset($request->keyparams)) {
                $params = $request->keyparams;
            }

            $details =  $inventory_z->getDataInvtotal();

            return $details;
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }




    /** z検索SEARCH取得
     *
     * @return list results
     */
    public function getDataZsearch(Request $request){
        //Log::debug("getDataZsearch in ");
        $this->array_messagedata = array();
        $s_order_no = "";
        $s_company_name = "";
        $s_history = "";
        $result = true;
        try {
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => null,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            //Log::debug("getDataAsearch params[s_order_no] = ".$params['s_order_no']);
            if (!isset($params['s_order_no']) && !isset($params['s_company_name']) && !isset($params['s_product_name'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => null,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $s_order_no = isset($params['s_order_no']) ? $params['s_order_no'] : "";
            $s_company_name = isset($params['s_company_name']) ? $params['s_company_name'] : "";
            $s_product_name = isset($params['s_product_name']) ? $params['s_product_name'] : "";
            $s_history = isset($params['s_history']) ? $params['s_history'] : "";
            //Log::debug("getDataAsearch s_product_name = ".$s_product_name);
            $inventory_z = new InventoryZ();
            if(isset($s_order_no))      $inventory_z->setParamOrdernoAttribute($s_order_no);
            if(isset($s_company_name))  $inventory_z->setParamCompanynameAttribute($s_company_name);
            if(isset($s_product_name))  $inventory_z->setParamProductnameAttribute($s_product_name);
            if(isset($s_history))      $inventory_z->setParamSHistoryAttribute($s_history);
            $details =  $inventory_z->getSearchZ();
            $search_totals =  $inventory_z->getSearchTotal();

            return response()->json(
                [
                    'result' => $result, 
                    'details' => $details, 
                    's_order_no' => $s_order_no, 
                    's_company_name' => $s_company_name, 
                    's_product_name' => $s_product_name,
                    's_history' => $s_history,
                    'search_totals' => $search_totals,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata
                ]
            );
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }



    /** Z詳細取得
     *
     * @return list results
     */
    public function getDataZone(Request $request){
        //Log::debug("getDataZone in ");
        $this->array_messagedata = array();
        $edit_id = "";
        $product_id = "";
        $result = true;
        try {
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => null,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            //Log::debug("getDataZone params[edit_id] = ".$params['edit_id']);
            //Log::debug("getDataZone params[product_id] = ".$params['product_id']);
            if (!isset($params['edit_id'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "edit_id", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => null,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $edit_id = $params['edit_id'];
            //Log::debug("getDataZone edit_id = ".$edit_id);
            $inventory_z = new InventoryZ();
            $inventory_z->setParamEditidAttribute($edit_id);
            $details =  $inventory_z->getDataInvZ();

            
            $product_id = $params['product_id'];
            //Log::debug("getDataAone product_id = ".$product_id);
            if(isset($product_id)) {
                $inventory_z2 = new InventoryZ();
                $inventory_z2->setParamProductidAttribute($product_id);
                $details2 =  $inventory_z2->getDataInvZ();
            } else {
                $details2 = "";
            }

            return response()->json(
                ['result' => $result, 'details' => $details, 'details2' => $details2, 'edit_id' => $edit_id, 'product_id' => $product_id,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }



    /** ゴミ箱取得
     *
     * @return list results
     */
    public function getDataDust(Request $request){
        Log::debug("getDataDust in ");
        $this->array_messagedata = array();
        $status = "";
        $result = true;
        try {
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => null, 'details2' => null, 'status' => 'ステータス指定なし',
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;

            $status = $params['status'];
            Log::debug("getDataDust status = ".$status);
            $inventory_a = new InventoryA();
            $inventory_a->setParamStatusAttribute($status);
            $details =  $inventory_a->getDataInvA();

            $inventory_z = new InventoryZ();
            $inventory_z->setParamStatusAttribute($status);
            $details2 =  $inventory_z->getDataInvZ();


            return response()->json(
                ['result' => $result, 'details' => $details, 'details2' => $details2, 'status' => $status, 
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }


    /**
     * レコード削除
     *
     * @param Request $request
     * @return void
     */
    public function delete(Request $request){
        global $id ;
        $this->array_messagedata = array();
        $result = true;
        try {
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            if (!isset($params['details'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "details", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $details = $params['details'];
            $delkind = $params['delkind'];
            $re_data = $this->fixdel($details,$delkind);
            if (!isset($re_data['id'])) {
                $result = false;
            }

            return response()->json(
                ['result' => $result, 'id' => $re_data['id'], 
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * DELETE
     *
     * @param [type] $inputs
     * @return void
     */
    private function fixdel($details,$delkind){
        //$material_management = new MatManage();
        $systemdate = Carbon::now();
 
        DB::beginTransaction();
        try{


            if($details['order_info'] == 'a') {
                $inventory_a = new InventoryA();
                $inventory_a->setIdAttribute($details['id']);
                $inventory_a->setProductidAttribute($details['product_id']);
                $re_data = $inventory_a->delData($delkind);
    
            }
            elseif($details['order_info'] == 'z') {
                $inventory_z = new InventoryZ();
                $inventory_z->setIdAttribute($details['id']);
                $inventory_z->setProductidAttribute($details['product_id']);
                $re_data = $inventory_z->delData($delkind);

            }

            
            /*
            $is_exists = $inventory_a->isExistsInfo();
            if($is_exists){
                $inventory_a->delDataA();
            }
            */

            //$re_data = $material_management->delData($delkind);
            //Log::info("insertZ in inventory_z = ".$inventory_z);

            DB::commit();
            return $re_data;

        }catch(\PDOException $pe){
            Log::error($pe->getMessage());
            DB::rollBack();
            throw $pe;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
            throw $e;
        }

    }



}
