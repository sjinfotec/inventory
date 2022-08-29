<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\MatManage;

class MatManageController extends Controller
{


    public function index()
    {
    	//Log::debug('ViewInventoryController index in GET[order_info] = '.$_GET["order_info"]);
        // リダイレクト
        // return redirect()->route('edit_work_order.edithome', [
        //     'order_no' => $_GET["order_no"],
        //     'seq' => $_GET["seq"]
        // ]);
    	if (isset($_GET["product_name"]) || isset($_GET["mdate"]) || isset($_GET["charge"])) {
            $rv_product_name = !empty($_GET["product_name"]) ? $_GET['product_name'] : "";
            $rv_mdate = !empty($_GET["mdate"]) ? $_GET['mdate'] : "";
            $rv_charge = !empty($_GET["charge"]) ? $_GET['charge'] : "";
            $rv_orderfr = !empty($_GET["orderfr"]) ? $_GET['orderfr'] : "";

	        return view('matmanage', [
            	'product_name' => $rv_product_name,
            	'mdate' => $rv_mdate,
            	'charge' => $rv_charge,
            	'orderfr' => $rv_orderfr
	        ]);
	    }
        else {
	        //$authusers = Auth::user();
            return view('matmanage'
            );

    	} 

    }

    public function home()
    {
        return view('mm_home'
        );
    }

    public function dust()
    {
        return view('mm_dust'
        );
    }


    public function search()
    {
    
        return view('matmanage'
        );
    }



    /**
     * 新規登録
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request){
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
            $marks = $params['marks'];
            $re_data = $this->insert($details,$marks);
            if (!isset($re_data['id'])) {
                $result = false;
            }

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
    }

    /**
     * INSERT
     *
     * @param [type] $inputs
     * @return void
     */
    private function insert($details,$marks){
        $material_management = new MatManage();
        $systemdate = Carbon::now();
 
        DB::beginTransaction();
        try{


            $material_management->setMdateAttribute($details['mdate']);
            $material_management->setDepartmentAttribute($details['department']);
            $material_management->setChargeAttribute($details['charge']);
            $material_management->setProductnameAttribute($details['product_name']);
            $material_management->setProductcodeAttribute($details['product_code']);
            $material_management->setProductnumberAttribute($details['product_number']);
            $material_management->setUnitAttribute($details['unit']);
            $material_management->setQuantityAttribute($details['quantity']);
            $material_management->setReceiptAttribute($details['receipt']);
            $material_management->setDeliveryAttribute($details['delivery']);
            $material_management->setNowinventoryAttribute($details['now_inventory']);
            $material_management->setNboxAttribute($details['nbox']);
            $material_management->setOrderaddressAttribute($details['order_address']);
            $material_management->setUnitpriceAttribute($details['unit_price']);
            $material_management->setTotalAttribute($details['total']);
            $material_management->setRemarksAttribute($details['remarks']);
            $material_management->setNoteAttribute($details['note']);
            $material_management->setStatusAttribute($details['status']);
            $material_management->setMarksAttribute($marks);
            $material_management->setCreateduserAttribute($details['charge']);
            $material_management->setCreatedatAttribute($systemdate);
            $material_management->setIsdeletedAttribute($details['is_deleted']);

            
            /*
            $is_exists = $inventory_a->isExistsInfo();
            if($is_exists){
                $inventory_a->delDataA();
            }
            */

            $re_data = $material_management->insertData(3);   //引数 3 ＝ 新規登録
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
        $material_management = new MatManage();
        //$user = Auth::user();
        //$login_user_code = $user->code;
        //$login_account_id = $user->account_id;

        DB::beginTransaction();
        try{
            //$carbon = new Carbon($details['apply_term_from']);
            //$from = $carbon->copy()->format('Ymd');
            //$inventory_a->setApplytermfromAttribute($from);


            $material_management->setIdAttribute($details['id']);   
            $material_management->setMdateAttribute($details['mdate']);
            $material_management->setDepartmentAttribute($details['department']);
            $material_management->setChargeAttribute($details['charge']);
            $material_management->setProductnameAttribute($details['product_name']);
            $material_management->setProductcodeAttribute($details['product_code']);
            $material_management->setProductnumberAttribute($details['product_number']);
            $material_management->setUnitAttribute($details['unit']);
            $material_management->setQuantityAttribute($details['quantity']);
            $material_management->setReceiptAttribute($details['receipt']);
            $material_management->setDeliveryAttribute($details['delivery']);
            $material_management->setNowinventoryAttribute($details['now_inventory']);
            $material_management->setNboxAttribute($details['nbox']);
            $material_management->setOrderaddressAttribute($details['order_address']);
            $material_management->setUnitpriceAttribute($details['unit_price']);
            $material_management->setTotalAttribute($details['total']);
            $material_management->setRemarksAttribute($details['remarks']);
            $material_management->setNoteAttribute($details['note']);
            $material_management->setStatusAttribute($details['status']);
            $material_management->setMarksAttribute($details['marks']);
            $material_management->setCreateduserAttribute($details['charge']);
            $material_management->setUpdateduserAttribute($details['charge']);
            $material_management->setCreatedatAttribute($systemdate);
            $material_management->setUpdatedatAttribute($systemdate);
            $material_management->setIsdeletedAttribute($details['is_deleted']);


            //if ($details['id'] == "" || $details['id'] == null) {
            if ($upkind == 1 || $upkind == 2 ) {
                    //$inventory_a->setAccountidAttribute($login_account_id);
                $re_data = $material_management->insertData($upkind);
            } else {
                //$inventory_a->setParamAccountidAttribute($login_account_id);
                $re_data = $material_management->updateData($upkind);
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
    public function getData(Request $request){
        $this->array_messagedata = array();
        try {
            $details = $this->getDataFunc($request);
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
     * 取得
     *
     * @return void
     */
    public function getDataFunc($request){
        $this->array_messagedata = array();
        //$details = new Collection();
        $result = true;
        try {
            // パラメータセット
            $params = array();
            $params_product_name = null;
            $params_mdate = null;
            $params_charge = null;
            $params_orderfr = null;
            $params_marks = null;
            $params_is_deleted = null;

            $material_management = new MatManage();

            if (isset($request->keyparams)) {
                $params = $request->keyparams;
                if (!empty($params['product_name'])) {
                    $params_product_name = $params['product_name'];
                    $material_management->setParamProductnameAttribute($params_product_name);
                }
                if (!empty($params['mdate'])) {
                    $params_mdate = $params['mdate'];
                    $material_management->setParamMdateAttribute($params_mdate);
                }
                if (!empty($params['charge'])) {
                    $params_charge = $params['charge'];
                    $material_management->setParamChargeAttribute($params_charge);
                }
                if (!empty($params['orderfr'])) {
                    $params_orderfr = $params['orderfr'];
                    $material_management->setParamOrderfrAttribute($params_orderfr);
                }
                if (!empty($params['marks'])) {
                    $params_marks = $params['marks'];
                    $material_management->setParamMarksAttribute($params_marks);
                }
                if (!empty($params['is_deleted'])) {
                    $params_is_deleted = $params['is_deleted'];
                    $material_management->setParamIsdeletedAttribute($params_is_deleted);
                }
            }

            $details =  $material_management->getDataMM();

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

            $material_management = new MatManage();

            if (isset($request->keyparams)) {
                $params = $request->keyparams;
                if (!empty($params['marks'])) {
                    $params_marks = $params['marks'];
                    $material_management->setParamMarksAttribute($params_marks);
                }
            }

            $details =  $material_management->getDataMMtotal();

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
        $s_charge = "";
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
            if (!isset($params['s_charge']) && !isset($params['s_product_name'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "edit_id", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => null,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $s_charge = isset($params['s_charge']) ? $params['s_charge'] : "";
            $s_product_name = isset($params['s_product_name']) ? $params['s_product_name'] : "";
            $s_history = isset($params['s_history']) ? $params['s_history'] : "";
            //Log::debug("getDataZsearch s_company_name = ".$s_company_name);
            $material_management = new MatManage();
            if(isset($s_charge))      $material_management->setParamChargeAttribute($s_charge);
            if(isset($s_product_name))  $material_management->setParamProductnameAttribute($s_product_name);
            if(isset($s_history))      $material_management->setParamSHistoryAttribute($s_history);
            if (!empty($params['marks'])) {
                $params_marks = $params['marks'];
                $material_management->setParamMarksAttribute($params_marks);
            }

            $details =  $material_management->getSearch();

            return response()->json(
                ['result' => $result, 'details' => $details, 's_charge' => $s_charge, 's_product_name' => $s_product_name, 's_history' => $s_history,
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
        //Log::debug("getDataone in ");
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
            //Log::debug("getDataZone params[edit_id] = ".$params['edit_id']);
            //Log::debug("getDataZone params[product_code] = ".$params['product_code']);
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
            $material_management = new MatManage();
            $material_management->setParamEditidAttribute($edit_id);
            $details =  $material_management->getDataMM();

            
            $product_code = $params['product_code'];
            //Log::debug("getDataAone product_code = ".$product_code);
            if(isset($product_code)) {
                $material_management2 = new MatManage();
                $material_management2->setParamProductcodeAttribute($product_code);
                $details2 =  $material_management2->getDataMM();
            } else {
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



    /** ゴミ箱取得
     *
     * @return list results
     */
    public function getDataDust(Request $request){
        //Log::debug("getDataDust in ");
        $this->array_messagedata = array();
        $status = "";
        $marks = "";
        $is_deleted = "";
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

            //$status = $params['status'];
            $marks = isset($params['marks']) ? $params['marks'] : "";
            $is_deleted = $params['is_deleted'];
            //Log::debug("getDataDust status = ".$status);
            $material_management = new MatManage();
            //$material_management->setParamStatusAttribute($status);
            if(isset($marks))    $material_management->setParamMarksAttribute($marks);
            $material_management->setParamIsdeletedAttribute($is_deleted);
            $details =  $material_management->getDataMM();

            /*
            $inventory_z = new InventoryZ();
            $inventory_z->setParamStatusAttribute($status);
            $details2 =  $inventory_z->getDataInvZ();
            */


            return response()->json(
                ['result' => $result, 'details' => $details, 'details2' => null, 'status' => $status, 
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
        $material_management = new MatManage();
        $systemdate = Carbon::now();
 
        DB::beginTransaction();
        try{

            $material_management->setIdAttribute($details['id']);
            $material_management->setProductcodeAttribute($details['product_code']);
            /*
            $material_management->setMdateAttribute($details['mdate']);
            $material_management->setDepartmentAttribute($details['department']);
            $material_management->setChargeAttribute($details['charge']);
            $material_management->setProductnameAttribute($details['product_name']);
            $material_management->setProductcodeAttribute($details['product_code']);
            $material_management->setUnitAttribute($details['unit']);
            $material_management->setQuantityAttribute($details['quantity']);
            $material_management->setReceiptAttribute($details['receipt']);
            $material_management->setDeliveryAttribute($details['delivery']);
            $material_management->setNowinventoryAttribute($details['now_inventory']);
            $material_management->setNboxAttribute($details['nbox']);
            $material_management->setOrderaddressAttribute($details['order_address']);
            $material_management->setUnitpriceAttribute($details['unit_price']);
            $material_management->setTotalAttribute($details['total']);
            $material_management->setRemarksAttribute($details['remarks']);
            $material_management->setNoteAttribute($details['note']);
            $material_management->setStatusAttribute($details['status']);
            $material_management->setMarksAttribute($marks);
            $material_management->setCreateduserAttribute($details['charge']);
            $material_management->setCreatedatAttribute($systemdate);
            $material_management->setIsdeletedAttribute($details['is_deleted']);
            */
            
            /*
            $is_exists = $inventory_a->isExistsInfo();
            if($is_exists){
                $inventory_a->delDataA();
            }
            */

            $re_data = $material_management->delData($delkind);
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
