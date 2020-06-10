<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\WorkTime;
use App\WorkingTimedate;
use App\Setting;
use App\Company;
use App\Http\Controllers\DailyWorkingInformationController;
use App\Http\Controllers\ApiCommonController;
use App\GeneralCodes;
use App\FeatureItemSelection;
use App\WorkingTimeTable;

class MonthlyWorkingInformationController extends Controller
{

    // 集計用配列
    private $array_user = array();
    // メッセージ
    private $array_messagedata = array();

    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        $authusers = Auth::user();
        return view('monthly_working_information',
            compact(
                'authusers'
            ));
    }


    /**
     * 月次集計処理
     *
     * @return void
     */
    public function show(Request $request)
    {
        // Log::debug('------------- 月次集計 show in----------------');
        $this->array_user = array();
        $this->array_messagedata = array();
        $array_working_time_dates = array();
        $working_time_sum = new collection();
        $apicommon = new ApiCommonController();
        $workingtimedate_model = new WorkingTimedate();
        $company_name = "";
        try {
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['calcresults' => $array_working_time_dates, 'sumresults' => $working_time_sum, 'company_name' => $company_name,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
                    }
            $params = $request->keyparams;
            if (!isset($params['showorupdate'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "showorupdate", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['calcresults' => $array_working_time_dates, 'sumresults' => $working_time_sum, 'company_name' => $company_name,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            if (!isset($params['datefrom'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "datefrom", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['calcresults' => $array_working_time_dates, 'sumresults' => $working_time_sum, 'company_name' => $company_name,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            if (!isset($params['dateto'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "dateto", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['calcresults' => $array_working_time_dates, 'sumresults' => $working_time_sum, 'company_name' => $company_name,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            if (!isset($params['svdatefrom'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "svdatefrom", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['calcresults' => $array_working_time_dates, 'sumresults' => $working_time_sum, 'company_name' => $company_name,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            // パラメータセット
            $showorupdate = $params['showorupdate'];
            $datefrom = $params['datefrom'];
            $dateto = $params['dateto'];
            $svdatefrom = $params['svdatefrom'];
            $employmentstatus = null;
            $departmentcode = null;
            $usercode = null;
            if (isset($params['employmentstatus'])) {
                $employmentstatus = $params['employmentstatus'];
            }
            if (isset($params['departmentcode'])) {
                $departmentcode = $params['departmentcode'];
            }
            if (isset($params['usercode'])) {
                $usercode = $params['usercode'];
            }
            // 会社名を取得
            $company_name = Config::get('const.MEMO_DATA.MEMO_DATA_015');
            $company_model = new Company();
            $company_model->setApplytermfromAttribute($dateto);
            $companys = $company_model->getCompanyInfoApply();
            foreach ($companys as $company_result) {
                $company_name = $company_result->name;
                break;
            }
            // パラメータのチェック
            // 集計用日付設定
            $workingtimedate_model->setParamdatefromAttribute($datefrom);
            $workingtimedate_model->setParamdatetoAttribute($dateto);
            $chk_result = $workingtimedate_model->chkWorkingTimeData();
            $dt_end = null;
            if ($chk_result) {
                // showCalc implement
                $array_impl_showCalc = array (
                    'workingtimedate_model' => $workingtimedate_model,
                    'datefrom' => $datefrom,
                    'dateto' => $dateto,
                    'employmentstatus' => $employmentstatus,
                    'departmentcode' => $departmentcode,
                    'usercode' => $usercode
                );
                // 月次最新集計
                if ($showorupdate == Config::get('const.SHOW_OR_UPDATE.update')) {
                    $te = set_time_limit(180);
                    $dt_end = $this->showupdate($array_impl_showCalc, "show");
                    $dt_dateto = new Carbon($dateto);
                    // Log::debug('  $dt_end = '.$dt_end);
                    // Log::debug('  $dt_dateto = '.$dt_dateto);
                    if ($dt_end >= $dt_dateto) {
                        // 月次集計
                        // showCalc implement
                        $array_impl_showCalc = array (
                            'workingtimedate_model' => $workingtimedate_model,
                            'datefrom' => $svdatefrom,
                            'dateto' => $dateto,
                            'employmentstatus' => $employmentstatus,
                            'departmentcode' => $departmentcode,
                            'usercode' => $usercode
                        );
                        $working_time_sum = $this->showCalc($array_impl_showCalc);
                        if (count($this->array_user) == 0 ) {
                            $this->array_messagedata[] =  array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.not_workintime'));
                        }
                    }
                } else {
                    // 月次集計
                    $working_time_sum = $this->showCalc($array_impl_showCalc);
                    if (count($this->array_user) == 0 ) {
                        $this->array_messagedata[] =  array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.not_workintime'));
                    }
                }
            } else {
                $this->array_messagedata =  $array_messagedata->concat($workingtimedate_model->getMassegedataAttribute());
            }
    
            // Log::debug('------------- 月次集計 show end----------------');
            // Log::debug('  結果 array_user count = '.count($this->array_user));
            // Log::debug('  結果 working_time_sum count = '.count($working_time_sum));
            // Log::debug('  結果 $this->array_messagedata count = '.count($this->array_messagedata));
            return response()->json(
                ['calcresults' => $this->array_user, 'sumresults' => $working_time_sum, 'company_name' => $company_name, 'dt_end' => $dt_end,
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
     * 月次集計処理
     *
     * @return void
     */
    public function showCalc($params)
    {
        // Log::debug('------------- 月次集計開始 showCalc in----------------');
        $workingtimedate_model = $params['workingtimedate_model'];
        $datefrom = $params['datefrom'];
        $dateto = $params['dateto'];
        $employmentstatus = $params['employmentstatus'];
        $departmentcode = $params['departmentcode'];
        $usercode = $params['usercode'];
        $array_result_calcMain = array();
        try {

            // 集計用日付設定
            $workingtimedate_model->setParamdatefromAttribute($datefrom);
            $workingtimedate_model->setParamdatetoAttribute($dateto);
            $workingtimedate_model->setParamEmploymentStatusAttribute($employmentstatus);
            $workingtimedate_model->setParamDepartmentcodeAttribute($departmentcode);
            $workingtimedate_model->setParamUsercodeAttribute($usercode);
            // 労働時間の集計
            $this->calctWorkingTime($workingtimedate_model);
            // 労働時間のCSVデータ作成
            // $this->array_user = $this->setCsvDate();
            // 労働時間の合計集計
            $workingtimedate_model->setParamEmploymentStatusAttribute($employmentstatus);
            $workingtimedate_model->setParamDepartmentcodeAttribute($departmentcode);
            $workingtimedate_model->setParamUsercodeAttribute($usercode);
            $working_time_sum = $workingtimedate_model->getWorkingTimeDateTimeSum(Config::get('const.WORKINGTIME_DAY_OR_MONTH.monthly_basic'));
            return $working_time_sum;
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }



    /**
     * 最新更新集計処理(AttendanceLog.vue)
     *
     * @return void
     */
    public function calc(Request $request)
    {
        // Log::debug('--------------- 最新更新集計 開始 monthly calc in --------------------');
        $this->array_messagedata = array();
        $array_working_time_dates = array();
        $working_time_sum = new collection();
        $apicommon = new ApiCommonController();
        $workingtimedate_model = new WorkingTimedate();
        $company_name = "";
        try {
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['calcresults' => $array_working_time_dates, 'sumresults' => $working_time_sum, 'company_name' => $company_name,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
                    }
            $params = $request->keyparams;
            if (!isset($params['datefrom'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "datefrom", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['calcresults' => $array_working_time_dates, 'sumresults' => $working_time_sum, 'company_name' => $company_name,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
                    }
            if (!isset($params['dateto'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "dateto", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['calcresults' => $array_working_time_dates, 'sumresults' => $working_time_sum, 'company_name' => $company_name,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            // パラメータセット
            $datefrom = $params['datefrom'];
            $dateto = $params['dateto'];
            $employmentstatus = null;
            $departmentcode = null;
            $usercode = null;
            if (isset($params['employmentstatus'])) {
                $employmentstatus = $params['employmentstatus'];
            }
            if (isset($params['departmentcode'])) {
                $departmentcode = $params['departmentcode'];
            }
            if (isset($params['usercode'])) {
                $usercode = $params['usercode'];
            }
            // パラメータのチェック
            // 集計用日付設定
            $workingtimedate_model->setParamdatefromAttribute($datefrom);
            $workingtimedate_model->setParamdatetoAttribute($dateto);
            $chk_result = $workingtimedate_model->chkWorkingTimeData();
            if ($chk_result) {
                // showCalc implement
                $array_impl_showCalc = array (
                    'workingtimedate_model' => $workingtimedate_model,
                    'datefrom' => $datefrom,
                    'dateto' => $dateto,
                    'employmentstatus' => $employmentstatus,
                    'departmentcode' => $departmentcode,
                    'usercode' => $usercode
                );
                // 月次最新集計
                $te = set_time_limit(180);
                $dt_end = $this->showupdate($array_impl_showCalc, "calc");
            } else {
                $this->array_messagedata =  $array_messagedata->concat($workingtimedate_model->getMassegedataAttribute());
            }
    
            // Log::debug('------------- 最新更新集計 開始 monthly calc end----------------');
            return response()->json(
                ['calcresults' => $this->array_user, 'sumresults' => $working_time_sum, 'company_name' => $company_name,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }

        return response()->json(
            ['calcresult' => $calc_result,
                'calcresults' => $working_time_dates,
                'sumresults' => $working_time_sum,
                'company_name' => $company_name,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
        );
    }

    // -------------------------------------------------------------------------------

    /**
     * 最新更新集計処理
     *
     * @return void
     */
    public function showupdate($params, $kbn)
    {
        // Log::debug('--------------- 最新更新集計 開始 monthly showupdate in --------------------');
        $workingtimedate_model = $params['workingtimedate_model'];
        $datefrom = $params['datefrom'];
        $dateto = $params['dateto'];
        $employmentstatus = $params['employmentstatus'];
        $departmentcode = $params['departmentcode'];
        $usercode = $params['usercode'];
        $array_result_calcMain = array();

        $calc_result = true;
        $working_time_dates = array();
        $working_time_sum = array();
        $company_name = '';

        $apicommon = new ApiCommonController();
        $work_time = new WorkTime();
        // work_timeのパラメータのチェックを実施する
        $work_time->setParamDatefromAttribute($datefrom);
        $work_time->setParamDatetoAttribute($dateto);
        $work_time->setParamemploymentstatusAttribute($employmentstatus);
        $work_time->setParamDepartmentcodeAttribute($departmentcode);
        $work_time->setParamUsercodeAttribute($usercode);
        // 日次集計の計算を呼ぶ
        $daily_controller = new DailyWorkingInformationController();
        $calc_date = $datefrom;
        // $dt2 = new Carbon($dateto);
        // 1週間後の日付を設定
        // Log::debug(' datefrom = '.$datefrom);
        // Log::debug(' dateto = '.$dateto);
        $dt2 = new Carbon($dateto);
        $dt3 = new Carbon($datefrom);
        $dt_end = $dt3->addWeek(1);
        if ($kbn == "show") {
            if ($dt2 < $dt_end) {
                $dt_end = $dt2;
            }
        } else {
            $dt_end = $dt2;
        }
        // Log::debug(' dt2 = '.$dt2);
        // Log::debug(' dt3 = '.$dt2);
        // Log::debug(' dt_end = '.$dt_end);
        DB::beginTransaction();
        try{
            // パラメータの内容でworking_time_datesを削除
            // パラメータの内容でworking_time_datesを削除
            // 日付の範囲はcalcメソッドでworkingtimedate_modelに設定済み
            $workingtimedate_model->setParamEmploymentStatusAttribute($employmentstatus);
            $workingtimedate_model->setParamDepartmentcodeAttribute($departmentcode);
            $workingtimedate_model->setParamUsercodeAttribute($usercode);
            if ($workingtimedate_model->isExistsWorkingTimeDate()) {
                $workingtimedate_model->delWorkingTimeDate();
            };
            //feature selection
            $feature_model = new FeatureItemSelection();
            $feature_model->setParamaccountidAttribute(Config::get('const.ACCOUNTID.account_id'));
            $feature_model->setParamselectioncodeAttribute(Config::get('const.EDITION.EDITION'));
            $feature_data = $feature_model->getItem();
            $feature_attendance_count = 0;          // 出勤回数
            $feature_rest_count = 0;                // 休憩回数
            $mode_list = 0;                         // 2なら緊急取集使用
            $early_time = null;                     // 1なら早出時間集計
            $em_details = array();
            foreach($feature_data as $item) {
                if ($item->item_code == Config::get('const.C042.attendance_count')) {
                    $feature_attendance_count = intval($item->value_select);
                }
                if ($item->item_code == Config::get('const.C042.rest_count')) {
                    $feature_rest_count = intval($item->value_select);
                }
                if ($item->item_code == Config::get('const.C042.mode_list')) {
                    $mode_list = intval($item->value_select);
                    // 2なら緊急取集使用
                    if ($mode_list == 2) {
                        // 緊急収集のタイムテーブルを取得する
                        $time_table = new WorkingTimeTable();
                        $time_table->setNoAttribute(Config::get('const.C999_NAME.emergency_timetable_no'));
                        $time_table->setParamapplytermfromAttribute($datefrom);
                        $em_details = $time_table->getDetail();
                    }
                }
                if ($item->item_code == Config::get('const.C042.early_time')) {
                    $early_time = intval($item->value_select);
                }
                if ($feature_attendance_count > 0 && $feature_rest_count > 0 && $mode_list > 0 && $early_time != null) {
                    break;
                }
            }
            while (true) {
                // Log::debug(' ●● 最新更新集計 対象日付 ●● $calc_date = '.$calc_date);
                $dt1 = new Carbon($calc_date);
                // if ($dt1 > $dt2) { break; }
                if ($dt1 > $dt_end) { break; }
                // 打刻時刻を取得
                $work_time->setParamDatefromAttribute($calc_date);
                $work_time->setParamDatetoAttribute($calc_date);
                $work_time->setParamemploymentstatusAttribute($employmentstatus);
                $work_time->setParamDepartmentcodeAttribute($departmentcode);
                $work_time->setParamUsercodeAttribute($usercode);
                $work_time->setArrayrecordtimeAttribute($calc_date, $calc_date);
                // 休日判定
                // jdgBusinessKbn implement
                $array_impl_jdgBusinessKbn = array (
                    'departmentcode' => $departmentcode,
                    'employmentstatus' => $employmentstatus,
                    'usercode' => $usercode,
                    'datefrom' => $calc_date
                );
                $business_kubun = $apicommon->jdgBusinessKbn($array_impl_jdgBusinessKbn);
                // addDailyCalc implement
                $array_impl_addDailyCalc = array (
                    'work_time' => $work_time,
                    'datefrom' => $datefrom,
                    'dateto' => $dateto,
                    'employmentstatus' => $employmentstatus,
                    'departmentcode' => $departmentcode,
                    'usercode' => $usercode,
                    'business_kubun' => $business_kubun,
                    'feature_attendance_count' => $feature_attendance_count,
                    'feature_rest_count' => $feature_rest_count,
                    'early_time' => $early_time,
                    'em_details' => $em_details,
                    'calc_date' => $datefrom
                );
                $calc_result = $daily_controller->addDailyCalc($array_impl_addDailyCalc);
                $calc_date = date_format($dt1->addDay(1), 'Ymd');
            }
            DB::commit();
            return $dt_end;
        }catch(\PDOException $pe){
            DB::rollBack();
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_error_dailycalc'));
        }catch(\Exception $e){
            DB::rollBack();
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_access_error_dailycalc'));
        }
    }

    /**
     * 日付開始終了作成
     *      表示区分により日付開始終了作成する
     *      作成した日付はWorkingTimedateモデルのparam_date_from、param_date_toに設定
     *      
     * @return void
     */
    public function makeDateFromTo($displayKbn, $dateYm, $workingtimedate_model)
    {
        // Log::debug('makeDateFromTo in $displayKbn = '.$displayKbn);
        // Log::debug('makeDateFromTo in $dateYm = '.$dateYm);

        $make_fromdate = '';
        $make_todate = '';
        // 表示区分
        if($displayKbn == Config::get('const.C016.display_closing')){
            // 設定マスタより締め日取得
            $setting_model = new Setting();
            $target_dateYmd = new Carbon($dateYm.'01');
            // 当月締め日
            $setting_model->setParamFiscalmonthAttribute(date_format($target_dateYmd, 'm'));
            $setting_model->setParamYearAttribute(date_format($target_dateYmd, 'Y'));
            $closing = $setting_model->getMonthClosing();
            if (isset($closing)) {
                // 前月締め日
                $beformonth_endOfMonth = $target_dateYmd->firstOfMonth()->addDay(-1);
                $setting_model->setParamFiscalmonthAttribute(date_format($beformonth_endOfMonth, 'm'));
                $setting_model->setParamYearAttribute(date_format($target_dateYmd, 'Y'));
                $beformonth_closing = $setting_model->getMonthClosing();
                if (isset($beformonth_closing)) {

                    $beformonth_fromdate = date_format($beformonth_endOfMonth, 'Ym');
                    $beformonth_dateYmd = new Carbon($beformonth_fromdate.str_pad($beformonth_closing, 2, 0, STR_PAD_LEFT));
                    $make_fromdate = $beformonth_dateYmd->addDay(1);            // 開始は前月締め日+1日を設定
                    $make_fromto = new Carbon($beformonth_fromdate.str_pad($beformonth_closing, 2, 0, STR_PAD_LEFT));
                    $make_todate = new Carbon($dateYm.str_pad($closing, 2, 0, STR_PAD_LEFT));       // 開始は締め日を設定
                } else {
                    $this->array_messagedata[] =  array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.not_setting_closing'));
                    Log::error('makeDateFromTo 前月締め日 '.Config::get('const.MSG_ERROR.not_setting_closing'));
                    return false;
                }
            } else {
                $this->array_messagedata[] =  array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.not_setting_closing'));
                Log::error('makeDateFromTo 当月締め日 '.Config::get('const.MSG_ERROR.not_setting_closing'));
                return false;
            }
        } else {
            $make_fromdate = new Carbon($dateYm.'01');
            $dt = new Carbon($dateYm.'01');
            $make_todate = $dt->endOfMonth();
        }
        // 集計用日付設定
        $workingtimedate_model->setParamdatefromAttribute(date_format($make_fromdate, 'Ymd'));
        $workingtimedate_model->setParamdatetoAttribute(date_format($make_todate, 'Ymd'));

        // Log::debug('makeDateFromTo end $make_fromdate = '.$make_fromdate);
        // Log::debug('makeDateFromTo end $make_todate = '.$make_todate);

        return true;
    }

    /**
     * 労働時間の集計
     *      多次元連想配列
     *          array_user (
     *              [社員] => array_date (
     *                              [日付] 
     *                              [出勤時刻] 
     *                              [退勤時刻] 
     *                          )
     *              )
     * 
     * @return void
     */
    public function calctWorkingTime($workingtimedate_model)
    {
        // Log::debug('----------- calctWorkingTime in --------------');
        // 集計用配列
        $array_date_calctworkingtime = array();
        $array_date_time = array();
        // キーブレーク
        $current_employment_status = null;
        $current_department_code = null;
        $current_user_code = null;
        $current_date = null;
        $current_result = null;
        $before_employment_status = null;
        $before_department_code = null;
        $before_user_code = null;
        $before_date = null;
        $before_result = null;
        // 指定パラメータよりレコード取得パラメータはメインで設定済み
        $workingtimedates = $workingtimedate_model->getWorkingTimeDateTimeFormat(
            Config::get('const.WORKINGTIME_DAY_OR_MONTH.monthly_basic'), $workingtimedate_model->getParamdatefromAttribute(), '');

        if(count($workingtimedates) > 0){
            foreach($workingtimedates as $result) {
                $current_employment_status = $result->employment_status;
                $current_department_code = $result->department_code;
                $current_user_code = $result->user_code;
                $current_date = $result->working_date;
                $current_result = $result;
                if ($before_employment_status == null) {$before_employment_status = $current_employment_status;}
                if ($before_department_code == null) {$before_department_code = $current_department_code;}
                if ($before_user_code == null) {$before_user_code = $current_user_code;}
                if ($before_date == null) {$before_date = $current_date;}
                if ($before_result == null) {$before_result = $result;}
                // 同じキーの場合
                if ($current_employment_status == $before_employment_status &&
                    $current_department_code == $before_department_code &&
                    $current_user_code == $before_user_code ) {
                    // array_date_timeの設定
                    $array_date_calctworkingtime = $this->setArrayDate($current_result);
                    if (count($array_date_calctworkingtime) > 0) {
                        $array_date_time[] = $array_date_calctworkingtime;
                    }
                    // Log::debug('同じキー $array_date_time = '.count($array_date_time));
                } elseif ($current_employment_status == $before_employment_status &&
                    $current_department_code == $before_department_code) {
                    // ユーザーが変わった場合
                    // Log::debug('user break ');
                    // Log::debug('ユーザーが変わった $before_user_code = '.$before_user_code);
                    // Log::debug('ユーザーが変わった $before_date = '.$before_date);
                    // 個人合計労働時間の集計を取得する
                    // 労働時間の集計用パラメータは個人の情報に設定する。日付はmakeDateFromToで設定済み
                    $workingtimedate_model->setParamEmploymentStatusAttribute($before_employment_status);
                    $workingtimedate_model->setParamDepartmentcodeAttribute($before_department_code);
                    $workingtimedate_model->setParamUsercodeAttribute($before_user_code);
                    $working_time_sum = $workingtimedate_model->getWorkingTimeDateTimeSum(Config::get('const.WORKINGTIME_DAY_OR_MONTH.monthly_basic'));
                    // Log::debug('ユーザーが変わった $working_time_sum = '.count($working_time_sum));
                    // this->array_userの設定
                    $this->setArrayUser($before_result, $working_time_sum, $array_date_time);
                    // 次用に配列クリア
                    $array_date_time = array();
                    // 同じ値にする
                    $before_user_code = $current_user_code;
                    $before_result = $result;
                    // array_date_timeの設定
                    $array_date_calctworkingtime = $this->setArrayDate($result);
                    if (count($array_date_calctworkingtime) > 0) {
                        $array_date_time[] = $array_date_calctworkingtime;
                    }
                    // Log::debug('ユーザーが変わった $array_date_time = '.count($array_date_time));
                } elseif ($current_employment_status == $before_employment_status) {
                    // 部署が変わった場合
                    // Log::debug('department break ');
                    // Log::debug('部署が変わった $before_user_code = '.$before_user_code);
                    // Log::debug('部署が変わった $$before_result->user_name = '.$before_result->user_name);
                    // 個人合計労働時間の集計を取得する
                    // 労働時間の集計用パラメータは個人の情報に設定する。日付はmakeDateFromToで設定済み
                    $workingtimedate_model->setParamEmploymentStatusAttribute($before_employment_status);
                    $workingtimedate_model->setParamDepartmentcodeAttribute($before_department_code);
                    $workingtimedate_model->setParamUsercodeAttribute($before_user_code);
                    $working_time_sum = $workingtimedate_model->getWorkingTimeDateTimeSum(Config::get('const.WORKINGTIME_DAY_OR_MONTH.monthly_basic'));
                    // this->array_userの設定
                    $this->setArrayUser($before_result, $working_time_sum, $array_date_time);
                    // 次用に配列クリア
                    $array_date_time = array();
                    // 同じ値にする
                    $before_department_code = $current_department_code;
                    $before_user_code = $current_user_code;
                    $before_result = $result;
                    // array_working_dateの設定
                    $array_date_calctworkingtime = $this->setArrayDate($result);
                    if (count($array_date_calctworkingtime) > 0) {
                        $array_date_time[] = $array_date_calctworkingtime;
                    }
                    // Log::debug('部署が変わった $array_date_time = '.count($array_date_time));
                } else {
                    // 勤務形態が変わった場合
                    // Log::debug('employment_status break ');
                    // Log::debug('勤務形態が変わった $before_user_code = '.$before_user_code);
                    // Log::debug('勤務形態が変わった $$before_result->user_name = '.$before_result->user_name);
                    // 個人合計労働時間の集計を取得する
                    // 労働時間の集計用パラメータは個人の情報に設定する。日付はmakeDateFromToで設定済み
                    $workingtimedate_model->setParamEmploymentStatusAttribute($before_employment_status);
                    $workingtimedate_model->setParamDepartmentcodeAttribute($before_department_code);
                    $workingtimedate_model->setParamUsercodeAttribute($before_user_code);
                    $working_time_sum = $workingtimedate_model->getWorkingTimeDateTimeSum(Config::get('const.WORKINGTIME_DAY_OR_MONTH.monthly_basic'));
                    // this->array_userの設定
                    $this->setArrayUser($before_result, $working_time_sum, $array_date_time);
                    // 次用に配列クリア
                    $array_date_time = array();
                    // 同じ値にする
                    $before_employment_status = $current_employment_status;
                    $before_department_code = $current_department_code;
                    $before_user_code = $current_user_code;
                    $before_result = $result;
                    // array_working_dateの設定
                    $array_date_calctworkingtime = $this->setArrayDate($result);
                    if (count($array_date_calctworkingtime) > 0) {
                        $array_date_time[] = $array_date_calctworkingtime;
                    }
                    // Log::debug('勤務形態が変わった $array_date_time = '.count($array_date_time));
                }
            }
            if (count($array_date_time) > 0) {
                // 個人合計労働時間の集計を取得する
                // 労働時間の集計用パラメータは個人の情報に設定する。日付はmakeDateFromToで設定済み
                $workingtimedate_model->setParamEmploymentStatusAttribute($current_employment_status);
                $workingtimedate_model->setParamDepartmentcodeAttribute($current_department_code);
                $workingtimedate_model->setParamUsercodeAttribute($current_user_code);
                // Log::debug('残り $current_employment_status = '.$current_employment_status);
                // Log::debug('残り $current_department_code = '.$current_department_code);
                // Log::debug('残り $current_user_code = '.$current_user_code);
                $working_time_sum = $workingtimedate_model->getWorkingTimeDateTimeSum(Config::get('const.WORKINGTIME_DAY_OR_MONTH.monthly_basic'));
                // this->array_userの設定
                $this->setArrayUser($before_result, $working_time_sum, $array_date_time);
            }
        }

        // Log::debug(' calctWorkingTime 結果 count($array_date_time) = '.count($array_date_time));
        // Log::debug(' calctWorkingTime 結果 count($this->array_user) = '.count($this->array_user));
        // Log::debug('----------- calctWorkingTime end --------------');
    }

    /**
     * CSV出力用セット
     *      
     * @return void
     */
    public function setCsvDate()
    {
        $set_time_dates = $this->array_user;
        // CSV出力用　null → ""
        foreach ($set_time_dates as $index1 => $item) {
            foreach ($set_time_dates[$index1]['date'] as $record) {
                $attendance = $record['attendance'];
                $leaving = $record['leaving'];
                if(!isset($record['attendance'])){
                    $record['attendance'] = "";
                    $attendance = "";
                }
                if(!isset($record['leaving'])){
                    $record['leaving'] = "";
                    $leaving = "";
                }
                if($attendance == "" && $leaving == ""){
                    if(!isset($record['total_working_times'])){
                        $record['total_working_times'] = "";
                    } else{
                        if($record['total_working_times'] == "00:00"){
                            $record['total_working_times'] = "";
                        }
                    }
                    if(!isset($record['regular_working_times'])){
                        $record['regular_working_times'] = "";
                    } else{
                        if($record['regular_working_times'] == "00:00"){
                            $record['regular_working_times'] = "";
                        }
                    }
                    if(!isset($record['off_hours_working_hours'])){
                        $record['off_hours_working_hours'] = "";
                    } else{
                        if($record['off_hours_working_hours'] == "00:00"){
                            $record['off_hours_working_hours'] = "";
                        }
                    }
                    if(!isset($record['late_night_overtime_hours'])){
                        $record['late_night_overtime_hours'] = "";
                    } else{
                        if($record['late_night_overtime_hours'] == "00:00"){
                            $record['late_night_overtime_hours'] = "";
                        }
                    }
                    if(!isset($record['late_night_working_hours'])){
                        $record['late_night_working_hours'] = "";
                    } else{
                        if($record['late_night_working_hours'] == "00:00"){
                            $record['late_night_working_hours'] = "";
                        }
                    }
                }
            }
        }
        return $set_time_dates;
    }

    /**
     * 集計配列日付セット
     *      
     * @return void
     */
    public function setArrayDate($result)
    {

        $attendance = $result->attendance_time_1;
        $leaving = "";
        if($result->leaving_time_1 != "00:00") {
            $leaving = $result->leaving_time_1;
        }
        if($result->leaving_time_2 != "00:00") {
            $leaving = $result->leaving_time_2;
        }
        if($result->leaving_time_3 != "00:00") {
            $leaving = $result->leaving_time_3;
        }
        if($result->leaving_time_4 != "00:00") {
            $leaving = $result->leaving_time_4;
        }
        if($result->leaving_time_5 != "00:00") {
            $leaving = $result->leaving_time_5;
        }
        $total_working_times = $result->total_working_times;
        $regular_working_times = $result->regular_working_times;
        $off_hours_working_hours = $result->off_hours_working_hours;
        $late_night_overtime_hours = $result->late_night_overtime_hours;
        $late_night_working_hours = $result->late_night_working_hours;
        if($attendance == "" && $leaving == ""){
            if($total_working_times == "00:00"){
                $total_working_times = "";
            }
            if($regular_working_times == "00:00"){
                $regular_working_times = "";
            }
            if($off_hours_working_hours == "00:00"){
                $off_hours_working_hours = "";
            }
            if($late_night_overtime_hours == "00:00"){
                $late_night_overtime_hours = "";
            }
            if($late_night_working_hours == "00:00"){
                $late_night_working_hours = "";
            }
        }

        $datetime = new Carbon($result->working_date);
        $week = array("日", "月", "火", "水", "木", "金", "土");
        $w = (int)$datetime->format('w');
        $week_data = $week[$w];
        $remark_data = '';
        $remark_data1 = mb_convert_encoding($result->remark_holiday_name, "UTF-8", "UTF-8");
        $remark_data .= $result->remark_holiday_name;
        if (strlen($remark_data) == 0) {
            $remark_data .= $result->remark_check_result;
        } else {
            $remark_data .= ' '.$result->remark_check_result;
        }
        if (strlen($remark_data) == 0) {
            $remark_data .= $result->remark_check_max_times;
        } else {
            $remark_data .= ' '.$result->remark_check_max_times;
        }
        if (strlen($remark_data) == 0) {
            $remark_data .= $result->remark_check_interval;
        } else {
            $remark_data .= ' '.$result->remark_check_interval;
        }
        return array(
            'user_code' => $result->user_code,
            'workingdate' => date_format($datetime, 'Ymd'),
            'workingdatename' => date_format($datetime, 'Y年m月d日')."（".$week_data."）",
            'attendance' => $result->attendance_time_1,
            'leaving' => $result->leaving_time_1,
            'public_going_out_hours' => $result->public_going_out_hours,
            'missing_middle_hours' => $result->missing_middle_hours,
            'remark_holiday_name' => $remark_data1,
            'total_working_times' => $total_working_times,
            'regular_working_times' => $regular_working_times,
            'off_hours_working_hours' => $off_hours_working_hours,
            'late_night_overtime_hours' => $late_night_overtime_hours,
            'late_night_working_hours' => $late_night_working_hours
        );
    }

    /**
     * 集計配列ユーザーセット
     *      
     * @return void
     */
    public function setArrayUser($result, $working_time_sum, $array_date_time)
    {

        $array_date = $array_date_time;
        // Log::debug(' csetArrayUser working_time_sum = '.count($working_time_sum));
        foreach($working_time_sum as $working_time_sum_result) {
            $this->array_user[] = array(
                'user_code' => $result->user_code, 
                'user_name' => $result->user_name, 
                'employment' => $result->employment_status_name,
                'department' => $result->department_name,
                'total_working_times' => $working_time_sum_result->total_working_times,
                'regular_working_times' => $working_time_sum_result->regular_working_times,
                'out_of_regular_working_times' => $working_time_sum_result->out_of_regular_working_times,
                'overtime_hours' => $working_time_sum_result->overtime_hours,
                'late_night_overtime_hours' => $working_time_sum_result->late_night_overtime_hours,
                'late_night_working_hours' => $working_time_sum_result->late_night_working_hours,
                'legal_working_times' => $working_time_sum_result->legal_working_times,
                'out_of_legal_working_times' => $working_time_sum_result->out_of_legal_working_times,
                'not_employment_working_hours' => $working_time_sum_result->not_employment_working_hours,
                'off_hours_working_hours' => $working_time_sum_result->off_hours_working_hours,
                'legal_working_holiday_hours' => $working_time_sum_result->legal_working_holiday_hours,
                'out_of_legal_working_holiday_hours' => $working_time_sum_result->out_of_legal_working_holiday_hours,
                'public_going_out_hours' => $working_time_sum_result->public_going_out_hours,
                'missing_middle_hours' => $working_time_sum_result->missing_middle_hours,
                'total_working_status' => $working_time_sum_result->total_working_status,
                'total_go_out' => $working_time_sum_result->total_go_out,
                'total_paid_holidays' => $working_time_sum_result->total_paid_holidays,
                'total_holiday_kubun' => $working_time_sum_result->total_holiday_kubun,
                'total_leave_early' => $working_time_sum_result->total_leave_early,
                'total_late' => $working_time_sum_result->total_late,
                'total_absence' => $working_time_sum_result->total_absence,
                'date' => $array_date
            );
            break;
        }
        //
        if (count($working_time_sum) == 0) {
            $this->array_user[] = array(
                'user_code' => $result->user_code, 
                'user_name' => $result->user_name, 
                'employment' => $result->employment_status_name,
                'department' => $result->department_name,
                'total_working_times' => "00:00",
                'regular_working_times' => "00:00",
                'out_of_regular_working_times' => "00:00",
                'overtime_hours' => "00:00",
                'late_night_overtime_hours' => "00:00",
                'late_night_working_hours' => "00:00",
                'legal_working_times' => "00:00",
                'out_of_legal_working_times' => "00:00",
                'not_employment_working_hours' => "00:00",
                'off_hours_working_hours' => "00:00",
                'legal_working_holiday_hours' => "00:00",
                'out_of_legal_working_holiday_hours' => "00:00",
                'public_going_out_hours' => "00:00",
                'missing_middle_hours' => "00:00",
                'total_working_status' => 0,
                'total_go_out' => 0,
                'total_paid_holidays' => 0,
                'total_holiday_kubun' => 0,
                'total_leave_early' => 0,
                'total_late' => 0,
                'total_absence' => 0,
                'date' => $array_date
            );
        }
    }

}
