<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\ApiCommonController;
use App\Http\Controllers\ExcelSpreadsheetController;
use App\ImportBackOrder;
use App\BackOrder;
use Carbon\Carbon;

class StoreBackOrderController extends Controller
{
    //--------------- テーブル名 -----------------------------------
    private $table_back_order = 'back_order';
    private $table_import_back_order = 'import_back_order';

    private $array_messagedata = array();

    //
    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        $authusers = Auth::user();
        $loginusercode = $authusers->code;
        return view('store_backorder', compact('authusers'));
    }

    /**
     * 登録
     *
     * @param Request $request
     * @return response
     */
    public function store(Request $request){
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
            if (!isset($params['user_code'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "user_code", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            if (!isset($params['file_name'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "file_name", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }

            // 登録
            DB::beginTransaction();
            $result = $this->insertImportBackorder($params);
            // 受注残テーブルに登録
            //      新規登録のみ
            $result = $this->insertBackorder($params);
            DB::commit();
            return response()->json(
                ['result' => $result,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            DB::rollBack();
            throw $pe;
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /** 
     *  インポート受注残の登録
     *
     */
    public function insertImportBackorder($params){
        $array_detailes = array();
        try {
            // EXCELデータ取得
            $login_user_code = Auth::user()->code;
            // getExcelRowData implement
            // cellnodata セルに値が無い時に値を指定
            // calcresult trueを指定するとセル内の計算結果を返して、falseなら計算式が表示される
            // cellformat 各セルのフォーマットを利用するかしないか
            // indexkey trueだと、A1形式がキーになった配列。falseだと0から始まる数字がキーなった配列
            // Log::debug('insertBackorder file_name = '.$params['file_name']);
            $array_impl_getExcelRowData = array (
                'filename' => $params['file_name'],
                'cellnodata' => null,
                'calcresult' => true,
                'cellformat' => false,
                'indexkey' => true
            );
            $excelspread = new ExcelSpreadsheetController();
            $details = $excelspread->getExcelRowData($array_impl_getExcelRowData);
            $item_count = 0;
            foreach($details as $item) {
                if (!isset($item['E'])) {
                    break;
                }
                $item_count += 1;
                // 見出しskip
                if ($item_count > 1) {
                    $array_items = array(
                        'out_seq' => $item['A'],
                        'order_date' => $item['B'],
                        'row_seq' => $item['C'],
                        'drawing_no' => $item['D'],
                        'order_no' => $item['E'],
                        'customer_name' => $item['F'],
                        'model_number' => $item['G'],
                        'product_name' => $item['H'],
                        'quality_name' => $item['I'],
                        'order_count' => $item['J'],
                        'supply_date' => $item['K'],
                        'order_kingaku' => $item['L'],
                        'outline_name' => $item['M'],
                        'unit_price' => $item['N'],
                        'created_user' => $login_user_code,
                        'updated_user' => null
                    );
                    $array_detailes[] = $array_items;
                }

            }
            // Log::debug('insertBackorder item_count = '.$item_count);

            $imp_model = new ImportBackOrder();
            $imp_model->delAlldata();
            $collection = collect($array_detailes);
            // chunk()で1000個ごとに分割する
            $imp_data = $collection->chunk(1000);
            // 1000レコードを約10回インサート
            foreach ($imp_data as $value) {
                $imp_model->insertArray($value->toArray());
            }
            return true;
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.data_insert_error'));
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /** 
     *  受注残の登録
     *      存在しない場合のみ
     */
    public function insertBackorder($params){
        $array_detailes = array();
        $backorder_model = new BackOrder();
        try {
            $login_user_code = Auth::user()->code;
            $import_back_orders = DB::table($this->table_import_back_order)
                ->orderBy('order_no')
                ->orderBy('model_number')
                ->orderBy('row_seq')
                ->orderBy('order_date')
                ->get();
            // 
            $backorder_order_no = null;
            $item_backorder_order_no = null;
            $backorder_seq = 0;
            foreach($import_back_orders as $item) {
                if (isset($item->order_no)) {
                    $item_backorder_order_no = $item->order_no;
                } else {
                    $item_backorder_order_no = '00-00-0000';
                }
                if ($item_backorder_order_no  != $backorder_order_no) {
                    $backorder_seq = 0;
                    $result_exists = DB::table($this->table_back_order)
                    ->where('order_no', $item_backorder_order_no )
                    ->exists();
                    if ($result_exists) {
                        DB::table($this->table_back_order)
                        ->where('order_no', $item_backorder_order_no)
                        ->delete();
                    }
                    $backorder_order_no = $item->order_no;
                }
                $backorder_seq += 1;
                // Log::debug('insertBackorder $backorder_seq = '.$backorder_seq);
                $backorder_model->setOutseqAttribute($item->out_seq);
                $backorder_model->setOrdernoAttribute($item_backorder_order_no );
                $backorder_model->setSeqAttribute($backorder_seq);
                $backorder_model->setOrderdateAttribute(date('Ymd', ($item->order_date - (int)Config::get('const.BASEVALUE.excel_serial_base')) * 60 * 60 * 24));
                if (strpos($item->row_seq, "/")) {
                    $backorder_model->setRowseqAttribute($item->row_seq);
                } else {
                    $backorder_model->setRowseqAttribute(date('n/j', ($item->row_seq - (int)Config::get('const.BASEVALUE.excel_serial_base')) * 60 * 60 * 24));
                }
                $backorder_model->setDrawingnoAttribute($item->drawing_no);
                $backorder_model->setCustomernameAttribute($item->customer_name);
                $backorder_model->setModelnumberAttribute($item->model_number);
                $backorder_model->setProductnameAttribute($item->product_name);
                $backorder_model->setQualitynameAttribute($item->quality_name);
                $backorder_model->setOrdercountAttribute($item->order_count);
                if (is_numeric(substr($item->supply_date,0,1))) {
                    $backorder_model->setSupplydateAttribute(date('Ymd', ($item->supply_date - (int)Config::get('const.BASEVALUE.excel_serial_base')) * 60 * 60 * 24));
                } else {
                    $backorder_model->setSupplydateAttribute($item->supply_date);
                }
                $backorder_model->setOrderkingakuAttribute($item->order_kingaku);
                $backorder_model->setOutlinenameAttribute($item->outline_name);
                $backorder_model->setUnitpriceAttribute($item->unit_price);
                $backorder_model->setIsUpdateAttribute(false);
                $backorder_model->setCreateduserAttribute($login_user_code);
                $backorder_model->setCreatedatAttribute(Carbon::now());
                $backorder_model->insert();
            }
            return true;
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.data_insert_error'));
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }
}
