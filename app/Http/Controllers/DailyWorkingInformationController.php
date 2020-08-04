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
use App\TempWorkingTimeDate;
use App\TempCalcWorkingTime;
use App\WorkingTimeTable;
use App\FeatureItemSelection;
use App\Http\Controllers\ApiCommonController;


class DailyWorkingInformationController extends Controller
{

    // 打刻データ配列
    private $array_working_mode = array();
    private $array_working_datetime = array();
    private $array_working_datetime_id = array();
    private $array_working_editor_department_code = array();
    private $array_working_editor_department_name = array();
    private $array_working_editor_user_code = array();
    private $array_working_editor_user_name = array();
    private $array_timetable_from_time  = array();
    private $array_timetable_to_time = array();
    private $array_check_result = array();
    private $array_check_max_times = array();
    private $array_check_interval = array();
    private $array_working_timetable_no = array();
    private $array_mobile_positions = array();
    // 計算用配列
    private $array_calc_mode = array();
    private $array_calc_working_timetable_no = array();
    private $array_calc_time = array();
    private $array_calc_status = array();
    private $array_calc_note = array();
    private $calc_late_night_working_hours = 0;     // 深夜労働時間arrayにしないで処理する
    // 表示用配列
    private $array_dsp_time_id = array();
    private $array_dsp_editor_department_code = array();
    private $array_dsp_editor_department_name = array();
    private $array_dsp_editor_user_code = array();
    private $array_dsp_editor_user_name = array();
    // mobile位置情報
    private $array_calc_mobile_positions = array();
    private $array_calc_late = array();
    private $array_calc_leave_early = array();
    private $array_calc_calc = array();
    private $array_calc_to_be_confirmed = array();
    private $array_calc_pattern = array();
    private $array_calc_check_result = array();
    private $array_calc_check_max_times = array();
    private $array_calc_check_interval = array();


    private $not_employment_working = 0;
    private $user_temp_seq = 0;
    private $check_interval2 = 0;

    private $array_messagedata = array();

    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        $authusers = Auth::user();
        return view('daily_working_information',
            compact(
                'authusers'
            ));
    }

    /**
     * 日次集計表示 
     *
     * @return void
     */
    public function show(Request $request){

        $this->array_messagedata = array();
        $array_working_time_dates = array();
        $working_time_sum = new collection();
        $apicommon = new ApiCommonController();
        try {
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['calcresults' => $array_working_time_dates,
                    'sumresults' => $working_time_sum,
                    'datename' => "",
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            if (!isset($params['datefrom'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "datefrom", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['calcresults' => $array_working_time_dates,
                    'sumresults' => $working_time_sum,
                    'datename' => "",
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            // 開始日付のフォーマット 2019年10月01日(火)
            $datefrom = $params['datefrom'];
            $date_name = $apicommon->getYMDWeek($datefrom);
            if (!isset($params['dateto'])) {
                return response()->json(
                    ['calcresults' => $array_working_time_dates,
                    'sumresults' => $working_time_sum,
                    'datename' => $date_name,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            // パラメータセット
            $employmentstatus = null;
            $departmentcode = null;
            $usercode = null;
            $dateto = $params['dateto'];
            if (isset($params['employmentstatus'])) {
                $employmentstatus = $params['employmentstatus'];
            }
            if (isset($params['departmentcode'])) {
                $departmentcode = $params['departmentcode'];
            }
            if (isset($params['usercode'])) {
                $usercode = $params['usercode'];
            }
            // 日次集計計算 showCalc implement
            $array_impl_showCalc = array (
                'datefrom' => $datefrom,
                'dateto' => $dateto,
                'employmentstatus' => $employmentstatus,
                'departmentcode' => $departmentcode,
                'usercode' => $usercode
            );
            // 日次集計計算
            $array_result_showCalc = $this->showCalc($array_impl_showCalc);
            if (count($this->array_messagedata) > 0) {
                return response()->json(
                    ['calcresults' => $array_working_time_dates,
                    'sumresults' => $working_time_sum,
                    'datename' => $date_name,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $array_working_time_dates = $array_result_showCalc['array_working_time_dates'];
            $working_time_sum = $array_result_showCalc['working_time_sum'];

            return response()->json(
                ['calcresults' => $array_working_time_dates,
                    'sumresults' => $working_time_sum,
                    'datename' => $date_name,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]);
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 日次集計計算 
     *
     * @return void
     */
    public function showCalc($params){

        $business_kubun = "";
        $datefrom = $params['datefrom'];
        $dateto = $params['dateto'];
        $employmentstatus = $params['employmentstatus'];
        $departmentcode = $params['departmentcode'];
        $usercode = $params['usercode'];
        $array_result_calcMain = array();
        try {
            $apicommon = new ApiCommonController();
            $work_time = new WorkTime();
            // パラメータのチェック
            $array_chk_work_time = $this->chkWorkingTimeData($params);
            $chk_work_time = $array_chk_work_time['result'];
            if ($chk_work_time) {
                $work_time = $array_chk_work_time['work_time'];
                // 休日判定
                // jdgBusinessKbn implement
                $array_impl_jdgBusinessKbn = array (
                    'departmentcode' => $departmentcode,
                    'employmentstatus' => $employmentstatus,
                    'usercode' => $usercode,
                    'datefrom' => $datefrom
                );
                Log::debug('jdgBusinessKbn in ');
                $business_kubun = $apicommon->jdgBusinessKbn($array_impl_jdgBusinessKbn);
                Log::debug('jdgBusinessKbn $business_kubun = '.$business_kubun);
                if (!isset($business_kubun)) {
                    $dt = date_format(new Carbon($datefrom), 'Y年m月d日');
                    $this->array_messagedata[] = 
                        array( Config::get('const.RESPONCE_ITEM.message') => str_replace('{0}', $dt, Config::get('const.MSG_ERROR.not_setting_calendar')));
                    Log::error(str_replace('{0}', $datefrom, Config::get('const.MSG_ERROR.not_setting_calendar')));
                    Log::error('datefrom = '.$datefrom);
                    Log::error('employmentstatus = '.$employmentstatus);
                    Log::error('departmentcode = '.$departmentcode);
                    Log::error('usercode = '.$usercode);
                    $chk_work_time = false;
                }
            }
            if ($chk_work_time) {
                // calcMain implement
                $array_impl_calcMain = array (
                    'work_time' => $work_time,
                    'datefrom' => $datefrom,
                    'dateto' => $dateto,
                    'employmentstatus' => $employmentstatus,
                    'departmentcode' => $departmentcode,
                    'usercode' => $usercode,
                    'business_kubun' => $business_kubun
                );
                $array_result_calcMain = $this->calcMain($array_impl_calcMain);
            }
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }

        return $array_result_calcMain;
    }

    /**
     * パラメータのチェック 
     *
     * @return void
     */
    public function chkWorkingTimeData($param){

        try {
            // 打刻時刻を取得
            $work_time = new WorkTime();
            $work_time->setParamDatefromAttribute($param['datefrom']);
            $work_time->setParamDatetoAttribute($param['dateto']);
            $work_time->setParamemploymentstatusAttribute($param['employmentstatus']);
            $work_time->setParamDepartmentcodeAttribute($param['departmentcode']);
            $work_time->setParamUsercodeAttribute($param['usercode']);
            $result = $work_time->chkWorkingTimeData();
            if (!$result) {
                $this->array_messagedata[] =  array( Config::get('const.RESPONCE_ITEM.message') => $work_time->getMassegedataAttribute());
            }
            return array (
                'result' => $result,
                'work_time' => $work_time
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
     * 日次集計計算 
     *
     * @return void
     */
    public function calcMain($params){

        Log::debug('------------- 日次集計計算開始 calcMain in----------------');
        $work_time = $params['work_time'];
        $datefrom = $params['datefrom'];
        $dateto = $params['dateto'];
        $employmentstatus = $params['employmentstatus'];
        $departmentcode = $params['departmentcode'];
        $usercode = $params['usercode'];
        $business_kubun = $params['business_kubun'];
        // 変数初期化
        $working_time_dates = new collection();
        $array_working_time_dates = array();
        $working_time_sum = new collection();
        DB::beginTransaction();
        try {
            // -------------- debug -------------- start --------
            if ($business_kubun == 1) {
                Log::debug('------------- 集計開始 日付 = '.$datefrom.' 出勤日　business_kubun = '.$business_kubun );
            } elseif($business_kubun == 2) {
                Log::debug('------------- 集計開始 日付 = '.$datefrom.' 法定外休日　business_kubun = '.$business_kubun );
            } else {
                Log::debug('------------- 集計開始 日付 = '.$datefrom.' 法定休日　business_kubun = '.$business_kubun );
            }
            // -------------- debug -------------- end --------
            // パラメータの内容でworking_time_datesを削除
            $working_model = new WorkingTimedate();
            $working_model->setParamdatefromAttribute(date_format(new Carbon($datefrom), 'Ymd'));
            $working_model->setParamdatetoAttribute(date_format(new Carbon($dateto), 'Ymd'));
            $working_model->setParamEmploymentStatusAttribute($employmentstatus);
            $working_model->setParamDepartmentcodeAttribute($departmentcode);
            $working_model->setParamUsercodeAttribute($usercode);
            if ($working_model->isExistsWorkingTimeDate()) {
                $working_model->delWorkingTimeDate();
            }
            // 日次集計表示
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
            // temporary作成
            $addCalc = $this->addDailyCalc($array_impl_addDailyCalc);
            if ($addCalc) {
                $working_model->setParamdatefromAttribute(date_format(new Carbon($datefrom), 'Ymd'));
                $working_model->setParamdatetoAttribute(date_format(new Carbon($dateto), 'Ymd'));
                $working_model->setParamEmploymentStatusAttribute($employmentstatus);
                $working_model->setParamDepartmentcodeAttribute($departmentcode);
                $working_model->setParamUsercodeAttribute($usercode);
                // 集計結果
                Log::debug('------------- 集計結果 取得　 法定休日　business_kubun = '.$business_kubun );
                $working_time_dates = 
                    $working_model->getWorkingTimeDateTimeFormat(
                        Config::get('const.WORKINGTIME_DAY_OR_MONTH.daily_basic'),
                        $working_model->getParamdatefromAttribute(), $business_kubun);
                // 合計結果
                if (count($working_time_dates) > 0) {
                    $working_time_sum = $working_model->getWorkingTimeDateTimeSum(Config::get('const.WORKINGTIME_DAY_OR_MONTH.daily_basic'));
                } else {
                    $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.not_workintime'));
                    Log::error('datefrom = '.$datefrom);
                    Log::error('dateto = '.$dateto);
                    Log::error('employmentstatus = '.$employmentstatus);
                    Log::error('departmentcode = '.$departmentcode);
                    Log::error('usercode = '.$usercode);
                }

                // 勤怠集計結果コレクション設定
                $json_working_time_dates = $working_time_dates->toJson();
                $dict_working_time_dates = json_decode ($json_working_time_dates, true);
                foreach ($dict_working_time_dates as $key => $value) {
                    $array_working_time_attendances = array();
                    $time_cnt = 1;
                    $array_working_time_attendances = array_merge($array_working_time_attendances, $this->setCollect_Working_time($value, $time_cnt, true));
                    for ($i=$time_cnt+1; $i<6; $i++) {
                        $array_working_time_attendances = array_merge($array_working_time_attendances, $this->setCollect_Working_time($value, $i, false));
                    }
                    $time_cnt = 5;
                    for ($i=$time_cnt+1; $i<8; $i++) {
                        $array_working_time_attendances = array_merge($array_working_time_attendances, $this->setCollect_Working_time_public($value, $i, false));
                    }
                    // 集計結果配列設定
                    $array_w = array();
                    $array_w = $this->setArray_Working_time($value, $array_working_time_attendances);
                    for ($i=0;$i<count($array_w);$i++) {
                        $array_working_time_dates[] = $array_w[$i];
                    }
                }
                DB::commit();
            } else {
                DB::rollBack();
            }

        }catch(\PDOException $pe){
            DB::rollBack();
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_error_dailycalc'));
            throw $pe;
        }catch(\Exception $e){
            DB::rollBack();
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_accesee_error_dailycalc'));
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.data_error_dailycalc'));
            Log::error($e->getMessage());
            throw $e;
        }
        Log::debug('------------- 日次集計計算終了 calcMain end  日付 = '.$datefrom.' business_kubun = '.$business_kubun );

        return array (
            'array_working_time_dates' => $array_working_time_dates,
            'working_time_sum' => $working_time_sum
        );
    }

    /**
     * 日次集計表示 
     *
     * @return void
     */
    public function addDailyCalc($params) {
        Log::debug('---------------------- 日次集計表示 addDailyCalc in ------------------------ ');
        $work_time = $params['work_time'];
        $datefrom = $params['datefrom'];
        $dateto = $params['dateto'];
        $employmentstatus = $params['employmentstatus'];
        $departmentcode = $params['departmentcode'];
        $usercode = $params['usercode'];
        $business_kubun = $params['business_kubun'];
        $feature_attendance_count = $params['feature_attendance_count'];
        $feature_rest_count = $params['feature_rest_count'];
        $em_details = $params['em_details'];
        $calc_date = $params['calc_date'];
        $early_time = $params['early_time'];
        
        $calc_result = true;
        $add_result = true;

        $temp_working_model = new TempWorkingTimeDate();
        $timetable_model = new WorkingTimeTable();
        $apicommon = new ApiCommonController();
        // シフト打刻を取得するために$datetoの翌日をParamDatetoを再設定
        $nextdt = $apicommon->getNextDay($dateto, 'Y/m/d');
        // getWorkTimegetParamDatetoAttributeしている
        $work_time->setParamDatetoAttribute($nextdt);
        // 終了日付で検索（最新の情報として）
        $work_time_results = $work_time->getWorkTimes($datefrom, $dateto, $business_kubun);
        if(count($work_time_results) > 0){
            $temp_calc_model = new TempCalcWorkingTime();
            // temporary削除処理
            try{
                $temp_calc_model->delTempCalcWorkingtime();
                $temp_working_model->delTempWorkingTimeDate();
                try{
                    // 日次集計計算登録
                    // calcWorkingTimeDate implement
                    $array_impl_calcWorkingTimeDate = array (
                        'worktimes' => $work_time_results,
                        'target_date' => $work_time->getParamDatefromAttribute(),
                        'business_kubun' => $business_kubun,
                        'feature_attendance_count' => $feature_attendance_count,
                        'feature_rest_count' => $feature_rest_count,
                        'early_time' => $early_time,
                        'em_details' => $em_details
                    );
                    $calc_result = $this->calcWorkingTimeDate($array_impl_calcWorkingTimeDate);
                    if ($calc_result) {
                        // タイムテーブルを取得
                        $timetable_model->setParamdatefromAttribute($datefrom);
                        $timetable_model->setParamdatetoAttribute($dateto);
                        $timetable_model->setParamemploymentstatusAttribute($employmentstatus);
                        $timetable_model->setParamDepartmentcodeAttribute($departmentcode);
                        $timetables = $timetable_model->getWorkingTimeTableJoin();
                        if (count($timetables) > 0) {
                            // 日次集計
                            $add_result = $this->calcTempWorkingTimeDate($timetables, $datefrom);
                        } else {
                            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.not_setting_timetable'));
                            Log::error(Config::get('const.LOG_MSG.not_setting_timetable'));
                            Log::error('getWorkingTimeTableJoin datefrom = '.$datefrom);
                            Log::error('getWorkingTimeTableJoin dateto = '.$dateto);
                            Log::error('getWorkingTimeTableJoin employmentstatus = '.$employmentstatus);
                            Log::error('getWorkingTimeTableJoin departmentcode = '.$departmentcode);
                            Log::error('getWorkingTimeTableJoin usercode = '.$usercode);
                            $add_result = false;
                        }
                    } else {
                        $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.not_workintime'));
                        Log::error(Config::get('const.LOG_MSG.not_workintime'));
                        Log::error('calcWorkingTimeDate datefrom = '.$datefrom);
                        Log::error('calcWorkingTimeDate dateto = '.$dateto);
                        Log::error('calcWorkingTimeDate employmentstatus = '.$employmentstatus);
                        Log::error('calcWorkingTimeDate departmentcode = '.$departmentcode);
                        Log::error('calcWorkingTimeDate usercode = '.$usercode);
                        Log::error('calcWorkingTimeDate target_date = '.$target_date);
                        Log::error('calcWorkingTimeDate business_kubun = '.$business_kubun);
                        $add_result = false;
                    }
                }catch(\PDOException $pe){
                    $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_accesee_error_dailycalc'));
                    Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
                    Log::error($pe->getMessage());
                    $add_result = false;
                    throw $pe;
                }catch(\Exception $e){
                    $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_error_dailycalc'));
                    Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.data_error_dailycalc'));
                    Log::error($e->getMessage());
                    $add_result = false;
                    throw $e;
                }
            }catch(\PDOException $pe){
                $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_accesee_error_dailycalc'));
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.data_error_dailycalc'));
                Log::error($pe->getMessage());
                $add_result = false;
                throw $pe;
            }
        } else {
            $add_result = false;
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.not_workintime'));
            Log::error('getWorkTimes datefrom = '.$datefrom);
            Log::error('getWorkTimes dateto = '.$dateto);
            Log::error('getWorkTimes employmentstatus = '.$employmentstatus);
            Log::error('getWorkTimes departmentcode = '.$departmentcode);
            Log::error('getWorkTimes usercode = '.$usercode);
        }
        if ($add_result) {
            Log::debug('temporaryからworkingtimedate作成 ');
            // temporaryからworkingtimedate作成
            $array_impl_addworkingTimeDate = array (
                'datefrom' => $datefrom,
                'dateto' => $dateto,
                'employmentstatus' => $employmentstatus,
                'departmentcode' => $departmentcode,
                'usercode' => $usercode,
                'calc_date' => $calc_date
            );
            $add_result = $this->addworkingTimeDate($array_impl_addworkingTimeDate);
        }
        Log::debug('---------------------- 日次集計表示 addDailyCalc end ------------------------ ');

        return $add_result;
    }

    /**
     * 日次集計作成 
     *
     * @return void
     */
    public function addworkingTimeDate($params) {
        Log::debug('---------------------- 日次集計作成 addworkingTimeDate in ------------------------ ');
        $datefrom = $params['datefrom'];
        $dateto = $params['dateto'];
        $employmentstatus = $params['employmentstatus'];
        $departmentcode = $params['departmentcode'];
        $usercode = $params['usercode'];
        $calc_date = $params['calc_date'];

        Log::debug('                       datefrom = '.$datefrom);
        Log::debug('                       dateto = '.$dateto);
        Log::debug('                       employmentstatus = '.$employmentstatus);
        Log::debug('                       departmentcode = '.$departmentcode);
        Log::debug('                       usercode = '.$usercode);
        Log::debug('                       calc_date = '.$calc_date);
        // 出勤・退勤データtempから登録
        $temp_working_model = new TempWorkingTimeDate();
        $temp_working_model->setParamdatefromAttribute(date_format(new Carbon($calc_date), 'Ymd'));
        $temp_working_model->setParamdatetoAttribute(date_format(new Carbon($calc_date), 'Ymd'));
        $temp_working_model->setParamEmploymentStatusAttribute($employmentstatus);
        $temp_working_model->setParamDepartmentcodeAttribute($departmentcode);
        $temp_working_model->setParamUsercodeAttribute($usercode);
        try{
            $temp_working_time_dates = $temp_working_model->getTempWorkingTimeDateUserJoin($calc_date);
            Log::debug('                       count($temp_working_time_dates) = '.count($temp_working_time_dates));
            if (count($temp_working_time_dates) > 0) {
                $working_model = new WorkingTimedate();
                $working_model->setParamdatefromAttribute(date_format(new Carbon($calc_date), 'Ymd'));
                $working_model->setParamdatetoAttribute(date_format(new Carbon($calc_date), 'Ymd'));
                $working_model->setParamEmploymentStatusAttribute($employmentstatus);
                $working_model->setParamDepartmentcodeAttribute($departmentcode);
                $working_model->setParamUsercodeAttribute($usercode);
                $working_model->insertWorkingTimeDateFromTemp($temp_working_time_dates);

            }
            Log::debug('---------------------- 日次集計作成 addworkingTimeDate end ------------------------ ');
            return true;
        }catch(\PDOException $pe){
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_error_dailycalc'));
            Log::error(Config::get('const.LOG_MSG.unknown_error'));
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_accesee_error_dailycalc'));
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 日次労働時間取得
     *
     *      指定したユーザー、日付範囲内の労働時間計算のもとデータを取得するSQL
     *
     *      INPUT：
     *          ①テーブル：departments　部署範囲内 and 削除=0
     *          ②テーブル：users　      ユーザー範囲内 and 削除=0
     *          ③テーブル：work_times　 ユーザーand日付範囲内 and 削除=0
     *          ④①と②と③の結合          ①.ユーザー = ②.ユーザー and ②.ユーザー = ③.ユーザー
     *
     *      使用方法：
     *          ①department_code指定プロパティを事前設定（未設定有効）
     *          ②user_code指定プロパティを事前設定（未設定有効）
     *          ③日付範囲指定プロパティを事前設定（未設定無効）
     *          ④メソッド：calcWorkingTimeDateを実行
     *
     * @return sql取得結果
     */
    private function calcWorkingTimeDate($params){
        Log::debug('---------------------- 日次労働時間取得 calcWorkingTimeDate in ------------------------ '.$params['target_date']);
        $worktimes = $params['worktimes'];
        $target_date = $params['target_date'];
        $business_kubun = $params['business_kubun'];
        // 所定時間帯設定数
        $feature_attendance_count = $params['feature_attendance_count'];
        // 休憩時間帯設定数
        $feature_rest_count = $params['feature_rest_count'];
        // 早出時間集計
        $early_time = $params['early_time'];
        // 緊急収集のタイムテーブル
        $em_details = $params['em_details'];            

        $current_date = null;
        $current_department_code = null;
        $current_user_code = null;
        $current_result = null;
        $before_date = null;
        $before_user_code = null;
        $before_department_code = null;
        $before_result = null;
        $target_date_ymd = date_format(new Carbon($target_date), 'Ymd');
        // ユーザー休暇区分判定用
        $before_holiday_date = null;
        $before_holiday_user_code = null;
        $before_holiday_department_code = null;
        $before_holiday_kubun = null;
        $before_business_kubun = null;
        // 打刻データ配列の初期化
        $this->iniArrayWorkingTime();
        $add_results = true;
        // $noinput_user_cnt = 0;
        $user_temp_out_cnt = 0;
        $target_flg = false;
        $attendance_target_flg = false;         // 出勤打刻があるか
        $this->user_temp_seq = 0;               // ユーザー単位のtemp出力時のseq
        $before_out_flg = false;
        $attendance_work_time = null;
        $attendance_business_kubun = null;
        // ユーザー単位処理
        foreach ($worktimes as $result) {
            // 打刻データありの場合
            Log::debug('------ 日次労働時間取得 code = '.$result->user_code.' '.$result->user_name. ' 開始   計算ターゲット日付'.$target_date_ymd.' ------------------------ ');
            Log::debug('         部署  $result->department_name   = '.$result->department_name);
            Log::debug('         打刻時刻 $result->record_datetime = '.$result->record_datetime);
            Log::debug('         打刻日  $result->record_date = '.$result->record_date);
            Log::debug('         モード  $result->mode = '.$result->mode);
            Log::debug('         出勤区分 $result->business_kubun = '.$result->business_kubun);
            Log::debug('                 $result->business_name  = '.$result->business_name);
            Log::debug('         ユーザー休暇区分 $result->user_holiday_kubun = '.$result->user_holiday_kubun);
            Log::debug('                         $result->user_holiday_name  = '.$result->user_holiday_name);
            Log::debug('                         $result->user_holiday_description  = '.$result->user_holiday_description);
            Log::debug('                         $result->user_working_date  = '.$result->user_working_date);
            Log::debug('         タイムテーブル  no = '.$result->working_timetable_no);
            Log::debug('         タイムテーブル  name = '.$result->working_timetable_name);
            Log::debug('         タイムテーブル　開始時刻　$result->working_timetable_from_time = '.$result->working_timetable_from_time);
            Log::debug('         タイムテーブル　終了時刻　result->working_timetable_to_time    = '.$result->working_timetable_to_time);
            // 出勤回数が複数回の場合は同じデータを取得している場合があるので、その分skipさせる
            //     出勤1回  10:00 11:30  対象
            //     出勤2回  10:00 11:30　skip
            //     出勤3回  10:00 11:30  skip
            $is_dup = $this->isDupTime($result, $before_result);        // false:skip
            Log::debug('         skip?　is_dup  = '.$is_dup);
            $is_today_holiday = true;          // false:skip
            if ($is_dup) {
                // if ($attendance_target_flg) {
                    // if ($result->mode != Config::get('const.C005.attendance_time') &&
                    //     $result->mode != Config::get('const.C005.emergency_time')) {
                        // 夜勤の場合同じ日に休暇ある場合は休暇を当日としない
                        // if ($result->working_timetable_from_time > $result->working_timetable_to_time) {
                            if ($result->user_working_date != null && $result->user_working_date != "") {
                                if ($result->user_holiday_description == Config::get('const.C013_DESC_VALUE.target_calc_time') ||
                                    $result->user_holiday_description == Config::get('const.C013_DESC_VALUE.non_calc_time')) {
                                    if ($target_date_ymd == $result->user_working_date) {
                                        Log::debug('        同じ日に休暇ある場合は休暇を当日とする該当 ');
                                        $result->record_datetime = date_format(new Carbon($target_date), 'Y-m-d')." 00:00:01";
                                        $result->record_date = date_format(new Carbon($target_date), 'Ymd');
                                        Log::debug('        同じ日に休暇ある場合は休暇を当日とする該当 $result->record_datetime = '.$result->record_datetime);
                                        Log::debug('        同じ日に休暇ある場合は休暇を当日とする該当 $result->record_date = '.$result->record_date);
                                        $result->mode = null;
                                        $is_today_holiday = true;
                                    } else {
                                        Log::debug('        同じ日に休暇ある場合は休暇を当日としないに該当 ');
                                        if ($result->mode != null && $result->mode != '') {
                                            $result->user_holiday_kubun = null;
                                            $result->user_holiday_name = null;
                                            $result->user_holiday_description = null;
                                            $is_today_holiday = true;
                                        } else {
                                            $is_today_holiday = false;
                                        }
                                    }
                                }
                            }
                        // }
                    // }
                // }
            }
            Log::debug('        $is_today_holiday = '.$is_today_holiday);
            if ($is_today_holiday) {
                if ($result->record_datetime != null && $result->mode != null && $result->mode != '') {
                    // 設定値確認（エラー内容はログに出力している）
                    $chk_setting = $this->chkSettingData($result);
                    // 設定が正常である場合
                    if ($chk_setting == 0)  {
                        // 翌日退勤した場合を考慮し、同日処理を行うようにするため、$current_dateは$target_date_ymdとする
                        // よって日付ブレーク処理は無意味となるけど
                        $current_date = $target_date_ymd;
                        $current_department_code = $result->department_code;
                        $current_user_code = $result->user_code;
                        $current_result = $result;
                        // 日付or部署orユーザーが１件前と異なれば
                        // 出勤打刻flgをfalseに初期設定
                        if ($current_date != $before_date ||
                            $current_department_code != $before_department_code ||
                            $current_user_code != $before_user_code) {
                            $attendance_target_flg = false;
                            $attendance_work_time = null;
                            $attendance_business_kubun = null;
                        }
                        // 出勤回数が複数回の場合はタイムテーブルの開始終了時刻が正しく対応順に取得されない場合があるので
                        // 対応するタイムテーブルの開始終了時刻を新たに設定する
                        //     出勤1回  10:00 11:30  対象    タイムテーブル開始終了  15:30  16:30   ×
                        //     出勤2回  12:30 14:00　対象    タイムテーブル開始終了  10:00  11:30   ×
                        //     出勤3回  15:30 16:30  対象    タイムテーブル開始終了  12:30  14:00   ×
                        if ($result->mode == Config::get('const.C005.attendance_time') ||
                            $result->mode == Config::get('const.C005.emergency_time')) {
                            $attendance_work_time = $result->record_datetime;
                            $attendance_business_kubun = $result->business_kubun;
                            $array_impl_setWorkingtimetabletime = array (
                                'target_date' => $target_date_ymd,
                                'feature_attendance_count' => $feature_attendance_count,
                                'attendance_work_time' => $attendance_work_time,
                                'early_time' => $early_time,
                                'em_details' => $em_details,
                                'result' => $result
                            );
                            $array_result = $this->setWorkingtimetabletime($array_impl_setWorkingtimetabletime);
                            // TODO $resultを変更したくない
                            $result->working_timetable_no = $array_result['working_timetable_no'];
                            $result->working_timetable_name = $array_result['working_timetable_name'];
                            $result->working_timetable_from_time = $array_result['working_from_time'];
                            $result->working_timetable_to_time = $array_result['working_to_time'];
                            Log::debug('         タイムテーブル　変更後　no　     $result->working_timetable_no = '.$result->working_timetable_no);
                            Log::debug('         タイムテーブル　変更後　name   　$result->working_timetable_name    = '.$result->working_timetable_name);
                            Log::debug('         タイムテーブル　変更後　開始時刻　$result->working_timetable_from_time = '.$result->working_timetable_from_time);
                            Log::debug('         タイムテーブル　変更後　終了時刻　result->working_timetable_to_time    = '.$result->working_timetable_to_time);
                        } else {
                            if ($attendance_business_kubun != null && $attendance_business_kubun != "") {
                                $result->business_kubun = $attendance_business_kubun;
                            }
                        }
                        $array_impl_isCurrentDateCalc = array (
                            'target_date_ymd' => $target_date_ymd,
                            'attendance_flg' => $attendance_target_flg,
                            'result' => $result
                        );
                        $target_flg = $this->isCurrentDateCalc($array_impl_isCurrentDateCalc);
                        if ($result->mode == Config::get('const.C005.attendance_time') ||
                            $result->mode == Config::get('const.C005.emergency_time')) {
                            $attendance_target_flg = $target_flg;
                        }
                        Log::debug('        出勤打刻があるか $attendance_target_flg ='.$attendance_target_flg);
                        Log::debug('        出勤打刻があるか $target_flg ='.$target_flg);
                        if ($before_date == null) {$before_date = $current_date;}
                        if ($before_department_code == null) {$before_department_code = $current_department_code;}
                        if ($before_user_code == null) {$before_user_code = $current_user_code;}
                        if ($before_result == null) {$before_result = $result;}
                        if ($target_flg == true) {
                            Log::debug('        当日の打刻あり、当日計算対象データ');
                            // ユーザー休暇区分判定用
                            $before_holiday_date = null;
                            $before_holiday_user_code = null;
                            $before_holiday_department_code = null;
                            $before_holiday_kubun = null;
                            $before_business_kubun = null;
                            $before_out_flg = true;
                            // 同じキーの場合
                            if ($current_date == $before_date &&
                                $current_department_code == $before_department_code &&
                                $current_user_code == $before_user_code) {
                                // 打刻データ配列の設定
                                $this->pushArrayWorkingTime($result);
                            } elseif ($current_date == $before_date &&
                                $current_department_code == $before_department_code) {
                                // ユーザーが変わった場合
                                Log::debug('    ユーザーが変わった場合 ');
                                // ユーザー労働時間計算(１個前のユーザーを計算する)
                                Log::debug('        temp_calc_workingtimesの登録開始');
                                Log::debug('        １個前のユーザーを登録開始 $before_user_code = '.$before_user_code);
                                Log::debug('            部署     = '.$before_result->department_name);
                                Log::debug('            打刻時刻 = '.$before_result->record_datetime);
                                Log::debug('            モード   = '.$before_result->mode);
                                Log::debug('            打刻日   = '.$before_result->record_date);
                                // ユーザー労働時間計算
                                // calcWorkingTimeDate implement
                                $array_impl_calcWorkingTime = array (
                                    'target_date' => $before_date,
                                    'target_user_code' => $before_user_code,
                                    'target_department_code' => $before_department_code,
                                    'target_business_kubun' => $business_kubun,
                                    'target_result' => $before_result
                                );
                                $this->calcWorkingTime($array_impl_calcWorkingTime);
                                try{
                                    // temporaryに登録する
                                    // calcWorkingTimeDate implement
                                    $array_impl_insTempCalcItem = array (
                                        'target_date' => $before_date,
                                        'target_result' => $before_result
                                    );
                                    $this->insTempCalcItem($array_impl_insTempCalcItem);
                                }catch(\PDOException $pe){
                                    $add_results = false;
                                    throw $pe;
                                }
                                Log::debug('        temp_calc_workingtimesの登録終了');
                                // 次データ計算事前処理
                                // beforeArrayWorkingTimeは現データが有効の場合の事前処理
                                $this->beforeArrayWorkingTime($result);
                                // ユーザーを同じく設定
                                $before_user_code = $current_user_code;
                                $before_out_flg = true;
                                $this->user_temp_seq = 0;
                            } elseif ($current_date == $before_date) {
                                // 部署が変わった場合
                                Log::debug('    部署が変わった場合 ');
                                // ユーザー労働時間計算(１個前のユーザーを計算する)
                                Log::debug('    temp_calc_workingtimesの登録開始');
                                Log::debug('        １個前のユーザーを登録開始 $before_user_code = '.$before_user_code);
                                Log::debug('            部署     = '.$before_result->department_name);
                                Log::debug('            打刻時刻 = '.$before_result->record_datetime);
                                Log::debug('            モード   = '.$before_result->mode);
                                Log::debug('            打刻日   = '.$before_result->record_date);
                                // ユーザー労働時間計算
                                // calcWorkingTimeDate implement
                                $array_impl_calcWorkingTime = array (
                                    'target_date' => $before_date,
                                    'target_user_code' => $before_user_code,
                                    'target_department_code' => $before_department_code,
                                    'target_business_kubun' => $business_kubun,
                                    'target_result' => $before_result
                                );
                                $this->calcWorkingTime($array_impl_calcWorkingTime);
                                try{
                                    // temporaryに登録する
                                    // calcWorkingTimeDate implement
                                    $array_impl_insTempCalcItem = array (
                                        'target_date' => $before_date,
                                        'target_result' => $before_result
                                    );
                                    $this->insTempCalcItem($array_impl_insTempCalcItem);
                                }catch(\PDOException $pe){
                                    $add_results = false;
                                    throw $pe;
                                }
                                Log::debug('    temp_calc_workingtimesの登録終了');
                                // 次データ計算事前処理
                                // beforeArrayWorkingTimeは現データが有効の場合の事前処理
                                $this->beforeArrayWorkingTime($result);
                                // 部署を同じく設定
                                $before_department_code = $current_department_code;
                                // ユーザーを同じく設定
                                $before_user_code = $current_user_code;
                                $before_out_flg = true;
                                $this->user_temp_seq = 0;
                            } else {
                                // 日付が変わった場合
                                Log::debug('    日付が変わった ');
                                try{
                                    // ユーザー労働時間登録(１個前のユーザーを登録する)
                                    Log::debug('        １個前のユーザーを登録開始 $before_user_code = '.$before_user_code);
                                    Log::debug('            部署     = '.$before_result->department_name);
                                    Log::debug('            打刻時刻 = '.$before_result->record_datetime);
                                    Log::debug('            モード   = '.$before_result->mode);
                                    Log::debug('            打刻日   = '.$before_result->record_date);
                                    // ユーザー労働時間登録
                                    // array_impl_addWorkingTime implement
                                    $array_impl_addWorkingTime = array (
                                        'target_date' => $before_date,
                                        'target_user_code' => $before_user_code,
                                        'target_department_code' => $before_department_code,
                                        'target_business_kubun' => $business_kubun,
                                        'target_result' => $before_result
                                    );
                                    $add_results = $this->addWorkingTime($array_impl_addWorkingTime);
                                    Log::debug('    １個前のユーザーを登録終了 $before_user_code = '.$before_user_code);
                                    // 次データ計算事前処理
                                    // beforeArrayWorkingTimeは現データが有効の場合の事前処理
                                    $this->beforeArrayWorkingTime($result);
                                    // 日付を同じく設定
                                    $before_date = $current_date;
                                    // 部署を同じく設定
                                    $before_department_code = $current_department_code;
                                    // ユーザーを同じく設定
                                    $before_user_code = $current_user_code;
                                    $before_out_flg = true;
                                }catch(\PDOException $pe){
                                    $add_results = false;
                                    throw $pe;
                                }catch(\Exception $e){
                                    Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.data_error_dailycalc'));
                                    Log::error($e->getMessage());
                                    $add_results = false;
                                    throw $e;
                                }
                            }
                        } else {
                            Log::debug('        当日の打刻なし、当日計算対象データ');
                            // 前のデータが打刻ありであれば計算する
                            $user_holiday_kubun = null;
                            $user_holiday_name = null;
                            $user_working_date = null;
                            if (count($this->array_working_mode) > 0) {
                                try{
                                    Log::debug('        １個前のユーザーを登録開始 $before_user_code = '.$before_user_code);
                                    Log::debug('            部署     = '.$before_result->department_name);
                                    Log::debug('            打刻時刻 = '.$before_result->record_datetime);
                                    Log::debug('            モード   = '.$before_result->mode);
                                    Log::debug('            打刻日   = '.$before_result->record_date);
                                    // ユーザー労働時間登録
                                    // array_impl_addWorkingTime implement
                                    $array_impl_addWorkingTime = array (
                                        'target_date' => $before_date,
                                        'target_user_code' => $before_user_code,
                                        'target_department_code' => $before_department_code,
                                        'target_business_kubun' => $business_kubun,
                                        'target_result' => $before_result
                                    );
                                    $add_results = $this->addWorkingTime($array_impl_addWorkingTime);
                                    // 次データ計算事前処理(打刻ないデータはbeforeArrayWorkingTimeは使用しない)
                                    Log::debug('        １個前のユーザーを登録終了 $before_user_code = '.$before_user_code);
                                    $before_date = null;
                                    $before_user_code = null;
                                    $before_department_code = null;
                                    $before_result = null;
                                    // 打刻データ配列の初期化
                                    $this->iniArrayWorkingTime();
                                    // 計算用配列の初期化
                                    $this->iniArrayCalc();
                                    $before_out_flg = true;
                                }catch(\PDOException $pe){
                                    $add_results = false;
                                    throw $pe;
                                }catch(\Exception $e){
                                    $add_results = false;
                                    Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.data_error_dailycalc'));
                                    Log::error($e->getMessage());
                                    throw $e;
                                }
                            }
                            // 打刻ないデータはtempに出力
                            // Log::debug('        打刻ないデータはtempに出力するか判定 $current_date = '.$current_date);
                            // Log::debug('            $before_date = '.$before_date);
                            // Log::debug('            打刻時刻      = '.$result->record_datetime);
                            // Log::debug('            打刻日付      = '.$result->record_date);
                            // Log::debug('            タイムテーブルNO      = '.$result->working_timetable_no);
                            // Log::debug('            ターゲット日付   = '.$target_date_ymd);
                            // Log::debug('            ユーザー休暇   = '.$user_holiday_kubun);
                            // Log::debug('            1件前出力      = '.$before_out_flg);
                            // 1件前の日付がnullである場合、いきなり対象日付がないということなので出力
                            //if (!isset($result->record_datetime) || isset($user_holiday_kubun)) {
                            //if (!$before_out_flg || isset($user_holiday_kubun)) {
                            // 1件前出力していず、休暇扱いか出勤日である場合は空の情報として出力する
                            // if (!isset($result->record_datetime) &&
                            //      (!$before_out_flg || isset($user_holiday_kubun)))
                            // {
                            // 有効打刻データがなくて、休暇扱いか出勤日である場合はtempに出力
                            // 打刻されていれば出勤以外は出力対象外
                            // $temp_out_flg1 = false;
                            // $temp_out_flg2 = false;
                            // $temp_out_flg3 = false;
                            // if (isset($user_holiday_kubun) && $user_holiday_kubun >= (int)Config::get('const.C013.paid_holiday')) {
                            //     $temp_out_flg1 = true;
                            // }
                            // if ($result->business_kubun == Config::get('const.C007.basic')) {
                            //     $temp_out_flg2 = true;
                            // }
                            // if (!$attendance_target_flg)
                            // {
                            //     $temp_out_flg3 = true;
                            // }
                            // if ($temp_out_flg3 && ($temp_out_flg1 || $temp_out_flg2))
                            // {
                            // //if ($temp_out_flg ) {   // 20191012
                            //     try{
                            //         Log::debug('        打刻ないデータはtempに出力 $current_date = ');
                            //         // 同じキーの場合
                            //         if ($current_date == $before_date &&
                            //             $current_department_code == $before_department_code &&
                            //             $current_user_code == $before_user_code) {
                            //             $noinput_user_cnt++;
                            //         } else {
                            //             $noinput_user_cnt = 1;
                            //         }
                            //         if ($noinput_user_cnt == 1) {
                            //             $ptn = 0;
                            //         } else {
                            //             $ptn = 6;
                            //         }
                            //         if(isset($result->user_holiday_kubun)) { $user_holiday_kubun = $result->user_holiday_kubun; }
                            //         if(isset($result->user_holiday_name)) { $user_holiday_name = $result->user_holiday_name; }
                            //         if(isset($result->user_working_date)) { $user_working_date = $result->user_working_date; }
                            //         if ($before_holiday_department_code != $result->department_code ||
                            //             $before_holiday_user_code != $result->user_code ||
                            //             $before_holiday_date != $result->user_working_date ||
                            //             $before_holiday_kubun != $user_holiday_kubun) {
                            //             $dt = date_format(new Carbon($target_date), 'Ymd');
                            //             Log::debug('            ターゲット日付 = '.$dt);
                            //             Log::debug('            ユーザー休暇   = '.$user_holiday_name);
                            //             Log::debug('            　　　　日付   = '.$user_working_date);
                            //             // setNoInputTimePtn implement
                            //             $array_impl_setNoInputTimePtn = array (
                            //                 'ptn' => $ptn,
                            //                 'user_holiday_name' => $user_holiday_name,
                            //                 'target_date' => $dt,
                            //                 'hpliday_date' => $user_working_date,
                            //                 'value_working_timetable_no' => $result->working_timetable_no
                            //             );
                            //             $this->pushArrayCalc($this->setNoInputTimePtn($array_impl_setNoInputTimePtn));
                            //             // temporaryに登録する
                            //             Log::debug('    temp_calc_workingtimesの登録開始');
                            //             Log::debug('        現ユーザー = '.$current_user_code.' record_time = '.$result->record_datetime);
                            //             // calcWorkingTimeDate implement
                            //             $array_impl_insTempCalcItem = array (
                            //                 'target_date' => $target_date,
                            //                 'target_result' => $result
                            //             );
                            //             $this->insTempCalcItem($array_impl_insTempCalcItem);
                            //             Log::debug('    temp_calc_workingtimesの登録終了');
                            //         }
                            //         // 日付とユーザー休暇区分を保存
                            //         $before_holiday_date = $result->user_working_date;
                            //         $before_holiday_user_code = $result->user_code;
                            //         $before_holiday_department_code = $result->department_code;
                            //         $before_holiday_kubun = $user_holiday_kubun;
                            //     }catch(\PDOException $pe){
                            //         $add_results = false;
                            //         throw $pe;
                            //     }
                            //     // 次データ計算事前処理(打刻ないデータはbeforeArrayWorkingTimeは使用しない)
                            //     $before_date = null;
                            //     $before_user_code = null;
                            //     $before_department_code = null;
                            //     $before_result = null;
                            //     $before_out_flg = true;
                            //     // 次データ計算事前処理
                            //     // 打刻データ配列の初期化
                            //     $this->iniArrayWorkingTime();
                            //     // 計算用配列の初期化
                            //     $this->iniArrayCalc();
                            // } else {
                            //     $before_out_flg = true;
                            //     Log::debug('        打刻ないデータはtempに出力しない '.$result->record_datetime);
                            // }
                        }
                    } else {
                        // 前のデータが打刻ありであれば計算する
                        $user_holiday_kubun = null;
                        $user_holiday_name = null;
                        $user_working_date = null;
                        if (count($this->array_working_mode) > 0) {
                            try{
                                Log::debug('        １個前のユーザーを登録開始 $before_user_code = '.$before_user_code);
                                Log::debug('            部署     = '.$before_result->department_name);
                                Log::debug('            打刻時刻 = '.$before_result->record_datetime);
                                Log::debug('            モード   = '.$before_result->mode);
                                Log::debug('            打刻日   = '.$before_result->record_date);
                                // ユーザー労働時間登録
                                // array_impl_addWorkingTime implement
                                $array_impl_addWorkingTime = array (
                                    'target_date' => $before_date,
                                    'target_user_code' => $before_user_code,
                                    'target_department_code' => $before_department_code,
                                    'target_business_kubun' => $business_kubun,
                                    'target_result' => $before_result
                                );
                                $add_results = $this->addWorkingTime($array_impl_addWorkingTime);
                                // 次データ計算事前処理(打刻ないデータはbeforeArrayWorkingTimeは使用しない)
                                $before_date = null;
                                $before_user_code = null;
                                $before_department_code = null;
                                $before_result = null;
                                $before_out_flg = true;
                                // 次データ計算事前処理
                                // 打刻データ配列の初期化
                                $this->iniArrayWorkingTime();
                                // 計算用配列の初期化
                                $this->iniArrayCalc();
                            }catch(\PDOException $pe){
                                $add_results = false;
                                throw $pe;
                            }catch(\Exception $e){
                                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.data_error_dailycalc'));
                                Log::error($e->getMessage());
                                $add_results = false;
                                throw $e;
                            }
                        }
                        try{
                            Log::debug('    temp_calc_workingtimesの登録開始');
                            Log::debug('        現ユーザー = '.$result->user_code.' record_time = '.$result->record_datetime);
                            // temporaryに登録する
                            if(isset($result->user_holiday_kubun)) { $user_holiday_kubun = $result->user_holiday_kubun; }
                            if(isset($result->user_holiday_name)) { $user_holiday_name = $result->user_holiday_name; }
                            if(isset($result->user_working_date)) { $user_working_date = $result->user_working_date; }
                            if ($before_holiday_department_code != $result->department_code ||
                                $before_holiday_user_code != $result->user_code ||
                                $before_holiday_date != $result->user_working_date ||
                                $before_holiday_kubun != $user_holiday_kubun) {
                                Log::debug('    temp_calc_workingtimesの登録開始');
                                $dt = date_format(new Carbon($target_date), 'Ymd');
                                Log::debug('            ターゲット日付 = '.$target_date);
                                Log::debug('            ユーザー休暇  ='.$user_holiday_name);
                                Log::debug('        　　　　    日付  = '.$user_working_date);
                                $ptn = $chk_setting;
                                // setNoInputTimePtn implement
                                $array_impl_setNoInputTimePtn = array (
                                    'ptn' => $ptn,
                                    'user_holiday_name' => $user_holiday_name,
                                    'target_date' => $dt,
                                    'hpliday_date' => $user_working_date,
                                    'value_working_timetable_no' => $result->working_timetable_no
                                );
                                $this->pushArrayCalc($this->setNoInputTimePtn($array_impl_setNoInputTimePtn));
                                // calcWorkingTimeDate implement
                                $array_impl_insTempCalcItem = array (
                                    'target_date' => $result->record_date,
                                    'target_result' => $result
                                );
                                $this->insTempCalcItem($array_impl_insTempCalcItem);
                                Log::debug('    temp_calc_workingtimesの登録終了');
                            }
                            // 日付とユーザー休暇区分を保存
                            $before_holiday_date = $result->user_working_date;
                            $before_holiday_user_code = $result->user_code;
                            $before_holiday_department_code = $result->department_code;
                            $before_holiday_kubun = $user_holiday_kubun;
                            $before_business_kubun = $result->business_kubun;
                        }catch(\PDOException $pe){
                            $add_results = false;
                            throw $pe;
                        }
                        // 次データ計算事前処理
                        // 打刻データ配列の初期化
                        $this->iniArrayWorkingTime();
                        // 計算用配列の初期化
                        $this->iniArrayCalc();
                    }
                } else {
                    Log::debug('        $result->record_datetime = null 打刻データなし count($this->array_working_mode) = '.count($this->array_working_mode));
                    // 前のデータが打刻ありであれば計算する
                    $user_holiday_kubun = null;
                    $user_holiday_name = null;
                    $user_working_date = null;
                    if (count($this->array_working_mode) > 0) {
                        try{
                            // ユーザー労働時間登録(１個前のユーザーを登録する)
                            Log::debug('        １個前のユーザーを登録開始 $before_user_code = '.$before_user_code);
                            Log::debug('            部署     = '.$before_result->department_name);
                            Log::debug('            打刻時刻 = '.$before_result->record_datetime);
                            Log::debug('            モード   = '.$before_result->mode);
                            Log::debug('            打刻日   = '.$before_result->record_date);
                            // ユーザー労働時間登録
                            // array_impl_addWorkingTime implement
                            $array_impl_addWorkingTime = array (
                                'target_date' => $before_date,
                                'target_user_code' => $before_user_code,
                                'target_department_code' => $before_department_code,
                                'target_business_kubun' => $business_kubun,
                                'target_result' => $before_result
                            );
                            $add_results = $this->addWorkingTime($array_impl_addWorkingTime);
                            // 次データ計算事前処理(打刻ないデータはbeforeArrayWorkingTimeは使用しない)
                            /*$before_date = null;
                            $before_user_code = null;
                            $before_department_code = null;
                            $before_result = null;*/
                            $before_out_flg = true;
                        // 打刻データ配列の初期化
                            $this->iniArrayWorkingTime();
                            // 計算用配列の初期化
                            $this->iniArrayCalc();
                        }catch(\PDOException $pe){
                            $add_results = false;
                            throw $pe;
                        }catch(\Exception $e){
                            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.data_error_dailycalc'));
                            Log::error($e->getMessage());
                            $add_results = false;
                            throw $e;
                        }
                    }
                    // 打刻ないデータはtempに出力
                    // 20200414 add start
                    Log::debug('        打刻ないデータ');
                    if ($before_department_code != $result->department_code ||
                        $before_user_code != $result->user_code ||
                        $before_holiday_date != $result->user_working_date) {
                        Log::debug('        データ break $result->user_holiday_kubun= '.$result->user_holiday_kubun);
                        Log::debug('        データ break $result->business_kubun= '.$result->business_kubun);
                        Log::debug('        データ break $result->user_working_date = '.$result->user_working_date);
                        // 出勤打刻flgをfalseに初期設定
                        $attendance_target_flg = false;
                        $attendance_work_time = null;
                        $attendance_business_kubun = null;
                        // 有効打刻データがなくて、休暇扱いか出勤日である場合はtempに出力
                        // 打刻されていれば出勤以外は出力対象外
                        $temp_out_flg1 = false;
                        $temp_out_flg2 = false;
                        $temp_out_flg3 = false;
                        $chk_business_kubun = $result->business_kubun;
                        if ($attendance_business_kubun != null || $attendance_business_kubun != "") {
                            $chk_business_kubun = $attendance_business_kubun;
                        }
                        if (isset($result->user_holiday_kubun)) {
                            if ($result->user_holiday_kubun >= (int)Config::get('const.C013.paid_holiday')) {
                                // 当日の休暇である場合
                                if ($result->user_working_date == $target_date_ymd) {
                                    $temp_out_flg1 = true;
                                }
                            } else {
                                if ($chk_business_kubun == Config::get('const.C007.basic')) {
                                    $temp_out_flg2 = true;
                                }
                            }
                        } else {
                            if ($chk_business_kubun == Config::get('const.C007.basic')) {
                                $temp_out_flg2 = true;
                            }
                        }
                        if (!$attendance_target_flg)
                        {
                            $temp_out_flg3 = true;
                        }
                        Log::debug('        データ $temp_out_flg1 '.$temp_out_flg1);
                        Log::debug('        データ $temp_out_flg2 '.$temp_out_flg2);
                        Log::debug('        データ $temp_out_flg3 '.$temp_out_flg3);
                        if ($temp_out_flg3 && ($temp_out_flg1 || $temp_out_flg2))
                        {
                            $array_impl_addHolidayTemp = array (
                            'current_date' => $target_date_ymd,
                            'current_result' => $result
                            );
                            $this->addHolidayTemp($array_impl_addHolidayTemp);
                        }
                        // } else {
                        //     // if (!$before_out_flg) {
                        //     //     $temp_non_date_flg = true;
                        //     // }
                        //     Log::debug('        打刻ないデータ 日付とユーザー休暇区分が１件前と同じ');
                        //     $temp_non_date_flg = $before_out_flg;       // 20200303修正
                        // }
                    }
                    // 20200414 add end
    
                    // ただし、日付とユーザー休暇区分が１件前と同じ場合は出力しない
                    // Log::debug('        打刻ないデータ = '.$result->user_code.' record_time = '.$result->record_datetime.' before_out_flg = '.$before_out_flg);
                    // $temp_non_date_flg = false;
                    // if(isset($result->user_holiday_kubun)) { $user_holiday_kubun = $result->user_holiday_kubun; }
                    // if(isset($result->user_holiday_name)) { $user_holiday_name = $result->user_holiday_name; }
                    // if(isset($result->user_working_date)) { $user_working_date = $result->user_working_date; }
                    // if (isset($before_result)) {
                    //     Log::debug('        打刻ないデータ before_result あり');
                    //     if ($before_result->department_code != $result->department_code ||
                    //         $before_result->user_code != $result->user_code ||
                    //         $before_result->user_working_date != $result->user_working_date ||
                    //         $before_holiday_kubun != $user_holiday_kubun) {
                    //         // if (!$before_out_flg) {
                    //         //     $temp_non_date_flg = true;
                    //         // }
                    //         Log::debug('        打刻ないデータ 日付とユーザー休暇区分が１件前と同じでない');
                    //         $temp_non_date_flg = $before_out_flg;       // 20200303修正
                    //     } else {
                    //         // if (!$before_out_flg) {
                    //         //     $temp_non_date_flg = true;
                    //         // }
                    //         Log::debug('        打刻ないデータ 日付とユーザー休暇区分が１件前と同じ');
                    //         $temp_non_date_flg = $before_out_flg;       // 20200303修正
                    //     }
                    // }
                    // 1件前の日付がnullである場合、いきなり対象日付がないということなので出力
                    // if (!$before_out_flg) {
                    //     Log::debug('        打刻ないデータ いきなり対象日付がない');
                    //     $temp_non_date_flg = true;
                    // }
                    // try{
                    //     if($temp_non_date_flg) {
                    //         Log::debug('    temp_calc_workingtimesの登録開始');
                    //         $ptn = 0;
                    //         $dt = date_format(new Carbon($target_date), 'Ymd');
                    //         Log::debug('            ターゲット日付 = '.$dt);
                    //         Log::debug('            ユーザー休暇  = '.$user_holiday_name);
                    //         Log::debug('            　　　　日付  = '.$user_working_date);
                    //         // setNoInputTimePtn implement
                    //         $array_impl_setNoInputTimePtn = array (
                    //             'ptn' => $ptn,
                    //             'user_holiday_name' => $user_holiday_name,
                    //             'target_date' => $dt,
                    //             'hpliday_date' => $user_working_date,
                    //             'value_working_timetable_no' => $result->working_timetable_no
                    //         );
                    //         $this->pushArrayCalc($this->setNoInputTimePtn($array_impl_setNoInputTimePtn));
                    //         // temporaryに登録する
                    //         // calcWorkingTimeDate implement
                    //         $array_impl_insTempCalcItem = array (
                    //             'target_date' => $target_date,
                    //             'target_result' => $result
                    //         );
                    //         $this->insTempCalcItem($array_impl_insTempCalcItem);
                    //         Log::debug('    temp_calc_workingtimesの登録終了');
                    //     }
                        // 日付とユーザー休暇区分を保存
                        $before_holiday_date = $result->user_working_date;
                        $before_holiday_user_code = $result->user_code;
                        $before_holiday_department_code = $result->department_code;
                        $before_holiday_kubun = $user_holiday_kubun;
                        $before_business_kubun = $result->business_kubun;
                        // }catch(\PDOException $pe){
                    //     $add_results = false;
                    //     throw $pe;
                    // }
                    // 次データ計算事前処理(打刻ないデータはbeforeArrayWorkingTimeは使用しない)
                    $before_date = null;
                    $before_user_code = null;
                    $before_department_code = null;
                    $before_result = null;
                    $before_out_flg = true;
                    // 次データ計算事前処理
                    // 打刻データ配列の初期化
                    $this->iniArrayWorkingTime();
                    // 計算用配列の初期化
                    $this->iniArrayCalc();
                }
                $before_result = $result;
            }
        }

        Log::debug('            count($this->array_working_mode) = '.count($this->array_working_mode));
        if (count($this->array_working_mode) > 0) {
            try{
                Log::debug('    最終残のユーザーを登録開始 $current_user_code = '.$current_user_code.' record_time = '.$current_result->record_datetime);
                // ユーザー労働時間登録
                // array_impl_addWorkingTime implement
                $array_impl_addWorkingTime = array (
                    'target_date' => $current_date,
                    'target_user_code' => $current_user_code,
                    'target_department_code' => $current_department_code,
                    'target_business_kubun' => $business_kubun,
                    'target_result' => $current_result
                );
                $add_results = $this->addWorkingTime($array_impl_addWorkingTime);
                // 打刻データ配列の初期化
                $this->iniArrayWorkingTime();
                // 計算用配列の初期化
                $this->iniArrayCalc();
                Log::debug('    最終残のユーザーを登録終了 $current_user_code = '.$current_user_code);
            }catch(\PDOException $pe){
                $add_results = false;
                throw $pe;
            }catch(\Exception $e){
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.data_error_dailycalc'));
                Log::error($e->getMessage());
                $add_results = false;
                throw $e;
            }
        // } else {
        //     // 打刻ないデータはtempに出力
        //     // 20200414 add start
        //     Log::debug('        最終残打刻ないデータ');
        //     // 有効打刻データがなくて、休暇扱いか出勤日である場合はtempに出力
        //     // 打刻されていれば出勤以外は出力対象外
        //     $temp_out_flg1 = false;
        //     $temp_out_flg2 = false;
        //     $temp_out_flg3 = false;
        //     if (isset($before_result->user_holiday_kubun) && $before_result->user_holiday_kubun >= (int)Config::get('const.C013.paid_holiday')) {
        //         $temp_out_flg1 = true;
        //     }
        //     if ($before_result->business_kubun == Config::get('const.C007.basic')) {
        //         $temp_out_flg2 = true;
        //     }
        //     if (!$attendance_target_flg)
        //     {
        //         $temp_out_flg3 = true;
        //     }
        //     Log::debug('        最終残データ $temp_out_flg1 '.$temp_out_flg1);
        //     Log::debug('        最終残データ $temp_out_flg2 '.$temp_out_flg2);
        //     Log::debug('        最終残データ $temp_out_flg3 '.$temp_out_flg3);
        //     if ($temp_out_flg3 && ($temp_out_flg1 || $temp_out_flg2))
        //     {
        //         $array_impl_addHolidayTemp = array (
        //         'current_date' => $target_date_ymd,
        //         'current_result' => $before_result
        //         );
        //         $this->addHolidayTemp($array_impl_addHolidayTemp);
        //     }
        //     // 20200414 add end
        }

        Log::debug('---------------------- 日次労働時間取得 calcWorkingTimeDate end ------------------------ ');

        return $add_results;

    }

    /**
     * ユーザー休暇データ登録
     *
     * @return 登録結果
     */
    private function addHolidayTemp($params)
    {
        Log::debug('---------------------- ユーザー休暇データ登録 addHolidayTemp in ------------------------ ');
        $current_date = $params['current_date'];
        $current_result = $params['current_result'];
        $user_holiday_name = $current_result->user_holiday_name;
        $user_working_date = $current_result->user_working_date;
        $working_timetable_no = $current_result->working_timetable_no;

        try{
            Log::debug('        打刻ないデータはtempに出力 $current_date = '.$current_date);
            $ptn = 0;
            $dt = date_format(new Carbon($current_date), 'Ymd');
            Log::debug('            ターゲット日付 = '.$dt);
            Log::debug('            ユーザー休暇   = '.$user_holiday_name);
            Log::debug('            　　　　日付   = '.$user_working_date);
            // setNoInputTimePtn implement
            $array_impl_setNoInputTimePtn = array (
                'ptn' => $ptn,
                'user_holiday_name' => $user_holiday_name,
                'target_date' => $dt,
                'hpliday_date' => $user_working_date,
                'value_working_timetable_no' => $working_timetable_no
            );
            $this->pushArrayCalc($this->setNoInputTimePtn($array_impl_setNoInputTimePtn));
            // temporaryに登録する
            // calcWorkingTimeDate implement
            $array_impl_insTempCalcItem = array (
                'target_date' => $current_date,
                'target_result' => $current_result
            );
            $this->insTempCalcItem($array_impl_insTempCalcItem);
            Log::debug('    temp_calc_workingtimesの登録終了');
        }catch(\PDOException $pe){
            $add_results = false;
            throw $pe;
        }

        Log::debug('---------------------- ユーザー休暇データ登録 addHolidayTemp end ------------------------ ');

    }
                
    /**
     * ユーザー労働時間登録
     *
     * @return 登録結果
     */
    private function addWorkingTime($params)
    {
        Log::debug('---------------------- addWorkingTime in ------------------------ ');
        // パラメータ設定
        $target_date = $params['target_date'];
        $target_user_code = $params['target_user_code'];
        $target_department_code = $params['target_department_code'];
        $business_kubun = $params['target_business_kubun'];
        $target_result = $params['target_result'];
        // ユーザー労働時間計算(１個前のユーザーを計算する)
        // calcWorkingTimeDate implement
        $array_impl_calcWorkingTime = array (
            'target_date' => $target_date,
            'target_user_code' => $target_user_code,
            'target_department_code' => $target_department_code,
            'target_business_kubun' => $business_kubun,
            'target_result' => $target_result
        );
        $this->calcWorkingTime($array_impl_calcWorkingTime);
        // temporaryに登録する
        try{
            // calcWorkingTimeDate implement
            $array_impl_insTempCalcItem = array (
                'target_date' => $target_date,
                'target_result' => $target_result
            );
            $this->insTempCalcItem($array_impl_insTempCalcItem);
        }catch(\PDOException $pe){
            throw $pe;
        }

        Log::debug('---------------------- addWorkingTime end ------------------------ ');
        return true;

    }

    /**
     * ユーザー労働時間計算
     *
     * @return 労働時間計算結果
     */
    private function calcWorkingTime($params)
    {
        Log::debug('---------------------- calcWorkingTime in ---');
        // パラメータ設定
        $target_date = $params['target_date'];
        $target_user_code = $params['target_user_code'];
        $target_department_code = $params['target_department_code'];
        $business_kubun = $params['target_business_kubun'];
        $target_result = $params['target_result'];
        $interval = $target_result->interval1;
        $user_holiday_kubun = $target_result->user_holiday_kubun;
        $use_free_item = $target_result->use_free_item;
        $target_record_time = $target_result->record_datetime;

        $work_time = new WorkTime();
        $work_time->setParamDepartmentcodeAttribute($target_department_code);
        $work_time->setParamUsercodeAttribute($target_user_code);
        $work_time->setParamStartDateAttribute($target_date);
        $work_time->setParamDatetoAttribute($target_date);
        $cnt = 0;
        // 前提 count($array_working_mode) = count($array_working_datetime)
        // working_timetable_noは出勤時刻のworking_timetable_noとする（日付が変わって退勤などがあるとworking_timetable_noが異なっている場合があるため）
        // また出勤時刻のworking_timetable_noがない場合はresultのworking_timetable_noとする
        $working_timetable_no_set_attendance = false;       // 出勤
        $working_timetable_no_set_emergency = false;        // 緊急出勤
        $attendance_time_index = -1;
        $value_working_timetable_no = 0;
        for($i=0;$i<count($this->array_working_mode);$i++){
            $value_mode = $this->array_working_mode[$i];
            $value_record_datetime = $this->array_working_datetime[$i];
            $value_record_datetime_id = $this->array_working_datetime_id[$i];
            $value_editor_department_code= $this->array_working_editor_department_code[$i];
            $value_editor_department_name = $this->array_working_editor_department_name[$i];
            $value_editor_user_code = $this->array_working_editor_user_code[$i];
            $value_editor_user_name = $this->array_working_editor_user_name[$i];
            $value_timetable_from_time = $this->array_timetable_from_time[$i];
            $value_timetable_to_time = $this->array_timetable_to_time[$i];
            $value_check_result = $this->array_check_result[$i];
            $value_check_max_times = $this->array_check_max_times[$i];
            $value_check_interval = $this->array_check_interval[$i];
            $value_mobile_positions = $this->array_mobile_positions[$i];
            Log::debug('        ユーザー労働時間計算 $value_mode = '.$value_mode);
            Log::debug('        ユーザー労働時間計算 $value_timetable_from_time = '.$value_timetable_from_time);
            Log::debug('        ユーザー労働時間計算 $value_timetable_to_time = '.$value_timetable_to_time);
            $dt = new Carbon($value_record_datetime);
            $record_date = date_format($dt, 'Ymd');
            // 事前にテーブル再取得（テーブル取得1日以前のMAX打刻時刻）しておく
            Log::debug('        テーブル取得1日以前のMAX打刻時刻 $value_record_datetime = '.$value_record_datetime);
            $before_value_mode = null;
            $before_value_datetime = null;
            $work_time->setParamStartDateAttribute($value_record_datetime);
            $work_time->setParamEndDateAttribute($value_record_datetime);
            $before_times = $work_time->getBeforeDailyMaxData();
            foreach ($before_times as $before_result) {
                // 打刻時刻の設定
                $before_value_mode = $before_result->mode;
                $before_value_datetime = $before_result->record_datetime;
                break;
            }
            Log::debug('        テーブル取得1日以前のMAX打刻時刻 $before_value_mode = '.$before_value_mode);
            Log::debug('        テーブル取得1日以前のMAX打刻時刻 $before_value_datetime = '.$before_value_datetime);
            // 事前にテーブル再取得（テーブル取得1日以降のMIN打刻時刻）しておく
            $after_value_mode = null;
            $after_value_datetime = null;
            $after_times = $work_time->getAfterDailyMinData();
            foreach ($after_times as $after_result) {
                // 打刻時刻の設定
                $after_value_mode = $after_result->mode;
                $after_value_datetime = $after_result->record_datetime;
                break;
            }
            Log::debug('        テーブル取得1日以降のMIN打刻時刻 $after_value_mode = '.$after_value_mode);
            Log::debug('        テーブル取得1日以降のMIN打刻時刻 $after_value_datetime = '.$after_value_datetime);
            $work_time->setParamDatefromAttribute($target_date);
            $work_time->setParamDatetoAttribute($target_date);
            // 出勤打刻の場合
            if ($value_mode == Config::get('const.C005.attendance_time')) {
                $value_working_timetable_no = $this->array_working_timetable_no[$i];
                $working_timetable_no_set_attendance = true;
                // 出勤状態設定
                // setAttendancetime implement
                $array_impl_setAttendancetime = array (
                    'cnt' => $cnt,
                    'work_time' => $work_time,
                    'value_record_datetime' => $value_record_datetime,
                    'value_timetable_from_time' => $value_timetable_from_time,
                    'value_timetable_to_time' => $value_timetable_to_time,
                    'value_check_result' => $value_check_result,
                    'value_check_max_times' => $value_check_max_times,
                    'value_check_interval' => $value_check_interval,
                    'value_record_datetime_id' => $value_record_datetime_id,
                    'value_editor_department_code' => $value_editor_department_code,
                    'value_editor_department_name' => $value_editor_department_name,
                    'value_editor_user_code' => $value_editor_user_code,
                    'value_editor_user_name' => $value_editor_user_name,
                    'value_mobile_positions' => $value_mobile_positions,
                    'before_value_mode' => $before_value_mode,
                    'before_value_datetime' => $before_value_datetime,
                    'business_kubun' => $business_kubun,
                    'interval' => $interval,
                    'user_holiday_kubun' => $user_holiday_kubun,
                    'use_free_item' => $use_free_item,
                    'value_working_timetable_no' => $value_working_timetable_no
                );
                $this->setAttendancetime($array_impl_setAttendancetime);
                if ($target_date == $record_date) {
                    if ($attendance_time_index == -1) {
                        $attendance_time_index = $i;
                    }
                }
            } elseif ($value_mode == Config::get('const.C005.leaving_time')) {      // 退勤の場合
                if (!$working_timetable_no_set_attendance) {
                    $value_working_timetable_no = $this->array_working_timetable_no[$i];
                }
                // 退勤状態設定
                // setAttendancetime implement
                $array_impl_setLeavingtime = array (
                    'cnt' => $cnt,
                    'work_time' => $work_time,
                    'value_record_datetime' => $value_record_datetime,
                    'value_record_datetime_id' => $value_record_datetime_id,
                    'value_editor_department_code' => $value_editor_department_code,
                    'value_editor_department_name' => $value_editor_department_name,
                    'value_editor_user_code' => $value_editor_user_code,
                    'value_editor_user_name' => $value_editor_user_name,
                    'value_timetable_from_time' => $value_timetable_from_time,
                    'value_timetable_to_time' => $value_timetable_to_time,
                    'value_check_result' => $value_check_result,
                    'value_check_max_times' => $value_check_max_times,
                    'value_mobile_positions' => $value_mobile_positions,
                    'before_value_mode' => $before_value_mode,
                    'before_value_datetime' => $before_value_datetime,
                    'business_kubun' => $business_kubun,
                    'user_holiday_kubun' => $user_holiday_kubun,
                    'use_free_item' => $use_free_item,
                    'value_working_timetable_no' => $value_working_timetable_no,
                    'attendance_time_index' => $attendance_time_index
                );
                $this->setLeavingtime($array_impl_setLeavingtime);
                $working_timetable_no_set_attendance = false;
            } elseif ($value_mode == Config::get('const.C005.missing_middle_time')) {       // 私用外出の場合
                if (!$working_timetable_no_set_attendance && !$working_timetable_no_set_emergency) {
                    $value_working_timetable_no = $this->array_working_timetable_no[$i];
                }
                // 私用外出状態設定
                // setMissingMiddleTime implement
                $array_impl_setMissingMiddleTime = array (
                    'cnt' => $cnt,
                    'work_time' => $work_time,
                    'value_record_datetime' => $value_record_datetime,
                    'value_record_datetime_id' => $value_record_datetime_id,
                    'value_editor_department_code' => $value_editor_department_code,
                    'value_editor_department_name' => $value_editor_department_name,
                    'value_editor_user_code' => $value_editor_user_code,
                    'value_editor_user_name' => $value_editor_user_name,
                    'value_timetable_from_time' => $value_timetable_from_time,
                    'value_timetable_to_time' => $value_timetable_to_time,
                    'value_check_result' => $value_check_result,
                    'value_check_max_times' => $value_check_max_times,
                    'value_mobile_positions' => $value_mobile_positions,
                    'before_value_mode' => $before_value_mode,
                    'before_value_datetime' => $before_value_datetime,
                    'business_kubun' => $business_kubun,
                    'user_holiday_kubun' => $user_holiday_kubun,
                    'use_free_item' => $use_free_item,
                    'value_working_timetable_no' => $value_working_timetable_no,
                    'attendance_time_index' => $attendance_time_index
                );
                $this->setMissingMiddleTime($array_impl_setMissingMiddleTime);
            } elseif ($value_mode == Config::get('const.C005.missing_middle_return_time')) {        // 私用外出戻りの場合
                if (!$working_timetable_no_set_attendance && !$working_timetable_no_set_emergency) {
                    $value_working_timetable_no = $this->array_working_timetable_no[$i];
                }
                // 私用外出戻り状態設定
                // setMissingMiddleReturnTime implement
                $array_impl_setMissingMiddleReturnTime = array (
                    'cnt' => $cnt,
                    'work_time' => $work_time,
                    'value_record_datetime' => $value_record_datetime,
                    'value_record_datetime_id' => $value_record_datetime_id,
                    'value_editor_department_code' => $value_editor_department_code,
                    'value_editor_department_name' => $value_editor_department_name,
                    'value_editor_user_code' => $value_editor_user_code,
                    'value_editor_user_name' => $value_editor_user_name,
                    'value_timetable_from_time' => $value_timetable_from_time,
                    'value_timetable_to_time' => $value_timetable_to_time,
                    'value_check_result' => $value_check_result,
                    'value_check_max_times' => $value_check_max_times,
                    'value_mobile_positions' => $value_mobile_positions,
                    'before_value_mode' => $before_value_mode,
                    'before_value_datetime' => $before_value_datetime,
                    'business_kubun' => $business_kubun,
                    'user_holiday_kubun' => $user_holiday_kubun,
                    'use_free_item' => $use_free_item,
                    'value_working_timetable_no' => $value_working_timetable_no,
                    'attendance_time_index' => $attendance_time_index
                );
                $this->setMissingMiddleReturnTime($array_impl_setMissingMiddleReturnTime);
            } elseif ($value_mode == Config::get('const.C005.public_going_out_time')) {             // 公用外出の場合
                if (!$working_timetable_no_set_attendance && !$working_timetable_no_set_emergency) {
                    $value_working_timetable_no = $this->array_working_timetable_no[$i];
                }
                // 公用外出状態設定
                // setPubliGoingOutTime implement
                $array_impl_setPubliGoingOutTime = array (
                    'cnt' => $cnt,
                    'work_time' => $work_time,
                    'value_record_datetime' => $value_record_datetime,
                    'value_record_datetime_id' => $value_record_datetime_id,
                    'value_editor_department_code' => $value_editor_department_code,
                    'value_editor_department_name' => $value_editor_department_name,
                    'value_editor_user_code' => $value_editor_user_code,
                    'value_editor_user_name' => $value_editor_user_name,
                    'value_timetable_from_time' => $value_timetable_from_time,
                    'value_timetable_to_time' => $value_timetable_to_time,
                    'value_check_result' => $value_check_result,
                    'value_check_max_times' => $value_check_max_times,
                    'value_mobile_positions' => $value_mobile_positions,
                    'before_value_mode' => $before_value_mode,
                    'before_value_datetime' => $before_value_datetime,
                    'business_kubun' => $business_kubun,
                    'user_holiday_kubun' => $user_holiday_kubun,
                    'use_free_item' => $use_free_item,
                    'value_working_timetable_no' => $value_working_timetable_no,
                    'attendance_time_index' => $attendance_time_index
                );
                $this->setPubliGoingOutTime($array_impl_setPubliGoingOutTime);
            } elseif ($value_mode == Config::get('const.C005.public_going_out_return_time')) {      // 公用外出戻りの場合
                if (!$working_timetable_no_set_attendance && !$working_timetable_no_set_emergency) {
                    $value_working_timetable_no = $this->array_working_timetable_no[$i];
                }
                // 公用外出状態設定
                // setPublicGoingOutReturnTime implement
                $array_impl_setPublicGoingOutReturnTime = array (
                    'cnt' => $cnt,
                    'work_time' => $work_time,
                    'value_record_datetime' => $value_record_datetime,
                    'value_record_datetime_id' => $value_record_datetime_id,
                    'value_editor_department_code' => $value_editor_department_code,
                    'value_editor_department_name' => $value_editor_department_name,
                    'value_editor_user_code' => $value_editor_user_code,
                    'value_editor_user_name' => $value_editor_user_name,
                    'value_timetable_from_time' => $value_timetable_from_time,
                    'value_timetable_to_time' => $value_timetable_to_time,
                    'value_check_result' => $value_check_result,
                    'value_check_max_times' => $value_check_max_times,
                    'value_mobile_positions' => $value_mobile_positions,
                    'before_value_mode' => $before_value_mode,
                    'before_value_datetime' => $before_value_datetime,
                    'business_kubun' => $business_kubun,
                    'user_holiday_kubun' => $user_holiday_kubun,
                    'use_free_item' => $use_free_item,
                    'value_working_timetable_no' => $value_working_timetable_no,
                    'attendance_time_index' => $attendance_time_index
                );
                $this->setPublicGoingOutReturnTime($array_impl_setPublicGoingOutReturnTime);
            } elseif ($value_mode == Config::get('const.C005.emergency_time')) {            // 緊急収集開始
                $value_working_timetable_no = $this->array_working_timetable_no[$i];
                Log::debug('          setEmergencytime前 value_working_timetable_no = '.$value_working_timetable_no);
                $working_timetable_no_set_emergency = true;
                // 出勤状態設定
                // setAttendancetime implement
                $array_impl_setEmergencytime = array (
                    'cnt' => $cnt,
                    'work_time' => $work_time,
                    'value_record_datetime' => $value_record_datetime,
                    'value_timetable_from_time' => $value_timetable_from_time,
                    'value_timetable_to_time' => $value_timetable_to_time,
                    'value_check_result' => $value_check_result,
                    'value_check_max_times' => $value_check_max_times,
                    'value_check_interval' => $value_check_interval,
                    'value_record_datetime_id' => $value_record_datetime_id,
                    'value_editor_department_code' => $value_editor_department_code,
                    'value_editor_department_name' => $value_editor_department_name,
                    'value_editor_user_code' => $value_editor_user_code,
                    'value_editor_user_name' => $value_editor_user_name,
                    'value_mobile_positions' => $value_mobile_positions,
                    'before_value_mode' => $before_value_mode,
                    'before_value_datetime' => $before_value_datetime,
                    'business_kubun' => $business_kubun,
                    'interval' => $interval,
                    'user_holiday_kubun' => $user_holiday_kubun,
                    'use_free_item' => $use_free_item,
                    'value_working_timetable_no' => $value_working_timetable_no
                );
                $this->setEmergencytime($array_impl_setEmergencytime);
                if ($target_date == $record_date) {
                    if ($attendance_time_index == -1) {
                        $attendance_time_index = $i;
                    }
                }
            } elseif ($value_mode == Config::get('const.C005.emergency_return_time')) {         // 緊急収集終了の場合
                if (!$working_timetable_no_set_emergency) {
                    $value_working_timetable_no = $this->array_working_timetable_no[$i];
                }
                // 退勤状態設定
                // setAttendancetime implement
                $array_impl_setEmergencyReturntime = array (
                    'cnt' => $cnt,
                    'work_time' => $work_time,
                    'value_mode' => $value_mode,
                    'value_record_datetime' => $value_record_datetime,
                    'value_record_datetime_id' => $value_record_datetime_id,
                    'value_editor_department_code' => $value_editor_department_code,
                    'value_editor_department_name' => $value_editor_department_name,
                    'value_editor_user_code' => $value_editor_user_code,
                    'value_editor_user_name' => $value_editor_user_name,
                    'value_timetable_from_time' => $value_timetable_from_time,
                    'value_timetable_to_time' => $value_timetable_to_time,
                    'value_check_result' => $value_check_result,
                    'value_check_max_times' => $value_check_max_times,
                    'value_mobile_positions' => $value_mobile_positions,
                    'before_value_mode' => $before_value_mode,
                    'before_value_datetime' => $before_value_datetime,
                    'business_kubun' => $business_kubun,
                    'user_holiday_kubun' => $user_holiday_kubun,
                    'use_free_item' => $use_free_item,
                    'value_working_timetable_no' => $value_working_timetable_no,
                    'attendance_time_index' => $attendance_time_index
                );
                $this->setEmergencyReturntime($array_impl_setEmergencyReturntime);
                $working_timetable_no_set_emergency = false;
            }
            $before_value_mode = $value_mode;
            $before_value_datetime = $value_record_datetime;
            $cnt = $cnt + 1;
        }

        Log::debug('---------------------- calcWorkingTime end ------------------------ ');
       
    }

    /**
     * 出勤打刻処理
     *
     * @param  打刻時刻のSEQ
     *         打刻時刻
     *         １個前のモード
     * @return void
     */
    private function setAttendancetime($params)
    {

        Log::debug('---------------------- 出勤打刻処理 setAttendancetime in ------------------------ ');
        $cnt = $params['cnt'];
        $work_time = $params['work_time'];
        $value_record_datetime = $params['value_record_datetime'];
        $value_timetable_from_time = $params['value_timetable_from_time'];
        $value_timetable_to_time = $params['value_timetable_to_time'];
        $value_check_result = $params['value_check_result'];
        $value_check_max_times = $params['value_check_max_times'];
        $value_check_interval = $params['value_check_interval'];
        $value_record_datetime_id = $params['value_record_datetime_id'];
        $value_editor_department_code = $params['value_editor_department_code'];
        $value_editor_department_name = $params['value_editor_department_name'];
        $value_editor_user_code = $params['value_editor_user_code'];
        $value_editor_user_name = $params['value_editor_user_name'];
        $value_mobile_positions = $params['value_mobile_positions'];
        $before_value_mode = $params['before_value_mode'];          // 1個前のモード
        $before_value_datetime = $params['before_value_datetime'];
        $business_kubun = $params['business_kubun'];
        $interval = $params['interval'];
        $user_holiday_kubun = $params['user_holiday_kubun'];
        $use_free_item = $params['use_free_item'];
        $value_working_timetable_no = $params['value_working_timetable_no'];
        $apicommon = new ApiCommonController();
        $attendance_from_date = new Carbon($work_time->getParamDatefromAttribute());        // 出勤1日のはじめ
        $attendance_from_date_format = date_format($attendance_from_date, 'Y/m/d');
        $timetable_from_date = new Carbon(
            $attendance_from_date_format.' '.$value_timetable_from_time);                   // タイムテーブルの始業時刻
        if ($value_timetable_from_time <= $value_timetable_to_time) {
            $timetable_to_date = new Carbon(
                $attendance_from_date_format.' '.$value_timetable_to_time);                 // タイムテーブルの終業時刻
        } else {
            // 翌日
            $nextdt =$apicommon->getNextDay($attendance_from_date_format, 'Y/m/d');
            $timetable_to_date = new Carbon(
                    $nextdt.' '.$value_timetable_to_time);                                  // タイムテーブルの終業時刻
        }
        $attendance_to_date = new Carbon(
            $attendance_from_date_format.' 23:59:59');                                      // 出勤1日の終わり
        $record_datetime = new Carbon($value_record_datetime);                              // 打刻日付時刻
        $record_before_datetime = new Carbon($before_value_datetime);                       // １個前の打刻時刻
        Log::debug('            出勤1日のはじめ attendance_from_date  = '.$attendance_from_date);
        Log::debug('            タイムテーブルの始業時刻 timetable_from_date  = '.$timetable_from_date);
        Log::debug('            タイムテーブルの終業時刻 timetable_to_date  = '.$timetable_to_date);
        Log::debug('            出勤1日の終わり attendance_to_date  = '.$attendance_to_date);
        Log::debug('            打刻日付時刻 record_datetime  = '.$record_datetime);
        Log::debug('                        record_before_datetime = '.$record_before_datetime);
        Log::debug('            1個前のモード before_value_mode = '.$before_value_mode);
        Log::debug('            1個前の打刻   before_value_datetime = '.$before_value_datetime);
        Log::debug('            cnt  = '.$cnt);
        // パターン設定
        $ptn = null;

        // ---------------------出勤が最初の場合 -----------------------------------------------------------------------------------
        if ($cnt == 0) {
            if ($before_value_mode == Config::get('const.C005.attendance_time') ||
                $before_value_mode == Config::get('const.C005.emergency_time')) {           // １個前のモードが出勤である場合
                if ($record_before_datetime < $attendance_from_date) {                      // １個前の打刻時刻 < 出勤1日のはじめ
                    // パターン２（打刻ミス（出勤済）（要確認）。勤務状態は打刻なし）
                    $ptn = '2';
                }
            } elseif ($before_value_mode == Config::get('const.C005.leaving_time') ||
                    $before_value_mode == Config::get('const.C005.emergency_return_time')) {       // １個前のモードが退勤である場合
                if ($record_datetime >= $attendance_from_date &&
                    $record_datetime <= $timetable_from_date) {                             // 出勤1日のはじめ <= 打刻時刻 < タイムテーブルの始業時刻
                    $ptn = '1';
                    // 退勤から出勤までのタイム差を取得しインターバルチェック
                    $value_check_interval = $apicommon->chkInteval($record_datetime, $record_before_datetime);
                } elseif ($record_datetime >= $timetable_from_date &&
                            $record_datetime <= $timetable_to_date) {                       // タイムテーブルの始業時刻 <= 打刻時刻 < タイムテーブルの終業時刻
                    // パターン３（遅刻出勤（遅刻=1）。勤務状態は出勤状態）
                    $ptn = '3';
                    // 退勤から出勤までのタイム差を取得しインターバルチェック
                    $value_check_interval = $apicommon->chkInteval($record_datetime, $record_before_datetime);
                } elseif ($record_datetime >= $timetable_to_date &&
                            $record_datetime <= $attendance_to_date) {                      // タイムテーブルの終業時刻 <= 打刻時刻 < 出勤1日の終わり
                    //if ($record_before_datetime < $attendance_from_date) {                // １個前の打刻時刻 < 出勤1日のはじめ
                        // パターン４（遅刻出勤（要確認）。勤務状態は出勤状態）
                        $ptn = '4';
                        // 退勤から出勤までのタイム差を取得しインターバルチェック
                        $value_check_interval = $apicommon->chkInteval($record_datetime, $record_before_datetime);
                    //}
                } elseif ($record_datetime > $attendance_to_date) {                         // 出勤1日の終わり < 打刻時刻
                    if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                        // 不明データ
                        // 出勤打刻時刻が出勤1日の終わりより大きいことはない
                        $log_data = $work_time->getParamDatefromAttribute();
                        $log_data .= $work_time->getParamUsercodeAttribute();
                        $log_data .= $work_time->getParamDepartmentcodeAttribute();
                    }
                } else {
                    // 不明データ
                    $log_data = $work_time->getParamDatefromAttribute();
                    $log_data .= $work_time->getParamUsercodeAttribute();
                    $log_data .= $work_time->getParamDepartmentcodeAttribute();
                }
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_time')) {    // １個前のモードが私用または公用外出である場合
                if ($record_before_datetime < $attendance_from_date) {                      // １個前の打刻時刻 < 出勤1日のはじめ
                    // パターン５（打刻ミス（外出戻りしていない）。勤務状態は打刻なし）
                    $ptn = '5';
                }
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_time_return_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_return_time')) {     // １個前のモードが戻り
                if ($record_before_datetime < $attendance_from_date) {                      // １個前の打刻時刻 < 出勤1日のはじめ
                    // パターン６（打刻ミス（退勤していない）。勤務状態は打刻なし）
                    $ptn = '6';
                }
            } else {                                                                        // １個前のモードがない
                Log::debug('        １個前のモードがない ');
                if ($record_datetime >= $attendance_from_date &&
                    $record_datetime <= $timetable_from_date) {                              // 出勤1日のはじめ <= 打刻時刻 < タイムテーブルの始業時刻
                    // パターン１（正常出勤。勤務状態は出勤状態）
                    $ptn = '1';
                } elseif ($record_datetime >= $timetable_from_date &&
                            $record_datetime <= $timetable_to_date) {                        // タイムテーブルの始業時刻 <= 打刻時刻 < タイムテーブルの終業時刻
                    // パターン３（遅刻出勤（遅刻=1）。勤務状態は出勤状態）
                    $ptn = '3';
                } elseif ($record_datetime >= $timetable_to_date &&
                            $record_datetime <= $attendance_to_date) {                       // タイムテーブルの終業時刻 <= 打刻時刻 < 出勤1日の終わり
                    // パターン４（遅刻出勤（要確認）。勤務状態は出勤状態）
                    $ptn = '4';
                } elseif ($record_datetime > $attendance_to_date) {                         // 出勤1日の終わり < 打刻時刻
                    // 不明データ
                    // 出勤打刻時刻が出勤1日の終わりより大きいことはない
                    $log_data = $work_time->getParamDatefromAttribute();
                    $log_data .= $work_time->getParamUsercodeAttribute();
                    $log_data .= $work_time->getParamDepartmentcodeAttribute();
                } else {
                    // 不明データ
                    $log_data = $work_time->getParamDatefromAttribute();
                    $log_data .= $work_time->getParamUsercodeAttribute();
                    $log_data .= $work_time->getParamDepartmentcodeAttribute();
                }
            }
        // ---------------------出勤が２番目以降の場合 -----------------------------------------------------------------------------------
        } else {
            if ($before_value_mode == Config::get('const.C005.attendance_time') ||
                $before_value_mode == Config::get('const.C005.emergency_time')) {           // １個前のモードが出勤である場合
                if ($record_before_datetime >= $attendance_from_date &&
                    $record_before_datetime <= $attendance_to_date) {                       // 出勤1日のはじめ <= １個前の打刻時刻 < 出勤1日の終わり
                    // パターン６（打刻ミス（退勤していない）。勤務状態は打刻なし）
                    $ptn = '6';
                }
            } elseif ($before_value_mode == Config::get('const.C005.leaving_time') ||
                    $before_value_mode == Config::get('const.C005.emergency_return_time')) {       // １個前のモードが退勤である場合
                if ($record_datetime >= $attendance_from_date &&
                            $record_datetime <= $timetable_from_date) {                      // 出勤1日のはじめ <= 打刻時刻 < タイムテーブルの始業時刻
                    if ($record_before_datetime >= $attendance_from_date &&
                                $record_before_datetime <= $timetable_from_date) {           // 出勤1日のはじめ <= １個前の打刻時刻 < タイムテーブルの始業時刻
                        // パターン１（正常出勤。勤務状態は出勤状態）
                        $ptn = '1';
                        // 退勤から出勤までのタイム差を取得しインターバルチェック
                        $value_check_interval = $apicommon->chkInteval($record_datetime, $record_before_datetime);
                    }
                } elseif ($record_datetime >= $timetable_from_date &&
                                 $record_datetime <= $timetable_to_date) {                   // タイムテーブルの始業時刻 <= 打刻時刻 < タイムテーブルの終業時刻
                    if ($record_before_datetime >= $attendance_from_date &&
                                     $record_before_datetime <= $timetable_to_date) {        // 出勤1日のはじめ <= １個前の打刻時刻 < タイムテーブルの終業時刻
                        // パターン３（遅刻出勤（遅刻=1）。勤務状態は出勤状態）
                        $ptn = '3';
                        // 退勤から出勤までのタイム差を取得しインターバルチェック
                        $value_check_interval = $apicommon->chkInteval($record_datetime, $record_before_datetime);
                    }
                } elseif ($record_datetime >= $timetable_to_date &&
                                $record_datetime <= $attendance_to_date) {                   // タイムテーブルの終業時刻 <= 打刻時刻 < 出勤1日の終わり
                    if ($record_before_datetime >= $attendance_from_date &&
                                    $record_before_datetime < $attendance_to_date) {        // 出勤1日のはじめ <= １個前の打刻時刻 < 出勤1日の終わり
                        // パターン４（遅刻出勤（要確認）。勤務状態は出勤状態）
                        $ptn = '4';
                        // 退勤から出勤までのタイム差を取得しインターバルチェック
                        $value_check_interval = $apicommon->chkInteval($record_datetime, $record_before_datetime);
                    }
                } elseif ($record_datetime > $attendance_to_date) {
                    // 不明データ
                    // 出勤打刻時刻が出勤1日の終わりより大きいことはない
                    $log_data = $work_time->getParamDatefromAttribute();
                    $log_data .= $work_time->getParamUsercodeAttribute();
                    $log_data .= $work_time->getParamDepartmentcodeAttribute();
                } else {
                    // 不明データ
                    $log_data = $work_time->getParamDatefromAttribute();
                    $log_data .= $work_time->getParamUsercodeAttribute();
                    $log_data .= $work_time->getParamDepartmentcodeAttribute();
                }
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_time')) {    // １個前のモードが私用または公用外出である場合
                if ($record_before_datetime >= $attendance_from_date &&
                            $record_before_datetime < $attendance_to_date) {                // 出勤1日のはじめ <= １個前の打刻時刻 < 出勤1日の終わり
                    // パターン５（打刻ミス（外出戻りしていない）。勤務状態は打刻なし）
                    $ptn = '5';
                }
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_return_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_return_time')) {     // １個前のモードが戻り
                if ($record_before_datetime >= $attendance_from_date &&
                            $record_before_datetime < $attendance_to_date) {                // 出勤1日のはじめ <= １個前の打刻時刻 < 出勤1日の終わり
                    // パターン６（打刻ミス（退勤していない）。勤務状態は打刻なし）
                    $ptn = '6';
                }
            } else {                                                                        // １個前のモードがない
                if ($record_datetime >= $attendance_from_date &&
                            $record_datetime <= $timetable_from_date) {                      // 出勤1日のはじめ <= 打刻時刻 < タイムテーブルの始業時刻
                    if ($record_before_datetime >= $attendance_from_date &&
                                $record_before_datetime < $timetable_from_date) {           // 出勤1日のはじめ <= １個前の打刻時刻 < タイムテーブルの始業時刻
                        // パターン１（正常出勤。勤務状態は出勤状態）
                        $ptn = '1';
                    }
                } elseif ($record_datetime >= $timetable_from_date &&
                                 $record_datetime <= $timetable_to_date) {                   // タイムテーブルの始業時刻 <= 打刻時刻 < タイムテーブルの終業時刻
                    if ($record_before_datetime >= $attendance_from_date &&
                                     $record_before_datetime < $timetable_to_date) {        // 出勤1日のはじめ <= １個前の打刻時刻 < タイムテーブルの終業時刻
                        // パターン３（遅刻出勤（遅刻=1）。勤務状態は出勤状態）
                        $ptn = '3';
                    }
                } elseif ($record_datetime >= $timetable_to_date &&
                                $record_datetime <= $attendance_to_date) {                   // タイムテーブルの終業時刻 <= 打刻時刻 < 出勤1日の終わり
                    if ($record_before_datetime >= $attendance_from_date &&
                                    $record_before_datetime < $attendance_to_date) {        // 出勤1日のはじめ <= １個前の打刻時刻 < 出勤1日の終わり
                        // パターン４（遅刻出勤（要確認）。勤務状態は出勤状態）
                        $ptn = '4';
                    }
                } elseif ($record_datetime > $attendance_to_date) {
                    // 不明データ
                    // 出勤打刻時刻が出勤1日の終わりより大きいことはない
                    $log_data = $work_time->getParamDatefromAttribute();
                    $log_data .= $work_time->getParamUsercodeAttribute();
                    $log_data .= $work_time->getParamDepartmentcodeAttribute();
                } else {
                    // 不明データ
                    $log_data = $work_time->getParamDatefromAttribute();
                    $log_data .= $work_time->getParamUsercodeAttribute();
                    $log_data .= $work_time->getParamDepartmentcodeAttribute();
                }
            }
        }

        if ($ptn == null) {
            $ptn = '';
        }
        // setAttendanceCollectPtn implement
        $array_impl_setAttendanceCollectPtn = array (
            'ptn' => $ptn,
            'record_datetime' => $record_datetime,
            'value_record_datetime_id' => $value_record_datetime_id,
            'value_editor_department_code' => $value_editor_department_code,
            'value_editor_department_name' => $value_editor_department_name,
            'value_editor_user_code' => $value_editor_user_code,
            'value_editor_user_name' => $value_editor_user_name,
            'value_check_result' => $value_check_result,
            'value_check_max_times' => $value_check_max_times,
            'value_check_interval' => $value_check_interval,
            'value_mobile_positions' => $value_mobile_positions,
            'business_kubun' => $business_kubun,
            'user_holiday_kubun' => $user_holiday_kubun,
            'use_free_item' => $use_free_item,
            'value_working_timetable_no' => $value_working_timetable_no
        );
        $this->pushArrayCalc($this->setAttendanceCollectPtn($array_impl_setAttendanceCollectPtn));

        $this->check_interval2 = $value_check_interval;
        Log::debug('---------------------- 出勤打刻処理 setAttendancetime end ------------------------ ');
    }

    /**
     * 出勤打刻時刻テーブル設定
     *
     *        $collect_working_times
     *          'mode': 打刻モード（出勤）
     *          'time': 打刻時刻
     *          'status': 打刻状態
     *          'note': メモ
     *        　'late': 遅刻
     *          'Leave_early':早退
     *          'interval':インターバルチェック
     *          'calc':当日計算
     *          'to_be_confirmed':要確認
     * 
     * @param  打刻時刻
     * @return テーブル
     */
    private function setAttendanceCollectPtn($params)
    {
        Log::debug('---------------------- setAttendanceCollectPtn in -- -------------------- ');
        // パラメータ設定
        Log::debug('                       ptn = '.$params['ptn']);
        $ptn = $params['ptn'];
        $record_datetime = $params['record_datetime'];
        $value_record_datetime_id = $params['value_record_datetime_id'];
        $value_editor_department_code = $params['value_editor_department_code'];
        $value_editor_department_name = $params['value_editor_department_name'];
        $value_editor_user_code = $params['value_editor_user_code'];
        $value_editor_user_name = $params['value_editor_user_name'];
        $value_check_result = $params['value_check_result'];
        $value_check_max_times = $params['value_check_max_times'];
        $value_check_interval = $params['value_check_interval'];
        $value_mobile_positions = $params['value_mobile_positions'];
        $business_kubun = $params['business_kubun'];
        $user_holiday_kubun = $params['user_holiday_kubun'];
        $use_free_item = $params['use_free_item'];
        $working_timetable_no = $params['value_working_timetable_no'];
        $temp_calc_model = new TempCalcWorkingTime();

        if ($ptn == '1') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.attendance'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('1');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute($value_check_interval);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } elseif ($ptn == '2') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_001'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute($value_check_interval);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } elseif ($ptn == '3') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.attendance'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            // 遅刻の設定値（休日は"0"）
            if (!isset($business_kubun)) { $business_kubun = Config::get('const.C007.basic'); }
            if (!isset($user_holiday_kubun)) { $user_holiday_kubun = Config::get('const.C013.non_set'); }
            Log::debug('        用途フリー項目 =  '.$use_free_item);
            if ($business_kubun == Config::get('const.C007.basic')) {
                if ((!isset($use_free_item)) ||
                    (isset($use_free_item) &&
                    (substr($use_free_item, Config::get('const.USEFREEITEM.day_holiday'), 1) == "1"))) {
                    $temp_calc_model->setLateAttribute('1');
                } else {
                    $temp_calc_model->setLateAttribute('0');
                }
            } else {
                $temp_calc_model->setLateAttribute('0');
            }
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('1');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute($value_check_interval);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } elseif ($ptn == '4') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.attendance'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_001'));
            // 遅刻の設定値（休日は"0"）
            if (!isset($business_kubun)) { $business_kubun = Config::get('const.C007.basic'); }
            if (!isset($user_holiday_kubun)) { $user_holiday_kubun = Config::get('const.C013.non_set'); }
            Log::debug('        用途フリー項目 =  '.$use_free_item);
            if ($business_kubun == Config::get('const.C007.basic')) {
                if ((!isset($use_free_item)) ||
                    (isset($use_free_item) &&
                    (substr($use_free_item, Config::get('const.USEFREEITEM.day_holiday'), 1) == "1"))) {
                    $temp_calc_model->setLateAttribute('1');
                } else {
                    $temp_calc_model->setLateAttribute('0');
                }
            } else {
                $temp_calc_model->setLateAttribute('0');
            }
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('1');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute($value_check_interval);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } elseif ($ptn == '5') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_003'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute($value_check_interval);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } elseif ($ptn == '6') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_001'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute($value_check_interval);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } else {
            // 不明データとして作成する
            $temp_calc_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.unknown'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_004'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute($value_check_interval);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        }

        Log::debug('---------------------- setAttendanceCollectPtn end ------------------------ ');
        return $temp_calc_model;
            
    }

    /**
     * 退勤打刻処理
     *
     * @param  打刻時刻のSEQ
     *         打刻時刻
     *         １個前のモード
     * @return チェック結果
     */
    private function setLeavingtime($params)
    {
        try {
            Log::debug('---------------------- setLeavingtime in ------------------------ ');
            $cnt = $params['cnt'];
            $work_time = $params['work_time'];
            $value_record_datetime = $params['value_record_datetime'];
            $value_record_datetime_id = $params['value_record_datetime_id'];
            $value_editor_department_code = $params['value_editor_department_code'];
            $value_editor_department_name = $params['value_editor_department_name'];
            $value_editor_user_code = $params['value_editor_user_code'];
            $value_editor_user_name = $params['value_editor_user_name'];
            $value_timetable_from_time = $params['value_timetable_from_time'];
            $value_timetable_to_time = $params['value_timetable_to_time'];
            $value_check_result = $params['value_check_result'];
            $value_check_max_times = $params['value_check_max_times'];
            $value_mobile_positions = $params['value_mobile_positions'];
            $before_value_mode = $params['before_value_mode'];
            $before_value_datetime = $params['before_value_datetime'];
            $business_kubun = $params['business_kubun'];
            $user_holiday_kubun = $params['user_holiday_kubun'];
            $use_free_item = $params['use_free_item'];
            $value_working_timetable_no = $params['value_working_timetable_no'];
            $attendance_time_index = $params['attendance_time_index'];

            $array_impl_setLeavingCollectPtn = null;
            $apicommon = new ApiCommonController();
            $attendance_from_date = new Carbon($work_time->getParamDatefromAttribute());        // 出勤1日のはじめ
            $attendance_from_date_format = date_format($attendance_from_date, 'Y/m/d');
            $timetable_from_date = new Carbon(
                $attendance_from_date_format.' '.$value_timetable_from_time);                   // タイムテーブルの始業時刻
            if ($value_timetable_from_time <= $value_timetable_to_time) {
                $timetable_to_date = new Carbon(
                    $attendance_from_date_format.' '.$value_timetable_to_time);                 // タイムテーブルの終業時刻
            } else {
                // 翌日
                $nextdt =$apicommon->getNextDay($attendance_from_date_format, 'Y/m/d');
                $timetable_to_date = new Carbon(
                        $nextdt.' '.$value_timetable_to_time);                                  // タイムテーブルの終業時刻
            }
            $attendance_to_date = new Carbon(
                $attendance_from_date_format.' 23:59:59');                                      // 出勤1日の終わり
            $record_datetime = new Carbon($value_record_datetime);                              // 打刻日付時刻
            $record_before_datetime = new Carbon($before_value_datetime);                       // １個前の打刻時刻
            Log::debug('            出勤1日のはじめ attendance_from_date  = '.$attendance_from_date);
            Log::debug('            タイムテーブルの始業時刻 timetable_from_date  = '.$timetable_from_date);
            Log::debug('            タイムテーブルの終業時刻 timetable_to_date  = '.$timetable_to_date);
            Log::debug('            出勤1日の終わり attendance_to_date  = '.$attendance_to_date);
            Log::debug('            打刻日付時刻 record_datetime  = '.$record_datetime);
            Log::debug('                        record_before_datetime = '.$record_before_datetime);
            Log::debug('            1個前のモード before_value_mode = '.$before_value_mode);
            Log::debug('            1個前の打刻   before_value_datetime = '.$before_value_datetime);
            Log::debug('            cnt  = '.$cnt);
            // パターン設定
            $ptn = null;

            // ---------------------退勤が最初の場合 -----------------------------------------------------------------------------------
            if ($cnt == 0) {
                if ($before_value_mode == Config::get('const.C005.attendance_time') ||
                    $before_value_mode == Config::get('const.C005.missing_middle_return_time') ||
                    $before_value_mode == Config::get('const.C005.public_going_out_return_time')) {     // １個前のモードが出勤または戻りである場合
                    if ($record_datetime >= $attendance_from_date &&
                                $record_datetime <= $timetable_from_date) {                      // 出勤1日のはじめ <= 打刻時刻 < タイムテーブルの始業時刻
                        if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                            // パターン１（正常退勤。勤務状態は退勤状態。当日時間計算なし。）
                            $ptn = '1';
                        }
                    } elseif ($record_datetime >= $timetable_from_date &&
                                $record_datetime <= $timetable_to_date) {                        // タイムテーブルの始業時刻 <= 打刻時刻 < タイムテーブルの終業時刻
                        if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                            // パターン２３４
                            $ptn = '2';
                        }
                    } elseif ($record_datetime >= $timetable_to_date &&
                                $record_datetime <= $attendance_to_date) {                       // タイムテーブルの終業時刻 <= 打刻時刻 < 出勤1日の終わり
                        if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                            // パターン２３５
                            $ptn = '2';
                        }
                    } elseif ($record_datetime > $attendance_to_date) {                         // 出勤1日の終わり < 打刻時刻
                        if ($record_before_datetime <= $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                            // パターン２３５
                            $ptn = '2';
                        }
                    } else {
                        // 不明データとして作成する
                        $log_data = $work_time->getParamDatefromAttribute();
                        $log_data .= $work_time->getParamUsercodeAttribute();
                        $log_data .= $work_time->getParamDepartmentcodeAttribute();
                    }
                } elseif ($before_value_mode == Config::get('const.C005.leaving_time') ||
                        $before_value_mode == Config::get('const.C005.emergency_return_time')) {       // １個前のモードが退勤である場合
                    if ($record_before_datetime < $attendance_from_date) {                      // １個前の打刻時刻 < 出勤1日のはじめ
                        // パターン６（打刻ミス（出勤していない）。勤務状態は打刻なし）
                        $ptn = '6';
                    }
                } elseif ($before_value_mode == Config::get('const.C005.missing_middle_time') ||
                    $before_value_mode == Config::get('const.C005.public_going_out_time') ||
                    $before_value_mode == Config::get('const.C005.emergency_time')) {           // １個前のモードが外出である場合
                    if ($record_before_datetime < $attendance_from_date) {                      // １個前の打刻時刻 < 出勤1日のはじめ
                        // パターン７（打刻ミス（外出戻りしていない）。勤務状態は打刻なし）
                        $ptn = '7';
                    }
                } else {                                                                        // １個前のモードがない
                    // パターン６（打刻ミス（出勤していない）。勤務状態は打刻なし）
                    $ptn = '6';
                }
            // ---------------------退勤が２番目以降の場合 -----------------------------------------------------------------------------------
            } else {
                if ($before_value_mode == Config::get('const.C005.attendance_time')) {          // １個前のモードが出勤
                    if ($record_datetime >= $attendance_from_date &&
                        $record_datetime <= $timetable_from_date) {                              // 出勤1日のはじめ <= 打刻時刻 < タイムテーブルの始業時刻
                        if ($record_before_datetime >= $attendance_from_date &&
                            $record_before_datetime < $timetable_from_date) {                   // 出勤1日のはじめ <= １個前の打刻時刻 < タイムテーブルの始業時刻
                            // パターン４（早退（要確認）。勤務状態は退勤状態。当日計算。）
                            $ptn = '4';
                        }
                    } elseif ($record_datetime >= $timetable_from_date &&
                                $record_datetime <= $timetable_to_date) {                        // タイムテーブルの始業時刻 <= 打刻時刻 < タイムテーブルの終業時刻
                        if ($record_before_datetime >= $attendance_from_date &&
                            $record_before_datetime < $timetable_to_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻 < タイムテーブルの終業時刻
                            // パターン４（早退（要確認）。勤務状態は退勤状態。当日計算。）
                            $ptn = '4';
                        }
                    } elseif ($record_datetime >= $timetable_to_date &&
                                $record_datetime <= $attendance_to_date) {                       // タイムテーブルの終業時刻 <= 打刻時刻 < 出勤1日の終わり
                        if ($record_before_datetime >= $attendance_from_date &&
                            $record_before_datetime < $timetable_to_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻 < 出勤1日の終わり
                            // パターン８（勤務状態は退勤状態。当日計算。）
                            $ptn = '8';
                        }
                    } elseif ($record_datetime > $attendance_to_date) {                         // 出勤1日の終わり < 打刻時刻
                        if ($record_before_datetime >= $attendance_from_date &&
                            $record_before_datetime < $timetable_to_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻 < 出勤1日の終わり
                            // パターン８（勤務状態は退勤状態。当日計算。）
                            $ptn = '8';
                        } elseif ($record_before_datetime > $timetable_to_date) {               // １個前の打刻時刻 > 出勤1日の終わり
                            // パターン１（正常退勤。勤務状態は退勤状態。当日時間計算なし。）
                            $ptn = '1';
                        }
                    }
                } elseif ($before_value_mode == Config::get('const.C005.leaving_time') ||
                        $before_value_mode == Config::get('const.C005.emergency_return_time')) {       // １個前のモードが退勤である場合
                    if ($record_before_datetime >= $attendance_from_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻
                        // パターン６（打刻ミス（出勤していない）。勤務状態は打刻なし）
                        $ptn = '6';
                    }
                } elseif ($before_value_mode == Config::get('const.C005.missing_middle_time') ||
                    $before_value_mode == Config::get('const.C005.public_going_out_time') ||
                    $before_value_mode == Config::get('const.C005.emergency_time')) {           // １個前のモードが外出である場合
                    if ($record_before_datetime >= $attendance_from_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻
                        // パターン７（打刻ミス（外出戻りしていない）。勤務状態は打刻なし）
                        $ptn = '7';
                    }
                } elseif ($before_value_mode == Config::get('const.C005.missing_middle_return_time') ||
                    $before_value_mode == Config::get('const.C005.public_going_out_return_time')) {     // １個前のモードが戻り
                    if ($record_datetime >= $attendance_from_date &&
                                $record_datetime <= $timetable_from_date) {                      // 出勤1日のはじめ <= 打刻時刻 < タイムテーブルの始業時刻
                        if ($record_before_datetime >= $attendance_from_date &&
                                    $record_before_datetime < $timetable_from_date) {           // 出勤1日のはじめ <= １個前の打刻時刻 < タイムテーブルの始業時刻
                            // これより前の出勤打刻履歴を調査
                            if ($cnt > 1) {
                                for($i=$cnt-2;$i>=0;$i--){
                                    if ($this->array_working_mode[$i] == Config::get('const.C005.attendance_time')) {     // ２個前モードが出勤
                                        if ($this->array_working_datetime[$i] < $attendance_from_date) {     // ２個前の出勤時刻 < 出勤1日のはじめ
                                            // パターン１（正常退勤。勤務状態は退勤状態。当日時間計算なし。）
                                            $ptn = '1';
                                        } else {
                                            // パターン４（早退（要確認）。勤務状態は退勤状態。当日計算。）
                                            $ptn = '4';
                                        }
                                        break;
                                    }
                                }
                            } else {
                                // パターン１（正常退勤。勤務状態は退勤状態。当日時間計算なし。）
                                $ptn = '1';
                            }
                        }
                    } elseif ($record_datetime >= $timetable_from_date &&
                                    $record_datetime <= $timetable_to_date) {                   // タイムテーブルの始業時刻 <= 打刻時刻 < タイムテーブルの終業時刻
                        if ($record_before_datetime >= $attendance_from_date &&
                                        $record_before_datetime < $timetable_to_date) {        // 出勤1日のはじめ <= １個前の打刻時刻 < タイムテーブルの終業時刻
                            // これより前の出勤打刻履歴を調査
                            if ($cnt > 1) {
                                for($i=$cnt-2;$i>=0;$i--){
                                    if ($this->array_working_mode[$i] == Config::get('const.C005.attendance_time') ||
                                        $this->array_working_mode[$i] == Config::get('const.C005.emergency_time')) {     // ２個前モードが出勤
                                        if ($this->array_working_datetime[$i] < $attendance_from_date) {     // ２個前の出勤時刻 < 出勤1日のはじめ
                                            // パターン２３４
                                            $ptn = '2';
                                        } else {
                                            // パターン２３５
                                            $ptn = '2';
                                        }
                                        break;
                                    }
                                }
                            } else {
                                // パターン２３４
                                $ptn = '2';
                            }
                        }
                    } elseif ($record_datetime >= $timetable_to_date &&
                            $record_datetime <= $attendance_to_date) {                   // タイムテーブルの終業時刻 <= 打刻時刻 < 出勤1日の終わり
                        if ($record_before_datetime >= $attendance_from_date &&
                                        $record_before_datetime < $attendance_to_date) {        // 出勤1日のはじめ <= １個前の打刻時刻 < 出勤1日の終わり
                            // これより前の出勤打刻履歴を調査
                            if ($cnt > 1) {
                                for($i=$cnt-2;$i>=0;$i--){
                                    if ($this->array_working_mode[$i] == Config::get('const.C005.attendance_time') ||
                                        $this->array_working_mode[$i] == Config::get('const.C005.emergency_time')) {     // ２個前モードが出勤
                                        if ($this->array_working_datetime[$i] < $attendance_from_date) {     // ２個前の出勤時刻 < 出勤1日のはじめ
                                            // パターン２３５
                                            $ptn = '2';
                                        } else {
                                            // パターン８（勤務状態は退勤状態。当日計算。）
                                            $ptn = '8';
                                        }
                                        break;
                                    }
                                }
                            } else {
                                // パターン２３５
                                $ptn = '2';
                            }
                        }
                    } elseif ($record_datetime > $attendance_to_date) {
                        // これより前の出勤打刻履歴を調査
                        if ($cnt > 1) {
                            for($i=$cnt-2;$i>=0;$i--){
                                if ($this->array_working_mode[$i] == Config::get('const.C005.attendance_time') ||
                                    $this->array_working_mode[$i] == Config::get('const.C005.emergency_time')) {     // ２個前モードが出勤
                                    if ($this->array_working_datetime[$i] < $attendance_from_date) {     // ２個前の出勤時刻 < 出勤1日のはじめ
                                        // パターン２３５
                                        $ptn = '2';
                                    } else {
                                        // パターン８（勤務状態は退勤状態。当日計算。）
                                        $ptn = '8';
                                    }
                                    break;
                                }
                            }
                        } else {
                            // パターン２３５
                            $ptn = '2';
                        }
                    } else {
                        // 不明データとして作成する
                        $log_data = $work_time->getParamDatefromAttribute();
                        $log_data .= $work_time->getParamUsercodeAttribute();
                        $log_data .= $work_time->getParamDepartmentcodeAttribute();
                    }
                } else {                                                                        // １個前のモードがない
                    // 不明データとして作成する
                    $log_data = $work_time->getParamDatefromAttribute();
                    $log_data .= $work_time->getParamUsercodeAttribute();
                    $log_data .= $work_time->getParamDepartmentcodeAttribute();
                }
            }

            if ($ptn == null) {
                $ptn = '';
            }
            // setLeavingCollectPtn implement
            $array_impl_setLeavingCollectPtn = array (
                'ptn' => $ptn,
                'record_datetime' => $record_datetime,
                'value_record_datetime_id' => $value_record_datetime_id,
                'value_editor_department_code' => $value_editor_department_code,
                'value_editor_department_name' => $value_editor_department_name,
                'value_editor_user_code' => $value_editor_user_code,
                'value_editor_user_name' => $value_editor_user_name,
                'value_check_result' => $value_check_result,
                'value_check_max_times' => $value_check_max_times,
                'value_mobile_positions' => $value_mobile_positions,
                'business_kubun' => $business_kubun,
                'user_holiday_kubun' => $user_holiday_kubun,
                'use_free_item' => $use_free_item,
                'value_working_timetable_no' => $value_working_timetable_no,
                'attendance_time_index' => $attendance_time_index
            );
            $this->pushArrayCalc(
            $this->setLeavingCollectPtn($array_impl_setLeavingCollectPtn));
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.data_error_dailycalc'));
            Log::error($e->getMessage());
            throw $e;
        }
        Log::debug('---------------------- setLeavingtime end ------------------------ ');
            
    }

    /**
     * 退勤打刻時刻テーブル設定
     *
     *        $collect_working_times
     *          'mode': 打刻モード（出勤）
     *          'time': 打刻時刻
     *          'status': 打刻状態
     *          'note': メモ
     *        　'late': 遅刻
     *          'Leave_early':早退
     *          'interval':インターバルチェック
     *          'calc':当日計算
     *          'to_be_confirmed':要確認
     * 
     * @param  打刻時刻
     * @return テーブル
     */
    private function setLeavingCollectPtn($params)
    {
        Log::debug('---------------------- setLeavingCollectPtn in ----------------- ');
        // パラメータ設定
        $ptn = $params['ptn'];
        $record_datetime = $params['record_datetime'];
        $value_record_datetime_id = $params['value_record_datetime_id'];
        $value_editor_department_code = $params['value_editor_department_code'];
        $value_editor_department_name = $params['value_editor_department_name'];
        $value_editor_user_code = $params['value_editor_user_code'];
        $value_editor_user_name = $params['value_editor_user_name'];
        $value_check_result = $params['value_check_result'];
        $value_check_max_times = $params['value_check_max_times'];
        $value_mobile_positions = $params['value_mobile_positions'];
        $business_kubun = $params['business_kubun'];
        $user_holiday_kubun = $params['user_holiday_kubun'];
        $use_free_item = $params['use_free_item'];
        $value_working_timetable_no = $params['value_working_timetable_no'];
        $attendance_time_index = $params['attendance_time_index'];

        $temp_calc_model = new TempCalcWorkingTime();
        if ($ptn == '1') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.leaving'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            if ($attendance_time_index >= 0) {
                $temp_calc_model->setCurrentcalcAttribute($this->array_calc_calc[$attendance_time_index]);
            } else {
                $temp_calc_model->setCurrentcalcAttribute('0');
            }
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } elseif ($ptn == '2') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.leaving'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            if ($attendance_time_index >= 0) {
                $temp_calc_model->setCurrentcalcAttribute($this->array_calc_calc[$attendance_time_index]);
            } else {
                $temp_calc_model->setCurrentcalcAttribute('0');
            }
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } elseif ($ptn == '3') {
            // 自動設定はなしにする
            $temp_calc_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.attendance'));
            $temp_calc_model->setNoteAttribute('');
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            if ($attendance_time_index >= 0) {
                $temp_calc_model->setCurrentcalcAttribute($this->array_calc_calc[$attendance_time_index]);
            } else {
                $temp_calc_model->setCurrentcalcAttribute('0');
            }
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } elseif ($ptn == '4') {
            // なしにする
            $temp_calc_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.leaving'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('0');
            // 遅刻の設定値（休日は"0"）
            if (!isset($business_kubun)) { $business_kubun = Config::get('const.C007.basic'); }
            if (!isset($user_holiday_kubun)) { $user_holiday_kubun = Config::get('const.C013.non_set'); }
            Log::debug('        用途フリー項目 =  '.$use_free_item);
            if ($business_kubun == Config::get('const.C007.basic')) {
                if ((!isset($use_free_item)) ||
                    (isset($use_free_item) &&
                    (substr($use_free_item, Config::get('const.USEFREEITEM.day_holiday'), 1) == "1"))) {
                    $temp_calc_model->setLateAttribute('1');
                } else {
                    $temp_calc_model->setLateAttribute('0');
                }
            } else {
                $temp_calc_model->setLateAttribute('0');
            }
            if ($attendance_time_index >= 0) {
                $temp_calc_model->setCurrentcalcAttribute($this->array_calc_calc[$attendance_time_index]);
            } else {
                $temp_calc_model->setCurrentcalcAttribute('0');
            }
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } elseif ($ptn == '5') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.leaving'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            if ($attendance_time_index >= 0) {
                $temp_calc_model->setCurrentcalcAttribute($this->array_calc_calc[$attendance_time_index]);
            } else {
                $temp_calc_model->setCurrentcalcAttribute('0');
            }
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } elseif ($ptn == '6') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            if ($attendance_time_index >= 0) {
                $temp_calc_model->setCurrentcalcAttribute($this->array_calc_calc[$attendance_time_index]);
            } else {
                $temp_calc_model->setCurrentcalcAttribute('0');
            }
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } elseif ($ptn == '7') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_003'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            if ($attendance_time_index >= 0) {
                $temp_calc_model->setCurrentcalcAttribute($this->array_calc_calc[$attendance_time_index]);
            } else {
                $temp_calc_model->setCurrentcalcAttribute('0');
            }
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } elseif ($ptn == '8') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.leaving'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            if ($attendance_time_index >= 0) {
                $temp_calc_model->setCurrentcalcAttribute($this->array_calc_calc[$attendance_time_index]);
            } else {
                $temp_calc_model->setCurrentcalcAttribute('0');
            }
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } else {
            // 不明データとして作成する
            $temp_calc_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.unknown'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_004'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            if ($attendance_time_index >= 0) {
                $temp_calc_model->setCurrentcalcAttribute($this->array_calc_calc[$attendance_time_index]);
            } else {
                $temp_calc_model->setCurrentcalcAttribute('0');
            }
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        }
            
        Log::debug('---------------------- setLeavingCollectPtn end ------------------------ ');
        return $temp_calc_model;
    }

    /**
     * 私用外出打刻処理
     *
     * @param  打刻時刻のSEQ
     *         打刻時刻
     *         １個前のモード
     * @return チェック結果
     */
    private function setMissingMiddleTime($params)
    {
        Log::debug('---------------------- setMissingMiddleTime in ------------------------ ');
        $cnt = $params['cnt'];
        $work_time = $params['work_time'];
        $value_record_datetime = $params['value_record_datetime'];
        $value_record_datetime_id = $params['value_record_datetime_id'];
        $value_editor_department_code = $params['value_editor_department_code'];
        $value_editor_department_name = $params['value_editor_department_name'];
        $value_editor_user_code = $params['value_editor_user_code'];
        $value_editor_user_name = $params['value_editor_user_name'];
        $value_timetable_from_time = $params['value_timetable_from_time'];
        $value_timetable_to_time = $params['value_timetable_to_time'];
        $value_check_result = $params['value_check_result'];
        $value_check_max_times = $params['value_check_max_times'];
        $value_mobile_positions = $params['value_mobile_positions'];
        $before_value_mode = $params['before_value_mode'];
        $before_value_datetime = $params['before_value_datetime'];
        $business_kubun = $params['business_kubun'];
        $user_holiday_kubun = $params['user_holiday_kubun'];
        $use_free_item = $params['use_free_item'];
        $value_working_timetable_no = $params['value_working_timetable_no'];
        $attendance_time_index = $params['attendance_time_index'];

        $apicommon = new ApiCommonController();
        $attendance_from_date = new Carbon($work_time->getParamDatefromAttribute());        // 出勤1日のはじめ
        $attendance_from_date_format = date_format($attendance_from_date, 'Y/m/d');
        $timetable_from_date = new Carbon(
            $attendance_from_date_format.' '.$value_timetable_from_time);                   // タイムテーブルの始業時刻
        if ($value_timetable_from_time <= $value_timetable_to_time) {
            $timetable_to_date = new Carbon(
                $attendance_from_date_format.' '.$value_timetable_to_time);                 // タイムテーブルの終業時刻
        } else {
            // 翌日
            $nextdt =$apicommon->getNextDay($attendance_from_date_format, 'Y/m/d');
            $timetable_to_date = new Carbon(
                    $nextdt.' '.$value_timetable_to_time);                                  // タイムテーブルの終業時刻
        }
        $attendance_to_date = new Carbon(
            $attendance_from_date_format.' 23:59:59');                                      // 出勤1日の終わり
        $record_datetime = new Carbon($value_record_datetime);                              // 打刻日付時刻
        $record_before_datetime = new Carbon($before_value_datetime);                       // １個前の打刻時刻
        Log::debug('            出勤1日のはじめ attendance_from_date  = '.$attendance_from_date);
        Log::debug('            タイムテーブルの始業時刻 timetable_from_date  = '.$timetable_from_date);
        Log::debug('            タイムテーブルの終業時刻 timetable_to_date  = '.$timetable_to_date);
        Log::debug('            出勤1日の終わり attendance_to_date  = '.$attendance_to_date);
        Log::debug('            打刻日付時刻 record_datetime  = '.$record_datetime);
        Log::debug('                        record_before_datetime = '.$record_before_datetime);
        Log::debug('            1個前のモード before_value_mode = '.$before_value_mode);
        Log::debug('            1個前の打刻   before_value_datetime = '.$before_value_datetime);
        Log::debug('            cnt  = '.$cnt);
        // パターン設定
        $ptn = null;

        // ---------------------私用外出が最初の場合 -----------------------------------------------------------------------------------
        if ($cnt == 0) {
            if ($before_value_mode == Config::get('const.C005.attendance_time') ||
                $before_value_mode == Config::get('const.C005.missing_middle_return_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_return_time') ||
                $before_value_mode == Config::get('const.C005.emergency_time')) {           // １個前のモードが出勤または戻りである場合
                if ($record_datetime >= $attendance_from_date &&
                            $record_datetime < $attendance_to_date) {                       // 出勤1日のはじめ <= 打刻時刻 < 出勤1日の終わり
                    if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                        // パターン１（私用外出。勤務状態は私用外出状態。）
                        $ptn = '1';
                    }
                }
            } elseif ($before_value_mode == Config::get('const.C005.leaving_time') ||
                    $before_value_mode == Config::get('const.C005.emergency_return_time')) {       // １個前のモードが退勤である場合
                if ($record_before_datetime < $attendance_from_date) {                      // １個前の打刻時刻 < 出勤1日のはじめ
                    // パターン２（打刻ミス（出勤していない）。勤務状態は打刻なし）
                    $ptn = '2';
                }
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_time')) {    // １個前のモードが私用外出である場合
                if ($record_before_datetime < $attendance_from_date) {                      // １個前の打刻時刻 < 出勤1日のはじめ
                    // パターン３（打刻ミス（私用外出戻りしていない）。勤務状態は打刻なし）
                    $ptn = '3';
                }
            } else {                                                                        // １個前のモードがない
                // 不明データとして作成する
                $log_data = $work_time->getParamDatefromAttribute();
                $log_data .= $work_time->getParamUsercodeAttribute();
                $log_data .= $work_time->getParamDepartmentcodeAttribute();
            }
        // ---------------------私用外出が２番目以降の場合 -----------------------------------------------------------------------------------
        } else {
            if ($before_value_mode == Config::get('const.C005.attendance_time') ||
                $before_value_mode == Config::get('const.C005.missing_middle_return_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_return_time') ||
                $before_value_mode == Config::get('const.C005.emergency_time')) {           // １個前のモードが出勤または戻りである場合
                if ($record_datetime >= $attendance_from_date &&
                    $record_datetime < $timetable_from_date) {                              // 出勤1日のはじめ <= 打刻時刻 < タイムテーブルの始業時刻
                    if ($record_before_datetime >= $attendance_from_date &&
                        $record_before_datetime < $timetable_from_date) {                   // 出勤1日のはじめ <= １個前の打刻時刻 < タイムテーブルの始業時刻
                        // パターン１（正常私用外出。勤務状態は私用外出状態。）
                        $ptn = '1';
                    }
                } elseif ($record_datetime >= $timetable_from_date &&
                            $record_datetime < $timetable_to_date) {                        // タイムテーブルの始業時刻 <= 打刻時刻 < タイムテーブルの終業時刻
                    if ($record_before_datetime >= $attendance_from_date &&
                        $record_before_datetime < $timetable_to_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻 < タイムテーブルの終業時刻
                        // パターン１（正常私用外出。勤務状態は私用外出状態。）
                        $ptn = '1';
                    }
                } elseif ($record_datetime >= $timetable_to_date &&
                            $record_datetime < $attendance_to_date) {                       // タイムテーブルの終業時刻 <= 打刻時刻 < 出勤1日の終わり
                    if ($record_before_datetime >= $attendance_from_date &&
                        $record_before_datetime < $timetable_to_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻 < 出勤1日の終わり
                        // パターン１（正常私用外出。勤務状態は私用外出状態。）
                        $ptn = '1';
                    }
                } elseif ($record_datetime > $timetable_to_date) {                          // 打刻時刻 > 出勤1日の終わり
                    if ($record_before_datetime >= $attendance_from_date &&
                        $record_before_datetime < $timetable_to_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻 < 出勤1日の終わり
                        // パターン１（正常私用外出。勤務状態は私用外出状態。）
                        $ptn = '1';
                    }
                } else {                                                                    // １個前のモードがない
                    // 不明データとして作成する
                    $log_data = $work_time->getParamDatefromAttribute();
                    $log_data .= $work_time->getParamUsercodeAttribute();
                    $log_data .= $work_time->getParamDepartmentcodeAttribute();
                }
            } elseif ($before_value_mode == Config::get('const.C005.leaving_time') ||
                    $before_value_mode == Config::get('const.C005.emergency_return_time')) {       // １個前のモードが退勤である場合
                if ($record_before_datetime >= $attendance_from_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻
                    // パターン２（打刻ミス（出勤していない）。勤務状態は打刻なし）
                    $ptn = '2';
                }
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_time')) {    // １個前のモードが私用外出
                if ($record_before_datetime >= $attendance_from_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻
                    // 不明データとして作成する
                    $ptn = '';
                }
            } else {                                                                        // １個前のモードがない
                // 不明データとして作成する
                $log_data = $work_time->getParamDatefromAttribute();
                $log_data .= $work_time->getParamUsercodeAttribute();
                $log_data .= $work_time->getParamDepartmentcodeAttribute();
                Log::error(Config::get('const.MSG_ERROR.mismatch_data').' method = setMissingMiddleTime '.$log_data);
                Log::error('私用外出が２番目以降 setMissingMiddleTime before_value_mode = '.$before_value_mode);
            }
        }

        if ($ptn == null) {
            $ptn = '';
        }
        // setMissingmiddleCollectPtn implement
        $array_impl_setMissingmiddleCollectPtn = array (
            'ptn' => $ptn,
            'record_datetime' => $record_datetime,
            'value_record_datetime_id' => $value_record_datetime_id,
            'value_editor_department_code' => $value_editor_department_code,
            'value_editor_department_name' => $value_editor_department_name,
            'value_editor_user_code' => $value_editor_user_code,
            'value_editor_user_name' => $value_editor_user_name,
            'value_check_result' => $value_check_result,
            'value_check_max_times' => $value_check_max_times,
            'value_mobile_positions' => $value_mobile_positions,
            'business_kubun' => $business_kubun,
            'user_holiday_kubun' => $user_holiday_kubun,
            'use_free_item' => $use_free_item,
            'value_working_timetable_no' => $value_working_timetable_no,
            'attendance_time_index' => $attendance_time_index
        );
        $this->pushArrayCalc($this->setMissingmiddleCollectPtn($array_impl_setMissingmiddleCollectPtn));
        Log::debug('---------------------- setMissingMiddleTime end ------------------------ ');
            
    }

    /**
     * 私用外出打刻時刻テーブル設定
     *
     *        $collect_working_times
     *          'mode': 打刻モード（私用外出）
     *          'time': 打刻時刻
     *          'status': 打刻状態
     *          'note': メモ
     *        　'late': 遅刻
     *          'Leave_early':早退
     *          'interval':インターバルチェック
     *          'calc':当日計算
     *          'to_be_confirmed':要確認
     * 
     * @param  打刻時刻
     * @return テーブル
     */
    private function setMissingmiddleCollectPtn($params)
    {
        Log::debug('---------------------- setMissingmiddleCollectPtn in ------------------------ ');
        // パラメータ設定
        $ptn = $params['ptn'];
        $record_datetime = $params['record_datetime'];
        $value_record_datetime_id = $params['value_record_datetime_id'];
        $value_editor_department_code = $params['value_editor_department_code'];
        $value_editor_department_name = $params['value_editor_department_name'];
        $value_editor_user_code = $params['value_editor_user_code'];
        $value_editor_user_name = $params['value_editor_user_name'];
        $value_check_result = $params['value_check_result'];
        $value_check_max_times = $params['value_check_max_times'];
        $value_mobile_positions = $params['value_mobile_positions'];
        $business_kubun = $params['business_kubun'];
        $user_holiday_kubun = $params['user_holiday_kubun'];
        $use_free_item = $params['use_free_item'];
        $value_working_timetable_no = $params['value_working_timetable_no'];
        $attendance_time_index = $params['attendance_time_index'];

        $temp_calc_model = new TempCalcWorkingTime();

        if ($ptn == '1') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.missing_middle_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.missing_middle'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            if ($attendance_time_index >= 0) {
                $temp_calc_model->setCurrentcalcAttribute($this->array_calc_calc[$attendance_time_index]);
            } else {
                $temp_calc_model->setCurrentcalcAttribute('0');
            }
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } elseif ($ptn == '2') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.missing_middle_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_008'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            if ($attendance_time_index >= 0) {
                $temp_calc_model->setCurrentcalcAttribute($this->array_calc_calc[$attendance_time_index]);
            } else {
                $temp_calc_model->setCurrentcalcAttribute('0');
            }
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } elseif ($ptn == '3') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.missing_middle_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_003'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            if ($attendance_time_index >= 0) {
                $temp_calc_model->setCurrentcalcAttribute($this->array_calc_calc[$attendance_time_index]);
            } else {
                $temp_calc_model->setCurrentcalcAttribute('0');
            }
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } else {
            // 不明データとして作成する
            $temp_calc_model->setModeAttribute(Config::get('const.C005.missing_middle_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.unknown'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_004'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            if ($attendance_time_index >= 0) {
                $temp_calc_model->setCurrentcalcAttribute($this->array_calc_calc[$attendance_time_index]);
            } else {
                $temp_calc_model->setCurrentcalcAttribute('0');
            }
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        }

        Log::debug('---------------------- setMissingmiddleCollectPtn end ------------------------ ');
        return $temp_calc_model;
            
    }

    /**
     * 私用外出戻り打刻処理
     *
     * @param  打刻時刻のSEQ
     *         打刻時刻
     *         １個前のモード
     * @return チェック結果
     */
    private function setMissingMiddleReturnTime($params)
    {
        Log::debug('---------------------- setMissingMiddleReturnTime in ------------------------ ');
        $cnt = $params['cnt'];
        $work_time = $params['work_time'];
        $value_record_datetime = $params['value_record_datetime'];
        $value_record_datetime_id = $params['value_record_datetime_id'];
        $value_editor_department_code = $params['value_editor_department_code'];
        $value_editor_department_name = $params['value_editor_department_name'];
        $value_editor_user_code = $params['value_editor_user_code'];
        $value_editor_user_name = $params['value_editor_user_name'];
        $value_timetable_from_time = $params['value_timetable_from_time'];
        $value_timetable_to_time = $params['value_timetable_to_time'];
        $value_check_result = $params['value_check_result'];
        $value_check_max_times = $params['value_check_max_times'];
        $value_mobile_positions = $params['value_mobile_positions'];
        $before_value_mode = $params['before_value_mode'];
        $before_value_datetime = $params['before_value_datetime'];
        $business_kubun = $params['business_kubun'];
        $user_holiday_kubun = $params['user_holiday_kubun'];
        $use_free_item = $params['use_free_item'];
        $value_working_timetable_no = $params['value_working_timetable_no'];
        $attendance_time_index = $params['attendance_time_index'];

        $apicommon = new ApiCommonController();
        $attendance_from_date = new Carbon($work_time->getParamDatefromAttribute());        // 出勤1日のはじめ
        $attendance_from_date_format = date_format($attendance_from_date, 'Y/m/d');
        $timetable_from_date = new Carbon(
            $attendance_from_date_format.' '.$value_timetable_from_time);                   // タイムテーブルの始業時刻
        if ($value_timetable_from_time <= $value_timetable_to_time) {
            $timetable_to_date = new Carbon(
                $attendance_from_date_format.' '.$value_timetable_to_time);                 // タイムテーブルの終業時刻
        } else {
            // 翌日
            $nextdt =$apicommon->getNextDay($attendance_from_date_format, 'Y/m/d');
            $timetable_to_date = new Carbon(
                    $nextdt.' '.$value_timetable_to_time);                                  // タイムテーブルの終業時刻
        }
        $attendance_to_date = new Carbon(
            $attendance_from_date_format.' 23:59:59');                                      // 出勤1日の終わり
        $record_datetime = new Carbon($value_record_datetime);                              // 打刻日付時刻
        $record_before_datetime = new Carbon($before_value_datetime);                       // １個前の打刻時刻
        Log::debug('            出勤1日のはじめ attendance_from_date  = '.$attendance_from_date);
        Log::debug('            タイムテーブルの始業時刻 timetable_from_date  = '.$timetable_from_date);
        Log::debug('            タイムテーブルの終業時刻 timetable_to_date  = '.$timetable_to_date);
        Log::debug('            出勤1日の終わり attendance_to_date  = '.$attendance_to_date);
        Log::debug('            打刻日付時刻 record_datetime  = '.$record_datetime);
        Log::debug('                        record_before_datetime = '.$record_before_datetime);
        Log::debug('            1個前のモード before_value_mode = '.$before_value_mode);
        Log::debug('            1個前の打刻   before_value_datetime = '.$before_value_datetime);
        Log::debug('            cnt  = '.$cnt);
        // パターン設定
        $ptn = null;

        // ---------------------私用外出戻りが最初の場合 -----------------------------------------------------------------------------------
        if ($cnt == 0) {
            if ($before_value_mode == Config::get('const.C005.attendance_time') ||
                $before_value_mode == Config::get('const.C005.leaving_time') ||
                $before_value_mode == Config::get('const.C005.missing_middle_return_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_return_time') ||
                $before_value_mode == Config::get('const.C005.emergency_time') ||
                $before_value_mode == Config::get('const.C005.emergency_return_time')) {     // １個前のモードが出勤または退勤または私用外出戻りである場合
                if ($record_datetime >= $attendance_from_date &&
                            $record_datetime < $attendance_to_date) {                       // 出勤1日のはじめ <= 打刻時刻 < 出勤1日の終わり
                    if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                        // パターン１（打刻ミス（私用外出していない）。勤務状態は打刻なし）
                        $ptn = '1';
                    }
                }
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_time')) {    // １個前のモードが私用外出である場合
                if ($record_before_datetime < $attendance_from_date) {                      // １個前の打刻時刻 < 出勤1日のはじめ
                    // パターン２（正常戻り。勤務状態は戻り状態。）
                    $ptn = '2';
                }
            } else {                                                                        // １個前のモードがない
                // 不明データとして作成する
                $log_data = $work_time->getParamDatefromAttribute();
                $log_data .= $work_time->getParamUsercodeAttribute();
                $log_data .= $work_time->getParamDepartmentcodeAttribute();
            }
        // ---------------------私用外出戻りが２番目以降の場合 -----------------------------------------------------------------------------------
        } else {
            if ($before_value_mode == Config::get('const.C005.attendance_time') ||
                $before_value_mode == Config::get('const.C005.leaving_time') ||
                $before_value_mode == Config::get('const.C005.missing_middle_return_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_return_time') ||
                $before_value_mode == Config::get('const.C005.emergency_time') ||
                $before_value_mode == Config::get('const.C005.emergency_return_time')) {     // １個前のモードが出勤または退勤または私用外出戻りである場合
                if ($record_datetime >= $attendance_from_date) {                            // 出勤1日のはじめ <= 打刻時刻
                    if ($record_before_datetime >= $attendance_from_date) {                 // 出勤1日のはじめ <= １個前の打刻時刻
                        // パターン１（打刻ミス（私用外出していない）。勤務状態は打刻なし）
                        $ptn = '1';
                    }
                }
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_time')) {    // １個前のモードが私用外出
                if ($record_datetime >= $attendance_from_date &&
                    $record_datetime < $timetable_from_date) {                              // 出勤1日のはじめ <= 打刻時刻 < タイムテーブルの始業時刻
                    if ($record_before_datetime >= $attendance_from_date &&
                        $record_before_datetime < $timetable_from_date) {                   // 出勤1日のはじめ <= １個前の打刻時刻 < タイムテーブルの始業時刻
                        // パターン２（正常戻り。勤務状態は戻り状態。）
                        $ptn = '2';
                    }
                } elseif ($record_datetime >= $timetable_from_date &&
                            $record_datetime < $timetable_to_date) {                        // タイムテーブルの始業時刻 <= 打刻時刻 < タイムテーブルの終業時刻
                    if ($record_before_datetime >= $attendance_from_date &&
                        $record_before_datetime < $timetable_to_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻 < タイムテーブルの終業時刻
                        // パターン２（正常戻り。勤務状態は戻り状態。）
                        $ptn = '2';
                    }
                } elseif ($record_datetime >= $timetable_to_date &&
                            $record_datetime < $attendance_to_date) {                       // タイムテーブルの終業時刻 <= 打刻時刻 < 出勤1日の終わり
                    if ($record_before_datetime >= $attendance_from_date &&
                        $record_before_datetime < $timetable_to_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻 < 出勤1日の終わり
                        // パターン２（正常戻り。勤務状態は戻り状態。）
                        $ptn = '2';
                    }
                } elseif ($record_datetime > $timetable_to_date) {                          // 打刻時刻 > 出勤1日の終わり
                    if ($record_before_datetime >= $attendance_from_date) {                 // 出勤1日のはじめ <= １個前の打刻時刻
                        // パターン２（正常戻り。勤務状態は戻り状態。）
                        $ptn = '2';
                    }
                } else {                                                                    // １個前のモードがない
                    // 不明データとして作成する
                    $log_data = $work_time->getParamDatefromAttribute();
                    $log_data .= $work_time->getParamUsercodeAttribute();
                    $log_data .= $work_time->getParamDepartmentcodeAttribute();
                }
            } else {                                                                        // １個前のモードがない
                // 不明データとして作成する
                $log_data = $work_time->getParamDatefromAttribute();
                $log_data .= $work_time->getParamUsercodeAttribute();
                $log_data .= $work_time->getParamDepartmentcodeAttribute();
            }
        }

        if ($ptn == null) {
            $ptn = '';
        }
        // setMissingmiddleCollectPtn implement
        $array_impl_setMissingmiddleReturnCollectPtn = array (
            'ptn' => $ptn,
            'record_datetime' => $record_datetime,
            'value_record_datetime_id' => $value_record_datetime_id,
            'value_editor_department_code' => $value_editor_department_code,
            'value_editor_department_name' => $value_editor_department_name,
            'value_editor_user_code' => $value_editor_user_code,
            'value_editor_user_name' => $value_editor_user_name,
            'value_check_result' => $value_check_result,
            'value_check_max_times' => $value_check_max_times,
            'value_mobile_positions' => $value_mobile_positions,
            'business_kubun' => $business_kubun,
            'user_holiday_kubun' => $user_holiday_kubun,
            'use_free_item' => $use_free_item,
            'value_working_timetable_no' => $value_working_timetable_no,
            'attendance_time_index' => $attendance_time_index
        );
        $this->pushArrayCalc($this->setMissingmiddleReturnCollectPtn($array_impl_setMissingmiddleReturnCollectPtn));
        Log::debug('---------------------- setMissingMiddleReturnTime end ------------------------ ');
            
    }

    /**
     * 私用外出戻り打刻時刻テーブル設定
     *
     *        $collect_working_times
     *          'mode': 打刻モード（私用外出戻り）
     *          'time': 打刻時刻
     *          'status': 打刻状態
     *          'note': メモ
     *        　'late': 遅刻
     *          'Leave_early':早退
     *          'interval':インターバルチェック
     *          'calc':当日計算
     *          'to_be_confirmed':要確認
     * 
     * @param  打刻時刻
     * @return テーブル
     */
    private function setMissingmiddleReturnCollectPtn($params)
    {
        Log::debug('---------------------- setMissingmiddleReturnCollectPtn in ------------------------ ');
        // パラメータ設定
        $ptn = $params['ptn'];
        $record_datetime = $params['record_datetime'];
        $value_record_datetime_id = $params['value_record_datetime_id'];
        $value_editor_department_code = $params['value_editor_department_code'];
        $value_editor_department_name = $params['value_editor_department_name'];
        $value_editor_user_code = $params['value_editor_user_code'];
        $value_editor_user_name = $params['value_editor_user_name'];
        $value_check_result = $params['value_check_result'];
        $value_check_max_times = $params['value_check_max_times'];
        $value_mobile_positions = $params['value_mobile_positions'];
        $business_kubun = $params['business_kubun'];
        $user_holiday_kubun = $params['user_holiday_kubun'];
        $use_free_item = $params['use_free_item'];
        $value_working_timetable_no = $params['value_working_timetable_no'];
        $attendance_time_index = $params['attendance_time_index'];

        $temp_calc_model = new TempCalcWorkingTime();

        if ($ptn == '1') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.missing_middle_return_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_009'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            if ($attendance_time_index >= 0) {
                $temp_calc_model->setCurrentcalcAttribute($this->array_calc_calc[$attendance_time_index]);
            } else {
                $temp_calc_model->setCurrentcalcAttribute('0');
            }
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } elseif ($ptn == '2') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.missing_middle_return_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.missing_return'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            if ($attendance_time_index >= 0) {
                $temp_calc_model->setCurrentcalcAttribute($this->array_calc_calc[$attendance_time_index]);
            } else {
                $temp_calc_model->setCurrentcalcAttribute('0');
            }
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } else {
            // 不明データとして作成する
            $temp_calc_model->setModeAttribute(Config::get('const.C005.missing_middle_return_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.unknown'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_004'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            if ($attendance_time_index >= 0) {
                $temp_calc_model->setCurrentcalcAttribute($this->array_calc_calc[$attendance_time_index]);
            } else {
                $temp_calc_model->setCurrentcalcAttribute('0');
            }
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        }

        Log::debug('---------------------- setMissingmiddleReturnCollectPtn end ------------------------ ');
        return $temp_calc_model;
            
    }

    /**
     * 公用外出打刻処理
     *
     * @param  打刻時刻のSEQ
     *         打刻時刻
     *         １個前のモード
     * @return チェック結果
     */
    private function setPubliGoingOutTime($params)
    {
        Log::debug('---------------------- setPubliGoingOutTime in ------------------------ ');
        $cnt = $params['cnt'];
        $work_time = $params['work_time'];
        $value_record_datetime = $params['value_record_datetime'];
        $value_record_datetime_id = $params['value_record_datetime_id'];
        $value_editor_department_code = $params['value_editor_department_code'];
        $value_editor_department_name = $params['value_editor_department_name'];
        $value_editor_user_code = $params['value_editor_user_code'];
        $value_editor_user_name = $params['value_editor_user_name'];
        $value_timetable_from_time = $params['value_timetable_from_time'];
        $value_timetable_to_time = $params['value_timetable_to_time'];
        $value_check_result = $params['value_check_result'];
        $value_check_max_times = $params['value_check_max_times'];
        $value_mobile_positions = $params['value_mobile_positions'];
        $before_value_mode = $params['before_value_mode'];
        $before_value_datetime = $params['before_value_datetime'];
        $business_kubun = $params['business_kubun'];
        $user_holiday_kubun = $params['user_holiday_kubun'];
        $use_free_item = $params['use_free_item'];
        $value_working_timetable_no = $params['value_working_timetable_no'];
        $attendance_time_index = $params['attendance_time_index'];

        $apicommon = new ApiCommonController();
        $attendance_from_date = new Carbon($work_time->getParamDatefromAttribute());        // 出勤1日のはじめ
        $attendance_from_date_format = date_format($attendance_from_date, 'Y/m/d');
        $timetable_from_date = new Carbon(
            $attendance_from_date_format.' '.$value_timetable_from_time);                   // タイムテーブルの始業時刻
        if ($value_timetable_from_time <= $value_timetable_to_time) {
            $timetable_to_date = new Carbon(
                $attendance_from_date_format.' '.$value_timetable_to_time);                 // タイムテーブルの終業時刻
        } else {
            // 翌日
            $nextdt =$apicommon->getNextDay($attendance_from_date_format, 'Y/m/d');
            $timetable_to_date = new Carbon(
                    $nextdt.' '.$value_timetable_to_time);                                  // タイムテーブルの終業時刻
        }
        $attendance_to_date = new Carbon(
            $attendance_from_date_format.' 23:59:59');                                      // 出勤1日の終わり
        $record_datetime = new Carbon($value_record_datetime);                              // 打刻日付時刻
        $record_before_datetime = new Carbon($before_value_datetime);                       // １個前の打刻時刻
        Log::debug('            出勤1日のはじめ attendance_from_date  = '.$attendance_from_date);
        Log::debug('            タイムテーブルの始業時刻 timetable_from_date  = '.$timetable_from_date);
        Log::debug('            タイムテーブルの終業時刻 timetable_to_date  = '.$timetable_to_date);
        Log::debug('            出勤1日の終わり attendance_to_date  = '.$attendance_to_date);
        Log::debug('            打刻日付時刻 record_datetime  = '.$record_datetime);
        Log::debug('                        record_before_datetime = '.$record_before_datetime);
        Log::debug('            1個前のモード before_value_mode = '.$before_value_mode);
        Log::debug('            1個前の打刻   before_value_datetime = '.$before_value_datetime);
        Log::debug('            cnt  = '.$cnt);
        // パターン設定
        $ptn = null;

        // ---------------------公用外出が最初の場合 -----------------------------------------------------------------------------------
        if ($cnt == 0) {
            if ($before_value_mode == Config::get('const.C005.attendance_time') ||
                $before_value_mode == Config::get('const.C005.missing_middle_return_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_return_time') ||
                $before_value_mode == Config::get('const.C005.emergency_time')) {           // １個前のモードが出勤または戻りである場合
                if ($record_datetime >= $attendance_from_date &&
                            $record_datetime < $attendance_to_date) {                       // 出勤1日のはじめ <= 打刻時刻 < 出勤1日の終わり
                    if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                        // パターン１（公用外出。勤務状態は公用外出状態。）
                        $ptn = '1';
                    }
                }
            } elseif ($before_value_mode == Config::get('const.C005.leaving_time') ||
                    $before_value_mode == Config::get('const.C005.emergency_return_time')) {       // １個前のモードが退勤である場合
                if ($record_before_datetime < $attendance_from_date) {                      // １個前の打刻時刻 < 出勤1日のはじめ
                    // パターン２（打刻ミス（出勤していない）。勤務状態は打刻なし）
                    $ptn = '2';
                }
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_time')) {    // １個前のモードが外出である場合
                if ($record_before_datetime < $attendance_from_date) {                      // １個前の打刻時刻 < 出勤1日のはじめ
                    // パターン３（打刻ミス（公用外出戻りしていない）。勤務状態は打刻なし）
                    $ptn = '3';
                }
            } else {                                                                        // １個前のモードがない
                // 不明データとして作成する
                $log_data = $work_time->getParamDatefromAttribute();
                $log_data .= $work_time->getParamUsercodeAttribute();
                $log_data .= $work_time->getParamDepartmentcodeAttribute();
            }
        // ---------------------公用外出が２番目以降の場合 -----------------------------------------------------------------------------------
        } else {
            if ($before_value_mode == Config::get('const.C005.attendance_time') ||
                $before_value_mode == Config::get('const.C005.missing_middle_return_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_return_time') ||
                $before_value_mode == Config::get('const.C005.emergency_time')) {           // １個前のモードが出勤または戻りである場合
                if ($record_datetime >= $attendance_from_date &&
                    $record_datetime < $timetable_from_date) {                              // 出勤1日のはじめ <= 打刻時刻 < タイムテーブルの始業時刻
                    if ($record_before_datetime >= $attendance_from_date &&
                        $record_before_datetime < $timetable_from_date) {                   // 出勤1日のはじめ <= １個前の打刻時刻 < タイムテーブルの始業時刻
                        // パターン１（正常公用外出。勤務状態は公用外出状態。）
                        $ptn = '1';
                    }
                } elseif ($record_datetime >= $timetable_from_date &&
                            $record_datetime < $timetable_to_date) {                        // タイムテーブルの始業時刻 <= 打刻時刻 < タイムテーブルの終業時刻
                    if ($record_before_datetime >= $attendance_from_date &&
                        $record_before_datetime < $timetable_to_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻 < タイムテーブルの終業時刻
                        // パターン１（正常公用外出。勤務状態は公用外出状態。）
                        $ptn = '1';
                    }
                } elseif ($record_datetime >= $timetable_to_date &&
                            $record_datetime < $attendance_to_date) {                       // タイムテーブルの終業時刻 <= 打刻時刻 < 出勤1日の終わり
                    if ($record_before_datetime >= $attendance_from_date &&
                        $record_before_datetime < $timetable_to_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻 < 出勤1日の終わり
                        // パターン１（正常公用外出。勤務状態は公用外出状態。）
                        $ptn = '1';
                    }
                } elseif ($record_datetime > $timetable_to_date) {                          // 打刻時刻 > 出勤1日の終わり
                    if ($record_before_datetime >= $attendance_from_date &&
                        $record_before_datetime < $timetable_to_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻 < 出勤1日の終わり
                        // パターン１（正常公用外出。勤務状態は公用外出状態。）
                        $ptn = '1';
                    }
                } else {                                                                    // １個前のモードがない
                    // 不明データとして作成する
                    $log_data = $work_time->getParamDatefromAttribute();
                    $log_data .= $work_time->getParamUsercodeAttribute();
                    $log_data .= $work_time->getParamDepartmentcodeAttribute();
                }
            } elseif ($before_value_mode == Config::get('const.C005.leaving_time') ||
                    $before_value_mode == Config::get('const.C005.emergency_return_time')) {       // １個前のモードが退勤である場合
                if ($record_before_datetime >= $attendance_from_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻
                    // パターン２（打刻ミス（出勤していない）。勤務状態は打刻なし）
                    $ptn = '2';
                }
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_time')) {    // １個前のモードが私用外出
                if ($record_before_datetime >= $attendance_from_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻
                    // 不明データとして作成する
                    $ptn = '';
                }
            } else {                                                                        // １個前のモードがない
                // 不明データとして作成する
                $log_data = $work_time->getParamDatefromAttribute();
                $log_data .= $work_time->getParamUsercodeAttribute();
                $log_data .= $work_time->getParamDepartmentcodeAttribute();
            }
        }
        if ($ptn == null) {
            $ptn = '';
        }
        // setPublicGoingOutCollectPtn implement
        $array_impl_setPublicGoingOutCollectPtn = array (
            'ptn' => $ptn,
            'record_datetime' => $record_datetime,
            'value_record_datetime_id' => $value_record_datetime_id,
            'value_editor_department_code' => $value_editor_department_code,
            'value_editor_department_name' => $value_editor_department_name,
            'value_editor_user_code' => $value_editor_user_code,
            'value_editor_user_name' => $value_editor_user_name,
            'value_check_result' => $value_check_result,
            'value_check_max_times' => $value_check_max_times,
            'value_mobile_positions' => $value_mobile_positions,
            'business_kubun' => $business_kubun,
            'user_holiday_kubun' => $user_holiday_kubun,
            'use_free_item' => $use_free_item,
            'value_working_timetable_no' => $value_working_timetable_no,
            'attendance_time_index' => $attendance_time_index
        );
        $this->pushArrayCalc($this->setPublicGoingOutCollectPtn($array_impl_setPublicGoingOutCollectPtn));

        Log::debug('---------------------- setPubliGoingOutTime end ------------------------ ');
        Log::debug('        公用外出打刻処理 end');
            
    }

    /**
     * 公用外出打刻時刻テーブル設定
     *
     *        $collect_working_times
     *          'mode': 打刻モード（私用外出）
     *          'time': 打刻時刻
     *          'status': 打刻状態
     *          'note': メモ
     *        　'late': 遅刻
     *          'Leave_early':早退
     *          'interval':インターバルチェック
     *          'calc':当日計算
     *          'to_be_confirmed':要確認
     * 
     * @param  打刻時刻
     * @return テーブル
     */
    private function setPublicGoingOutCollectPtn($params)
    {
        Log::debug('---------------------- setPublicGoingOutCollectPtn in ------------------------ ');
        // パラメータ設定
        $ptn = $params['ptn'];
        $record_datetime = $params['record_datetime'];
        $value_record_datetime_id = $params['value_record_datetime_id'];
        $value_editor_department_code = $params['value_editor_department_code'];
        $value_editor_department_name = $params['value_editor_department_name'];
        $value_editor_user_code = $params['value_editor_user_code'];
        $value_editor_user_name = $params['value_editor_user_name'];
        $value_check_result = $params['value_check_result'];
        $value_check_max_times = $params['value_check_max_times'];
        $value_mobile_positions = $params['value_mobile_positions'];
        $business_kubun = $params['business_kubun'];
        $user_holiday_kubun = $params['user_holiday_kubun'];
        $use_free_item = $params['use_free_item'];
        $value_working_timetable_no = $params['value_working_timetable_no'];
        $attendance_time_index = $params['attendance_time_index'];

        $temp_calc_model = new TempCalcWorkingTime();

        if ($ptn == '1') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.public_going_out_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.public_going_out'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            if ($attendance_time_index >= 0) {
                $temp_calc_model->setCurrentcalcAttribute($this->array_calc_calc[$attendance_time_index]);
            } else {
                $temp_calc_model->setCurrentcalcAttribute('0');
            }
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } elseif ($ptn == '2') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.public_going_out_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_008'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            if ($attendance_time_index >= 0) {
                $temp_calc_model->setCurrentcalcAttribute($this->array_calc_calc[$attendance_time_index]);
            } else {
                $temp_calc_model->setCurrentcalcAttribute('0');
            }
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } elseif ($ptn == '3') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.public_going_out_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_003'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            if ($attendance_time_index >= 0) {
                $temp_calc_model->setCurrentcalcAttribute($this->array_calc_calc[$attendance_time_index]);
            } else {
                $temp_calc_model->setCurrentcalcAttribute('0');
            }
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } else {
            // 不明データとして作成する
            $temp_calc_model->setModeAttribute(Config::get('const.C005.public_going_out_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.unknown'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_004'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            if ($attendance_time_index >= 0) {
                $temp_calc_model->setCurrentcalcAttribute($this->array_calc_calc[$attendance_time_index]);
            } else {
                $temp_calc_model->setCurrentcalcAttribute('0');
            }
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        }

        Log::debug('---------------------- setPublicGoingOutCollectPtn end ------------------------ ');
        return $temp_calc_model;
            
    }

    /**
     * 公用外出戻り打刻処理
     *
     * @param  打刻時刻のSEQ
     *         打刻時刻
     *         １個前のモード
     * @return チェック結果
     */
    private function setPublicGoingOutReturnTime($params)
    {
        Log::debug('---------------------- setPublicGoingOutReturnTime in ------------------------ ');
        $cnt = $params['cnt'];
        $work_time = $params['work_time'];
        $value_record_datetime = $params['value_record_datetime'];
        $value_record_datetime_id = $params['value_record_datetime_id'];
        $value_editor_department_code = $params['value_editor_department_code'];
        $value_editor_department_name = $params['value_editor_department_name'];
        $value_editor_user_code = $params['value_editor_user_code'];
        $value_editor_user_name = $params['value_editor_user_name'];
        $value_timetable_from_time = $params['value_timetable_from_time'];
        $value_timetable_to_time = $params['value_timetable_to_time'];
        $value_check_result = $params['value_check_result'];
        $value_check_max_times = $params['value_check_max_times'];
        $value_mobile_positions = $params['value_mobile_positions'];
        $before_value_mode = $params['before_value_mode'];
        $before_value_datetime = $params['before_value_datetime'];
        $business_kubun = $params['business_kubun'];
        $user_holiday_kubun = $params['user_holiday_kubun'];
        $use_free_item = $params['use_free_item'];
        $value_working_timetable_no = $params['value_working_timetable_no'];
        $attendance_time_index = $params['attendance_time_index'];

        $apicommon = new ApiCommonController();
        $attendance_from_date = new Carbon($work_time->getParamDatefromAttribute());        // 出勤1日のはじめ
        $attendance_from_date_format = date_format($attendance_from_date, 'Y/m/d');
        $timetable_from_date = new Carbon(
            $attendance_from_date_format.' '.$value_timetable_from_time);                   // タイムテーブルの始業時刻
        if ($value_timetable_from_time <= $value_timetable_to_time) {
            $timetable_to_date = new Carbon(
                $attendance_from_date_format.' '.$value_timetable_to_time);                 // タイムテーブルの終業時刻
        } else {
            // 翌日
            $nextdt =$apicommon->getNextDay($attendance_from_date_format, 'Y/m/d');
            $timetable_to_date = new Carbon(
                    $nextdt.' '.$value_timetable_to_time);                                  // タイムテーブルの終業時刻
        }
        $attendance_to_date = new Carbon(
            $attendance_from_date_format.' 23:59:59');                                      // 出勤1日の終わり
        $record_datetime = new Carbon($value_record_datetime);                              // 打刻日付時刻
        $record_before_datetime = new Carbon($before_value_datetime);                       // １個前の打刻時刻
        Log::debug('            出勤1日のはじめ attendance_from_date  = '.$attendance_from_date);
        Log::debug('            タイムテーブルの始業時刻 timetable_from_date  = '.$timetable_from_date);
        Log::debug('            タイムテーブルの終業時刻 timetable_to_date  = '.$timetable_to_date);
        Log::debug('            出勤1日の終わり attendance_to_date  = '.$attendance_to_date);
        Log::debug('            打刻日付時刻 record_datetime  = '.$record_datetime);
        Log::debug('                        record_before_datetime = '.$record_before_datetime);
        Log::debug('            1個前のモード before_value_mode = '.$before_value_mode);
        Log::debug('            1個前の打刻   before_value_datetime = '.$before_value_datetime);
        Log::debug('            cnt  = '.$cnt);
        // パターン設定
        $ptn = null;

        // ---------------------公用外出戻りが最初の場合 -----------------------------------------------------------------------------------
        if ($cnt == 0) {
            if ($before_value_mode == Config::get('const.C005.attendance_time') ||
                $before_value_mode == Config::get('const.C005.leaving_time') ||
                $before_value_mode == Config::get('const.C005.missing_middle_return_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_return_time') ||
                $before_value_mode == Config::get('const.C005.emergency_time') ||
                $before_value_mode == Config::get('const.C005.emergency_return_time')) {     // １個前のモードが出勤または退勤または外出戻りである場合
                if ($record_datetime >= $attendance_from_date &&
                            $record_datetime < $attendance_to_date) {                       // 出勤1日のはじめ <= 打刻時刻 < 出勤1日の終わり
                    if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                        // パターン１（打刻ミス（公用外出していない）。勤務状態は打刻なし）
                        $ptn = '1';
                    }
                }
            } elseif ($before_value_mode == Config::get('const.C005.public_going_out_time')) {      // １個前のモードが公用外出である場合
                if ($record_before_datetime < $attendance_from_date) {                      // １個前の打刻時刻 < 出勤1日のはじめ
                    // パターン２（正常戻り。勤務状態は戻り状態。）
                    $ptn = '2';
                }
            } else {                                                                        // １個前のモードがない
                // 不明データとして作成する
                $log_data = $work_time->getParamDatefromAttribute();
                $log_data .= $work_time->getParamUsercodeAttribute();
                $log_data .= $work_time->getParamDepartmentcodeAttribute();
            }
        // ---------------------公用外出戻りが２番目以降の場合 -----------------------------------------------------------------------------------
        } else {
            if ($before_value_mode == Config::get('const.C005.attendance_time') ||
                $before_value_mode == Config::get('const.C005.leaving_time') ||
                $before_value_mode == Config::get('const.C005.missing_middle_return_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_return_time') ||
                $before_value_mode == Config::get('const.C005.emergency_time') ||
                $before_value_mode == Config::get('const.C005.emergency_return_time')) {    // １個前のモードが出勤または退勤または外出戻りである場合
                if ($record_datetime >= $attendance_from_date) {                            // 出勤1日のはじめ <= 打刻時刻
                    if ($record_before_datetime >= $attendance_from_date) {                 // 出勤1日のはじめ <= １個前の打刻時刻
                        // パターン１（打刻ミス（公用外出していない）。勤務状態は打刻なし）
                        $ptn = '1';
                    }
                }
            } elseif ($before_value_mode == Config::get('const.C005.public_going_out_time')) {      // １個前のモードが公用外出
                if ($record_datetime >= $attendance_from_date &&
                    $record_datetime < $timetable_from_date) {                              // 出勤1日のはじめ <= 打刻時刻 < タイムテーブルの始業時刻
                    if ($record_before_datetime >= $attendance_from_date &&
                        $record_before_datetime < $timetable_from_date) {                   // 出勤1日のはじめ <= １個前の打刻時刻 < タイムテーブルの始業時刻
                        // パターン２（正常戻り。勤務状態は戻り状態。）
                        $ptn = '2';
                    }
                } elseif ($record_datetime >= $timetable_from_date &&
                            $record_datetime < $timetable_to_date) {                        // タイムテーブルの始業時刻 <= 打刻時刻 < タイムテーブルの終業時刻
                    if ($record_before_datetime >= $attendance_from_date &&
                        $record_before_datetime < $timetable_to_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻 < タイムテーブルの終業時刻
                        // パターン２（正常戻り。勤務状態は戻り状態。）
                        $ptn = '2';
                    }
                } elseif ($record_datetime >= $timetable_to_date &&
                            $record_datetime < $attendance_to_date) {                       // タイムテーブルの終業時刻 <= 打刻時刻 < 出勤1日の終わり
                    if ($record_before_datetime >= $attendance_from_date &&
                        $record_before_datetime < $timetable_to_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻 < 出勤1日の終わり
                        // パターン２（正常戻り。勤務状態は戻り状態。）
                        $ptn = '2';
                    }
                } elseif ($record_datetime > $timetable_to_date) {                          // 打刻時刻 > 出勤1日の終わり
                    if ($record_before_datetime >= $attendance_from_date) {                 // 出勤1日のはじめ <= １個前の打刻時刻
                        // パターン２（正常戻り。勤務状態は戻り状態。）
                        $ptn = '2';
                    }
                } else {                                                                    // １個前のモードがない
                    // 不明データとして作成する
                    $log_data = $work_time->getParamDatefromAttribute();
                    $log_data .= $work_time->getParamUsercodeAttribute();
                    $log_data .= $work_time->getParamDepartmentcodeAttribute();
                }
            } else {                                                                        // １個前のモードがない
                // 不明データとして作成する
                $log_data = $work_time->getParamDatefromAttribute();
                $log_data .= $work_time->getParamUsercodeAttribute();
                $log_data .= $work_time->getParamDepartmentcodeAttribute();
            }
        }
        if ($ptn == null) {
            $ptn = '';
        }
        // setPublicGoingOutReturnCollectPtn implement
        $array_impl_setPublicGoingOutReturnCollectPtn = array (
            'ptn' => $ptn,
            'record_datetime' => $record_datetime,
            'value_record_datetime_id' => $value_record_datetime_id,
            'value_editor_department_code' => $value_editor_department_code,
            'value_editor_department_name' => $value_editor_department_name,
            'value_editor_user_code' => $value_editor_user_code,
            'value_editor_user_name' => $value_editor_user_name,
            'value_check_result' => $value_check_result,
            'value_check_max_times' => $value_check_max_times,
            'value_mobile_positions' => $value_mobile_positions,
            'business_kubun' => $business_kubun,
            'user_holiday_kubun' => $user_holiday_kubun,
            'use_free_item' => $use_free_item,
            'value_working_timetable_no' => $value_working_timetable_no,
            'attendance_time_index' => $attendance_time_index
        );
        $this->pushArrayCalc($this->setPublicGoingOutReturnCollectPtn($array_impl_setPublicGoingOutReturnCollectPtn));

        Log::debug('---------------------- setPublicGoingOutReturnTime end ------------------------ ');
            
    }

    /**
     * 公用外出戻り打刻時刻テーブル設定
     *
     *        $collect_working_times
     *          'mode': 打刻モード（私用外出戻り）
     *          'time': 打刻時刻
     *          'status': 打刻状態
     *          'note': メモ
     *        　'late': 遅刻
     *          'Leave_early':早退
     *          'interval':インターバルチェック
     *          'calc':当日計算
     *          'to_be_confirmed':要確認
     * 
     * @param  打刻時刻
     * @return テーブル
     */
    private function setPublicGoingOutReturnCollectPtn($params)
    {
        Log::debug('---------------------- setPublicGoingOutReturnCollectPtn in ------------------------ ');
        // パラメータ設定
        $ptn = $params['ptn'];
        $record_datetime = $params['record_datetime'];
        $value_record_datetime_id = $params['value_record_datetime_id'];
        $value_editor_department_code = $params['value_editor_department_code'];
        $value_editor_department_name = $params['value_editor_department_name'];
        $value_editor_user_code = $params['value_editor_user_code'];
        $value_editor_user_name = $params['value_editor_user_name'];
        $value_check_result = $params['value_check_result'];
        $value_check_max_times = $params['value_check_max_times'];
        $value_mobile_positions = $params['value_mobile_positions'];
        $business_kubun = $params['business_kubun'];
        $user_holiday_kubun = $params['user_holiday_kubun'];
        $use_free_item = $params['use_free_item'];
        $value_working_timetable_no = $params['value_working_timetable_no'];
        $attendance_time_index = $params['attendance_time_index'];

        $temp_calc_model = new TempCalcWorkingTime();

        if ($ptn == '1') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.public_going_out_return_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_009'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            if ($attendance_time_index >= 0) {
                $temp_calc_model->setCurrentcalcAttribute($this->array_calc_calc[$attendance_time_index]);
            } else {
                $temp_calc_model->setCurrentcalcAttribute('0');
            }
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } elseif ($ptn == '2') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.public_going_out_return_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.public_return'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            if ($attendance_time_index >= 0) {
                $temp_calc_model->setCurrentcalcAttribute($this->array_calc_calc[$attendance_time_index]);
            } else {
                $temp_calc_model->setCurrentcalcAttribute('0');
            }
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } else {
            // 不明データとして作成する
            $temp_calc_model->setModeAttribute(Config::get('const.C005.public_going_out_return_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.unknown'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_004'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            if ($attendance_time_index >= 0) {
                $temp_calc_model->setCurrentcalcAttribute($this->array_calc_calc[$attendance_time_index]);
            } else {
                $temp_calc_model->setCurrentcalcAttribute('0');
            }
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        }

        Log::debug('---------------------- setPublicGoingOutReturnCollectPtn end ------------------------ ');
        return $temp_calc_model;
            
    }

    /**
     * 緊急収集開始打刻処理
     *
     * @param  打刻時刻のSEQ
     *         打刻時刻
     *         １個前のモード
     * @return void
     */
    private function setEmergencytime($params)
    {

        Log::debug('---------------------- 緊急収集開始打刻処理 setEmergencytime in ------------------------ ');
        $cnt = $params['cnt'];
        $work_time = $params['work_time'];
        $value_record_datetime = $params['value_record_datetime'];
        $value_timetable_from_time = $params['value_timetable_from_time'];
        $value_timetable_to_time = $params['value_timetable_to_time'];
        $value_check_result = $params['value_check_result'];
        $value_check_max_times = $params['value_check_max_times'];
        $value_check_interval = $params['value_check_interval'];
        $value_record_datetime_id = $params['value_record_datetime_id'];
        $value_editor_department_code = $params['value_editor_department_code'];
        $value_editor_department_name = $params['value_editor_department_name'];
        $value_editor_user_code = $params['value_editor_user_code'];
        $value_editor_user_name = $params['value_editor_user_name'];
        $value_mobile_positions = $params['value_mobile_positions'];
        $before_value_mode = $params['before_value_mode'];          // 1個前のモード
        $before_value_datetime = $params['before_value_datetime'];
        $business_kubun = $params['business_kubun'];
        $interval = $params['interval'];
        $user_holiday_kubun = $params['user_holiday_kubun'];
        $use_free_item = $params['use_free_item'];
        $value_working_timetable_no = $params['value_working_timetable_no'];
        $apicommon = new ApiCommonController();
        $attendance_from_date = new Carbon($work_time->getParamDatefromAttribute());        // 出勤1日のはじめ
        $attendance_from_date_format = date_format($attendance_from_date, 'Y/m/d');
        $timetable_from_date = new Carbon(
            $attendance_from_date_format.' '.$value_timetable_from_time);                   // タイムテーブルの始業時刻
        if ($value_timetable_from_time <= $value_timetable_to_time) {
            $timetable_to_date = new Carbon(
                $attendance_from_date_format.' '.$value_timetable_to_time);                 // タイムテーブルの終業時刻
        } else {
            // 翌日
            $nextdt =$apicommon->getNextDay($attendance_from_date_format, 'Y/m/d');
            $timetable_to_date = new Carbon(
                    $nextdt.' '.$value_timetable_to_time);                                  // タイムテーブルの終業時刻
        }
        $attendance_to_date = new Carbon(
            $attendance_from_date_format.' 23:59:59');                                      // 出勤1日の終わり
        $record_datetime = new Carbon($value_record_datetime);                              // 打刻日付時刻
        $record_before_datetime = new Carbon($before_value_datetime);                       // １個前の打刻時刻
        Log::debug('            出勤1日のはじめ attendance_from_date  = '.$attendance_from_date);
        Log::debug('            タイムテーブルの始業時刻 value_timetable_from_time  = '.$value_timetable_from_time);
        Log::debug('                                   timetable_from_date  = '.$timetable_from_date);
        Log::debug('            タイムテーブルの終業時刻 value_timetable_to_time  = '.$value_timetable_to_time);
        Log::debug('                                   timetable_to_date  = '.$timetable_to_date);
        Log::debug('            出勤1日の終わり attendance_to_date  = '.$attendance_to_date);
        Log::debug('            打刻日付時刻 record_datetime  = '.$record_datetime);
        Log::debug('                        record_before_datetime = '.$record_before_datetime);
        Log::debug('            1個前のモード before_value_mode = '.$before_value_mode);
        Log::debug('            1個前の打刻   before_value_datetime = '.$before_value_datetime);
        Log::debug('            cnt  = '.$cnt);
        // パターン設定
        $ptn = null;

        // ---------------------出勤が最初の場合 -----------------------------------------------------------------------------------
        if ($cnt == 0) {
            if ($before_value_mode == Config::get('const.C005.attendance_time') ||
                $before_value_mode == Config::get('const.C005.emergency_time')) {           // １個前のモードが出勤である場合
                // パターン２（打刻ミス（出勤済）（要確認）。勤務状態は打刻なし）
                $ptn = '2';
            } elseif ($before_value_mode == Config::get('const.C005.leaving_time') ||
                    $before_value_mode == Config::get('const.C005.emergency_return_time')) {       // １個前のモードが退勤である場合
                $ptn = '1';
                // 退勤から出勤までのタイム差を取得しインターバルチェック
                $value_check_interval = $apicommon->chkInteval($record_datetime, $record_before_datetime);
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_time')) {    // １個前のモードが私用または公用外出である場合
                // パターン５（打刻ミス（外出戻りしていない）。勤務状態は打刻なし）
                $ptn = '5';
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_time_return_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_return_time')) {     // １個前のモードが戻り
                // パターン６（打刻ミス（退勤していない）。勤務状態は打刻なし）
                $ptn = '6';
            } else {                                                                        // １個前のモードがない
                Log::debug('        １個前のモードがない ');
                // パターン１（正常出勤。勤務状態は出勤状態）
                $ptn = '1';
            }
        // ---------------------出勤が２番目以降の場合 -----------------------------------------------------------------------------------
        } else {
            if ($before_value_mode == Config::get('const.C005.attendance_time') ||
                $before_value_mode == Config::get('const.C005.emergency_time')) {           // １個前のモードが出勤である場合
                // パターン６（打刻ミス（退勤していない）。勤務状態は打刻なし）
                $ptn = '6';
            } elseif ($before_value_mode == Config::get('const.C005.leaving_time') ||
                    $before_value_mode == Config::get('const.C005.emergency_return_time')) {       // １個前のモードが退勤である場合
                // パターン１（正常出勤。勤務状態は出勤状態）
                $ptn = '1';
                // 退勤から出勤までのタイム差を取得しインターバルチェック
                $value_check_interval = $apicommon->chkInteval($record_datetime, $record_before_datetime);
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_time')) {    // １個前のモードが私用または公用外出である場合
                // パターン５（打刻ミス（外出戻りしていない）。勤務状態は打刻なし）
                $ptn = '5';
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_return_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_return_time')) {     // １個前のモードが戻り
                // パターン６（打刻ミス（退勤していない）。勤務状態は打刻なし）
                $ptn = '6';
            } else {                                                                        // １個前のモードがない
                // パターン１（正常出勤。勤務状態は出勤状態）
                $ptn = '1';
            }
        }

        if ($ptn == null) {
            $ptn = '';
        }
        // setEmergencyCollectPtn implement
        $array_impl_setEmergencyCollectPtn = array (
            'ptn' => $ptn,
            'record_datetime' => $record_datetime,
            'value_record_datetime_id' => $value_record_datetime_id,
            'value_editor_department_code' => $value_editor_department_code,
            'value_editor_department_name' => $value_editor_department_name,
            'value_editor_user_code' => $value_editor_user_code,
            'value_editor_user_name' => $value_editor_user_name,
            'value_check_result' => $value_check_result,
            'value_check_max_times' => $value_check_max_times,
            'value_check_interval' => $value_check_interval,
            'value_mobile_positions' => $value_mobile_positions,
            'business_kubun' => $business_kubun,
            'user_holiday_kubun' => $user_holiday_kubun,
            'use_free_item' => $use_free_item,
            'value_working_timetable_no' => $value_working_timetable_no
        );
        $this->pushArrayCalc($this->setEmergencyCollectPtn($array_impl_setEmergencyCollectPtn));

        $this->check_interval2 = $value_check_interval;
        Log::debug('---------------------- 緊急収集開始打刻処理 setEmergencytime end ------------------------ ');
    }

    /**
     * 緊急収集開始打刻時刻テーブル設定
     *
     *        $collect_working_times
     *          'mode': 打刻モード（緊急収集開始）
     *          'time': 打刻時刻
     *          'status': 打刻状態
     *          'note': メモ
     *        　'late': 遅刻
     *          'Leave_early':早退
     *          'interval':インターバルチェック
     *          'calc':当日計算
     *          'to_be_confirmed':要確認
     * 
     * @param  打刻時刻
     * @return テーブル
     */
    private function setEmergencyCollectPtn($params)
    {
        Log::debug('---------------------- setEmergencyCollectPtn in -- -------------------- ');
        // パラメータ設定
        $ptn = $params['ptn'];
        $record_datetime = $params['record_datetime'];
        $value_record_datetime_id = $params['value_record_datetime_id'];
        $value_editor_department_code = $params['value_editor_department_code'];
        $value_editor_department_name = $params['value_editor_department_name'];
        $value_editor_user_code = $params['value_editor_user_code'];
        $value_editor_user_name = $params['value_editor_user_name'];
        $value_check_result = $params['value_check_result'];
        $value_check_max_times = $params['value_check_max_times'];
        $value_check_interval = $params['value_check_interval'];
        $value_mobile_positions = $params['value_mobile_positions'];
        $business_kubun = $params['business_kubun'];
        $user_holiday_kubun = $params['user_holiday_kubun'];
        $use_free_item = $params['use_free_item'];
        $working_timetable_no = $params['value_working_timetable_no'];
        $temp_calc_model = new TempCalcWorkingTime();

        if ($ptn == '1') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.emergency_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.emergency'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('1');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute($value_check_interval);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } elseif ($ptn == '2') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.emergency_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_001'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute($value_check_interval);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } elseif ($ptn == '5') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.emergency_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_003'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute($value_check_interval);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } elseif ($ptn == '6') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.emergency_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_001'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute($value_check_interval);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } else {
            // 不明データとして作成する
            $temp_calc_model->setModeAttribute(Config::get('const.C005.emergency_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.unknown'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_004'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute($value_check_interval);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        }

        Log::debug('---------------------- setEmergencyCollectPtn end ------------------------ ');
        return $temp_calc_model;
            
    }

    /**
     * 緊急収集終了打刻処理
     *
     * @param  打刻時刻のSEQ
     *         打刻時刻
     *         １個前のモード
     * @return チェック結果
     */
    private function setEmergencyReturntime($params)
    {
        Log::debug('---------------------- 緊急収集終了打刻処理 setEmergencyReturntime in ------------------------ ');
        $cnt = $params['cnt'];
        $work_time = $params['work_time'];
        $value_record_datetime = $params['value_record_datetime'];
        $value_record_datetime_id = $params['value_record_datetime_id'];
        $value_editor_department_code = $params['value_editor_department_code'];
        $value_editor_department_name = $params['value_editor_department_name'];
        $value_editor_user_code = $params['value_editor_user_code'];
        $value_editor_user_name = $params['value_editor_user_name'];
        $value_timetable_from_time = $params['value_timetable_from_time'];
        $value_timetable_to_time = $params['value_timetable_to_time'];
        $value_check_result = $params['value_check_result'];
        $value_check_max_times = $params['value_check_max_times'];
        $value_mobile_positions = $params['value_mobile_positions'];
        $before_value_mode = $params['before_value_mode'];
        $before_value_datetime = $params['before_value_datetime'];
        $business_kubun = $params['business_kubun'];
        $user_holiday_kubun = $params['user_holiday_kubun'];
        $use_free_item = $params['use_free_item'];
        $value_working_timetable_no = $params['value_working_timetable_no'];
        $attendance_time_index = $params['attendance_time_index'];

        $array_impl_setLeavingCollectPtn = null;
        $apicommon = new ApiCommonController();
        $attendance_from_date = new Carbon($work_time->getParamDatefromAttribute());        // 出勤1日のはじめ
        $attendance_from_date_format = date_format($attendance_from_date, 'Y/m/d');
        $timetable_from_date = new Carbon(
            $attendance_from_date_format.' '.$value_timetable_from_time);                   // タイムテーブルの始業時刻
        if ($value_timetable_from_time <= $value_timetable_to_time) {
            $timetable_to_date = new Carbon(
                $attendance_from_date_format.' '.$value_timetable_to_time);                 // タイムテーブルの終業時刻
        } else {
            // 翌日
            $nextdt =$apicommon->getNextDay($attendance_from_date_format, 'Y/m/d');
            $timetable_to_date = new Carbon(
                    $nextdt.' '.$value_timetable_to_time);                                  // タイムテーブルの終業時刻
        }
        $attendance_to_date = new Carbon(
            $attendance_from_date_format.' 23:59:59');                                      // 出勤1日の終わり
        $record_datetime = new Carbon($value_record_datetime);                              // 打刻日付時刻
        $record_before_datetime = new Carbon($before_value_datetime);                       // １個前の打刻時刻
        Log::debug('            出勤1日のはじめ attendance_from_date  = '.$attendance_from_date);
        Log::debug('            タイムテーブルの始業時刻 timetable_from_date  = '.$timetable_from_date);
        Log::debug('            タイムテーブルの終業時刻 timetable_to_date  = '.$timetable_to_date);
        Log::debug('            出勤1日の終わり attendance_to_date  = '.$attendance_to_date);
        Log::debug('            打刻日付時刻 record_datetime  = '.$record_datetime);
        Log::debug('                        record_before_datetime = '.$record_before_datetime);
        Log::debug('            1個前のモード before_value_mode = '.$before_value_mode);
        Log::debug('            1個前の打刻   before_value_datetime = '.$before_value_datetime);
        Log::debug('            cnt  = '.$cnt);
        // パターン設定
        $ptn = null;

        // ---------------------緊急収集終了が最初の場合 -----------------------------------------------------------------------------------
        if ($cnt == 0) {
            if ($before_value_mode == Config::get('const.C005.emergency_time') ||
                $before_value_mode == Config::get('const.C005.missing_middle_return_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_return_time')) {     // １個前のモードが出勤または戻りである場合
                // パターン１（正常退勤。勤務状態は退勤状態。当日時間計算なし。）
                $ptn = '1';
            } elseif ($before_value_mode == Config::get('const.C005.leaving_time') ||
                    $before_value_mode == Config::get('const.C005.emergency_return_time')) {        // １個前のモードが退勤である場合
                // パターン６（打刻ミス（出勤していない）。勤務状態は打刻なし）
                $ptn = '6';
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_time')) {            // １個前のモードが外出である場合
                // パターン７（打刻ミス（外出戻りしていない）。勤務状態は打刻なし）
                $ptn = '7';
            } else {                                                                                // １個前のモードがない
                // パターン６（打刻ミス（出勤していない）。勤務状態は打刻なし）
                $ptn = '6';
            }
        // ---------------------緊急収集終了が２番目以降の場合 -----------------------------------------------------------------------------------
        } else {
            if ($before_value_mode == Config::get('const.C005.emergency_time')) {                   // １個前のモードが出勤
                // パターン１（正常退勤。勤務状態は退勤状態）
                $ptn = '1';
            } elseif ($before_value_mode == Config::get('const.C005.leaving_time') ||
                    $before_value_mode == Config::get('const.C005.emergency_return_time')) {        // １個前のモードが退勤である場合
                // パターン６（打刻ミス（出勤していない）。勤務状態は打刻なし）
                $ptn = '6';
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_time')) {            // １個前のモードが外出である場合
                // パターン７（打刻ミス（外出戻りしていない）。勤務状態は打刻なし）
                $ptn = '7';
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_return_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_return_time')) {     // １個前のモードが戻り
                // これより前の出勤打刻履歴を調査
                if ($cnt > 1) {
                    for($i=$cnt-2;$i>=0;$i--){
                        if ($this->array_working_mode[$i] == Config::get('const.C005.emergency_time')) {     // ２個前モードが緊急収集開始
                            // パターン１（正常退勤。勤務状態は退勤状態。当日時間計算なし。）
                            $ptn = '1';
                            break;
                        }
                    }
                } else {
                    // パターン１（正常退勤。勤務状態は退勤状態。当日時間計算なし。）
                    $ptn = '1';
                }
            } else {                                                                        // １個前のモードがない
                // 不明データとして作成する
                $log_data = $work_time->getParamDatefromAttribute();
                $log_data .= $work_time->getParamUsercodeAttribute();
                $log_data .= $work_time->getParamDepartmentcodeAttribute();
            }
        }

        if ($ptn == null) {
            $ptn = '';
        }
        // setLeavingCollectPtn implement
        $array_impl_setEmergencyReturnCollectPtn = array (
            'ptn' => $ptn,
            'record_datetime' => $record_datetime,
            'value_record_datetime_id' => $value_record_datetime_id,
            'value_editor_department_code' => $value_editor_department_code,
            'value_editor_department_name' => $value_editor_department_name,
            'value_editor_user_code' => $value_editor_user_code,
            'value_editor_user_name' => $value_editor_user_name,
            'value_check_result' => $value_check_result,
            'value_check_max_times' => $value_check_max_times,
            'value_mobile_positions' => $value_mobile_positions,
            'business_kubun' => $business_kubun,
            'user_holiday_kubun' => $user_holiday_kubun,
            'use_free_item' => $use_free_item,
            'value_working_timetable_no' => $value_working_timetable_no,
            'attendance_time_index' => $attendance_time_index
        );
        $this->pushArrayCalc(
            $this->setEmergencyReturnCollectPtn($array_impl_setEmergencyReturnCollectPtn));
        Log::debug('---------------------- setEmergencyReturntime end ------------------------ ');
            
    }

    /**
     * 退勤打刻時刻テーブル設定
     *
     *        $collect_working_times
     *          'mode': 打刻モード（出勤）
     *          'time': 打刻時刻
     *          'status': 打刻状態
     *          'note': メモ
     *        　'late': 遅刻
     *          'Leave_early':早退
     *          'interval':インターバルチェック
     *          'calc':当日計算
     *          'to_be_confirmed':要確認
     * 
     * @param  打刻時刻
     * @return テーブル
     */
    private function setEmergencyReturnCollectPtn($params)
    {
        Log::debug('---------------------- setEmergencyReturnCollectPtn in ----------------- ');
        // パラメータ設定
        $ptn = $params['ptn'];
        $record_datetime = $params['record_datetime'];
        $value_record_datetime_id = $params['value_record_datetime_id'];
        $value_editor_department_code = $params['value_editor_department_code'];
        $value_editor_department_name = $params['value_editor_department_name'];
        $value_editor_user_code = $params['value_editor_user_code'];
        $value_editor_user_name = $params['value_editor_user_name'];
        $value_check_result = $params['value_check_result'];
        $value_check_max_times = $params['value_check_max_times'];
        $value_mobile_positions = $params['value_mobile_positions'];
        $business_kubun = $params['business_kubun'];
        $user_holiday_kubun = $params['user_holiday_kubun'];
        $use_free_item = $params['use_free_item'];
        $value_working_timetable_no = $params['value_working_timetable_no'];
        $attendance_time_index = $params['attendance_time_index'];

        $temp_calc_model = new TempCalcWorkingTime();
        if ($ptn == '1') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.emergency_return_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.emergency_return'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            if ($attendance_time_index >= 0) {
                $temp_calc_model->setCurrentcalcAttribute($this->array_calc_calc[$attendance_time_index]);
            } else {
                $temp_calc_model->setCurrentcalcAttribute('0');
            }
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } elseif ($ptn == '6') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.emergency_return_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            if ($attendance_time_index >= 0) {
                $temp_calc_model->setCurrentcalcAttribute($this->array_calc_calc[$attendance_time_index]);
            } else {
                $temp_calc_model->setCurrentcalcAttribute('0');
            }
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } elseif ($ptn == '7') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.emergency_return_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_003'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            if ($attendance_time_index >= 0) {
                $temp_calc_model->setCurrentcalcAttribute($this->array_calc_calc[$attendance_time_index]);
            } else {
                $temp_calc_model->setCurrentcalcAttribute('0');
            }
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } else {
            // 不明データとして作成する
            $temp_calc_model->setModeAttribute(Config::get('const.C005.emergency_return_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorktimesidAttribute($value_record_datetime_id);
            $temp_calc_model->setEditordepartmentcodeAttribute($value_editor_department_code);
            $temp_calc_model->setEditordepartmentnameAttribute($value_editor_department_name);
            $temp_calc_model->setEditorusercodeAttribute($value_editor_user_code);
            $temp_calc_model->setEditorusernameAttribute($value_editor_user_name);
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.unknown'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_004'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            if ($attendance_time_index >= 0) {
                $temp_calc_model->setCurrentcalcAttribute($this->array_calc_calc[$attendance_time_index]);
            } else {
                $temp_calc_model->setCurrentcalcAttribute('0');
            }
            $temp_calc_model->setCurrentcalcAttribute('1'); // 必ず当日計算にしてみた 20200221
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        }
            
        Log::debug('---------------------- setEmergencyReturnCollectPtn end ------------------------ ');
        return $temp_calc_model;
    }

    /**
     * 打刻データない場合の打刻時刻テーブル設定
     *
     *        $collect_working_times
     *          'mode': 打刻モード（私用外出戻り）
     *          'time': 打刻時刻
     *          'status': 打刻状態
     *          'note': メモ
     *        　'late': 遅刻
     *          'Leave_early':早退
     *          'interval':インターバルチェック
     *          'calc':当日計算
     *          'to_be_confirmed':要確認
     * 
     * @param  打刻時刻
     * @return テーブル
     */
    private function setNoInputTimePtn($params)
    {
        Log::debug('---------------------- setNoInputTimePtn in ------------------------ ');
        // パラメータ設定
        $ptn = $params['ptn'];
        $user_holiday_name = $params['user_holiday_name'];
        $target_date = $params['target_date'];
        $hpliday_date = $params['hpliday_date'];
        $value_working_timetable_no = $params['value_working_timetable_no'];

        $temp_calc_model = new TempCalcWorkingTime();

        if ($ptn == '0') {
            $temp_calc_model->setModeAttribute('0');
            $temp_calc_model->setRecorddatetimeAttribute('');
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute('0');
            if (isset($user_holiday_name) && $user_holiday_name != '') {
                $temp_calc_model->setNoteAttribute('');
                if ($target_date == $hpliday_date) {
                    $temp_calc_model->setCurrentcalcAttribute('1');
                } else {
                    $temp_calc_model->setCurrentcalcAttribute('0');
                }
            } else {
                $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_008'));
                $temp_calc_model->setCurrentcalcAttribute('1');
            }
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute(0);
            $temp_calc_model->setCheckmaxtimesAttribute(0);
            $temp_calc_model->setPositionsAttribute(null);
        } elseif ($ptn == '1') {    // 設定ミス
            $temp_calc_model->setModeAttribute('0');
            $temp_calc_model->setRecorddatetimeAttribute('');
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute('0');
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_010'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('1');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute(0);
            $temp_calc_model->setCheckmaxtimesAttribute(0);
            $temp_calc_model->setPositionsAttribute(null);
        } elseif ($ptn == '2') {    // 設定ミス
            $temp_calc_model->setModeAttribute('0');
            $temp_calc_model->setRecorddatetimeAttribute('');
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute('0');
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_011'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('1');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute(0);
            $temp_calc_model->setCheckmaxtimesAttribute(0);
            $temp_calc_model->setPositionsAttribute(null);
        } elseif ($ptn == '3') {    // 設定ミス
            $temp_calc_model->setModeAttribute('0');
            $temp_calc_model->setRecorddatetimeAttribute('');
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute('0');
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_012'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('1');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute(0);
            $temp_calc_model->setCheckmaxtimesAttribute(0);
            $temp_calc_model->setPositionsAttribute(null);
        } elseif ($ptn == '4') {    // 設定ミス
            $temp_calc_model->setModeAttribute('0');
            $temp_calc_model->setRecorddatetimeAttribute('');
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute('0');
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_013'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('1');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute(0);
            $temp_calc_model->setCheckmaxtimesAttribute(0);
            $temp_calc_model->setPositionsAttribute(null);
        } elseif ($ptn == '5') {    // ���定ミス
            $temp_calc_model->setModeAttribute('0');
            $temp_calc_model->setRecorddatetimeAttribute('');
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute('0');
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_014'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('1');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute(0);
            $temp_calc_model->setCheckmaxtimesAttribute(0);
            $temp_calc_model->setPositionsAttribute(null);
        } elseif ($ptn == '6') {    // ptn=1のnoteなし
            $temp_calc_model->setModeAttribute('0');
            $temp_calc_model->setRecorddatetimeAttribute('');
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute('0');
            $temp_calc_model->setNoteAttribute('');
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('1');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute(0);
            $temp_calc_model->setCheckmaxtimesAttribute(0);
            $temp_calc_model->setPositionsAttribute(null);
        } else {
            // 不明データとして作成する
            $temp_calc_model->setModeAttribute('0');
            $temp_calc_model->setRecorddatetimeAttribute('');
            $temp_calc_model->setWorkingtimetablenoAttribute($value_working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.unknown'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_004'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('1');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute(0);
            $temp_calc_model->setCheckmaxtimesAttribute(0);
            $temp_calc_model->setPositionsAttribute(null);
        }

        Log::debug('---------------------- setNoInputTimePtn end ------------------------ ');
        return $temp_calc_model;
            
    }

    /**
     * 設定値確認
     *
     * @return チェック結果
     */
    private function chkSettingData($chkdata)
    {
        Log::debug('---------------------- chkSettingData in ------------------------ ');
        $chk_setting = 0;
        // 部署設定されているか
        if ($chkdata->department_code == null) {
            if ($chkdata->user_code != null) {
                $this->array_messagedata[] = 
                    array( Config::get('const.RESPONCE_ITEM.message') => str_replace('{0}',
                        $chkdata->user_name, Config::get('const.MSG_ERROR.not_setting_department_code')));
                Log::error($chkdata->user_name.Config::get('const.MSG_ERROR.not_setting_department_code'));
            } else {
                $this->array_messagedata[] = 
                array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.not_setting_department_code_nouser'));
                Log::error(Config::get('const.MSG_ERROR.not_setting_department_code_nouser'));
            }
            $chk_setting = 1;
        }
        // 締日設定されているか
        if ($chkdata->closing == null) {
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.not_setting_closing'));
            Log::error(Config::get('const.MSG_ERROR.not_setting_closing'));
            $chk_setting = 2;
        }
        // 時間単位設定されているか
        if ($chkdata->time_unit == null) {
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.not_setting_time_unit'));
            Log::error(Config::get('const.MSG_ERROR.not_setting_time_unit'));
            $chk_setting = 3;
        }
        // 時間の丸め設定されているか
        if ($chkdata->time_rounding == null) {
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.not_setting_time_rounding'));
            Log::error(Config::get('const.MSG_ERROR.not_setting_time_rounding'));
            $chk_setting = 4;
        }
        // 期首月設定されているか
        if ($chkdata->beginning_month == null) {
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.not_setting_beginning_month'));
            Log::error(Config::get('const.MSG_ERROR.not_setting_beginning_month'));
            $chk_setting = 5;
        }

        Log::debug('---------------------- chkSettingData end ------------------------ ');
        return $chk_setting;
            
    }

    /**
     * 次データ計算事前処理
     *
     * @return void
     */
    private function beforeArrayWorkingTime($result)
    {
        Log::debug('---------------------- beforeArrayWorkingTime in ------------------------ ');
        // 打刻データ配列の初期化
        $this->iniArrayWorkingTime();
        // 打刻データ配列の設定
        $this->pushArrayWorkingTime($result);
        // 計算用配列の初期化
        $this->iniArrayCalc();
        Log::debug('---------------------- beforeArrayWorkingTime end ------------------------ ');
    }

    /**
     * 打刻データ配列の初期化
     *
     * @return void
     */
    private function iniArrayWorkingTime()
    {
        Log::debug('---------------------- iniArrayWorkingTime in ------------------------ ');
        // 打刻データ配列
        $this->array_messagedata = array();
        $this->array_working_mode = array();
        $this->array_working_datetime = array();
        $this->array_working_datetime_id = array();
        $this->array_working_editor_department_code = array();
        $this->array_working_editor_department_name = array();
        $this->array_working_editor_user_code = array();
        $this->array_working_editor_user_name = array();
        $this->array_timetable_from_time = array();
        $this->array_timetable_to_time = array();
        $this->array_check_result = array();
        $this->array_check_max_times = array();
        $this->array_check_interval = array();
        $this->array_working_timetable_no = array();
        $this->array_mobile_positions = array();
        Log::debug('---------------------- iniArrayWorkingTime end ------------------------ ');
    }

    /**
     * 打刻データ配列の設定
     *
     * @return void
     */
    private function pushArrayWorkingTime($result)
    {
        Log::debug('---------------------- pushArrayWorkingTime in ------------------------ ');
        // 打刻データ配列
        $this->array_working_mode[] = $result->mode;
        $this->array_working_datetime[] = $result->record_datetime;
        $this->array_working_datetime_id[] = $result->record_datetime_id;
        $this->array_working_editor_department_code[] = $result->editor_department_code;
        $this->array_working_editor_department_name[] = $result->editor_department_name;
        $this->array_working_editor_user_code[] = $result->editor_user_code;
        $this->array_working_editor_user_name[] = $result->editor_user_code_name;
        $this->array_timetable_from_time[] = $result->working_timetable_from_time;
        $this->array_timetable_to_time[] = $result->working_timetable_to_time;
        if (isset($result->check_result)) {
            $this->array_check_result[] = $result->check_result;
        } else {
            $this->array_check_result[] = 0;
        }
        if (isset($result->check_max_time)) {
            $this->array_check_max_times[] = $result->check_max_time;
        } else {
            $this->array_check_max_times[] = 0;
        }
        if (isset($result->check_interval)) {
            $this->array_check_interval[] = $result->check_interval;
        } else {
            $this->array_check_interval[] = 0;
        }
        if (isset($result->working_timetable_no)) {
            $this->array_working_timetable_no[] = $result->working_timetable_no;
        } else {
            $this->array_working_timetable_no[] = 0;
        }
        if (isset($result->x_positions) && isset($result->y_positions)) {
            $this->array_mobile_positions[] = $result->x_positions.' '.$result->y_positions;
        } else {
            $this->array_mobile_positions[] = null;
        }
        Log::debug('---------------------- pushArrayWorkingTime end ------------------------ ');
    }

    /**
     * 計算用配列の初期化
     *
     * @return void
     */
    private function iniArrayCalc()
    {
        Log::debug('---------------------- iniArrayCalc in ------------------------ ');
        // 計算用配列
        $this->array_calc_mode = array();
        $this->array_calc_working_timetable_no = array();
        $this->array_calc_time = array();
        $this->calc_late_night_working_hours = 0;
        $this->array_calc_status = array();
        $this->array_calc_note = array();
        $this->array_calc_late = array();
        $this->array_calc_leave_early = array();
        $this->array_calc_calc = array();
        $this->array_calc_to_be_confirmed = array();
        $this->array_calc_pattern = array();
        $this->array_calc_check_result = array();
        $this->array_calc_check_max_times = array();
        $this->array_calc_check_interval = array();
        $this->array_calc_mobile_positions = array();
        // 表示用配列
        $this->array_dsp_time_id = array();
        $this->array_dsp_editor_department_code = array();
        $this->array_dsp_editor_department_name = array();
        $this->array_dsp_editor_user_code = array();
        $this->array_dsp_editor_user_name = array();
        Log::debug('---------------------- iniArrayCalc end ------------------------ ');
    }

    /**
     * 計算用配列の設定
     *
     * @return void
     */
    private function pushArrayCalc($temp_calc_model)
    {
        Log::debug('---------------------- pushArrayCalc in ------------------------ ');
        // 計算用配列配列
        $this->array_calc_mode[] = $temp_calc_model->getModeAttribute();
        $this->array_calc_working_timetable_no[] = $temp_calc_model->getWorkingtimetablenoAttribute();
        $this->array_calc_time[] = $temp_calc_model->getRecorddatetimeAttribute();
        $this->array_calc_status[] = $temp_calc_model->getWorkingstatusAttribute();
        $this->array_calc_note[] = $temp_calc_model->getNoteAttribute();
        $this->array_calc_late[] = $temp_calc_model->getLateAttribute();
        $this->array_calc_leave_early[] = $temp_calc_model->getLeaveearlyAttribute();
        $this->array_calc_calc[] = $temp_calc_model->getCurrentcalcAttribute();
        $this->array_calc_to_be_confirmed[] = $temp_calc_model->getTobeconfirmedAttribute();
        $this->array_calc_pattern[] = $temp_calc_model->getPatternAttribute();
        $this->array_calc_check_result[] = $temp_calc_model->getCheckresultAttribute();
        $this->array_calc_check_max_times[] = $temp_calc_model->getCheckmaxtimesAttribute();
        $this->array_calc_check_interval[] = $temp_calc_model->getCheckintervalAttribute();
        $this->array_calc_mobile_positions[] = $temp_calc_model->getPositionsAttribute();
        // 表示用配列配列
        $this->array_dsp_time_id[] = $temp_calc_model->getWorktimesidAttribute();
        $this->array_dsp_editor_department_code[] = $temp_calc_model->getEditordepartmentcodeAttribute();
        $this->array_dsp_editor_department_name[] = $temp_calc_model->getEditordepartmentnameAttribute();
        $this->array_dsp_editor_user_code[] = $temp_calc_model->getEditorusercodeAttribute();
        $this->array_dsp_editor_user_name[] = $temp_calc_model->getEditorusernameAttribute();
        Log::debug('---------------------- pushArrayCalc end ------------------------ ');
    }

    /**
     * temp日次集計タイムレコードの登録
     *
     * @return void
     */
    private function insTempCalcItem($params)
    {
        Log::debug('---------------------- insTempCalcItem in ------------------------ ');
        // パラメータ設定
        $target_date = $params['target_date'];
        $result = $params['target_result'];
        $temp_calc_model = new TempCalcWorkingTime();
        Log::debug('                       insTempCalcItem target_date = '.$target_date);
        Log::debug('                       insTempCalcItem user_code = '.$result->user_code);
        Log::debug('                       insTempCalcItem user_name = '.$result->user_name);
    
        // 計算用配列からtemporary項目を設定する
        $dt = new Carbon($target_date);
        $temp_calc_model->setWorkingdateAttribute(date_format($dt, 'Ymd'));
        $temp_calc_model->setEmploymentstatusAttribute($result->employment_status);
        $temp_calc_model->setDepartmentcodeAttribute($result->department_code);
        $temp_calc_model->setUsercodeAttribute($result->user_code);
        $temp_calc_model->setEmploymentstatusnameAttribute($result->employment_status_name);
        $temp_calc_model->setDepartmentnameAttribute($result->department_name);
        $temp_calc_model->setUsernameAttribute($result->user_name);
        $temp_calc_model->setWorkingtimetablenameAttribute($result->working_timetable_name);
        $temp_calc_model->setWorkingtimetablefromtimeAttribute($result->working_timetable_from_time);
        $temp_calc_model->setWorkingtimetabletotimeAttribute($result->working_timetable_to_time);
        if (isset($result->shift_no)) {
            $temp_calc_model->setShiftnoAttribute($result->shift_no);
        } else {
            $temp_calc_model->setShiftnoAttribute('');
        }
        $temp_calc_model->setShiftnameAttribute($result->shift_name);
        $temp_calc_model->setShiftfromtimeAttribute($result->shift_from_time);
        $temp_calc_model->setShifttotimeAttribute($result->shift_to_time);
        $temp_calc_model->setWeekdaykubunAttribute($result->weekday_kubun);
        $temp_calc_model->setWeekdaynameAttribute($result->weekday_name);
        $temp_calc_model->setBusinesskubunAttribute($result->business_kubun);
        $temp_calc_model->setBusinessnameAttribute($result->business_name);
        if (isset($result->user_holiday_kubun)) {
            $temp_calc_model->setHolidaykubunAttribute($result->user_holiday_kubun);
        } else {
            $temp_calc_model->setHolidaykubunAttribute(null);
        }
        if (isset($result->user_holiday_name)) {
            $temp_calc_model->setHolidaynameAttribute($result->user_holiday_name);
        } else {
            $temp_calc_model->setHolidaynameAttribute('');
        }
        $temp_calc_model->setClosingAttribute($result->closing);
        $temp_calc_model->setUplimittimeAttribute($result->uplimit_time);
        $temp_calc_model->setStatutoryuplimittimeAttribute($result->statutory_uplimit_time);
        $temp_calc_model->setTimeunitAttribute($result->time_unit);
        $temp_calc_model->setTimeroundingAttribute($result->time_rounding);
        $temp_calc_model->setMax3MonthtotalAttribute($result->max_3month_total);
        $temp_calc_model->setMax6MonthtotalAttribute($result->max_6month_total);
        $temp_calc_model->setMax12MonthtotalAttribute($result->max_12month_total);
        $temp_calc_model->setBeginningmonthAttribute($result->beginning_month);
        $temp_calc_model->setYearAttribute($result->year);
        $temp_calc_model->setSystemDateAttribute(Carbon::now());

        for($i=0;$i<count($this->array_calc_mode);$i++){
            $this->user_temp_seq++;
            $temp_calc_model->setSeqAttribute($this->user_temp_seq);
            Log::debug('        用途フリー項目 =  '.$result->use_free_item);
            if ((!isset($result->use_free_item)) ||
                (isset($result->use_free_item) &&
                (substr($result->use_free_item, Config::get('const.USEFREEITEM.day_holiday'), 1) == "0"))) {
                $temp_calc_model->setLateAttribute('1');
            } else {
                $temp_calc_model->setLateAttribute('0');
            }
            $set_chk = "";
            if (isset($result->use_free_item)) {
                if (substr($result->use_free_item, Config::get('const.USEFREEITEM.day_holiday'), 1) == "0") {
                    if (isset($result->user_holiday_kubun)) {
                        $set_chk = "1";
                    } else {
                        $set_chk = "2";
                    }
                } else {
                    if (isset($result->user_holiday_kubun)) {
                        $set_chk = "3";
                    } else {
                        $set_chk = "2";
                    }
                }
            } else {
                $set_chk = "4";
            }

            Log::debug('                       insTempCalcItem holiday_kubun = '.$result->holiday_kubun);
            Log::debug('                       insTempCalcItem use_free_item = '.$result->use_free_item);
            Log::debug('                       insTempCalcItem set_chk = '.$set_chk);
            if ($set_chk == "1") {
                Log::debug('                       array_calc_mode  = '.$this->array_calc_mode[$i]);
                Log::debug('                       array_calc_time  = '.$this->array_calc_time[$i]);
                $temp_calc_model->setModeAttribute($this->array_calc_mode[$i]);
                $temp_calc_model->setRecorddatetimeAttribute($this->array_calc_time[$i]);
                $temp_calc_model->setWorktimesidAttribute($this->array_dsp_time_id[$i]);
                $temp_calc_model->setEditordepartmentcodeAttribute($this->array_dsp_editor_department_code[$i]);
                $temp_calc_model->setEditordepartmentnameAttribute($this->array_dsp_editor_department_name[$i]);
                $temp_calc_model->setEditorusercodeAttribute($this->array_dsp_editor_user_code[$i]);
                $temp_calc_model->setEditorusernameAttribute($this->array_dsp_editor_user_name[$i]);
                $temp_calc_model->setWorkingtimetablenoAttribute($this->array_calc_working_timetable_no[$i]);
                if (isset($this->array_calc_time[$i])) {
                    $edt_calc_datetime = new Carbon($this->array_calc_time[$i]);
                    $temp_calc_model->setRecordyearAttribute($edt_calc_datetime->format('Y'));
                    $temp_calc_model->setRecordmonthAttribute($edt_calc_datetime->format('m'));
                    $temp_calc_model->setRecorddateAttribute($edt_calc_datetime->format('Ymd'));
                    $temp_calc_model->setRecordtimeAttribute($edt_calc_datetime->format('His'));
                } else {
                    $temp_calc_model->setRecordyearAttribute($dt->format('Y'));
                    $temp_calc_model->setRecordmonthAttribute($dt->format('m'));
                    $temp_calc_model->setRecorddateAttribute($dt->format('Ymd'));
                    $temp_calc_model->setRecordtimeAttribute(null);
                }
                $temp_calc_model->setWorkingstatusAttribute($this->array_calc_status[$i]);
            } elseif ($set_chk == "2") {
                Log::debug('                       insTempCalcItem non_set = '.$result->holiday_kubun);
                Log::debug('                       insTempCalcItem array_calc_mode = '.$this->array_calc_mode[$i]);
                Log::debug('                       insTempCalcItem array_calc_time = '.$this->array_calc_time[$i]);
                $temp_calc_model->setModeAttribute($this->array_calc_mode[$i]);
                $temp_calc_model->setRecorddatetimeAttribute($this->array_calc_time[$i]);
                $temp_calc_model->setWorktimesidAttribute($this->array_dsp_time_id[$i]);
                $temp_calc_model->setEditordepartmentcodeAttribute($this->array_dsp_editor_department_code[$i]);
                $temp_calc_model->setEditordepartmentnameAttribute($this->array_dsp_editor_department_name[$i]);
                $temp_calc_model->setEditorusercodeAttribute($this->array_dsp_editor_user_code[$i]);
                $temp_calc_model->setEditorusernameAttribute($this->array_dsp_editor_user_name[$i]);
                $temp_calc_model->setWorkingtimetablenoAttribute($this->array_calc_working_timetable_no[$i]);
                $temp_calc_model->setWorkingtimetablenoAttribute($this->array_calc_working_timetable_no[$i]);
                if (isset($this->array_calc_time[$i])) {
                    $edt_calc_datetime = new Carbon($this->array_calc_time[$i]);
                    $temp_calc_model->setRecordyearAttribute($edt_calc_datetime->format('Y'));
                    $temp_calc_model->setRecordmonthAttribute($edt_calc_datetime->format('m'));
                    $temp_calc_model->setRecorddateAttribute($edt_calc_datetime->format('Ymd'));
                    $temp_calc_model->setRecordtimeAttribute($edt_calc_datetime->format('His'));
                } else {
                    $temp_calc_model->setRecordyearAttribute($dt->format('Y'));
                    $temp_calc_model->setRecordmonthAttribute($dt->format('m'));
                    $temp_calc_model->setRecorddateAttribute($dt->format('Ymd'));
                    $temp_calc_model->setRecordtimeAttribute(null);
                }
                $temp_calc_model->setWorkingstatusAttribute($this->array_calc_status[$i]);
            } elseif ($set_chk == "3") {
                Log::debug('                       insTempCalcItem なし = '.$result->holiday_kubun);
                $temp_calc_model->setModeAttribute(null);
                $temp_calc_model->setRecorddatetimeAttribute(null);
                $temp_calc_model->setWorkingtimetablenoAttribute($this->array_calc_working_timetable_no[$i]);
                $temp_calc_model->setWorktimesidAttribute($this->array_dsp_time_id[$i]);
                $temp_calc_model->setEditordepartmentcodeAttribute($this->array_dsp_editor_department_code[$i]);
                $temp_calc_model->setEditordepartmentnameAttribute($this->array_dsp_editor_department_name[$i]);
                $temp_calc_model->setEditorusercodeAttribute($this->array_dsp_editor_user_code[$i]);
                $temp_calc_model->setEditorusernameAttribute($this->array_dsp_editor_user_name[$i]);
                if (isset($this->array_calc_time[$i])) {
                    $edt_calc_datetime = new Carbon($this->array_calc_time[$i]);
                    $temp_calc_model->setRecordyearAttribute($edt_calc_datetime->format('Y'));
                    $temp_calc_model->setRecordmonthAttribute($edt_calc_datetime->format('m'));
                    $temp_calc_model->setRecorddateAttribute($edt_calc_datetime->format('Ymd'));
                    $temp_calc_model->setRecordtimeAttribute($edt_calc_datetime->format('His'));
                } else {
                    $temp_calc_model->setRecordyearAttribute($dt->format('Y'));
                    $temp_calc_model->setRecordmonthAttribute($dt->format('m'));
                    $temp_calc_model->setRecorddateAttribute($dt->format('Ymd'));
                    $temp_calc_model->setRecordtimeAttribute(null);
                }
                $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.user_holiday'));
            } else {
                Log::debug('                       array_calc_mode  = '.$this->array_calc_mode[$i]);
                Log::debug('                       array_calc_time  = '.$this->array_calc_time[$i]);
                $temp_calc_model->setModeAttribute($this->array_calc_mode[$i]);
                $temp_calc_model->setRecorddatetimeAttribute($this->array_calc_time[$i]);
                $temp_calc_model->setWorktimesidAttribute($this->array_dsp_time_id[$i]);
                $temp_calc_model->setEditordepartmentcodeAttribute($this->array_dsp_editor_department_code[$i]);
                $temp_calc_model->setEditordepartmentnameAttribute($this->array_dsp_editor_department_name[$i]);
                $temp_calc_model->setEditorusercodeAttribute($this->array_dsp_editor_user_code[$i]);
                $temp_calc_model->setEditorusernameAttribute($this->array_dsp_editor_user_name[$i]);
                $temp_calc_model->setWorkingtimetablenoAttribute($this->array_calc_working_timetable_no[$i]);
                if (isset($this->array_calc_time[$i]) && $this->array_calc_time[$i] != '') {
                    $edt_calc_datetime = new Carbon($this->array_calc_time[$i]);
                    $temp_calc_model->setRecordyearAttribute($edt_calc_datetime->format('Y'));
                    $temp_calc_model->setRecordmonthAttribute($edt_calc_datetime->format('m'));
                    $temp_calc_model->setRecorddateAttribute($edt_calc_datetime->format('Ymd'));
                    $temp_calc_model->setRecordtimeAttribute($edt_calc_datetime->format('His'));
                } else {
                    $temp_calc_model->setRecordyearAttribute($dt->format('Y'));
                    $temp_calc_model->setRecordmonthAttribute($dt->format('m'));
                    $temp_calc_model->setRecorddateAttribute($dt->format('Ymd'));
                    $temp_calc_model->setRecordtimeAttribute(null);
                }
                $temp_calc_model->setWorkingstatusAttribute($this->array_calc_status[$i]);
            }
            $temp_calc_model->setNoteAttribute($this->array_calc_note[$i]);
            $temp_calc_model->setLateAttribute($this->array_calc_late[$i]);
            $temp_calc_model->setLeaveearlyAttribute($this->array_calc_leave_early[$i]);
            $temp_calc_model->setCurrentcalcAttribute($this->array_calc_calc[$i]);
            $temp_calc_model->setTobeconfirmedAttribute($this->array_calc_to_be_confirmed[$i]);
            $temp_calc_model->setPatternAttribute($this->array_calc_pattern[$i]);
            $temp_calc_model->setCheckresultAttribute($this->array_calc_check_result[$i]);
            $temp_calc_model->setCheckmaxtimesAttribute($this->array_calc_check_max_times[$i]);
            $temp_calc_model->setCheckintervalAttribute($this->array_calc_check_interval[$i]);
            $temp_calc_model->setPositionsAttribute($this->array_calc_mobile_positions[$i]);

            try{
                Log::debug('                       insertTempCalcWorkingtime call = ');
                $temp_calc_model->insertTempCalcWorkingtime();
            }catch(\PDOException $pe){
                throw $pe;
            }
        }
        Log::debug('---------------------- insTempCalcItem end ------------------------ ');
    }

    /**
     * 日次集計
     *
     *
     * @return 集計結果
     */
    private function calcTempWorkingTimeDate($timetables, $target_date){

        Log::debug('---------------------- calcTempWorkingTimeDate in ------------------------ ');
        $this->not_employment_working = 0;
        $current_date = null;
        $current_department_code = null;
        $current_user_code = null;
        $current_result = null;
        $before_date = null;
        $before_department_code = null;
        $before_user_code = null;
        $before_result = null;
        $note = '';
        $late = '';
        $leave_early = '';
        $current_calc = '';
        $to_be_confirmed = '';
        $array_notelateetc = array();
        // 計算用時刻保存
        $attendance_time = '';
        $leaving_time = '';
        $missing_middle_time = '';
        $missing_middle_return_time = '';
        $public_going_out_time = '';
        $public_going_out_return_time = '';
        // モバイル用時刻保存
        $attendance_time_positions = null;
        $leaving_time_positions = null;
        $missing_middle_time_positions = null;
        $missing_return_time_positions = null;
        $public_going_out_time_positions = null;
        $public_return_time_positions = null;
        //
        $working_status = 0;
        $array_working_time_kubun = array(Config::get('const.C004.regular_working_time'),
            Config::get('const.C004.regular_working_breaks_time'),
            Config::get('const.C004.out_of_regular_working_time'),
            Config::get('const.C004.out_of_regular_night_working_time'),
            Config::get('const.C004.statutory_working_time'),
            Config::get('const.C004.out_of_statutory_working_time'),
            Config::get('const.C004.out_of_statutory_night_working_time'),
            Config::get('const.C004.working_breaks_time')
        );
        // 出勤・退勤の労働時間数配列
        $array_attendance_calc_time = array();
        for ($i=0;$i<count($array_working_time_kubun);$i++) {
            $array_attendance_calc_time[$i] = 0; 
        }
        // 外出・戻りの時間数配列
        $array_missing_middle_time = array();
        for ($i=0;$i<count($array_working_time_kubun);$i++) {
            $array_missing_middle_time[$i] = 0; 
        }
        // 外出・戻りの時間数配列
        $array_public_going_out_time = array();
        for ($i=0;$i<count($array_working_time_kubun);$i++) {
            $array_public_going_out_time[$i] = 0; 
        }
        // 緊急収集の労働時間数配列
        $array_emergency_time = array();
        for ($i=0;$i<count($array_working_time_kubun);$i++) {
            $array_emergency_time[$i] = 0; 
        }
        // 深夜労働時間
        $this->calc_late_night_working_hours = 0;
        // データ登録用出勤・退勤の労働時刻数配列
        $array_add_attendance_time = array();
        $array_add_attendance_time_id = array();
        $array_add_attendance_editor_department_code = array();
        $array_add_attendance_editor_department_name = array();
        $array_add_attendance_editor_user_code = array();
        $array_add_attendance_editor_user_name = array();
        $array_add_attendance_time_positions = array();
        $array_add_leaving_time = array();
        $array_add_leaving_time_id = array();
        $array_add_leaving_editor_department_code = array();
        $array_add_leaving_editor_department_name = array();
        $array_add_leaving_editor_user_code = array();
        $array_add_leaving_editor_user_name = array();
        $array_add_leaving_time_positions = array();
        // データ登録用外出・戻りの労働時刻数配列
        $array_add_missing_middle_time = array();
        $array_add_missing_middle_time_id = array();
        $array_add_missing_middle_editor_department_code = array();
        $array_add_missing_middle_editor_department_name = array();
        $array_add_missing_middle_editor_user_code = array();
        $array_add_missing_middle_editor_user_name = array();
        $array_add_missing_middle_time_positions = array();
        $array_add_missing_return_time = array();
        $array_add_missing_return_time_id = array();
        $array_add_missing_return_editor_department_code = array();
        $array_add_missing_return_editor_department_name = array();
        $array_add_missing_return_editor_user_code = array();
        $array_add_missing_return_editor_user_name = array();
        $array_add_missing_return_time_positions = array();
        $array_add_public_going_out_time = array();
        $array_add_public_going_out_time_id = array();
        $array_add_public_going_out_editor_department_code = array();
        $array_add_public_going_out_editor_department_name = array();
        $array_add_public_going_out_editor_user_code = array();
        $array_add_public_going_out_editor_user_name = array();
        $array_add_public_going_out_time_positions = array();
        $array_add_public_return_time = array();
        $array_add_public_return_time_id = array();
        $array_add_public_return_editor_department_code = array();
        $array_add_public_return_editor_department_name = array();
        $array_add_public_return_editor_user_code = array();
        $array_add_public_return_editor_user_name = array();
        $array_add_public_return_time_positions = array();
        // データ登録用緊急収集の労働時刻数配列
        $array_add_emergency_time = array();
        $array_add_emergency_time_id = array();
        $array_add_emergency_editor_department_code = array();
        $array_add_emergency_editor_department_name = array();
        $array_add_emergency_editor_user_code = array();
        $array_add_emergency_editor_user_name = array();
        $array_add_emergency_time_positions = array();
        $array_add_emergency_return_time = array();
        $array_add_emergency_return_time_id = array();
        $array_add_emergency_return_editor_department_code = array();
        $array_add_emergency_return_editor_department_name = array();
        $array_add_emergency_return_editor_user_code = array();
        $array_add_emergency_return_editor_user_name = array();
        $array_add_emergency_return_time_positions = array();
        // ユーザー休暇区分判定用
        $before_holiday_date = null;
        $before_holiday_user_code = null;
        $before_holiday_department_code = null;
        $before_holiday_kubun = null;
        $before_holiday_set = false;

        $apicommon = new ApiCommonController();
        // TODO: 引数の$timetablesと1.と2.を纏められないか
        // 1.時間丸め用にタイムテーブル労働開始終了時間テーブル設定する
        $array_get_timetable_result = $apicommon->setWorkingStartEndTimeTable($target_date);
        // 2.集計必要な休暇用のタイムテーブルを取得
        $timetable_model = new WorkingTimeTable();
        $timetable_model->setParamdatefromAttribute($target_date);
        $timetable_model->setParamdatetoAttribute($target_date);
        $array_break_worktimetable_result = $timetable_model->getAllTimeTables();
        // ユーザー単位処理
        $temp_calc_model = new TempCalcWorkingTime();
        $worktimes = $temp_calc_model->getTempCalcWorkingtime();
        $add_result = true;
        if (count($worktimes) == 0) {
            return false;
        }
        $calc_nobreak_cnt = 0;
        $set_calcTimes_flg = false;
        foreach ($worktimes as $result) {
            // 現在の情報保存
            Log::debug('日次集計 ユーザー  code = '.$result->user_code.' '.$result->user_name);
            Log::debug('        部署 = '.$result->department_name);
            Log::debug('        打刻日 = '.$result->working_date);
            Log::debug('        モード = '.$result->mode);
            Log::debug('        打刻日時刻 = '.$result->record_datetime);
            Log::debug('        打刻日 = '.$result->record_date);
            Log::debug('        打刻時刻 = '.$result->record_time);
            Log::debug('        打刻時刻ID = '.$result->work_times_id);
            Log::debug('        ノート = '.$result->note);
            Log::debug('        遅刻 = '.$result->late);
            Log::debug('        早退 = '.$result->leave_early);
            Log::debug('        出勤日 = '.$result->business_kubun);
            Log::debug('        　　　 = '.$result->business_name);
            Log::debug('        休暇 = '.$result->holiday_kubun);
            Log::debug('        　　　= '.$result->holiday_name);
            Log::debug('        　　　= '.$result->holiday_description);
            Log::debug('        当日分計算　=  '.$result->current_calc);
            Log::debug('        勤務状態 =  '.$result->working_status);
            Log::debug('        タイムテーブル番号 =  '.$result->working_timetable_no);
            Log::debug('        用途フリー項目 =  '.$result->use_free_item);
            // 緊急か？
            $apicommon = new ApiCommonController();
            $isemergency_time = $apicommon->isEmagency($result->working_timetable_no);
            Log::debug('        緊急か =  '.$isemergency_time);
            $current_date = $result->working_date;
            $current_department_code = $result->department_code;
            $current_user_code = $result->user_code;
            $current_result = $result;
            if ($before_date == null) {$before_date = $current_date;}
            if ($before_department_code == null) {$before_department_code = $current_department_code;}
            if ($before_user_code == null) {$before_user_code = $current_user_code;}
            if ($before_result == null) {$before_result = $result;}
            $mode_chk = false;
            if ($result->mode == Config::get('const.C005.attendance_time')) { $mode_chk = true; }
            if ($result->mode == Config::get('const.C005.leaving_time')) { $mode_chk = true; }
            if ($result->mode == Config::get('const.C005.missing_middle_time')) { $mode_chk = true; }
            if ($result->mode == Config::get('const.C005.missing_middle_return_time')) { $mode_chk = true; }
            if ($result->mode == Config::get('const.C005.public_going_out_time')) { $mode_chk = true; }
            if ($result->mode == Config::get('const.C005.public_going_out_return_time')) { $mode_chk = true; }
            if ($result->mode == Config::get('const.C005.emergency_time')) { $mode_chk = true; }
            if ($result->mode == Config::get('const.C005.emergency_return_time')) { $mode_chk = true; }
            // 同じキーの場合
            if ($current_date == $before_date &&
                $current_department_code == $current_department_code &&
                $current_user_code == $before_user_code) {
                Log::debug('    同じキーの場合  ');
                $calc_nobreak_cnt++;
                if ($result->mode == Config::get('const.C005.attendance_time')) {
                    Log::debug('        temp_working_time_dates 出勤  $result->record_datetime = '.$result->record_datetime);
                    Log::debug('        temp_working_time_dates 出勤  $result->work_times_id = '.$result->work_times_id);
                    $attendance_time = $result->record_datetime;
                    $attendance_time_id = $result->work_times_id;
                    $attendance_editor_department_code = $result->editor_department_code;
                    $attendance_editor_department_name = $result->editor_department_name;
                    $attendance_editor_user_code = $result->editor_user_code;
                    $attendance_editor_user_name = $result->editor_user_name;
                    if (isset($result->x_positions) && isset($result->y_positions)) {
                        $attendance_time_positions = $result->x_positions.' '.$result->y_positions;
                    } else {
                        $attendance_time_positions = null;
                    }               
                }
                if ($result->mode == Config::get('const.C005.leaving_time')) {
                    Log::debug('        temp_working_time_dates 退勤  $result->record_datetime = '.$result->record_datetime);
                    Log::debug('        temp_working_time_dates 退勤  $result->work_times_id = '.$result->work_times_id);
                    $leaving_time = $result->record_datetime;
                    $leaving_time_id = $result->work_times_id;
                    $leaving_editor_department_code = $result->editor_department_code;
                    $leaving_editor_department_name = $result->editor_department_name;
                    $leaving_editor_user_code = $result->editor_user_code;
                    $leaving_editor_user_name = $result->editor_user_name;
                    if (isset($result->x_positions) && isset($result->y_positions)) {
                        $leaving_time_positions = $result->x_positions.' '.$result->y_positions;
                    } else {
                        $leaving_time_positions = null;
                    }               
                }
                if ($result->mode == Config::get('const.C005.missing_middle_time')) {
                    $missing_middle_time = $result->record_datetime;
                    $missing_middle_time_id = $result->work_times_id;
                    $missing_middle_editor_department_code = $result->editor_department_code;
                    $missing_middle_editor_department_name = $result->editor_department_name;
                    $missing_middle_editor_user_code = $result->editor_user_code;
                    $missing_middle_editor_user_name = $result->editor_user_name;
                    if (isset($result->x_positions) && isset($result->y_positions)) {
                        $missing_middle_time_positions = $result->x_positions.' '.$result->y_positions;
                    } else {
                        $missing_middle_time_positions = null;
                    }               
                }
                if ($result->mode == Config::get('const.C005.missing_middle_return_time')) {
                    $missing_middle_return_time = $result->record_datetime;
                    $missing_middle_return_time_id = $result->work_times_id;
                    $missing_return_editor_department_code = $result->editor_department_code;
                    $missing_return_editor_department_name = $result->editor_department_name;
                    $missing_return_editor_user_code = $result->editor_user_code;
                    $missing_return_editor_user_name = $result->editor_user_name;
                    if (isset($result->x_positions) && isset($result->y_positions)) {
                        $missing_return_time_positions = $result->x_positions.' '.$result->y_positions;
                    } else {
                        $missing_return_time_positions = null;
                    }               
                }
                if ($result->mode == Config::get('const.C005.public_going_out_time')) {
                    $public_going_out_time = $result->record_datetime;
                    $public_going_out_time_id = $result->work_times_id;
                    $public_going_out_editor_department_code = $result->editor_department_code;
                    $public_going_out_editor_department_name = $result->editor_department_name;
                    $public_going_out_editor_user_code = $result->editor_user_code;
                    $public_going_out_editor_user_name = $result->editor_user_name;
                    if (isset($result->x_positions) && isset($result->y_positions)) {
                        $public_going_out_time_positions = $result->x_positions.' '.$result->y_positions;
                    } else {
                        $public_going_out_time_positions = null;
                    }               
                }
                if ($result->mode == Config::get('const.C005.public_going_out_return_time')) {
                    $public_going_out_return_time = $result->record_datetime;
                    $public_going_out_return_time_id = $result->work_times_id;
                    $public_return_editor_department_code = $result->editor_department_code;
                    $public_return_editor_department_name = $result->editor_department_name;
                    $public_return_editor_user_code = $result->editor_user_code;
                    $public_return_editor_user_name = $result->editor_user_name;
                    if (isset($result->x_positions) && isset($result->y_positions)) {
                        $public_return_time_positions = $result->x_positions.' '.$result->y_positions;
                    } else {
                        $public_return_time_positions = null;
                    }               
                }
                if ($result->mode == Config::get('const.C005.emergency_time')) {
                    Log::debug('        temp_working_time_dates 緊急収集開始  $result->record_datetime = '.$result->record_datetime);
                    Log::debug('        temp_working_time_dates 緊急収集開始  $result->work_times_id = '.$result->work_times_id);
                    $emergency_time = $result->record_datetime;
                    $emergency_time_id = $result->work_times_id;
                    $emergency_editor_department_code = $result->editor_department_code;
                    $emergency_editor_department_name = $result->editor_department_name;
                    $emergency_editor_user_code = $result->editor_user_code;
                    $emergency_editor_user_name = $result->editor_user_name;
                    if (isset($result->x_positions) && isset($result->y_positions)) {
                        $emergency_time_positions = $result->x_positions.' '.$result->y_positions;
                    } else {
                        $emergency_time_positions = null;
                    }               
                }
                if ($result->mode == Config::get('const.C005.emergency_return_time')) {
                    Log::debug('        temp_working_time_dates 緊急収集終了  $result->record_datetime = '.$result->record_datetime);
                    Log::debug('        temp_working_time_dates 緊急収集終了  $result->work_times_id = '.$result->work_times_id);
                    $emergency_return_time = $result->record_datetime;
                    $emergency_return_time_id = $result->work_times_id;
                    $emergency_return_editor_department_code = $result->editor_department_code;
                    $emergency_return_editor_department_name = $result->editor_department_name;
                    $emergency_return_editor_user_code = $result->editor_user_code;
                    $emergency_return_editor_user_name = $result->editor_user_name;
                    if (isset($result->x_positions) && isset($result->y_positions)) {
                        $emergency_return_time_positions = $result->x_positions.' '.$result->y_positions;
                    } else {
                        $emergency_return_time_positions = null;
                    }               
                }
                $array_notelateetc = $this->setNoteLateEtc($result);
                $note .= $array_notelateetc[0];
                $late = $array_notelateetc[1];
                $leave_early = $array_notelateetc[2];
                $to_be_confirmed = $array_notelateetc[3];
                $working_timetable_no = $result->working_timetable_no;
                $dtNow = new Carbon();
                if ($result->current_calc == '1' && $result->record_datetime < $dtNow) {    // 打刻時刻 < 現在時刻
                    $working_status = $result->working_status;
                }
                // 労働時間の計算
                if ($result->current_calc == '1') {     // 当日分である場合
                    $set_calcTimes_flg = false;
                    // ユーザー休暇区分判定用
                    $before_holiday_date = null;
                    $before_holiday_user_code = null;
                    $before_holiday_department_code = null;
                    $before_holiday_kubun = null;
                    // 計算セットフラグ　　calcTimes実行ならtrueにする
                    // ----------------------- 私用外出 -------------------------------------------
                    // 私用外出は複数ある可能性があるので私用外出計算は戻り時点で計算する。
                    if ($result->mode == Config::get('const.C005.missing_middle_time') && $missing_middle_time <> ''){
                        $array_add_missing_middle_time[] = $missing_middle_time;
                        $array_add_missing_middle_time_id[] = $missing_middle_time_id;
                        $array_add_missing_middle_editor_department_code[] = $missing_middle_editor_department_code;
                        $array_add_missing_middle_editor_department_name[] = $missing_middle_editor_department_name;
                        $array_add_missing_middle_editor_user_code[] = $missing_middle_editor_user_code;
                        $array_add_missing_middle_editor_user_name[] = $missing_middle_editor_user_name;
                        $array_add_missing_middle_time_positions[] = $missing_middle_time_positions;
                    }
                    if ($result->mode == Config::get('const.C005.missing_middle_return_time') && $missing_middle_return_time <> ''){
                        $array_add_missing_return_time[] = $missing_middle_return_time;
                        $array_add_missing_return_time_id[] = $missing_middle_return_time_id;
                        $array_add_missing_return_editor_department_code[] = $missing_return_editor_department_code;
                        $array_add_missing_return_editor_department_name[] = $missing_return_editor_department_name;
                        $array_add_missing_return_editor_user_code[] = $missing_return_editor_user_code;
                        $array_add_missing_return_editor_user_name[] = $missing_return_editor_user_name;
                        $array_add_missing_return_time_positions[] = $missing_return_time_positions;
                    }
                    if ($missing_middle_time <> '' && $missing_middle_return_time <> ''){
                        for ($i=0;$i<count($array_working_time_kubun);$i++) {
                            if (($array_working_time_kubun[$i] <> Config::get('const.C004.regular_working_breaks_time')) &&
                                ($array_working_time_kubun[$i] <> Config::get('const.C004.working_breaks_time')))  {
                                // // roundTimeByTimeStart implement
                                // $array_roundTimeByTimeStart = array (
                                //     'current_date' => $current_date,
                                //     'start_time' => $missing_middle_time,
                                //     'time_unit' => $result->time_unit,
                                //     'time_rounding' => $result->time_rounding,
                                //     'working_timetable_no' => $working_timetable_no,
                                //     'array_get_timetable_result' => $array_get_timetable_result
                                // );
                                // // roundTimeByTimeEnd implement
                                // $array_roundTimeByTimeEnd = array (
                                //     'current_date' => $current_date,
                                //     'end_time' => $missing_middle_return_time,
                                //     'time_unit' => $result->time_unit,
                                //     'time_rounding' => $result->time_rounding,
                                //     'working_timetable_no' => $working_timetable_no,
                                //     'array_get_timetable_result' => $array_get_timetable_result
                                // );
                                if (!$isemergency_time) {
                                    // roundTimeByTimeStart implement
                                    $array_roundTimeStart = array (
                                        'round_time' => $missing_middle_time,
                                        'time_unit' => $result->time_unit,
                                        'time_rounding' => $result->time_rounding
                                    );
                                    // roundTimeByTimeEnd implement
                                    $array_roundTimeEnd = array (
                                        'round_time' => $missing_middle_return_time,
                                        'time_unit' => $result->time_unit,
                                        'time_rounding' => $result->time_rounding
                                    );
                                    // $roundTimestart_time = $apicommon->roundTimeByTimeStart($array_roundTimeByTimeStart);
                                    // $roundTimeend_time = $apicommon->roundTimeByTimeEnd($array_roundTimeByTimeEnd);
                                    $roundTimestart_time = $apicommon->roundTimeStart($array_roundTimeStart);
                                    $roundTimeend_time = $apicommon->roundTimeEnd($array_roundTimeEnd);
                                } else {
                                    $roundTimestart_time = $missing_middle_time;
                                    $roundTimeend_time = $missing_middle_return_time;
                                }
                                Log::debug('        temp_working_time_dates  $roundTimestart_time = '.$roundTimestart_time);
                                Log::debug('        temp_working_time_dates  $roundTimeend_time = '.$roundTimeend_time);
                                // roundTimeByTimeEnd implement
                                $array_impl_calcTimes = array (
                                    'inc' => Config::get('const.INC_NO.missing_return'),
                                    'timetables' => $timetables,
                                    'working_timetable_no' => $working_timetable_no,
                                    'isemergency_time' => $isemergency_time,
                                    'working_time_kubun' => $array_working_time_kubun[$i],
                                    'current_date' => $current_date,
                                    'roundTimestart_time' => $roundTimestart_time,
                                    'roundTimeend_time' => $roundTimeend_time,
                                    'array_attendance_calc_time' => $array_attendance_calc_time,
                                    'array_missing_middle_time' => $array_missing_middle_time
                                );
                                $array_missing_middle_time[$i] += 
                                    $this->calcTimes($array_impl_calcTimes);
                                $set_calcTimes_flg = true;
                            }
                        }
                        // 私用外出時刻を初期化して次の計算準備
                        $missing_middle_time = '';
                        $missing_middle_return_time = '';
                    }
                    // ----------------------- 公用外出 -------------------------------------------
                    // 公用外出は複数ある可能性があるので公用外出計算は戻り時点で計算する。
                    if ($result->mode == Config::get('const.C005.public_going_out_time') && $public_going_out_time <> ''){
                        $array_add_public_going_out_time[] = $public_going_out_time;
                        $array_add_public_going_out_time_id[] = $public_going_out_time_id;
                        $array_add_public_going_out_editor_department_code[] = $public_going_out_editor_department_code;
                        $array_add_public_going_out_editor_department_name[] = $public_going_out_editor_department_name;
                        $array_add_public_going_out_editor_user_code[] = $public_going_out_editor_user_code;
                        $array_add_public_going_out_editor_user_name[] = $public_going_out_editor_user_name;
                        $array_add_public_going_out_time_positions[] = $public_going_out_time_positions;
                    }
                    if ($result->mode == Config::get('const.C005.public_going_out_return_time') && $public_going_out_return_time <> ''){
                        $array_add_public_return_time[] = $public_going_out_return_time;
                        $array_add_public_return_time_id[] = $public_going_out_return_time_id;
                        $array_add_public_return_editor_department_code[] = $public_return_editor_department_code;
                        $array_add_public_return_editor_department_name[] = $public_return_editor_department_name;
                        $array_add_public_return_editor_user_code[] = $public_return_editor_user_code;
                        $array_add_public_return_editor_user_name[] = $public_return_editor_user_name;
                        $array_add_public_return_time_positions[] = $public_return_time_positions;
                    }
                    if ($public_going_out_time <> '' && $public_going_out_return_time <> ''){
                        for ($i=0;$i<count($array_working_time_kubun);$i++) {
                            if (($array_working_time_kubun[$i] <> Config::get('const.C004.regular_working_breaks_time')) &&
                                ($array_working_time_kubun[$i] <> Config::get('const.C004.working_breaks_time')))  {
                                // // roundTimeByTimeStart implement
                                // $array_roundTimeByTimeStart = array (
                                //     'current_date' => $current_date,
                                //     'start_time' => $public_going_out_time,
                                //     'time_unit' => $result->time_unit,
                                //     'time_rounding' => $result->time_rounding,
                                //     'working_timetable_no' => $working_timetable_no,
                                //     'array_get_timetable_result' => $array_get_timetable_result
                                // );
                                // // roundTimeByTimeEnd implement
                                // $array_roundTimeByTimeEnd = array (
                                //     'current_date' => $current_date,
                                //     'end_time' => $public_going_out_return_time,
                                //     'time_unit' => $result->time_unit,
                                //     'time_rounding' => $result->time_rounding,
                                //     'working_timetable_no' => $working_timetable_no,
                                //     'array_get_timetable_result' => $array_get_timetable_result
                                // );
                                if (!$isemergency_time) {
                                    // roundTimeByTimeStart implement
                                    $array_roundTimeStart = array (
                                        'round_time' => $public_going_out_time,
                                        'time_unit' => $result->time_unit,
                                        'time_rounding' => $result->time_rounding
                                    );
                                    // roundTimeByTimeEnd implement
                                    $array_roundTimeEnd = array (
                                        'round_time' => $public_going_out_return_time,
                                        'time_unit' => $result->time_unit,
                                        'time_rounding' => $result->time_rounding
                                    );
                                    // $roundTimestart_time = $apicommon->roundTimeByTimeStart($array_roundTimeByTimeStart);
                                    // $roundTimeend_time = $apicommon->roundTimeByTimeEnd($array_roundTimeByTimeEnd);
                                    $roundTimestart_time = $apicommon->roundTimeStart($array_roundTimeStart);
                                    $roundTimeend_time = $apicommon->roundTimeEnd($array_roundTimeEnd);
                                } else {
                                    $roundTimestart_time = $public_going_out_time;
                                    $roundTimeend_time = $public_going_out_return_time;
                                }
                                Log::debug('        temp_working_time_dates  $roundTimestart_time = '.$roundTimestart_time);
                                Log::debug('        temp_working_time_dates  $roundTimeend_time = '.$roundTimeend_time);
                                // $array_impl_calcTimes = array (
                                //     'inc' => Config::get('const.INC_NO.public_going_out_return'),
                                //     'timetables' => $timetables,
                                //     'working_timetable_no' => $working_timetable_no,
                                //     'isemergency_time' => $isemergency_time,
                                //     'working_time_kubun' => $array_working_time_kubun[$i],
                                //     'current_date' => $current_date,
                                //     'roundTimestart_time' => $public_going_out_time,
                                //     'roundTimeend_time' => $public_going_out_return_time,
                                //     'array_attendance_calc_time' => $array_attendance_calc_time,
                                //     'array_missing_middle_time' => $array_public_going_out_time
                                // );
                                $array_impl_calcTimes = array (
                                    'inc' => Config::get('const.INC_NO.public_going_out_return'),
                                    'timetables' => $timetables,
                                    'working_timetable_no' => $working_timetable_no,
                                    'isemergency_time' => $isemergency_time,
                                    'working_time_kubun' => $array_working_time_kubun[$i],
                                    'current_date' => $current_date,
                                    'roundTimestart_time' => $roundTimestart_time,
                                    'roundTimeend_time' => $roundTimeend_time,
                                    'array_attendance_calc_time' => $array_attendance_calc_time,
                                    'array_missing_middle_time' => $array_public_going_out_time
                                );
                                $array_public_going_out_time[$i] += 
                                    $this->calcTimes($array_impl_calcTimes);
                                $set_calcTimes_flg = true;
                            }
                        }
                        // 公用外出時刻を初期化して次の計算準備
                        $public_going_out_time = '';
                        $public_going_out_return_time = '';
                    }
                    // ----------------------- 出勤 -------------------------------------------
                    if ($result->mode == Config::get('const.C005.attendance_time') && $attendance_time <> ''){
                        Log::debug('        temp_working_time_dates 出勤  $attendance_time = '.$attendance_time);
                        Log::debug('        temp_working_time_dates 出勤  $attendance_time_id = '.$attendance_time_id);
                        $array_add_attendance_time[] = $attendance_time;
                        $array_add_attendance_time_id[] = $attendance_time_id;
                        $array_add_attendance_editor_department_code[] = $attendance_editor_department_code;
                        $array_add_attendance_editor_department_name[] = $attendance_editor_department_name;
                        $array_add_attendance_editor_user_code[] = $attendance_editor_user_code;
                        $array_add_attendance_editor_user_name[] = $attendance_editor_user_name;
                        $array_add_attendance_time_positions[] = $attendance_time_positions;
                    }
                    // ----------------------- 退勤 -------------------------------------------
                    // 退勤データの場合計算開始
                    if ($result->mode == Config::get('const.C005.leaving_time') && $leaving_time <> ''){
                        Log::debug('        出勤退勤データ集計  count($array_working_time_kubun) = '.count($array_working_time_kubun));
                        $array_add_leaving_time[] = $leaving_time;
                        $array_add_leaving_time_id[] = $leaving_time_id;
                        $array_add_leaving_editor_department_code[] = $leaving_editor_department_code;
                        $array_add_leaving_editor_department_name[] = $leaving_editor_department_name;
                        $array_add_leaving_editor_user_code[] = $leaving_editor_user_code;
                        $array_add_leaving_editor_user_name[] = $leaving_editor_user_name;
                        $array_add_leaving_time_positions[] = $leaving_time_positions;
                        $index = (int)(Config::get('const.C004.regular_working_time'))-1;
                        for ($i=0;$i<count($array_working_time_kubun);$i++) {
                            if (($array_working_time_kubun[$i] <> Config::get('const.C004.regular_working_breaks_time')) &&
                                ($array_working_time_kubun[$i] <> Config::get('const.C004.working_breaks_time')))  {
                                // roundTimeByTimeStart implement
                                $array_roundTimeByTimeStart = array (
                                    'current_date' => $current_date,
                                    'start_time' => $attendance_time,
                                    'time_unit' => $result->time_unit,
                                    'time_rounding' => $result->time_rounding,
                                    'working_timetable_no' => $working_timetable_no,
                                    'array_get_timetable_result' => $array_get_timetable_result
                                );
                                // roundTimeByTimeEnd implement
                                $array_roundTimeByTimeEnd = array (
                                    'current_date' => $current_date,
                                    'end_time' => $leaving_time,
                                    'time_unit' => $result->time_unit,
                                    'time_rounding' => $result->time_rounding,
                                    'working_timetable_no' => $working_timetable_no,
                                    'array_get_timetable_result' => $array_get_timetable_result
                                );
                                $array_impl_calcTimes = array (
                                    'inc' => Config::get('const.INC_NO.attendace_leaving'),
                                    'timetables' => $timetables,
                                    'working_timetable_no' => $working_timetable_no,
                                    'isemergency_time' => $isemergency_time,
                                    'working_time_kubun' => $array_working_time_kubun[$i],
                                    'current_date' => $current_date,
                                    'roundTimestart_time' => $apicommon->roundTimeByTimeStart($array_roundTimeByTimeStart),
                                    'roundTimeend_time' => $apicommon->roundTimeByTimeEnd($array_roundTimeByTimeEnd),
                                    'array_attendance_calc_time' => $array_attendance_calc_time,
                                    'array_missing_middle_time' => $array_missing_middle_time
                                );
                                $array_attendance_calc_time[$i] += 
                                    $this->calcTimes($array_impl_calcTimes);
                                $set_calcTimes_flg = true;
                            } else {
                                // 休憩時間を別途計算する
                                Log::debug('        休憩時間を別途計算する 退勤 $array_roundTimeByTimeStart = '.count($array_roundTimeByTimeStart));
                                $this->not_employment_working += 
                                    $this->calcBreakTimes(
                                        $timetables,
                                        $working_timetable_no,
                                        $array_working_time_kubun[$i],
                                        $current_date,
                                        $apicommon->roundTimeByTimeStart($array_roundTimeByTimeStart),
                                        $apicommon->roundTimeByTimeEnd($array_roundTimeByTimeEnd)
                                    );
                            }
                        }
                        // 出勤退勤時刻を初期化して次の計算準備
                        $attendance_time = '';
                        $leaving_time = '';
                    }
                    // ----------------------- 緊急収集開始 -------------------------------------------
                    if ($result->mode == Config::get('const.C005.emergency_time') && $emergency_time <> ''){
                        Log::debug('        temp_working_time_dates 緊急収集開始  $emergency_time = '.$emergency_time);
                        Log::debug('        temp_working_time_dates 緊急収集開始  $emergency_time_id = '.$emergency_time_id);
                        $array_add_emergency_time[] = $emergency_time;
                        $array_add_emergency_time_id[] = $emergency_time_id;
                        $array_add_emergency_editor_department_code[] = $emergency_editor_department_code;
                        $array_add_emergency_editor_department_name[] = $emergency_editor_department_name;
                        $array_add_emergency_editor_user_code[] = $emergency_editor_user_code;
                        $array_add_emergency_editor_user_name[] = $emergency_editor_user_name;
                        $array_add_emergency_time_positions[] = $emergency_time_positions;
                    }
                    // ----------------------- 緊急収集終了 -------------------------------------------
                    // 緊急収集終了データの場合計算開始
                    if ($result->mode == Config::get('const.C005.emergency_return_time') && $emergency_return_time <> ''){
                        Log::debug('        緊急収集データ集計  count($array_working_time_kubun) = '.count($array_working_time_kubun));
                        $array_add_emergency_return_time[] = $emergency_return_time;
                        $array_add_emergency_return_time_id[] = $emergency_return_time_id;
                        $array_add_emergency_return_editor_department_code[] = $emergency_return_editor_department_code;
                        $array_add_emergency_return_editor_department_name[] = $emergency_return_editor_department_name;
                        $array_add_emergency_return_editor_user_code[] = $emergency_return_editor_user_code;
                        $array_add_emergency_return_editor_user_name[] = $emergency_return_editor_user_name;
                        $array_add_emergency_return_time_positions[] = $emergency_return_time_positions;
                        $index = (int)(Config::get('const.C004.regular_working_time'))-1;
                        for ($i=0;$i<count($array_working_time_kubun);$i++) {
                            if (($array_working_time_kubun[$i] <> Config::get('const.C004.regular_working_breaks_time')) &&
                                ($array_working_time_kubun[$i] <> Config::get('const.C004.working_breaks_time')))  {
                                $array_impl_calcTimes = array (
                                    'inc' => Config::get('const.INC_NO.emergency_return'),
                                    'timetables' => $timetables,
                                    'working_timetable_no' => $working_timetable_no,
                                    'isemergency_time' => $isemergency_time,
                                    'working_time_kubun' => $array_working_time_kubun[$i],
                                    'current_date' => $current_date,
                                    'roundTimestart_time' => $emergency_time,
                                    'roundTimeend_time' => $emergency_return_time,
                                    'array_attendance_calc_time' => $array_emergency_time,
                                    'array_missing_middle_time' => $array_missing_middle_time
                                );
                                $array_emergency_time[$i] += 
                                    $this->calcTimes($array_impl_calcTimes);
                                $set_calcTimes_flg = true;
                                $array_attendance_calc_time = $array_emergency_time;
                            } else {
                                Log::debug('        休憩時間を別途計算する 緊急収集終了 ');
                                // 休憩時間を別途計算する
                                $this->not_employment_working += 
                                    $this->calcBreakTimes(
                                        $timetables,
                                        $working_timetable_no,
                                        $array_working_time_kubun[$i],
                                        $current_date,
                                        $emergency_time,
                                        $emergency_return_time
                                    );
                            }
                        }
                        // 出勤退勤時刻を初期化して次の計算準備
                        $emergency_time = '';
                        $emergency_return_time = '';
                    }
                    // calcTimes計算対象外であった場合、
                    // 出勤していなく１日休暇設定されていればデータ作成 use_free_item ="1"
                    // 出勤していなく休暇設定されていなければデータ作成 use_free_item ="0"
                    //if (!$set_calcTimes_flg && isset($result->holiday_kubun)) {
                    Log::debug('        temp_working_time_datesデータ作成事前条件チェック $set_calcTimes_flg = '.$set_calcTimes_flg);
                    Log::debug('        temp_working_time_datesデータ作成事前条件チェック $mode_chk = '.$mode_chk);
                    if (!$set_calcTimes_flg && !$mode_chk) {
                        Log::debug('        temp_working_time_datesデータ作成開始 ');
                        Log::debug('            calcTimes計算対象外データ作成 =  '.$current_user_code);
                        Log::debug('                休暇区分  = '.$result->holiday_kubun);
                        Log::debug('        用途フリー項目 =  '.$result->use_free_item);
                        if ((!isset($result->use_free_item)) ||
                            (isset($result->use_free_item) &&
                                (substr($result->use_free_item, Config::get('const.USEFREEITEM.day_holiday'), 1) == "1"))) {
                            // setLeavingCollectPtn implement
                            $array_impl_addTempWorkingTimeDate = array (
                                'target_date' => $current_date,
                                'target_user_code' => $current_user_code,
                                'target_department_code' => $current_department_code,
                                'target_result' => $current_result,
                                'note' => $note,
                                'working_status' => $working_status,
                                'timetables' => $timetables,
                                'array_attendance_calc_time' => $array_attendance_calc_time,
                                'array_missing_middle_time' => $array_missing_middle_time,
                                'array_public_going_out_time' => $array_public_going_out_time,
                                'array_emergency_time' => $array_emergency_time,
                                'array_add_attendance_time' => $array_add_attendance_time,
                                'array_add_attendance_time_id' => $array_add_attendance_time_id,
                                'array_add_attendance_editor_department_code' => $array_add_attendance_editor_department_code,
                                'array_add_attendance_editor_department_name' => $array_add_attendance_editor_department_name,
                                'array_add_attendance_editor_user_code' => $array_add_attendance_editor_user_code,
                                'array_add_attendance_editor_user_name' => $array_add_attendance_editor_user_name,
                                'array_add_attendance_time_positions' => $array_add_attendance_time_positions,
                                'array_add_leaving_time' => $array_add_leaving_time,
                                'array_add_leaving_time_id' => $array_add_leaving_time_id,
                                'array_add_leaving_editor_department_code' => $array_add_leaving_editor_department_code,
                                'array_add_leaving_editor_department_name' => $array_add_leaving_editor_department_name,
                                'array_add_leaving_editor_user_code' => $array_add_leaving_editor_user_code,
                                'array_add_leaving_editor_user_name' => $array_add_leaving_editor_user_name,
                                'array_add_leaving_time_positions' => $array_add_leaving_time_positions,
                                'array_add_missing_middle_time' => $array_add_missing_middle_time,
                                'array_add_missing_middle_time_id' => $array_add_missing_middle_time_id,
                                'array_add_missing_middle_editor_department_code' => $array_add_missing_middle_editor_department_code,
                                'array_add_missing_middle_editor_department_name' => $array_add_missing_middle_editor_department_name,
                                'array_add_missing_middle_editor_user_code' => $array_add_missing_middle_editor_user_code,
                                'array_add_missing_middle_editor_user_name' => $array_add_missing_middle_editor_user_name,
                                'array_add_missing_middle_time_positions' => $array_add_missing_middle_time_positions,
                                'array_add_missing_return_time' => $array_add_missing_return_time,
                                'array_add_missing_return_time_id' => $array_add_missing_return_time_id,
                                'array_add_missing_return_editor_department_code' => $array_add_missing_return_editor_department_code,
                                'array_add_missing_return_editor_department_name' => $array_add_missing_return_editor_department_name,
                                'array_add_missing_return_editor_user_code' => $array_add_missing_return_editor_user_code,
                                'array_add_missing_return_editor_user_name' => $array_add_missing_return_editor_user_name,
                                'array_add_missing_return_time_positions' => $array_add_missing_return_time_positions,
                                'array_add_public_going_out_time' => $array_add_public_going_out_time,
                                'array_add_public_going_out_time_id' => $array_add_public_going_out_time_id,
                                'array_add_public_going_out_editor_department_code' => $array_add_public_going_out_editor_department_code,
                                'array_add_public_going_out_editor_department_name' => $array_add_public_going_out_editor_department_name,
                                'array_add_public_going_out_editor_user_code' => $array_add_public_going_out_editor_user_code,
                                'array_add_public_going_out_editor_user_name' => $array_add_public_going_out_editor_user_name,
                                'array_add_public_going_out_time_positions' => $array_add_public_going_out_time_positions,
                                'array_add_public_return_time' => $array_add_public_return_time,
                                'array_add_public_return_time_id' => $array_add_public_return_time_id,
                                'array_add_public_return_editor_department_code' => $array_add_public_return_editor_department_code,
                                'array_add_public_return_editor_department_name' => $array_add_public_return_editor_department_name,
                                'array_add_public_return_editor_user_code' => $array_add_public_return_editor_user_code,
                                'array_add_public_return_editor_user_name' => $array_add_public_return_editor_user_name,
                                'array_add_public_return_time_positions' => $array_add_public_return_time_positions,
                                'array_break_worktimetable_result' => $array_break_worktimetable_result,
                                'array_add_emergency_time' => $array_add_emergency_time,
                                'array_add_emergency_time_id' => $array_add_emergency_time_id,
                                'array_add_emergency_editor_department_code' => $array_add_emergency_editor_department_code,
                                'array_add_emergency_editor_department_name' => $array_add_emergency_editor_department_name,
                                'array_add_emergency_editor_user_code' => $array_add_emergency_editor_user_code,
                                'array_add_emergency_editor_user_name' => $array_add_emergency_editor_user_name,
                                'array_add_emergency_time_positions' => $array_add_emergency_time_positions,
                                'array_add_emergency_return_time' => $array_add_emergency_return_time,
                                'array_add_emergency_return_time_id' => $array_add_emergency_return_time_id,
                                'array_add_emergency_return_editor_department_code' => $array_add_emergency_return_editor_department_code,
                                'array_add_emergency_return_editor_department_name' => $array_add_emergency_return_editor_department_name,
                                'array_add_emergency_return_editor_user_code' => $array_add_emergency_return_editor_user_code,
                                'array_add_emergency_return_editor_user_name' => $array_add_emergency_return_editor_user_name,
                                'array_add_emergency_return_time_positions' => $array_add_emergency_return_time_positions,
                                'array_get_timetable_result' => $array_get_timetable_result
                            );

                            $add_result = $this->addTempWorkingTimeDate($array_impl_addTempWorkingTimeDate);
                            // 次データ計算事前処理
                            $array_result_NextData =
                                $this->calcTempWorkingTimeDateNextData(
                                    $array_working_time_kubun,
                                    $result
                                );
                            $array_attendance_calc_time = $array_result_NextData['array_attendance_calc_time'];
                            $array_missing_middle_time = $array_result_NextData['array_missing_middle_time'];
                            $array_public_going_out_time = $array_result_NextData['array_public_going_out_time'];
                            $array_emergency_time = $array_result_NextData['array_emergency_time'];
                            $array_add_attendance_time = $array_result_NextData['array_add_attendance_time'];
                            $array_add_leaving_time = $array_result_NextData['array_add_leaving_time'];
                            $array_add_missing_middle_time = $array_result_NextData['array_add_missing_middle_time'];
                            $array_add_missing_return_time = $array_result_NextData['array_add_missing_return_time'];
                            $array_add_public_going_out_time = $array_result_NextData['array_add_public_going_out_time'];
                            $array_add_public_return_time = $array_result_NextData['array_add_public_return_time'];
                            $array_add_emergency_time = $array_result_NextData['array_add_emergency_time'];
                            $array_add_emergency_return_time = $array_result_NextData['array_add_emergency_return_time'];
                            $array_add_attendance_time_positions = $array_result_NextData['array_add_attendance_time_positions'];
                            $array_add_leaving_time_positions = $array_result_NextData['array_add_leaving_time_positions'];
                            $array_add_missing_middle_time_positions = $array_result_NextData['array_add_missing_middle_time_positions'];
                            $array_add_missing_return_time_positions = $array_result_NextData['array_add_missing_return_time_positions'];
                            $array_add_public_going_out_time_positions = $array_result_NextData['array_add_public_going_out_time_positions'];
                            $array_add_public_return_time_positions = $array_result_NextData['array_add_public_return_time_positions'];
                            $array_add_emergency_time_positions = $array_result_NextData['array_add_emergency_time_positions'];
                            $array_add_emergency_return_time_positions = $array_result_NextData['array_add_emergency_return_time_positions'];
                            $array_add_attendance_time_id = $array_result_NextData['array_add_attendance_time_id'];
                            $array_add_attendance_editor_department_code = $array_result_NextData['array_add_attendance_editor_department_code'];
                            $array_add_attendance_editor_department_name = $array_result_NextData['array_add_attendance_editor_department_name'];
                            $array_add_attendance_editor_user_code = $array_result_NextData['array_add_attendance_editor_user_code'];
                            $array_add_attendance_editor_user_name = $array_result_NextData['array_add_attendance_editor_user_name'];
                            $array_add_leaving_time_id = $array_result_NextData['array_add_leaving_time_id'];
                            $array_add_leaving_editor_department_code = $array_result_NextData['array_add_leaving_editor_department_code'];
                            $array_add_leaving_editor_department_name = $array_result_NextData['array_add_leaving_editor_department_name'];
                            $array_add_leaving_editor_user_code = $array_result_NextData['array_add_leaving_editor_user_code'];
                            $array_add_leaving_editor_user_name = $array_result_NextData['array_add_leaving_editor_user_name'];
                            $array_add_missing_middle_time_id = $array_result_NextData['array_add_missing_middle_time_id'];
                            $array_add_missing_middle_editor_department_code = $array_result_NextData['array_add_missing_middle_editor_department_code'];
                            $array_add_missing_middle_editor_department_name = $array_result_NextData['array_add_missing_middle_editor_department_name'];
                            $array_add_missing_middle_editor_user_code = $array_result_NextData['array_add_missing_middle_editor_user_code'];
                            $array_add_missing_middle_editor_user_name = $array_result_NextData['array_add_missing_middle_editor_user_name'];
                            $array_add_missing_return_time_id = $array_result_NextData['array_add_missing_return_time_id'];
                            $array_add_missing_return_editor_department_code = $array_result_NextData['array_add_missing_return_editor_department_code'];
                            $array_add_missing_return_editor_department_name = $array_result_NextData['array_add_missing_return_editor_department_name'];
                            $array_add_missing_return_editor_user_code = $array_result_NextData['array_add_missing_return_editor_user_code'];
                            $array_add_missing_return_editor_user_name = $array_result_NextData['array_add_missing_return_editor_user_name'];
                            $array_add_public_going_out_time_id = $array_result_NextData['array_add_public_going_out_time_id'];
                            $array_add_public_going_out_editor_department_code = $array_result_NextData['array_add_public_going_out_editor_department_code'];
                            $array_add_public_going_out_editor_department_name = $array_result_NextData['array_add_public_going_out_editor_department_name'];
                            $array_add_public_going_out_editor_user_code = $array_result_NextData['array_add_public_going_out_editor_user_code'];
                            $array_add_public_going_out_editor_user_name = $array_result_NextData['array_add_public_going_out_editor_user_name'];
                            $array_add_public_return_time_id = $array_result_NextData['array_add_public_return_time_id'];
                            $array_add_public_return_editor_department_code = $array_result_NextData['array_add_public_return_editor_department_code'];
                            $array_add_public_return_editor_department_name = $array_result_NextData['array_add_public_return_editor_department_name'];
                            $array_add_public_return_editor_user_code = $array_result_NextData['array_add_public_return_editor_user_code'];
                            $array_add_public_return_editor_user_name = $array_result_NextData['array_add_public_return_editor_user_name'];
                            $array_add_emergency_time_id = $array_result_NextData['array_add_emergency_time_id'];
                            $array_add_emergency_editor_department_code = $array_result_NextData['array_add_emergency_editor_department_code'];
                            $array_add_emergency_editor_department_name = $array_result_NextData['array_add_emergency_editor_department_name'];
                            $array_add_emergency_editor_user_code = $array_result_NextData['array_add_emergency_editor_user_code'];
                            $array_add_emergency_editor_user_name = $array_result_NextData['array_add_emergency_editor_user_name'];
                            $array_add_emergency_return_time_id = $array_result_NextData['array_add_emergency_return_time_id'];
                            $array_add_emergency_return_editor_department_code = $array_result_NextData['array_add_emergency_return_editor_department_code'];
                            $array_add_emergency_return_editor_department_name = $array_result_NextData['array_add_emergency_return_editor_department_name'];
                            $array_add_emergency_return_editor_user_code = $array_result_NextData['array_add_emergency_return_editor_user_code'];
                            $array_add_emergency_return_editor_user_name = $array_result_NextData['array_add_emergency_return_editor_user_name'];
                            $attendance_time = $array_result_NextData['attendance_time'];
                            $leaving_time = $array_result_NextData['leaving_time'];
                            $missing_middle_time = $array_result_NextData['missing_middle_time'];
                            $missing_middle_return_time = $array_result_NextData['missing_middle_return_time'];
                            $public_going_out_time = $array_result_NextData['public_going_out_time'];
                            $public_going_out_return_time = $array_result_NextData['public_going_out_return_time'];
                            $emergency_time = $array_result_NextData['emergency_time'];
                            $emergency_return_time = $array_result_NextData['emergency_return_time'];
                            $attendance_time_positions = $array_result_NextData['attendance_time_positions'];
                            $leaving_time_positions = $array_result_NextData['leaving_time_positions'];
                            $missing_middle_time_positions = $array_result_NextData['missing_middle_time_positions'];
                            $missing_return_time_positions = $array_result_NextData['missing_return_time_positions'];
                            $public_going_out_time_positions = $array_result_NextData['public_going_out_time_positions'];
                            $public_return_time_positions = $array_result_NextData['public_return_time_positions'];
                            $emergency_time_positions = $array_result_NextData['emergency_time_positions'];
                            $emergency_return_time_positions = $array_result_NextData['emergency_return_time_positions'];
                            $attendance_time_id = $array_result_NextData['attendance_time_id'];
                            $leaving_time_id = $array_result_NextData['leaving_time_id'];
                            $missing_middle_time_id = $array_result_NextData['missing_middle_time_id'];
                            $missing_middle_return_time_id = $array_result_NextData['missing_middle_return_time_id'];
                            $public_going_out_time_id = $array_result_NextData['public_going_out_time_id'];
                            $public_going_out_return_time_id = $array_result_NextData['public_going_out_return_time_id'];
                            $emergency_time_id = $array_result_NextData['emergency_time_id'];
                            $emergency_return_time_id = $array_result_NextData['emergency_return_time_id'];
                            $attendance_editor_department_code = $array_result_NextData['attendance_editor_department_code'];
                            $attendance_editor_department_name = $array_result_NextData['attendance_editor_department_name'];
                            $attendance_editor_user_code = $array_result_NextData['attendance_editor_user_code'];
                            $attendance_editor_user_name = $array_result_NextData['attendance_editor_user_name'];
                            $leaving_editor_department_code = $array_result_NextData['leaving_editor_department_code'];
                            $leaving_editor_department_name = $array_result_NextData['leaving_editor_department_name'];
                            $leaving_editor_user_code = $array_result_NextData['leaving_editor_user_code'];
                            $leaving_editor_user_name = $array_result_NextData['leaving_editor_user_name'];
                            $missing_middle_editor_department_code = $array_result_NextData['missing_middle_editor_department_code'];
                            $missing_middle_editor_department_name = $array_result_NextData['missing_middle_editor_department_name'];
                            $missing_middle_editor_user_code = $array_result_NextData['missing_middle_editor_user_code'];
                            $missing_middle_editor_user_name = $array_result_NextData['missing_middle_editor_user_name'];
                            $missing_return_editor_department_code = $array_result_NextData['missing_return_editor_department_code'];
                            $missing_return_editor_department_name = $array_result_NextData['missing_return_editor_department_name'];
                            $missing_return_editor_user_code = $array_result_NextData['missing_return_editor_user_code'];
                            $missing_return_editor_user_name = $array_result_NextData['missing_return_editor_user_name'];
                            $public_going_out_editor_department_code = $array_result_NextData['public_going_out_editor_department_code'];
                            $public_going_out_editor_department_name = $array_result_NextData['public_going_out_editor_department_name'];
                            $public_going_out_editor_user_code = $array_result_NextData['public_going_out_editor_user_code'];
                            $public_going_out_editor_user_name = $array_result_NextData['public_going_out_editor_user_name'];
                            $public_return_editor_department_code = $array_result_NextData['public_return_editor_department_code'];
                            $public_return_editor_department_name = $array_result_NextData['public_return_editor_department_name'];
                            $public_return_editor_user_code = $array_result_NextData['public_return_editor_user_code'];
                            $public_return_editor_user_name = $array_result_NextData['public_return_editor_user_name'];
                            $emergency_editor_department_code = $array_result_NextData['emergency_editor_department_code'];
                            $emergency_editor_department_name = $array_result_NextData['emergency_editor_department_name'];
                            $emergency_editor_user_code = $array_result_NextData['emergency_editor_user_code'];
                            $emergency_editor_user_name = $array_result_NextData['emergency_editor_user_name'];
                            $emergency_return_editor_department_code = $array_result_NextData['emergency_return_editor_department_code'];
                            $emergency_return_editor_department_name = $array_result_NextData['emergency_return_editor_department_name'];
                            $emergency_return_editor_user_code = $array_result_NextData['emergency_return_editor_user_code'];
                            $emergency_return_editor_user_name = $array_result_NextData['emergency_return_editor_user_name'];
                            // 同じ値にする
                            $before_date = $current_date;
                            $before_department_code = $current_department_code;
                            $before_user_code = $current_user_code; 
                            $before_result = $result;
                            $this->not_employment_working = 0;
                            $working_status = $result->working_status;
                            $note = '';
                            $late = '';
                            $leave_early = '';
                            $to_be_confirmed = '';
                            $calc_nobreak_cnt = 0;
                            $array_notelateetc = $this->setNoteLateEtc($result);
                            $note .= $array_notelateetc[0];
                            $late = $array_notelateetc[1];
                            $leave_early = $array_notelateetc[2];
                            $to_be_confirmed = $array_notelateetc[3];
                            $calc_nobreak_cnt++;
                            // ユーザー休暇区分判定用
                            $before_holiday_set = true;
                            $before_holiday_date = $current_date;
                            $before_holiday_user_code = $current_user_code;
                            $before_holiday_department_code = $current_department_code;
                            $before_holiday_kubun = $result->holiday_kubun;
                        }
                    }
                } else {
                    // 時刻だけ設定する
                    Log::debug('    当日分計算対象外');
                    // ----------------------- 私用外出 -------------------------------------------
                    Log::debug('    私用外出打刻時刻 = '.$missing_middle_time);
                    if ($result->mode == Config::get('const.C005.missing_middle_time') && $missing_middle_time <> ''){
                        $array_add_missing_middle_time[] = $missing_middle_time;
                        $array_add_missing_middle_time_id[] = $missing_middle_time_id;
                        $array_add_missing_middle_editor_department_code[] = $missing_middle_editor_department_code;
                        $array_add_missing_middle_editor_department_name[] = $missing_middle_editor_department_name;
                        $array_add_missing_middle_editor_user_code[] = $missing_middle_editor_user_code;
                        $array_add_missing_middle_editor_user_name[] = $missing_middle_editor_user_name;
                        $array_add_missing_middle_time_positions[] = $missing_middle_time_positions;
                    }
                    Log::debug('    私用外出戻り打刻時刻 = '.$missing_middle_return_time);
                    if ($result->mode == Config::get('const.C005.missing_middle_return_time') && $missing_middle_return_time <> ''){
                        $array_add_missing_return_time[] = $missing_middle_return_time;
                        $array_add_missing_return_time_id[] = $missing_middle_return_time_id;
                        $array_add_missing_return_editor_department_code[] = $missing_return_editor_department_code;
                        $array_add_missing_return_editor_department_name[] = $missing_return_editor_department_name;
                        $array_add_missing_return_editor_user_code[] = $missing_return_editor_user_code;
                        $array_add_missing_return_editor_user_name[] = $missing_return_editor_user_name;
                        $array_add_missing_return_time_positions[] = $missing_return_time_positions;
                    }
                    // ----------------------- 公用外出 -------------------------------------------
                    Log::debug('    公用外出打刻時刻 = '.$public_going_out_time);
                    if ($result->mode == Config::get('const.C005.public_going_out_time') && $public_going_out_time <> ''){
                        $array_add_public_going_out_time[] = $public_going_out_time;
                        $array_add_public_going_out_time_id[] = $public_going_out_time_id;
                        $array_add_public_going_out_editor_department_code[] = $public_going_out_editor_department_code;
                        $array_add_public_going_out_editor_department_name[] = $public_going_out_editor_department_name;
                        $array_add_public_going_out_editor_user_code[] = $public_going_out_editor_user_code;
                        $array_add_public_going_out_editor_user_name[] = $public_going_out_editor_user_name;
                        $array_add_public_going_out_time_positions[] = $public_going_out_time_positions;
                    }
                    Log::debug('    公用外出戻り打刻時刻 = '.$public_going_out_return_time);
                    if ($result->mode == Config::get('const.C005.public_going_out_return_time') && $public_going_out_return_time <> ''){
                        $array_add_public_return_time[] = $public_going_out_return_time;
                        $array_add_public_return_time_id[] = $public_going_out_return_time_id;
                        $array_add_public_return_editor_department_code[] = $public_return_editor_department_code;
                        $array_add_public_return_editor_department_name[] = $public_return_editor_department_name;
                        $array_add_public_return_editor_user_code[] = $public_return_editor_user_code;
                        $array_add_public_return_editor_user_name[] = $public_return_editor_user_name;
                        $array_add_public_return_time_positions[] = $public_return_time_positions;
                    }
                    // ----------------------- 出勤 -------------------------------------------
                    Log::debug('    出勤打刻時刻 = '.$attendance_time);
                    if ($result->mode == Config::get('const.C005.attendance_time') && $attendance_time <> ''){
                        $array_add_attendance_time[] = $attendance_time;
                        $array_add_attendance_time_id[] = $attendance_time_id;
                        $array_add_attendance_editor_department_code[] = $attendance_editor_department_code;
                        $array_add_attendance_editor_department_name[] = $attendance_editor_department_name;
                        $array_add_attendance_editor_user_code[] = $attendance_editor_user_code;
                        $array_add_attendance_editor_user_name[] = $attendance_editor_user_name;
                        $array_add_attendance_time_positions[] = $attendance_time_positions;
                    }
                    // ----------------------- 退勤 -------------------------------------------
                    Log::debug('    退勤打刻時刻 = '.$leaving_time);
                    if ($result->mode == Config::get('const.C005.leaving_time') && $leaving_time <> ''){
                        $array_add_leaving_time[] = $leaving_time;
                        $array_add_leaving_time_id[] = $leaving_time_id;
                        $array_add_leaving_editor_department_code[] = $leaving_editor_department_code;
                        $array_add_leaving_editor_department_name[] = $leaving_editor_department_name;
                        $array_add_leaving_editor_user_code[] = $leaving_editor_user_code;
                        $array_add_leaving_editor_user_name[] = $leaving_editor_user_name;
                        $array_add_leaving_time_positions[] = $leaving_time_positions;
                    }
                    // 前のデータが計算対象であれば出力する
                    // 計算セットフラグ
                    Log::debug('        前のデータが計算対象であれば出力する $set_calcTimes_flg = '.$set_calcTimes_flg);
                    if ($set_calcTimes_flg) {
                        Log::debug('        temp_working_time_datesデータ作成開始 ');
                        Log::debug('            １個前のユーザーを登録 '.$before_user_code);
                        // ユーザー労働時間登録(１個前のユーザーを登録する)
                        // setLeavingCollectPtn implement
                        $array_impl_addTempWorkingTimeDate = array (
                            'target_date' => $before_date,
                            'target_user_code' => $before_user_code,
                            'target_department_code' => $before_department_code,
                            'target_result' => $before_result,
                            'note' => $note,
                            'working_status' => $working_status,
                            'timetables' => $timetables,
                            'array_attendance_calc_time' => $array_attendance_calc_time,
                            'array_missing_middle_time' => $array_missing_middle_time,
                            'array_public_going_out_time' => $array_public_going_out_time,
                            'array_emergency_time' => $array_emergency_time,
                            'array_add_attendance_time' => $array_add_attendance_time,
                            'array_add_attendance_time_id' => $array_add_attendance_time_id,
                            'array_add_attendance_editor_department_code' => $array_add_attendance_editor_department_code,
                            'array_add_attendance_editor_department_name' => $array_add_attendance_editor_department_name,
                            'array_add_attendance_editor_user_code' => $array_add_attendance_editor_user_code,
                            'array_add_attendance_editor_user_name' => $array_add_attendance_editor_user_name,
                            'array_add_attendance_time_positions' => $array_add_attendance_time_positions,
                            'array_add_leaving_time' => $array_add_leaving_time,
                            'array_add_leaving_time_id' => $array_add_leaving_time_id,
                            'array_add_leaving_editor_department_code' => $array_add_leaving_editor_department_code,
                            'array_add_leaving_editor_department_name' => $array_add_leaving_editor_department_name,
                            'array_add_leaving_editor_user_code' => $array_add_leaving_editor_user_code,
                            'array_add_leaving_editor_user_name' => $array_add_leaving_editor_user_name,
                            'array_add_leaving_time_positions' => $array_add_leaving_time_positions,
                            'array_add_missing_middle_time' => $array_add_missing_middle_time,
                            'array_add_missing_middle_time_id' => $array_add_missing_middle_time_id,
                            'array_add_missing_middle_editor_department_code' => $array_add_missing_middle_editor_department_code,
                            'array_add_missing_middle_editor_department_name' => $array_add_missing_middle_editor_department_name,
                            'array_add_missing_middle_editor_user_code' => $array_add_missing_middle_editor_user_code,
                            'array_add_missing_middle_editor_user_name' => $array_add_missing_middle_editor_user_name,
                            'array_add_missing_middle_time_positions' => $array_add_missing_middle_time_positions,
                            'array_add_missing_return_time' => $array_add_missing_return_time,
                            'array_add_missing_return_time_id' => $array_add_missing_return_time_id,
                            'array_add_missing_return_editor_department_code' => $array_add_missing_return_editor_department_code,
                            'array_add_missing_return_editor_department_name' => $array_add_missing_return_editor_department_name,
                            'array_add_missing_return_editor_user_code' => $array_add_missing_return_editor_user_code,
                            'array_add_missing_return_editor_user_name' => $array_add_missing_return_editor_user_name,
                            'array_add_missing_return_time_positions' => $array_add_missing_return_time_positions,
                            'array_add_public_going_out_time' => $array_add_public_going_out_time,
                            'array_add_public_going_out_time_id' => $array_add_public_going_out_time_id,
                            'array_add_public_going_out_editor_department_code' => $array_add_public_going_out_editor_department_code,
                            'array_add_public_going_out_editor_department_name' => $array_add_public_going_out_editor_department_name,
                            'array_add_public_going_out_editor_user_code' => $array_add_public_going_out_editor_user_code,
                            'array_add_public_going_out_editor_user_name' => $array_add_public_going_out_editor_user_name,
                            'array_add_public_going_out_time_positions' => $array_add_public_going_out_time_positions,
                            'array_add_public_return_time' => $array_add_public_return_time,
                            'array_add_public_return_time_id' => $array_add_public_return_time_id,
                            'array_add_public_return_editor_department_code' => $array_add_public_return_editor_department_code,
                            'array_add_public_return_editor_department_name' => $array_add_public_return_editor_department_name,
                            'array_add_public_return_editor_user_code' => $array_add_public_return_editor_user_code,
                            'array_add_public_return_editor_user_name' => $array_add_public_return_editor_user_name,
                            'array_add_public_return_time_positions' => $array_add_public_return_time_positions,
                            'array_break_worktimetable_result' => $array_break_worktimetable_result,
                            'array_add_emergency_time' => $array_add_emergency_time,
                            'array_add_emergency_time_id' => $array_add_emergency_time_id,
                            'array_add_emergency_editor_department_code' => $array_add_emergency_editor_department_code,
                            'array_add_emergency_editor_department_name' => $array_add_emergency_editor_department_name,
                            'array_add_emergency_editor_user_code' => $array_add_emergency_editor_user_code,
                            'array_add_emergency_editor_user_name' => $array_add_emergency_editor_user_name,
                            'array_add_emergency_time_positions' => $array_add_emergency_time_positions,
                            'array_add_emergency_return_time' => $array_add_emergency_return_time,
                            'array_add_emergency_return_time_id' => $array_add_emergency_return_time_id,
                            'array_add_emergency_return_editor_department_code' => $array_add_emergency_return_editor_department_code,
                            'array_add_emergency_return_editor_department_name' => $array_add_emergency_return_editor_department_name,
                            'array_add_emergency_return_editor_user_code' => $array_add_emergency_return_editor_user_code,
                            'array_add_emergency_return_editor_user_name' => $array_add_emergency_return_editor_user_name,
                            'array_add_emergency_return_time_positions' => $array_add_emergency_return_time_positions,
                            'array_get_timetable_result' => $array_get_timetable_result
                    );
                        $add_result = $this->addTempWorkingTimeDate($array_impl_addTempWorkingTimeDate);
                    }
                    // 次データ計算事前処理
                    $array_result_NextData =
                        $this->calcTempWorkingTimeDateNextData(
                            $array_working_time_kubun,
                            $result
                        );
                    $array_attendance_calc_time = $array_result_NextData['array_attendance_calc_time'];
                    $array_missing_middle_time = $array_result_NextData['array_missing_middle_time'];
                    $array_public_going_out_time = $array_result_NextData['array_public_going_out_time'];
                    $array_emergency_time = $array_result_NextData['array_emergency_time'];
                    $array_add_attendance_time = $array_result_NextData['array_add_attendance_time'];
                    $array_add_leaving_time = $array_result_NextData['array_add_leaving_time'];
                    $array_add_missing_middle_time = $array_result_NextData['array_add_missing_middle_time'];
                    $array_add_missing_return_time = $array_result_NextData['array_add_missing_return_time'];
                    $array_add_public_going_out_time = $array_result_NextData['array_add_public_going_out_time'];
                    $array_add_public_return_time = $array_result_NextData['array_add_public_return_time'];
                    $array_add_emergency_time = $array_result_NextData['array_add_emergency_time'];
                    $array_add_emergency_return_time = $array_result_NextData['array_add_emergency_return_time'];
                    $array_add_attendance_time_positions = $array_result_NextData['array_add_attendance_time_positions'];
                    $array_add_leaving_time_positions = $array_result_NextData['array_add_leaving_time_positions'];
                    $array_add_missing_middle_time_positions = $array_result_NextData['array_add_missing_middle_time_positions'];
                    $array_add_missing_return_time_positions = $array_result_NextData['array_add_missing_return_time_positions'];
                    $array_add_public_going_out_time_positions = $array_result_NextData['array_add_public_going_out_time_positions'];
                    $array_add_public_return_time_positions = $array_result_NextData['array_add_public_return_time_positions'];
                    $array_add_emergency_time_positions = $array_result_NextData['array_add_emergency_time_positions'];
                    $array_add_emergency_return_time_positions = $array_result_NextData['array_add_emergency_return_time_positions'];
                    $array_add_attendance_time_id = $array_result_NextData['array_add_attendance_time_id'];
                    $array_add_attendance_editor_department_code = $array_result_NextData['array_add_attendance_editor_department_code'];
                    $array_add_attendance_editor_department_name = $array_result_NextData['array_add_attendance_editor_department_name'];
                    $array_add_attendance_editor_user_code = $array_result_NextData['array_add_attendance_editor_user_code'];
                    $array_add_attendance_editor_user_name = $array_result_NextData['array_add_attendance_editor_user_name'];
                    $array_add_leaving_time_id = $array_result_NextData['array_add_leaving_time_id'];
                    $array_add_leaving_editor_department_code = $array_result_NextData['array_add_leaving_editor_department_code'];
                    $array_add_leaving_editor_department_name = $array_result_NextData['array_add_leaving_editor_department_name'];
                    $array_add_leaving_editor_user_code = $array_result_NextData['array_add_leaving_editor_user_code'];
                    $array_add_leaving_editor_user_name = $array_result_NextData['array_add_leaving_editor_user_name'];
                    $array_add_missing_middle_time_id = $array_result_NextData['array_add_missing_middle_time_id'];
                    $array_add_missing_middle_editor_department_code = $array_result_NextData['array_add_missing_middle_editor_department_code'];
                    $array_add_missing_middle_editor_department_name = $array_result_NextData['array_add_missing_middle_editor_department_name'];
                    $array_add_missing_middle_editor_user_code = $array_result_NextData['array_add_missing_middle_editor_user_code'];
                    $array_add_missing_middle_editor_user_name = $array_result_NextData['array_add_missing_middle_editor_user_name'];
                    $array_add_missing_return_time_id = $array_result_NextData['array_add_missing_return_time_id'];
                    $array_add_missing_return_editor_department_code = $array_result_NextData['array_add_missing_return_editor_department_code'];
                    $array_add_missing_return_editor_department_name = $array_result_NextData['array_add_missing_return_editor_department_name'];
                    $array_add_missing_return_editor_user_code = $array_result_NextData['array_add_missing_return_editor_user_code'];
                    $array_add_missing_return_editor_user_name = $array_result_NextData['array_add_missing_return_editor_user_name'];
                    $array_add_public_going_out_time_id = $array_result_NextData['array_add_public_going_out_time_id'];
                    $array_add_public_going_out_editor_department_code = $array_result_NextData['array_add_public_going_out_editor_department_code'];
                    $array_add_public_going_out_editor_department_name = $array_result_NextData['array_add_public_going_out_editor_department_name'];
                    $array_add_public_going_out_editor_user_code = $array_result_NextData['array_add_public_going_out_editor_user_code'];
                    $array_add_public_going_out_editor_user_name = $array_result_NextData['array_add_public_going_out_editor_user_name'];
                    $array_add_public_return_time_id = $array_result_NextData['array_add_public_return_time_id'];
                    $array_add_public_return_editor_department_code = $array_result_NextData['array_add_public_return_editor_department_code'];
                    $array_add_public_return_editor_department_name = $array_result_NextData['array_add_public_return_editor_department_name'];
                    $array_add_public_return_editor_user_code = $array_result_NextData['array_add_public_return_editor_user_code'];
                    $array_add_public_return_editor_user_name = $array_result_NextData['array_add_public_return_editor_user_name'];
                    $array_add_emergency_time_id = $array_result_NextData['array_add_emergency_time_id'];
                    $array_add_emergency_editor_department_code = $array_result_NextData['array_add_emergency_editor_department_code'];
                    $array_add_emergency_editor_department_name = $array_result_NextData['array_add_emergency_editor_department_name'];
                    $array_add_emergency_editor_user_code = $array_result_NextData['array_add_emergency_editor_user_code'];
                    $array_add_emergency_editor_user_name = $array_result_NextData['array_add_emergency_editor_user_name'];
                    $array_add_emergency_return_time_id = $array_result_NextData['array_add_emergency_return_time_id'];
                    $array_add_emergency_return_editor_department_code = $array_result_NextData['array_add_emergency_return_editor_department_code'];
                    $array_add_emergency_return_editor_department_name = $array_result_NextData['array_add_emergency_return_editor_department_name'];
                    $array_add_emergency_return_editor_user_code = $array_result_NextData['array_add_emergency_return_editor_user_code'];
                    $array_add_emergency_return_editor_user_name = $array_result_NextData['array_add_emergency_return_editor_user_name'];
                    $attendance_time = $array_result_NextData['attendance_time'];
                    $leaving_time = $array_result_NextData['leaving_time'];
                    $missing_middle_time = $array_result_NextData['missing_middle_time'];
                    $missing_middle_return_time = $array_result_NextData['missing_middle_return_time'];
                    $public_going_out_time = $array_result_NextData['public_going_out_time'];
                    $public_going_out_return_time = $array_result_NextData['public_going_out_return_time'];
                    $emergency_time = $array_result_NextData['emergency_time'];
                    $emergency_return_time = $array_result_NextData['emergency_return_time'];
                    $attendance_time_positions = $array_result_NextData['attendance_time_positions'];
                    $leaving_time_positions = $array_result_NextData['leaving_time_positions'];
                    $missing_middle_time_positions = $array_result_NextData['missing_middle_time_positions'];
                    $missing_return_time_positions = $array_result_NextData['missing_return_time_positions'];
                    $public_going_out_time_positions = $array_result_NextData['public_going_out_time_positions'];
                    $public_return_time_positions = $array_result_NextData['public_return_time_positions'];
                    $emergency_time_positions = $array_result_NextData['emergency_time_positions'];
                    $emergency_return_time_positions = $array_result_NextData['emergency_return_time_positions'];
                    $attendance_time_id = $array_result_NextData['attendance_time_id'];
                    $leaving_time_id = $array_result_NextData['leaving_time_id'];
                    $missing_middle_time_id = $array_result_NextData['missing_middle_time_id'];
                    $missing_middle_return_time_id = $array_result_NextData['missing_middle_return_time_id'];
                    $public_going_out_time_id = $array_result_NextData['public_going_out_time_id'];
                    $public_going_out_return_time_id = $array_result_NextData['public_going_out_return_time_id'];
                    $emergency_time_id = $array_result_NextData['emergency_time_id'];
                    $emergency_return_time_id = $array_result_NextData['emergency_return_time_id'];
                    $attendance_editor_department_code = $array_result_NextData['attendance_editor_department_code'];
                    $attendance_editor_department_name = $array_result_NextData['attendance_editor_department_name'];
                    $attendance_editor_user_code = $array_result_NextData['attendance_editor_user_code'];
                    $attendance_editor_user_name = $array_result_NextData['attendance_editor_user_name'];
                    $leaving_editor_department_code = $array_result_NextData['leaving_editor_department_code'];
                    $leaving_editor_department_name = $array_result_NextData['leaving_editor_department_name'];
                    $leaving_editor_user_code = $array_result_NextData['leaving_editor_user_code'];
                    $leaving_editor_user_name = $array_result_NextData['leaving_editor_user_name'];
                    $missing_middle_editor_department_code = $array_result_NextData['missing_middle_editor_department_code'];
                    $missing_middle_editor_department_name = $array_result_NextData['missing_middle_editor_department_name'];
                    $missing_middle_editor_user_code = $array_result_NextData['missing_middle_editor_user_code'];
                    $missing_middle_editor_user_name = $array_result_NextData['missing_middle_editor_user_name'];
                    $missing_return_editor_department_code = $array_result_NextData['missing_return_editor_department_code'];
                    $missing_return_editor_department_name = $array_result_NextData['missing_return_editor_department_name'];
                    $missing_return_editor_user_code = $array_result_NextData['missing_return_editor_user_code'];
                    $missing_return_editor_user_name = $array_result_NextData['missing_return_editor_user_name'];
                    $public_going_out_editor_department_code = $array_result_NextData['public_going_out_editor_department_code'];
                    $public_going_out_editor_department_name = $array_result_NextData['public_going_out_editor_department_name'];
                    $public_going_out_editor_user_code = $array_result_NextData['public_going_out_editor_user_code'];
                    $public_going_out_editor_user_name = $array_result_NextData['public_going_out_editor_user_name'];
                    $public_return_editor_department_code = $array_result_NextData['public_return_editor_department_code'];
                    $public_return_editor_department_name = $array_result_NextData['public_return_editor_department_name'];
                    $public_return_editor_user_code = $array_result_NextData['public_return_editor_user_code'];
                    $public_return_editor_user_name = $array_result_NextData['public_return_editor_user_name'];
                    $emergency_editor_department_code = $array_result_NextData['emergency_editor_department_code'];
                    $emergency_editor_department_name = $array_result_NextData['emergency_editor_department_name'];
                    $emergency_editor_user_code = $array_result_NextData['emergency_editor_user_code'];
                    $emergency_editor_user_name = $array_result_NextData['emergency_editor_user_name'];
                    $emergency_return_editor_department_code = $array_result_NextData['emergency_return_editor_department_code'];
                    $emergency_return_editor_department_name = $array_result_NextData['emergency_return_editor_department_name'];
                    $emergency_return_editor_user_code = $array_result_NextData['emergency_return_editor_user_code'];
                    $emergency_return_editor_user_name = $array_result_NextData['emergency_return_editor_user_name'];
                    // 同じ値にする
                    $before_date = $current_date;
                    $before_department_code = $current_department_code;
                    $before_user_code = $current_user_code; 
                    $before_result = $result;
                    $this->not_employment_working = 0;
                    $working_status = $result->working_status;
                    $note = '';
                    $late = '';
                    $leave_early = '';
                    $to_be_confirmed = '';
                    $calc_nobreak_cnt = 0;
                    $array_notelateetc = $this->setNoteLateEtc($result);
                    $note .= $array_notelateetc[0];
                    $late = $array_notelateetc[1];
                    $leave_early = $array_notelateetc[2];
                    $to_be_confirmed = $array_notelateetc[3];
                    $calc_nobreak_cnt++;
                    // ユーザー休暇区分判定用
                    $before_holiday_set = true;
                    $before_holiday_date = $current_date;
                    $before_holiday_user_code = $current_user_code;
                    $before_holiday_department_code = $current_department_code;
                    $before_holiday_kubun = $result->holiday_kubun;
                }
            } elseif ($current_date == $before_date &&
                    $current_department_code == $before_department_code) {
                Log::debug('--- '.$before_result->user_name.' 終了  ------ '.$before_date.' モード  ------ '.$before_result->mode.' ---- $calc_nobreak_cnt = '.$calc_nobreak_cnt );
                Log::debug('--- '.$result->user_name.' 開始  ------ '.$current_date.' モード  ------ '.$result->mode.' ---- $calc_nobreak_cnt = '.$calc_nobreak_cnt );
                // ユーザーが変わった場合
                Log::debug('    ユーザーが変わった場合 ');
                Log::debug('        $result->user_code  '.$before_user_code.'->'.$result->user_code);
                Log::debug('        $result->mode  '.$result->mode);
                Log::debug('        $result->record_datetime  '.$result->record_datetime);
                Log::debug('        $result->working_timetable_from_time  '.$result->working_timetable_from_time);
                Log::debug('        $result->working_timetable_to_time  '.$result->working_timetable_to_time);
                try{
                    Log::debug('        $working_status  '.$working_status);
                    if ($working_status == 0 ) {
                        Log::debug('        当日分　勤務状態 打刻時刻 =  '.$before_result->record_datetime);
                        Log::debug('        当日分　勤務状態 現在時刻 =  '.$dtNow);
                        if ($before_result->record_datetime < $dtNow) {                            // 打刻時刻 < 現在時刻
                            $working_status = $before_result->working_status;
                        }
                        Log::debug('当日分　勤務状態 =  '.$working_status);
                    }
                    Log::debug('        $calc_nobreak_cnt  '.$calc_nobreak_cnt);
                    if ($calc_nobreak_cnt == 0) {
                        $array_notelateetc = $this->setNoteLateEtc($before_result);
                        $note .= $array_notelateetc[0];
                        Log::debug('        setNoteLateEtc $note =  '.$note);
                        $late = $array_notelateetc[1];
                        $leave_early = $array_notelateetc[2];
                        $to_be_confirmed = $array_notelateetc[3];
                    }
                    // １個前のユーザーが休暇設定されていた場合はすでに登録済み
                    Log::debug('        before_holiday_set = '.$before_holiday_set);
                    Log::debug('        before_holiday_kubun = '.$before_holiday_kubun);
                    Log::debug('        result->holiday_kubun = '.$result->holiday_kubun);
                    if ($before_holiday_set == false) {
                        Log::debug('        temp_working_time_datesデータ作成開始 ');
                        Log::debug('            １個前のユーザーを登録 '.$before_user_code);
                        // ユーザー労働時間登録(１個前のユーザーを登録する)
                        // setLeavingCollectPtn implement
                        $array_impl_addTempWorkingTimeDate = array (
                            'target_date' => $before_date,
                            'target_user_code' => $before_user_code,
                            'target_department_code' => $before_department_code,
                            'target_result' => $before_result,
                            'note' => $note,
                            'working_status' => $working_status,
                            'timetables' => $timetables,
                            'array_attendance_calc_time' => $array_attendance_calc_time,
                            'array_missing_middle_time' => $array_missing_middle_time,
                            'array_public_going_out_time' => $array_public_going_out_time,
                            'array_emergency_time' => $array_emergency_time,
                            'array_add_attendance_time' => $array_add_attendance_time,
                            'array_add_attendance_time_id' => $array_add_attendance_time_id,
                            'array_add_attendance_editor_department_code' => $array_add_attendance_editor_department_code,
                            'array_add_attendance_editor_department_name' => $array_add_attendance_editor_department_name,
                            'array_add_attendance_editor_user_code' => $array_add_attendance_editor_user_code,
                            'array_add_attendance_editor_user_name' => $array_add_attendance_editor_user_name,
                            'array_add_attendance_time_positions' => $array_add_attendance_time_positions,
                            'array_add_leaving_time' => $array_add_leaving_time,
                            'array_add_leaving_time_id' => $array_add_leaving_time_id,
                            'array_add_leaving_editor_department_code' => $array_add_leaving_editor_department_code,
                            'array_add_leaving_editor_department_name' => $array_add_leaving_editor_department_name,
                            'array_add_leaving_editor_user_code' => $array_add_leaving_editor_user_code,
                            'array_add_leaving_editor_user_name' => $array_add_leaving_editor_user_name,
                            'array_add_leaving_time_positions' => $array_add_leaving_time_positions,
                            'array_add_missing_middle_time' => $array_add_missing_middle_time,
                            'array_add_missing_middle_time_id' => $array_add_missing_middle_time_id,
                            'array_add_missing_middle_editor_department_code' => $array_add_missing_middle_editor_department_code,
                            'array_add_missing_middle_editor_department_name' => $array_add_missing_middle_editor_department_name,
                            'array_add_missing_middle_editor_user_code' => $array_add_missing_middle_editor_user_code,
                            'array_add_missing_middle_editor_user_name' => $array_add_missing_middle_editor_user_name,
                            'array_add_missing_middle_time_positions' => $array_add_missing_middle_time_positions,
                            'array_add_missing_return_time' => $array_add_missing_return_time,
                            'array_add_missing_return_time_id' => $array_add_missing_return_time_id,
                            'array_add_missing_return_editor_department_code' => $array_add_missing_return_editor_department_code,
                            'array_add_missing_return_editor_department_name' => $array_add_missing_return_editor_department_name,
                            'array_add_missing_return_editor_user_code' => $array_add_missing_return_editor_user_code,
                            'array_add_missing_return_editor_user_name' => $array_add_missing_return_editor_user_name,
                            'array_add_missing_return_time_positions' => $array_add_missing_return_time_positions,
                            'array_add_public_going_out_time' => $array_add_public_going_out_time,
                            'array_add_public_going_out_time_id' => $array_add_public_going_out_time_id,
                            'array_add_public_going_out_editor_department_code' => $array_add_public_going_out_editor_department_code,
                            'array_add_public_going_out_editor_department_name' => $array_add_public_going_out_editor_department_name,
                            'array_add_public_going_out_editor_user_code' => $array_add_public_going_out_editor_user_code,
                            'array_add_public_going_out_editor_user_name' => $array_add_public_going_out_editor_user_name,
                            'array_add_public_going_out_time_positions' => $array_add_public_going_out_time_positions,
                            'array_add_public_return_time' => $array_add_public_return_time,
                            'array_add_public_return_time_id' => $array_add_public_return_time_id,
                            'array_add_public_return_editor_department_code' => $array_add_public_return_editor_department_code,
                            'array_add_public_return_editor_department_name' => $array_add_public_return_editor_department_name,
                            'array_add_public_return_editor_user_code' => $array_add_public_return_editor_user_code,
                            'array_add_public_return_editor_user_name' => $array_add_public_return_editor_user_name,
                            'array_add_public_return_time_positions' => $array_add_public_return_time_positions,
                            'array_break_worktimetable_result' => $array_break_worktimetable_result,
                            'array_add_emergency_time' => $array_add_emergency_time,
                            'array_add_emergency_time_id' => $array_add_emergency_time_id,
                            'array_add_emergency_editor_department_code' => $array_add_emergency_editor_department_code,
                            'array_add_emergency_editor_department_name' => $array_add_emergency_editor_department_name,
                            'array_add_emergency_editor_user_code' => $array_add_emergency_editor_user_code,
                            'array_add_emergency_editor_user_name' => $array_add_emergency_editor_user_name,
                            'array_add_emergency_time_positions' => $array_add_emergency_time_positions,
                            'array_add_emergency_return_time' => $array_add_emergency_return_time,
                            'array_add_emergency_return_time_id' => $array_add_emergency_return_time_id,
                            'array_add_emergency_return_editor_department_code' => $array_add_emergency_return_editor_department_code,
                            'array_add_emergency_return_editor_department_name' => $array_add_emergency_return_editor_department_name,
                            'array_add_emergency_return_editor_user_code' => $array_add_emergency_return_editor_user_code,
                            'array_add_emergency_return_editor_user_name' => $array_add_emergency_return_editor_user_name,
                            'array_add_emergency_return_time_positions' => $array_add_emergency_return_time_positions,
                            'array_get_timetable_result' => $array_get_timetable_result
                        );
                        $add_result = $this->addTempWorkingTimeDate($array_impl_addTempWorkingTimeDate);
                    }
                    $before_holiday_set = false;
                    // 日付とユーザー休暇区分を保存
                    $before_holiday_date = $current_date;
                    $before_holiday_user_code = $current_user_code;
                    $before_holiday_department_code = $current_department_code;
                    $before_holiday_kubun = $result->holiday_kubun;
                    // 次データ計算事前処理
                    $array_result_NextData =
                        $this->calcTempWorkingTimeDateNextData(
                            $array_working_time_kubun,
                            $result
                        );
                    $array_attendance_calc_time = $array_result_NextData['array_attendance_calc_time'];
                    $array_missing_middle_time = $array_result_NextData['array_missing_middle_time'];
                    $array_public_going_out_time = $array_result_NextData['array_public_going_out_time'];
                    $array_emergency_time = $array_result_NextData['array_emergency_time'];
                    $array_add_attendance_time = $array_result_NextData['array_add_attendance_time'];
                    $array_add_leaving_time = $array_result_NextData['array_add_leaving_time'];
                    $array_add_missing_middle_time = $array_result_NextData['array_add_missing_middle_time'];
                    $array_add_missing_return_time = $array_result_NextData['array_add_missing_return_time'];
                    $array_add_public_going_out_time = $array_result_NextData['array_add_public_going_out_time'];
                    $array_add_public_return_time = $array_result_NextData['array_add_public_return_time'];
                    $array_add_emergency_time = $array_result_NextData['array_add_emergency_time'];
                    $array_add_emergency_return_time = $array_result_NextData['array_add_emergency_return_time'];
                    $array_add_attendance_time_positions = $array_result_NextData['array_add_attendance_time_positions'];
                    $array_add_leaving_time_positions = $array_result_NextData['array_add_leaving_time_positions'];
                    $array_add_missing_middle_time_positions = $array_result_NextData['array_add_missing_middle_time_positions'];
                    $array_add_missing_return_time_positions = $array_result_NextData['array_add_missing_return_time_positions'];
                    $array_add_public_going_out_time_positions = $array_result_NextData['array_add_public_going_out_time_positions'];
                    $array_add_public_return_time_positions = $array_result_NextData['array_add_public_return_time_positions'];
                    $array_add_emergency_time_positions = $array_result_NextData['array_add_emergency_time_positions'];
                    $array_add_emergency_return_time_positions = $array_result_NextData['array_add_emergency_return_time_positions'];
                    $array_add_attendance_time_id = $array_result_NextData['array_add_attendance_time_id'];
                    $array_add_attendance_editor_department_code = $array_result_NextData['array_add_attendance_editor_department_code'];
                    $array_add_attendance_editor_department_name = $array_result_NextData['array_add_attendance_editor_department_name'];
                    $array_add_attendance_editor_user_code = $array_result_NextData['array_add_attendance_editor_user_code'];
                    $array_add_attendance_editor_user_name = $array_result_NextData['array_add_attendance_editor_user_name'];
                    $array_add_leaving_time_id = $array_result_NextData['array_add_leaving_time_id'];
                    $array_add_leaving_editor_department_code = $array_result_NextData['array_add_leaving_editor_department_code'];
                    $array_add_leaving_editor_department_name = $array_result_NextData['array_add_leaving_editor_department_name'];
                    $array_add_leaving_editor_user_code = $array_result_NextData['array_add_leaving_editor_user_code'];
                    $array_add_leaving_editor_user_name = $array_result_NextData['array_add_leaving_editor_user_name'];
                    $array_add_missing_middle_time_id = $array_result_NextData['array_add_missing_middle_time_id'];
                    $array_add_missing_middle_editor_department_code = $array_result_NextData['array_add_missing_middle_editor_department_code'];
                    $array_add_missing_middle_editor_department_name = $array_result_NextData['array_add_missing_middle_editor_department_name'];
                    $array_add_missing_middle_editor_user_code = $array_result_NextData['array_add_missing_middle_editor_user_code'];
                    $array_add_missing_middle_editor_user_name = $array_result_NextData['array_add_missing_middle_editor_user_name'];
                    $array_add_missing_return_time_id = $array_result_NextData['array_add_missing_return_time_id'];
                    $array_add_missing_return_editor_department_code = $array_result_NextData['array_add_missing_return_editor_department_code'];
                    $array_add_missing_return_editor_department_name = $array_result_NextData['array_add_missing_return_editor_department_name'];
                    $array_add_missing_return_editor_user_code = $array_result_NextData['array_add_missing_return_editor_user_code'];
                    $array_add_missing_return_editor_user_name = $array_result_NextData['array_add_missing_return_editor_user_name'];
                    $array_add_public_going_out_time_id = $array_result_NextData['array_add_public_going_out_time_id'];
                    $array_add_public_going_out_editor_department_code = $array_result_NextData['array_add_public_going_out_editor_department_code'];
                    $array_add_public_going_out_editor_department_name = $array_result_NextData['array_add_public_going_out_editor_department_name'];
                    $array_add_public_going_out_editor_user_code = $array_result_NextData['array_add_public_going_out_editor_user_code'];
                    $array_add_public_going_out_editor_user_name = $array_result_NextData['array_add_public_going_out_editor_user_name'];
                    $array_add_public_return_time_id = $array_result_NextData['array_add_public_return_time_id'];
                    $array_add_public_return_editor_department_code = $array_result_NextData['array_add_public_return_editor_department_code'];
                    $array_add_public_return_editor_department_name = $array_result_NextData['array_add_public_return_editor_department_name'];
                    $array_add_public_return_editor_user_code = $array_result_NextData['array_add_public_return_editor_user_code'];
                    $array_add_public_return_editor_user_name = $array_result_NextData['array_add_public_return_editor_user_name'];
                    $array_add_emergency_time_id = $array_result_NextData['array_add_emergency_time_id'];
                    $array_add_emergency_editor_department_code = $array_result_NextData['array_add_emergency_editor_department_code'];
                    $array_add_emergency_editor_department_name = $array_result_NextData['array_add_emergency_editor_department_name'];
                    $array_add_emergency_editor_user_code = $array_result_NextData['array_add_emergency_editor_user_code'];
                    $array_add_emergency_editor_user_name = $array_result_NextData['array_add_emergency_editor_user_name'];
                    $array_add_emergency_return_time_id = $array_result_NextData['array_add_emergency_return_time_id'];
                    $array_add_emergency_return_editor_department_code = $array_result_NextData['array_add_emergency_return_editor_department_code'];
                    $array_add_emergency_return_editor_department_name = $array_result_NextData['array_add_emergency_return_editor_department_name'];
                    $array_add_emergency_return_editor_user_code = $array_result_NextData['array_add_emergency_return_editor_user_code'];
                    $array_add_emergency_return_editor_user_name = $array_result_NextData['array_add_emergency_return_editor_user_name'];
                    $attendance_time = $array_result_NextData['attendance_time'];
                    $leaving_time = $array_result_NextData['leaving_time'];
                    $missing_middle_time = $array_result_NextData['missing_middle_time'];
                    $missing_middle_return_time = $array_result_NextData['missing_middle_return_time'];
                    $public_going_out_time = $array_result_NextData['public_going_out_time'];
                    $public_going_out_return_time = $array_result_NextData['public_going_out_return_time'];
                    $emergency_time = $array_result_NextData['emergency_time'];
                    $emergency_return_time = $array_result_NextData['emergency_return_time'];
                    $attendance_time_positions = $array_result_NextData['attendance_time_positions'];
                    $leaving_time_positions = $array_result_NextData['leaving_time_positions'];
                    $missing_middle_time_positions = $array_result_NextData['missing_middle_time_positions'];
                    $missing_return_time_positions = $array_result_NextData['missing_return_time_positions'];
                    $public_going_out_time_positions = $array_result_NextData['public_going_out_time_positions'];
                    $public_return_time_positions = $array_result_NextData['public_return_time_positions'];
                    $emergency_time_positions = $array_result_NextData['emergency_time_positions'];
                    $emergency_return_time_positions = $array_result_NextData['emergency_return_time_positions'];
                    $attendance_time_id = $array_result_NextData['attendance_time_id'];
                    $leaving_time_id = $array_result_NextData['leaving_time_id'];
                    $missing_middle_time_id = $array_result_NextData['missing_middle_time_id'];
                    $missing_middle_return_time_id = $array_result_NextData['missing_middle_return_time_id'];
                    $public_going_out_time_id = $array_result_NextData['public_going_out_time_id'];
                    $public_going_out_return_time_id = $array_result_NextData['public_going_out_return_time_id'];
                    $emergency_time_id = $array_result_NextData['emergency_time_id'];
                    $emergency_return_time_id = $array_result_NextData['emergency_return_time_id'];
                    $attendance_editor_department_code = $array_result_NextData['attendance_editor_department_code'];
                    $attendance_editor_department_name = $array_result_NextData['attendance_editor_department_name'];
                    $attendance_editor_user_code = $array_result_NextData['attendance_editor_user_code'];
                    $attendance_editor_user_name = $array_result_NextData['attendance_editor_user_name'];
                    $leaving_editor_department_code = $array_result_NextData['leaving_editor_department_code'];
                    $leaving_editor_department_name = $array_result_NextData['leaving_editor_department_name'];
                    $leaving_editor_user_code = $array_result_NextData['leaving_editor_user_code'];
                    $leaving_editor_user_name = $array_result_NextData['leaving_editor_user_name'];
                    $missing_middle_editor_department_code = $array_result_NextData['missing_middle_editor_department_code'];
                    $missing_middle_editor_department_name = $array_result_NextData['missing_middle_editor_department_name'];
                    $missing_middle_editor_user_code = $array_result_NextData['missing_middle_editor_user_code'];
                    $missing_middle_editor_user_name = $array_result_NextData['missing_middle_editor_user_name'];
                    $missing_return_editor_department_code = $array_result_NextData['missing_return_editor_department_code'];
                    $missing_return_editor_department_name = $array_result_NextData['missing_return_editor_department_name'];
                    $missing_return_editor_user_code = $array_result_NextData['missing_return_editor_user_code'];
                    $missing_return_editor_user_name = $array_result_NextData['missing_return_editor_user_name'];
                    $public_going_out_editor_department_code = $array_result_NextData['public_going_out_editor_department_code'];
                    $public_going_out_editor_department_name = $array_result_NextData['public_going_out_editor_department_name'];
                    $public_going_out_editor_user_code = $array_result_NextData['public_going_out_editor_user_code'];
                    $public_going_out_editor_user_name = $array_result_NextData['public_going_out_editor_user_name'];
                    $public_return_editor_department_code = $array_result_NextData['public_return_editor_department_code'];
                    $public_return_editor_department_name = $array_result_NextData['public_return_editor_department_name'];
                    $public_return_editor_user_code = $array_result_NextData['public_return_editor_user_code'];
                    $public_return_editor_user_name = $array_result_NextData['public_return_editor_user_name'];
                    $emergency_editor_department_code = $array_result_NextData['emergency_editor_department_code'];
                    $emergency_editor_department_name = $array_result_NextData['emergency_editor_department_name'];
                    $emergency_editor_user_code = $array_result_NextData['emergency_editor_user_code'];
                    $emergency_editor_user_name = $array_result_NextData['emergency_editor_user_name'];
                    $emergency_return_editor_department_code = $array_result_NextData['emergency_return_editor_department_code'];
                    $emergency_return_editor_department_name = $array_result_NextData['emergency_return_editor_department_name'];
                    $emergency_return_editor_user_code = $array_result_NextData['emergency_return_editor_user_code'];
                    $emergency_return_editor_user_name = $array_result_NextData['emergency_return_editor_user_name'];
                    // 同じ値にする
                    $before_user_code = $current_user_code; 
                    $before_result = $result;
                    $this->not_employment_working = 0;
                    $working_status = $result->working_status;
                    $note = '';
                    $late = '';
                    $leave_early = '';
                    $to_be_confirmed = '';
                    $calc_nobreak_cnt = 0;
                    $array_notelateetc = $this->setNoteLateEtc($result);
                    $note .= $array_notelateetc[0];
                    $late = $array_notelateetc[1];
                    $leave_early = $array_notelateetc[2];
                    $to_be_confirmed = $array_notelateetc[3];
                    $calc_nobreak_cnt++;
                }catch(\PDOException $pe){
                    $add_result = false;
                    throw $pe;
                }catch(\Exception $e){
                    Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.data_error_dailycalc'));
                    Log::error($e->getMessage());
                    $add_result = false;
                    throw $e;
                }
                // 現データが当日計算対象で、出勤していない休暇設定されていればデータ作成
                //if ($result->current_calc == '1' && isset($result->holiday_kubun)) {
                if ($result->current_calc == '1' && !$mode_chk) {
                    Log::debug('        temp_working_time_datesデータ作成開始 ');
                    Log::debug('            現データが当日計算対象データ作成 =  '.$current_user_code);
                    Log::debug('                休暇区分  = '.$result->holiday_kubun);
                    Log::debug('        用途フリー項目 =  '.$result->use_free_item);
                    if ((!isset($result->use_free_item)) ||
                        (isset($result->use_free_item) &&
                        (substr($result->use_free_item, Config::get('const.USEFREEITEM.day_holiday'), 1) == "1"))) {
                        // setLeavingCollectPtn implement
                        $array_impl_addTempWorkingTimeDate = array (
                            'target_date' => $current_date,
                            'target_user_code' => $current_user_code,
                            'target_department_code' => $current_department_code,
                            'target_result' => $current_result,
                            'note' => $note,
                            'working_status' => $working_status,
                            'timetables' => $timetables,
                            'array_attendance_calc_time' => $array_attendance_calc_time,
                            'array_missing_middle_time' => $array_missing_middle_time,
                            'array_public_going_out_time' => $array_public_going_out_time,
                            'array_emergency_time' => $array_emergency_time,
                            'array_add_attendance_time' => $array_add_attendance_time,
                            'array_add_attendance_time_id' => $array_add_attendance_time_id,
                            'array_add_attendance_editor_department_code' => $array_add_attendance_editor_department_code,
                            'array_add_attendance_editor_department_name' => $array_add_attendance_editor_department_name,
                            'array_add_attendance_editor_user_code' => $array_add_attendance_editor_user_code,
                            'array_add_attendance_editor_user_name' => $array_add_attendance_editor_user_name,
                            'array_add_attendance_time_positions' => $array_add_attendance_time_positions,
                            'array_add_leaving_time' => $array_add_leaving_time,
                            'array_add_leaving_time_id' => $array_add_leaving_time_id,
                            'array_add_leaving_editor_department_code' => $array_add_leaving_editor_department_code,
                            'array_add_leaving_editor_department_name' => $array_add_leaving_editor_department_name,
                            'array_add_leaving_editor_user_code' => $array_add_leaving_editor_user_code,
                            'array_add_leaving_editor_user_name' => $array_add_leaving_editor_user_name,
                            'array_add_leaving_time_positions' => $array_add_leaving_time_positions,
                            'array_add_missing_middle_time' => $array_add_missing_middle_time,
                            'array_add_missing_middle_time_id' => $array_add_missing_middle_time_id,
                            'array_add_missing_middle_editor_department_code' => $array_add_missing_middle_editor_department_code,
                            'array_add_missing_middle_editor_department_name' => $array_add_missing_middle_editor_department_name,
                            'array_add_missing_middle_editor_user_code' => $array_add_missing_middle_editor_user_code,
                            'array_add_missing_middle_editor_user_name' => $array_add_missing_middle_editor_user_name,
                            'array_add_missing_middle_time_positions' => $array_add_missing_middle_time_positions,
                            'array_add_missing_return_time' => $array_add_missing_return_time,
                            'array_add_missing_return_time_id' => $array_add_missing_return_time_id,
                            'array_add_missing_return_editor_department_code' => $array_add_missing_return_editor_department_code,
                            'array_add_missing_return_editor_department_name' => $array_add_missing_return_editor_department_name,
                            'array_add_missing_return_editor_user_code' => $array_add_missing_return_editor_user_code,
                            'array_add_missing_return_editor_user_name' => $array_add_missing_return_editor_user_name,
                            'array_add_missing_return_time_positions' => $array_add_missing_return_time_positions,
                            'array_add_public_going_out_time' => $array_add_public_going_out_time,
                            'array_add_public_going_out_time_id' => $array_add_public_going_out_time_id,
                            'array_add_public_going_out_editor_department_code' => $array_add_public_going_out_editor_department_code,
                            'array_add_public_going_out_editor_department_name' => $array_add_public_going_out_editor_department_name,
                            'array_add_public_going_out_editor_user_code' => $array_add_public_going_out_editor_user_code,
                            'array_add_public_going_out_editor_user_name' => $array_add_public_going_out_editor_user_name,
                            'array_add_public_going_out_time_positions' => $array_add_public_going_out_time_positions,
                            'array_add_public_return_time' => $array_add_public_return_time,
                            'array_add_public_return_time_id' => $array_add_public_return_time_id,
                            'array_add_public_return_editor_department_code' => $array_add_public_return_editor_department_code,
                            'array_add_public_return_editor_department_name' => $array_add_public_return_editor_department_name,
                            'array_add_public_return_editor_user_code' => $array_add_public_return_editor_user_code,
                            'array_add_public_return_editor_user_name' => $array_add_public_return_editor_user_name,
                            'array_add_public_return_time_positions' => $array_add_public_return_time_positions,
                            'array_break_worktimetable_result' => $array_break_worktimetable_result,
                            'array_add_emergency_time' => $array_add_emergency_time,
                            'array_add_emergency_time_id' => $array_add_emergency_time_id,
                            'array_add_emergency_editor_department_code' => $array_add_emergency_editor_department_code,
                            'array_add_emergency_editor_department_name' => $array_add_emergency_editor_department_name,
                            'array_add_emergency_editor_user_code' => $array_add_emergency_editor_user_code,
                            'array_add_emergency_editor_user_name' => $array_add_emergency_editor_user_name,
                            'array_add_emergency_time_positions' => $array_add_emergency_time_positions,
                            'array_add_emergency_return_time' => $array_add_emergency_return_time,
                            'array_add_emergency_return_time_id' => $array_add_emergency_return_time_id,
                            'array_add_emergency_return_editor_department_code' => $array_add_emergency_return_editor_department_code,
                            'array_add_emergency_return_editor_department_name' => $array_add_emergency_return_editor_department_name,
                            'array_add_emergency_return_editor_user_code' => $array_add_emergency_return_editor_user_code,
                            'array_add_emergency_return_editor_user_name' => $array_add_emergency_return_editor_user_name,
                            'array_add_emergency_return_time_positions' => $array_add_emergency_return_time_positions,
                            'array_get_timetable_result' => $array_get_timetable_result
                        );
                        $add_result = $this->addTempWorkingTimeDate($array_impl_addTempWorkingTimeDate);
                        // 次データ計算事前処理
                        $array_result_NextData =
                            $this->calcTempWorkingTimeDateNextData(
                                $array_working_time_kubun,
                                $result
                            );
                        $array_attendance_calc_time = $array_result_NextData['array_attendance_calc_time'];
                        $array_missing_middle_time = $array_result_NextData['array_missing_middle_time'];
                        $array_public_going_out_time = $array_result_NextData['array_public_going_out_time'];
                        $array_emergency_time = $array_result_NextData['array_emergency_time'];
                        $array_add_attendance_time = $array_result_NextData['array_add_attendance_time'];
                        $array_add_leaving_time = $array_result_NextData['array_add_leaving_time'];
                        $array_add_missing_middle_time = $array_result_NextData['array_add_missing_middle_time'];
                        $array_add_missing_return_time = $array_result_NextData['array_add_missing_return_time'];
                        $array_add_public_going_out_time = $array_result_NextData['array_add_public_going_out_time'];
                        $array_add_public_return_time = $array_result_NextData['array_add_public_return_time'];
                        $array_add_emergency_time = $array_result_NextData['array_add_emergency_time'];
                        $array_add_emergency_return_time = $array_result_NextData['array_add_emergency_return_time'];
                        $array_add_attendance_time_positions = $array_result_NextData['array_add_attendance_time_positions'];
                        $array_add_leaving_time_positions = $array_result_NextData['array_add_leaving_time_positions'];
                        $array_add_missing_middle_time_positions = $array_result_NextData['array_add_missing_middle_time_positions'];
                        $array_add_missing_return_time_positions = $array_result_NextData['array_add_missing_return_time_positions'];
                        $array_add_public_going_out_time_positions = $array_result_NextData['array_add_public_going_out_time_positions'];
                        $array_add_public_return_time_positions = $array_result_NextData['array_add_public_return_time_positions'];
                        $array_add_emergency_time_positions = $array_result_NextData['array_add_emergency_time_positions'];
                        $array_add_emergency_return_time_positions = $array_result_NextData['array_add_emergency_return_time_positions'];
                        $array_add_attendance_time_id = $array_result_NextData['array_add_attendance_time_id'];
                        $array_add_attendance_editor_department_code = $array_result_NextData['array_add_attendance_editor_department_code'];
                        $array_add_attendance_editor_department_name = $array_result_NextData['array_add_attendance_editor_department_name'];
                        $array_add_attendance_editor_user_code = $array_result_NextData['array_add_attendance_editor_user_code'];
                        $array_add_attendance_editor_user_name = $array_result_NextData['array_add_attendance_editor_user_name'];
                        $array_add_leaving_time_id = $array_result_NextData['array_add_leaving_time_id'];
                        $array_add_leaving_editor_department_code = $array_result_NextData['array_add_leaving_editor_department_code'];
                        $array_add_leaving_editor_department_name = $array_result_NextData['array_add_leaving_editor_department_name'];
                        $array_add_leaving_editor_user_code = $array_result_NextData['array_add_leaving_editor_user_code'];
                        $array_add_leaving_editor_user_name = $array_result_NextData['array_add_leaving_editor_user_name'];
                        $array_add_missing_middle_time_id = $array_result_NextData['array_add_missing_middle_time_id'];
                        $array_add_missing_middle_editor_department_code = $array_result_NextData['array_add_missing_middle_editor_department_code'];
                        $array_add_missing_middle_editor_department_name = $array_result_NextData['array_add_missing_middle_editor_department_name'];
                        $array_add_missing_middle_editor_user_code = $array_result_NextData['array_add_missing_middle_editor_user_code'];
                        $array_add_missing_middle_editor_user_name = $array_result_NextData['array_add_missing_middle_editor_user_name'];
                        $array_add_missing_return_time_id = $array_result_NextData['array_add_missing_return_time_id'];
                        $array_add_missing_return_editor_department_code = $array_result_NextData['array_add_missing_return_editor_department_code'];
                        $array_add_missing_return_editor_department_name = $array_result_NextData['array_add_missing_return_editor_department_name'];
                        $array_add_missing_return_editor_user_code = $array_result_NextData['array_add_missing_return_editor_user_code'];
                        $array_add_missing_return_editor_user_name = $array_result_NextData['array_add_missing_return_editor_user_name'];
                        $array_add_public_going_out_time_id = $array_result_NextData['array_add_public_going_out_time_id'];
                        $array_add_public_going_out_editor_department_code = $array_result_NextData['array_add_public_going_out_editor_department_code'];
                        $array_add_public_going_out_editor_department_name = $array_result_NextData['array_add_public_going_out_editor_department_name'];
                        $array_add_public_going_out_editor_user_code = $array_result_NextData['array_add_public_going_out_editor_user_code'];
                        $array_add_public_going_out_editor_user_name = $array_result_NextData['array_add_public_going_out_editor_user_name'];
                        $array_add_public_return_time_id = $array_result_NextData['array_add_public_return_time_id'];
                        $array_add_public_return_editor_department_code = $array_result_NextData['array_add_public_return_editor_department_code'];
                        $array_add_public_return_editor_department_name = $array_result_NextData['array_add_public_return_editor_department_name'];
                        $array_add_public_return_editor_user_code = $array_result_NextData['array_add_public_return_editor_user_code'];
                        $array_add_public_return_editor_user_name = $array_result_NextData['array_add_public_return_editor_user_name'];
                        $array_add_emergency_time_id = $array_result_NextData['array_add_emergency_time_id'];
                        $array_add_emergency_editor_department_code = $array_result_NextData['array_add_emergency_editor_department_code'];
                        $array_add_emergency_editor_department_name = $array_result_NextData['array_add_emergency_editor_department_name'];
                        $array_add_emergency_editor_user_code = $array_result_NextData['array_add_emergency_editor_user_code'];
                        $array_add_emergency_editor_user_name = $array_result_NextData['array_add_emergency_editor_user_name'];
                        $array_add_emergency_return_time_id = $array_result_NextData['array_add_emergency_return_time_id'];
                        $array_add_emergency_return_editor_department_code = $array_result_NextData['array_add_emergency_return_editor_department_code'];
                        $array_add_emergency_return_editor_department_name = $array_result_NextData['array_add_emergency_return_editor_department_name'];
                        $array_add_emergency_return_editor_user_code = $array_result_NextData['array_add_emergency_return_editor_user_code'];
                        $array_add_emergency_return_editor_user_name = $array_result_NextData['array_add_emergency_return_editor_user_name'];
                        $attendance_time = $array_result_NextData['attendance_time'];
                        $leaving_time = $array_result_NextData['leaving_time'];
                        $missing_middle_time = $array_result_NextData['missing_middle_time'];
                        $missing_middle_return_time = $array_result_NextData['missing_middle_return_time'];
                        $public_going_out_time = $array_result_NextData['public_going_out_time'];
                        $public_going_out_return_time = $array_result_NextData['public_going_out_return_time'];
                        $emergency_time = $array_result_NextData['emergency_time'];
                        $emergency_return_time = $array_result_NextData['emergency_return_time'];
                        $attendance_time_positions = $array_result_NextData['attendance_time_positions'];
                        $leaving_time_positions = $array_result_NextData['leaving_time_positions'];
                        $missing_middle_time_positions = $array_result_NextData['missing_middle_time_positions'];
                        $missing_return_time_positions = $array_result_NextData['missing_return_time_positions'];
                        $public_going_out_time_positions = $array_result_NextData['public_going_out_time_positions'];
                        $public_return_time_positions = $array_result_NextData['public_return_time_positions'];
                        $emergency_time_positions = $array_result_NextData['emergency_time_positions'];
                        $emergency_return_time_positions = $array_result_NextData['emergency_return_time_positions'];
                        $attendance_time_id = $array_result_NextData['attendance_time_id'];
                        $leaving_time_id = $array_result_NextData['leaving_time_id'];
                        $missing_middle_time_id = $array_result_NextData['missing_middle_time_id'];
                        $missing_middle_return_time_id = $array_result_NextData['missing_middle_return_time_id'];
                        $public_going_out_time_id = $array_result_NextData['public_going_out_time_id'];
                        $public_going_out_return_time_id = $array_result_NextData['public_going_out_return_time_id'];
                        $emergency_time_id = $array_result_NextData['emergency_time_id'];
                        $emergency_return_time_id = $array_result_NextData['emergency_return_time_id'];
                        $attendance_editor_department_code = $array_result_NextData['attendance_editor_department_code'];
                        $attendance_editor_department_name = $array_result_NextData['attendance_editor_department_name'];
                        $attendance_editor_user_code = $array_result_NextData['attendance_editor_user_code'];
                        $attendance_editor_user_name = $array_result_NextData['attendance_editor_user_name'];
                        $leaving_editor_department_code = $array_result_NextData['leaving_editor_department_code'];
                        $leaving_editor_department_name = $array_result_NextData['leaving_editor_department_name'];
                        $leaving_editor_user_code = $array_result_NextData['leaving_editor_user_code'];
                        $leaving_editor_user_name = $array_result_NextData['leaving_editor_user_name'];
                        $missing_middle_editor_department_code = $array_result_NextData['missing_middle_editor_department_code'];
                        $missing_middle_editor_department_name = $array_result_NextData['missing_middle_editor_department_name'];
                        $missing_middle_editor_user_code = $array_result_NextData['missing_middle_editor_user_code'];
                        $missing_middle_editor_user_name = $array_result_NextData['missing_middle_editor_user_name'];
                        $missing_return_editor_department_code = $array_result_NextData['missing_return_editor_department_code'];
                        $missing_return_editor_department_name = $array_result_NextData['missing_return_editor_department_name'];
                        $missing_return_editor_user_code = $array_result_NextData['missing_return_editor_user_code'];
                        $missing_return_editor_user_name = $array_result_NextData['missing_return_editor_user_name'];
                        $public_going_out_editor_department_code = $array_result_NextData['public_going_out_editor_department_code'];
                        $public_going_out_editor_department_name = $array_result_NextData['public_going_out_editor_department_name'];
                        $public_going_out_editor_user_code = $array_result_NextData['public_going_out_editor_user_code'];
                        $public_going_out_editor_user_name = $array_result_NextData['public_going_out_editor_user_name'];
                        $public_return_editor_department_code = $array_result_NextData['public_return_editor_department_code'];
                        $public_return_editor_department_name = $array_result_NextData['public_return_editor_department_name'];
                        $public_return_editor_user_code = $array_result_NextData['public_return_editor_user_code'];
                        $public_return_editor_user_name = $array_result_NextData['public_return_editor_user_name'];
                        $emergency_editor_department_code = $array_result_NextData['emergency_editor_department_code'];
                        $emergency_editor_department_name = $array_result_NextData['emergency_editor_department_name'];
                        $emergency_editor_user_code = $array_result_NextData['emergency_editor_user_code'];
                        $emergency_editor_user_name = $array_result_NextData['emergency_editor_user_name'];
                        $emergency_return_editor_department_code = $array_result_NextData['emergency_return_editor_department_code'];
                        $emergency_return_editor_department_name = $array_result_NextData['emergency_return_editor_department_name'];
                        $emergency_return_editor_user_code = $array_result_NextData['emergency_return_editor_user_code'];
                        $emergency_return_editor_user_name = $array_result_NextData['emergency_return_editor_user_name'];
                        // 同じ値にする
                        $before_date = $current_date;
                        $before_department_code = $current_department_code;
                        $before_user_code = $current_user_code; 
                        $before_result = $result;
                        $this->not_employment_working = 0;
                        $working_status = 0;
                        $note = '';
                        $late = '';
                        $leave_early = '';
                        $to_be_confirmed = '';
                        $calc_nobreak_cnt = 0;
                        $array_notelateetc = $this->setNoteLateEtc($result);
                        $note .= $array_notelateetc[0];
                        Log::debug('setNoteLateEtc $note =  '.$note);
                        $late = $array_notelateetc[1];
                        $leave_early = $array_notelateetc[2];
                        $to_be_confirmed = $array_notelateetc[3];
                        $calc_nobreak_cnt++;
                        // ユーザー休暇区分判定用
                        $before_holiday_set = true;
                        $before_holiday_date = $current_date;
                        $before_holiday_user_code = $current_user_code;
                        $before_holiday_department_code = $current_department_code;
                        $before_holiday_kubun = $result->holiday_kubun;
                    }
                }
            } elseif ($current_date == $before_date) {
                // 部署が変わった場合
                Log::debug('部署が変わった場合 ');
                Log::debug('--- '.$before_result->user_name.' 終了  ------ '.$before_date.' モード  ------ '.$before_result->mode.' ---- $calc_nobreak_cnt = '.$calc_nobreak_cnt );
                Log::debug('--- '.$result->user_name.' 開始  ------ '.$current_date.' モード  ------ '.$result->mode.' ---- $calc_nobreak_cnt = '.$calc_nobreak_cnt );
                try{
                    if ($working_status == 0 ) {
                        Log::debug('当日分　勤務状態 打刻時刻 =  '.$before_result->record_datetime);
                        Log::debug('当日分　勤務状態 現在刻 =  '.$dtNow);
                        if ($before_result->record_datetime < $dtNow) {                            // 打刻時刻 < 現在時刻
                            $working_status = $before_result->working_status;
                        }
                        Log::debug('当日分　勤務状態 =  '.$working_status);
                    }
                    if ($calc_nobreak_cnt == 0) {
                        $array_notelateetc = $this->setNoteLateEtc($before_result);
                        $note .= $array_notelateetc[0];
                        Log::debug('setNoteLateEtc $note =  '.$note);
                        $late = $array_notelateetc[1];
                        $leave_early = $array_notelateetc[2];
                        $to_be_confirmed = $array_notelateetc[3];
                    }
                    // １個前のユーザーが休暇設定されていた場合はすでに登録済み
                    if ($before_holiday_set == false) {
                        // ユーザー労働時間登録(１個前のユーザーを登録する)
                        Log::debug('        temp_working_time_datesデータ作成開始 ');
                        Log::debug('            １個前のユーザーを登録 '.$before_user_code);
                        // setLeavingCollectPtn implement
                        $array_impl_addTempWorkingTimeDate = array (
                            'target_date' => $before_date,
                            'target_user_code' => $before_user_code,
                            'target_department_code' => $before_department_code,
                            'target_result' => $before_result,
                            'note' => $note,
                            'working_status' => $working_status,
                            'timetables' => $timetables,
                            'array_attendance_calc_time' => $array_attendance_calc_time,
                            'array_missing_middle_time' => $array_missing_middle_time,
                            'array_public_going_out_time' => $array_public_going_out_time,
                            'array_emergency_time' => $array_emergency_time,
                            'array_add_attendance_time' => $array_add_attendance_time,
                            'array_add_attendance_time_id' => $array_add_attendance_time_id,
                            'array_add_attendance_editor_department_code' => $array_add_attendance_editor_department_code,
                            'array_add_attendance_editor_department_name' => $array_add_attendance_editor_department_name,
                            'array_add_attendance_editor_user_code' => $array_add_attendance_editor_user_code,
                            'array_add_attendance_editor_user_name' => $array_add_attendance_editor_user_name,
                            'array_add_attendance_time_positions' => $array_add_attendance_time_positions,
                            'array_add_leaving_time' => $array_add_leaving_time,
                            'array_add_leaving_time_id' => $array_add_leaving_time_id,
                            'array_add_leaving_editor_department_code' => $array_add_leaving_editor_department_code,
                            'array_add_leaving_editor_department_name' => $array_add_leaving_editor_department_name,
                            'array_add_leaving_editor_user_code' => $array_add_leaving_editor_user_code,
                            'array_add_leaving_editor_user_name' => $array_add_leaving_editor_user_name,
                            'array_add_leaving_time_positions' => $array_add_leaving_time_positions,
                            'array_add_missing_middle_time' => $array_add_missing_middle_time,
                            'array_add_missing_middle_time_id' => $array_add_missing_middle_time_id,
                            'array_add_missing_middle_editor_department_code' => $array_add_missing_middle_editor_department_code,
                            'array_add_missing_middle_editor_department_name' => $array_add_missing_middle_editor_department_name,
                            'array_add_missing_middle_editor_user_code' => $array_add_missing_middle_editor_user_code,
                            'array_add_missing_middle_editor_user_name' => $array_add_missing_middle_editor_user_name,
                            'array_add_missing_middle_time_positions' => $array_add_missing_middle_time_positions,
                            'array_add_missing_return_time' => $array_add_missing_return_time,
                            'array_add_missing_return_time_id' => $array_add_missing_return_time_id,
                            'array_add_missing_return_editor_department_code' => $array_add_missing_return_editor_department_code,
                            'array_add_missing_return_editor_department_name' => $array_add_missing_return_editor_department_name,
                            'array_add_missing_return_editor_user_code' => $array_add_missing_return_editor_user_code,
                            'array_add_missing_return_editor_user_name' => $array_add_missing_return_editor_user_name,
                            'array_add_missing_return_time_positions' => $array_add_missing_return_time_positions,
                            'array_add_public_going_out_time' => $array_add_public_going_out_time,
                            'array_add_public_going_out_time_id' => $array_add_public_going_out_time_id,
                            'array_add_public_going_out_editor_department_code' => $array_add_public_going_out_editor_department_code,
                            'array_add_public_going_out_editor_department_name' => $array_add_public_going_out_editor_department_name,
                            'array_add_public_going_out_editor_user_code' => $array_add_public_going_out_editor_user_code,
                            'array_add_public_going_out_editor_user_name' => $array_add_public_going_out_editor_user_name,
                            'array_add_public_going_out_time_positions' => $array_add_public_going_out_time_positions,
                            'array_add_public_return_time' => $array_add_public_return_time,
                            'array_add_public_return_time_id' => $array_add_public_return_time_id,
                            'array_add_public_return_editor_department_code' => $array_add_public_return_editor_department_code,
                            'array_add_public_return_editor_department_name' => $array_add_public_return_editor_department_name,
                            'array_add_public_return_editor_user_code' => $array_add_public_return_editor_user_code,
                            'array_add_public_return_editor_user_name' => $array_add_public_return_editor_user_name,
                            'array_add_public_return_time_positions' => $array_add_public_return_time_positions,
                            'array_break_worktimetable_result' => $array_break_worktimetable_result,
                            'array_add_emergency_time' => $array_add_emergency_time,
                            'array_add_emergency_time_id' => $array_add_emergency_time_id,
                            'array_add_emergency_editor_department_code' => $array_add_emergency_editor_department_code,
                            'array_add_emergency_editor_department_name' => $array_add_emergency_editor_department_name,
                            'array_add_emergency_editor_user_code' => $array_add_emergency_editor_user_code,
                            'array_add_emergency_editor_user_name' => $array_add_emergency_editor_user_name,
                            'array_add_emergency_time_positions' => $array_add_emergency_time_positions,
                            'array_add_emergency_return_time' => $array_add_emergency_return_time,
                            'array_add_emergency_return_time_id' => $array_add_emergency_return_time_id,
                            'array_add_emergency_return_editor_department_code' => $array_add_emergency_return_editor_department_code,
                            'array_add_emergency_return_editor_department_name' => $array_add_emergency_return_editor_department_name,
                            'array_add_emergency_return_editor_user_code' => $array_add_emergency_return_editor_user_code,
                            'array_add_emergency_return_editor_user_name' => $array_add_emergency_return_editor_user_name,
                            'array_add_emergency_return_time_positions' => $array_add_emergency_return_time_positions,
                            'array_get_timetable_result' => $array_get_timetable_result
                        );
                        $add_result = $this->addTempWorkingTimeDate($array_impl_addTempWorkingTimeDate);
                    }
                    $before_holiday_set = false;
                    // 日付とユーザー休暇区分を保存
                    $before_holiday_date = $current_date;
                    $before_holiday_user_code = $current_user_code;
                    $before_holiday_department_code = $current_department_code;
                    $before_holiday_kubun = $result->holiday_kubun;
                    // 次データ計算事前処理
                    $array_result_NextData =
                        $this->calcTempWorkingTimeDateNextData(
                            $array_working_time_kubun,
                            $result
                        );
                    $array_attendance_calc_time = $array_result_NextData['array_attendance_calc_time'];
                    $array_missing_middle_time = $array_result_NextData['array_missing_middle_time'];
                    $array_public_going_out_time = $array_result_NextData['array_public_going_out_time'];
                    $array_emergency_time = $array_result_NextData['array_emergency_time'];
                    $array_add_attendance_time = $array_result_NextData['array_add_attendance_time'];
                    $array_add_leaving_time = $array_result_NextData['array_add_leaving_time'];
                    $array_add_missing_middle_time = $array_result_NextData['array_add_missing_middle_time'];
                    $array_add_missing_return_time = $array_result_NextData['array_add_missing_return_time'];
                    $array_add_public_going_out_time = $array_result_NextData['array_add_public_going_out_time'];
                    $array_add_public_return_time = $array_result_NextData['array_add_public_return_time'];
                    $array_add_emergency_time = $array_result_NextData['array_add_emergency_time'];
                    $array_add_emergency_return_time = $array_result_NextData['array_add_emergency_return_time'];
                    $array_add_attendance_time_positions = $array_result_NextData['array_add_attendance_time_positions'];
                    $array_add_leaving_time_positions = $array_result_NextData['array_add_leaving_time_positions'];
                    $array_add_missing_middle_time_positions = $array_result_NextData['array_add_missing_middle_time_positions'];
                    $array_add_missing_return_time_positions = $array_result_NextData['array_add_missing_return_time_positions'];
                    $array_add_public_going_out_time_positions = $array_result_NextData['array_add_public_going_out_time_positions'];
                    $array_add_public_return_time_positions = $array_result_NextData['array_add_public_return_time_positions'];
                    $array_add_emergency_time_positions = $array_result_NextData['array_add_emergency_time_positions'];
                    $array_add_emergency_return_time_positions = $array_result_NextData['array_add_emergency_return_time_positions'];
                    $array_add_attendance_time_id = $array_result_NextData['array_add_attendance_time_id'];
                    $array_add_attendance_editor_department_code = $array_result_NextData['array_add_attendance_editor_department_code'];
                    $array_add_attendance_editor_department_name = $array_result_NextData['array_add_attendance_editor_department_name'];
                    $array_add_attendance_editor_user_code = $array_result_NextData['array_add_attendance_editor_user_code'];
                    $array_add_attendance_editor_user_name = $array_result_NextData['array_add_attendance_editor_user_name'];
                    $array_add_leaving_time_id = $array_result_NextData['array_add_leaving_time_id'];
                    $array_add_leaving_editor_department_code = $array_result_NextData['array_add_leaving_editor_department_code'];
                    $array_add_leaving_editor_department_name = $array_result_NextData['array_add_leaving_editor_department_name'];
                    $array_add_leaving_editor_user_code = $array_result_NextData['array_add_leaving_editor_user_code'];
                    $array_add_leaving_editor_user_name = $array_result_NextData['array_add_leaving_editor_user_name'];
                    $array_add_missing_middle_time_id = $array_result_NextData['array_add_missing_middle_time_id'];
                    $array_add_missing_middle_editor_department_code = $array_result_NextData['array_add_missing_middle_editor_department_code'];
                    $array_add_missing_middle_editor_department_name = $array_result_NextData['array_add_missing_middle_editor_department_name'];
                    $array_add_missing_middle_editor_user_code = $array_result_NextData['array_add_missing_middle_editor_user_code'];
                    $array_add_missing_middle_editor_user_name = $array_result_NextData['array_add_missing_middle_editor_user_name'];
                    $array_add_missing_return_time_id = $array_result_NextData['array_add_missing_return_time_id'];
                    $array_add_missing_return_editor_department_code = $array_result_NextData['array_add_missing_return_editor_department_code'];
                    $array_add_missing_return_editor_department_name = $array_result_NextData['array_add_missing_return_editor_department_name'];
                    $array_add_missing_return_editor_user_code = $array_result_NextData['array_add_missing_return_editor_user_code'];
                    $array_add_missing_return_editor_user_name = $array_result_NextData['array_add_missing_return_editor_user_name'];
                    $array_add_public_going_out_time_id = $array_result_NextData['array_add_public_going_out_time_id'];
                    $array_add_public_going_out_editor_department_code = $array_result_NextData['array_add_public_going_out_editor_department_code'];
                    $array_add_public_going_out_editor_department_name = $array_result_NextData['array_add_public_going_out_editor_department_name'];
                    $array_add_public_going_out_editor_user_code = $array_result_NextData['array_add_public_going_out_editor_user_code'];
                    $array_add_public_going_out_editor_user_name = $array_result_NextData['array_add_public_going_out_editor_user_name'];
                    $array_add_public_return_time_id = $array_result_NextData['array_add_public_return_time_id'];
                    $array_add_public_return_editor_department_code = $array_result_NextData['array_add_public_return_editor_department_code'];
                    $array_add_public_return_editor_department_name = $array_result_NextData['array_add_public_return_editor_department_name'];
                    $array_add_public_return_editor_user_code = $array_result_NextData['array_add_public_return_editor_user_code'];
                    $array_add_public_return_editor_user_name = $array_result_NextData['array_add_public_return_editor_user_name'];
                    $array_add_emergency_time_id = $array_result_NextData['array_add_emergency_time_id'];
                    $array_add_emergency_editor_department_code = $array_result_NextData['array_add_emergency_editor_department_code'];
                    $array_add_emergency_editor_department_name = $array_result_NextData['array_add_emergency_editor_department_name'];
                    $array_add_emergency_editor_user_code = $array_result_NextData['array_add_emergency_editor_user_code'];
                    $array_add_emergency_editor_user_name = $array_result_NextData['array_add_emergency_editor_user_name'];
                    $array_add_emergency_return_time_id = $array_result_NextData['array_add_emergency_return_time_id'];
                    $array_add_emergency_return_editor_department_code = $array_result_NextData['array_add_emergency_return_editor_department_code'];
                    $array_add_emergency_return_editor_department_name = $array_result_NextData['array_add_emergency_return_editor_department_name'];
                    $array_add_emergency_return_editor_user_code = $array_result_NextData['array_add_emergency_return_editor_user_code'];
                    $array_add_emergency_return_editor_user_name = $array_result_NextData['array_add_emergency_return_editor_user_name'];
                    $attendance_time = $array_result_NextData['attendance_time'];
                    $leaving_time = $array_result_NextData['leaving_time'];
                    $missing_middle_time = $array_result_NextData['missing_middle_time'];
                    $missing_middle_return_time = $array_result_NextData['missing_middle_return_time'];
                    $public_going_out_time = $array_result_NextData['public_going_out_time'];
                    $public_going_out_return_time = $array_result_NextData['public_going_out_return_time'];
                    $emergency_time = $array_result_NextData['emergency_time'];
                    $emergency_return_time = $array_result_NextData['emergency_return_time'];
                    $attendance_time_positions = $array_result_NextData['attendance_time_positions'];
                    $leaving_time_positions = $array_result_NextData['leaving_time_positions'];
                    $missing_middle_time_positions = $array_result_NextData['missing_middle_time_positions'];
                    $missing_return_time_positions = $array_result_NextData['missing_return_time_positions'];
                    $public_going_out_time_positions = $array_result_NextData['public_going_out_time_positions'];
                    $public_return_time_positions = $array_result_NextData['public_return_time_positions'];
                    $emergency_time_positions = $array_result_NextData['emergency_time_positions'];
                    $emergency_return_time_positions = $array_result_NextData['emergency_return_time_positions'];
                    $attendance_time_id = $array_result_NextData['attendance_time_id'];
                    $leaving_time_id = $array_result_NextData['leaving_time_id'];
                    $missing_middle_time_id = $array_result_NextData['missing_middle_time_id'];
                    $missing_middle_return_time_id = $array_result_NextData['missing_middle_return_time_id'];
                    $public_going_out_time_id = $array_result_NextData['public_going_out_time_id'];
                    $public_going_out_return_time_id = $array_result_NextData['public_going_out_return_time_id'];
                    $emergency_time_id = $array_result_NextData['emergency_time_id'];
                    $emergency_return_time_id = $array_result_NextData['emergency_return_time_id'];
                    $attendance_editor_department_code = $array_result_NextData['attendance_editor_department_code'];
                    $attendance_editor_department_name = $array_result_NextData['attendance_editor_department_name'];
                    $attendance_editor_user_code = $array_result_NextData['attendance_editor_user_code'];
                    $attendance_editor_user_name = $array_result_NextData['attendance_editor_user_name'];
                    $leaving_editor_department_code = $array_result_NextData['leaving_editor_department_code'];
                    $leaving_editor_department_name = $array_result_NextData['leaving_editor_department_name'];
                    $leaving_editor_user_code = $array_result_NextData['leaving_editor_user_code'];
                    $leaving_editor_user_name = $array_result_NextData['leaving_editor_user_name'];
                    $missing_middle_editor_department_code = $array_result_NextData['missing_middle_editor_department_code'];
                    $missing_middle_editor_department_name = $array_result_NextData['missing_middle_editor_department_name'];
                    $missing_middle_editor_user_code = $array_result_NextData['missing_middle_editor_user_code'];
                    $missing_middle_editor_user_name = $array_result_NextData['missing_middle_editor_user_name'];
                    $missing_return_editor_department_code = $array_result_NextData['missing_return_editor_department_code'];
                    $missing_return_editor_department_name = $array_result_NextData['missing_return_editor_department_name'];
                    $missing_return_editor_user_code = $array_result_NextData['missing_return_editor_user_code'];
                    $missing_return_editor_user_name = $array_result_NextData['missing_return_editor_user_name'];
                    $public_going_out_editor_department_code = $array_result_NextData['public_going_out_editor_department_code'];
                    $public_going_out_editor_department_name = $array_result_NextData['public_going_out_editor_department_name'];
                    $public_going_out_editor_user_code = $array_result_NextData['public_going_out_editor_user_code'];
                    $public_going_out_editor_user_name = $array_result_NextData['public_going_out_editor_user_name'];
                    $public_return_editor_department_code = $array_result_NextData['public_return_editor_department_code'];
                    $public_return_editor_department_name = $array_result_NextData['public_return_editor_department_name'];
                    $public_return_editor_user_code = $array_result_NextData['public_return_editor_user_code'];
                    $public_return_editor_user_name = $array_result_NextData['public_return_editor_user_name'];
                    $emergency_editor_department_code = $array_result_NextData['emergency_editor_department_code'];
                    $emergency_editor_department_name = $array_result_NextData['emergency_editor_department_name'];
                    $emergency_editor_user_code = $array_result_NextData['emergency_editor_user_code'];
                    $emergency_editor_user_name = $array_result_NextData['emergency_editor_user_name'];
                    $emergency_return_editor_department_code = $array_result_NextData['emergency_return_editor_department_code'];
                    $emergency_return_editor_department_name = $array_result_NextData['emergency_return_editor_department_name'];
                    $emergency_return_editor_user_code = $array_result_NextData['emergency_return_editor_user_code'];
                    $emergency_return_editor_user_name = $array_result_NextData['emergency_return_editor_user_name'];
                    // 同じ値にする
                    $before_department_code = $current_department_code;
                    $before_user_code = $current_user_code; 
                    $before_result = $result;
                    $this->not_employment_working = 0;
                    $working_status = $result->working_status;
                    $note = '';
                    $late = '';
                    $leave_early = '';
                    $to_be_confirmed = '';
                    $calc_nobreak_cnt = 0;
                    $array_notelateetc = $this->setNoteLateEtc($result);
                    $note .= $array_notelateetc[0];
                    Log::debug('setNoteLateEtc $note =  '.$note);
                    $late = $array_notelateetc[1];
                    $leave_early = $array_notelateetc[2];
                    $to_be_confirmed = $array_notelateetc[3];
                    $calc_nobreak_cnt++;
                }catch(\PDOException $pe){
                    $add_result = false;
                    throw $pe;
                }catch(\Exception $e){
                    Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.data_error_dailycalc'));
                    Log::error($e->getMessage());
                    $add_result = false;
                    throw $e;
                }
                // 現データが当日計算対象で、出勤していない休暇設定されていればデータ作成
                //if ($result->current_calc == '1' && isset($result->holiday_kubun)) {
                if ($result->current_calc == '1' && !$mode_chk) {
                    Log::debug('        temp_working_time_datesデータ作成開始 ');
                    Log::debug('            現データが当日計算対象データ作成 =  '.$current_user_code);
                    Log::debug('                休暇区分  = '.$result->holiday_kubun);
                    Log::debug('        用途フリー項目 =  '.$result->use_free_item);
                    if ((!isset($result->use_free_item)) ||
                        (isset($result->use_free_item) &&
                        (substr($result->use_free_item, Config::get('const.USEFREEITEM.day_holiday'), 1) == "1"))) {
                        // setLeavingCollectPtn implement
                        $array_impl_addTempWorkingTimeDate = array (
                            'target_date' => $current_date,
                            'target_user_code' => $current_user_code,
                            'target_department_code' => $current_department_code,
                            'target_result' => $current_result,
                            'note' => $note,
                            'working_status' => $working_status,
                            'timetables' => $timetables,
                            'array_attendance_calc_time' => $array_attendance_calc_time,
                            'array_missing_middle_time' => $array_missing_middle_time,
                            'array_public_going_out_time' => $array_public_going_out_time,
                            'array_emergency_time' => $array_emergency_time,
                            'array_add_attendance_time' => $array_add_attendance_time,
                            'array_add_attendance_time_id' => $array_add_attendance_time_id,
                            'array_add_attendance_editor_department_code' => $array_add_attendance_editor_department_code,
                            'array_add_attendance_editor_department_name' => $array_add_attendance_editor_department_name,
                            'array_add_attendance_editor_user_code' => $array_add_attendance_editor_user_code,
                            'array_add_attendance_editor_user_name' => $array_add_attendance_editor_user_name,
                            'array_add_attendance_time_positions' => $array_add_attendance_time_positions,
                            'array_add_leaving_time' => $array_add_leaving_time,
                            'array_add_leaving_time_id' => $array_add_leaving_time_id,
                            'array_add_leaving_editor_department_code' => $array_add_leaving_editor_department_code,
                            'array_add_leaving_editor_department_name' => $array_add_leaving_editor_department_name,
                            'array_add_leaving_editor_user_code' => $array_add_leaving_editor_user_code,
                            'array_add_leaving_editor_user_name' => $array_add_leaving_editor_user_name,
                            'array_add_leaving_time_positions' => $array_add_leaving_time_positions,
                            'array_add_missing_middle_time' => $array_add_missing_middle_time,
                            'array_add_missing_middle_time_id' => $array_add_missing_middle_time_id,
                            'array_add_missing_middle_editor_department_code' => $array_add_missing_middle_editor_department_code,
                            'array_add_missing_middle_editor_department_name' => $array_add_missing_middle_editor_department_name,
                            'array_add_missing_middle_editor_user_code' => $array_add_missing_middle_editor_user_code,
                            'array_add_missing_middle_editor_user_name' => $array_add_missing_middle_editor_user_name,
                            'array_add_missing_middle_time_positions' => $array_add_missing_middle_time_positions,
                            'array_add_missing_return_time' => $array_add_missing_return_time,
                            'array_add_missing_return_time_id' => $array_add_missing_return_time_id,
                            'array_add_missing_return_editor_department_code' => $array_add_missing_return_editor_department_code,
                            'array_add_missing_return_editor_department_name' => $array_add_missing_return_editor_department_name,
                            'array_add_missing_return_editor_user_code' => $array_add_missing_return_editor_user_code,
                            'array_add_missing_return_editor_user_name' => $array_add_missing_return_editor_user_name,
                            'array_add_missing_return_time_positions' => $array_add_missing_return_time_positions,
                            'array_add_public_going_out_time' => $array_add_public_going_out_time,
                            'array_add_public_going_out_time_id' => $array_add_public_going_out_time_id,
                            'array_add_public_going_out_editor_department_code' => $array_add_public_going_out_editor_department_code,
                            'array_add_public_going_out_editor_department_name' => $array_add_public_going_out_editor_department_name,
                            'array_add_public_going_out_editor_user_code' => $array_add_public_going_out_editor_user_code,
                            'array_add_public_going_out_editor_user_name' => $array_add_public_going_out_editor_user_name,
                            'array_add_public_going_out_time_positions' => $array_add_public_going_out_time_positions,
                            'array_add_public_return_time' => $array_add_public_return_time,
                            'array_add_public_return_time_id' => $array_add_public_return_time_id,
                            'array_add_public_return_editor_department_code' => $array_add_public_return_editor_department_code,
                            'array_add_public_return_editor_department_name' => $array_add_public_return_editor_department_name,
                            'array_add_public_return_editor_user_code' => $array_add_public_return_editor_user_code,
                            'array_add_public_return_editor_user_name' => $array_add_public_return_editor_user_name,
                            'array_add_public_return_time_positions' => $array_add_public_return_time_positions,
                            'array_break_worktimetable_result' => $array_break_worktimetable_result,
                            'array_add_emergency_time' => $array_add_emergency_time,
                            'array_add_emergency_time_id' => $array_add_emergency_time_id,
                            'array_add_emergency_editor_department_code' => $array_add_emergency_editor_department_code,
                            'array_add_emergency_editor_department_name' => $array_add_emergency_editor_department_name,
                            'array_add_emergency_editor_user_code' => $array_add_emergency_editor_user_code,
                            'array_add_emergency_editor_user_name' => $array_add_emergency_editor_user_name,
                            'array_add_emergency_time_positions' => $array_add_emergency_time_positions,
                            'array_add_emergency_return_time' => $array_add_emergency_return_time,
                            'array_add_emergency_return_time_id' => $array_add_emergency_return_time_id,
                            'array_add_emergency_return_editor_department_code' => $array_add_emergency_return_editor_department_code,
                            'array_add_emergency_return_editor_department_name' => $array_add_emergency_return_editor_department_name,
                            'array_add_emergency_return_editor_user_code' => $array_add_emergency_return_editor_user_code,
                            'array_add_emergency_return_editor_user_name' => $array_add_emergency_return_editor_user_name,
                            'array_add_emergency_return_time_positions' => $array_add_emergency_return_time_positions,
                            'array_get_timetable_result' => $array_get_timetable_result
                        );
                        $add_result = $this->addTempWorkingTimeDate($array_impl_addTempWorkingTimeDate);
                        // 次データ計算事前処理
                        $array_result_NextData =
                            $this->calcTempWorkingTimeDateNextData(
                                $array_working_time_kubun,
                                $result
                            );
                        $array_attendance_calc_time = $array_result_NextData['array_attendance_calc_time'];
                        $array_missing_middle_time = $array_result_NextData['array_missing_middle_time'];
                        $array_public_going_out_time = $array_result_NextData['array_public_going_out_time'];
                        $array_emergency_time = $array_result_NextData['array_emergency_time'];
                        $array_add_attendance_time = $array_result_NextData['array_add_attendance_time'];
                        $array_add_leaving_time = $array_result_NextData['array_add_leaving_time'];
                        $array_add_missing_middle_time = $array_result_NextData['array_add_missing_middle_time'];
                        $array_add_missing_return_time = $array_result_NextData['array_add_missing_return_time'];
                        $array_add_public_going_out_time = $array_result_NextData['array_add_public_going_out_time'];
                        $array_add_public_return_time = $array_result_NextData['array_add_public_return_time'];
                        $array_add_emergency_time = $array_result_NextData['array_add_emergency_time'];
                        $array_add_emergency_return_time = $array_result_NextData['array_add_emergency_return_time'];
                        $array_add_attendance_time_positions = $array_result_NextData['array_add_attendance_time_positions'];
                        $array_add_leaving_time_positions = $array_result_NextData['array_add_leaving_time_positions'];
                        $array_add_missing_middle_time_positions = $array_result_NextData['array_add_missing_middle_time_positions'];
                        $array_add_missing_return_time_positions = $array_result_NextData['array_add_missing_return_time_positions'];
                        $array_add_public_going_out_time_positions = $array_result_NextData['array_add_public_going_out_time_positions'];
                        $array_add_public_return_time_positions = $array_result_NextData['array_add_public_return_time_positions'];
                        $array_add_emergency_time_positions = $array_result_NextData['array_add_emergency_time_positions'];
                        $array_add_emergency_return_time_positions = $array_result_NextData['array_add_emergency_return_time_positions'];
                        $array_add_attendance_time_id = $array_result_NextData['array_add_attendance_time_id'];
                        $array_add_attendance_editor_department_code = $array_result_NextData['array_add_attendance_editor_department_code'];
                        $array_add_attendance_editor_department_name = $array_result_NextData['array_add_attendance_editor_department_name'];
                        $array_add_attendance_editor_user_code = $array_result_NextData['array_add_attendance_editor_user_code'];
                        $array_add_attendance_editor_user_name = $array_result_NextData['array_add_attendance_editor_user_name'];
                        $array_add_leaving_time_id = $array_result_NextData['array_add_leaving_time_id'];
                        $array_add_leaving_editor_department_code = $array_result_NextData['array_add_leaving_editor_department_code'];
                        $array_add_leaving_editor_department_name = $array_result_NextData['array_add_leaving_editor_department_name'];
                        $array_add_leaving_editor_user_code = $array_result_NextData['array_add_leaving_editor_user_code'];
                        $array_add_leaving_editor_user_name = $array_result_NextData['array_add_leaving_editor_user_name'];
                        $array_add_missing_middle_time_id = $array_result_NextData['array_add_missing_middle_time_id'];
                        $array_add_missing_middle_editor_department_code = $array_result_NextData['array_add_missing_middle_editor_department_code'];
                        $array_add_missing_middle_editor_department_name = $array_result_NextData['array_add_missing_middle_editor_department_name'];
                        $array_add_missing_middle_editor_user_code = $array_result_NextData['array_add_missing_middle_editor_user_code'];
                        $array_add_missing_middle_editor_user_name = $array_result_NextData['array_add_missing_middle_editor_user_name'];
                        $array_add_missing_return_time_id = $array_result_NextData['array_add_missing_return_time_id'];
                        $array_add_missing_return_editor_department_code = $array_result_NextData['array_add_missing_return_editor_department_code'];
                        $array_add_missing_return_editor_department_name = $array_result_NextData['array_add_missing_return_editor_department_name'];
                        $array_add_missing_return_editor_user_code = $array_result_NextData['array_add_missing_return_editor_user_code'];
                        $array_add_missing_return_editor_user_name = $array_result_NextData['array_add_missing_return_editor_user_name'];
                        $array_add_public_going_out_time_id = $array_result_NextData['array_add_public_going_out_time_id'];
                        $array_add_public_going_out_editor_department_code = $array_result_NextData['array_add_public_going_out_editor_department_code'];
                        $array_add_public_going_out_editor_department_name = $array_result_NextData['array_add_public_going_out_editor_department_name'];
                        $array_add_public_going_out_editor_user_code = $array_result_NextData['array_add_public_going_out_editor_user_code'];
                        $array_add_public_going_out_editor_user_name = $array_result_NextData['array_add_public_going_out_editor_user_name'];
                        $array_add_public_return_time_id = $array_result_NextData['array_add_public_return_time_id'];
                        $array_add_public_return_editor_department_code = $array_result_NextData['array_add_public_return_editor_department_code'];
                        $array_add_public_return_editor_department_name = $array_result_NextData['array_add_public_return_editor_department_name'];
                        $array_add_public_return_editor_user_code = $array_result_NextData['array_add_public_return_editor_user_code'];
                        $array_add_public_return_editor_user_name = $array_result_NextData['array_add_public_return_editor_user_name'];
                        $array_add_emergency_time_id = $array_result_NextData['array_add_emergency_time_id'];
                        $array_add_emergency_editor_department_code = $array_result_NextData['array_add_emergency_editor_department_code'];
                        $array_add_emergency_editor_department_name = $array_result_NextData['array_add_emergency_editor_department_name'];
                        $array_add_emergency_editor_user_code = $array_result_NextData['array_add_emergency_editor_user_code'];
                        $array_add_emergency_editor_user_name = $array_result_NextData['array_add_emergency_editor_user_name'];
                        $array_add_emergency_return_time_id = $array_result_NextData['array_add_emergency_return_time_id'];
                        $array_add_emergency_return_editor_department_code = $array_result_NextData['array_add_emergency_return_editor_department_code'];
                        $array_add_emergency_return_editor_department_name = $array_result_NextData['array_add_emergency_return_editor_department_name'];
                        $array_add_emergency_return_editor_user_code = $array_result_NextData['array_add_emergency_return_editor_user_code'];
                        $array_add_emergency_return_editor_user_name = $array_result_NextData['array_add_emergency_return_editor_user_name'];
                        $attendance_time = $array_result_NextData['attendance_time'];
                        $leaving_time = $array_result_NextData['leaving_time'];
                        $missing_middle_time = $array_result_NextData['missing_middle_time'];
                        $missing_middle_return_time = $array_result_NextData['missing_middle_return_time'];
                        $public_going_out_time = $array_result_NextData['public_going_out_time'];
                        $public_going_out_return_time = $array_result_NextData['public_going_out_return_time'];
                        $emergency_time = $array_result_NextData['emergency_time'];
                        $emergency_return_time = $array_result_NextData['emergency_return_time'];
                        $attendance_time_positions = $array_result_NextData['attendance_time_positions'];
                        $leaving_time_positions = $array_result_NextData['leaving_time_positions'];
                        $missing_middle_time_positions = $array_result_NextData['missing_middle_time_positions'];
                        $missing_return_time_positions = $array_result_NextData['missing_return_time_positions'];
                        $public_going_out_time_positions = $array_result_NextData['public_going_out_time_positions'];
                        $public_return_time_positions = $array_result_NextData['public_return_time_positions'];
                        $emergency_time_positions = $array_result_NextData['emergency_time_positions'];
                        $emergency_return_time_positions = $array_result_NextData['emergency_return_time_positions'];
                        $attendance_time_id = $array_result_NextData['attendance_time_id'];
                        $leaving_time_id = $array_result_NextData['leaving_time_id'];
                        $missing_middle_time_id = $array_result_NextData['missing_middle_time_id'];
                        $missing_middle_return_time_id = $array_result_NextData['missing_middle_return_time_id'];
                        $public_going_out_time_id = $array_result_NextData['public_going_out_time_id'];
                        $public_going_out_return_time_id = $array_result_NextData['public_going_out_return_time_id'];
                        $emergency_time_id = $array_result_NextData['emergency_time_id'];
                        $emergency_return_time_id = $array_result_NextData['emergency_return_time_id'];
                        $attendance_editor_department_code = $array_result_NextData['attendance_editor_department_code'];
                        $attendance_editor_department_name = $array_result_NextData['attendance_editor_department_name'];
                        $attendance_editor_user_code = $array_result_NextData['attendance_editor_user_code'];
                        $attendance_editor_user_name = $array_result_NextData['attendance_editor_user_name'];
                        $leaving_editor_department_code = $array_result_NextData['leaving_editor_department_code'];
                        $leaving_editor_department_name = $array_result_NextData['leaving_editor_department_name'];
                        $leaving_editor_user_code = $array_result_NextData['leaving_editor_user_code'];
                        $leaving_editor_user_name = $array_result_NextData['leaving_editor_user_name'];
                        $missing_middle_editor_department_code = $array_result_NextData['missing_middle_editor_department_code'];
                        $missing_middle_editor_department_name = $array_result_NextData['missing_middle_editor_department_name'];
                        $missing_middle_editor_user_code = $array_result_NextData['missing_middle_editor_user_code'];
                        $missing_middle_editor_user_name = $array_result_NextData['missing_middle_editor_user_name'];
                        $missing_return_editor_department_code = $array_result_NextData['missing_return_editor_department_code'];
                        $missing_return_editor_department_name = $array_result_NextData['missing_return_editor_department_name'];
                        $missing_return_editor_user_code = $array_result_NextData['missing_return_editor_user_code'];
                        $missing_return_editor_user_name = $array_result_NextData['missing_return_editor_user_name'];
                        $public_going_out_editor_department_code = $array_result_NextData['public_going_out_editor_department_code'];
                        $public_going_out_editor_department_name = $array_result_NextData['public_going_out_editor_department_name'];
                        $public_going_out_editor_user_code = $array_result_NextData['public_going_out_editor_user_code'];
                        $public_going_out_editor_user_name = $array_result_NextData['public_going_out_editor_user_name'];
                        $public_return_editor_department_code = $array_result_NextData['public_return_editor_department_code'];
                        $public_return_editor_department_name = $array_result_NextData['public_return_editor_department_name'];
                        $public_return_editor_user_code = $array_result_NextData['public_return_editor_user_code'];
                        $public_return_editor_user_name = $array_result_NextData['public_return_editor_user_name'];
                        $emergency_editor_department_code = $array_result_NextData['emergency_editor_department_code'];
                        $emergency_editor_department_name = $array_result_NextData['emergency_editor_department_name'];
                        $emergency_editor_user_code = $array_result_NextData['emergency_editor_user_code'];
                        $emergency_editor_user_name = $array_result_NextData['emergency_editor_user_name'];
                        $emergency_return_editor_department_code = $array_result_NextData['emergency_return_editor_department_code'];
                        $emergency_return_editor_department_name = $array_result_NextData['emergency_return_editor_department_name'];
                        $emergency_return_editor_user_code = $array_result_NextData['emergency_return_editor_user_code'];
                        $emergency_return_editor_user_name = $array_result_NextData['emergency_return_editor_user_name'];
                        // 同じ値にする
                        $before_date = $current_date;
                        $before_department_code = $current_department_code;
                        $before_user_code = $current_user_code; 
                        $before_result = $result;
                        $this->not_employment_working = 0;
                        $working_status = $result->working_status;
                        $note = '';
                        $late = '';
                        $leave_early = '';
                        $to_be_confirmed = '';
                        $calc_nobreak_cnt = 0;
                        $array_notelateetc = $this->setNoteLateEtc($result);
                        $note .= $array_notelateetc[0];
                        Log::debug('setNoteLateEtc $note =  '.$note);
                        $late = $array_notelateetc[1];
                        $leave_early = $array_notelateetc[2];
                        $to_be_confirmed = $array_notelateetc[3];
                        $calc_nobreak_cnt++;
                        // ユーザー休暇区分判定用
                        $before_holiday_set = true;
                        $before_holiday_date = $current_date;
                        $before_holiday_user_code = $current_user_code;
                        $before_holiday_department_code = $current_department_code;
                        $before_holiday_kubun = $result->holiday_kubun;
                    }
                }
            } else {
                // 日付が変わった場合
                Log::debug('日付が変わった場合 ');
                Log::debug('--- '.$before_result->user_name.' 終了  ------ '.$before_date.' モード  ------ '.$before_result->mode.' ---- $calc_nobreak_cnt = '.$calc_nobreak_cnt );
                Log::debug('--- '.$result->user_name.' 開始  ------ '.$current_date.' モード  ------ '.$result->mode.' ---- $calc_nobreak_cnt = '.$calc_nobreak_cnt );
                try{
                    if ($working_status == 0 ) {
                        Log::debug('当日分　勤務状態 打刻時刻 =  '.$before_result->record_datetime);
                        Log::debug('当日分　勤務状態 現在時刻 =  '.$dtNow);
                        if ($before_result->record_datetime < $dtNow) {                            // 打刻時刻 < 現在時刻
                            $working_status = $before_result->working_status;
                        }
                        Log::debug('当日分　勤務状態 =  '.$working_status);
                    }
                    if ($calc_nobreak_cnt == 0) {
                        $array_notelateetc = $this->setNoteLateEtc($before_result);
                        $note .= $array_notelateetc[0];
                        Log::debug('setNoteLateEtc $note =  '.$note);
                        $late = $array_notelateetc[1];
                        $leave_early = $array_notelateetc[2];
                        $to_be_confirmed = $array_notelateetc[3];
                    }
                    // １個前のユーザーが休暇設定されていた場合はすでに登録済み
                    if ($before_holiday_set == false) {
                        // ユーザー労働時間登録(１個前のユーザーを登録する)
                        Log::debug('        temp_working_time_datesデータ作成開始 ');
                        Log::debug('            １個前のユーザーを登録 '.$before_user_code);
                        // setLeavingCollectPtn implement
                        $array_impl_addTempWorkingTimeDate = array (
                            'target_date' => $before_date,
                            'target_user_code' => $before_user_code,
                            'target_department_code' => $before_department_code,
                            'target_result' => $before_result,
                            'note' => $note,
                            'working_status' => $working_status,
                            'timetables' => $timetables,
                            'array_attendance_calc_time' => $array_attendance_calc_time,
                            'array_missing_middle_time' => $array_missing_middle_time,
                            'array_public_going_out_time' => $array_public_going_out_time,
                            'array_emergency_time' => $array_emergency_time,
                            'array_add_attendance_time' => $array_add_attendance_time,
                            'array_add_attendance_time_id' => $array_add_attendance_time_id,
                            'array_add_attendance_editor_department_code' => $array_add_attendance_editor_department_code,
                            'array_add_attendance_editor_department_name' => $array_add_attendance_editor_department_name,
                            'array_add_attendance_editor_user_code' => $array_add_attendance_editor_user_code,
                            'array_add_attendance_editor_user_name' => $array_add_attendance_editor_user_name,
                            'array_add_attendance_time_positions' => $array_add_attendance_time_positions,
                            'array_add_leaving_time' => $array_add_leaving_time,
                            'array_add_leaving_time_id' => $array_add_leaving_time_id,
                            'array_add_leaving_editor_department_code' => $array_add_leaving_editor_department_code,
                            'array_add_leaving_editor_department_name' => $array_add_leaving_editor_department_name,
                            'array_add_leaving_editor_user_code' => $array_add_leaving_editor_user_code,
                            'array_add_leaving_editor_user_name' => $array_add_leaving_editor_user_name,
                            'array_add_leaving_time_positions' => $array_add_leaving_time_positions,
                            'array_add_missing_middle_time' => $array_add_missing_middle_time,
                            'array_add_missing_middle_time_id' => $array_add_missing_middle_time_id,
                            'array_add_missing_middle_editor_department_code' => $array_add_missing_middle_editor_department_code,
                            'array_add_missing_middle_editor_department_name' => $array_add_missing_middle_editor_department_name,
                            'array_add_missing_middle_editor_user_code' => $array_add_missing_middle_editor_user_code,
                            'array_add_missing_middle_editor_user_name' => $array_add_missing_middle_editor_user_name,
                            'array_add_missing_middle_time_positions' => $array_add_missing_middle_time_positions,
                            'array_add_missing_return_time' => $array_add_missing_return_time,
                            'array_add_missing_return_time_id' => $array_add_missing_return_time_id,
                            'array_add_missing_return_editor_department_code' => $array_add_missing_return_editor_department_code,
                            'array_add_missing_return_editor_department_name' => $array_add_missing_return_editor_department_name,
                            'array_add_missing_return_editor_user_code' => $array_add_missing_return_editor_user_code,
                            'array_add_missing_return_editor_user_name' => $array_add_missing_return_editor_user_name,
                            'array_add_missing_return_time_positions' => $array_add_missing_return_time_positions,
                            'array_add_public_going_out_time' => $array_add_public_going_out_time,
                            'array_add_public_going_out_time_id' => $array_add_public_going_out_time_id,
                            'array_add_public_going_out_editor_department_code' => $array_add_public_going_out_editor_department_code,
                            'array_add_public_going_out_editor_department_name' => $array_add_public_going_out_editor_department_name,
                            'array_add_public_going_out_editor_user_code' => $array_add_public_going_out_editor_user_code,
                            'array_add_public_going_out_editor_user_name' => $array_add_public_going_out_editor_user_name,
                            'array_add_public_going_out_time_positions' => $array_add_public_going_out_time_positions,
                            'array_add_public_return_time' => $array_add_public_return_time,
                            'array_add_public_return_time_id' => $array_add_public_return_time_id,
                            'array_add_public_return_editor_department_code' => $array_add_public_return_editor_department_code,
                            'array_add_public_return_editor_department_name' => $array_add_public_return_editor_department_name,
                            'array_add_public_return_editor_user_code' => $array_add_public_return_editor_user_code,
                            'array_add_public_return_editor_user_name' => $array_add_public_return_editor_user_name,
                            'array_add_public_return_time_positions' => $array_add_public_return_time_positions,
                            'array_break_worktimetable_result' => $array_break_worktimetable_result,
                            'array_add_emergency_time' => $array_add_emergency_time,
                            'array_add_emergency_time_id' => $array_add_emergency_time_id,
                            'array_add_emergency_editor_department_code' => $array_add_emergency_editor_department_code,
                            'array_add_emergency_editor_department_name' => $array_add_emergency_editor_department_name,
                            'array_add_emergency_editor_user_code' => $array_add_emergency_editor_user_code,
                            'array_add_emergency_editor_user_name' => $array_add_emergency_editor_user_name,
                            'array_add_emergency_time_positions' => $array_add_emergency_time_positions,
                            'array_add_emergency_return_time' => $array_add_emergency_return_time,
                            'array_add_emergency_return_time_id' => $array_add_emergency_return_time_id,
                            'array_add_emergency_return_editor_department_code' => $array_add_emergency_return_editor_department_code,
                            'array_add_emergency_return_editor_department_name' => $array_add_emergency_return_editor_department_name,
                            'array_add_emergency_return_editor_user_code' => $array_add_emergency_return_editor_user_code,
                            'array_add_emergency_return_editor_user_name' => $array_add_emergency_return_editor_user_name,
                            'array_add_emergency_return_time_positions' => $array_add_emergency_return_time_positions,
                            'array_get_timetable_result' => $array_get_timetable_result
                        );
                        $add_result = $this->addTempWorkingTimeDate($array_impl_addTempWorkingTimeDate);
                    }
                    $before_holiday_set = false;
                    // 日付とユーザー休暇区分を保存
                    $before_holiday_date = $current_date;
                    $before_holiday_user_code = $current_user_code;
                    $before_holiday_department_code = $current_department_code;
                    $before_holiday_kubun = $result->holiday_kubun;
                    // 次データ計算事前処理
                    $array_result_NextData =
                        $this->calcTempWorkingTimeDateNextData(
                            $array_working_time_kubun,
                            $result
                        );
                    $array_attendance_calc_time = $array_result_NextData['array_attendance_calc_time'];
                    $array_missing_middle_time = $array_result_NextData['array_missing_middle_time'];
                    $array_public_going_out_time = $array_result_NextData['array_public_going_out_time'];
                    $array_emergency_time = $array_result_NextData['array_emergency_time'];
                    $array_add_attendance_time = $array_result_NextData['array_add_attendance_time'];
                    $array_add_leaving_time = $array_result_NextData['array_add_leaving_time'];
                    $array_add_missing_middle_time = $array_result_NextData['array_add_missing_middle_time'];
                    $array_add_missing_return_time = $array_result_NextData['array_add_missing_return_time'];
                    $array_add_public_going_out_time = $array_result_NextData['array_add_public_going_out_time'];
                    $array_add_public_return_time = $array_result_NextData['array_add_public_return_time'];
                    $array_add_emergency_time = $array_result_NextData['array_add_emergency_time'];
                    $array_add_emergency_return_time = $array_result_NextData['array_add_emergency_return_time'];
                    $array_add_attendance_time_positions = $array_result_NextData['array_add_attendance_time_positions'];
                    $array_add_leaving_time_positions = $array_result_NextData['array_add_leaving_time_positions'];
                    $array_add_missing_middle_time_positions = $array_result_NextData['array_add_missing_middle_time_positions'];
                    $array_add_missing_return_time_positions = $array_result_NextData['array_add_missing_return_time_positions'];
                    $array_add_public_going_out_time_positions = $array_result_NextData['array_add_public_going_out_time_positions'];
                    $array_add_public_return_time_positions = $array_result_NextData['array_add_public_return_time_positions'];
                    $array_add_emergency_time_positions = $array_result_NextData['array_add_emergency_time_positions'];
                    $array_add_emergency_return_time_positions = $array_result_NextData['array_add_emergency_return_time_positions'];
                    $array_add_attendance_time_id = $array_result_NextData['array_add_attendance_time_id'];
                    $array_add_attendance_editor_department_code = $array_result_NextData['array_add_attendance_editor_department_code'];
                    $array_add_attendance_editor_department_name = $array_result_NextData['array_add_attendance_editor_department_name'];
                    $array_add_attendance_editor_user_code = $array_result_NextData['array_add_attendance_editor_user_code'];
                    $array_add_attendance_editor_user_name = $array_result_NextData['array_add_attendance_editor_user_name'];
                    $array_add_leaving_time_id = $array_result_NextData['array_add_leaving_time_id'];
                    $array_add_leaving_editor_department_code = $array_result_NextData['array_add_leaving_editor_department_code'];
                    $array_add_leaving_editor_department_name = $array_result_NextData['array_add_leaving_editor_department_name'];
                    $array_add_leaving_editor_user_code = $array_result_NextData['array_add_leaving_editor_user_code'];
                    $array_add_leaving_editor_user_name = $array_result_NextData['array_add_leaving_editor_user_name'];
                    $array_add_missing_middle_time_id = $array_result_NextData['array_add_missing_middle_time_id'];
                    $array_add_missing_middle_editor_department_code = $array_result_NextData['array_add_missing_middle_editor_department_code'];
                    $array_add_missing_middle_editor_department_name = $array_result_NextData['array_add_missing_middle_editor_department_name'];
                    $array_add_missing_middle_editor_user_code = $array_result_NextData['array_add_missing_middle_editor_user_code'];
                    $array_add_missing_middle_editor_user_name = $array_result_NextData['array_add_missing_middle_editor_user_name'];
                    $array_add_missing_return_time_id = $array_result_NextData['array_add_missing_return_time_id'];
                    $array_add_missing_return_editor_department_code = $array_result_NextData['array_add_missing_return_editor_department_code'];
                    $array_add_missing_return_editor_department_name = $array_result_NextData['array_add_missing_return_editor_department_name'];
                    $array_add_missing_return_editor_user_code = $array_result_NextData['array_add_missing_return_editor_user_code'];
                    $array_add_missing_return_editor_user_name = $array_result_NextData['array_add_missing_return_editor_user_name'];
                    $array_add_public_going_out_time_id = $array_result_NextData['array_add_public_going_out_time_id'];
                    $array_add_public_going_out_editor_department_code = $array_result_NextData['array_add_public_going_out_editor_department_code'];
                    $array_add_public_going_out_editor_department_name = $array_result_NextData['array_add_public_going_out_editor_department_name'];
                    $array_add_public_going_out_editor_user_code = $array_result_NextData['array_add_public_going_out_editor_user_code'];
                    $array_add_public_going_out_editor_user_name = $array_result_NextData['array_add_public_going_out_editor_user_name'];
                    $array_add_public_return_time_id = $array_result_NextData['array_add_public_return_time_id'];
                    $array_add_public_return_editor_department_code = $array_result_NextData['array_add_public_return_editor_department_code'];
                    $array_add_public_return_editor_department_name = $array_result_NextData['array_add_public_return_editor_department_name'];
                    $array_add_public_return_editor_user_code = $array_result_NextData['array_add_public_return_editor_user_code'];
                    $array_add_public_return_editor_user_name = $array_result_NextData['array_add_public_return_editor_user_name'];
                    $array_add_emergency_time_id = $array_result_NextData['array_add_emergency_time_id'];
                    $array_add_emergency_editor_department_code = $array_result_NextData['array_add_emergency_editor_department_code'];
                    $array_add_emergency_editor_department_name = $array_result_NextData['array_add_emergency_editor_department_name'];
                    $array_add_emergency_editor_user_code = $array_result_NextData['array_add_emergency_editor_user_code'];
                    $array_add_emergency_editor_user_name = $array_result_NextData['array_add_emergency_editor_user_name'];
                    $array_add_emergency_return_time_id = $array_result_NextData['array_add_emergency_return_time_id'];
                    $array_add_emergency_return_editor_department_code = $array_result_NextData['array_add_emergency_return_editor_department_code'];
                    $array_add_emergency_return_editor_department_name = $array_result_NextData['array_add_emergency_return_editor_department_name'];
                    $array_add_emergency_return_editor_user_code = $array_result_NextData['array_add_emergency_return_editor_user_code'];
                    $array_add_emergency_return_editor_user_name = $array_result_NextData['array_add_emergency_return_editor_user_name'];
                    $attendance_time = $array_result_NextData['attendance_time'];
                    $leaving_time = $array_result_NextData['leaving_time'];
                    $missing_middle_time = $array_result_NextData['missing_middle_time'];
                    $missing_middle_return_time = $array_result_NextData['missing_middle_return_time'];
                    $public_going_out_time = $array_result_NextData['public_going_out_time'];
                    $public_going_out_return_time = $array_result_NextData['public_going_out_return_time'];
                    $emergency_time = $array_result_NextData['emergency_time'];
                    $emergency_return_time = $array_result_NextData['emergency_return_time'];
                    $attendance_time_positions = $array_result_NextData['attendance_time_positions'];
                    $leaving_time_positions = $array_result_NextData['leaving_time_positions'];
                    $missing_middle_time_positions = $array_result_NextData['missing_middle_time_positions'];
                    $missing_return_time_positions = $array_result_NextData['missing_return_time_positions'];
                    $public_going_out_time_positions = $array_result_NextData['public_going_out_time_positions'];
                    $public_return_time_positions = $array_result_NextData['public_return_time_positions'];
                    $emergency_time_positions = $array_result_NextData['emergency_time_positions'];
                    $emergency_return_time_positions = $array_result_NextData['emergency_return_time_positions'];
                    $attendance_time_id = $array_result_NextData['attendance_time_id'];
                    $leaving_time_id = $array_result_NextData['leaving_time_id'];
                    $missing_middle_time_id = $array_result_NextData['missing_middle_time_id'];
                    $missing_middle_return_time_id = $array_result_NextData['missing_middle_return_time_id'];
                    $public_going_out_time_id = $array_result_NextData['public_going_out_time_id'];
                    $public_going_out_return_time_id = $array_result_NextData['public_going_out_return_time_id'];
                    $emergency_time_id = $array_result_NextData['emergency_time_id'];
                    $emergency_return_time_id = $array_result_NextData['emergency_return_time_id'];
                    $attendance_editor_department_code = $array_result_NextData['attendance_editor_department_code'];
                    $attendance_editor_department_name = $array_result_NextData['attendance_editor_department_name'];
                    $attendance_editor_user_code = $array_result_NextData['attendance_editor_user_code'];
                    $attendance_editor_user_name = $array_result_NextData['attendance_editor_user_name'];
                    $leaving_editor_department_code = $array_result_NextData['leaving_editor_department_code'];
                    $leaving_editor_department_name = $array_result_NextData['leaving_editor_department_name'];
                    $leaving_editor_user_code = $array_result_NextData['leaving_editor_user_code'];
                    $leaving_editor_user_name = $array_result_NextData['leaving_editor_user_name'];
                    $missing_middle_editor_department_code = $array_result_NextData['missing_middle_editor_department_code'];
                    $missing_middle_editor_department_name = $array_result_NextData['missing_middle_editor_department_name'];
                    $missing_middle_editor_user_code = $array_result_NextData['missing_middle_editor_user_code'];
                    $missing_middle_editor_user_name = $array_result_NextData['missing_middle_editor_user_name'];
                    $missing_return_editor_department_code = $array_result_NextData['missing_return_editor_department_code'];
                    $missing_return_editor_department_name = $array_result_NextData['missing_return_editor_department_name'];
                    $missing_return_editor_user_code = $array_result_NextData['missing_return_editor_user_code'];
                    $missing_return_editor_user_name = $array_result_NextData['missing_return_editor_user_name'];
                    $public_going_out_editor_department_code = $array_result_NextData['public_going_out_editor_department_code'];
                    $public_going_out_editor_department_name = $array_result_NextData['public_going_out_editor_department_name'];
                    $public_going_out_editor_user_code = $array_result_NextData['public_going_out_editor_user_code'];
                    $public_going_out_editor_user_name = $array_result_NextData['public_going_out_editor_user_name'];
                    $public_return_editor_department_code = $array_result_NextData['public_return_editor_department_code'];
                    $public_return_editor_department_name = $array_result_NextData['public_return_editor_department_name'];
                    $public_return_editor_user_code = $array_result_NextData['public_return_editor_user_code'];
                    $public_return_editor_user_name = $array_result_NextData['public_return_editor_user_name'];
                    $emergency_editor_department_code = $array_result_NextData['emergency_editor_department_code'];
                    $emergency_editor_department_name = $array_result_NextData['emergency_editor_department_name'];
                    $emergency_editor_user_code = $array_result_NextData['emergency_editor_user_code'];
                    $emergency_editor_user_name = $array_result_NextData['emergency_editor_user_name'];
                    $emergency_return_editor_department_code = $array_result_NextData['emergency_return_editor_department_code'];
                    $emergency_return_editor_department_name = $array_result_NextData['emergency_return_editor_department_name'];
                    $emergency_return_editor_user_code = $array_result_NextData['emergency_return_editor_user_code'];
                    $emergency_return_editor_user_name = $array_result_NextData['emergency_return_editor_user_name'];
                    // 同じ値にする
                    $before_date = $current_date;
                    $before_department_code = $current_department_code;
                    $before_user_code = $current_user_code; 
                    $before_result = $result;
                    $this->not_employment_working = 0;
                    $working_status = $result->working_status;
                    $note = '';
                    $late = '';
                    $leave_early = '';
                    $to_be_confirmed = '';
                    $calc_nobreak_cnt = 0;
                    $array_notelateetc = $this->setNoteLateEtc($result);
                    $note .= $array_notelateetc[0];
                    Log::debug('setNoteLateEtc $note =  '.$note);
                    $late = $array_notelateetc[1];
                    $leave_early = $array_notelateetc[2];
                    $to_be_confirmed = $array_notelateetc[3];
                    $calc_nobreak_cnt++;
                }catch(\PDOException $pe){
                    $add_result = false;
                    throw $pe;
                }catch(\Exception $e){
                    Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.data_error_dailycalc'));
                    Log::error($e->getMessage());
                    $add_result = false;
                    throw $e;
                }
                // 現データが当日計算対象で、出勤していない休暇設定されていればデータ作成
                //if ($result->current_calc == '1' && isset($result->holiday_kubun)) {
                if ($result->current_calc == '1' && !$mode_chk) {
                    Log::debug('        temp_working_time_datesデータ作成開始 ');
                    Log::debug('            現データが当日計算対象データ作成 =  '.$current_user_code);
                    Log::debug('                休暇区分  = '.$result->holiday_kubun);
                    Log::debug('        用途フリー項目 =  '.$result->use_free_item);
                    if ((!isset($result->use_free_item)) ||
                        (isset($result->use_free_item) &&
                        (substr($result->use_free_item, Config::get('const.USEFREEITEM.day_holiday'), 1) == "1"))) {
                        // setLeavingCollectPtn implement
                        $array_impl_addTempWorkingTimeDate = array (
                            'target_date' => $current_date,
                            'target_user_code' => $current_user_code,
                            'target_department_code' => $current_department_code,
                            'target_result' => $current_result,
                            'note' => $note,
                            'working_status' => $working_status,
                            'timetables' => $timetables,
                            'array_attendance_calc_time' => $array_attendance_calc_time,
                            'array_missing_middle_time' => $array_missing_middle_time,
                            'array_public_going_out_time' => $array_public_going_out_time,
                            'array_emergency_time' => $array_emergency_time,
                            'array_add_attendance_time' => $array_add_attendance_time,
                            'array_add_attendance_time_id' => $array_add_attendance_time_id,
                            'array_add_attendance_editor_department_code' => $array_add_attendance_editor_department_code,
                            'array_add_attendance_editor_department_name' => $array_add_attendance_editor_department_name,
                            'array_add_attendance_editor_user_code' => $array_add_attendance_editor_user_code,
                            'array_add_attendance_editor_user_name' => $array_add_attendance_editor_user_name,
                            'array_add_attendance_time_positions' => $array_add_attendance_time_positions,
                            'array_add_leaving_time' => $array_add_leaving_time,
                            'array_add_leaving_time_id' => $array_add_leaving_time_id,
                            'array_add_leaving_editor_department_code' => $array_add_leaving_editor_department_code,
                            'array_add_leaving_editor_department_name' => $array_add_leaving_editor_department_name,
                            'array_add_leaving_editor_user_code' => $array_add_leaving_editor_user_code,
                            'array_add_leaving_editor_user_name' => $array_add_leaving_editor_user_name,
                            'array_add_leaving_time_positions' => $array_add_leaving_time_positions,
                            'array_add_missing_middle_time' => $array_add_missing_middle_time,
                            'array_add_missing_middle_time_id' => $array_add_missing_middle_time_id,
                            'array_add_missing_middle_editor_department_code' => $array_add_missing_middle_editor_department_code,
                            'array_add_missing_middle_editor_department_name' => $array_add_missing_middle_editor_department_name,
                            'array_add_missing_middle_editor_user_code' => $array_add_missing_middle_editor_user_code,
                            'array_add_missing_middle_editor_user_name' => $array_add_missing_middle_editor_user_name,
                            'array_add_missing_middle_time_positions' => $array_add_missing_middle_time_positions,
                            'array_add_missing_return_time' => $array_add_missing_return_time,
                            'array_add_missing_return_time_id' => $array_add_missing_return_time_id,
                            'array_add_missing_return_editor_department_code' => $array_add_missing_return_editor_department_code,
                            'array_add_missing_return_editor_department_name' => $array_add_missing_return_editor_department_name,
                            'array_add_missing_return_editor_user_code' => $array_add_missing_return_editor_user_code,
                            'array_add_missing_return_editor_user_name' => $array_add_missing_return_editor_user_name,
                            'array_add_missing_return_time_positions' => $array_add_missing_return_time_positions,
                            'array_add_public_going_out_time' => $array_add_public_going_out_time,
                            'array_add_public_going_out_time_id' => $array_add_public_going_out_time_id,
                            'array_add_public_going_out_editor_department_code' => $array_add_public_going_out_editor_department_code,
                            'array_add_public_going_out_editor_department_name' => $array_add_public_going_out_editor_department_name,
                            'array_add_public_going_out_editor_user_code' => $array_add_public_going_out_editor_user_code,
                            'array_add_public_going_out_editor_user_name' => $array_add_public_going_out_editor_user_name,
                            'array_add_public_going_out_time_positions' => $array_add_public_going_out_time_positions,
                            'array_add_public_return_time' => $array_add_public_return_time,
                            'array_add_public_return_time_id' => $array_add_public_return_time_id,
                            'array_add_public_return_editor_department_code' => $array_add_public_return_editor_department_code,
                            'array_add_public_return_editor_department_name' => $array_add_public_return_editor_department_name,
                            'array_add_public_return_editor_user_code' => $array_add_public_return_editor_user_code,
                            'array_add_public_return_editor_user_name' => $array_add_public_return_editor_user_name,
                            'array_add_public_return_time_positions' => $array_add_public_return_time_positions,
                            'array_break_worktimetable_result' => $array_break_worktimetable_result,
                            'array_add_emergency_time' => $array_add_emergency_time,
                            'array_add_emergency_time_id' => $array_add_emergency_time_id,
                            'array_add_emergency_editor_department_code' => $array_add_emergency_editor_department_code,
                            'array_add_emergency_editor_department_name' => $array_add_emergency_editor_department_name,
                            'array_add_emergency_editor_user_code' => $array_add_emergency_editor_user_code,
                            'array_add_emergency_editor_user_name' => $array_add_emergency_editor_user_name,
                            'array_add_emergency_time_positions' => $array_add_emergency_time_positions,
                            'array_add_emergency_return_time' => $array_add_emergency_return_time,
                            'array_add_emergency_return_time_id' => $array_add_emergency_return_time_id,
                            'array_add_emergency_return_editor_department_code' => $array_add_emergency_return_editor_department_code,
                            'array_add_emergency_return_editor_department_name' => $array_add_emergency_return_editor_department_name,
                            'array_add_emergency_return_editor_user_code' => $array_add_emergency_return_editor_user_code,
                            'array_add_emergency_return_editor_user_name' => $array_add_emergency_return_editor_user_name,
                            'array_add_emergency_return_time_positions' => $array_add_emergency_return_time_positions,
                            'array_get_timetable_result' => $array_get_timetable_result
                        );
                        $add_result = $this->addTempWorkingTimeDate($array_impl_addTempWorkingTimeDate);
                        // 次データ計算事前処理
                        $array_result_NextData =
                            $this->calcTempWorkingTimeDateNextData(
                                $array_working_time_kubun,
                                $result
                            );
                        $array_attendance_calc_time = $array_result_NextData['array_attendance_calc_time'];
                        $array_missing_middle_time = $array_result_NextData['array_missing_middle_time'];
                        $array_public_going_out_time = $array_result_NextData['array_public_going_out_time'];
                        $array_emergency_time = $array_result_NextData['array_emergency_time'];
                        $array_add_attendance_time = $array_result_NextData['array_add_attendance_time'];
                        $array_add_leaving_time = $array_result_NextData['array_add_leaving_time'];
                        $array_add_missing_middle_time = $array_result_NextData['array_add_missing_middle_time'];
                        $array_add_missing_return_time = $array_result_NextData['array_add_missing_return_time'];
                        $array_add_public_going_out_time = $array_result_NextData['array_add_public_going_out_time'];
                        $array_add_public_return_time = $array_result_NextData['array_add_public_return_time'];
                        $array_add_emergency_time = $array_result_NextData['array_add_emergency_time'];
                        $array_add_emergency_return_time = $array_result_NextData['array_add_emergency_return_time'];
                        $array_add_attendance_time_positions = $array_result_NextData['array_add_attendance_time_positions'];
                        $array_add_leaving_time_positions = $array_result_NextData['array_add_leaving_time_positions'];
                        $array_add_missing_middle_time_positions = $array_result_NextData['array_add_missing_middle_time_positions'];
                        $array_add_missing_return_time_positions = $array_result_NextData['array_add_missing_return_time_positions'];
                        $array_add_public_going_out_time_positions = $array_result_NextData['array_add_public_going_out_time_positions'];
                        $array_add_public_return_time_positions = $array_result_NextData['array_add_public_return_time_positions'];
                        $array_add_emergency_time_positions = $array_result_NextData['array_add_emergency_time_positions'];
                        $array_add_emergency_return_time_positions = $array_result_NextData['array_add_emergency_return_time_positions'];
                        $array_add_attendance_time_id = $array_result_NextData['array_add_attendance_time_id'];
                        $array_add_attendance_editor_department_code = $array_result_NextData['array_add_attendance_editor_department_code'];
                        $array_add_attendance_editor_department_name = $array_result_NextData['array_add_attendance_editor_department_name'];
                        $array_add_attendance_editor_user_code = $array_result_NextData['array_add_attendance_editor_user_code'];
                        $array_add_attendance_editor_user_name = $array_result_NextData['array_add_attendance_editor_user_name'];
                        $array_add_leaving_time_id = $array_result_NextData['array_add_leaving_time_id'];
                        $array_add_leaving_editor_department_code = $array_result_NextData['array_add_leaving_editor_department_code'];
                        $array_add_leaving_editor_department_name = $array_result_NextData['array_add_leaving_editor_department_name'];
                        $array_add_leaving_editor_user_code = $array_result_NextData['array_add_leaving_editor_user_code'];
                        $array_add_leaving_editor_user_name = $array_result_NextData['array_add_leaving_editor_user_name'];
                        $array_add_missing_middle_time_id = $array_result_NextData['array_add_missing_middle_time_id'];
                        $array_add_missing_middle_editor_department_code = $array_result_NextData['array_add_missing_middle_editor_department_code'];
                        $array_add_missing_middle_editor_department_name = $array_result_NextData['array_add_missing_middle_editor_department_name'];
                        $array_add_missing_middle_editor_user_code = $array_result_NextData['array_add_missing_middle_editor_user_code'];
                        $array_add_missing_middle_editor_user_name = $array_result_NextData['array_add_missing_middle_editor_user_name'];
                        $array_add_missing_return_time_id = $array_result_NextData['array_add_missing_return_time_id'];
                        $array_add_missing_return_editor_department_code = $array_result_NextData['array_add_missing_return_editor_department_code'];
                        $array_add_missing_return_editor_department_name = $array_result_NextData['array_add_missing_return_editor_department_name'];
                        $array_add_missing_return_editor_user_code = $array_result_NextData['array_add_missing_return_editor_user_code'];
                        $array_add_missing_return_editor_user_name = $array_result_NextData['array_add_missing_return_editor_user_name'];
                        $array_add_public_going_out_time_id = $array_result_NextData['array_add_public_going_out_time_id'];
                        $array_add_public_going_out_editor_department_code = $array_result_NextData['array_add_public_going_out_editor_department_code'];
                        $array_add_public_going_out_editor_department_name = $array_result_NextData['array_add_public_going_out_editor_department_name'];
                        $array_add_public_going_out_editor_user_code = $array_result_NextData['array_add_public_going_out_editor_user_code'];
                        $array_add_public_going_out_editor_user_name = $array_result_NextData['array_add_public_going_out_editor_user_name'];
                        $array_add_public_return_time_id = $array_result_NextData['array_add_public_return_time_id'];
                        $array_add_public_return_editor_department_code = $array_result_NextData['array_add_public_return_editor_department_code'];
                        $array_add_public_return_editor_department_name = $array_result_NextData['array_add_public_return_editor_department_name'];
                        $array_add_public_return_editor_user_code = $array_result_NextData['array_add_public_return_editor_user_code'];
                        $array_add_public_return_editor_user_name = $array_result_NextData['array_add_public_return_editor_user_name'];
                        $array_add_emergency_time_id = $array_result_NextData['array_add_emergency_time_id'];
                        $array_add_emergency_editor_department_code = $array_result_NextData['array_add_emergency_editor_department_code'];
                        $array_add_emergency_editor_department_name = $array_result_NextData['array_add_emergency_editor_department_name'];
                        $array_add_emergency_editor_user_code = $array_result_NextData['array_add_emergency_editor_user_code'];
                        $array_add_emergency_editor_user_name = $array_result_NextData['array_add_emergency_editor_user_name'];
                        $array_add_emergency_return_time_id = $array_result_NextData['array_add_emergency_return_time_id'];
                        $array_add_emergency_return_editor_department_code = $array_result_NextData['array_add_emergency_return_editor_department_code'];
                        $array_add_emergency_return_editor_department_name = $array_result_NextData['array_add_emergency_return_editor_department_name'];
                        $array_add_emergency_return_editor_user_code = $array_result_NextData['array_add_emergency_return_editor_user_code'];
                        $array_add_emergency_return_editor_user_name = $array_result_NextData['array_add_emergency_return_editor_user_name'];
                        $attendance_time = $array_result_NextData['attendance_time'];
                        $leaving_time = $array_result_NextData['leaving_time'];
                        $missing_middle_time = $array_result_NextData['missing_middle_time'];
                        $missing_middle_return_time = $array_result_NextData['missing_middle_return_time'];
                        $public_going_out_time = $array_result_NextData['public_going_out_time'];
                        $public_going_out_return_time = $array_result_NextData['public_going_out_return_time'];
                        $emergency_time = $array_result_NextData['emergency_time'];
                        $emergency_return_time = $array_result_NextData['emergency_return_time'];
                        $attendance_time_positions = $array_result_NextData['attendance_time_positions'];
                        $leaving_time_positions = $array_result_NextData['leaving_time_positions'];
                        $missing_middle_time_positions = $array_result_NextData['missing_middle_time_positions'];
                        $missing_return_time_positions = $array_result_NextData['missing_return_time_positions'];
                        $public_going_out_time_positions = $array_result_NextData['public_going_out_time_positions'];
                        $public_return_time_positions = $array_result_NextData['public_return_time_positions'];
                        $emergency_time_positions = $array_result_NextData['emergency_time_positions'];
                        $emergency_return_time_positions = $array_result_NextData['emergency_return_time_positions'];
                        $attendance_time_id = $array_result_NextData['attendance_time_id'];
                        $leaving_time_id = $array_result_NextData['leaving_time_id'];
                        $missing_middle_time_id = $array_result_NextData['missing_middle_time_id'];
                        $missing_middle_return_time_id = $array_result_NextData['missing_middle_return_time_id'];
                        $public_going_out_time_id = $array_result_NextData['public_going_out_time_id'];
                        $public_going_out_return_time_id = $array_result_NextData['public_going_out_return_time_id'];
                        $emergency_time_id = $array_result_NextData['emergency_time_id'];
                        $emergency_return_time_id = $array_result_NextData['emergency_return_time_id'];
                        $attendance_editor_department_code = $array_result_NextData['attendance_editor_department_code'];
                        $attendance_editor_department_name = $array_result_NextData['attendance_editor_department_name'];
                        $attendance_editor_user_code = $array_result_NextData['attendance_editor_user_code'];
                        $attendance_editor_user_name = $array_result_NextData['attendance_editor_user_name'];
                        $leaving_editor_department_code = $array_result_NextData['leaving_editor_department_code'];
                        $leaving_editor_department_name = $array_result_NextData['leaving_editor_department_name'];
                        $leaving_editor_user_code = $array_result_NextData['leaving_editor_user_code'];
                        $leaving_editor_user_name = $array_result_NextData['leaving_editor_user_name'];
                        $missing_middle_editor_department_code = $array_result_NextData['missing_middle_editor_department_code'];
                        $missing_middle_editor_department_name = $array_result_NextData['missing_middle_editor_department_name'];
                        $missing_middle_editor_user_code = $array_result_NextData['missing_middle_editor_user_code'];
                        $missing_middle_editor_user_name = $array_result_NextData['missing_middle_editor_user_name'];
                        $missing_return_editor_department_code = $array_result_NextData['missing_return_editor_department_code'];
                        $missing_return_editor_department_name = $array_result_NextData['missing_return_editor_department_name'];
                        $missing_return_editor_user_code = $array_result_NextData['missing_return_editor_user_code'];
                        $missing_return_editor_user_name = $array_result_NextData['missing_return_editor_user_name'];
                        $public_going_out_editor_department_code = $array_result_NextData['public_going_out_editor_department_code'];
                        $public_going_out_editor_department_name = $array_result_NextData['public_going_out_editor_department_name'];
                        $public_going_out_editor_user_code = $array_result_NextData['public_going_out_editor_user_code'];
                        $public_going_out_editor_user_name = $array_result_NextData['public_going_out_editor_user_name'];
                        $public_return_editor_department_code = $array_result_NextData['public_return_editor_department_code'];
                        $public_return_editor_department_name = $array_result_NextData['public_return_editor_department_name'];
                        $public_return_editor_user_code = $array_result_NextData['public_return_editor_user_code'];
                        $public_return_editor_user_name = $array_result_NextData['public_return_editor_user_name'];
                        $emergency_editor_department_code = $array_result_NextData['emergency_editor_department_code'];
                        $emergency_editor_department_name = $array_result_NextData['emergency_editor_department_name'];
                        $emergency_editor_user_code = $array_result_NextData['emergency_editor_user_code'];
                        $emergency_editor_user_name = $array_result_NextData['emergency_editor_user_name'];
                        $emergency_return_editor_department_code = $array_result_NextData['emergency_return_editor_department_code'];
                        $emergency_return_editor_department_name = $array_result_NextData['emergency_return_editor_department_name'];
                        $emergency_return_editor_user_code = $array_result_NextData['emergency_return_editor_user_code'];
                        $emergency_return_editor_user_name = $array_result_NextData['emergency_return_editor_user_name'];
                        // 同じ値にする
                        $before_date = $current_date;
                        $before_department_code = $current_department_code;
                        $before_user_code = $current_user_code; 
                        $before_result = $result;
                        $this->not_employment_working = 0;
                        $working_status = 0;
                        $note = '';
                        $late = '';
                        $leave_early = '';
                        $to_be_confirmed = '';
                        $calc_nobreak_cnt = 0;
                        $array_notelateetc = $this->setNoteLateEtc($result);
                        $note .= $array_notelateetc[0];
                        $late = $array_notelateetc[1];
                        $leave_early = $array_notelateetc[2];
                        $to_be_confirmed = $array_notelateetc[3];
                        $calc_nobreak_cnt++;
                        // ユーザー休暇区分判定用
                        $before_holiday_set = true;
                        $before_holiday_date = $current_date;
                        $before_holiday_user_code = $current_user_code;
                        $before_holiday_department_code = $current_department_code;
                        $before_holiday_kubun = $result->holiday_kubun;
                    }
                }
            }
        }

        if ($calc_nobreak_cnt > 0 && $current_result->current_calc != 0) {
            if (count($array_add_attendance_time) > 0 ||
                count($array_add_leaving_time) > 0 ||
                count($array_add_missing_middle_time) > 0 ||
                count($array_add_missing_return_time) > 0 ||
                count($array_add_public_going_out_time) > 0 ||
                count($array_add_public_return_time) > 0 ||
                count($array_add_emergency_time) > 0 ||
                count($array_add_emergency_return_time) > 0) {
                try{
                    Log::debug('--- '.$current_result->user_name.' 終了  ------ '.$current_date.' モード  ------ '.$current_result->mode.' ---- $calc_nobreak_cnt = '.$calc_nobreak_cnt );
                    Log::debug('        残り　当日分　勤務状態 =  '.$working_status);
                    Log::debug('            temp_working_time_datesデータ作成開始 ');
                    Log::debug('                現ユーザーを登録 '.$current_user_code);
                    // setLeavingCollectPtn implement
                    $array_impl_addTempWorkingTimeDate = array (
                        'target_date' => $current_date,
                        'target_user_code' => $current_user_code,
                        'target_department_code' => $current_department_code,
                        'target_result' => $current_result,
                        'note' => $note,
                        'working_status' => $working_status,
                        'timetables' => $timetables,
                        'array_attendance_calc_time' => $array_attendance_calc_time,
                        'array_missing_middle_time' => $array_missing_middle_time,
                        'array_public_going_out_time' => $array_public_going_out_time,
                        'array_emergency_time' => $array_emergency_time,
                        'array_add_attendance_time' => $array_add_attendance_time,
                        'array_add_attendance_time_id' => $array_add_attendance_time_id,
                        'array_add_attendance_editor_department_code' => $array_add_attendance_editor_department_code,
                        'array_add_attendance_editor_department_name' => $array_add_attendance_editor_department_name,
                        'array_add_attendance_editor_user_code' => $array_add_attendance_editor_user_code,
                        'array_add_attendance_editor_user_name' => $array_add_attendance_editor_user_name,
                        'array_add_attendance_time_positions' => $array_add_attendance_time_positions,
                        'array_add_leaving_time' => $array_add_leaving_time,
                        'array_add_leaving_time_id' => $array_add_leaving_time_id,
                        'array_add_leaving_editor_department_code' => $array_add_leaving_editor_department_code,
                        'array_add_leaving_editor_department_name' => $array_add_leaving_editor_department_name,
                        'array_add_leaving_editor_user_code' => $array_add_leaving_editor_user_code,
                        'array_add_leaving_editor_user_name' => $array_add_leaving_editor_user_name,
                        'array_add_leaving_time_positions' => $array_add_leaving_time_positions,
                        'array_add_missing_middle_time' => $array_add_missing_middle_time,
                        'array_add_missing_middle_time_id' => $array_add_missing_middle_time_id,
                        'array_add_missing_middle_editor_department_code' => $array_add_missing_middle_editor_department_code,
                        'array_add_missing_middle_editor_department_name' => $array_add_missing_middle_editor_department_name,
                        'array_add_missing_middle_editor_user_code' => $array_add_missing_middle_editor_user_code,
                        'array_add_missing_middle_editor_user_name' => $array_add_missing_middle_editor_user_name,
                        'array_add_missing_middle_time_positions' => $array_add_missing_middle_time_positions,
                        'array_add_missing_return_time' => $array_add_missing_return_time,
                        'array_add_missing_return_time_id' => $array_add_missing_return_time_id,
                        'array_add_missing_return_editor_department_code' => $array_add_missing_return_editor_department_code,
                        'array_add_missing_return_editor_department_name' => $array_add_missing_return_editor_department_name,
                        'array_add_missing_return_editor_user_code' => $array_add_missing_return_editor_user_code,
                        'array_add_missing_return_editor_user_name' => $array_add_missing_return_editor_user_name,
                        'array_add_missing_return_time_positions' => $array_add_missing_return_time_positions,
                        'array_add_public_going_out_time' => $array_add_public_going_out_time,
                        'array_add_public_going_out_time_id' => $array_add_public_going_out_time_id,
                        'array_add_public_going_out_editor_department_code' => $array_add_public_going_out_editor_department_code,
                        'array_add_public_going_out_editor_department_name' => $array_add_public_going_out_editor_department_name,
                        'array_add_public_going_out_editor_user_code' => $array_add_public_going_out_editor_user_code,
                        'array_add_public_going_out_editor_user_name' => $array_add_public_going_out_editor_user_name,
                        'array_add_public_going_out_time_positions' => $array_add_public_going_out_time_positions,
                        'array_add_public_return_time' => $array_add_public_return_time,
                        'array_add_public_return_time_id' => $array_add_public_return_time_id,
                        'array_add_public_return_editor_department_code' => $array_add_public_return_editor_department_code,
                        'array_add_public_return_editor_department_name' => $array_add_public_return_editor_department_name,
                        'array_add_public_return_editor_user_code' => $array_add_public_return_editor_user_code,
                        'array_add_public_return_editor_user_name' => $array_add_public_return_editor_user_name,
                        'array_add_public_return_time_positions' => $array_add_public_return_time_positions,
                        'array_break_worktimetable_result' => $array_break_worktimetable_result,
                        'array_add_emergency_time' => $array_add_emergency_time,
                        'array_add_emergency_time_id' => $array_add_emergency_time_id,
                        'array_add_emergency_editor_department_code' => $array_add_emergency_editor_department_code,
                        'array_add_emergency_editor_department_name' => $array_add_emergency_editor_department_name,
                        'array_add_emergency_editor_user_code' => $array_add_emergency_editor_user_code,
                        'array_add_emergency_editor_user_name' => $array_add_emergency_editor_user_name,
                        'array_add_emergency_time_positions' => $array_add_emergency_time_positions,
                        'array_add_emergency_return_time' => $array_add_emergency_return_time,
                        'array_add_emergency_return_time_id' => $array_add_emergency_return_time_id,
                        'array_add_emergency_return_editor_department_code' => $array_add_emergency_return_editor_department_code,
                        'array_add_emergency_return_editor_department_name' => $array_add_emergency_return_editor_department_name,
                        'array_add_emergency_return_editor_user_code' => $array_add_emergency_return_editor_user_code,
                        'array_add_emergency_return_editor_user_name' => $array_add_emergency_return_editor_user_name,
                        'array_add_emergency_return_time_positions' => $array_add_emergency_return_time_positions,
                        'array_get_timetable_result' => $array_get_timetable_result
                    );
                    $add_result = $this->addTempWorkingTimeDate($array_impl_addTempWorkingTimeDate);
                }catch(\PDOException $pe){
                    $add_result = false;
                    throw $pe;
                }catch(\Exception $e){
                    Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.data_error_dailycalc'));
                    Log::error($e->getMessage());
                    $add_result = false;
                    throw $e;
                }
                Log::debug('        temp_working_time_datesデータ作成終了 ');
            }
        }

        Log::debug('---------------------- calcTempWorkingTimeDate end ------------------------ ');
        return $add_result;

    }
    

    /**
     * 労働時間計算
     *
     *
     * @param  $inc              ： 1:出勤・退勤 2:私用外出・私用外出戻り 3:公用外出・公用外出戻り 4:緊急収集
     * @param  $target_from_time ： 開始時刻（出勤・私用外出）
     * @param  $target_to_time   ： 終了時刻（退勤・私用外出戻り）
     * @return 計算結果時間
     */
    private function calcTimes($params)
    {
        Log::debug('---------------------- calcTimes ------------------------ ');
        $inc = $params['inc'];
        $timetables = $params['timetables'];
        $working_timetable_no = $params['working_timetable_no'];
        $isemergency_time = $params['isemergency_time'];
        $working_time_kubun = $params['working_time_kubun'];
        $current_date = $params['current_date'];
        $target_from_time = $params['roundTimestart_time'];
        $target_to_time = $params['roundTimeend_time'];
        $array_attendance_calc_time = $params['array_attendance_calc_time'];
        $array_gouing_out_time = $params['array_missing_middle_time'];
        $apicommon = new ApiCommonController();
        $working_times = 0;             // 労働時間
        $calc_times = 0;
        $calc_times_regular = 0;

        // 労働時間区分の開始終了時刻を取得
        // タイムテーブルをnoとworking_time_kubunで特定
        if ($working_time_kubun != 99) {
            $array_times = $apicommon->analyzeTimeTable($timetables, $working_time_kubun, $working_timetable_no);
        } else {
            $array_times = $apicommon->analyzeTimeTable($timetables, 1, $working_timetable_no);
        }

        foreach($array_times as $result_time) {
            // 時間登録の開始時間
            $from_time = $result_time['from_time'];
            // 時間登録の終了時間
            $to_time = $result_time['to_time'];
            Log::debug(' ◆◆◆◆◆　労働時間計算　 ◆◆◆◆◆◆');
            Log::debug('            対象日付 current_date = '.$current_date);
            Log::debug('            打刻開始 target_from_time = '.$target_from_time);
            Log::debug('            打刻終了 from_time = '.$target_to_time);
            Log::debug('            タイムテーブル no = '.$working_timetable_no);
            Log::debug('            タイムテーブル from_time = '.$from_time);
            Log::debug('            タイムテーブル to_time = '.$to_time);
            Log::debug('            勤務区分 working_time_kubun = '.$working_time_kubun);
            // 緊急時は打刻＝タイムテーブル時刻（深夜時間はタイムテーブル時刻）
            if ($isemergency_time &&
                $working_time_kubun != Config::get('const.C004.out_of_regular_night_working_time')) {
                $from_time = date_format(new Carbon($target_from_time), 'H:i:s');
                $to_time = date_format(new Carbon($target_to_time), 'H:i:s');
            }
            if (isset($from_time) && isset($to_time)) {
                // from_time日付付与
                $working_time_from_time = $apicommon->convTimeToDateFrom($from_time, $current_date, $target_from_time, $target_to_time);         
                $working_time_calc_from = $working_time_from_time;
                // to_time日付付与
                $working_time_to_time = $apicommon->convTimeToDateTo($from_time, $to_time, $current_date, $target_from_time, $target_to_time);         
                $working_time_calc_to = $working_time_to_time;
                // ------------------ DEBUG strat ----------------------------------------
                Log::debug('            working_time_from_time = '.$working_time_from_time);
                Log::debug('　　　　　　 出勤時刻または外出  target_from_time = '.$target_from_time);
                Log::debug('            退勤時刻または戻り  target_to_time = '.$target_to_time);
                Log::debug('            設定開始時刻  working_time_calc_from = '.$working_time_calc_from);
                Log::debug('            設定終了時刻  working_time_calc_to = '.$working_time_calc_to);
                Log::debug('            労働区分  working_time_kubun = '.$working_time_kubun);
                Log::debug('            inc = '.$inc);
                // ------------------ DEBUG end ----------------------------------------
                // 深夜労働残業時間以外の場合
                if ($working_time_kubun != Config::get('const.C004.out_of_regular_night_working_time') ||
                    $inc == Config::get('const.INC_NO.public_going_out_return') ||
                    $inc == Config::get('const.INC_NO.public_return')) {
                    if ($working_time_kubun == 99) {
                        $working_time_calc_from = $target_from_time;
                        $working_time_calc_to = $target_to_time;
                    }
                    // 打刻時刻$targetが所定時間内$working_timeの場合
                    $isbetweenTime = $apicommon->chkBetweenTime($target_from_time, $target_to_time, $working_time_calc_from, $working_time_calc_to);
                    Log::debug('            打刻時刻$targetが所定時間内  isbetweenTime = '.$isbetweenTime);
                    if ($isbetweenTime) {
                        if ($working_time_calc_from < $working_time_calc_to) {
                            if ($target_from_time > $working_time_calc_from) {
                                $working_time_calc_from = $target_from_time;
                            }
                            if ($target_to_time < $working_time_calc_to) {
                                $working_time_calc_to = $target_to_time;
                            }
                            $calc_times = $apicommon->diffTimeSerial($working_time_calc_from, $working_time_calc_to);
                            $working_times += $calc_times;
                            // ------------------ DEBUG strat ----------------------------------------
                            Log::debug('          　深夜労働残業時間以外の場合');
                            Log::debug(' 　　　　　　打刻時刻が所定時間内の場合 ');
                            Log::debug('　　　　　　 計算開始時刻  working_time_calc_from = '.$working_time_calc_from);
                            Log::debug('　　　　　　 計算終了時刻  working_time_calc_to = '.$working_time_calc_to);
                            Log::debug('            労働時間      calc_times = '.$calc_times."  ".$calc_times / 60 / 60);
                            Log::debug('            累計労働時間  working_times = '.$working_times."  ".$working_times / 60 / 60);
                            // ------------------ DEBUG end ----------------------------------------
                        }
                    }
                    // 夜勤の場合は打刻target_from_time、target_to_timeが翌日の場合があるため
                    // working_time_calc_from、working_time_calc_toを翌日にして計算する
                    $working_time_calc_from_nextday = $apicommon->getNextDay($working_time_from_time, 'Y-m-d H:i:s');
                    $working_time_calc_to_nextday = $apicommon->getNextDay($working_time_to_time, 'Y-m-d H:i:s');
                    if ($apicommon->chkBetweenTime($target_from_time, $target_to_time, $working_time_calc_from_nextday, $working_time_calc_to_nextday)) {
                        // ------------------ DEBUG strat ----------------------------------------
                        Log::debug('          　夜勤の場合の翌日労働時間計算');
                        Log::debug('          　打刻開始時刻  target_from_time = '.$target_from_time);
                        Log::debug('          　打刻計算終了時刻  target_to_time = '.$target_to_time);
                        Log::debug('          　当日計算開始時刻  working_time_calc_from = '.$working_time_calc_from);
                        Log::debug('          　当日計算終了時刻  working_time_calc_to = '.$working_time_calc_to);
                        Log::debug('          　翌日計算開始時刻  working_time_calc_from_nextday = '.$working_time_calc_from_nextday);
                        Log::debug('          　翌日計算終了時刻  working_time_calc_to_nextday = '.$working_time_calc_to_nextday);
                        // ------------------ DEBUG end ----------------------------------------
                        if ($working_time_calc_from_nextday < $working_time_calc_to_nextday) {
                            /*if ($target_from_time > $working_time_calc_from) {
                                $working_time_calc_from_nextday = $target_from_time;
                            } */
                            if ($target_from_time > $working_time_calc_from_nextday) {
                                $working_time_calc_from_nextday = $target_from_time;
                            }
                            if ($target_to_time < $working_time_calc_to_nextday) {
                                $working_time_calc_to_nextday = $target_to_time;
                            }
                            $calc_times = $apicommon->diffTimeSerial($working_time_calc_from_nextday, $working_time_calc_to_nextday);
                            $working_times += $calc_times;
                            // ------------------ DEBUG strat ----------------------------------------
                            Log::debug('          　翌日計算開始時刻 調整 working_time_calc_from_nextday = '.$working_time_calc_from_nextday);
                            Log::debug('          　翌日計算終了時刻 調整 working_time_calc_to_nextday = '.$working_time_calc_to_nextday);
                            Log::debug('            労働時間      calc_times = '.$calc_times."  ".$calc_times / 60 / 60);
                            Log::debug('            累計労働時間  working_times = '.$working_times."  ".$working_times / 60 / 60);
                            // ------------------ DEBUG end ----------------------------------------
                        }
                    }
                    if ($working_times != 0) {
                        // 打刻時間内に休憩時間を含んでいる場合、休憩時間累計を求めて減算する
                        //  計算対象のタイムテーブルの開始終了日時の範囲内に休憩開始終了時刻がある場合で
                        $braek_time = 0;
                        $braek_time = $apicommon->calcBetweenBreakTime(
                            $target_from_time,
                            $target_to_time,
                            $current_date,
                            $timetables,
                            $working_timetable_no,
                            $working_time_calc_from,
                            $working_time_calc_to);
                        Log::debug('            休憩時間累計      braek_time = '.$braek_time."  ".$braek_time / 60 / 60);
                        $working_times -= $braek_time;
                    }
                } else {
                    // 深夜労働残業時間
                    // ------------------ DEBUG strat ----------------------------------------
                    Log::debug('           【深夜労働残業時間 計算開始】');
                    // ------------------ DEBUG end ----------------------------------------
                    // 退勤時刻 > 深夜労働時間開始時刻
                    if ($target_to_time > $working_time_calc_from) {
                        // 深夜残業時間集計
                        // 出勤時刻から休憩時間を含めた基準時間（8時間後）を求める
                        $after_legal_working_hours_day = Config::get('const.C002.legal_working_hours_day') * 60 * 60;
                        $after_target_from_time = $target_from_time;
                        $after_daytime = "";            // 実働8時間後時刻
                        // 休憩時間がなくなるまで実働8時間後を求める
                        while(true){
                            $after_daytime = $apicommon->getAfterDayTime(
                                $after_target_from_time,
                                $after_legal_working_hours_day,
                                'Y-m-d H:i:s');
                            $braek_time = $apicommon->calcBetweenBreakTime(
                                $after_target_from_time,
                                $after_daytime,
                                $current_date,
                                $timetables,
                                $working_timetable_no,
                                null, null);
                            if ($braek_time == 0) {
                                break;
                            }
                            Log::debug('　　　　　　 休憩時間  = '.$braek_time);
                            $after_target_from_time = $after_daytime;
                            // $braek_timeは秒数なので
                            $after_legal_working_hours_day = $braek_time;
                        }
                        Log::debug('　　　　　　 出勤時刻から休憩時間を含めた基準時間（8時間後）を求める ');
                        Log::debug('　　　　　　 実働8時間後時刻  = '.$after_daytime);
                        // 実働8時間後時刻 < 深夜労働時間開始時刻
                        if ($after_daytime < $working_time_calc_from) {
                            // 退勤時刻 > 深夜労働時間開始時刻
                            if ($target_to_time > $working_time_calc_from) {
                                // 退勤時刻 < 深夜労働時間終了時刻
                                if ($target_to_time < $working_time_calc_to) {
                                    // 深夜労働時間開始時刻から退勤時刻を深夜残業時間とする
                                    $calc_times = $apicommon->diffTimeSerial($working_time_calc_from, $target_to_time);
                                    Log::debug('　　　　　　深夜労働時間開始時刻から退勤時刻を深夜残業時間とする = '.$calc_times);
                                } else {
                                    // 深夜労働時間開始時刻から深夜労働時間終了時刻を深夜残業時間とする
                                    $calc_times = $apicommon->diffTimeSerial($working_time_calc_from, $working_time_calc_to);
                                    Log::debug('　　　　　　深夜労働時間開始時刻から深夜労働時間終了時刻を深夜残業時間とする = '.$calc_times);
                                }
                            }
                        } else {
                            // 実働8時間後時刻 < 深夜労働時間終了時刻
                            if ($after_daytime < $working_time_calc_to) {
                                // 退勤時刻 > 実働8時間後時刻
                                if ($target_to_time > $after_daytime) {
                                    // 退勤時刻 < 深夜労働時間終了時刻
                                    if ($target_to_time < $working_time_calc_to) {
                                        // 実働8時間後時刻から退勤時刻を深夜残業時間とする
                                        $calc_times = $apicommon->diffTimeSerial($after_daytime, $target_to_time);
                                        Log::debug('　　　　　　実働8時間後時刻から退勤時刻を深夜残業時間とする = '.$calc_times);
                                    } else {
                                        // 深夜労働時間開始時刻から深夜労働時間終了時刻を深夜残業時間とする
                                        $calc_times = $apicommon->diffTimeSerial($after_daytime, $working_time_calc_to);
                                        Log::debug('　　　　　　実働8時間後時刻から深夜労働時間終了時刻を深夜残業時間とする = '.$calc_times);
                                    }
                                }
                            }
                        }
                        // 深夜労働時間集計
                        $w_time = 0;
                        // 退勤時刻 <= 深夜労働時間終了時刻
                        if ($target_to_time <= $working_time_calc_to) {
                            // 出勤時刻 <= 深夜労働時間開始時刻
                            if ($target_from_time <= $working_time_calc_from) {
                                // 深夜労働時間開始時刻から退勤時刻を深夜労働時間とする
                                $w_time = $apicommon->diffTimeSerial($working_time_calc_from, $target_to_time);
                                Log::debug('　　　　　　深夜労働時間開始時刻から退勤時刻を深夜労働時間とする = '.$w_time);
                            } else {
                                // 出勤時刻から退勤時刻を深夜労働時間とする
                                $w_time = $apicommon->diffTimeSerial($target_from_time, $target_to_time);
                                Log::debug('　　　　　　出勤時刻から退勤時刻を深夜労働時間とする = '.$w_time);
                            }
                        } else {
                            // 出勤時刻 <= 深夜労働時間開始時刻
                            if ($target_from_time <= $working_time_calc_from) {
                                // 深夜労働時間開始時刻から深夜労働時間終了時刻を深夜労働時間とする
                                $w_time = $apicommon->diffTimeSerial($working_time_calc_from, $working_time_calc_to);
                                Log::debug('　　　　　　深夜労働時間開始時刻から深夜労働時間終了時刻を深夜労働時間とする = '.$w_time);
                            } else {
                                // 出勤時刻から深夜労働時間終了時刻を深夜労働時間とする
                                $w_time = $apicommon->diffTimeSerial($target_from_time, $working_time_calc_to);
                                Log::debug('　　　　　　出勤時刻から深夜労働時間終了時刻を深夜労働時間とする = '.$w_time);
                            }
                        }
                        $this->calc_late_night_working_hours += ($w_time - $calc_times);
                        $working_times += $calc_times;
                        // ------------------ DEBUG strat ----------------------------------------
                        Log::debug('　　　　　　 深夜労働残業時間を計算 = '.$calc_times );
                        Log::debug('　　　　　　 深夜労働残業時間を累計計算 = '.$working_times );
                        Log::debug('　　　　　　 w_time = '.$w_time );
                        Log::debug('　　　　　　 深夜労働時間集計を計算 $w_time - $calc_times = '.($w_time - $calc_times));
                        Log::debug('　　　　　　 深夜労働時間集計を累計計算 = '.$this->calc_late_night_working_hours);
                        // ------------------ DEBUG end ----------------------------------------
                    }
                    if ($working_times != 0) {
                        // 打刻時間内に休憩時間を含んでいる場合、休憩時間累計を求めて減算する
                        //  計算対象のタイムテーブルの開始終了日時の範囲内に休憩開始終了時刻がある場合で
                        $braek_time = 0;
                        $braek_time = $apicommon->calcBetweenBreakTime(
                            $target_from_time,
                            $target_to_time,
                            $current_date,
                            $timetables,
                            $working_timetable_no,
                            $working_time_calc_from,
                            $working_time_calc_to);
                        Log::debug('            休憩時間累計      braek_time = '.$braek_time."  ".$braek_time / 60 / 60);
                        $working_times -= $braek_time;
                    }
                }
            }
        }
        // ------------------ DEBUG strat ----------------------------------------
        Log::debug('            累計労働時間  working_times = '.$working_times."  ".$working_times / 60 / 60);
        Log::debug('---------------------- calcTimes end ------------------------ ');
        // ------------------ DEBUG end ----------------------------------------

        return $working_times;
    }

    /**
     * 休憩時間合計計算
     *
     *
     * @param  $attendance_time ： 開始時刻（出勤）
     * @param  $leaving_time    ： 終了時刻（退勤）
     * @return 計算結果時間
     */
    private function calcBreakTimes($timetables, $working_timetable_no, $working_time_kubun, $current_date, $attendance_time, $leaving_time)
    {
        Log::debug('---------------------- calcBreakTimes in working_time_kubun = '.$working_time_kubun.' ------------------------ ');
        $apicommon = new ApiCommonController();
        $working_times = 0;             // 休憩時間合計時間

        // 労働時間区分の開始終了時刻を取得
        // タイムテーブルをnoとworking_time_kubunで特定
        $array_times = $apicommon->analyzeTimeTable($timetables, $working_time_kubun, $working_timetable_no);

        foreach($array_times as $result_time) {
            // 時間登録の開始時間
            $from_time = $result_time['from_time'];
            // 時間登録の終了時間
            $to_time = $result_time['to_time'];
            if (isset($from_time) && isset($to_time)) {
                // 日付付与
                // from_time日付付与
                $working_time_calc_from = 
                    $apicommon->convTimeToDateFrom($from_time, $current_date, $attendance_time, $leaving_time);         
                // to_time日付付与
                $working_time_calc_to = 
                    $apicommon->convTimeToDateTo($from_time, $to_time, $current_date, $attendance_time, $leaving_time);         
                Log::debug('from_time = '.$from_time);
                Log::debug('to_time = '.$to_time);
                Log::debug('current_date = '.$current_date);
                Log::debug('attendance_time = '.$attendance_time);
                Log::debug('leaving_time = '.$leaving_time);
                Log::debug('working_time_calc_from = '.$working_time_calc_from);
                Log::debug('working_time_calc_to = '.$working_time_calc_to);
                // 出退勤の範囲内であれば計算
                if (($working_time_calc_from > $attendance_time && $working_time_calc_from < $leaving_time) ||
                    ($working_time_calc_to > $attendance_time && $working_time_calc_to < $leaving_time)) {
                    if ($attendance_time > $working_time_calc_from) {
                        $working_time_calc_from = $attendance_time;
                        Log::debug('working_time_calc_from if then= '.$working_time_calc_from);
                    }
                    if ($leaving_time < $working_time_calc_to) {
                        $working_time_calc_to = $leaving_time;
                        Log::debug('working_time_calc_to if then= '.$working_time_calc_to);
                    }
                    Log::debug('working_time_kubun = '.$working_time_kubun);
                    Log::debug('attendance_time = '.$attendance_time);
                    Log::debug('leaving_time = '.$leaving_time);
                    Log::debug('working_time_calc_from = '.$working_time_calc_from);
                    Log::debug('working_time_calc_to = '.$working_time_calc_to);
                    if ($working_time_calc_from < $working_time_calc_to) {
                        $calc_times = $apicommon->diffTimeSerial($working_time_calc_from, $working_time_calc_to);
                        Log::debug('calc_times = '.$calc_times);
                        $working_times += $calc_times;
                        Log::debug('$working_times = '.$working_times);
                    }
                }
            }
        }
        Log::debug('calcBreakTimes end '.$working_times);
        Log::debug('---------------------- calcBreakTimes end '.$working_times.'------------------------ ');

        return $working_times;
    }

    /**
     * ユーザー労働時間登録（temp）
     *
     * @return 登録結果
     */
    private function addTempWorkingTimeDate($params)
    {
        Log::debug('---------------------- addTempWorkingTimeDate in ------------------------ ');
        try{
            // パラメータ設定
            $target_date = $params['target_date'];
            $target_user_code = $params['target_user_code'];
            $target_department_code = $params['target_department_code'];
            $target_result = $params['target_result'];
            $note = $params['note'];
            $working_status = $params['working_status'];
            $timetables = $params['timetables'];
            $array_attendance_calc_time = $params['array_attendance_calc_time'];
            $array_missing_middle_time = $params['array_missing_middle_time'];
            $array_public_going_out_time = $params['array_public_going_out_time'];
            $array_emergency_time = $params['array_emergency_time'];
            $array_add_attendance_time = $params['array_add_attendance_time'];
            $array_add_attendance_time_id = $params['array_add_attendance_time_id'];
            $array_add_attendance_editor_department_code = $params['array_add_attendance_editor_department_code'];
            $array_add_attendance_editor_department_name = $params['array_add_attendance_editor_department_name'];
            $array_add_attendance_editor_user_code = $params['array_add_attendance_editor_user_code'];
            $array_add_attendance_editor_user_name = $params['array_add_attendance_editor_user_name'];
            $array_add_attendance_time_positions = $params['array_add_attendance_time_positions'];
            $array_add_leaving_time = $params['array_add_leaving_time'];
            $array_add_leaving_time_id = $params['array_add_leaving_time_id'];
            $array_add_leaving_editor_department_code = $params['array_add_leaving_editor_department_code'];
            $array_add_leaving_editor_department_name = $params['array_add_leaving_editor_department_name'];
            $array_add_leaving_editor_user_code = $params['array_add_leaving_editor_user_code'];
            $array_add_leaving_editor_user_name = $params['array_add_leaving_editor_user_name'];
            $array_add_leaving_time_positions = $params['array_add_leaving_time_positions'];
            $array_add_missing_middle_time = $params['array_add_missing_middle_time'];
            $array_add_missing_middle_time_id = $params['array_add_missing_middle_time_id'];
            $array_add_missing_middle_editor_department_code = $params['array_add_missing_middle_editor_department_code'];
            $array_add_missing_middle_editor_department_name = $params['array_add_missing_middle_editor_department_name'];
            $array_add_missing_middle_editor_user_code = $params['array_add_missing_middle_editor_user_code'];
            $array_add_missing_middle_editor_user_name = $params['array_add_missing_middle_editor_user_name'];
            $array_add_missing_middle_time_positions = $params['array_add_missing_middle_time_positions'];
            $array_add_missing_return_time = $params['array_add_missing_return_time'];
            $array_add_missing_return_time_id = $params['array_add_missing_return_time_id'];
            $array_add_missing_return_editor_department_code = $params['array_add_missing_return_editor_department_code'];
            $array_add_missing_return_editor_department_name = $params['array_add_missing_return_editor_department_name'];
            $array_add_missing_return_editor_user_code = $params['array_add_missing_return_editor_user_code'];
            $array_add_missing_return_editor_user_name = $params['array_add_missing_return_editor_user_name'];
            $array_add_missing_return_time_positions = $params['array_add_missing_return_time_positions'];
            $array_add_public_going_out_time = $params['array_add_public_going_out_time'];
            $array_add_public_going_out_time_id = $params['array_add_public_going_out_time_id'];
            $array_add_public_going_out_editor_department_code = $params['array_add_public_going_out_editor_department_code'];
            $array_add_public_going_out_editor_department_name = $params['array_add_public_going_out_editor_department_name'];
            $array_add_public_going_out_editor_user_code = $params['array_add_public_going_out_editor_user_code'];
            $array_add_public_going_out_editor_user_name = $params['array_add_public_going_out_editor_user_name'];
            $array_add_public_going_out_time_positions = $params['array_add_public_going_out_time_positions'];
            $array_add_public_return_time = $params['array_add_public_return_time'];
            $array_add_public_return_time_id = $params['array_add_public_return_time_id'];
            $array_add_public_return_editor_department_code = $params['array_add_public_return_editor_department_code'];
            $array_add_public_return_editor_department_name = $params['array_add_public_return_editor_department_name'];
            $array_add_public_return_editor_user_code = $params['array_add_public_return_editor_user_code'];
            $array_add_public_return_editor_user_name = $params['array_add_public_return_editor_user_name'];
            $array_add_public_return_time_positions = $params['array_add_public_return_time_positions'];
            $array_add_emergency_time = $params['array_add_emergency_time'];
            $array_add_emergency_time_id = $params['array_add_emergency_time_id'];
            $array_add_emergency_editor_department_code = $params['array_add_emergency_editor_department_code'];
            $array_add_emergency_editor_department_name = $params['array_add_emergency_editor_department_name'];
            $array_add_emergency_editor_user_code = $params['array_add_emergency_editor_user_code'];
            $array_add_emergency_editor_user_name = $params['array_add_emergency_editor_user_name'];
            $array_add_emergency_time_positions = $params['array_add_emergency_time_positions'];
            $array_add_emergency_return_time = $params['array_add_emergency_return_time'];
            $array_add_emergency_return_time_id = $params['array_add_emergency_return_time_id'];
            $array_add_emergency_return_editor_department_code = $params['array_add_emergency_return_editor_department_code'];
            $array_add_emergency_return_editor_department_name = $params['array_add_emergency_return_editor_department_name'];
            $array_add_emergency_return_editor_user_code = $params['array_add_emergency_return_editor_user_code'];
            $array_add_emergency_return_editor_user_name = $params['array_add_emergency_return_editor_user_name'];
            $array_add_emergency_return_time_positions = $params['array_add_emergency_return_time_positions'];
            $array_break_worktimetable_result = $params['array_break_worktimetable_result'];
            $array_get_timetable_result = $params['array_get_timetable_result'];

            // 出退勤のデータがない場合は緊急データを出退勤にする
            Log::debug('出退勤 緊急 count($array_add_attendance_time = '.count($array_add_attendance_time));
            Log::debug('出退勤 緊急 count($array_add_emergency_time = '.count($array_add_emergency_time));
            if (count($array_add_attendance_time) == 0 && count($array_add_emergency_time) > 0) {
                $array_attendance_calc_time = $array_emergency_time;
                Log::debug('出退勤 緊急 count($array_emergency_time = '.count($array_emergency_time));
                Log::debug('出退勤 緊急 count($array_attendance_calc_time = '.count($array_attendance_calc_time));
                $array_add_attendance_time = $array_add_emergency_time;
                $array_add_attendance_time_id = $array_add_emergency_time_id;
                $array_add_attendance_editor_department_code = $array_add_emergency_editor_department_code;
                $array_add_attendance_editor_department_name = $array_add_emergency_editor_department_name;
                $array_add_attendance_editor_user_code = $array_add_emergency_editor_user_code;
                $array_add_attendance_editor_user_name = $array_add_emergency_editor_user_name;
                $array_add_attendance_time_positions = $array_add_emergency_time_positions;
                $array_add_leaving_time = $array_add_emergency_return_time;
                $array_add_leaving_time_id = $array_add_emergency_return_time_id;
                $array_add_leaving_editor_department_code = $array_add_emergency_return_editor_department_code;
                $array_add_leaving_editor_department_name = $array_add_emergency_return_editor_department_name;
                $array_add_leaving_editor_user_code = $array_add_emergency_return_editor_user_code;
                $array_add_leaving_editor_user_name = $array_add_emergency_return_editor_user_name;
                $array_add_leaving_time_positions = $array_add_emergency_return_time_positions;
            }
            // 休暇計算用出勤時刻
            $break_attendance_time = null;
            // 休暇計算用退勤時刻
            $break_leaving_time = null;
            $apicommon = new ApiCommonController();
            $temp_working_model = new TempWorkingTimeDate();
            $temp_working_model->setWorkingdateAttribute(date_format(new Carbon($target_date), 'Ymd'));
            $temp_working_model->setEmploymentstatusAttribute($target_result->employment_status);
            $temp_working_model->setDepartmentcodeAttribute($target_department_code);
            $temp_working_model->setUsercodeAttribute($target_user_code);
            $temp_working_model->setSeqAttribute(1);
            $temp_working_model->setEmploymentstatusnameAttribute($target_result->employment_status_name);
            $temp_working_model->setDepartmentnameAttribute($target_result->department_name);
            $temp_working_model->setUsernameAttribute($target_result->user_name);
            $temp_working_model->setWorkingtimetablenoAttribute($target_result->working_timetable_no);
            $temp_working_model->setWorkingtimetablenameAttribute($target_result->working_timetable_name);
            // 出勤打刻５回までチェック
            $attendence_note_set = false;
            if (!$this->chkWorkingTime($array_add_attendance_time, (int)(Config::get('const.ARRAY_MAX_INDEX.attendace_time')) )) {
                $note .= Config::get('const.MEMO_DATA.MEMO_DATA_016').' '; 
                $attendence_note_set = true;
            }
            // 設定
            Log::debug('  count($array_add_attendance_time) = '.count($array_add_attendance_time));
            $array_decide_times = $this->decideWorkingTimeFrom($array_add_attendance_time, count($array_add_attendance_time));
            Log::debug('  count($array_decide_times) = '.count($array_decide_times));
            for ($i=0;$i<count($array_decide_times);$i++) {
                if ($i<count($array_decide_times)) {
                    if ($break_attendance_time == null) {$break_attendance_time = $array_decide_times[$i]; }
                    Log::debug('  $i<count($array_decide_times $array_decide_times[$i] = '.$array_decide_times[$i]);
                    $temp_working_model->setAttendancetimeAttribute($i, $array_decide_times[$i]);
                    $temp_working_model->setAttendancetimeidAttribute($i, $array_add_attendance_time_id[$i]);
                    $temp_working_model->setAttendanceeditordepartmentcodeAttribute($i, $array_add_attendance_editor_department_code[$i]);
                    $temp_working_model->setAttendanceeditordepartmentnameAttribute($i, $array_add_attendance_editor_department_name[$i]);
                    $temp_working_model->setAttendanceeditorusercodeAttribute($i, $array_add_attendance_editor_user_code[$i]);
                    $temp_working_model->setAttendanceeditorusernameAttribute($i, $array_add_attendance_editor_user_name[$i]);
                    if (isset($array_add_attendance_time_positions[$i])) {
                        $temp_working_model->setAttendancetimepositionsAttribute($i, $array_add_attendance_time_positions[$i]);
                    } else {
                        $temp_working_model->setAttendancetimepositionsAttribute($i, null);
                    }
                } else {
                    Log::debug('  else $array_decide_times[$i] = '.$array_decide_times[$i]);
                    $temp_working_model->setAttendancetimeAttribute($i, null);
                    $temp_working_model->setAttendancetimeidAttribute($i, null);
                    $temp_working_model->setAttendanceeditordepartmentcodeAttribute($i, null);
                    $temp_working_model->setAttendanceeditordepartmentnameAttribute($i, null);
                    $temp_working_model->setAttendanceeditorusercodeAttribute($i, null);
                    $temp_working_model->setAttendanceeditorusernameAttribute($i, null);
                    $temp_working_model->setAttendancetimepositionsAttribute($i, null);
                }
            }
            // 退勤打刻５回までチェック 出勤でチェックエラー時はnote設定しない1
            if (!$this->chkWorkingTime($array_add_leaving_time,(int)(Config::get('const.ARRAY_MAX_INDEX.leaving_time')) )) {
                if ($attendence_note_set == false) {
                    $note .= Config::get('const.MEMO_DATA.MEMO_DATA_016').' '; 
                }
            }
            // 設定
            $array_decide_times = $this->decideWorkingTimeTo(
                $array_add_leaving_time,
                count($array_add_leaving_time),
                $array_add_attendance_time,
                count($array_add_attendance_time));
            for ($i=0;$i<count($array_decide_times);$i++) {
                if ($i<count($array_decide_times)) {
                    if ($break_leaving_time == null) {$break_leaving_time = $array_decide_times[$i]; }
                    $temp_working_model->setLeavingtimeAttribute($i, $array_decide_times[$i]);
                    $temp_working_model->setLeavingtimeidAttribute($i, $array_add_leaving_time_id[$i]);
                    $temp_working_model->setLeavingeditordepartmentcodeAttribute($i, $array_add_leaving_editor_department_code[$i]);
                    $temp_working_model->setLeavingeditordepartmentnameAttribute($i, $array_add_leaving_editor_department_name[$i]);
                    $temp_working_model->setLeavingeditorusercodeAttribute($i, $array_add_leaving_editor_user_code[$i]);
                    $temp_working_model->setLeavingeditorusernameAttribute($i, $array_add_leaving_editor_user_name[$i]);
                    if (isset($array_add_leaving_time_positions[$i])) {
                        $temp_working_model->setLeavingtimepositionsAttribute($i, $array_add_leaving_time_positions[$i]);
                    } else {
                        $temp_working_model->setLeavingtimepositionsAttribute($i, null);
                    }
                } else {
                    $temp_working_model->setLeavingtimeAttribute($i, null);
                    $temp_working_model->setLeavingtimeidAttribute($i, null);
                    $temp_working_model->setLeavingeditordepartmentcodeAttribute($i, null);
                    $temp_working_model->setLeavingeditordepartmentnameAttribute($i, null);
                    $temp_working_model->setLeavingeditorusercodeAttribute($i, null);
                    $temp_working_model->setLeavingeditorusernameAttribute($i, null);
                    $temp_working_model->setLeavingtimepositionsAttribute($i, null);
                }
            }
            // 中抜け打刻５回までチェック
            $missing_middle_note_set = false;
            if (!$this->chkWorkingTime($array_add_missing_middle_time,(int)(Config::get('const.ARRAY_MAX_INDEX.missing_middle_time')) )) {
                $note .= Config::get('const.MEMO_DATA.MEMO_DATA_018').' '; 
                $missing_middle_note_set = true;
            }
            // 設定
            $array_decide_times = $this->decideWorkingTimeFrom($array_add_missing_middle_time, count($array_add_missing_middle_time));
            for ($i=0;$i<count($array_decide_times);$i++) {
                if ($i<count($array_decide_times)) {
                    $temp_working_model->setMissingmiddletimeAttribute($i, $array_decide_times[$i]);
                    $temp_working_model->setMissingmiddletimeidAttribute($i, $array_add_missing_middle_time_id[$i]);
                    $temp_working_model->setMissingeditordepartmentcodeAttribute($i, $array_add_missing_middle_editor_department_code[$i]);
                    $temp_working_model->setMissingeditordepartmentnameAttribute($i, $array_add_missing_middle_editor_department_name[$i]);
                    $temp_working_model->setMissingeditorusercodeAttribute($i, $array_add_missing_middle_editor_user_code[$i]);
                    $temp_working_model->setMissingeditorusernameAttribute($i, $array_add_missing_middle_editor_user_name[$i]);
                    if (isset($array_add_missing_middle_time_positions[$i])) {
                        $temp_working_model->setMissingmiddletimepositionsAttribute($i, $array_add_missing_middle_time_positions[$i]);
                    } else {
                        $temp_working_model->setMissingmiddletimepositionsAttribute($i, null);
                    }
                } else {
                    $temp_working_model->setMissingmiddletimeAttribute($i, null);
                    $temp_working_model->setMissingmiddletimeidAttribute($i, null);
                    $temp_working_model->setMissingeditordepartmentcodeAttribute($i, null);
                    $temp_working_model->setMissingeditordepartmentnameAttribute($i, null);
                    $temp_working_model->setMissingeditorusercodeAttribute($i, null);
                    $temp_working_model->setMissingeditorusernameAttribute($i, null);
                    $temp_working_model->setMissingmiddletimepositionsAttribute($i, null);
                }
            }
            // 中抜け戻り打刻５回までチェック
            if (!$this->chkWorkingTime($array_add_missing_return_time,(int)(Config::get('const.ARRAY_MAX_INDEX.missing_return_time')) )) {
                if ($missing_middle_note_set == false) {
                    $note .= Config::get('const.MEMO_DATA.MEMO_DATA_018').' '; 
                }
            }
            // 設定
            $array_decide_times = $this->decideWorkingTimeTo(
                $array_add_missing_return_time,
                count($array_add_missing_return_time),
                $array_add_missing_middle_time,
                count($array_add_missing_middle_time));
            for ($i=0;$i<count($array_decide_times);$i++) {
                if ($i<count($array_decide_times)) {
                    $temp_working_model->setMissingmiddlereturntimeAttribute($i, $array_decide_times[$i]);
                    $temp_working_model->setMissingmiddlereturntimeidAttribute($i, $array_add_missing_return_time_id[$i]);
                    $temp_working_model->setMissingreturneditordepartmentcodeAttribute($i, $array_add_missing_return_editor_department_code[$i]);
                    $temp_working_model->setMissingreturneditordepartmentnameAttribute($i, $array_add_missing_return_editor_department_name[$i]);
                    $temp_working_model->setMissingreturneditorusercodeAttribute($i, $array_add_missing_return_editor_user_code[$i]);
                    $temp_working_model->setMissingreturneditorusernameAttribute($i, $array_add_missing_return_editor_user_name[$i]);
                    if (isset($array_add_missing_return_time_positions[$i])) {
                        $temp_working_model->setMissingmiddlereturntimepositionsAttribute($i, $array_add_missing_return_time_positions[$i]);
                    } else {
                        $temp_working_model->setMissingmiddlereturntimepositionsAttribute($i, null);
                    }
                } else {
                    $temp_working_model->setMissingmiddlereturntimeAttribute($i, null);
                    $temp_working_model->setMissingmiddlereturntimeidAttribute($i, null);
                    $temp_working_model->setMissingreturneditordepartmentcodeAttribute($i, null);
                    $temp_working_model->setMissingreturneditordepartmentnameAttribute($i, null);
                    $temp_working_model->setMissingreturneditorusercodeAttribute($i, null);
                    $temp_working_model->setMissingreturneditorusernameAttribute($i, null);
                    $temp_working_model->setMissingmiddlereturntimepositionsAttribute($i, null);
                }
            }
            // 公用外出打刻５回までチェック
            $public_going_out_note_set = false;
            if (!$this->chkWorkingTime($array_add_public_going_out_time,(int)(Config::get('const.ARRAY_MAX_INDEX.public_going_out_time')) )) {
                $note .= Config::get('const.MEMO_DATA.MEMO_DATA_017').' '; 
                $public_going_out_note_set = true;
            }
            // 設定
            $array_decide_times = $this->decideWorkingTimeFrom($array_add_public_going_out_time, count($array_add_public_going_out_time));
            for ($i=0;$i<count($array_decide_times);$i++) {
                if ($i<count($array_decide_times)) {
                    $temp_working_model->setPublicgoingouttimeAttribute($i, $array_decide_times[$i]);
                    $temp_working_model->setPublicgoingouttimeidAttribute($i, $array_add_public_going_out_time_id[$i]);
                    $temp_working_model->setPubliceditordepartmentcodeAttribute($i, $array_add_public_going_out_editor_department_code[$i]);
                    $temp_working_model->setPubliceditordepartmentnameAttribute($i, $array_add_public_going_out_editor_department_name[$i]);
                    $temp_working_model->setPubliceditorusercodeAttribute($i, $array_add_public_going_out_editor_user_code[$i]);
                    $temp_working_model->setPubliceditorusernameAttribute($i, $array_add_public_going_out_editor_user_name[$i]);
                    if (isset($array_add_public_going_out_time_positions[$i])) {
                        $temp_working_model->setPublicgoingouttimepositionsAttribute($i, $array_add_public_going_out_time_positions[$i]);
                    } else {
                        $temp_working_model->setPublicgoingouttimepositionsAttribute($i, null);
                    }
                } else {
                    $temp_working_model->setPublicgoingouttimeAttribute($i, null);
                    $temp_working_model->setPublicgoingouttimeidAttribute($i, null);
                    $temp_working_model->setPubliceditordepartmentcodeAttribute($i, null);
                    $temp_working_model->setPubliceditordepartmentnameAttribute($i, null);
                    $temp_working_model->setPubliceditorusercodeAttribute($i, null);
                    $temp_working_model->setPubliceditorusernameAttribute($i, null);
                    $temp_working_model->setPublicgoingouttimepositionsAttribute($i, null);
                }
            }
            // 公用外出戻り打刻５回までチェック
            if (!$this->chkWorkingTime($array_add_public_return_time,(int)(Config::get('const.ARRAY_MAX_INDEX.public_return_time')) )) {
                if ($public_going_out_note_set == false) {
                    $note .= Config::get('const.MEMO_DATA.MEMO_DATA_017').' '; 
                }
            }
            // 設定
            $array_decide_times = $this->decideWorkingTimeTo(
                $array_add_public_return_time,
                count($array_add_public_return_time),
                $array_add_public_going_out_time,
                count($array_add_public_going_out_time));
            for ($i=0;$i<count($array_decide_times);$i++) {
                if ($i<count($array_decide_times)) {
                    $temp_working_model->setPublicgoingoutreturntimeAttribute($i, $array_decide_times[$i]);
                    $temp_working_model->setPublicgoingoutreturntimeidAttribute($i, $array_add_public_return_time_id[$i]);
                    $temp_working_model->setPublicreturneditordepartmentcodeAttribute($i, $array_add_public_return_editor_department_code[$i]);
                    $temp_working_model->setPublicreturneditordepartmentnameAttribute($i, $array_add_public_return_editor_department_name[$i]);
                    $temp_working_model->setPublicreturneditorusercodeAttribute($i, $array_add_public_return_editor_user_code[$i]);
                    $temp_working_model->setPublicreturneditorusernameAttribute($i, $array_add_public_return_editor_user_name[$i]);
                    if (isset($array_add_public_return_time_positions[$i])) {
                        $temp_working_model->setPublicgoingoutreturntimepositionsAttribute($i, $array_add_public_return_time_positions[$i]);
                    } else {
                        $temp_working_model->setPublicgoingoutreturntimepositionsAttribute($i, null);
                    }
                } else {
                    $temp_working_model->setPublicgoingoutreturntimeAttribute($i, null);
                    $temp_working_model->setPublicgoingoutreturntimeidAttribute($i, null);
                    $temp_working_model->setPublicreturneditordepartmentcodeAttribute($i, null);
                    $temp_working_model->setPublicreturneditordepartmentnameAttribute($i, null);
                    $temp_working_model->setPublicreturneditorusercodeAttribute($i, null);
                    $temp_working_model->setPublicreturneditorusernameAttribute($i, null);
                    $temp_working_model->setPublicgoingoutreturntimepositionsAttribute($i, null);
                }
            }
            // 所定労働時間の計算
            // calcRegulartime implement
            $array_impl_calcRegulartime = array (
                'target_date' => $target_date,
                'target_result' => $target_result,
                'array_break_worktimetable_result' => $array_break_worktimetable_result,
                'array_attendance_calc_time' => $array_attendance_calc_time,
                'array_missing_middle_time' => $array_missing_middle_time,
                'timetables' => $timetables,
                'break_attendance_time' => $break_attendance_time,
                'break_leaving_time' => $break_leaving_time,
                'array_get_timetable_result' => $array_get_timetable_result
            );
            $array_regular_calc_time_stamp = $this->calcRegulartime($array_impl_calcRegulartime);
            // タイムテーブルの所定時間
            $regular_calc_time_stamp_basic = $array_regular_calc_time_stamp['regular_calc_time_basic'];
            // 所定時間内の実働時間
            $regular_calc_time_stamp = $array_regular_calc_time_stamp['regular_calc_time'];
            $regular_calc_time_basic = $apicommon->cnvToDecFromStamp($regular_calc_time_stamp_basic);
            $regular_calc_time = $apicommon->cnvToDecFromStamp($regular_calc_time_stamp);
            $holiday_calc_time_stamp = 0;
            $holiday_calc_time = 0;
            Log::debug('  target_result->business_kubun = '.$target_result->business_kubun);
            Log::debug('  regular_calc_time_basic = '.$regular_calc_time_basic);
            Log::debug('  regular_calc_time = '.$regular_calc_time);
            if ($target_result->business_kubun == Config::get('const.C007.basic')) {
                if ($target_result->holiday_description == null || $target_result->holiday_description == "") {
                    $temp_working_model->setRegularworkingtimesAttribute($regular_calc_time);                       // 所定労働時間（出勤日）
                } elseif ($target_result->holiday_description == Config::get('const.C013_DESC.morning_off') ||
                    $target_result->holiday_description == Config::get('const.C013_DESC.afternoon_off')) {
                    $temp_working_model->setRegularworkingtimesAttribute($regular_calc_time_basic);                 // 所定労働時間（半休）
                } elseif ($target_result->holiday_description == Config::get('const.C013_DESC.paid_holiday') ||
                    $target_result->holiday_description == Config::get('const.C013_DESC.public_damage') ||
                    $target_result->holiday_description == Config::get('const.C013_DESC.nigth_shift')) {
                    $temp_working_model->setRegularworkingtimesAttribute($regular_calc_time_basic);                 // 所定労働時間（1日集計対象休暇）
                } elseif ($target_result->holiday_description == Config::get('const.C013_DESC.late_work') ||
                    $target_result->holiday_description == Config::get('const.C013_DESC.leave_early_work') ||
                    $target_result->holiday_description == Config::get('const.C013_DESC.deemed_business_trip') ||
                    $target_result->holiday_description == Config::get('const.C013_DESC.deemed_direct_go') ||
                    $target_result->holiday_description == Config::get('const.C013_DESC.deemed_direct_return') ||
                    $target_result->holiday_description == Config::get('const.C013_DESC.deemed_direct_go_return')) {
                    $temp_working_model->setRegularworkingtimesAttribute($regular_calc_time);                       // 所定労働時間（1日集計対象休暇）
                } else {
                    $temp_working_model->setRegularworkingtimesAttribute(0);                                        // 所定労働時間（1日休）
                }
            } elseif ($target_result->business_kubun == Config::get('const.C007.legal_out_holoday')) {
                $holiday_calc_time_stamp = $regular_calc_time_stamp;
                $regular_calc_time_stamp = 0;
                $regular_calc_time = 0;
                $temp_working_model->setRegularworkingtimesAttribute($regular_calc_time);               // 所定労働時間（法定外休日）
            } else {
                $holiday_calc_time_stamp = $regular_calc_time_stamp;
                $regular_calc_time_stamp = 0;
                $regular_calc_time = 0;
                $temp_working_model->setRegularworkingtimesAttribute($regular_calc_time);               // 所定労働時間（法定休日）
            }
            Log::debug('  $holiday_calc_time_stamp = '.$holiday_calc_time_stamp);
            // 残業時間
            // calcOvertime implement
            $array_impl_calcOvertime = array (
                'target_result' => $target_result,
                'array_attendance_calc_time' => $array_attendance_calc_time,
                'array_missing_middle_time' => $array_missing_middle_time,
                'array_break_worktimetable_result' => $array_break_worktimetable_result
            );
            $overtime_hours_stamp = $this->calcOvertime($array_impl_calcOvertime);
            if ($regular_calc_time_stamp > 28800 ) {
                $overtime_hours_stamp = $overtime_hours_stamp + $regular_calc_time_stamp - 28800;
            }
            $overtime_hours = $apicommon->cnvToDecFromStamp($overtime_hours_stamp);
            Log::debug('  $overtime_hours_stamp = '.$overtime_hours_stamp);
            if ($target_result->business_kubun == Config::get('const.C007.basic')) {
                $temp_working_model->setOvertimehoursAttribute($overtime_hours);                        // 普通残業時間（出勤日）
            } elseif ($target_result->business_kubun == Config::get('const.C007.legal_out_holoday')) {
                $holiday_calc_time_stamp += $overtime_hours_stamp;
                $overtime_hours_stamp = 0;
                $overtime_hours = 0;
                $temp_working_model->setOvertimehoursAttribute($overtime_hours);                        // 普通残業時間（法定外休日）
            } else {
                $holiday_calc_time_stamp += $overtime_hours_stamp;
                $overtime_hours_stamp = 0;
                $overtime_hours = 0;
                $temp_working_model->setOvertimehoursAttribute($overtime_hours);                        // 普通残業時間（法定休日）
            }
            // 時間外労働時間
            $off_hours_working_hours = $overtime_hours;
            $temp_working_model->setOffhoursworkinghoursAttribute($off_hours_working_hours);
            // 深夜残業時間
            // calcLatenightovertime implement
            $array_impl_calcLatenightovertime = array (
                'array_attendance_calc_time' => $array_attendance_calc_time,
                'array_missing_middle_time' => $array_missing_middle_time
            );
            $lastnight_overtime_hours_stamp = $this->calcLatenightovertime($array_impl_calcLatenightovertime);
            $lastnight_overtime_hours = $apicommon->cnvToDecFromStamp($lastnight_overtime_hours_stamp);
            Log::debug('  $lastnight_overtime_hours_stamp = '.$lastnight_overtime_hours_stamp);
            Log::debug('  $lastnight_overtime_hours = '.$lastnight_overtime_hours);
            Log::debug('  $target_date = '.$target_date);
            Log::debug('  $target_user_code = '.$target_user_code);
            Log::debug('  $target_department_code = '.$target_department_code);
            if ($target_result->business_kubun == Config::get('const.C007.basic')) {
                $temp_working_model->setLatenightovertimehoursAttribute($lastnight_overtime_hours);                         // 深夜残業時間
                $temp_working_model->setOutoflegalworkingholidayhoursAttribute($holiday_calc_time);                         // 法定外休日労働時間
                $temp_working_model->setLegalworkingholidayhoursAttribute($holiday_calc_time);                              // 法定休日労働時間
                // 合計実働勤務時間
                Log::debug('  1 $regular_calc_time_stamp_basic = '.$regular_calc_time_stamp_basic);
                Log::debug('  1 $regular_calc_time_stamp = '.$regular_calc_time_stamp);
                Log::debug('  1 $overtime_hours_stamp = '.$overtime_hours_stamp);
                Log::debug('  1 $lastnight_overtime_hours_stamp = '.$lastnight_overtime_hours_stamp);
                $w_overtime_hours_stamp = 0;
                if ($regular_calc_time_stamp <= 28800 ) {
                    $w_overtime_hours_stamp = $overtime_hours_stamp;
                } else {
                    $w_overtime_hours_stamp = $overtime_hours_stamp - 28800;
                }
                if ($target_result->holiday_description == null || $target_result->holiday_description == "") {
                    $total_time_stamp = $regular_calc_time_stamp + $w_overtime_hours_stamp + $lastnight_overtime_hours_stamp;
                } elseif ($target_result->holiday_description == Config::get('const.C013_DESC.morning_off') ||
                            $target_result->holiday_description == Config::get('const.C013_DESC.afternoon_off')) {
                    if ($regular_calc_time_stamp < $regular_calc_time_stamp_basic) {
                        $total_time_stamp = $regular_calc_time_stamp + $w_overtime_hours_stamp + $lastnight_overtime_hours_stamp;
                    } else {
                        $total_time_stamp = $regular_calc_time_stamp_basic + $w_overtime_hours_stamp + $lastnight_overtime_hours_stamp;
                    }
                } elseif ($target_result->holiday_description == Config::get('const.C013_DESC.late_work') ||
                    $target_result->holiday_description == Config::get('const.C013_DESC.leave_early_work') ||
                    $target_result->holiday_description == Config::get('const.C013_DESC.deemed_business_trip') ||
                    $target_result->holiday_description == Config::get('const.C013_DESC.deemed_direct_go') ||
                    $target_result->holiday_description == Config::get('const.C013_DESC.deemed_direct_return') ||
                    $target_result->holiday_description == Config::get('const.C013_DESC.deemed_direct_go_return')) {
                    $total_time_stamp = $regular_calc_time_stamp + $w_overtime_hours_stamp + $lastnight_overtime_hours_stamp;
                } else {
                    $total_time_stamp = 0;
                }
                Log::debug('  1 $total_time_stamp = '.$total_time_stamp);
                $total_time = $apicommon->cnvToDecFromStamp($total_time_stamp);
                Log::debug('  1 $total_time = '.$total_time);
                $temp_working_model->setTotalworkingtimesAttribute($total_time);
            } elseif ($target_result->business_kubun == Config::get('const.C007.legal_out_holoday')) {
                $holiday_calc_time_stamp += $lastnight_overtime_hours_stamp;
                Log::debug('  2 $holiday_calc_time_stamp = '.$lastnight_overtime_hours_stamp);
                Log::debug('  2 $holiday_calc_time_stamp = '.$holiday_calc_time_stamp);
                $holiday_calc_time = $apicommon->cnvToDecFromStamp($holiday_calc_time_stamp);
                $lastnight_overtime_hours = 0;
                $temp_working_model->setLatenightovertimehoursAttribute($lastnight_overtime_hours);                         // 深夜残業時間
                $temp_working_model->setOutoflegalworkingholidayhoursAttribute($holiday_calc_time);                         // 法定外休日労働時間
                $temp_working_model->setLegalworkingholidayhoursAttribute($lastnight_overtime_hours);                       // 法定休日労働時間
                $lastnight_overtime_hours_stamp = 0;
                $lastnight_overtime_hours = 0;
                // 深夜手当はあるが深夜残業はない
                $temp_working_model->setLatenightovertimehoursAttribute($lastnight_overtime_hours);                         // 深夜残業時間
                $temp_working_model->setOutoflegalworkingholidaynightovertimehoursAttribute($lastnight_overtime_hours);     // 法定外休日深夜残業時間
                $temp_working_model->setLegalworkingholidaynightovertimehoursAttribute($lastnight_overtime_hours);          // 法定休日深夜残業時間
                // 合計勤務時間
                $total_time_stamp = $holiday_calc_time_stamp;
                Log::debug('  2 $total_time_stamp = '.$total_time_stamp);
                $total_time = $apicommon->cnvToDecFromStamp($total_time_stamp);
                Log::debug('  2 $total_time = '.$total_time);
                $temp_working_model->setTotalworkingtimesAttribute($total_time);
            } else {
                $holiday_calc_time_stamp += $lastnight_overtime_hours_stamp;
                Log::debug('  3 $holiday_calc_time_stamp = '.$lastnight_overtime_hours_stamp);
                Log::debug('  3 $holiday_calc_time_stamp = '.$holiday_calc_time_stamp);
                Log::debug('  $holiday_calc_time_stamp = '.$holiday_calc_time_stamp);
                $holiday_calc_time = $apicommon->cnvToDecFromStamp($holiday_calc_time_stamp);
                $lastnight_overtime_hours = 0;
                $temp_working_model->setLatenightovertimehoursAttribute($lastnight_overtime_hours);                         // 深夜残業時間
                $temp_working_model->setOutoflegalworkingholidayhoursAttribute($lastnight_overtime_hours);                  // 法定外休日労働時間
                $temp_working_model->setLegalworkingholidayhoursAttribute($holiday_calc_time);                              // 法定休日労働時間
                $lastnight_overtime_hours_stamp = 0;
                $lastnight_overtime_hours = 0;
                // 深夜手当はあるが深夜残業はない
                $temp_working_model->setLatenightovertimehoursAttribute($lastnight_overtime_hours);                         // 深夜残業時間
                $temp_working_model->setOutoflegalworkingholidaynightovertimehoursAttribute($lastnight_overtime_hours);     // 法定外休日深夜残業時間
                $temp_working_model->setLegalworkingholidaynightovertimehoursAttribute($lastnight_overtime_hours);          // 法定休日深夜残業時間
                // 合計勤務時間
                $total_time_stamp = $holiday_calc_time_stamp;
                Log::debug('  3 $total_time_stamp = '.$total_time_stamp);
                $total_time = $apicommon->cnvToDecFromStamp($total_time_stamp);
                Log::debug('  3 $total_time = '.$total_time);
                $temp_working_model->setTotalworkingtimesAttribute($total_time);
            }
            // 深夜労働時間
            $w_time = $apicommon->cnvToDecFromStamp($this->calc_late_night_working_hours);
            $temp_working_model->setLatenightworkinghoursAttribute($w_time);

            // 所定外労働時間
            $outside_calc_time = 0;
            if ($target_result->business_kubun == Config::get('const.C007.basic')) {
                $default_time = (int)(Config::get('const.C002.legal_working_hours_day'));
                if ($regular_calc_time < $default_time && $total_time > $default_time) {    // 所定労働時間 < 8 and 合計勤務時間 > 8 の場合
                    $outside_calc_time = $default_time - $regular_calc_time;
                } elseif ($regular_calc_time < $total_time) { 
                    $outside_calc_time = $total_time- $regular_calc_time;
                } 
                Log::debug('  $outside_calc_time = '.$outside_calc_time);
                // 法定労働時間 法定外労働時間
                if ($total_time > $default_time) {      // 合計勤務時間 > 8 の場合
                    // 法定労働時間
                    $temp_working_model->setLegalworkingtimesAttribute($default_time);
                    // 法定外労働時間
                    $temp_working_model->setOutoflegalworkingtimesAttribute($total_time - $default_time);
                } else {
                    // 法定労働時間
                    $temp_working_model->setLegalworkingtimesAttribute($total_time);
                    // 法定外労働時間
                    $temp_working_model->setOutoflegalworkingtimesAttribute(0);
                }
            } else {
                // 法定労働時間
                $temp_working_model->setLegalworkingtimesAttribute(0);
                // 法定外労働時間
                $temp_working_model->setOutoflegalworkingtimesAttribute(0);
            }
            $temp_working_model->setOutofregularworkingtimesAttribute($outside_calc_time);          // 所定外労働時間
            // 私用外出時間
            $w_time = 0;
            for ($i=0;$i<count($array_missing_middle_time);$i++) {
                $w_time += $array_missing_middle_time[$i];
            }
            $missing_middle_time = $apicommon->cnvToDecFromStamp($w_time);
            $temp_working_model->setMissingmiddlehoursAttribute($missing_middle_time);
            // 公用外出時間
            $w_time = 0;
            for ($i=0;$i<count($array_public_going_out_time);$i++) {
                $w_time += $array_public_going_out_time[$i];
            }
            $public_going_out_time = $apicommon->cnvToDecFromStamp($w_time);
            $temp_working_model->setPublicgoingouthoursAttribute($public_going_out_time);

            // 不就労時間
            // calcNotemploymentworkinghours implement
            $array_impl_calcNotemploymentworkinghours = array (
                'target_user_code' => $target_user_code,
                'target_department_code' => $target_department_code,
                'target_date' => $target_date,
                'target_result' => $target_result,
                'array_break_worktimetable_result' => $array_break_worktimetable_result,
                'array_attendance_calc_time' => $array_attendance_calc_time,
                'array_missing_middle_time' => $array_missing_middle_time,
                'regular_calc_time' => $regular_calc_time_stamp
            );
            $w_time = $this->calcNotemploymentworkinghours($array_impl_calcNotemploymentworkinghours);
            $not_employment_working_hours = $apicommon->cnvToDecFromStamp($w_time);
            $temp_working_model->setNotemploymentworkinghoursAttribute($not_employment_working_hours + $missing_middle_time);

            $temp_working_model->setWorkingtimetablenoAttribute($target_result->working_timetable_no);
            $temp_working_model->setWorkingstatusAttribute($working_status);
            $temp_working_model->setNoteAttribute($note);
            $temp_working_model->setLateAttribute($target_result->late);
            $temp_working_model->setLeaveearlyAttribute($target_result->leave_early);
            $temp_working_model->setCurrentcalcAttribute($target_result->current_calc);
            $temp_working_model->setTobeconfirmedAttribute($target_result->to_be_confirmed);
            $temp_working_model->setWeekdaykubunAttribute($target_result->weekday_kubun);
            $temp_working_model->setWeekdaynameAttribute($target_result->weekday_name);
            $temp_working_model->setBusinesskubunAttribute($target_result->business_kubun);
            $temp_working_model->setBusinessnameAttribute($target_result->business_name);
            $temp_working_model->setHolidaykubunAttribute($target_result->holiday_kubun);
            $temp_working_model->setHolidaynameAttribute($target_result->holiday_name);
            $temp_working_model->setClosingAttribute($target_result->closing);
            $temp_working_model->setUplimittimeAttribute($target_result->uplimit_time);
            $temp_working_model->setStatutoryuplimittimeAttribute($target_result->statutory_uplimit_time);
            $temp_working_model->setTimeunitAttribute($target_result->time_unit);
            $temp_working_model->setTimeroundingAttribute($target_result->time_rounding);
            $temp_working_model->setMax3MonthtotalAttribute($target_result->max_3month_total);
            $temp_working_model->setMax6MonthtotalAttribute($target_result->max_6month_total);
            $temp_working_model->setMax12MonthtotalAttribute($target_result->max_12month_total);
            $temp_working_model->setBeginningmonthAttribute($target_result->beginning_month);
            $temp_working_model->setCheckresultAttribute($target_result->check_result);
            $temp_working_model->setCheckmaxtimesAttribute($target_result->check_max_times);
            $temp_working_model->setCheckintervalAttribute($this->check_interval2);
            $this->check_interval2 = 0;
            $temp_working_model->setYearAttribute($target_result->year);
            $temp_working_model->setPatternAttribute($target_result->pattern);
            $temp_working_model->setFixedtimeAttribute('0');
            $temp_working_model->setSystemDateAttribute(Carbon::now());
            // 登録する
            $temp_working_model->setParamdatefromAttribute(date_format(new Carbon($target_date), 'Ymd'));
            $temp_working_model->setParamdatetoAttribute(date_format(new Carbon($target_date), 'Ymd'));
            $temp_working_model->setParamEmploymentStatusAttribute($target_result->employment_status);
            $temp_working_model->setParamDepartmentcodeAttribute($target_department_code);
            $temp_working_model->setParamUsercodeAttribute($target_user_code);
            // insert
            Log::debug('insertTempWorkingTimeDate target_date = '.$target_date);
            Log::debug('insertTempWorkingTimeDate target_department_code = '.$target_department_code);
            Log::debug('insertTempWorkingTimeDate target_user_code = '.$target_user_code);
            Log::debug('insertTempWorkingTimeDate call');
            $temp_working_model->insertTempWorkingTimeDate();
            Log::debug('insertTempWorkingTimeDate return');
            return true;
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.data_error_dailycalc'));
            Log::error($e->getMessage());
            throw $e;
        }
        Log::debug('---------------------- addTempWorkingTimeDate end ------------------------ ');


    }
 
    /**
     * 所定労働時間計算
     * 
     *
     * @return 
     */
    private function calcRegulartime($params)
    {
        Log::debug('---------------------- 所定労働時間計算 calcRegulartime in ------------------------ ');
        // パラメータ設定
        $param_target_date = $params['target_date'];
        $param_target_result = $params['target_result'];
        $param_array_break_worktimetable_result = $params['array_break_worktimetable_result'];
        $param_array_attendance_calc_time = $params['array_attendance_calc_time'];
        $param_array_missing_middle_time = $params['array_missing_middle_time'];
        $param_timetables = $params['timetables'];
        $param_break_attendance_time = $params['break_attendance_time'];
        $param_break_leaving_time = $params['break_leaving_time'];
        $param_array_get_timetable_result = $params['array_get_timetable_result'];
        Log::debug('                       所定労働時間計算 param_target_date = '.$param_target_date);
        Log::debug('                       所定労働時間計算 $param_target_result->holiday_description = '.$param_target_result->holiday_description);
        $regular_calc_time_basic = 0;
        $regular_calc_time = 0;
        $index = (int)(Config::get('const.C004.regular_working_time'))-1;
        Log::debug('                       所定労働時間計算 $param_array_attendance_calc_time[$index] = '.$param_array_attendance_calc_time[$index]);
        // 通常の所定労働
        $collect_array_break_worktimetable_result = collect($param_array_break_worktimetable_result);
        $filtered = $collect_array_break_worktimetable_result
            ->where('no', '=', $param_target_result->working_timetable_no)
            ->where('working_time_kubun', Config::get('const.C004.regular_working_time'));
        $regular_calc_time_basic = 0;
        $break_start_time = null;
        $break_end_time = null;
        $dt = new Carbon($param_target_date);
        $dt_ymd = date_format($dt, 'Y-m-d');
        $dt_addday = date_format($dt->copy()->addDay(), 'Y-m-d');
        $apicommon = new ApiCommonController();
        foreach ($filtered as $item) {
            Log::debug('                       所定労働時間計算 所定労働時間 $item->from_time = '.$item->from_time);
            Log::debug('                       所定労働時間計算 所定労働時間 $item->to_time = '.$item->to_time);
            if ($item->from_time != null && $item->from_time != "") {
                $break_start_time = $dt_ymd.' '.$item->from_time;
                if ($item->from_time <= $item->to_time) {
                    $break_end_time = $dt_ymd.' '.$item->to_time;
                } else {
                    $break_end_time = $dt_addday.' '.$item->to_time;
                }
                Log::debug('                       所定労働時間計算 所定労働時間 $break_start_time = '.$break_start_time);
                Log::debug('                       所定労働時間計算 所定労働時間 $break_end_time = '.$break_end_time);
                $regular_calc_time_basic += $apicommon->diffTimeSerial($break_start_time, $break_end_time);
                // 休憩時間を除く
                $filtered_breaks = $collect_array_break_worktimetable_result
                    ->where('no', '=', $param_target_result->working_timetable_no)
                    ->where('working_time_kubun', Config::get('const.C004.regular_working_breaks_time'));
                $regular_calc_time_basic_breaks = 0;
                $break_start_time_breaks = null;
                $break_end_time_breaks = null;
                foreach ($filtered_breaks as $item_breaks) {
                    Log::debug('                       所定労働時間計算 休憩時間 $item_breaks->from_time = '.$item_breaks->from_time);
                    Log::debug('                       所定労働時間計算 休憩時間 $item_breaks->to_time = '.$item_breaks->to_time);
                    if ($item_breaks->from_time != null && $item_breaks->from_time != "") {
                        Log::debug('                       所定労働時間計算 所定労働時間 $break_start_time = '.$item->from_time);
                        Log::debug('                       所定労働時間計算 所定労働時間 $break_end_time = '.$item->to_time);
                        if ($item->from_time <= $item->to_time) {
                            $break_start_time_breaks = $dt_ymd.' '.$item_breaks->from_time;
                            Log::debug('                       所定労働時間計算1 休憩時間 $item_breaks->from_time = '.$item_breaks->from_time);
                            Log::debug('                       所定労働時間計算1 休憩時間 $item_breaks->to_time = '.$item_breaks->to_time);
                            if ($item_breaks->from_time <= $item_breaks->to_time) {
                                $break_end_time_breaks = $dt_ymd.' '.$item_breaks->to_time;
                            } else {
                                $break_end_time_breaks = $dt_addday.' '.$item_breaks->to_time;
                            }
                        } else {
                            Log::debug('                       所定労働時間計算2 休憩時間 $item_breaks->from_time = '.$item_breaks->from_time);
                            if ($item_breaks->from_time > $item->to_time) {
                                $break_start_time_breaks = $dt_ymd.' '.$item_breaks->from_time;
                            } else {
                                $break_start_time_breaks = $dt_addday.' '.$item_breaks->from_time;
                            }
                            Log::debug('                       所定労働時間計算2 休憩時間 $break_start_time_breaks = '.$break_start_time_breaks);
                            Log::debug('                       所定労働時間計算2 休憩時間 $item_breaks->to_time = '.$item_breaks->to_time);
                            if ($item_breaks->to_time > $item->to_time) {
                                $break_end_time_breaks = $dt_ymd.' '.$item_breaks->to_time;
                            } else {
                                $break_end_time_breaks = $dt_addday.' '.$item_breaks->to_time;
                            }
                            Log::debug('                       所定労働時間計算2 休憩時間 $break_end_time_breaks = '.$break_end_time_breaks);
                        }
                        Log::debug('                       所定労働時間計算 所定労働時間 $break_start_time = '.$break_start_time);
                        Log::debug('                       所定労働時間計算 所定労働時間 $break_end_time = '.$break_end_time);
                        Log::debug('                       所定労働時間計算 休憩時間 $break_start_time_breaks = '.$break_start_time_breaks);
                        Log::debug('                       所定労働時間計算 休憩時間 $break_end_time_breaks = '.$break_end_time_breaks);
                        if ($break_start_time_breaks >= $break_start_time && $break_end_time_breaks <= $break_end_time) {
                            $regular_calc_time_basic_breaks += $apicommon->diffTimeSerial($break_start_time_breaks, $break_end_time_breaks);
                            Log::debug('                       所定労働時間計算 休憩時間 $regular_calc_time_basic_breaks = '.$regular_calc_time_basic_breaks);
                        }
                    }
                }
                Log::debug('                       所定労働時間計算 休憩時間 $regular_calc_time_basic_breaks = '.$regular_calc_time_basic_breaks);
                $regular_calc_time_basic = $regular_calc_time_basic - $regular_calc_time_basic_breaks;
            }
        }
        Log::debug('                       所定労働時間計算 $regular_calc_time_basic = '.$regular_calc_time_basic);

        // 休暇時の所定労働
        if ($param_target_result->holiday_description != "" && $param_target_result->holiday_description != null) {
            // calcHolidayRegulartime implement
            $array_impl_calcHolidayRegulartime = array (
                'target_date' => $param_target_date,
                'target_result' => $param_target_result,
                'array_break_worktimetable_result' => $param_array_break_worktimetable_result,
                'array_attendance_calc_time' => $param_array_attendance_calc_time,
                'array_missing_middle_time' => $param_array_missing_middle_time,
                'timetables' => $param_timetables,
                'break_attendance_time' => $param_break_attendance_time,
                'break_leaving_time' => $param_break_leaving_time,
                'array_get_timetable_result' => $param_array_get_timetable_result
            );
            $regular_calc_time = $this->calcHolidayRegulartime($array_impl_calcHolidayRegulartime);
            Log::debug('休暇労働時間 regular_calc_time = '.$regular_calc_time);
        } else {
            if ($param_array_attendance_calc_time[$index] > 0) {
                $regular_calc_time = $param_array_attendance_calc_time[$index] - $param_array_missing_middle_time[$index];
            }
        }

        Log::debug('---------------------- 所定労働時間計算 calcRegulartime end ------------------------ ');
        Log::debug('$regular_calc_time_basic ='.$regular_calc_time_basic);
        Log::debug('$regular_calc_time ='.$regular_calc_time);
        return array('regular_calc_time_basic' => $regular_calc_time_basic, 'regular_calc_time' => $regular_calc_time);
    }
 
    /**
     * 休暇集計労働時間計算
     * 
     *
     * @return 
     */
    private function calcHolidayRegulartime($params)
    {
        Log::debug('---------------------- 休暇集計労働時間計算 calcHolidayRegulartime in ------------------------ ');
        // パラメータ設定
        $param_target_date = $params['target_date'];
        $param_target_result = $params['target_result'];
        $param_array_break_worktimetable_result = $params['array_break_worktimetable_result'];
        $param_array_attendance_calc_time = $params['array_attendance_calc_time'];
        $param_array_missing_middle_time = $params['array_missing_middle_time'];
        $param_timetables = $params['timetables'];
        $param_break_attendance_time = $params['break_attendance_time'];
        $param_break_leaving_time = $params['break_leaving_time'];
        $param_array_get_timetable_result = $params['array_get_timetable_result'];
        
        //  1日集計対象休暇　　午前半休　　午後半休
        $result_getHolydayTempStartEndTime = array();
        $break_workingtime = 0;
        $braek_time = 0;
        // getHolydayTempStartEndTime implement
        $array_impl_getHolydayTempStartEndTime = array (
            'array_break_worktimetable_result' => $param_array_break_worktimetable_result,
            'target_date' => $param_target_date,
            'working_timetable_no' => $param_target_result->working_timetable_no,
            'user_holiday_description' => $param_target_result->holiday_description,
            'break_attendance_time' => $param_break_attendance_time,
            'break_leaving_time' => $param_break_leaving_time,
            'time_unit' => $param_target_result->time_unit,
            'time_rounding' => $param_target_result->time_rounding,
            'array_get_timetable_result' => $param_array_get_timetable_result
        );
        // タイムテーブルから所定時間を取得
        $result_getHolydayTempStartEndTime = $this->getHolydayTempStartEndTime($array_impl_getHolydayTempStartEndTime);
        $break_start_time = null;
        $break_end_time = null;
        if ($param_target_result->holiday_description == Config::get('const.C013_DESC_VALUE.target_calc_time')) {
            $break_start_time = $result_getHolydayTempStartEndTime['start_record_datetime'];
            $break_end_time = $result_getHolydayTempStartEndTime['end_record_datetime'];
        } elseif ($param_target_result->holiday_description == Config::get('const.C013_DESC_VALUE.half_am')) {
            Log::debug('休暇労働時間 $result_getHolydayTempStartEndTime[start_record_datetime] = '.$result_getHolydayTempStartEndTime['start_record_datetime']);
            $break_start_time = $result_getHolydayTempStartEndTime['start_record_datetime'];
            $break_end_time = $result_getHolydayTempStartEndTime['end_record_datetime'];
            // $break_end_time = $param_break_leaving_time;
        } elseif ($param_target_result->holiday_description == Config::get('const.C013_DESC_VALUE.half_pm')) {
            $break_start_time = $result_getHolydayTempStartEndTime['start_record_datetime'];
            $break_end_time = $result_getHolydayTempStartEndTime['end_record_datetime'];
        } else {
            $index = (int)(Config::get('const.C004.regular_working_time'))-1;
            if ($param_array_attendance_calc_time[$index] > 0) {
                $break_workingtime = $param_array_attendance_calc_time[$index] - $param_array_missing_middle_time[$index];
            }
            Log::debug('---------------------- 休暇集計以外労働時間計算 calcHolidayRegulartime end ------------------------ ');
            return $break_workingtime;
        }
        Log::debug('休暇労働時間 $break_start_time = '.$break_start_time);
        Log::debug('休暇労働時間 $break_end_time = '.$break_end_time);
        // 労働時間の計算
        if (($break_start_time != "" && $break_start_time != null)
            && ($break_end_time != "" && $break_end_time != null)) {
            if ($break_start_time > $break_end_time) {
                $dt = new Carbon($break_end_time);
                $break_end_time = date_format($dt->copy()->addDay(), 'Y-m-d H:i:s');
                Log::debug('休暇労働時間 next   $break_end_time = '.$break_end_time);
            }
            $apicommon = new ApiCommonController();
            $break_workingtime = $apicommon->diffTimeSerial($break_start_time, $break_end_time);
            Log::debug('休暇労働時間 $break_workingtime = '.$break_workingtime);
            // 休憩時間があれば減算
            $braek_time = $apicommon->calcBetweenBreakTime(
                $break_start_time,
                $break_end_time,
                $param_target_date,
                $param_timetables,
                $param_target_result->working_timetable_no,
                null,
                null);
        }
        Log::debug('休暇労働時間 $break_workingtime = '.$break_workingtime);
        Log::debug('休暇労働時間 $braek_time = '.$braek_time);
        $break_workingtime = $break_workingtime - $braek_time;

        Log::debug('---------------------- 休暇集計労働時間計算 calcHolidayRegulartime end ------------------------ ');
        Log::debug('$break_workingtime ='.$break_workingtime);
        return $break_workingtime;
    }

    /**
     * 休暇集計打刻仮時刻設定
     * 
     *      有給休暇などの休暇字の集計のため、仮の打刻時刻を設定する
     *      出勤・退勤はタイムテーブルの所定時刻の時刻を返却
     *
     * @return 
     */
    private function getHolydayTempStartEndTime($params)
    {
        Log::debug('---------------------- getHolydayTempStartEndTime in ------------------------ ');
        $param_array_break_worktimetable_result = $params['array_break_worktimetable_result'];
        $param_target_date = $params['target_date'];
        $param_working_timetable_no = $params['working_timetable_no'];
        $param_user_holiday_description = $params['user_holiday_description'];
        $param_break_attendance_time = $params['break_attendance_time'];
        $param_break_leaving_time = $params['break_leaving_time'];
        $param_time_unit = $params['time_unit'];
        $param_time_rounding = $params['time_rounding'];
        $param_array_get_timetable_result = $params['array_get_timetable_result'];
        $result_start_record_datetime = null;
        $result_end_record_datetime = null;
        $apicommon = new ApiCommonController();
        Log::debug('                       $param_target_date =  '.$param_target_date);

        $collect_array_break_worktimetable_result = collect($param_array_break_worktimetable_result);
        // 1日集計対象休暇
        if ($param_user_holiday_description == Config::get('const.C013_DESC_VALUE.target_calc_time')) {
            $dt = date_format(new Carbon($param_target_date), 'Y-m-d');
            $filtered = $collect_array_break_worktimetable_result
                ->where('no', '=', $param_working_timetable_no)
                ->where('working_time_kubun', Config::get('const.C004.regular_working_time'));
            foreach ($filtered as $item) {
                $result_start_record_datetime = $dt.' '.$item->from_time;
                $result_end_record_datetime = $dt.' '.$item->to_time;
                break;
            }
        }elseif ($param_user_holiday_description == Config::get('const.C013_DESC_VALUE.half_am')) {
            $dt = date_format(new Carbon($param_target_date), 'Y-m-d');
            $filtered = $collect_array_break_worktimetable_result
                ->where('no', '=', $param_working_timetable_no)
                ->where('working_time_kubun', '=', Config::get('const.C004.regular_working_time'));
            foreach ($filtered as $item) {
                // 時刻を丸め
                $from_times = null;
                // 出勤に打刻あれば打刻を設定
                if ($param_break_attendance_time != null && $param_break_attendance_time != "") {
                    $from_times = $param_break_attendance_time;
                } else {
                    $from_times = $item->from_time;
                }
                // roundTimeByTimeStart implement
                $array_roundTimeByTimeStart = array (
                    'current_date' => $param_target_date,
                    'start_time' => $from_times,
                    'time_unit' => $param_time_unit,
                    'time_rounding' => $param_time_rounding,
                    'working_timetable_no' => $param_working_timetable_no,
                    'array_get_timetable_result' => $param_array_get_timetable_result
                );
                $result_start_record_datetime = $apicommon->roundTimeByTimeStart($array_roundTimeByTimeStart);
                $to_times = null;
                // 退勤に打刻あれば打刻を設定
                if ($param_break_leaving_time != null && $param_break_leaving_time != "") {
                    $to_times = $param_break_leaving_time;
                } else {
                    $to_times = $item->to_time;
                }
                $array_roundTimeByTimeEnd = array (
                    'current_date' => $param_target_date,
                    'end_time' => $to_times,
                    'time_unit' => $param_time_unit,
                    'time_rounding' => $param_time_rounding,
                    'working_timetable_no' => $param_working_timetable_no,
                    'array_get_timetable_result' => $param_array_get_timetable_result
                );
                $result_end_record_datetime = $apicommon->roundTimeByTimeEnd($array_roundTimeByTimeEnd);
                break;
            }
        }elseif ($param_user_holiday_description == Config::get('const.C013_DESC_VALUE.half_pm')) {
            $dt = date_format(new Carbon($param_target_date), 'Y-m-d');
            $filtered = $collect_array_break_worktimetable_result
                ->where('no', '=', $param_working_timetable_no)
                ->where('working_time_kubun', '=', Config::get('const.C004.regular_working_time'));
            foreach ($filtered as $item) {
                // 時刻を丸め
                $from_times = null;
                // 出勤に打刻あれば打刻を設定
                if ($param_break_attendance_time != null && $param_break_attendance_time != "") {
                    $from_times = $param_break_attendance_time;
                } else {
                    $from_times = $item->from_time;
                }
                // roundTimeByTimeStart implement
                $array_roundTimeByTimeStart = array (
                    'current_date' => $param_target_date,
                    'start_time' => $from_times,
                    'time_unit' => $param_time_unit,
                    'time_rounding' => $param_time_rounding,
                    'working_timetable_no' => $param_working_timetable_no,
                    'array_get_timetable_result' => $param_array_get_timetable_result
                );
                $result_start_record_datetime = $apicommon->roundTimeByTimeStart($array_roundTimeByTimeStart);
                $to_times = null;
                // 退勤に打刻あれば打刻を設定
                if ($param_break_leaving_time != null && $param_break_leaving_time != "") {
                    $to_times = $param_break_leaving_time;
                } else {
                    $to_times = $item->to_time;
                }
                $array_roundTimeByTimeEnd = array (
                    'current_date' => $param_target_date,
                    'end_time' => $to_times,
                    'time_unit' => $param_time_unit,
                    'time_rounding' => $param_time_rounding,
                    'working_timetable_no' => $param_working_timetable_no,
                    'array_get_timetable_result' => $param_array_get_timetable_result
                );
                $result_end_record_datetime = $apicommon->roundTimeByTimeEnd($array_roundTimeByTimeEnd);
                break;
            }
        }
        Log::debug('                       $result_start_record_datetime =  '.$result_start_record_datetime);
        Log::debug('                       $result_end_record_datetime =  '.$result_end_record_datetime);
        Log::debug('---------------------- getHolydayTempStartEndTime end ------------------------ ');

        return array(
            'start_record_datetime' => $result_start_record_datetime,
            'end_record_datetime' =>$result_end_record_datetime);
    }
 
    /**
     * 普通残業時間計算
     * 
     *
     * @return 
     */
    private function calcOvertime($params)
    {
        Log::debug('---------------------- 普通残業時間計算 calcOvertime in ------------------------ ');
        // パラメータ設定
        $param_target_result = $params['target_result'];
        $param_array_attendance_calc_time = $params['array_attendance_calc_time'];
        $param_array_missing_middle_time = $params['array_missing_middle_time'];

        $index = (int)(Config::get('const.C004.out_of_regular_working_time'))-1;
        $w_time = 0;
        $overtime_hours = 0;
        // $array_attendance_calc_time[$index]=0はまだ退勤していないということ
        if ($param_array_attendance_calc_time[$index] > 0) {
            Log::debug('$param_array_attendance_calc_time ='.$param_array_attendance_calc_time[$index]);
            Log::debug('$param_array_missing_middle_time ='.$param_array_missing_middle_time[$index]);
            $overtime_hours = $param_array_attendance_calc_time[$index] - $param_array_missing_middle_time[$index];
        }

        Log::debug('---------------------- 普通残業時間計算 calcOvertime ed ------------------------ ');
        Log::debug('$overtime_hours ='.$overtime_hours);
        return $overtime_hours;
    }
 
    /**
     * 深夜残業時間計算
     * 
     *
     * @return 
     */
    private function calcLatenightovertime($params)
    {
        Log::debug('---------------------- 深夜残業時間計算 calcLatenightovertime in ------------------------ ');
        // パラメータ設定
        $param_array_attendance_calc_time = $params['array_attendance_calc_time'];
        $param_array_missing_middle_time = $params['array_missing_middle_time'];

        $index = (int)(Config::get('const.C004.out_of_regular_night_working_time'))-1;
        $lastnight_overtime_hours = 0;
        // $array_attendance_calc_time[$index]=0はまだ退勤していないということ
        if ($param_array_attendance_calc_time[$index] > 0) {
            Log::debug('$param_array_attendance_calc_time ='.$param_array_attendance_calc_time[$index]);
            Log::debug('$param_array_missing_middle_time ='.$param_array_missing_middle_time[$index]);
            $lastnight_overtime_hours = $param_array_attendance_calc_time[$index] - $param_array_missing_middle_time[$index];
        }

        Log::debug('---------------------- 深夜残業時間計算 calcLatenightovertime end ------------------------ ');
        Log::debug('$lastnight_overtime_hours ='.$lastnight_overtime_hours);
        return $lastnight_overtime_hours;
    }
 
    /**
     * 不就労時間計算
     * 
     *
     * @return 
     */
    private function calcNotemploymentworkinghours($params)
    {
        Log::debug('---------------------- 不就労時間計算 calcNotemploymentworkinghours in ------------------------ ');
        // パラメータ設定
        $param_target_user_code = $params['target_user_code'];
        $param_target_department_code = $params['target_department_code'];
        $param_target_date = $params['target_date'];
        $param_target_result = $params['target_result'];
        $array_break_worktimetable_result = $params['array_break_worktimetable_result'];
        $param_array_attendance_calc_time = $params['array_attendance_calc_time'];
        $param_array_missing_middle_time = $params['array_missing_middle_time'];
        $param_regular_calc_time = $params['regular_calc_time'];

        // // 規則所定労働時間を求める
        // $timetable_model = new WorkingTimeTable();
        // $timetable_model->setParamdatefromAttribute($param_target_date);
        // $timetable_model->setParamdatetoAttribute($param_target_date);
        // $timetable_model->setParamemploymentstatusAttribute($param_target_result->employment_status);
        // $timetable_model->setParamDepartmentcodeAttribute($param_target_department_code);
        // $timetable_model->setParamUsercodeAttribute($param_target_user_code);
        // // 平日は設定している所定労働時間を求める
        // // 休日は所定労働時間=8時間であるが、（所定という概念ではない）
        // $w_not_employment_time = 0;
        // if ($param_target_result->business_kubun == Config::get('const.C007.basic')) {
        //     $apicommon = new ApiCommonController();
        //     // 引数のtimetablesとは条件がちがうので取得しなおし
        //     $w_timetables = $timetable_model->getWorkingTimeTableJoin();
        //     $calc_time = 0;
        //     if (count($w_timetables) > 0) {
        //         $w_time = 0;
        //         $w_break_time = 0;
        //         $w_from_time1 = "";
        //         $w_from_time2 = "";
        //         $w_to_time1 = "";
        //         $w_to_time2 = "";
        //         $target_dt = new Carbon($param_target_date);
        //         foreach($w_timetables as $item) {
        //             if ($item->working_time_kubun == Config::get('const.C004.regular_working_time')) {
        //                 if (isset($item->from_time) && isset($item->to_time)) {
        //                     if ($item->from_time < $item->to_time) {
        //                         $from_time = date_format($target_dt, 'Y-m-d').' '.$item->from_time;
        //                         $to_time = date_format($target_dt, 'Y-m-d').' '.$item->to_time;
        //                         $w_time += $apicommon->diffTimeSerial($from_time, $to_time);
        //                         $w_from_time1 = $item->from_time;
        //                         $w_to_time1 = $item->to_time;
        //                         $w_from_time2 = $item->from_time;
        //                         $w_to_time2 = $item->to_time;
        //                     } else {
        //                         $from_time = date_format($target_dt, 'Y-m-d').' '.$item->from_time;
        //                         $to_time = date_format($target_dt, 'Y-m-d').' '.$item->to_time;
        //                         Log::ERROR('不就労2 $from_time '.$from_time);
        //                         Log::ERROR('不就労2 $to_time '.$to_time);
        //                         $nextdt =$apicommon->getNextDay(new Carbon($to_time), 'Y/m/d');
        //                         $to_time = date_format(new Carbon($nextdt), 'Y-m-d').' 00:00:00';
        //                         $w_time += $apicommon->diffTimeSerial($from_time, $to_time);
        //                         $w_from_time1 = $item->from_time;
        //                         $w_to_time1 = '00:00:00';
        //                         $from_time = $to_time;
        //                         $to_time = date_format(new Carbon($nextdt), 'Y-m-d').' '.$item->to_time;
        //                         $w_time += $apicommon->diffTimeSerial($from_time, $to_time);
        //                         $w_from_time2 = '00:00:00';
        //                         $w_to_time2 = $item->to_time;
        //                     }
        //                 }
        //             }
        //             // 所定労働時間内の休憩の場合はその分を減算する
        //             if ($item->working_time_kubun == Config::get('const.C004.regular_working_breaks_time')) {
        //                 if (isset($item->from_time) && isset($item->to_time)) {
        //                     if (($item->from_time > $w_from_time1 && $item->from_time < $w_to_time1) ||
        //                         ($item->from_time > $w_from_time2 && $item->from_time < $w_to_time2)) {
        //                         if ($item->from_time < $item->to_time) {
        //                             $from_time = date_format($target_dt, 'Y-m-d').' '.$item->from_time;
        //                             $to_time = date_format($target_dt, 'Y-m-d').' '.$item->to_time;
        //                             $w_break_time += $apicommon->diffTimeSerial($from_time, $to_time);
        //                         } else {
        //                             $from_time = date_format($target_dt, 'Y-m-d').' '.$item->from_time;
        //                             $to_time = date_format($target_dt, 'Y-m-d').' '.$item->to_time;
        //                             $nextdt =$apicommon->getNextDay(new Carbon($to_time), 'Y/m/d');
        //                             $to_time = date_format($nextdt, 'Y-m-d').' 00:00:00';
        //                             $w_break_time += $apicommon->diffTimeSerial($from_time, $to_time);
        //                             $from_time = $to_time;
        //                             $to_time = date_format(new Carbon($nextdt), 'Y-m-d').' '.$item->to_time;
        //                             $w_break_time += $apicommon->diffTimeSerial($from_time, $to_time);
        //                         }
        //                     }
        //                 }
        //             }
        //         }
        //     }
        //     if ($param_regular_calc_time > 0) {
        //         $w_not_employment_time = $w_time - $w_break_time - $param_regular_calc_time;
        //         if ($w_not_employment_time < 0) { $w_not_employment_time = 0; }
        //     } else {
        //         // 欠勤の場合は規則所定労働時間を不就労に設定
        //         if ($param_target_result->holiday_kubun == Config::get('const.C013.absence_work')) {
        //             $w_not_employment_time = $w_time - $w_break_time;
        //             if ($w_not_employment_time < 0) { $w_not_employment_time = 0; }
        //         }
        //     }
        // }
        // 平日は設定している所定労働時間を求める
        // 休日は所定労働時間=8時間であるが、（所定という概念ではない）
        $w_not_employment_time = 0;
        if ($param_target_result->business_kubun == Config::get('const.C007.basic')) {
            $collect_break_worktimetable_result = Collect($array_break_worktimetable_result);
            $filtered = $collect_break_worktimetable_result
            ->where('no', $param_target_result->working_timetable_no)
            ->where('working_time_kubun', Config::get('const.C004.regular_working_time'));
            $calc_retimes = 0;
            $apicommon = new ApiCommonController();
            $from_time = null;
            $to_time = null;
            foreach($filtered as $item) {
                // 所定労働時間開始と終了
                $target_result_from_time = $item->from_time;
                $target_result_to_time = $item->to_time;
                Log::debug('不就労1 $target_result_from_time '.$target_result_from_time);
                Log::debug('不就労1 $target_result_to_time '.$target_result_to_time);
                $target_dt = new Carbon($param_target_date);
                if (isset($target_result_from_time) && isset($target_result_to_time)) {
                    if ($target_result_from_time < $target_result_to_time) {
                        $from_time = date_format($target_dt, 'Y-m-d').' '.$target_result_from_time;
                        $to_time = date_format($target_dt, 'Y-m-d').' '.$target_result_to_time;
                    } else {
                        $from_time = date_format($target_dt, 'Y-m-d').' '.$target_result_from_time;
                        $to_time = date_format($target_dt, 'Y-m-d').' '.$target_result_to_time;
                        $nextdt =$apicommon->getNextDay(new Carbon($to_time), 'Y/m/d');
                        $to_time = date_format(new Carbon($nextdt), 'Y-m-d').' 00:00:00';
                    }
                    Log::debug('不就労2 $from_time '.$from_time);
                    Log::debug('不就労2 $to_time '.$to_time);
                    break;
                }
            }
            $w_regular_time = 0;        // 所定時間数
            $braek_time = 0;
            Log::debug('不就労3 $from_time ='.$from_time);
            Log::debug('不就労3 $to_time ='.$to_time);
            if (isset($from_time) && isset($to_time)) {
                $w_regular_time += $apicommon->diffTimeSerial($from_time, $to_time);
                // 所定労働時間内の休憩の場合はその分を減算する
                $braek_time = $apicommon->calcBetweenBreakTime(
                    $from_time,
                    $to_time,
                    $param_target_date,
                    $collect_break_worktimetable_result,
                    $param_target_result->working_timetable_no,
                    null, null);
            }
            $w_not_employment_time = 0;
            if ($param_regular_calc_time > 0) {
                // 所定時間数 - 休憩時間数 - 所定労働時間数
                Log::debug('不就労4 $w_regular_time ='.$w_regular_time);
                Log::debug('不就労4 $braek_time ='.$braek_time);
                Log::debug('不就労4 $param_regular_calc_time ='.$param_regular_calc_time);
                $w_not_employment_time = $w_regular_time - $braek_time - $param_regular_calc_time;
                if ($w_not_employment_time < 0) { $w_not_employment_time = 0; }
            } else {
                // 欠勤の場合は規則所定労働時間を不就労に設定
                if ($param_target_result->holiday_kubun == Config::get('const.C013.absence_work')) {
                    $w_not_employment_time = $w_regular_time - $braek_time;
                    if ($w_not_employment_time < 0) { $w_not_employment_time = 0; }
                }
            }
        }

        Log::debug('---------------------- 不就労時間計算 calcNotemploymentworkinghours end ------------------------ ');
        Log::debug('$w_not_employment_time ='.$w_not_employment_time);
        return $w_not_employment_time;
    }

    /**
     * 打刻時刻状況の判定
     * 
     *      メモ、遅刻、早退、要確認の設定
     *
     * @return 
     */
    private function setNoteLateEtc($target_result)
    {
        Log::debug('---------------------- setNoteLateEtc in ------------------------ ');
        $note = '';
        $late = '';
        $leave_early = '';
        $to_be_confirmed = '';
        if (isset($target_result->note)) { $note .= $target_result->note.' '; }
        if (isset($target_result->working_interval)) {
            if ($target_result->mode == Config::get('const.C005.attendance_time')) {
                if ($target_result->working_interval == '1') {
                    $note .= Config::get('const.MEMO_DATA.MEMO_DATA_007').' '; 
                }
            }
        }
        if (isset($target_result->late)) {
            if ($target_result->late == '1') {
                $late = '1';
            }
        }
        if (isset($target_result->leave_early)) {
            if ($target_result->leave_early == '1') {
                $leave_early = '1';
            }
        }
        if (isset($target_result->to_be_confirmed)) {
            if ($target_result->to_be_confirmed == '1') {
                $to_be_confirmed = '1';
            }
        }
        Log::debug('---------------------- setNoteLateEtc end ------------------------ ');

        return array($note, $late, $leave_early, $to_be_confirmed);
    }
 
    /**
     * 打刻５回までチェック
     * 
     *
     * @return 
     */
    private function chkWorkingTime($target_array_time, $max_cnt)
    {
        Log::debug('---------------------- chkWorkingTime in ------------------------ ');
        // 打刻５回までチェック
        if (count($target_array_time) > $max_cnt) {
            return false;
        }
        Log::debug('---------------------- chkWorkingTime end ------------------------ ');

        return true;
    }
 
    /**
     * 開始打刻時刻設定値を決める
     * 
     *
     * @return 
     */
    private function decideWorkingTimeFrom($target_array_time, $index)
    {
        Log::debug('---------------------- decideWorkingTimeFrom in ------------------------ ');
        $arrray_decide_times = array();
        for ($i=0;$i<$index;$i++) {
            Log::debug('  count($target_array_time) = '.count($target_array_time));
            if (count($target_array_time) > 0 && $i < count($target_array_time)){
                Log::debug('  $target_array_time[$i] = '.$target_array_time[$i]);
                $arrray_decide_times[$i] = $target_array_time[$i];
            } else {
                $arrray_decide_times[$i] = null;
            }
        }
        Log::debug('---------------------- decideWorkingTimeFrom end ------------------------ ');

        return $arrray_decide_times;
    }
 
    /**
     * 終了打刻時刻設定値を決める
     * 
     *
     * @return 
     */
    private function decideWorkingTimeTo($target_array_time, $index, $from_array_time, $from_array_cnt)
    {
        Log::debug('---------------------- decideWorkingTimeTo in ------------------------ ');
        $arrray_decide_times = array();
        for ($i=0;$i<$index;$i++) {
            if (count($target_array_time) > 0 && $i < count($target_array_time)){
                $arrray_decide_times[$i] = $target_array_time[$i];
            } else {
                $arrray_decide_times[$i] = null;
            }
        }
        Log::debug('---------------------- decideWorkingTimeTo end ------------------------ ');

        return $arrray_decide_times;
    }
 
    /**
     * 終了打刻時刻設定値を決める
     * 
     *
     * @return 
     */
    private function decideWorkingTimeTo20200215($target_array_time, $index, $from_array_time, $from_array_cnt)
    {
        Log::debug('---------------------- decideWorkingTimeTo in ------------------------ ');
        $arrray_decide_times = array();
        // 開始時刻より過去は設定しないため設定するindexも調整するためindexを別途準備
        $set_index = 0;
        $set_j = 0;
        $set_strat_j = 0;
        $set_flg = false;
        for ($i=0;$i<$from_array_cnt;$i++) {
            $set_flg = false;
            for ($j=$set_strat_j;$j<$index;$j++) {
                if (count($target_array_time) > 0 && $i < count($target_array_time)){
                    // 開始時刻<target_array_timeを設定
                    if ($target_array_time[$j] > $from_array_time[$i]) {
                        $set_index++;
                        $arrray_decide_times[$set_index-1] = $target_array_time[$j];
                        $set_flg = true;
                        $set_j = $j;
                        $set_strat_j = $j+1;
                        break;
                    }
                }
            }
            if (!$set_flg) {
                $set_index++;
                $arrray_decide_times[$set_index-1] = $target_array_time[$set_j];
                $set_strat_j = $set_j+1;
            }
        }

        if ($from_array_cnt == 0) {
            $arrray_decide_times = $target_array_time;
        }
        Log::debug('---------------------- decideWorkingTimeTo end ------------------------ ');

        return $arrray_decide_times;
    }
 
    /**
     * calcTempWorkingTimeDateの次データ計算のための準備
     * 
     */
    private function calcTempWorkingTimeDateNextData(
        $array_working_time_kubun,
        $result
        )
    {
        $array_result_set0 = $this->setArrayTimeSet0(
            $array_working_time_kubun
        );
        $array_attendance_calc_time = $array_result_set0['array_attendance_calc_time'];
        $array_missing_middle_time = $array_result_set0['array_missing_middle_time'];
        $array_public_going_out_time = $array_result_set0['array_public_going_out_time'];
        $array_emergency_time = $array_result_set0['array_emergency_time'];
        $array_add_attendance_time = array();
        $array_add_leaving_time = array();
        $array_add_missing_middle_time = array();
        $array_add_missing_return_time = array();
        $array_add_public_going_out_time = array();
        $array_add_public_return_time = array();
        $array_add_emergency_time = array();
        $array_add_emergency_return_time = array();
        $array_add_attendance_time_positions = array();
        $array_add_leaving_time_positions = array();
        $array_add_missing_middle_time_positions = array();
        $array_add_missing_return_time_positions = array();
        $array_add_public_going_out_time_positions = array();
        $array_add_public_return_time_positions = array();
        $array_add_emergency_time_positions = array();
        $array_add_emergency_return_time_positions = array();
        $array_add_attendance_time_id = array();
        $array_add_attendance_editor_department_code = array();
        $array_add_attendance_editor_department_name = array();
        $array_add_attendance_editor_user_code = array();
        $array_add_attendance_editor_user_name = array();
        $array_add_leaving_time_id = array();
        $array_add_leaving_editor_department_code = array();
        $array_add_leaving_editor_department_name = array();
        $array_add_leaving_editor_user_code = array();
        $array_add_leaving_editor_user_name = array();
        $array_add_missing_middle_time_id = array();
        $array_add_missing_middle_editor_department_code = array();
        $array_add_missing_middle_editor_department_name = array();
        $array_add_missing_middle_editor_user_code = array();
        $array_add_missing_middle_editor_user_name = array();
        $array_add_missing_return_time_id = array();
        $array_add_missing_return_editor_department_code = array();
        $array_add_missing_return_editor_department_name = array();
        $array_add_missing_return_editor_user_code = array();
        $array_add_missing_return_editor_user_name = array();
        $array_add_public_going_out_time_id = array();
        $array_add_public_going_out_editor_department_code = array();
        $array_add_public_going_out_editor_department_name = array();
        $array_add_public_going_out_editor_user_code = array();
        $array_add_public_going_out_editor_user_name = array();
        $array_add_public_return_time_id = array();
        $array_add_public_return_editor_department_code = array();
        $array_add_public_return_editor_department_name = array();
        $array_add_public_return_editor_user_code = array();
        $array_add_public_return_editor_user_name = array();
        $array_add_emergency_time_id = array();
        $array_add_emergency_editor_department_code = array();
        $array_add_emergency_editor_department_name = array();
        $array_add_emergency_editor_user_code = array();
        $array_add_emergency_editor_user_name = array();
        $array_add_emergency_return_time_id = array();
        $array_add_emergency_return_editor_department_code = array();
        $array_add_emergency_return_editor_department_name = array();
        $array_add_emergency_return_editor_user_code = array();
        $array_add_emergency_return_editor_user_name = array();
        $attendance_time = "";
        $leaving_time = "";
        $missing_middle_time = "";
        $missing_middle_return_time = "";
        $public_going_out_time = "";
        $public_going_out_return_time = "";
        $emergency_time = "";
        $emergency_return_time = "";
        $attendance_time_positions = "";
        $leaving_time_positions = "";
        $missing_middle_time_positions = "";
        $missing_return_time_positions = "";
        $public_going_out_time_positions = "";
        $public_return_time_positions = "";
        $emergency_time_positions = "";
        $emergency_return_time_positions = "";
        $attendance_time_id = "";
        $attendance_editor_department_code = "";
        $attendance_editor_department_name = "";
        $attendance_editor_user_code = "";
        $attendance_editor_user_name = "";
        $leaving_time_id = "";
        $leaving_editor_department_code = "";
        $leaving_editor_department_name = "";
        $leaving_editor_user_code = "";
        $leaving_editor_user_name = "";
        $missing_middle_time_id = "";
        $missing_middle_editor_department_code = "";
        $missing_middle_editor_department_name = "";
        $missing_middle_editor_user_code = "";
        $missing_middle_editor_user_name = "";
        $missing_middle_return_time_id = "";
        $missing_return_editor_department_code = "";
        $missing_return_editor_department_name = "";
        $missing_return_editor_user_code = "";
        $missing_return_editor_user_name = "";
        $public_going_out_time_id = "";
        $public_going_out_editor_department_code = "";
        $public_going_out_editor_department_name = "";
        $public_going_out_editor_user_code = "";
        $public_going_out_editor_user_name = "";
        $public_going_out_return_time_id = "";
        $public_return_editor_department_code = "";
        $public_return_editor_department_name = "";
        $public_return_editor_user_code = "";
        $public_return_editor_user_name = "";
        $emergency_time_id = "";
        $emergency_editor_department_code = "";
        $emergency_editor_department_name = "";
        $emergency_editor_user_code = "";
        $emergency_editor_user_name = "";
        $emergency_return_time_id = "";
        $emergency_return_editor_department_code = "";
        $emergency_return_editor_department_name = "";
        $emergency_return_editor_user_code = "";
        $emergency_return_editor_user_name = "";

        $array_time_position = array();
        if ($result->mode == Config::get('const.C005.attendance_time')) {
            $array_time_position = $this->setTimePosition($result);
            $attendance_time = $array_time_position['result_time'];
            $attendance_time_id = $array_time_position['result_time_id'];
            $attendance_editor_department_code = $array_time_position['result_editor_department_code'];
            $attendance_editor_department_name = $array_time_position['result_editor_department_name'];
            $attendance_editor_user_code = $array_time_position['result_editor_user_code'];
            $attendance_editor_user_name = $array_time_position['result_editor_user_name'];
            $attendance_time_positions =  $array_time_position['result_time_positions'];
            if ($attendance_time <> ''){
                $array_add_attendance_time[] = $attendance_time;
                $array_add_attendance_time_id[] = $attendance_time_id;
                $array_add_attendance_editor_department_code[] = $attendance_editor_department_code;
                $array_add_attendance_editor_department_name[] = $attendance_editor_department_name;
                $array_add_attendance_editor_user_code[] = $attendance_editor_user_code;
                $array_add_attendance_editor_user_name[] = $attendance_editor_user_name;
                $array_add_attendance_time_positions[] = $attendance_time_positions;
            }
        }
        if ($result->mode == Config::get('const.C005.leaving_time')) {
            $array_time_position = $this->setTimePosition($result);
            $leaving_time = $array_time_position['result_time'];
            $leaving_time_id = $array_time_position['result_time_id'];
            $leaving_editor_department_code = $array_time_position['result_editor_department_code'];
            $leaving_editor_department_name = $array_time_position['result_editor_department_name'];
            $leaving_editor_user_code = $array_time_position['result_editor_user_code'];
            $leaving_editor_user_name = $array_time_position['result_editor_user_name'];
            $leaving_time_positions =  $array_time_position['result_time_positions'];
            if ($leaving_time <> ''){
                $array_add_leaving_time[] = $leaving_time;
                $array_add_leaving_time_id[] = $leaving_time_id;
                $array_add_leaving_editor_department_code[] = $leaving_editor_department_code;
                $array_add_leaving_editor_department_name[] = $leaving_editor_department_name;
                $array_add_leaving_editor_user_code[] = $leaving_editor_user_code;
                $array_add_leaving_editor_user_name[] = $leaving_editor_user_name;
                $array_add_leaving_time_positions[] = $leaving_time_positions;
            }
        }
        if ($result->mode == Config::get('const.C005.missing_middle_time')) {
            $array_time_position = $this->setTimePosition($result);
            $missing_middle_time = $array_time_position['result_time'];
            $missing_middle_time_id = $array_time_position['result_time_id'];
            $missing_middle_editor_department_code = $array_time_position['result_editor_department_code'];
            $missing_middle_editor_department_name = $array_time_position['result_editor_department_name'];
            $missing_middle_editor_user_code = $array_time_position['result_editor_user_code'];
            $missing_middle_editor_user_name = $array_time_position['result_editor_user_name'];
            $missing_middle_time_positions =  $array_time_position['result_time_positions'];
            if ($missing_middle_time <> ''){
                $array_add_missing_middle_time[] = $missing_middle_time;
                $array_add_missing_middle_time_id[] = $missing_middle_time_id;
                $array_add_missing_middle_editor_department_code[] = $missing_middle_editor_department_code;
                $array_add_missing_middle_editor_department_name[] = $missing_middle_editor_department_name;
                $array_add_missing_middle_editor_user_code[] = $missing_middle_editor_user_code;
                $array_add_missing_middle_editor_user_name[] = $missing_middle_editor_user_name;
                $array_add_missing_middle_time_positions[] = $missing_middle_time_positions;
            }
        }
        if ($result->mode == Config::get('const.C005.missing_middle_return_time')) {
            $array_time_position = $this->setTimePosition($result);
            $missing_middle_return_time = $array_time_position['result_time'];
            $missing_middle_return_time_id = $array_time_position['result_time_id'];
            $missing_return_editor_department_code = $array_time_position['result_editor_department_code'];
            $missing_return_editor_department_name = $array_time_position['result_editor_department_name'];
            $missing_return_editor_user_code = $array_time_position['result_editor_user_code'];
            $missing_return_editor_user_name = $array_time_position['result_editor_user_name'];
            $missing_return_time_positions =  $array_time_position['result_time_positions'];
            if ($missing_middle_return_time <> ''){
                $array_add_missing_return_time[] = $missing_middle_return_time;
                $array_add_missing_return_time_id[] = $missing_middle_return_time_id;
                $array_add_missing_return_editor_department_code[] = $missing_return_editor_department_code;
                $array_add_missing_return_editor_department_name[] = $missing_return_editor_department_name;
                $array_add_missing_return_editor_user_code[] = $missing_return_editor_user_code;
                $array_add_missing_return_editor_user_name[] = $missing_return_editor_user_name;
                $array_add_missing_return_time_positions[] = $missing_return_time_positions;
            }
        }
        if ($result->mode == Config::get('const.C005.public_going_out_time')) {
            $array_time_position = $this->setTimePosition($result);
            $public_going_out_time = $array_time_position['result_time'];
            $public_going_out_time_id = $array_time_position['result_time_id'];
            $public_going_out_editor_department_code = $array_time_position['result_editor_department_code'];
            $public_going_out_editor_department_name = $array_time_position['result_editor_department_name'];
            $public_going_out_editor_user_code = $array_time_position['result_editor_user_code'];
            $public_going_out_editor_user_name = $array_time_position['result_editor_user_name'];
            $public_going_out_time_positions =  $array_time_position['result_time_positions'];
            if ($public_going_out_time <> ''){
                $array_add_public_going_out_time[] = $public_going_out_time;
                $array_add_public_going_out_time_id[] = $public_going_out_time_id;
                $array_add_public_going_out_editor_department_code[] = $public_going_out_editor_department_code;
                $array_add_public_going_out_editor_department_name[] = $public_going_out_editor_department_name;
                $array_add_public_going_out_editor_user_code[] = $public_going_out_editor_user_code;
                $array_add_public_going_out_editor_user_name[] = $public_going_out_editor_user_name;
                $array_add_public_going_out_time_positions[] = $public_going_out_time_positions;
            }
        }
        if ($result->mode == Config::get('const.C005.public_going_out_return_time')) {
            $array_time_position = $this->setTimePosition($result);
            $public_going_out_return_time = $array_time_position['result_time'];
            $public_going_out_return_time_id = $array_time_position['result_time_id'];
            $public_return_editor_department_code = $array_time_position['result_editor_department_code'];
            $public_return_editor_department_name = $array_time_position['result_editor_department_name'];
            $public_return_editor_user_code = $array_time_position['result_editor_user_code'];
            $public_return_editor_user_name = $array_time_position['result_editor_user_name'];
            $public_return_time_positions =  $array_time_position['result_time_positions'];
            if ($public_going_out_return_time <> ''){
                $array_add_public_return_time[] = $public_going_out_return_time;
                $array_add_public_return_time_id[] = $public_going_out_return_time_id;
                $array_add_going_out_return_editor_department_code[] = $going_out_return_editor_department_code;
                $array_add_going_out_return_editor_department_name[] = $going_out_return_editor_department_name;
                $array_add_going_out_return_editor_user_code[] = $going_out_return_editor_user_code;
                $array_add_going_out_return_editor_user_name[] = $going_out_return_editor_user_name;
                $array_add_public_return_time_positions[] = $public_return_time_positions;
            }
        }

        $array_time_position = array();
        if ($result->mode == Config::get('const.C005.emergency_time')) {
            $array_time_position = $this->setTimePosition($result);
            $emergency_time = $array_time_position['result_time'];
            $emergency_time_id = $array_time_position['result_time_id'];
            $emergency_editor_department_code = $array_time_position['result_editor_department_code'];
            $emergency_editor_department_name = $array_time_position['result_editor_department_name'];
            $emergency_editor_user_code = $array_time_position['result_editor_user_code'];
            $emergency_editor_user_name = $array_time_position['result_editor_user_name'];
            $emergency_time_positions =  $array_time_position['result_time_positions'];
            if ($emergency_time <> ''){
                $array_add_emergency_time[] = $emergency_time;
                $array_add_emergency_time_id[] = $emergency_time_id;
                $array_add_emergency_editor_department_code[] = $emergency_editor_department_code;
                $array_add_emergency_editor_department_name[] = $emergency_editor_department_name;
                $array_add_emergency_editor_user_code[] = $emergency_editor_user_code;
                $array_add_emergency_editor_user_name[] = $emergency_editor_user_name;
                $array_add_emergency_time_positions[] = $emergency_time_positions;
            }
        }
        if ($result->mode == Config::get('const.C005.emergency_return_time')) {
            $array_time_position = $this->setTimePosition($result);
            $emergency_return_time = $array_time_position['result_time'];
            $emergency_return_time_id = $array_time_position['result_time_id'];
            $emergency_return_editor_department_code = $array_time_position['result_editor_department_code'];
            $emergency_return_editor_department_name = $array_time_position['result_editor_department_name'];
            $emergency_return_editor_user_code = $array_time_position['result_editor_user_code'];
            $emergency_return_editor_user_name = $array_time_position['result_editor_user_name'];
            $emergency_return_time_positions =  $array_time_position['result_time_positions'];
            if ($emergency_return_time <> ''){
                $array_add_emergency_return_time[] = $emergency_return_time;
                $array_add_emergency_return_time_id[] = $emergency_return_time_id;
                $array_add_emergency_return_editor_department_code[] = $emergency_return_editor_department_code;
                $array_add_emergency_return_editor_department_name[] = $emergency_return_editor_department_name;
                $array_add_emergency_return_editor_user_code[] = $emergency_return_editor_user_code;
                $array_add_emergency_return_editor_user_name[] = $emergency_return_editor_user_name;
                $array_add_emergency_return_time_positions[] = $emergency_return_time_positions;
            }
        }

        $this->calc_late_night_working_hours = 0;
        return array(
            'array_attendance_calc_time' => $array_attendance_calc_time,
            'array_missing_middle_time' => $array_missing_middle_time,
            'array_public_going_out_time' => $array_public_going_out_time,
            'array_emergency_time' => $array_emergency_time,
            'array_add_attendance_time' => $array_add_attendance_time,
            'array_add_leaving_time' => $array_add_leaving_time,
            'array_add_missing_middle_time' => $array_add_missing_middle_time,
            'array_add_missing_return_time' => $array_add_missing_return_time,
            'array_add_public_going_out_time' => $array_add_public_going_out_time,
            'array_add_public_return_time' => $array_add_public_return_time,
            'array_add_emergency_time' => $array_add_emergency_time,
            'array_add_emergency_return_time' => $array_add_emergency_return_time,
            'array_add_attendance_time_positions' => $array_add_attendance_time_positions,
            'array_add_leaving_time_positions' => $array_add_leaving_time_positions,
            'array_add_missing_middle_time_positions' => $array_add_missing_middle_time_positions,
            'array_add_missing_return_time_positions' => $array_add_missing_return_time_positions,
            'array_add_public_going_out_time_positions' => $array_add_public_going_out_time_positions,
            'array_add_public_return_time_positions' => $array_add_public_return_time_positions,
            'array_add_emergency_time_positions' => $array_add_emergency_time_positions,
            'array_add_emergency_return_time_positions' => $array_add_emergency_return_time_positions,
            'array_add_attendance_time_id' => $array_add_attendance_time_id,
            'array_add_attendance_editor_department_code' => $array_add_attendance_editor_department_code,
            'array_add_attendance_editor_department_name' => $array_add_attendance_editor_department_name,
            'array_add_attendance_editor_user_code' => $array_add_attendance_editor_user_code,
            'array_add_attendance_editor_user_name' => $array_add_attendance_editor_user_name,
            'array_add_leaving_time_id' => $array_add_leaving_time_id,
            'array_add_leaving_editor_department_code' => $array_add_leaving_editor_department_code,
            'array_add_leaving_editor_department_name' => $array_add_leaving_editor_department_name,
            'array_add_leaving_editor_user_code' => $array_add_leaving_editor_user_code,
            'array_add_leaving_editor_user_name' => $array_add_leaving_editor_user_name,
            'array_add_missing_middle_time_id' => $array_add_missing_middle_time_id,
            'array_add_missing_middle_editor_department_code' => $array_add_missing_middle_editor_department_code,
            'array_add_missing_middle_editor_department_name' => $array_add_missing_middle_editor_department_name,
            'array_add_missing_middle_editor_user_code' => $array_add_missing_middle_editor_user_code,
            'array_add_missing_middle_editor_user_name' => $array_add_missing_middle_editor_user_name,
            'array_add_missing_return_time_id' => $array_add_missing_return_time_id,
            'array_add_missing_return_editor_department_code' => $array_add_missing_return_editor_department_code,
            'array_add_missing_return_editor_department_name' => $array_add_missing_return_editor_department_name,
            'array_add_missing_return_editor_user_code' => $array_add_missing_return_editor_user_code,
            'array_add_missing_return_editor_user_name' => $array_add_missing_return_editor_user_name,
            'array_add_public_going_out_time_id' => $array_add_public_going_out_time_id,
            'array_add_public_going_out_editor_department_code' => $array_add_public_going_out_editor_department_code,
            'array_add_public_going_out_editor_department_name' => $array_add_public_going_out_editor_department_name,
            'array_add_public_going_out_editor_user_code' => $array_add_public_going_out_editor_user_code,
            'array_add_public_going_out_editor_user_name' => $array_add_public_going_out_editor_user_name,
            'array_add_public_return_time_id' => $array_add_public_return_time_id,
            'array_add_public_return_editor_department_code' => $array_add_public_return_editor_department_code,
            'array_add_public_return_editor_department_name' => $array_add_public_return_editor_department_name,
            'array_add_public_return_editor_user_code' => $array_add_public_return_editor_user_code,
            'array_add_public_return_editor_user_name' => $array_add_public_return_editor_user_name,
            'array_add_emergency_time_id' => $array_add_emergency_time_id,
            'array_add_emergency_editor_department_code' => $array_add_emergency_editor_department_code,
            'array_add_emergency_editor_department_name' => $array_add_emergency_editor_department_name,
            'array_add_emergency_editor_user_code' => $array_add_emergency_editor_user_code,
            'array_add_emergency_editor_user_name' => $array_add_emergency_editor_user_name,
            'array_add_emergency_return_time_id' => $array_add_emergency_return_time_id,
            'array_add_emergency_return_editor_department_code' => $array_add_emergency_return_editor_department_code,
            'array_add_emergency_return_editor_department_name' => $array_add_emergency_return_editor_department_name,
            'array_add_emergency_return_editor_user_code' => $array_add_emergency_return_editor_user_code,
            'array_add_emergency_return_editor_user_name' => $array_add_emergency_return_editor_user_name,
            'attendance_time' => $attendance_time,
            'leaving_time' => $leaving_time,
            'missing_middle_time' => $missing_middle_time,
            'missing_middle_return_time' => $missing_middle_return_time,
            'public_going_out_time' => $public_going_out_time,
            'public_going_out_return_time' => $public_going_out_return_time,
            'emergency_time' => $emergency_time,
            'emergency_return_time' => $emergency_return_time,
            'attendance_time_positions' => $attendance_time_positions,
            'leaving_time_positions' => $leaving_time_positions,
            'missing_middle_time_positions' => $missing_middle_time_positions,
            'missing_return_time_positions' => $missing_return_time_positions,
            'public_going_out_time_positions' => $public_going_out_time_positions,
            'public_return_time_positions' => $public_return_time_positions,
            'emergency_time_positions' => $emergency_time_positions,
            'emergency_return_time_positions' => $emergency_return_time_positions,
            'attendance_time_id' => $attendance_time_id,
            'attendance_editor_department_code' => $attendance_editor_department_code,
            'attendance_editor_department_name' => $attendance_editor_department_name,
            'attendance_editor_user_code' => $attendance_editor_user_code,
            'attendance_editor_user_name' => $attendance_editor_user_name,
            'leaving_time_id' => $leaving_time_id,
            'leaving_editor_department_code' => $leaving_editor_department_code,
            'leaving_editor_department_name' => $leaving_editor_department_name,
            'leaving_editor_user_code' => $leaving_editor_user_code,
            'leaving_editor_user_name' => $leaving_editor_user_name,
            'missing_middle_time_id' => $missing_middle_time_id,
            'missing_middle_editor_department_code' => $missing_middle_editor_department_code,
            'missing_middle_editor_department_name' => $missing_middle_editor_department_name,
            'missing_middle_editor_user_code' => $missing_middle_editor_user_code,
            'missing_middle_editor_user_name' => $missing_middle_editor_user_name,
            'missing_middle_return_time_id' => $missing_middle_return_time_id,
            'missing_return_editor_department_code' => $missing_return_editor_department_code,
            'missing_return_editor_department_name' => $missing_return_editor_department_name,
            'missing_return_editor_user_code' => $missing_return_editor_user_code,
            'missing_return_editor_user_name' => $missing_return_editor_user_name,
            'public_going_out_time_id' => $public_going_out_time_id,
            'public_going_out_editor_department_code' => $public_going_out_editor_department_code,
            'public_going_out_editor_department_name' => $public_going_out_editor_department_name,
            'public_going_out_editor_user_code' => $public_going_out_editor_user_code,
            'public_going_out_editor_user_name' => $public_going_out_editor_user_name,
            'public_going_out_return_time_id' => $public_going_out_return_time_id,
            'public_return_editor_department_code' => $public_return_editor_department_code,
            'public_return_editor_department_name' => $public_return_editor_department_name,
            'public_return_editor_user_code' => $public_return_editor_user_code,
            'public_return_editor_user_name' => $public_return_editor_user_name,
            'emergency_time_id' => $emergency_time_id,
            'emergency_editor_department_code' => $emergency_editor_department_code,
            'emergency_editor_department_name' => $emergency_editor_department_name,
            'emergency_editor_user_code' => $emergency_editor_user_code,
            'emergency_editor_user_name' => $emergency_editor_user_name,
            'emergency_return_time_id' => $emergency_return_time_id,
            'emergency_return_editor_department_code' => $emergency_return_editor_department_code,
            'emergency_return_editor_department_name' => $emergency_return_editor_department_name,
            'emergency_return_editor_user_code' => $emergency_return_editor_user_code,
            'emergency_return_editor_user_name' => $emergency_return_editor_user_name,
        );
    }

    /**
     * 労働時間数配列初期化
     * 
     */
    private function setArrayTimeSet0($array_working_time_kubun)
    {
        $array_attendance_calc_time = array(); 
        $array_missing_middle_time = array(); 
        $array_public_going_out_time = array(); 
        $array_emergency_time = array(); 
        for ($i=0;$i<count($array_working_time_kubun);$i++) {
            $array_attendance_calc_time[$i] = 0; 
            $array_missing_middle_time[$i] = 0; 
            $array_public_going_out_time[$i] = 0; 
            $array_emergency_time[$i] = 0; 
        }

        return array(
            'array_attendance_calc_time' => $array_attendance_calc_time,
            'array_missing_middle_time' => $array_missing_middle_time,
            'array_public_going_out_time' => $array_public_going_out_time,
            'array_emergency_time' => $array_emergency_time
        );
    }

    /**
     * 休暇集計の場合仮出勤
     * 
     */
    private function setUserHolidayStartEndTime($array_working_time_kubun)
    {
        $array_attendance_calc_time = array(); 
        $array_missing_middle_time = array(); 
        $array_public_going_out_time = array(); 
        $array_emergency_time = array(); 
        for ($i=0;$i<count($array_working_time_kubun);$i++) {
            $array_attendance_calc_time[$i] = 0; 
            $array_missing_middle_time[$i] = 0; 
            $array_public_going_out_time[$i] = 0; 
            $array_emergency_time[$i] = 0; 
        }

        return array(
            'array_attendance_calc_time' => $array_attendance_calc_time,
            'array_missing_middle_time' => $array_missing_middle_time,
            'array_public_going_out_time' => $array_public_going_out_time,
            'array_emergency_time' => $array_emergency_time
        );
    }
 
    /**
     * 時刻と位置情報の設定
     * 
     */
    private function setTimePosition($result)
    {
        $result_time = $result->record_datetime;
        $result_time_id = $result->work_times_id;
        $result_editor_department_code = $result->editor_department_code;
        $result_editor_department_name = $result->editor_department_name;
        $result_editor_user_code = $result->editor_user_code;
        $result_editor_user_name = $result->editor_user_name;
        if (isset($result->x_positions) && isset($result->y_positions)) {
            $result_time_positions = $result->x_positions.' '.$result->y_positions;
        } else {
            $result_time_positions = null;
        }

        return array(
            'result_time' => $result_time,
            'result_time_id' => $result_time_id,
            'result_editor_department_code' => $result_editor_department_code,
            'result_editor_department_name' => $result_editor_department_name,
            'result_editor_user_code' => $result_editor_user_code,
            'result_editor_user_name' => $result_editor_user_name,
            'result_time_positions' => $result_time_positions);
    }

    /**
     * 勤怠集計結果コレクション設定
     * 
     *
     * @return 
     */
    private function setCollect_Working_time($array_setting_time, $time_cnt, $issetspace)
    {

        // 勤怠集計結果コレクション設定
        $array_working_time_attendances = array();
        if (isset($array_setting_time['attendance_time_'.$time_cnt]) ||
            isset($array_setting_time['leaving_time_'.$time_cnt]) ||
            isset($array_setting_time['missing_middle_time_'.$time_cnt]) ||
            isset($array_setting_time['missing_middle_return_time_'.$time_cnt]) ||
            isset($array_setting_time['public_going_out_time_'.$time_cnt]) ||
            isset($array_setting_time['public_going_out_return_time_'.$time_cnt]) ) {
            $array_working_time_attendances[] = array(
                'attendance_time' => $array_setting_time['attendance_time_'.$time_cnt],
                'attendance_time_id' => $array_setting_time['attendance_time_id_'.$time_cnt],
                'attendance_editor_department_code' => $array_setting_time['attendance_editor_department_code_'.$time_cnt],
                'attendance_editor_department_name' => $array_setting_time['attendance_editor_department_name_'.$time_cnt],
                'attendance_editor_user_code' => $array_setting_time['attendance_editor_user_code_'.$time_cnt],
                'attendance_editor_user_name' => $array_setting_time['attendance_editor_user_name_'.$time_cnt],
                'x_attendance_time_positions' => $array_setting_time['x_attendance_time_positions_'.$time_cnt],
                'y_attendance_time_positions' => $array_setting_time['y_attendance_time_positions_'.$time_cnt],
                'leaving_time' => $array_setting_time['leaving_time_'.$time_cnt],
                'leaving_time_id' => $array_setting_time['leaving_time_id_'.$time_cnt],
                'leaving_editor_department_code' => $array_setting_time['leaving_editor_department_code_'.$time_cnt],
                'leaving_editor_department_name' => $array_setting_time['leaving_editor_department_name_'.$time_cnt],
                'leaving_editor_user_code' => $array_setting_time['leaving_editor_user_code_'.$time_cnt],
                'leaving_editor_user_name' => $array_setting_time['leaving_editor_user_name_'.$time_cnt],
                'x_leaving_time_positions' => $array_setting_time['x_leaving_time_positions_'.$time_cnt],
                'y_leaving_time_positions' => $array_setting_time['y_leaving_time_positions_'.$time_cnt],
                'missing_middle_time' => $array_setting_time['missing_middle_time_'.$time_cnt],
                'missing_middle_time_id' => $array_setting_time['missing_middle_time_id_'.$time_cnt],
                'missing_editor_department_code' => $array_setting_time['missing_editor_department_code_'.$time_cnt],
                'missing_editor_department_name' => $array_setting_time['missing_editor_department_name_'.$time_cnt],
                'missing_editor_user_code' => $array_setting_time['missing_editor_user_code_'.$time_cnt],
                'missing_editor_user_name' => $array_setting_time['missing_editor_user_name_'.$time_cnt],
                'x_missing_middle_time_positions' => $array_setting_time['x_missing_middle_time_positions_'.$time_cnt],
                'y_missing_middle_time_positions' => $array_setting_time['y_missing_middle_time_positions_'.$time_cnt],
                'missing_middle_return_time' => $array_setting_time['missing_middle_return_time_'.$time_cnt],
                'missing_middle_return_time_id' => $array_setting_time['missing_middle_return_time_id_'.$time_cnt],
                'missing_return_editor_department_code' => $array_setting_time['missing_return_editor_department_code_'.$time_cnt],
                'missing_return_editor_department_name' => $array_setting_time['missing_return_editor_department_name_'.$time_cnt],
                'missing_return_editor_user_code' => $array_setting_time['missing_return_editor_user_code_'.$time_cnt],
                'missing_return_editor_user_name' => $array_setting_time['missing_return_editor_user_name_'.$time_cnt],
                'x_missing_middle_return_time_positions' => $array_setting_time['x_missing_middle_return_time_positions_'.$time_cnt],
                'y_missing_middle_return_time_positions' => $array_setting_time['y_missing_middle_return_time_positions_'.$time_cnt],
                'public_going_out_time' => $array_setting_time['public_going_out_time_'.$time_cnt],
                'public_going_out_time_id' => $array_setting_time['public_going_out_time_id_'.$time_cnt],
                'public_editor_department_code' => $array_setting_time['public_editor_department_code_'.$time_cnt],
                'public_editor_department_name' => $array_setting_time['public_editor_department_name_'.$time_cnt],
                'public_editor_user_code' => $array_setting_time['public_editor_user_code_'.$time_cnt],
                'public_editor_user_name' => $array_setting_time['public_editor_user_name_'.$time_cnt],
                'x_public_going_out_time_positions' => $array_setting_time['x_public_going_out_time_positions_'.$time_cnt],
                'y_public_going_out_time_positions' => $array_setting_time['y_public_going_out_time_positions_'.$time_cnt],
                'public_going_out_return_time' => $array_setting_time['public_going_out_return_time_'.$time_cnt],
                'public_going_out_return_time_id' => $array_setting_time['public_going_out_return_time_id_'.$time_cnt],
                'public_return_editor_department_code' => $array_setting_time['public_return_editor_department_code_'.$time_cnt],
                'public_return_editor_department_name' => $array_setting_time['public_return_editor_department_name_'.$time_cnt],
                'public_return_editor_user_code' => $array_setting_time['public_return_editor_user_code_'.$time_cnt],
                'public_return_editor_user_name' => $array_setting_time['public_return_editor_user_name_'.$time_cnt],
                'x_public_going_out_return_time_positions' => $array_setting_time['x_public_going_out_return_time_positions_'.$time_cnt],
                'y_public_going_out_return_time_positions' => $array_setting_time['y_public_going_out_return_time_positions_'.$time_cnt]
            );
        } else {
            if ($issetspace) {
                $array_working_time_attendances[] = array(
                    'attendance_time' => '',
                    'attendance_time_id' => '',
                    'attendance_editor_department_code' => '',
                    'attendance_editor_department_name' => '',
                    'attendance_editor_user_code' => '',
                    'attendance_editor_user_name' => '',
                    'x_attendance_time_positions' => '',
                    'y_attendance_time_positions' => '',
                    'leaving_time' => '',
                    'leaving_time_id' => '',
                    'leaving_editor_department_code' => '',
                    'leaving_editor_department_name' => '',
                    'leaving_editor_user_code' => '',
                    'leaving_editor_user_name' => '',
                    'x_leaving_time_positions' => '',
                    'y_leaving_time_positions' => '',
                    'missing_middle_time' => '',
                    'missing_middle_time_id' => '',
                    'missing_editor_department_code' => '',
                    'missing_editor_department_name' => '',
                    'missing_editor_user_code' => '',
                    'missing_editor_user_name' => '',
                    'x_missing_middle_time_positions' => '',
                    'y_missing_middle_time_positions' => '',
                    'missing_middle_return_time' => '',
                    'missing_middle_return_time_id' => '',
                    'missing_return_editor_department_code' => '',
                    'missing_return_editor_department_name' => '',
                    'missing_return_editor_user_code' => '',
                    'missing_return_editor_user_name' => '',
                    'x_missing_middle_return_time_positions' => '',
                    'y_missing_middle_return_time_positions' => '',
                    'public_going_out_time' => '',
                    'public_going_out_time_id' => '',
                    'public_editor_department_code' => '',
                    'public_editor_department_name' => '',
                    'public_editor_user_code' => '',
                    'public_editor_user_name' => '',
                    'x_public_going_out_time_positions' => '',
                    'y_public_going_out_time_positions' => '',
                    'public_going_out_return_time' => '',
                    'public_going_out_return_time_id' => '',
                    'public_return_editor_department_code' => '',
                    'public_return_editor_department_name' => '',
                    'public_return_editor_user_code' => '',
                    'public_return_editor_user_name' => '',
                    'x_public_going_out_return_time_positions' => '',
                    'y_public_going_out_return_time_positions' => ''
                );
            }
        }
        return $array_working_time_attendances;
    }

    /**
     * 勤怠集計結果コレクション設定
     * 
     *
     * @return 
     */
    private function setCollect_Working_time_public($array_setting_time, $time_cnt, $issetspace)
    {

        // 勤怠集計結果コレクション設定
        $array_working_time_attendances = array();
        if (isset($array_setting_time['public_going_out_time_'.$time_cnt]) ||
            isset($array_setting_time['public_going_out_return_time_'.$time_cnt]) ) {
            $array_working_time_attendances[] = array(
                'attendance_time' => '',
                'attendance_time_id' => '',
                'attendance_editor_department_code' => '',
                'attendance_editor_department_name' => '',
                'attendance_editor_user_code' => '',
                'attendance_editor_user_name' => '',
                'x_attendance_time_positions' => '',
                'y_attendance_time_positions' => '',
                'leaving_time' => '',
                'leaving_time_id' => '',
                'leaving_editor_department_code' => '',
                'leaving_editor_department_name' => '',
                'leaving_editor_user_code' => '',
                'leaving_editor_user_name' => '',
                'x_leaving_time_positions' => '',
                'y_leaving_time_positions' => '',
                'missing_middle_time' => '',
                'missing_middle_time_id' => '',
                'missing_editor_department_code' => '',
                'missing_editor_department_name' => '',
                'missing_editor_user_code' => '',
                'missing_editor_user_name' => '',
                'x_missing_middle_time_positions' => '',
                'y_missing_middle_time_positions' => '',
                'missing_middle_return_time' => '',
                'missing_middle_return_time_id' => '',
                'missing_return_editor_department_code' => '',
                'missing_return_editor_department_name' => '',
                'missing_return_editor_user_code' => '',
                'missing_return_editor_user_name' => '',
                'x_missing_middle_return_time_positions' => '',
                'y_missing_middle_return_time_positions' => '',
                'public_going_out_time' => $array_setting_time['public_going_out_time_'.$time_cnt],
                'public_going_out_time_id' => $array_setting_time['public_going_out_time_id_'.$time_cnt],
                'public_editor_department_code' => $array_setting_time['public_editor_department_code_'.$time_cnt],
                'public_editor_department_name' => $array_setting_time['public_editor_department_name_'.$time_cnt],
                'public_editor_user_code' => $array_setting_time['public_editor_user_code_'.$time_cnt],
                'public_editor_user_name' => $array_setting_time['public_editor_user_name_'.$time_cnt],
                'x_public_going_out_time_positions' => $array_setting_time['x_public_going_out_time_positions_'.$time_cnt],
                'y_public_going_out_time_positions' => $array_setting_time['y_public_going_out_time_positions_'.$time_cnt],
                'public_going_out_return_time' => $array_setting_time['public_going_out_return_time_'.$time_cnt],
                'public_going_out_return_time_id' => $array_setting_time['public_going_out_return_time_id_'.$time_cnt],
                'public_return_editor_department_code' => $array_setting_time['public_return_editor_department_code_'.$time_cnt],
                'public_return_editor_department_name' => $array_setting_time['public_return_editor_department_name_'.$time_cnt],
                'public_return_editor_user_code' => $array_setting_time['public_return_editor_user_code_'.$time_cnt],
                'public_return_editor_user_name' => $array_setting_time['public_return_editor_user_name_'.$time_cnt],
                'x_public_going_out_return_time_positions' => $array_setting_time['x_public_going_out_return_time_positions_'.$time_cnt],
                'y_public_going_out_return_time_positions' => $array_setting_time['y_public_going_out_return_time_positions_'.$time_cnt]
            );
        } else {
            if ($issetspace) {
                $array_working_time_attendances[] = array(
                    'attendance_time' => '',
                    'attendance_time_id' => '',
                    'attendance_editor_department_code' => '',
                    'attendance_editor_department_name' => '',
                    'attendance_editor_user_code' => '',
                    'attendance_editor_user_name' => '',
                    'x_attendance_time_positions' => '',
                    'y_attendance_time_positions' => '',
                    'leaving_time' => '',
                    'leaving_time_id' => '',
                    'leaving_editor_department_code' => '',
                    'leaving_editor_department_name' => '',
                    'leaving_editor_user_code' => '',
                    'leaving_editor_user_name' => '',
                    'x_leaving_time_positions' => '',
                    'y_leaving_time_positions' => '',
                    'missing_middle_time' => '',
                    'missing_middle_time_id' => '',
                    'missing_editor_department_code' => '',
                    'missing_editor_department_name' => '',
                    'missing_editor_user_code' => '',
                    'missing_editor_user_name' => '',
                    'x_missing_middle_time_positions' => '',
                    'y_missing_middle_time_positions' => '',
                    'missing_middle_return_time' => '',
                    'missing_middle_return_time_id' => '',
                    'missing_return_editor_department_code' => '',
                    'missing_return_editor_department_name' => '',
                    'missing_return_editor_user_code' => '',
                    'missing_return_editor_user_name' => '',
                    'x_missing_middle_return_time_positions' => '',
                    'y_missing_middle_return_time_positions' => '',
                    'public_going_out_time' => '',
                    'public_going_out_time_id' => '',
                    'public_editor_department_code' => '',
                    'public_editor_department_name' => '',
                    'public_editor_user_code' => '',
                    'public_editor_user_name' => '',
                    'x_public_going_out_time_positions' => '',
                    'y_public_going_out_time_positions' => '',
                    'public_going_out_return_time' => '',
                    'public_going_out_return_time_id' => '',
                    'public_return_editor_department_code' => '',
                    'public_return_editor_department_name' => '',
                    'public_return_editor_user_code' => '',
                    'public_return_editor_user_name' => '',
                    'x_public_going_out_return_time_positions' => '',
                    'y_public_going_out_return_time_positions' => ''
                );
            }
        }
        return $array_working_time_attendances;
    }
 
    /**
     * 集計結果配列設定
     * 
     *
     * @return 
     */
    private function setArray_Working_time($working_time, $array_attendance_time)
    {
        $array_working_time_dates = array();
        for ($i=0;$i<count($array_attendance_time);$i++) {
            if ($i == 0)
            {
                $array_working_time_dates[] = array(
                    'working_date' => $working_time["working_date"],
                    'employment_status' => $working_time["employment_status"],
                    'department_code' => $working_time["department_code"],
                    'user_code' => $working_time["user_code"],
                    'employment_status_name' => $working_time["employment_status_name"],
                    'department_name' => $working_time["department_name"],
                    'user_name' => $working_time["user_name"],
                    'working_timetable_no' => $working_time["working_timetable_no"],
                    'working_timetable_name' => $working_time["working_timetable_name"],
                    'attendance_time' => $array_attendance_time[$i]["attendance_time"],
                    'attendance_time_id' => $array_attendance_time[$i]["attendance_time_id"],
                    'attendance_editor_department_code' => $array_attendance_time[$i]["attendance_editor_department_code"],
                    'attendance_editor_department_name' => $array_attendance_time[$i]["attendance_editor_department_name"],
                    'attendance_editor_user_code' => $array_attendance_time[$i]["attendance_editor_user_code"],
                    'attendance_editor_user_name' => $array_attendance_time[$i]["attendance_editor_user_name"],
                    'x_attendance_time_positions' => $array_attendance_time[$i]["x_attendance_time_positions"],
                    'y_attendance_time_positions' => $array_attendance_time[$i]["y_attendance_time_positions"],
                    'leaving_time' => $array_attendance_time[$i]["leaving_time"],
                    'leaving_time_id' => $array_attendance_time[$i]["leaving_time_id"],
                    'leaving_editor_department_code' => $array_attendance_time[$i]["leaving_editor_department_code"],
                    'leaving_editor_department_name' => $array_attendance_time[$i]["leaving_editor_department_name"],
                    'leaving_editor_user_code' => $array_attendance_time[$i]["leaving_editor_user_code"],
                    'leaving_editor_user_name' => $array_attendance_time[$i]["leaving_editor_user_name"],
                    'x_leaving_time_positions' => $array_attendance_time[$i]["x_leaving_time_positions"],
                    'y_leaving_time_positions' => $array_attendance_time[$i]["y_leaving_time_positions"],
                    'missing_middle_time' => $array_attendance_time[$i]["missing_middle_time"],
                    'missing_middle_time_id' => $array_attendance_time[$i]["missing_middle_time_id"],
                    'missing_editor_department_code' => $array_attendance_time[$i]["missing_editor_department_code"],
                    'missing_editor_department_name' => $array_attendance_time[$i]["missing_editor_department_name"],
                    'missing_editor_user_code' => $array_attendance_time[$i]["missing_editor_user_code"],
                    'missing_editor_user_name' => $array_attendance_time[$i]["missing_editor_user_name"],
                    'x_missing_middle_time_positions' => $array_attendance_time[$i]["x_missing_middle_time_positions"],
                    'y_missing_middle_time_positions' => $array_attendance_time[$i]["y_missing_middle_time_positions"],
                    'missing_middle_return_time' => $array_attendance_time[$i]["missing_middle_return_time"],
                    'missing_middle_return_time_id' => $array_attendance_time[$i]["missing_middle_return_time_id"],
                    'missing_return_editor_department_code' => $array_attendance_time[$i]["missing_return_editor_department_code"],
                    'missing_return_editor_department_name' => $array_attendance_time[$i]["missing_return_editor_department_name"],
                    'missing_return_editor_user_code' => $array_attendance_time[$i]["missing_return_editor_user_code"],
                    'missing_return_editor_user_name' => $array_attendance_time[$i]["missing_return_editor_user_name"],
                    'x_missing_middle_return_time_positions' => $array_attendance_time[$i]["x_missing_middle_return_time_positions"],
                    'y_missing_middle_return_time_positions' => $array_attendance_time[$i]["y_missing_middle_return_time_positions"],
                    'public_going_out_time' => $array_attendance_time[$i]["public_going_out_time"],
                    'public_going_out_time_id' => $array_attendance_time[$i]["public_going_out_time_id"],
                    'public_editor_department_code' => $array_attendance_time[$i]["public_editor_department_code"],
                    'public_editor_department_name' => $array_attendance_time[$i]["public_editor_department_name"],
                    'public_editor_user_code' => $array_attendance_time[$i]["public_editor_user_code"],
                    'public_editor_user_name' => $array_attendance_time[$i]["public_editor_user_name"],
                    'x_public_going_out_time_positions' => $array_attendance_time[$i]["x_public_going_out_time_positions"],
                    'y_public_going_out_time_positions' => $array_attendance_time[$i]["y_public_going_out_time_positions"],
                    'public_going_out_return_time' => $array_attendance_time[$i]["public_going_out_return_time"],
                    'public_going_out_return_time_id' => $array_attendance_time[$i]["public_going_out_return_time_id"],
                    'public_return_editor_department_code' => $array_attendance_time[$i]["public_return_editor_department_code"],
                    'public_return_editor_department_name' => $array_attendance_time[$i]["public_return_editor_department_name"],
                    'public_return_editor_user_code' => $array_attendance_time[$i]["public_return_editor_user_code"],
                    'public_return_editor_user_name' => $array_attendance_time[$i]["public_return_editor_user_name"],
                    'x_public_going_out_return_time_positions' => $array_attendance_time[$i]["x_public_going_out_return_time_positions"],
                    'y_public_going_out_return_time_positions' => $array_attendance_time[$i]["y_public_going_out_return_time_positions"],
                    'total_working_times' => $working_time["total_working_times"],
                    'regular_working_times' => $working_time["regular_working_times"],
                    'out_of_regular_working_times' => $working_time["out_of_regular_working_times"],
                    'overtime_hours' => $working_time["overtime_hours"],
                    'late_night_overtime_hours' => $working_time["late_night_overtime_hours"],
                    'late_night_working_hours' => $working_time["late_night_working_hours"],
                    'legal_working_times' => $working_time["legal_working_times"],
                    'out_of_legal_working_times' => $working_time["out_of_legal_working_times"],
                    'not_employment_working_hours' => $working_time["not_employment_working_hours"],
                    'off_hours_working_hours' => $working_time["off_hours_working_hours"],
                    'public_going_out_hours' => $working_time["public_going_out_hours"],
                    'missing_middle_hours' => $working_time["missing_middle_hours"],
                    'out_of_legal_working_holiday_hours' => $working_time["out_of_legal_working_holiday_hours"],
                    'out_of_legal_working_holiday_night_overtime_hours' => $working_time["out_of_legal_working_holiday_night_overtime_hours"],
                    'legal_working_holiday_hours' => $working_time["legal_working_holiday_hours"],
                    'legal_working_holiday_night_overtime_hours' => $working_time["legal_working_holiday_night_overtime_hours"],
                    'working_status' => $working_time["working_status"],
                    'working_status_name' => $working_time["working_status_name"],
                    'remark_holiday_name' => $working_time["remark_holiday_name"],
                    'remark_check_result' => $working_time["remark_check_result"],
                    'remark_check_max_times' => $working_time["remark_check_max_times"],
                    'remark_check_interval' => $working_time["remark_check_interval"],
                    'note' => $working_time["note"],
                    'late' => $working_time["late"],
                    'leave_early' => $working_time["leave_early"],
                    'current_calc' => $working_time["current_calc"],
                    'to_be_confirmed' => $working_time["to_be_confirmed"],
                    'weekday_kubun' => $working_time["weekday_kubun"],
                    'weekday_name' => $working_time["weekday_name"],
                    'business_kubun' => $working_time["business_kubun"],
                    'business_name' => $working_time["business_name"],
                    // 'unused_holiday_kubun' => $working_time["unused_holiday_kubun"],
                    // 'unused_holiday_name' => $working_time["unused_holiday_name"],
                    'closing' => $working_time["closing"],
                    'uplimit_time' => $working_time["uplimit_time"],
                    'statutory_uplimit_time' => $working_time["statutory_uplimit_time"],
                    'time_unit' => $working_time["time_unit"],
                    'time_rounding' => $working_time["time_rounding"],
                    'max_3month_total' => $working_time["max_3month_total"],
                    'max_6month_total' => $working_time["max_6month_total"],
                    'max_12month_total' => $working_time["max_12month_total"],
                    'beginning_month' => $working_time["beginning_month"],
                    'year' => $working_time["year"],
                    'pattern' => $working_time["pattern"],
                    'check_result' => $working_time["check_result"],
                    'check_max_times' => $working_time["check_max_times"],
                    'check_interval' => $working_time["check_interval"],
                    'fixedtime' => $working_time["fixedtime"],
                    'holiday_kubun' => $working_time["holiday_kubun"],
                    'holiday_name' => $working_time["holiday_name"],
                    'holiday_description' => $working_time["holiday_description"]
                    // 'calendars_business_kubun' => $working_time["calendars_business_kubun"],
                    // 'working_time_name' => $working_time["working_time_name"],
                    // 'predeter_time_name' => $working_time["predeter_time_name"],
                    // 'predeter_time_secondname' => $working_time["predeter_time_secondname"],
                    // 'predeter_night_time_name' => $working_time["predeter_night_time_name"],
                    // 'predeter_night_time_secondname' => $working_time["predeter_night_time_secondname"]
                );
            } else {
                $array_working_time_dates[] = array(
                    'working_date' => '',
                    'employment_status' => '',
                    'department_code' => '',
                    'user_code' => '',
                    'employment_status_name' => '',
                    'department_name' => '',
                    'user_name' => '',
                    'working_timetable_no' => '',
                    'working_timetable_name' => '',
                    'attendance_time' => $array_attendance_time[$i]["attendance_time"],
                    'attendance_time_id' => $array_attendance_time[$i]["attendance_time_id"],
                    'attendance_editor_department_code' => $array_attendance_time[$i]["attendance_editor_department_code"],
                    'attendance_editor_department_name' => $array_attendance_time[$i]["attendance_editor_department_name"],
                    'attendance_editor_user_code' => $array_attendance_time[$i]["attendance_editor_user_code"],
                    'attendance_editor_user_name' => $array_attendance_time[$i]["attendance_editor_user_name"],
                    'x_attendance_time_positions' => $array_attendance_time[$i]["x_attendance_time_positions"],
                    'y_attendance_time_positions' => $array_attendance_time[$i]["y_attendance_time_positions"],
                    'leaving_time' => $array_attendance_time[$i]["leaving_time"],
                    'leaving_time_id' => $array_attendance_time[$i]["leaving_time_id"],
                    'leaving_editor_department_code' => $array_attendance_time[$i]["leaving_editor_department_code"],
                    'leaving_editor_department_name' => $array_attendance_time[$i]["leaving_editor_department_name"],
                    'leaving_editor_user_code' => $array_attendance_time[$i]["leaving_editor_user_code"],
                    'leaving_editor_user_name' => $array_attendance_time[$i]["leaving_editor_user_name"],
                    'x_leaving_time_positions' => $array_attendance_time[$i]["x_leaving_time_positions"],
                    'y_leaving_time_positions' => $array_attendance_time[$i]["y_leaving_time_positions"],
                    'missing_middle_time' => $array_attendance_time[$i]["missing_middle_time"],
                    'missing_middle_time_id' => $array_attendance_time[$i]["missing_middle_time_id"],
                    'missing_editor_department_code' => $array_attendance_time[$i]["missing_editor_department_code"],
                    'missing_editor_department_name' => $array_attendance_time[$i]["missing_editor_department_name"],
                    'missing_editor_user_code' => $array_attendance_time[$i]["missing_editor_user_code"],
                    'missing_editor_user_name' => $array_attendance_time[$i]["missing_editor_user_name"],
                    'x_missing_middle_time_positions' => $array_attendance_time[$i]["x_missing_middle_time_positions"],
                    'y_missing_middle_time_positions' => $array_attendance_time[$i]["y_missing_middle_time_positions"],
                    'missing_middle_return_time' => $array_attendance_time[$i]["missing_middle_return_time"],
                    'missing_middle_return_time_id' => $array_attendance_time[$i]["missing_middle_return_time_id"],
                    'missing_return_editor_department_code' => $array_attendance_time[$i]["missing_return_editor_department_code"],
                    'missing_return_editor_department_name' => $array_attendance_time[$i]["missing_return_editor_department_name"],
                    'missing_return_editor_user_code' => $array_attendance_time[$i]["missing_return_editor_user_code"],
                    'missing_return_editor_user_name' => $array_attendance_time[$i]["missing_return_editor_user_name"],
                    'x_missing_middle_return_time_positions' => $array_attendance_time[$i]["x_missing_middle_return_time_positions"],
                    'y_missing_middle_return_time_positions' => $array_attendance_time[$i]["y_missing_middle_return_time_positions"],
                    'public_going_out_time' => $array_attendance_time[$i]["public_going_out_time"],
                    'public_going_out_time_id' => $array_attendance_time[$i]["public_going_out_time_id"],
                    'public_editor_department_code' => $array_attendance_time[$i]["public_editor_department_code"],
                    'public_editor_department_name' => $array_attendance_time[$i]["public_editor_department_name"],
                    'public_editor_user_code' => $array_attendance_time[$i]["public_editor_user_code"],
                    'public_editor_user_name' => $array_attendance_time[$i]["public_editor_user_name"],
                    'x_public_going_out_time_positions' => $array_attendance_time[$i]["x_public_going_out_time_positions"],
                    'y_public_going_out_time_positions' => $array_attendance_time[$i]["y_public_going_out_time_positions"],
                    'public_going_out_return_time' => $array_attendance_time[$i]["public_going_out_return_time"],
                    'public_going_out_return_time_id' => $array_attendance_time[$i]["public_going_out_return_time_id"],
                    'public_return_editor_department_code' => $array_attendance_time[$i]["public_return_editor_department_code"],
                    'public_return_editor_department_name' => $array_attendance_time[$i]["public_return_editor_department_name"],
                    'public_return_editor_user_code' => $array_attendance_time[$i]["public_return_editor_user_code"],
                    'public_return_editor_user_name' => $array_attendance_time[$i]["public_return_editor_user_name"],
                    'x_public_going_out_return_time_positions' => $array_attendance_time[$i]["x_public_going_out_return_time_positions"],
                    'y_public_going_out_return_time_positions' => $array_attendance_time[$i]["y_public_going_out_return_time_positions"],
                    'total_working_times' => '',
                    'regular_working_times' => '',
                    'out_of_regular_working_times' => '',
                    'overtime_hours' => '',
                    'late_night_overtime_hours' => '',
                    'late_night_working_hours' => '',
                    'legal_working_times' => '',
                    'out_of_legal_working_times' => '',
                    'not_employment_working_hours' => '',
                    'off_hours_working_hours' => '',
                    'public_going_out_hours' => '',
                    'missing_middle_hours' => '',
                    'out_of_legal_working_holiday_hours' => '',
                    'out_of_legal_working_holiday_night_overtime_hours' => '',
                    'legal_working_holiday_hours' => '',
                    'legal_working_holiday_night_overtime_hours' => '',
                    'working_status' => '',
                    'working_status_name' => '',
                    'remark_holiday_name' => '',
                    'remark_check_result' => '',
                    'remark_check_max_times' => '',
                    'remark_check_interval' => '',
                    'note' => '',
                    'late' => '',
                    'leave_early' => '',
                    'current_calc' => '',
                    'to_be_confirmed' => '',
                    'weekday_kubun' => '',
                    'weekday_name' => '',
                    'business_kubun' => '',
                    'business_name' => '',
                    // 'unused_holiday_kubun' => '',
                    // 'unused_holiday_name' => '',
                    'closing' => '',
                    'uplimit_time' => '',
                    'statutory_uplimit_time' => '',
                    'time_unit' => '',
                    'time_rounding' => '',
                    'max_3month_total' => '',
                    'max_6month_total' => '',
                    'max_12month_total' => '',
                    'beginning_month' => '',
                    'year' => '',
                    'pattern' => '',
                    'check_result' => '',
                    'check_max_times' => '',
                    'check_interval' => '',
                    'fixedtime' => '',
                    'holiday_kubun' => '',
                    'holiday_name' => '',
                    'holiday_description' => ''
                    // 'calendars_business_kubun' => '',
                    // 'working_time_name' => '',
                    // 'predeter_time_name' => '',
                    // 'predeter_time_secondname' => '',
                    // 'predeter_night_time_name' => '',
                    // 'predeter_night_time_secondname' => ''
                );
            }
        }

        return $array_working_time_dates;
    }
 
    /**
     * 打刻データSKIP判定
     * 
     *
     * @return 
     */
    private function isDupTime($result, $before_result)
    {
        if ($result == null || $before_result == null) {
            return true;
        }
        if (($result->record_datetime == null || $result->record_datetime == "") ||
            ($before_result->record_datetime == null || $before_result->record_datetime == "")) {
            return true;
        }
        // 直前のデータと同じか？
        if ($result->user_code != $before_result->user_code ||
            $result->department_code != $before_result->department_code ||
            $result->record_date != $before_result->record_date ||
            $result->record_datetime != $before_result->record_datetime ||
            $result->mode != $before_result->mode) {
            return true;
        } else {
            return false;
        }
    }
 
    /**
     * モードや出勤打刻回数によるタイムテーブル所定時刻の設定
     * 
     *
     * @return 
     */
    private function setWorkingtimetabletime($params)
    {
        try {
            $target_date = $params['target_date'];
            $feature_attendance_count = $params['feature_attendance_count'];
            $attendance_work_time = $params['attendance_work_time'];
            $early_time = $params['early_time'];
            $em_details = $params['em_details'];
            $result = $params['result'];
            $working_timetable_no = $result->working_timetable_no;
            $working_timetable_name = $result->working_timetable_name;
            $working_from_time = $result->working_timetable_from_time;
            $working_to_time = $result->working_timetable_to_time;

            // 出退勤
            if ($result->mode == Config::get('const.C005.attendance_time') || $result->mode == Config::get('const.C005.leaving_time')) {
                Log::debug('         setWorkingtimetabletime 出退勤 $result->mode = '.$result->mode);
                $apicommon = new ApiCommonController();
                // 出退勤打刻回数が複数回の場合は、タイムテーブル開始終了時刻が対応されていないため、打刻時刻より開始終了時刻を特定する
                // 出退勤打刻回数 feature_attendance_count
                Log::debug('         setWorkingtimetabletime 出退勤 $feature_attendance_count = '.$feature_attendance_count);
                if ($feature_attendance_count > 1) {
                    // calcWorkingTimeDate implement
                    $array_impl_getWorkingHoursByStamp = array (
                        'target_date' => $target_date,
                        'department_code' => $result->department_code,
                        'user_code' => $result->user_code,
                        'mode' => $result->mode,
                        'record_datetime' => $result->record_datetime
                    );
                    $array_result = $apicommon->getWorkingHoursByStamp($array_impl_getWorkingHoursByStamp);
                    $working_from_time = $array_result['working_from_time'];
                    $working_to_time = $array_result['working_to_time'];
                    Log::debug('         setWorkingtimetabletime 出退勤 $working_from_time = '.$working_from_time);
                    Log::debug('         setWorkingtimetabletime 出退勤 $working_to_time = '.$working_to_time);
                }
                // 早出も集計する場合、出勤時刻前の場合のリンクNOが設定されていれば
                // タイムテーブル開始終了時刻を１時間間に調整
                Log::debug('         setWorkingtimetabletime 出退勤 $early_time = '.$early_time);
                Log::debug('         setWorkingtimetabletime 出退勤 $result->ago_time_no = '.$result->ago_time_no);
                if ($early_time == 1 && $result->ago_time_no != 0) {
                    // 打刻時刻とタイムテーブル開始終了時刻に1時間以上差があれば
                    // 出勤時刻前の場合のリンクNOのタイムテーブルを取得する
                    $calc_times = 0;
                    $dt = new Carbon($target_date);
                    $w_ymd = date_format($dt, 'Y-m-d');
                    if ($result->mode == Config::get('const.C005.attendance_time')) {
                        $from_datetime = $w_ymd.' '.$working_from_time;
                        Log::debug('         setWorkingtimetabletime 出退勤 $result->record_datetime = '.$result->record_datetime);
                        Log::debug('         setWorkingtimetabletime 出退勤 $from_datetime = '.$from_datetime);
                        $calc_times = $apicommon->diffTimeSerial($result->record_datetime, $from_datetime);
                    } elseif ($result->mode == Config::get('const.C005.leaving_time')) {
                        $to_datetime = $w_ymd.' '.$working_to_time;
                        Log::debug('         setWorkingtimetabletime 出退勤 $result->record_datetime = '.$result->record_datetime);
                        Log::debug('         setWorkingtimetabletime 出退勤 $to_datetime = '.$to_datetime);
                        $calc_times = $apicommon->diffTimeSerial($result->record_datetime, $to_datetime);
                    }
                    Log::debug('         setWorkingtimetabletime 出退勤 $calc_times = '.$calc_times);
                    if ($calc_times >= 3600) {
                        $time_table = new WorkingTimeTable();
                        $time_table->setNoAttribute($result->ago_time_no);
                        $time_table->setParamapplytermfromAttribute($target_date);
                        $ago_time_no_details = $time_table->getDetail();
                        foreach($ago_time_no_details as $item) {
                            $working_timetable_no = $item->no;
                            // 名称は設定しない
                            //$working_timetable_name = $item->name;
                            $working_from_time = $item->from_time;
                            $working_to_time = $item->to_time;
                            break;
                        }
                    }
                    $array_workingHours = array(
                        'working_timetable_no' => $working_timetable_no,
                        'working_timetable_name' => $working_timetable_name,
                        'working_from_time' => $working_from_time,
                        'working_to_time' => $working_to_time
                    );
                    return $array_workingHours;
                }
                // 出退勤打刻回数 feature_attendance_count
                Log::debug('         setWorkingtimetabletime 出退勤 $feature_attendance_count = '.$feature_attendance_count);
                if ($feature_attendance_count == 1) {
                    // そのまま返却
                    $array_workingHours = array(
                        'working_timetable_no' => $working_timetable_no,
                        'working_timetable_name' => $working_timetable_name,
                        'working_from_time' => $working_from_time,
                        'working_to_time' => $working_to_time
                    );
                    return $array_workingHours;
                }
                $array_workingHours = array(
                    'working_timetable_no' => $working_timetable_no,
                    'working_timetable_name' => $working_timetable_name,
                    'working_from_time' => $working_from_time,
                    'working_to_time' => $working_to_time
                );
                return $array_workingHours;
            } elseif ($result->mode == Config::get('const.C005.missing_middle_time') ||
                $result->mode == Config::get('const.C005.missing_middle_return_time') ||
                $result->mode == Config::get('const.C005.public_going_out_time') ||
                $result->mode == Config::get('const.C005.public_going_out_return_time')) {
                // 外出はそのまま返却
                $array_workingHours = array(
                    'working_timetable_no' => $working_timetable_no,
                    'working_timetable_name' => $working_timetable_name,
                    'working_from_time' => $working_from_time,
                    'working_to_time' => $working_to_time
                );
                return $array_workingHours;
            } elseif ($result->mode == Config::get('const.C005.emergency_time')) {
                // 緊急開始
                foreach($em_details as $item) {
                    $working_timetable_no = $item->no;
                    $working_timetable_name = $item->name;
                    break;
                }
                $dt = new Carbon($result->record_datetime);
                $w_dt = date_format($dt, 'H:i:s');
                $working_from_time = $w_dt;
                // 緊急終了時刻はまだ取得されていないことになるので
                // 仮に同じ時刻（=24時間後）に設定する
                $working_to_time = $w_dt;
                $array_workingHours = array(
                    'working_timetable_no' => $working_timetable_no,
                    'working_timetable_name' => $working_timetable_name,
                    'working_from_time' => $working_from_time,
                    'working_to_time' => $working_to_time
                );
                return $array_workingHours;
            } elseif ($result->mode == Config::get('const.C005.emergency_return_time')) {
                // 緊急終了
                foreach($em_details as $item) {
                    $working_timetable_no = $item->no;
                    $working_timetable_name = $item->name;
                    break;
                }
                $dt_start = new Carbon($attendance_work_time);
                $w_dt_start = date_format($dt_start, 'H:i:s');
                $dt = new Carbon($result->record_datetime);
                $w_dt = date_format($dt, 'H:i:s');
                $working_from_time = $w_dt_start;
                $working_to_time = $w_dt;
                $array_workingHours = array(
                    'working_timetable_no' => $working_timetable_no,
                    'working_timetable_name' => $working_timetable_name,
                    'working_from_time' => $working_from_time,
                    'working_to_time' => $working_to_time
                );
                return $array_workingHours;
            }

            // 設定
            $array_workingHours = array(
                'working_timetable_no' => $working_timetable_no,
                'working_timetable_name' => $working_timetable_name,
                'working_from_time' => $working_from_time,
                'working_to_time' => $working_to_time
            );
            return $array_workingHours;
        }catch(\PDOException $pe){
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_error_dailycalc'));
            throw $pe;
        }catch(\Exception $e){
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_accesee_error_dailycalc'));
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.data_error_dailycalc'));
            Log::error($e->getMessage());
            throw $e;
        }
    }
  
    /**
     * 当日の計算対象となるか
     * 
     *
     * @return 
     */
    private function isCurrentDateCalc($params)
    {
        $target_date = $params['target_date_ymd'];
        $result = $params['result'];
        $attendance_flg = $params['attendance_flg'];        // 対応する出勤モードの集計対象flg
        
        Log::debug('         isCurrentDateCalc $attendance_flg = '.$attendance_flg);
        $target_flg = false;                    // 当日の計算対象
        $dt = new Carbon($target_date);
        $w_minus1_ymd = date_format($dt->copy()->subDay(), 'Y-m-d');        // 前日
        $w_today_ymd = date_format($dt->copy(), 'Y-m-d');                   // 当日
        $w_plus1_ymd = date_format($dt->copy()->addDay(), 'Y-m-d');         // 翌日
        $w_plus2_ymd = date_format($dt->copy()->addDay(2), 'Y-m-d');        // 翌日+1
        Log::debug('         isCurrentDateCalc $w_minus1_ymd = '.$w_minus1_ymd);
        Log::debug('         isCurrentDateCalc $w_today_ymd = '.$w_today_ymd);
        Log::debug('         isCurrentDateCalc $w_plus1_ymd = '.$w_plus1_ymd);
        Log::debug('         isCurrentDateCalc $w_plus2_ymd = '.$w_plus2_ymd);
        //  タイムテーブル開始時刻
        $w_minus1_from_datetime = $w_minus1_ymd.' '.$result->working_timetable_from_time;       // 前日のタイムテーブル開始時刻
        $w_today_from_datetime = $w_today_ymd.' '.$result->working_timetable_from_time;         // 当日のタイムテーブル開始時刻
        $w_plus1_from_datetime = $w_plus1_ymd.' '.$result->working_timetable_from_time;         // 翌日のタイムテーブル開始時刻
        //  タイムテーブル終了時刻
        if ($result->working_timetable_from_time <= $result->working_timetable_to_time) {
            $w_minus1_to_datetime = $w_minus1_ymd.' '.$result->working_timetable_to_time;       // 前日のタイムテーブル終了時刻
            $w_today_to_datetime = $w_today_ymd.' '.$result->working_timetable_to_time;         // 当日のタイムテーブル終了時刻
            $w_plus1_to_datetime = $w_plus1_ymd.' '.$result->working_timetable_to_time;         // 翌日のタイムテーブル終了時刻
        } else {
            $w_minus1_to_datetime = $w_today_ymd.' '.$result->working_timetable_to_time;        // 前日のタイムテーブル終了時刻
            $w_today_to_datetime = $w_plus1_ymd.' '.$result->working_timetable_to_time;         // 当日のタイムテーブル終了時刻
            $w_plus1_to_datetime = $w_plus2_ymd.' '.$result->working_timetable_to_time;         // 翌日のタイムテーブル終了時刻
        }
        Log::debug('         isCurrentDateCalc w_minus1_from_datetime = '.$w_minus1_from_datetime);
        Log::debug('         isCurrentDateCalc w_minus1_to_datetime = '.$w_minus1_to_datetime);
        Log::debug('         isCurrentDateCalc w_today_from_datetime = '.$w_today_from_datetime);
        Log::debug('         isCurrentDateCalc w_today_to_datetime = '.$w_today_to_datetime);
        Log::debug('         isCurrentDateCalc w_plus1_from_datetime = '.$w_plus1_from_datetime);
        Log::debug('         isCurrentDateCalc w_plus1_to_datetime = '.$w_plus1_to_datetime);

        // 出勤または緊急取集開始
        if ($result->mode == Config::get('const.C005.attendance_time') ||
            $result->mode == Config::get('const.C005.emergency_time')) {
            // 出勤打刻時刻 >= 当日のタイムテーブル開始時刻  and 出勤打刻時刻 <= 当日のタイムテーブル終了時刻
            // の場合、当日の計算対象とする
            if ($result->record_datetime >= $w_today_from_datetime &&
                $result->record_datetime <= $w_today_to_datetime) {
                $target_flg = true;
            } else {
                // 出勤打刻時刻 >= 前日のタイムテーブル終了時刻  and 出勤打刻時刻 < 当日のタイムテーブル開始時刻
                // の場合、当日の計算対象とする
                if ($result->record_datetime >= $w_minus1_to_datetime &&
                    $result->record_datetime < $w_today_from_datetime) {
                    $target_flg = true;
                } else {
                    $target_flg = false;
                }
            }
        } else {
            // 対応する出勤モードの集計対象flgがtrue（出勤打刻があった）の場合は対象
            if ($attendance_flg) {
                $target_flg = true;
            } else {
                // 出勤打刻がなかった場合は打刻エラーとなるが、出勤以外の打刻が当日分の打刻である場合対象とする
                // 打刻時刻 >= 当日のタイムテーブル開始時刻  and 打刻時刻 <= 翌日のタイムテーブル開始時刻（当日のタイムテーブル終了時刻に変更）
                // の場合、当日の計算対象とする（TODO 100％ひろえない）
                if ($result->record_datetime >= $w_today_from_datetime &&
                    $result->record_datetime <= $w_today_to_datetime) {
                    $target_flg = true;
                } else {
                    $target_flg = false;
                }
                // if ($result->working_timetable_from_time <= $result->working_timetable_to_time) {
                //     $target_flg = false;
                // } else {
                //     if ($result->record_date > $target_date) {
                //         $target_flg = true;
                //     } else {
                //         $target_flg = false;
                //     }
                // }
                // 出対応する出勤モードの集計対象flgがfalse（出勤打刻がなかった）の場合は
                // タイムテーブル開始時刻>タイムテーブル終了時刻の場合は前日の夜勤と考えられるので
                // 集計対象外とする
                // 打刻時刻 < 当日のタイムテーブル開始時刻
                // の場合、当日の計算対象とする
                // if ($result->working_timetable_from_time > $result->working_timetable_to_time) {
                //     $target_flg = false;
                // } else {
                //     if ($result->record_datetime < $w_today_from_datetime) {
                //         $target_flg = true;
                //     } else {
                        // $target_flg = false;
                //     }
                // }
                // -----------------------　20200321コメント化 start --------------------- 
                // if ($current_department_code != $before_department_code ||
                //     $current_user_code != $before_user_code) {
                //     $attendance_target_flg = false;
                // }
                // $target_flg = $attendance_target_flg;
                // -----------------------　20200321コメント化 end --------------------- 
                // -----------------------　20200215コメント化 start --------------------- 
                // if ($result->mode == Config::get('const.C005.leaving_time')) {
                //     $attendance_target_flg = false;
                // }
                // -----------------------　20200215コメント化 end --------------------- 
            }
        }

        return $target_flg;
    }
}