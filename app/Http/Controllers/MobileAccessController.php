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
use App\Progressdetaile;
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
    public function putProcessHistory(Request $request) { 
        Log::debug('MobileAccessController putProcessHistory in ');
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
            $process_time_h = 0;
            $process_time_m = 0;
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
            if ($data["process_time_h"] == null || $data["process_time_h"] == "") {
                $process_time_h = 0;
            } else {
                $process_time_h = $data["process_time_h"];
            }
            $device_code = $data["device_code"];
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
            // 指示書／管理書の明細のmax工程Noを求める
            $progress_no = 1;
            $product_processes_code = "01";
            $product_processes_detail_no = 0;
            $department_code = null;
            $setup_history_no = null;
            $setup_time_m = 0;
            $setup_time_h = 0;
            $complete_date = null;
            $qr_code = null;
            $progress_details_cnt = 0;
            $created_user = null;
            $updated_user = null;
            $created_at = null;
            $updated_at = null;
            $progress_details_model = new Progressdetaile();
            $progress_details_model->setParamOrdernoAttribute($order_no);
            $progress_details_model->setParamSeqAttribute($seq);
            $progress_details_model->setParamDevicecodeAttribute($device_code);
            $progress_details_model->setParamUserscodeAttribute($user_code);
            $progress_details_items = new Collection();
            $progress_details_items = $progress_details_model->getProductdetaile();
            foreach($progress_details_items as $item) {
                $progress_details_cnt = $progress_details_cnt + 1;
                $progress_no = $item->progress_no;
                $product_processes_code = $item->product_processes_code;
                $product_processes_detail_no = $item->product_processes_detail_no;
                $department_code = $item->department_code;
                // $process_history_no = $item->process_history_no;
                // $work_kind = $item->work_kind;
                $setup_history_no = $item->setup_history_no;
                $setup_time_m = $item->setup_time_m;
                $setup_time_h = $item->setup_time_h;
                $complete_date = $item->complete_date;
                $qr_code = $item->qr_code;
                $created_user = $item->created_user;
                $updated_user = $item->updated_user;
                $created_at = $item->created_at;
                $updated_at = $item->updated_at;
                break;
            }
            // 存在しない場合
            if ($progress_details_cnt == 0) {
                $result_order_exists = DB::table($this->table_progress_details)
                    ->where('order_no', $order_no)
                    ->where('seq', $seq)
                    ->exists(); 
                if ($result_order_exists) {
                    // MAX番号+1を設定
                    $result_maxseq = DB::table($this->table_progress_details)
                        ->where('order_no', $order_no)
                        ->where('seq', $seq)
                        ->max('progress_no');
                    $progress_no = $result_maxseq + 1;
                }
            }

            DB::beginTransaction();
            $process_histories_model = new ProcessHistory();
            $process_histories_model->setOrdernoAttribute($order_no);
            $process_histories_model->setSeqAttribute($seq);
            $process_histories_model->setProcesshistorynoAttribute($process_history_no);
            $process_histories_model->setWorkkindAttribute($work_kind);
            $process_histories_model->setDevicecodeAttribute($device_code);
            $process_histories_model->setUsercodeAttribute($user_code);
            $process_histories_model->setRowseqAttribute($row_seq);
            Log::debug('mobile putProcessHistory process_histories_model $progress_no = '.$progress_no);
            $process_histories_model->setProgressnoAttribute($progress_no);
            $process_histories_model->setProcesshistorytimeAttribute(Carbon::now());
            $process_histories_model->setProcessTimeHAttribute($process_time_h);
            $process_histories_model->setProcessTimeMAttribute($process_time_m);
            $process_histories_model->setCreateduserAttribute($login_user_code);
            $process_histories_model->setCreatedatAttribute(Carbon::now());
            $process_histories_model->insert();
            // 指示書／管理書の明細に登録する
            $progress_details_model->setOrdernoAttribute($order_no);
            $progress_details_model->setSeqAttribute($seq);
            Log::debug('mobile putProcessHistory progress_details_model $progress_no = '.$progress_no);
            $progress_details_model->setProgressnoAttribute($progress_no);
            $progress_details_model->setProductprocessescodeAttribute($product_processes_code);
            $progress_details_model->setProductprocessesdetailnoAttribute($product_processes_detail_no);
            $progress_details_model->setDevicecodeAttribute($device_code);
            $progress_details_model->setDepartmentcodeAttribute($department_code);
            $progress_details_model->setUserscodeAttribute($user_code);
            $progress_details_model->setProcesshistorynoAttribute($process_history_no);
            $progress_details_model->setWorkkindAttribute($work_kind);
            if ($work_kind == Config::get('const.WORKKINDS.complete')) {
                if ($process_time_h == 0 && $process_time_m == 0) {
                    // 加工時間を自動計算する
                    $api_common = new ApiCommonController();
                    $array_impl_calcProcessTime = array (
                        'order_no' => $order_no,
                        'seq' => $seq,
                        'device_code' => $device_code,
                        'user_code' => $user_code,
                        'progress_no' => $progress_no
                    );
                    $calcdifftime = $api_common->calcProcessTime($array_impl_calcProcessTime);
                    Log::debug('mobile putProcessHistory $calcdifftime = '.$calcdifftime);
                    $process_time_h = (int)($calcdifftime / 60 / 60);
                    $process_time_m = ($calcdifftime - ($process_time_h * 60 * 60)) / 60;
                    Log::debug('mobile putProcessHistory $process_time_h = '.$process_time_h);
                    Log::debug('mobile putProcessHistory $process_time_m = '.$process_time_m);
                }
                // 完了日設定
                $dt = new Carbon();
                $complete_date = $dt->format('Ymd');
            } else {
                $complete_date = null;
            }
            $progress_details_model->setProcesstimemAttribute($process_time_m);
            $progress_details_model->setProcesstimehAttribute($process_time_h);
            $progress_details_model->setSetuphistorynoAttribute($setup_history_no);
            $progress_details_model->setSetuptimemAttribute($setup_time_m);
            $progress_details_model->setSetuptimehAttribute($setup_time_h);
            $progress_details_model->setCompletedateAttribute($complete_date);
            $progress_details_model->setQrcodeAttribute($qr_code);
            if ($progress_details_cnt == 0) {
                // 存在しない場合は登録
                $progress_details_model->setCreateduserAttribute($user_code);
                $progress_details_model->setUpdateduserAttribute(null);
                $progress_details_model->setCreatedatAttribute(Carbon::now());
                $progress_details_model->setUpdatedatAttribute(null);
                $progress_details_model->insert();
            } else {
                // 存在する場合は更新
                $progress_details_model->setParamOrdernoAttribute($order_no);
                $progress_details_model->setParamSeqAttribute($seq);
                $progress_details_model->setParamDevicecodeAttribute($device_code);
                $progress_details_model->setParamUserscodeAttribute($user_code);
                $progress_details_model->setCreateduserAttribute($created_user);
                $progress_details_model->setUpdateduserAttribute($created_at);
                $progress_details_model->setCreatedatAttribute(Carbon::now());
                $progress_details_model->setUpdatedatAttribute(null);
                $progress_details_model->updateDetail();
            }
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
