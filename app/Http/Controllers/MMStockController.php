<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\MMStock;
use App\MatManage;

class MMStockController extends Controller
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
            $rv_product_code = !empty($_GET["product_code"]) ? $_GET['product_code'] : "";
            $rv_receipt_day = !empty($_GET["receipt_day"]) ? $_GET['receipt_day'] : "";
            $rv_delivery_day = !empty($_GET["delivery_day"]) ? $_GET['delivery_day'] : "";
            $rv_orderfr = !empty($_GET["orderfr"]) ? $_GET['orderfr'] : "";

	        return view('stock_work_a', [
            	'order_info' => $rv_order_info,
            	'order_no' => $rv_order_no,
            	'company_id' => $rv_company_id,
            	'product_code' => $rv_product_code,
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
        return view('mm_home');
    }

    public function indexStock()
    {
        return view('mm_stock');
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
    public function storeAll(Request $request){
        Log::debug("storeAll in ");
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
            $marks = isset($params['marks']) ? $params['marks'] : "";
            //Log::debug("getDataDust details = ".$details);


            $mmstock = new MMStock();
            $systemdate = Carbon::now();
            $inputsys = "manual";
            $table = 'mmstock';


            $max_stock_month = DB::table($table)->max('stock_month');
            $distinct_stock_month = DB::table($table)
            ->select('stock_month')
            ->where('marks', $marks)
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
                    'stock_now_inventory' => null,
                    'stock_nbox' => '',
                    'stock_month' => $stock_month,
                    'status' => 'wait',
                    'marks' => $marks,
                    'created_at' => $systemdate
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
    public function store(Request $request){
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
                ['result' => $result, 'id' => $re_data['id'], 'inv_id' => $re_data['inv_id'], 'product_code' => $re_data['product_code'], 'product_name' => $re_data['product_name'],
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
    private function insert($details){
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
            $stock_a->setProductcodeAttribute($details['product_code']);
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
    public function fix(Request $request){
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
            $re_data = $this->update($details,$upkind);
            return response()->json(
                ['result' => $result, 'id' => $re_data['id'], 'product_code' => $re_data['product_code'], 'product_name' => $re_data['product_name'],
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
    public function status(Request $request){
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
            $re_data = $this->update($details,$upkind);
            return response()->json(
                ['result' => $result, 'id' => $re_data['id'], 'product_code' => $re_data['product_code'], 'product_name' => $re_data['product_name'],
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
    private function update($details,$upkind){
        $systemdate = Carbon::now();
        $updateuser = "Stock";
        //$stock_now_inventory = isset($details['stock_now_inventory']) ? $details['stock_now_inventory'] : "";
        //$stock_nbox = isset($details['stock_nbox']) ? $details['stock_nbox'] : "";
        $mm_stock = new MMStock();
        //$user = Auth::user();
        //$login_user_code = $user->code;
        //$login_account_id = $user->account_id;

        DB::beginTransaction();
        try{
            //$carbon = new Carbon($details['apply_term_from']);
            //$from = $carbon->copy()->format('Ymd');
            //$inventory_a->setApplytermfromAttribute($from);

            $mm_stock->setIdAttribute($details['id']);
            $mm_stock->setInvidAttribute($details['inv_id']);
            $mm_stock->setProductnameAttribute($details['product_name']);
            $mm_stock->setProductcodeAttribute($details['product_code']);
            $mm_stock->setUnitAttribute($details['unit']);
            $mm_stock->setQuantityAttribute($details['quantity']);
            $mm_stock->setNowinventoryAttribute($details['now_inventory']);
            $mm_stock->setNboxAttribute($details['nbox']);
            $mm_stock->setStocknowinventoryAttribute($details['stock_now_inventory']);
            $mm_stock->setStocknboxAttribute($details['stock_nbox']);
            $mm_stock->setUnitpriceAttribute($details['unit_price']);
            $mm_stock->setRemarksAttribute($details['remarks']);
            $mm_stock->setStatusAttribute($details['status']);
            $mm_stock->setMarksAttribute($details['marks']);
            $mm_stock->setStockmonthAttribute($details['stock_month']);
            $mm_stock->setUpdateduserAttribute($updateuser);
            $mm_stock->setUpdatedatAttribute($systemdate);

            
            //if ($details['id'] == "" || $details['id'] == null) {
            if ($upkind == 1 || $upkind == 2 ) {
                    //$inventory_a->setAccountidAttribute($login_account_id);
                $re_data = $mm_stock->insertDataStock($upkind);
            } else {
                //$inventory_a->setParamAccountidAttribute($login_account_id);
                $re_data = $mm_stock->updateDataStock($upkind);
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
    public function getDataMini(Request $request){
        //Log::debug("getDataAone in ");
        $this->array_messagedata = array();
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

            $params_marks = null;

            //$s_order_no = $params['s_order_no'];
            //Log::debug("getDataAsearch params[s_order_no] = ".$params['s_order_no']);
            //Log::debug("getDataAone edit_id = ".$edit_id);
            /*
            $inventory_a = new InventoryA();
            $details_a =  $inventory_a->getDataMiniInvA()->toArray();
            $inventory_z = new InventoryZ();
            $details_z =  $inventory_z->getDataMiniInvZ()->toArray();
            $details = array_merge($details_a, $details_z);
            */
            $material_management = new MatManage();
            if (isset($request->keyparams)) {
                $params = $request->keyparams;
                if (!empty($params['marks'])) {
                    $params_marks = $params['marks'];
                    $material_management->setParamMarksAttribute($params_marks);
                }
            }

            $details =  $material_management->getDataMiniMM();

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
    public function getStock(Request $request){
        //$params_order_info1 = $request->order_info;
        //Log::info("ViewInventoryController getDataAFunc paramsget = ".$params_order_info1);
        //Log::debug("debug --".$message);
        $this->array_messagedata = array();
        try {
            $details = $this->getStockFunc($request);
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
    public function getStockFunc($request){

        $this->array_messagedata = array();
        $result = true;
        try {

            // パラメータセット
            $params = array();
            $params_stock_month = null;
            $params_product_code = null;
            $params_orderfr = null;
            $params_marks = null;

            $mm_stock = new MMStock();
            if (isset($request->keyparams)) {
                $params = $request->keyparams;
                if (!empty($params['stock_month'])) {
                    $params_stock_month = $params['stock_month'];
                    $mm_stock->setParamStockmonthAttribute($params_stock_month);
                }
                if (!empty($params['marks'])) {
                    $params_marks = $params['marks'];
                    $mm_stock->setParamMarksAttribute($params_marks);
                }
                if (!empty($params['order_no'])) {
                    $params_order_no = $params['order_no'];
                    $mm_stock->setParamOrdernoAttribute($params_order_no);
                }
                if (!empty($params['product_code'])) {
                    $params_product_code = $params['product_code'];
                    $mm_stock->setParamProductcodeAttribute($params_product_code);
                }
                if (!empty($params['orderfr'])) {
                    $params_orderfr = $params['orderfr'];
                    $mm_stock->setParamOrderfrAttribute($params_orderfr);
                }
            }

            //$inventory_a->setParamEditidAttribute($edit_id);
            $details =  $mm_stock->getDataStock();

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
    public function getDatasearch(Request $request){
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
    public function getDataone(Request $request){
        //Log::debug("getDataAone in ");
        $this->array_messagedata = array();
        $edit_id = "";
        $product_code = "";
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
            //Log::debug("getDataAone params[product_code] = ".$params['product_code']);
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

            
            $product_code = $params['product_code'];
            //Log::debug("getDataAone product_code = ".$product_code);
            if(isset($product_code)) {
                $inventory_a2 = new InventoryA();
                $inventory_a2->setParamProductcodeAttribute($product_code);
                $details2 =  $inventory_a2->getDataInvA();
            }  else {
                $details2 = "";
            }
        


            return response()->json(
                ['result' => $result, 'details' => $details, 'details2' => $details2, 'edit_id' => $edit_id, 'product_code' => $product_code,
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
