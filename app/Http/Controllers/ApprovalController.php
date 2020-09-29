<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use App\Http\Controllers\ApiCommonController;
use App\Demand;
use App\DemandsDetail;
use App\Approval;
use App\Confirm;

class ApprovalController extends Controller
{
    private $array_messagedata = array();

    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        return view('approval');
    }

    /**
     * 承認一覧取得処理
     *
     * @return void
     */
    public function listApproval(Request $request)
    {
        Log::debug('---- 承認一覧取得処理 開始 -------------- ');
        Log::debug('---- パラメータ　$request->situation = '.$request->situation);
        Log::debug('---- パラメータ　$request->doc_code = '.$request->doccode);
        Log::debug('---- パラメータ　$request->usercode = '.$request->usercode);

        $apicommon = new ApiCommonController();
        // reqestクエリーセット
        $situation = $apicommon->setRequestQeury($request->situation);
        $doc_code = $apicommon->setRequestQeury($request->doccode);
        $usercode = $apicommon->setRequestQeury($request->usercode);
        $user = Auth::user();
        $login_user_code = $user->code;
        $login_account_id = $user->account_id;
        if ($usercode == null || $usercode == "") {
            $usercode = $login_user_code;
        }

        $list_result = false;
        $array_result = null;
        $array_demand = array();
        $array_demandDeatail = array();
        $array_demandResult = array();
        $get_demands = array();
        $get_demandsdetail = array();
        try {
            // パラメータ設定
            $approval_model = new Approval();
            $approval_model->setParamAccountidAttribute($login_account_id);
            $approval_model->setParamDoccodeAttribute($doc_code);
            $approval_model->setParamUsercodeAttribute($usercode);
            // 適用期間日付の取得
            $dt = new Carbon();
            $target_date = $dt->format('Ymd');
            $array_result = $approval_model->getDemandList($target_date, $situation);
            $before_no = null;
            $id_cnt = 0;
            $data_cnt = 0;
            foreach ($array_result as $item) {
                if ($item->no != $before_no) {
                    if ($before_no != null) {
                        $array_demand = array_merge($array_demand, $get_demands);
                        $array_demandDeatail = array_merge($array_demand, $get_demandsdetail);
                        $array_demandResult[] = array(
                            'array_demand' => $get_demands,
                            'array_demandDeatail' => $get_demandsdetail
                        );
                        $get_demands = array();
                        $get_demandsdetail = array();
                    }
                    $data_cnt++;
                    if ($data_cnt > 10) {
                        break;
                    }
                    $get_demands[] = array(
                        'id' => $item->id,
                        'department_code' => $item->department_code,
                        'user_name' => $item->department_name,
                        'user_code' => $item->user_code,
                        'user_name' => $item->user_name,
                        'no' => $item->no,
                        'doc_code' => $item->doc_code,
                        'doc_code_name' => $item->doc_code_name,
                        'log_no' => $item->log_no,
                        'status' => $item->status,
                        'status_name' => $item->status_name,
                        'demand_date' => $item->demand_date,
                        'date_from' => $item->date_from,
                        'date_to' => $item->date_to,
                        'demand_date_name' => $item->demand_date_name,
                        'date_from_name' => $item->date_from_name,
                        'date_to_name' => $item->date_to_name,
                        'demand_department_name' => $item->demand_department_name,
                        'demand_user_name' => $item->demand_user_name,
                        'demand_reason' => $item->demand_reason,
                        'before_after' => $item->before_after,
                        'before_after_name' => $item->before_after_name,
                        'mail_result' => $item->mail_result,
                        'mail_result_name' => $item->mail_result_name,
                        'mail_address' => $item->mail_address,
                        'nmail_department_code' => $item->nmail_department_code,
                        'nmail_department_name' => $item->nmail_department_name,
                        'nmail_user_code' => $item->nmail_user_code,
                        'nmail_user_name' => $item->nmail_user_name
                    );
                    $before_no = $item->no;
                }
                $get_demandsdetail[] = array(
                    'detail_row_no' => $item->detail_row_no,
                    'detail_working_item' => $item->detail_working_item,
                    'detail_date_from' => $item->detail_date_from,
                    'detail_time_from' => $item->detail_time_from,
                    'detail_date_to' => $item->detail_date_to,
                    'detail_time_to' => $item->detail_time_to,
                    'detail_scheduled_time' => $item->detail_scheduled_time,
                    'detail_demand_reason' => $item->detail_demand_reason
                );
            }
            if (count($get_demands) > 0) {
                $id_cnt++;
                $array_demand = array_merge($array_demand, $get_demands);
                $array_demandDeatail = array_merge($array_demand, $get_demandsdetail);
                $array_demandResult[] = array(
                    'id_cnt' => $id_cnt,
                    'array_demand' => $get_demands,
                    'array_demandDeatail' => $get_demandsdetail
                );
            }
            $list_result = true;
        }catch(\PDOException $pe){
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_access_error'));
            Log::error($pe->getMessage());
        }catch(\Exception $e){
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_access_error'));
            Log::error($e->getMessage());
        }
        Log::debug('---- 申請一覧取得処理 終了 -------------- ');
        Log::debug('    $list_result'.$list_result);
        Log::debug('    count $array_demand'.count($array_demand));
        Log::debug('    count $array_demandDeatail'.count($array_demandDeatail));
        Log::debug('    count $array_demandResult'.count($array_demandResult));
        Log::debug('    count $this->array_messagedata'.count($this->array_messagedata));
        if (count($array_demandResult) == 0) {
            $array_demandResult = null;
        }
        if (count($array_demand) == 0) {
            $array_demand = null;
        }
        if (count($array_demandDeatail) == 0) {
            $array_demandDeatail = null;
        }
        return response()->json([
            'list_result' => $list_result,
            'array_demandresult' => $array_demandResult,
            'array_demand' => $array_demand,
            'array_demanddeatail' => $array_demandDeatail,
            Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata
            ]);
    }

    /**
     * 承認処理
     *
     * @return void
     */
    public function makeApproval(Request $request)
    {
        Log::debug('---- 承認処理 開始 -------------- ');
        Log::debug('---- パラメータ $request->doccode = '.$request->doccode);
        Log::debug('---- パラメータ $request->kbn = '.$request->kbn);
        Log::debug('---- パラメータ demandno = '.$request->demandedit["demandno"]);
        $apicommon = new ApiCommonController();
        // reqestクエリーセット
        $doc_code = $apicommon->setRequestQeury($request->doccode);
        $demandedit = $apicommon->setRequestQeury($request->demandedit);
        $demandkbn = $apicommon->setRequestQeury($request->kbn);
        $store_result = true;
        $already_demandno = "";
        if (isset($demandedit["demandno"])) {
            if ($demandedit["demandno"] != "") {
                $already_demandno = $demandedit["demandno"];
            } else {
                $store_result = false;
            }
        } else {
            $store_result = false;
        }
        $demand_now = "";
        if (isset($demandedit["demand_now"])) {
            if ($demandedit["demand_now"] != "") {
                $demand_now = $demandedit["demand_now"];
            }
        }
        $demanddate = "";
        if (isset($demandedit["demanddate"])) {
            if ($demandedit["demanddate"] != "") {
                $demanddate = $demandedit["demanddate"];
            } else {
                $store_result = false;
            }
        } else {
            $store_result = false;
        }
        $getperiodfrom = "";
        if (isset($demandedit["getperiodfrom"])) {
            if ($demandedit["getperiodfrom"] != "") {
                $getperiodfrom = $demandedit["getperiodfrom"];
            }
        }
        $getperiodto = "";
        if (isset($demandedit["getperiodto"])) {
            if ($demandedit["getperiodto"] != "") {
                $getperiodto = $demandedit["getperiodto"];
            }
        }
        $demandreason = "";
        if (isset($demandedit["demandreason"])) {
            if ($demandedit["demandreason"] != "") {
                $demandreason = $demandedit["demandreason"];
            }
        }
        $sendbackreason = "";
        if (isset($demandedit["sendbackreason"])) {
            if ($demandedit["sendbackreason"] != "") {
                $sendbackreason = $demandedit["sendbackreason"];
            }
        }
        $confirm_user_code = "";
        if (isset($demandedit["confirm"])) {
            if ($demandedit["confirm"] != "") {
                $confirm_user_code = $demandedit["confirm"];
            }
        }
        $confirmfinal_user_code = "";
        if (isset($demandedit["confirmfinal"])) {
            if ($demandedit["confirmfinal"] != "") {
                $confirmfinal_user_code = $demandedit["confirmfinal"];
            }
        }
        $target_seq = 0;
        if ($confirm_user_code == "" && $confirmfinal_user_code != "") {
            $target_seq = Config::get('const.CONFIRM_SEQ.final_confirm');
            $confirm_user_code = $confirmfinal_user_code;
        }
        if ($store_result == false) {
            return response()->json(
                ['result' => $store_result,
                    'department_name' => '',
                    'user_name' => '',
                    'demandno' => $already_demandno,
                    'toaddress' => '',
                    'ccaddress' => '',
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]);
        }
        return response()->json(
            ['result' => $store_result,
                'department_name' => '人事課',
                'user_name' => '社員　九朗',
                'demandno' => $already_demandno,
                'toaddress' => 'takeda@ssjjoo.com',
                'ccaddress' => ['shindo@ssjjoo.com'],
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]);
        // 申請状況確認
        $approval_model = new Approval();
        try {
            // 承認データ
            $approval_model->setParamNoAttribute($already_demandno);
            $approval_model->setParamUsercodeAttribute($confirm_user_code);
            if ($target_seq == Config::get('const.CONFIRM_SEQ.final_confirm')) {
                $approval_model->setParamSeqAttribute($target_seq);
            }
            $result = $approval_model->getDemandfromNo();
            foreach ($result as $item) {
                $approval_model->setNoAttribute($item->no);
                $approval_model->setDoccodeAttribute($item->doc_code);
                $approval_model->setSeqAttribute($item->seq);
                $approval_model->setLognoAttribute($item->log_no);
                $approval_model->setStatusAttribute($item->status);
                $approval_model->setDepartmentcodeAttribute($item->department_code);
                $approval_model->setUsercodeAttribute($item->user_code);
                $approval_model->setApprovaldateAttribute($item->approval_date);
                $approval_model->setRemandreasonAttribute($item->remand_reason);
                $approval_model->setMailresultAttribute($item->mail_result);
                $approval_model->setMailaddressAttribute($item->mail_address);
                $approval_model->setNmaildepartmentcodeAttribute($item->nmail_department_code);
                $approval_model->setNmailusercodeAttribute($item->nmail_user_code);
                if (isset($demandkbn)) {
                    if ($demandkbn == Config::get('const.DEMAND_KBN.store')) {
                        if ($item->status == Config::get('const.C028.making') ||
                            $item->status == Config::get('const.C028.final_approved')) {
                            $this->array_messagedata[] = 
                                array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.making_or_final'));
                            $store_result = false;
                            return response()->json(
                                ['result' => $store_result,
                                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]);
                        }
                    } elseif ($demandkbn == Config::get('const.DEMAND_KBN.sendback')) {
                        if ($item->status == Config::get('const.C028.making') ||
                            $item->status == Config::get('const.C028.send_back') ||
                            $item->status == Config::get('const.C028.final_approved')) {
                            $this->array_messagedata[] = 
                                array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.alreadymaking_or_final'));
                            $store_result = false;
                            return response()->json(
                                ['result' => $store_result,
                                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]);
                        }
                    }
                }
                break;
            }
            $usercode = Auth::user()->code;
            // 部署取得
            $dt = new Carbon();
            $target_date = $dt->format('Ymd');
            $departmentcode = "";
            $user_name = "";
            $department_name = "";
            $result = $apicommon->getUserDepartment($usercode, $target_date);
            foreach($result as $item) {
                if (isset($item->name)) {
                    $user_name = $item->name;
                }
                if (isset($item->department_code)) {
                    $departmentcode = $item->department_code;
                }
                if (isset($item->department_name)) {
                    $department_name = $item->department_name;
                }
                break;
            }
            $result = $apicommon->getUserDepartment($confirm_user_code, $target_date);
            $confirm_departmentcode = "";
            foreach($result as $item) {
                if (isset($item->department_code)) {
                    Log::debug('$item->department_code =  '.$item->department_code);
                    $confirm_departmentcode = $item->department_code;
                }
                break;
            }
            // メールアドレス取得
            $confirm_email = $apicommon->getUserMailAddress($confirm_user_code, $target_date);
        }catch(\PDOException $pe){
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_select_error'));
            $store_result = false;
            Log::error($pe->getMessage());
        }catch(\Exception $e){
            DB::rollBack();
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_select_error'));
            $store_result = false;
            Log::error($e->getMessage());
        }

        if (!$store_result) {
            return response()->json(
                ['result' => $store_result,
                    'department_name' => $department_name,
                    'user_name' => $user_name,
                    'demandno' => $already_demandno,
                    'toaddress' => $confirm_email,
                    'ccaddress' => array(),
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]);
        }
        
        // 申請追加
        DB::beginTransaction();
        try {
            // 申請項目設定
            $maxlogno = $demand_model->getMaxLogno($already_demandno);
            if (isset($maxlogno)) {
                if ($maxlogno == "") {
                    $maxlogno = 0;
                }
            } else {
                $maxlogno = 0;
            }
            $logno = $maxlogno + 1;
            $target_demand_no = $already_demandno;
            $maxseq = 1;
            $approval_model->setNoAttribute($target_demand_no);
            $approval_model->setDoccodeAttribute($doc_code);
            $approval_model->setDemandnowAttribute($demand_now);
            $approval_model->setLognoAttribute($logno);
            $approval_model->setSeqAttribute($maxseq);
            if ($target_seq == Config::get('const.CONFIRM_SEQ.final_confirm')) {
                $approval_model->setStatusAttribute(Config::get('const.C028.final_approved'));
            } else {
                $approval_model->setStatusAttribute(Config::get('const.C028.approving'));
            }
            $approval_model->setDepartmentcodeAttribute($departmentcode);
            $approval_model->setUsercodeAttribute($usercode);
            $dt = new Carbon($demandedit["demanddate"]);
            $demanddate = $dt->format('Ymd');
            $approval_model->setDemanddateAttribute($demanddate);
            $dt = new Carbon($demandedit["getperiodfrom"]);
            $dt_date_from = $dt->format('Ymd');
            $approval_model->setDatefromAttribute($dt_date_from);
            if (isset($demandedit["getperiodto"])) {
                if ($demandedit["getperiodto"] == "") {
                    $approval_model->setDatetoAttribute($dt_date_from);
                } else {
                    $dt = new Carbon($demandedit["getperiodto"]);
                    $dt_date = $dt->format('Ymd');
                    $approval_model->setDatetoAttribute($dt_date);
                }
            } else {
                $approval_model->setDatetoAttribute($dt_date_from);
            }
            $approval_model->setDemandreasonAttribute($demandedit["demandreason"]);
            if ($demanddate <= $dt_date_from) {
                $approval_model->setBeforeafterAttribute(Config::get('const.C029.before'));
            } else {
                $approval_model->setBeforeafterAttribute(Config::get('const.C029.after'));
            }
            $approval_model->setMailresultAttribute(Config::get('const.C030.notyet'));
            $approval_model->setMailaddressAttribute($confirm_email);
            $approval_model->setNmailDepartmentCodeAttribute($confirm_departmentcode);
            $approval_model->setNmailUserCodeAttribute($confirm_user_code);
            $approval_model->setCreateduserAttribute($usercode);
            $systemdate = Carbon::now();
            $approval_model->setCreatedatAttribute($systemdate);
            $approval_model->insertDemand();
            // 申請明細項目設定
            $demandsdetail_model = new DemandsDetail();
            $rowno = 0;
            foreach ($demanddetails as $result) {
                $demandsdetail_model->setNoAttribute($target_demand_no);
                $demandsdetail_model->setDoccodeAttribute($doc_code);
                $demandsdetail_model->setLognoAttribute($logno);
                $rowno++;
                $demandsdetail_model->setRownoAttribute($rowno);
                $demandsdetail_model->setWorkingitemAttribute($result["working_item"]);
                $demandsdetail_model->setDatefromAttribute($dt_date_from);
                $demandsdetail_model->setTimefromAttribute($result["time_from_name"]);
                if ($result["time_from_name"] <= $result["time_to_name"]) {
                    $demandsdetail_model->setDatetoAttribute($dt_date_from);
                } else {
                    $dt = new Carbon($dt_date_from);
                    $demanddate = $dt->format('Ymd');
                    $demandsdetail_model->setDatetoAttribute($dt->addDay());
                }
                $demandsdetail_model->setTimetoAttribute($result["time_to_name"]);
                $demandsdetail_model->setScheduledtimeAttribute($result["scheduled_time"]);
                $demandsdetail_model->setDemandreasonAttribute($result["demand_reason"]);
                $demandsdetail_model->setCreateduserAttribute($usercode);
                $demandsdetail_model->setCreatedatAttribute($systemdate);
                $demandsdetail_model->insertDemandDetail();
            }
            $user = Auth::user();
            $login_user_code = $user->code;
            $login_account_id = $user->account_id;
                // 承認テーブルに登録
            $approval_model = new Approval();
            $approval_model->setNoAttribute($target_demand_no);
            $approval_model->setDoccodeAttribute($doc_code);
            // 承認者の承認順番を取得
            $confirm_model = new Confirm();
            $confirm_model->setParamAccountidAttribute($login_account_id);
            $confirm_model->setParamConfirmdepartmentcodeAttribute($confirm_departmentcode);
            $confirm_model->setParamUsercodeAttribute($confirm_user_code);
            $confirms = $confirm_model->selectConfirm();
            $confirm_seq = 0;
            foreach($confirms as $item) {
                if (isset($item->seq)) {
                    $confirm_seq = $item->seq;
                }
            }
            $approval_model->setSeqAttribute($confirm_seq);
            $approval_model->setLognoAttribute($logno);
            if ($demandkbn == Config::get('const.DEMAND_KBN.store')) {
                $approval_model->setStatusAttribute(Config::get('const.C028.applying'));
            } elseif ($demandkbn == Config::get('const.DEMAND_KBN.discharge')) {
                $approval_model->setStatusAttribute(Config::get('const.C028.making'));
            } else {
                $approval_model->setStatusAttribute(Config::get('const.C028.unknown'));
            }
            $approval_model->setDepartmentcodeAttribute($confirm_departmentcode);
            $approval_model->setUsercodeAttribute($confirm_user_code);
            $approval_model->setApprovaldateAttribute(NULL);
            $approval_model->setRemandreasonAttribute(NULL);
            $approval_model->setMailresultAttribute(NULL);
            $approval_model->setMailaddressAttribute(NULL);
            $approval_model->setNmaildepartmentcodeAttribute("");
            $approval_model->setNmailusercodeAttribute("");
            $approval_model->setCreateduserAttribute($usercode);
            $approval_model->setCreatedatAttribute($systemdate);
            $approval_model->insertApproval();
            DB::commit();
            $store_result = true;
        }catch(\PDOException $pe){
            DB::rollBack();
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_access_error'));
            $store_result = false;
            Log::error($pe->getMessage());
        }catch(\Exception $e){
            DB::rollBack();
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_access_error'));
            $store_result = false;
            Log::error($e->getMessage());
        }

        if (!$store_result) {
            return response()->json(
                ['result' => $store_result,
                    'department_name' => $department_name,
                    'user_name' => $user_name,
                    'demandno' => $already_demandno,
                    'toaddress' => $confirm_email,
                    'ccaddress' => array(),
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]);
        }
    }
}
