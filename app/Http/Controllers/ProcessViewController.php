<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Controllers\ApiCommonController;
use App\Progressheader;
use App\ProcessHistory;

class ProcessViewController extends Controller
{
    //
    //
    protected $table_progress_headers = 'progress_headers';
    protected $table_progress_details = 'progress_details';
    protected $table_product_processes = 'product_processes';
    protected $table_process_histories = 'process_histories';
    protected $table_offices = 'offices';
    protected $table_customers = 'customers';
    protected $table_products = 'products';
    protected $table_devices = 'devices';

    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        return view('process_view', [
        ]);
    }

    /**
     * 取得
     *
     * @param Request $request
     * @return void
     */
    public function getProcessView(Request $request) { 
        Log::debug('ProcessViewController getProcessView in ');
        $this->array_messagedata = array();
        $details = new Collection();
        $result = true;
        try {
            // パラメータチェック
            // ログインユーザの権限取得
            $user = Auth::user();
            $login_user_code = $user->code;
            $login_account_id = $user->account_id;
            $progress_header_model = new Progressheader();
            $progress_header_model->setParamOrdernoAttribute($params_order_no);
            $progress_header_model->setParamSeqAttribute($params_seq);
            $details = $progress_header_model->getProductheaderM();
            return response()->json(
                ['result' => true, 'details' => $details,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_product_processes, Config::get('const.LOG_MSG.data_select_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table_product_processes, Config::get('const.LOG_MSG.data_select_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }
}
