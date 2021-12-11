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

class MobileAccessController extends Controller
{
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
        Log::debug('MobileAccessController index in ');
        // リダイレクト
        // return redirect()->route('edit_work_order.edithome', [
        //     'order_no' => $_GET["order_no"],
        //     'seq' => $_GET["seq"]
        // ]);
	if (isset($_GET["order_no"])) {
        	Log::debug('MobileAccessController index in '.$_GET["order_no"]);
        	Log::debug('MobileAccessController index in '.$_GET["seq"]);
        	Log::debug('MobileAccessController index in '.$_GET["device"]);
        	Log::debug('MobileAccessController index in '.$_GET["user_code"]);
	        return view('process_info', [
            	'order_no' => $_GET["order_no"],
            	'seq' => $_GET["seq"],
            	'device' => $_GET["device"],
            	'user_code' => $_GET["user_code"]
	        ]);
	} else {
	        $authusers = Auth::user();

	        return view('home',
        	    compact(
                'authusers'
	            ));
	} 
    }

    /**
     * 取得
     *
     * @param Request $request
     * @return void
     */
    public function getProductheader(Request $request) { 
        Log::debug('MobileAccessController getProductheader in ');
        $this->array_messagedata = array();
        $details = new Collection();
        $result = true;
        try {
            // パラメータチェック
            $params = array();
            $params_order_no = null;
            $params_seq = null;
            $params_kind = null;
            $params_device = null;
            $params_user_code = null;
            if (isset($request->keyparams)) {
                $params = $request->keyparams;
                if (isset($params['order_no'])) {
                    $params_order_no = $params['order_no'];
                }
                if (isset($params['seq'])) {
                    $params_seq = $params['seq'];
                }
                if (isset($params['kind'])) {
                    $params_kind = $params['kind'];
                }
                if (isset($params['device'])) {
                    $params_device = $params['device'];
                }
                if (isset($params['user_code'])) {
                    $params_user_code = $params['user_code'];
                }
            }

            $params = array();

            // ログインユーザの権限取得
            $user = Auth::user();
            $login_user_code = $user->code;
            $login_account_id = $user->account_id;
            $progress_header_model = new Progressheader();
            $progress_header_model->setParamOrdernoAttribute($params_order_no);
            $progress_header_model->setParamSeqAttribute($params_seq);
            $progress_header_model->setParamDevicecodeAttribute($params_device);
            $progress_header_model->setParamUsercodeAttribute($params_user_code);
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

    /**
     * 登録
     *
     * @param Request $request
     * @return void
     */
    public function putProcessHistoery(Request $request) { 
        Log::debug('MobileAccessController putProcessHistoery in ');
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
            $data = $params['form'];
            $order_no = $data["order_no"];
            $seq = $data["seq"];
            $work_kind = $data["kind"];
            $device_code = $data["device_code"];
            $user_code = $data["user_code"];
            $row_seq = $data["row_seq"];
            $progress_no = $data["progress_no"];
            if ($data["process_time_h"] == null || $data["process_time_h"] == "") {
                $process_time_h = 0;
            } else {
                $process_time_h = $data["process_time_h"];
            }
            if ($data["process_time_m"] == null || $data["process_time_m"] == "") {
                $process_time_m = 0;
            } else {
                $process_time_m = $data["process_time_m"];
            }
            // 新規の場合加工履歴No=1に、既存はMAX番号+1を設定
            // データが存在するか
            $process_history_no = 1;
            $result_exists = DB::table($this->table_process_histories)
                ->where('order_no', $order_no)
                ->where('seq', $seq)
                ->exists(); 
            if ($result_exists) {
                // MAX番号+1を設定
                $result_maxseq = DB::table($this->table_process_histories)
                ->where('order_no', $order_no)
                ->where('seq', $seq)
                ->max('process_history_no');
                $process_history_no = $result_maxseq + 1;
            }
            $user = Auth::user();
            $login_user_code = $user->code;

            DB::beginTransaction();
            $process_histories_model = new ProcessHistory();
            $process_histories_model->setOrdernoAttribute($order_no);
            $process_histories_model->setSeqAttribute($seq);
            $process_histories_model->setProcesshistorynoAttribute($process_history_no);
            $process_histories_model->setWorkkindAttribute($work_kind);
            $process_histories_model->setDevicecodeAttribute($device_code);
            $process_histories_model->setUsercodeAttribute($user_code);
            $process_histories_model->setRowseqAttribute($row_seq);
            $process_histories_model->setProgressnoAttribute($progress_no);
            $process_histories_model->setProcesshistorytimeAttribute(Carbon::now());
            $process_histories_model->setProcessTimeHAttribute($process_time_h);
            $process_histories_model->setProcessTimeMAttribute($process_time_m);
            $process_histories_model->setCreateduserAttribute($login_user_code);
            $process_histories_model->setCreatedatAttribute(Carbon::now());
            $process_histories_model->insert();
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
}
