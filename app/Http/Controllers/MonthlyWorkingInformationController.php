<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\WorkTime;
use App\WorkingTimedate;
use App\Setting;
use App\Company;
use App\Http\Controllers\DailyWorkingInformationController;
use App\Http\Controllers\ApiCommonController;

class MonthlyWorkingInformationController extends Controller
{

    // 集計用配列
    private $array_user = array();
    private $array_date = array();
    // メッセージ
    private $array_messagedata = array();
    private $collect_massegedata = null;

    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        return view('monthly_working_information');
    }


    /**
     * 月次集計処理
     *
     * @return void
     */
    public function show(Request $request)
    {
        Log::debug('------------- 月次集計開始 show in----------------');
        Log::debug('    パラメータ  $request->datefrom= '.$request->datefrom);
        Log::debug('    パラメータ  $request->displaykbn = '.$request->displaykbn);
        Log::debug('    パラメータ  $request->employmentstatus = '.$request->employmentstatus);
        Log::debug('    パラメータ  $request->departmentcode = '.$request->departmentcode);
        Log::debug('    パラメータ  userc$request->usercodeode = '.$request->usercode);

        $apicommon = new ApiCommonController();
        // reqestクエリーセット
        $datefrom = $apicommon->setRequestQeury($request->datefrom);
        $displaykbn = $apicommon->setRequestQeury($request->displaykbn);
        $employmentstatus = $apicommon->setRequestQeury($request->employmentstatus);
        $departmentcode = $apicommon->setRequestQeury($request->departmentcode);
        $usercode = $apicommon->setRequestQeury($request->usercode);

        // メッセージ設定collect
        $this->collect_massegedata = collect();

        $working_time_dates = array();
        $working_time_sum = array();
        $company_name = Config::get('const.MEMO_DATA.MEMO_DATA_015');

        $workingtimedate_model = new WorkingTimedate();
        // 日付開始終了の作成
        $chk_result = $this->makeDateFromTo($displaykbn,  $datefrom, $workingtimedate_model);

        if ($chk_result) {
            // パラメータのチェック
            $chk_result = $workingtimedate_model->chkWorkingTimeData();
            if ($chk_result) {
                // 労働時間の集計用パラメータはここで設定してしまう。日付はmakeDateFromToで設定済み
                $workingtimedate_model->setParamEmploymentStatusAttribute($employmentstatus);
                $workingtimedate_model->setParamDepartmentcodeAttribute($departmentcode);
                $workingtimedate_model->setParamUsercodeAttribute($usercode);
                // 労働時間の集計
                $working_time_dates = $this->calctWorkingTime($workingtimedate_model);
                $workingtimedate_model->setParamEmploymentStatusAttribute($employmentstatus);
                $workingtimedate_model->setParamDepartmentcodeAttribute($departmentcode);
                $workingtimedate_model->setParamUsercodeAttribute($usercode);
                $working_time_sum = $workingtimedate_model->getWorkingTimeDateTimeSum(Config::get('const.WORKINGTIME_DAY_OR_MONTH.monthly_basic'));
                // 会社名を取得
                $company_model = new Company();
                $company_model->setApplytermfromAttribute($workingtimedate_model->getParamdatefromAttribute());
                $companys = $company_model->getCompanyInfoApply();
                foreach ($companys as $company_result) {
                    $company_name = $company_result->name;
                    break;
                }
            $calc_result = true;
            } else {
                $calc_result = false;
            }
        } else {
            $calc_result = false;
        }

        // CSV出力用　null → ""
        foreach ($working_time_dates as $index1 => $date) {
            foreach ($date['date'] as $index2 => $record) {
                if(!isset($record['attendance1'])){
                   $working_time_dates[$index1]['date'][$index2]['attendance1'] = "";
                }
                if(!isset($record['attendance2'])){
                   $working_time_dates[$index1]['date'][$index2]['attendance2'] = "";
                }
                if(!isset($record['attendance3'])){
                   $working_time_dates[$index1]['date'][$index2]['attendance3'] = "";
                }
                if(!isset($record['attendance4'])){
                   $working_time_dates[$index1]['date'][$index2]['attendance4'] = "";
                }
                if(!isset($record['attendance5'])){
                   $working_time_dates[$index1]['date'][$index2]['attendance5'] = "";
                }
                if(!isset($record['leaving1'])){
                   $working_time_dates[$index1]['date'][$index2]['leaving1'] = "";
                }
                if(!isset($record['leaving2'])){
                   $working_time_dates[$index1]['date'][$index2]['leaving2'] = "";
                }
                if(!isset($record['leaving3'])){
                   $working_time_dates[$index1]['date'][$index2]['leaving3'] = "";
                }
                if(!isset($record['leaving4'])){
                   $working_time_dates[$index1]['date'][$index2]['leaving4'] = "";
                }
                if(!isset($record['leaving5'])){
                   $working_time_dates[$index1]['date'][$index2]['leaving5'] = "";
                }
                if(!isset($record['attendance1'])){
                    if(!isset($record['total_working_times'])){
                        $working_time_dates[$index1]['date'][$index2]['total_working_times'] = "";
                    }
                }
                if(!isset($record['attendance1'])){
                    if(!isset($record['regular_working_times'])){
                        $working_time_dates[$index1]['date'][$index2]['regular_working_times'] = "";
                    }
                }
                if(!isset($record['attendance1'])){
                    if(!isset($record['off_hours_working_hours'])){
                        $working_time_dates[$index1]['date'][$index2]['off_hours_working_hours'] = "";
                    }
                }
                if(!isset($record['attendance1'])){
                    if(!isset($record['late_night_overtime_hours'])){
                        $working_time_dates[$index1]['date'][$index2]['late_night_overtime_hours'] = "";
                    }
                }
            }
        }
        
        if (count($working_time_dates) == 0 ) {
            $this->array_messagedata[] =  array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.not_workintime'));
        }
        Log::debug('  結果 working_time_dates count = '.count($working_time_dates));
        Log::debug('  結果 working_time_sum count = '.count($working_time_sum));
        Log::debug('  結果 $this->array_messagedata count = '.count($this->array_messagedata));
        return response()->json(
            ['calcresults' => $working_time_dates, 'sumresults' => $working_time_sum, 'company_name' => $company_name,
            Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
        );
    }


    /**
     * 最新更新集計処理
     *
     * @return void
     */
    public function calc(Request $request)
    {
        Log::debug('--------------- 最新更新集計 開始 monthly calc in --------------------');

        $calc_result = true;
        $working_time_dates = array();
        $working_time_sum = array();
        $company_name = '';

        $apicommon = new ApiCommonController();

        // reqestクエリーセット
        $datefrom = $apicommon->setRequestQeury($request->datefrom);
        $displaykbn = $apicommon->setRequestQeury($request->displaykbn);
        $employmentstatus = $apicommon->setRequestQeury($request->employmentstatus);
        $departmentcode = $apicommon->setRequestQeury($request->departmentcode);
        $usercode = $apicommon->setRequestQeury($request->usercode);
        Log::debug('$datefrom = '.$datefrom);
        Log::debug('$displaykbn = '.$displaykbn);
        Log::debug('$employmentstatus = '.$employmentstatus);
        Log::debug('$departmentcode = '.$departmentcode);
        Log::debug('$usercode = '.$usercode);

        // メッセージ設定collect
        $this->collect_massegedata = collect();

        $workingtimedate_model = new WorkingTimedate();
        // 日付開始終了の作成
        $chk_result = $this->makeDateFromTo($displaykbn,  $datefrom, $workingtimedate_model);
        $datefrom = $workingtimedate_model->getParamdatefromAttribute();
        $dateto = $workingtimedate_model->getParamdatetoAttribute();
        $datefrom_date = new Carbon($datefrom);
        $dateto_date = new Carbon($dateto);

        $work_time = new WorkTime();
        // work_timeのパラメータのチェックを実施する
        $work_time->setParamDatefromAttribute($datefrom);
        $work_time->setParamDatetoAttribute($dateto);
        $work_time->setParamemploymentstatusAttribute($employmentstatus);
        $work_time->setParamDepartmentcodeAttribute($departmentcode);
        $work_time->setParamUsercodeAttribute($usercode);
        $chk_result = $work_time->chkWorkingTimeData();
        if ($chk_result) {
            Log::debug('work_timeのパラメータのチェック OK ');
            // 日次集計の計算を呼ぶ
            $daily_controller = new DailyWorkingInformationController();
            $calc_date = $datefrom_date;
            Log::debug('first $calc_date = '.$calc_date);
            while (true) {
                Log::debug('$calc_date = '.$calc_date);
                Log::debug('$dateto_date = '.$dateto_date);
                if ($calc_date > $dateto_date) { break; }
                // 打刻時刻を取得
                $work_time->setParamDatefromAttribute($calc_date);
                $work_time->setParamDatetoAttribute($calc_date);
                $work_time->setParamemploymentstatusAttribute($employmentstatus);
                $work_time->setParamDepartmentcodeAttribute($departmentcode);
                $work_time->setParamUsercodeAttribute($usercode);
                // 休日判定
                $business_kubun = $apicommon->jdgBusinessKbn($calc_date);
                $calc_result = $daily_controller->addDailyCalc(
                    $work_time,
                    $calc_date,
                    $calc_date,
                    $employmentstatus,
                    $departmentcode,
                    $usercode,
                    $business_kubun
                );
                $calc_date = $calc_date->addDay(1);
                Log::debug('$calc_date = '.$calc_date);
            }
        } else {
            $calc_result = false;
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
     * 日付開始終了作成
     *      表示区分により日付開始終了作成する
     *      作成した日付はWorkingTimedateモデルのparam_date_from、param_date_toに設定
     *      
     * @return void
     */
    public function makeDateFromTo($displayKbn, $dateYm, $workingtimedate_model)
    {
        Log::debug('makeDateFromTo in $displayKbn = '.$displayKbn);
        Log::debug('makeDateFromTo in $dateYm = '.$dateYm);

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

        Log::debug('makeDateFromTo end $make_fromdate = '.$make_fromdate);
        Log::debug('makeDateFromTo end $make_todate = '.$make_todate);

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
        Log::debug('----------- calctWorkingTime in --------------');
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
        $workingtimedates = $workingtimedate_model->getWorkingTimeDateTimeFormat(Config::get('const.WORKINGTIME_DAY_OR_MONTH.monthly_basic'), '');

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
                    Log::debug('同じキー $current_user_code = '.$current_user_code);
                    Log::debug('同じキー $current_date = '.$current_date);
                    // array_working_dateの設定
                    $this->setArrayDate($current_result);
                } elseif ($current_employment_status == $before_employment_status &&
                    $current_department_code == $before_department_code) {
                    // ユーザーが変わった場合
                    Log::DEBUG('user break ');
                    Log::debug('ユーザーが変わった $before_user_code = '.$before_user_code);
                    Log::debug('ユーザーが変わった $before_date = '.$before_date);
                    // 個人合計労働時間の集計を取得する
                    // 労働時間の集計用パラメータは個人の情報に設定する。日付はmakeDateFromToで設定済み
                    $workingtimedate_model->setParamEmploymentStatusAttribute($before_employment_status);
                    $workingtimedate_model->setParamDepartmentcodeAttribute($before_department_code);
                    $workingtimedate_model->setParamUsercodeAttribute($before_user_code);
                    $working_time_sum = $workingtimedate_model->getWorkingTimeDateTimeSum(Config::get('const.WORKINGTIME_DAY_OR_MONTH.monthly_basic'));
                    Log::debug('ユーザーが変わった $working_time_sum = '.count($working_time_sum));
                    $this->setArrayUser($before_result, $working_time_sum);
                    Log::debug('ユーザーが変わった $array_date = '.count($this->array_date));
                    Log::debug('ユーザーが変わった $array_user = '.count($this->array_user));
                    // 次用に配列クリア
                    $this->array_date = array();
                    // 同じ値にする
                    $before_employment_status = $current_employment_status;
                    $before_department_code = $current_department_code;
                    $before_user_code = $current_user_code;
                    $before_result = $result;
                    $this->setArrayDate($result);
                } elseif ($current_employment_status == $before_employment_status) {
                    // 部署が変わった場合
                    Log::DEBUG('department break ');
                    Log::debug('部署が変わった $before_user_code = '.$before_user_code);
                    Log::debug('部署が変わった $$before_result->user_name = '.$before_result->user_name);
                    // 個人合計労働時間の集計を取得する
                    // 労働時間の集計用パラメータは個人の情報に設定する。日付はmakeDateFromToで設定済み
                    $workingtimedate_model->setParamEmploymentStatusAttribute($before_employment_status);
                    $workingtimedate_model->setParamDepartmentcodeAttribute($before_department_code);
                    $workingtimedate_model->setParamUsercodeAttribute($before_user_code);
                    $working_time_sum = $workingtimedate_model->getWorkingTimeDateTimeSum(Config::get('const.WORKINGTIME_DAY_OR_MONTH.monthly_basic'));
                    $this->setArrayUser($before_result, $working_time_sum);
                    Log::debug('部署が変わった $array_date = '.count($this->array_date));
                    Log::debug('部署が変わった $array_user = '.count($this->array_user));
                    // 次用に配列クリア
                    $this->array_date = array();
                    // 同じ値にする
                    $before_employment_status = $current_employment_status;
                    $before_department_code = $current_department_code;
                    $before_result = $result;
                    // array_working_dateの設定
                    $this->setArrayDate($result);
                } else {
                    // 勤務形態が変わった場合
                    Log::DEBUG('employment_status break ');
                    Log::debug('勤務形態が変わった $before_user_code = '.$before_user_code);
                    Log::debug('勤務形態が変わった $$before_result->user_name = '.$before_result->user_name);
                    // 個人合計労働時間の集計を取得する
                    // 労働時間の集計用パラメータは個人の情報に設定する。日付はmakeDateFromToで設定済み
                    $workingtimedate_model->setParamEmploymentStatusAttribute($before_employment_status);
                    $workingtimedate_model->setParamDepartmentcodeAttribute($before_department_code);
                    $workingtimedate_model->setParamUsercodeAttribute($before_user_code);
                    $working_time_sum = $workingtimedate_model->getWorkingTimeDateTimeSum(Config::get('const.WORKINGTIME_DAY_OR_MONTH.monthly_basic'));
                    $this->setArrayUser($before_result, $working_time_sum);
                    Log::debug('勤務形態が変わった $array_date = '.count($this->array_date));
                    Log::debug('勤務形態が変わった $array_user = '.count($this->array_user));
                    // 次用に配列クリア
                    $this->array_date = array();
                    // 同じ値にする
                    $before_employment_status = $current_employment_status;
                    $before_result = $result;
                    // array_working_dateの設定
                    $this->setArrayDate($result);
                }
            }
        } else {
            return $this->array_user;
        }

        if (count($this->array_date) > 0) {
            // 個人合計労働時間の集計を取得する
            // 労働時間の集計用パラメータは個人の情報に設定する。日付はmakeDateFromToで設定済み
            $workingtimedate_model->setParamEmploymentStatusAttribute($current_employment_status);
            $workingtimedate_model->setParamDepartmentcodeAttribute($current_department_code);
            $workingtimedate_model->setParamUsercodeAttribute($current_user_code);
            Log::debug('残り $current_employment_status = '.$current_employment_status);
            Log::debug('残り $current_department_code = '.$current_department_code);
            Log::debug('残り $current_user_code = '.$current_user_code);
            $working_time_sum = $workingtimedate_model->getWorkingTimeDateTimeSum(Config::get('const.WORKINGTIME_DAY_OR_MONTH.monthly_basic'));
            $this->setArrayUser($before_result, $working_time_sum);
        }

        return $this->array_user;
    }

    /**
     * 集計配列日付セット
     *      
     * @return void
     */
    public function setArrayDate($result)
    {

        $datetime = new Carbon($result->working_date);
        $week = array("日", "月", "火", "水", "木", "金", "土");
        $w = (int)$datetime->format('w');
        $week_data = $week[$w];
        $remark_data = $result->remark_holiday_name;

        $this->array_date[] = array(
            'workingdate' => date_format($datetime, 'Y年m月d日').'（'.$week_data.'）',
            'attendance1' => $result->attendance_time_1,
            'attendance2' => $result->attendance_time_2,
            'attendance3' => $result->attendance_time_3,
            'attendance4' => $result->attendance_time_4,
            'attendance5' => $result->attendance_time_5,
            'leaving1' => $result->leaving_time_1,
            'leaving2' => $result->leaving_time_2,
            'leaving3' => $result->leaving_time_3,
            'leaving4' => $result->leaving_time_4,
            'leaving5' => $result->leaving_time_5,
            'total_working_times' => $result->total_working_times,
            'regular_working_times' => $result->regular_working_times,
            'off_hours_working_hours' => $result->off_hours_working_hours,
            'late_night_overtime_hours' => $result->late_night_overtime_hours,
            'remark_data' => $remark_data
        );
    }

    /**
     * 集計配列ユーザーセット
     *      
     * @return void
     */
    public function setArrayUser($result, $working_time_sum)
    {

        Log::debug('集計配列ユーザーセット $result->user_code = '.$result->user_code);
        Log::debug('集計配列ユーザーセット $result->user_name = '.$result->user_name);
        Log::debug('集計配列ユーザーセット $result->employment_status_name = '.$result->employment_status_name);
        Log::debug('集計配列ユーザーセット $result->department_name = '.$result->department_name);
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
                'legal_working_times' => $working_time_sum_result->legal_working_times,
                'out_of_legal_working_times' => $working_time_sum_result->out_of_legal_working_times,
                'not_employment_working_hours' => $working_time_sum_result->not_employment_working_hours,
                'off_hours_working_hours' => $working_time_sum_result->off_hours_working_hours,
                'public_going_out_hours' => $working_time_sum_result->public_going_out_hours,
                'missing_middle_hours' => $working_time_sum_result->missing_middle_hours,
                'total_working_status' => $working_time_sum_result->total_working_status,
                'total_go_out' => $working_time_sum_result->total_go_out,
                'total_paid_holidays' => $working_time_sum_result->total_paid_holidays,
                'total_holiday_kubun' => $working_time_sum_result->total_holiday_kubun,
                'total_leave_early' => $working_time_sum_result->total_leave_early,
                'total_late' => $working_time_sum_result->total_late,
                'total_absence' => $working_time_sum_result->total_absence,
                'date' => $this->array_date
            );
            break;
        }
    }

}
