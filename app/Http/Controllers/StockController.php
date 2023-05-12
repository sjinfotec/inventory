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
use App\StockA;
use App\InventoryA;
use App\InventoryZ;
use App\Http\Controllers\ApiCommonController;

class StockController extends Controller
{

    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
    	if (isset($_GET["order_info"]) && $_GET["order_info"] == 1) {
            $rv_order_info = !empty($_GET["order_info"]) ? $_GET['order_info'] : "";
            $rv_order_no = !empty($_GET["order_no"]) ? $_GET['order_no'] : "";
            $rv_company_id = !empty($_GET["company_id"]) ? $_GET['company_id'] : "";
            $rv_product_id2 = !empty($_GET["product_id2"]) ? $_GET['product_id2'] : "";
            $rv_receipt_day = !empty($_GET["receipt_day"]) ? $_GET['receipt_day'] : "";
            $rv_delivery_day = !empty($_GET["delivery_day"]) ? $_GET['delivery_day'] : "";
            $rv_orderfr = !empty($_GET["orderfr"]) ? $_GET['orderfr'] : "";

	        return view('stock_work_a', [
            	'order_info' => $rv_order_info,
            	'order_no' => $rv_order_no,
            	'company_id' => $rv_company_id,
            	'product_id2' => $rv_product_id2,
            	'receipt_day' => $rv_receipt_day,
            	'delivery_day' => $rv_delivery_day,
            	'orderfr' => $rv_orderfr
	        ]);
	    }
        else {
	        //$authusers = Auth::user();
            return view('home'
            );

    	} 

    }

    


    public function stockTop()
    {
        return view('stock_work_top');
    }

    public function indexStockA()
    {
        return view('stock_work_a');
    }
    public function indexStockZ()
    {
        return view('stock_work_z');
    }
    public function search()
    {
        return view('stock_work_a');
    }







    /** 棚卸テーブルにALLインサート
     *
     * @return list results
     */
    public function storeAllA(Request $request){
        Log::debug("storeAllA in ");
        $this->array_messagedata = array();
        $status = "";
        $result = true;
        $detailstr = "str";
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

            $details = $params['details'];
            $detailstr = "";

            $stock_month = isset($params['stock_month']) ? $params['stock_month'] : "";

            //$details = $params['form'];
            //$details['id'] = $params['edit_id'];
            $upkind = isset($params['upkind']) ? $params['upkind'] : "";
            $status = isset($params['status']) ? $params['status'] : "";
            //Log::debug("getDataDust details = ".$details);


            $stock_a = new StockA();
            $systemdate = Carbon::now();
            $inputsys = "manual";
            $table = 'stock_a';


            $max_stock_month = DB::table($table)->max('stock_month');
            $distinct_stock_month = DB::table($table)
            ->select('stock_month')
            ->distinct()
            ->get()
            ->toArray();

            $stock_month_arr = "";
            $stock_month_arr = array_column($distinct_stock_month, 'stock_month');

            $arrayin = isset($distinct_stock_month) ? in_array( $stock_month , array_column( $distinct_stock_month, 'stock_month')) : false;

            if($arrayin === false) {
                if( $insert_ok = DB::table($table)->insert($details) ) {
                $update_num = DB::table($table)
                ->where('status', 'newest')
                ->update([
                    'stock_now_inventory' => '',
                    'stock_nbox' => '',
                    'stock_month' => $stock_month,
                    'status' => 'wait',
                ]);
                } else {
                    $insert_ok = false;
                }
    
            } else {
                $insert_ok = false;
                $update_num = 0;
                $result = false;

            }

            /*
            return response()->json(
                ['result' => $result, 'id' => $re_data['id'], 'inv_id' => $re_data['inv_id'], 'stock_now_inventory' => $re_data['now_inventory'], 'stock_nbox' => $re_data['nbox'],
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
            */
            return response()->json(
                ['result' => $result, 'stock_month' => $stock_month, 'max_stock_month' => $max_stock_month,
                'distinct_stock_month' => $distinct_stock_month, 'stock_month_arr' => $stock_month_arr, 'arrayin' => $arrayin,
                'insert_ok' => $insert_ok, 'update_num' => $update_num,
                'detailstr' => $detailstr, 
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
                ['result' => $result, 'id' => $re_data['id'], 'inv_id' => $re_data['inv_id'], 'product_id' => $re_data['product_id'], 'product_name' => $re_data['product_name'],
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
        $stock_a = new StockA();
        $systemdate = Carbon::now();
        $inputsys = "manual";
        //$apply_term_from = Config::get('const.INIT_DATE.initdate');

        DB::beginTransaction();
        try{
            $stock_a->setInvidAttribute($details['inv_id']);
            $stock_a->setOrdernoAttribute($details['order_no']);
            $stock_a->setCompanynameAttribute($details['company_name']);
            $stock_a->setCompanyidAttribute($details['company_id']);
            $stock_a->setProductnameAttribute($details['product_name']);
            $stock_a->setProductidAttribute($details['product_id']);
            $stock_a->setUnitAttribute($details['unit']);
            $stock_a->setQuantityAttribute($details['quantity']);
            $stock_a->setNowinventoryAttribute($details['now_inventory']);
            $stock_a->setNboxAttribute($details['nbox']);
            $stock_a->setStatusAttribute($details['status']);
            $stock_a->setOrderinfoAttribute($details['order_info']);
            $stock_a->setMarksAttribute($details['marks']);
            $stock_a->setCreateduserAttribute($inputsys);
            $stock_a->setCreatedatAttribute($systemdate);
            
            /*
            $is_exists = $inventory_a->isExistsInfo();
            if($is_exists){
                $inventory_a->delDataA();
            }
            */

            $re_data = $stock_a->insertDataA();   //引数 3 ＝ 新規登録
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
                'stock_now_inventory' => $re_data['stock_now_inventory'], 'stock_nbox' => $re_data['stock_nbox'],
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
        $updateuser = "Stock";
        //$stock_now_inventory = isset($details['stock_now_inventory']) ? $details['stock_now_inventory'] : "";
        //$stock_nbox = isset($details['stock_nbox']) ? $details['stock_nbox'] : "";
        $stock_a = new StockA();
        //$user = Auth::user();
        //$login_user_code = $user->code;
        //$login_account_id = $user->account_id;

        DB::beginTransaction();
        try{
            //$carbon = new Carbon($details['apply_term_from']);
            //$from = $carbon->copy()->format('Ymd');
            //$inventory_a->setApplytermfromAttribute($from);

            $stock_a->setIdAttribute($details['id']);
            $stock_a->setInvidAttribute($details['inv_id']);
            $stock_a->setOrdernoAttribute($details['order_no']);
            $stock_a->setCompanynameAttribute($details['company_name']);
            $stock_a->setCompanyidAttribute($details['company_id']);
            $stock_a->setProductnameAttribute($details['product_name']);
            $stock_a->setProductidAttribute($details['product_id']);
            $stock_a->setUnitAttribute($details['unit']);
            $stock_a->setQuantityAttribute($details['quantity']);
            $stock_a->setNowinventoryAttribute($details['now_inventory']);
            $stock_a->setNboxAttribute($details['nbox']);
            $stock_a->setStocknowinventoryAttribute($details['stock_now_inventory']);
            $stock_a->setStocknboxAttribute($details['stock_nbox']);
            $stock_a->setUnitpriceAttribute($details['unit_price']);
            //$stock_a->setTotalAttribute($details['total']);
            //$stock_a->setRemarksAttribute($details['remarks']);
            $stock_a->setStatusAttribute($details['status']);
            $stock_a->setOrderinfoAttribute($details['order_info']);
            $stock_a->setStockmonthAttribute($details['stock_month']);
            $stock_a->setUpdateduserAttribute($updateuser);
            $stock_a->setUpdatedatAttribute($systemdate);
            $stock_a->setParamNowinventoryAttribute($details['now_inventory']);

            
            //if ($details['id'] == "" || $details['id'] == null) {
            if ($upkind == 1 || $upkind == 2 ) {
                    //$inventory_a->setAccountidAttribute($login_account_id);
                $re_data = $stock_a->insertDataStockA($upkind);
            } else {
                //$inventory_a->setParamAccountidAttribute($login_account_id);
                $re_data = $stock_a->updateDataStockA($upkind);
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





    /** 登録用mini取得
     *
     * @return list results
     */
    public function getDataMiniA(Request $request){
        //Log::debug("getDataAone in ");
        $this->array_messagedata = array();
        //$s_order_no = "";
        $result = true;
        try {
            // パラメータチェック
            $params = array();
            /*
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => null,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            */
            $params = $request->keyparams;
            //$s_order_no = $params['s_order_no'];
            //Log::debug("getDataAsearch params[s_order_no] = ".$params['s_order_no']);
            //Log::debug("getDataAone edit_id = ".$edit_id);
            $inventory_a = new InventoryA();
            $details_a =  $inventory_a->getDataMiniInvA()->toArray();
            $inventory_z = new InventoryZ();
            $details_z =  $inventory_z->getDataMiniInvZ()->toArray();
            $details = array_merge($details_a, $details_z);

            return response()->json(
                ['result' => $result, 'details' => $details, 
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
     * 取得（request）
     *
     * @return void
     */
    public function getStockA(Request $request){
        //$params_order_info1 = $request->order_info;
        //Log::info("ViewInventoryController getDataAFunc paramsget = ".$params_order_info1);
        //Log::debug("debug --".$message);
        $this->array_messagedata = array();
        try {
            $details = $this->getStockAFunc($request);
            return response()->json(
                ['result' => true, 'details3' => $details,
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
    public function getStockAFunc($request){

        $this->array_messagedata = array();
        $result = true;
        try {

            // パラメータセット
            $params = array();
            $params_stock_month = null;
            $params_order_info = null;
            $params_order_no = null;
            $params_company_id = null;
            $params_product_id2 = null;
            $params_receipt_day = null;
            $params_delivery_day= null;
            $params_orderfr = null;

            $stock_a = new StockA();
            if (isset($request->keyparams)) {
                $params = $request->keyparams;
                if (!empty($params['stock_month'])) {
                    $params_stock_month = $params['stock_month'];
                    $stock_a->setParamStockmonthAttribute($params_stock_month);
                }
                if (!empty($params['order_info'])) {
                    $params_order_info = $params['order_info'];
                    $stock_a->setParamOrderinfoAttribute($params_order_info);
                }
                if (!empty($params['order_no'])) {
                    $params_order_no = $params['order_no'];
                    $stock_a->setParamOrdernoAttribute($params_order_no);
                }
                if (!empty($params['company_id'])) {
                    $params_company_id = $params['company_id'];
                    $stock_a->setParamCompanyidAttribute($params_company_id);
                }
                if (!empty($params['product_id2'])) {
                    $params_product_id2 = $params['product_id2'];
                    $stock_a->setParamProductid2Attribute($params_product_id2);
                }
                if (!empty($params['receipt_day'])) {
                    $params_receipt_day = $params['receipt_day'];
                    $stock_a->setParamReceiptdayAttribute($params_receipt_day);
                }
                if (!empty($params['delivery_day'])) {
                    $params_delivery_day = $params['delivery_day'];
                    $stock_a->setParamDeliverydayAttribute($params_delivery_day);
                }
                if (!empty($params['orderfr'])) {
                    $params_orderfr = $params['orderfr'];
                    $stock_a->setParamOrderfrAttribute($params_orderfr);
                }
            }

            //$inventory_a->setParamEditidAttribute($edit_id);
            $details =  $stock_a->getDataStock();

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
            if (!isset($params['s_order_no'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "edit_id", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => null,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $s_order_no = $params['s_order_no'];
            //Log::debug("getDataAone edit_id = ".$edit_id);
            $inventory_a = new InventoryA();
            $inventory_a->setParamOrdernoAttribute($s_order_no);
            $details =  $inventory_a->getSearchA();

            return response()->json(
                ['result' => $result, 'details' => $details, 's_order_no' => $s_order_no,
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





}
