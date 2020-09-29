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

class DemandController extends Controller
{
    private $array_messagedata = array();

    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        $authusers = Auth::user();
        return view('demand',
            compact(
                'authusers'
            ));
    }

    /**
     * 申請一覧取得処理
     *
     * @return void
     */
    public function listDemand(Request $request)
    {
        Log::debug('---- 申請一覧取得処理 開始 -------------- ');
        Log::debug('---- パラメータ　$request->doc_code = '.$request->doccode);
        Log::debug('---- パラメータ　$request->usercode = '.$request->usercode);

        $apicommon = new ApiCommonController();
        // reqestクエリーセット
        $doc_code = $apicommon->setRequestQeury($request->doccode);
        $usercode = $apicommon->setRequestQeury($request->usercode);
        if ($usercode == null || $usercode == "") {
            $usercode = Auth::user()->code;
        }

        $list_result = false;
        $array_result = null;
        $array_demand = array();
        $array_demandDeatail = array();
        $array_demandResult = array();
        $get_demands = array();
        $get_demandsdetail = array();
        $user = Auth::user();
        $login_user_code = $user->code;
        $login_account_id = $user->account_id;
        try {
            // パラメータ設定
            $demand_model = new Demand();
            $demand_model->setParamAccountidAttribute($login_account_id);
            $demand_model->setParamDoccodeAttribute($doc_code);
            $demand_model->setParamUsercodeAttribute($usercode);
            // $demand_model->setParamLimitAttribute(10);
            // 適用期間日付の取得
            $dt = new Carbon();
            $target_date = $dt->format('Ymd');
            $array_result = $demand_model->getDemandList($target_date);
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
                    'detail_department_code' => $item->detail_department_code,
                    'detail_user_code' => $item->detail_user_code,
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
     * 申請処理
     *
     * @return void
     */
    public function makeDemand(Request $request)
    {
        Log::debug('---- 申請処理 開始 -------------- ');
        $apicommon = new ApiCommonController();
        // reqestクエリーセット
        $doc_code = $apicommon->setRequestQeury($request->doccode);
        $demandedit = $apicommon->setRequestQeury($request->demandedit);
        $demanddetails = $apicommon->setRequestQeury($request->demanddetail);
        $demandkbn = $apicommon->setRequestQeury($request->kbn);
        $store_result = true;
        $already_demandno = "";
        $justbefore_result = null;
        // 申請状況確認
        $demand_model = new Demand();
        $user = Auth::user();
        $login_user_code = $user->code;
        $login_account_id = $user->account_id;
        try {
            if (isset($demandedit["demandno"])) {
                if ($demandedit["demandno"] != "") {
                    $already_demandno = $demandedit["demandno"];
                    $demand_model->setParamNoAttribute($already_demandno);
                    $justbefore_result = $demand_model->getDemandfromNo();
                    foreach ($result as $item) {
                        if (isset($item->status)) {
                            if ($item->status == Config::get('const.C028.approving') ||
                                $item->status == Config::get('const.C028.final_approved')) {
                                $this->array_messagedata[] = 
                                    array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.rounding_not_demand'));
                                $store_result = false;
                                return response()->json(
                                    ['result' => $store_result,
                                        'demandno' => $demandedit["demandno"],
                                        Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]);
                            }
                        }
                        break;
                    }
                }
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
            $confirm_user_code = "";
            if (isset($demandedit["confirm"])) {
                if ($demandedit["confirm"] != "") {
                    $confirm_user_code = $demandedit["confirm"];
                }
            }
            if ($confirm_user_code == "") {
                if (isset($demandedit["confirmfinal"])) {
                    if ($demandedit["confirmfinal"] != "") {
                        $confirm_user_code = $demandedit["confirmfinal"];
                    }
                }
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
            Log::debug('$confirm_departmentcode =  '.$confirm_departmentcode);
            // メールアドレス取得
            $confirm_email = $apicommon->getUserMailAddress($confirm_user_code, $target_date);
        }catch(\PDOException $pe){
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
                    'demandno' => $demandedit["demandno"],
                    'toaddress' => $confirm_email,
                    'ccaddress' => array(),
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]);
        }
        
        // 申請追加
        // 申請番号作成
        $demand_model->setParamDoccodeAttribute($doc_code);
        $maxseq = 0;
        Log::debug("---- already_demandno = ".$already_demandno);
        if ($already_demandno == "") {
            $maxseq = $demand_model->getMaxSeq($target_date);
            Log::debug("---- maxseq = ".$maxseq);
            if (isset($maxseq)) {
                if ($maxseq == "") {
                    $maxseq = 0;
                }
            } else {
                $maxseq = 0;
            }
            $maxseq++;
            $target_demand_no = str_pad($doc_code, 2, 0, STR_PAD_LEFT).substr($target_date, 2).str_pad($maxseq, 6, 0, STR_PAD_LEFT);
            Log::debug("---- target_demand_no = ".$target_demand_no);
            $logno = 1;
        } else {
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
        }
        // 申請追加
        DB::beginTransaction();
        try {
            // 申請項目設定
            $demand_model->setNoAttribute($target_demand_no);
            $demand_model->setDoccodeAttribute($doc_code);
            $demand_model->setDemandnowAttribute($target_date);
            $demand_model->setLognoAttribute($logno);
            $demand_model->setSeqAttribute($maxseq);
            if ($demandkbn == Config::get('const.DEMAND_KBN.store')) {
                $demand_model->setStatusAttribute(Config::get('const.C028.applying'));
            } elseif ($demandkbn == Config::get('const.DEMAND_KBN.discharge')) {
                $demand_model->setStatusAttribute(Config::get('const.C028.making'));
            } else {
                $demand_model->setStatusAttribute(Config::get('const.C028.unknown'));
            }
            $demand_model->setDepartmentcodeAttribute($departmentcode);
            $demand_model->setUsercodeAttribute($usercode);
            $dt = new Carbon($demandedit["demanddate"]);
            $demanddate = $dt->format('Ymd');
            $demand_model->setDemanddateAttribute($demanddate);
            $dt = new Carbon($demandedit["getperiodfrom"]);
            $dt_date_from = $dt->format('Ymd');
            $demand_model->setDatefromAttribute($dt_date_from);
            if (isset($demandedit["getperiodto"])) {
                if ($demandedit["getperiodto"] == "") {
                    $demand_model->setDatetoAttribute($dt_date_from);
                } else {
                    $dt = new Carbon($demandedit["getperiodto"]);
                    $dt_date = $dt->format('Ymd');
                    $demand_model->setDatetoAttribute($dt_date);
                }
            } else {
                $demand_model->setDatetoAttribute($dt_date_from);
            }
            $demand_model->setDemandreasonAttribute($demandedit["demandreason"]);
            if ($demanddate <= $dt_date_from) {
                $demand_model->setBeforeafterAttribute(Config::get('const.C029.before'));
            } else {
                $demand_model->setBeforeafterAttribute(Config::get('const.C029.after'));
            }
            $demand_model->setMailresultAttribute(Config::get('const.C030.notyet'));
            $demand_model->setMailaddressAttribute($confirm_email);
            $demand_model->setNmailDepartmentCodeAttribute($confirm_departmentcode);
            $demand_model->setNmailUserCodeAttribute($confirm_user_code);
            // 承認者の承認順番を取得
            $confirm_model = new Confirm();
            $confirm_model->setParamAccountidAttribute($login_account_id );
            $confirm_model->setParamConfirmdepartmentcodeAttribute($confirm_departmentcode);
            $confirm_model->setParamUsercodeAttribute($confirm_user_code);
            Log::debug('    $login_account_id = '.$login_account_id);
            Log::debug('    $confirm_departmentcode = '.$confirm_departmentcode);
            Log::debug('    $confirm_user_code = '.$confirm_user_code);
            $confirms = $confirm_model->selectConfirm();
            $confirm_seq = 0;
            foreach($confirms as $item) {
                if (isset($item->seq)) {
                    $confirm_seq = $item->seq;
                }
            }
            $demand_model->setNmailseqAttribute($confirm_seq);
            $demand_model->setCreateduserAttribute($usercode);
            $systemdate = Carbon::now();
            $demand_model->setCreatedatAttribute($systemdate);
            $demand_model->insertDemand();
            // 申請明細項目設定
            $demandsdetail_model = new DemandsDetail();
            $rowno = 0;
            foreach ($demanddetails as $result) {
                $demandsdetail_model->setNoAttribute($target_demand_no);
                $demandsdetail_model->setDoccodeAttribute($doc_code);
                $demandsdetail_model->setLognoAttribute($logno);
                $rowno++;
                $demandsdetail_model->setRownoAttribute($rowno);
                $demandsdetail_model->setDepartmentcodeAttribute($departmentcode);
                $demandsdetail_model->setUsercodeAttribute($result["user_code"]);
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
            // 承認テーブルに登録
            $approval_model = new Approval();
            $approval_model->setNoAttribute($target_demand_no);
            $approval_model->setDoccodeAttribute($doc_code);
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
            // 適用期間日付の取得
            $demand_model->setParamNoAttribute($target_demand_no);
            $dt = new Carbon();
            $target_date = $dt->format('Ymd');
            $getmails = $demand_model->getMailAddress($target_date);
            $array_Ccaddress = array();
            foreach ($getmails as $item) {
                $array_Ccaddress[] = $item->email;
            }
            DB::commit();
            $store_result = true;
        }catch(\PDOException $pe){
            DB::rollBack();
            $store_result = false;
            Log::error($pe->getMessage());
        }catch(\Exception $e){
            DB::rollBack();
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_access_error'));
            $store_result = false;
            Log::error($e->getMessage());
        }
        return response()->json(
            ['result' => $store_result,
                'department_name' => $department_name,
                'user_name' => $user_name,
                'demandno' => $target_demand_no,
                'toaddress' => $confirm_email,
                'ccaddress' => $array_Ccaddress,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]);
    }

    /**
     * メールアドレス取得処理
     *
     * @return void
     */
    public function getMail($target_date, $doc_code, $target_demand_no, $user_code, $user_name, $department_name, $demanddate)
    {
        Log::debug('---- メール送信処理 開始 -------------- ');
        Log::debug('---- パラメータ　$target_date = '.$target_date);
        Log::debug('---- パラメータ　$rdoc_code = '.$doc_code);
        Log::debug('---- パラメータ　$target_demand_no = '.$target_demand_no);
        Log::debug('---- パラメータ　$user_code = '.$user_code);
        Log::debug('---- パラメータ　$user_name = '.$user_name);
        Log::debug('---- パラメータ　$department_name = '.$department_name);
        Log::debug('---- パラメータ　$demanddate = '.$demanddate);

        // メールアドレス取得（正副）
        $demand_model = new Demand();
        $apicommon = new ApiCommonController();

        try {
            mb_language("Japanese"); 
            mb_internal_encoding("UTF-8");
            // Fromメールアドレス取得
            $from_email = $apicommon->getUserMailAddress($user_code, $target_date);
            $pfrom = "-f $from_email";
            // 適用期間日付の取得
            $demand_model->setParamNoAttribute($target_demand_no);
            $dt = new Carbon();
            $target_date = $dt->format('Ymd');
            $getmails = $demand_model->getMailAddress($target_date);
            $to = "";
            $headers = "From: ".$from_email;
            foreach ($getmails as $item) {
                if ($item->main_sub == Config::get('const.C027.main')) {
                    if (strlen($to) == 0) {
                        $to .= $item->email;
                    } else {
                        $to .= "\r\n";
                        $to .= "Cc: ".$item->email;
                    }
                } else {
                    $headers .= "\r\n";
                    $headers .= "Cc: ".$item->email;
                }
            }
            $subject = "";
            switch ($doc_code){
                case Config::get('const.C026.overtime_demand'):
                    $subject = Config::get('const.C026_NAME.overtime_demand');
                    break;
                case Config::get('const.C026.holidaywork_demand'):
                    $subject = Config::get('const.C026_NAME.holidaywork_demand');
                    break;
                case Config::get('const.C026.holidaytransfer_demand'):
                    $subject = Config::get('const.C026_NAME.holidaytransfer_demand');
                    break;
                case Config::get('const.C026.submission_demand'):
                    $subject = Config::get('const.C026_NAME.submission_demand');
                    break;
                case Config::get('const.C026.shiftchange_demand'):
                    $subject = Config::get('const.C026_NAME.shiftchange_demand');
                    break;
                case Config::get('const.C026.paidholiday_demand'):
                    $subject = Config::get('const.C026_NAME.paidholiday_demand');
                    break;
                case Config::get('const.C026.late_demand'):
                    $subject = Config::get('const.C026_NAME.late_demand');
                    break;
                case Config::get('const.C026.earlyleave_demand'):
                    $subject = Config::get('const.C026_NAME.earlyleave_demand');
                    break;
                case Config::get('const.C026.goingout_demand'):
                    $subject = Config::get('const.C026_NAME.goingout_demand');
                    break;
                case Config::get('const.C026.absence_demand'):
                    $subject = Config::get('const.C026_NAME.absence_demand');
                    break;
                default:
                    break;
            }
            $subject .= "承認依頼";
            $message = "申請番号：".$target_demand_no;
            $dt = new Carbon($demanddate);
            $message .= "\r\n"."申請日：".$dt->format('Y年m月d日');
            $message .= "\r\n"."申請者：（部署）".$department_name."（氏名）".$user_name;
            Log::debug('$to = '.$to);
            Log::debug('$subject = '.$subject);
            Log::debug('$message = '.$message);
            Log::debug('$headers = '.$headers);
            Log::debug('$pfrom = '.$pfrom);
            $header = "MIME-Version: 1.0\n";
            $header .= "Content-Transfer-Encoding: 7bit\n";
            $header .= "Content-Type: text/plain; charset=ISO-2022-JP\n";
            //$header .= "Message-Id: <" . md5(uniqid(microtime())) . "@【ドメイン名】>\n";
            $header .= "from: takeda@ssjjoo.com\n";
            $header .= "Reply-To: 【takeda@ssjjoo.com\n";
            $header .= "Return-Path:【takeda@ssjjoo.com\n";
            $header .= "X-Mailer: PHP/". phpversion();
            //https://qiita.com/ShibuyaKosuke/items/309c0a7d969baf0ea8d1
            //mb_send_mail('shindo@ssjjoo.com', $subject, $message, $header, "-f takeda@ssjjoo.com");
            //mb_send_mail($to, $subject, $message, $headers, $pfrom);
        }catch(\Exception $e){
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.mail_send_eror'));
            Log::error($e->getMessage());
        }
    }

}
