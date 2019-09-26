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
use App\TempWorkingTimeDate;
use App\TempCalcWorkingTime;
use App\WorkingTimeTable;
use App\Http\Controllers\ApiCommonController;


class DailyWorkingInformationController extends Controller
{
    protected $table_working_timetables = 'working_timetables';

    // 打刻データ配列
    private $array_working_mode = array();
    private $array_working_datetime = array();
    private $array_timetable_from_time  = array();
    private $array_timetable_to_time = array();
    private $array_check_result = array();
    private $array_check_max_times = array();
    private $array_check_interval = array();
    // 計算用配列
    private $array_calc_mode = array();
    private $array_calc_time = array();
    private $array_calc_statu = array();
    private $array_calc_note = array();

    private $array_calc_late = array();
    private $array_calc_leave_early = array();
    private $array_calc_calc = array();
    private $array_calc_to_be_confirmed = array();
    private $array_calc_pattern = array();
    private $array_calc_check_result = array();
    private $array_calc_check_max_times = array();
    private $array_calc_check_interval = array();


    private $not_employment_working = 0;

    private $array_messagedata = array();

    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        return view('daily_working_information');
    }

    /**
     * 日次集計表示 
     *
     * @return void
     */
    public function show(Request $request){

        Log::debug('------------- 日次集計開始 show in----------------');
        Log::debug('    パラメータ  $request->datefrom= '.$request->datefrom);
        Log::debug('    パラメータ  $request->dateto = '.$request->dateto);
        Log::debug('    パラメータ  $request->employmentstatus = '.$request->employmentstatus);
        Log::debug('    パラメータ  $request->departmentcode = '.$request->departmentcode);
        Log::debug('    パラメータ  userc$request->usercodeode = '.$request->usercode);
        $calc_result = true;
        $add_result = true;
        // reqestクエリーセット
        $datefrom = null;
        if(isset($request->datefrom)){
            $datefrom = $request->datefrom;
        }
        $dateto = null;
        if(isset($request->dateto)){
            $dateto = $request->dateto;
        }
        $employmentstatus = null;
        if(isset($request->employmentstatus)){
            $employmentstatus = $request->employmentstatus;
        }
        $departmentcode = null;
        if(isset($request->departmentcode)){
            $departmentcode =$request->departmentcode;
        }
        $usercode = null;
        if(isset($request->usercode)){
            $usercode = $request->usercode;
        }

        $apicommon = new ApiCommonController();
        $work_time = new WorkTime();

        $business_kubun = "";
        $working_time_dates = null;
        $working_time_sum = null;

        // 打刻時刻を取得
        $work_time->setParamDatefromAttribute($datefrom);
        $work_time->setParamDatetoAttribute($dateto);
        $work_time->setParamemploymentstatusAttribute($employmentstatus);
        $work_time->setParamDepartmentcodeAttribute($departmentcode);
        $work_time->setParamUsercodeAttribute($usercode);
        // パラメータのチェック
        // datefromとdatetoがあるが、このメソッドではdatefrom=datetoであること
        $chk_work_time = $work_time->chkWorkingTimeData();
        if ($chk_work_time) {
            // 休日判定
            $business_kubun = $apicommon->jdgBusinessKbn($datefrom);
            if (!isset($business_kubun)) {
                $dt = date_format(new Carbon($datefrom), 'Y年m月d日');
                $this->array_messagedata[] = 
                    array( Config::get('const.RESPONCE_ITEM.message') => str_replace('{0}', $dt, Config::get('const.MSG_ERROR.not_setting_calendar')));
                Log::error(str_replace('{0}', $datefrom, Config::get('const.MSG_ERROR.not_setting_calendar')));
                $chk_work_time = false;
            }
        } else {
            $this->array_messagedata[] =  array( Config::get('const.RESPONCE_ITEM.message') => $work_time->getMassegedataAttribute());
        }
        if ($chk_work_time) {
            Log::debug('------------- パラメータのチェック OK  ----------------');
            // -------------- debug -------------- start --------
            if ($business_kubun == 1) {
                Log::debug('------------- 集計開始 日付 = '.$datefrom.' 出勤日　business_kubun = '.$business_kubun );
            } else if($business_kubun == 2) {
                Log::debug('------------- 集計開始 日付 = '.$datefrom.' 法定外休日　business_kubun = '.$business_kubun );
            } else {
                Log::debug('------------- 集計開始 日付 = '.$datefrom.' 法定休日　business_kubun = '.$business_kubun );
            }
            // -------------- debug -------------- end --------
            $addCalc = $this->addDailyCalc(
                $work_time,
                $datefrom,
                $dateto,
                $employmentstatus,
                $departmentcode,
                $usercode,
                $business_kubun);
            if ($addCalc) {
                $working_model = new WorkingTimedate();
                $working_model->setParamdatefromAttribute(date_format(new Carbon($datefrom), 'Ymd'));
                $working_model->setParamdatetoAttribute(date_format(new Carbon($dateto), 'Ymd'));
                $working_model->setParamEmploymentStatusAttribute($employmentstatus);
                $working_model->setParamDepartmentcodeAttribute($departmentcode);
                $working_model->setParamUsercodeAttribute($usercode);
                // 集計結果
                $working_time_dates = 
                    $working_model->getWorkingTimeDateTimeFormat(
                        Config::get('const.WORKINGTIME_DAY_OR_MONTH.daily_basic'), $business_kubun);
                // 合計結果
                if (count($working_time_dates) > 0) {
                    $working_time_sum = $working_model->getWorkingTimeDateTimeSum(Config::get('const.WORKINGTIME_DAY_OR_MONTH.daily_basic'));
                } else {
                    $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.not_workintime'));
                }
                Log::debug('------------- 集計終了 日付 = '.$datefrom.' business_kubun = '.$business_kubun );
            } else {
                $add_result = false;
            }
        } else {
            Log::debug('------------- パラメータのチェック NG  ----------------');
            $add_result = false;
        }

        Log::debug('    集計結果　$working_model = '.count($working_time_dates));
        Log::debug('    合計結果  $working_time_sum = '.count($working_time_sum));
        Log::debug('    メッセージ  $array_messagedata = '.count($this->array_messagedata));

        return response()->json(
            ['calcresults' => $working_time_dates,
                'sumresults' => $working_time_sum,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]);
    }

    /**
     * 日次集計表示 
     *
     * @return void
     */
    public function addDailyCalc($work_time, $datefrom, $dateto, $employmentstatus, $departmentcode, $usercode, $business_kubun) {
        Log::DEBUG('---------------------- addDailyCalc in ------------------------ '.$datefrom);

        $calc_result = true;
        $add_result = true;

        // 打刻時刻を取得
        $temp_working_model = new TempWorkingTimeDate();
        $apicommon = new ApiCommonController();
        // シフト打刻を取得するために$datetoの翌日をParamDatetoを再設定
        $nextdt =$apicommon->getNextDay($dateto, 'Y/m/d');
        // getWorkTimegetParamDatetoAttributeしている
        $work_time->setParamDatetoAttribute($nextdt);
        // 終了日付で検索（最新の情報として）
        $work_time_results = $work_time->getWorkTimes($datefrom, $dateto, $business_kubun);
        if(count($work_time_results) > 0){
            $temp_calc_model = new TempCalcWorkingTime();
            try{
                // temporary削除処理
                DB::beginTransaction();
                $temp_calc_model->delTempCalcWorkingtime();
                $temp_working_model->delTempWorkingTimeDate();
                try{
                    // 日次集計計算登録
                    Log::debug('---------------- 日次集計計算登録(temp_calc_workingtimes) start -----------------------');
                    $calc_result = $this->calcWorkingTimeDate($work_time_results, $work_time->getParamDatefromAttribute(), $business_kubun);
                    Log::debug('---------------- 日次集計計算登録(temp_calc_workingtimes) end -----------------------');
                    if ($calc_result) {
                        // タイムテーブルを取得
                        $timetable_model = new WorkingTimeTable();
                        $timetable_model->setParamdatefromAttribute($datefrom);
                        $timetable_model->setParamdatetoAttribute($dateto);
                        $timetable_model->setParamemploymentstatusAttribute($employmentstatus);
                        $timetable_model->setParamDepartmentcodeAttribute($departmentcode);
                        $timetables = $timetable_model->getWorkingTimeTableJoin();
                        if (count($timetables) > 0) {
                            Log::debug('---------------- 日次集計登録(temp_working_time_dates) start -----------------------');
                            Log::debug('タイムテーブル設定OK');
                            // 日次集計
                            $add_result = $this->calcTempWorkingTimeDate($timetables);
                            Log::debug('---------------- 日次集計登録(temp_working_time_dates) end -----------------------');
                        } else {
                            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.not_setting_timetable'));
                            Log::error(Config::get('const.LOG_MSG.not_setting_timetable'));
                            $add_result = false;
                        }
                    } else {
                        Log::debug('$calc_result = false');
                        $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.not_workintime'));
                        Log::error(Config::get('const.LOG_MSG.not_workintime'));
                        $add_result = false;
                    }
                    DB::commit();
                    Log::debug('temporary commit');
                }catch(\PDOException $pe){
                    DB::rollBack();
                    Log::debug('temporary rollBack');
                    $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_accesee_eror_dailycalc'));
                    Log::error(Config::get('const.MSG_ERROR.data_accesee_eror_dailycalc'));
                    Log::error($pe->getMessage());
                    $add_result = false;
                }catch(\Exception $e){
                    DB::rollBack();
                    Log::debug('temporary rollBack');
                    $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_error_dailycalc'));
                    Log::error(Config::get('const.LOG_MSG.data_error_dailycalc'));
                    Log::error($e->getMessage());
                    $add_result = false;
                }
            }catch(\PDOException $pe){
                DB::rollBack();
                Log::debug('temporary rollBack');
                $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_accesee_eror_dailycalc'));
                Log::error($pe->getMessage());
                $add_result = false;
            }
        } else {
            $add_result = false;
            Log::debug(Config::get('const.MSG_ERROR.not_workintime'));
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.not_workintime'));
        }

        // 出勤・退勤データtempから登録
        $working_time_dates = null;
        $working_time_sum = null;
        if ($add_result) {
            $temp_working_model->setParamdatefromAttribute(date_format(new Carbon($datefrom), 'Ymd'));
            $temp_working_model->setParamdatetoAttribute(date_format(new Carbon($dateto), 'Ymd'));
            $temp_working_model->setParamEmploymentStatusAttribute($employmentstatus);
            $temp_working_model->setParamDepartmentcodeAttribute($departmentcode);
            $temp_working_model->setParamUsercodeAttribute($usercode);
            try{
                Log::debug('getTempWorkingTimeDateUserJoin ');
                $temp_working_time_dates = $temp_working_model->getTempWorkingTimeDateUserJoin($dateto);
                if (count($temp_working_time_dates) > 0) {
                    Log::debug('isset $temp_working_time_dates true ');
                    $working_model = new WorkingTimedate();
                    $working_model->setParamdatefromAttribute(date_format(new Carbon($datefrom), 'Ymd'));
                    $working_model->setParamdatetoAttribute(date_format(new Carbon($dateto), 'Ymd'));
                    $working_model->setParamEmploymentStatusAttribute($employmentstatus);
                    $working_model->setParamDepartmentcodeAttribute($departmentcode);
                    $working_model->setParamUsercodeAttribute($usercode);
                    try{
                        DB::beginTransaction();
                        Log::debug(' calc beginTransaction ');
                        if ($working_model->isExistsWorkingTimeDate()) {
                            Log::debug(' $delWorkingTimeDate  ');
                            $working_model->delWorkingTimeDate();
                        };
                        Log::debug(' $insertWorkingTimeDateFromTemp  ');
                        $temp_array = array();
                        foreach($temp_working_time_dates as $working_time_date) {
                            $temp_collect = collect($working_time_date);
                            $temp_array[] = $temp_collect->toArray();
                        } 
                        $working_model->insertWorkingTimeDateFromTemp($temp_array);
                        DB::commit();
                        Log::debug(' calc commit ');
                    }catch(\PDOException $pe){
                        DB::rollBack();
                        Log::debug(' calc rollBack ');
                        $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_error_dailycalc'));
                    }catch(\Exception $e){
                        DB::rollBack();
                        Log::debug(' calc rollBack ');
                        $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_accesee_eror_dailycalc'));
                        $add_result = false;
                    }
                }
            }catch(\PDOException $pe){
                $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_error_dailycalc'));
                Log::error(Config::get('const.LOG_MSG.data_error_dailycalc'));
                Log::error($e->getMessage());
                $add_result = false;
            }catch(\Exception $e){
                $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_accesee_eror_dailycalc'));
                $add_result = false;
            }
        }

        return $add_result;
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
    private function calcWorkingTimeDate($worktimes, $target_date, $business_kubun){

        Log::DEBUG('---------------------- calcWorkingTimeDate in データ件数 = ------------------------ '.count($worktimes));
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
        // 打刻データ配列の初期化
        $this->iniArrayWorkingTime();
        $add_results = true;
        $noinput_user_cnt = 0;
        $target_flg = true;
        $before_out_flg = false;
        // ユーザー単位処理
        foreach ($worktimes as $result) {
            // 打刻データありの場合
            Log::DEBUG('----日次労働時間取得 code = '.$result->user_code.' '.$result->user_name. ' 開始   計算ターゲット日付'.$target_date_ymd.' ------------------------ ');
            Log::DEBUG('        部署     = '.$result->department_name);
            Log::DEBUG('        打刻時刻 = '.$result->record_datetime);
            Log::DEBUG('        モード   = '.$result->mode);
            Log::DEBUG('        打刻日   = '.$result->record_date);
            Log::DEBUG('        モード   = '.$result->mode);
            Log::DEBUG('        出勤区分 = '.$result->business_kubun);
            Log::DEBUG('                = '.$result->business_name);
            Log::DEBUG('        ユーザー休暇区分 = '.$result->user_holiday_kubun);
            Log::DEBUG('                        = '.$result->user_holiday_name);
            Log::DEBUG('        タイムテーブル　開始時刻　= '.$result->working_timetable_from_time);
            Log::DEBUG('        タイムテーブル　終了時刻　= '.$result->working_timetable_to_time);
            if ($result->record_datetime != null && $result->mode != null) {
                // 設定値確認
                $chk_setting = $this->chkSettingData($result);
                // 設定が正常である場合
                if ($chk_setting == 0)  {
                    Log::DEBUG('        設定値確認 OK');
                    // 翌日退勤した場合を考慮し、同日処理を行うようにするため、$current_dateは$target_date_ymdとする
                    // よって日付ブレーク処理は無意味となる
                    $current_date = $target_date_ymd;
                    $current_department_code = $result->department_code;
                    $current_user_code = $result->user_code;
                    $current_result = $result;
                    if ($before_date == null) {$before_date = $current_date;}
                    if ($before_department_code == null) {$before_department_code = $current_department_code;}
                    if ($before_user_code == null) {$before_user_code = $current_user_code;}
                    if ($before_result == null) {$before_result = $result;}
                    // 指定日付<=であれば集計対象、>であれば打刻なしとして登録
                    if ($target_flg) {
                        if ($result->record_date > $target_date_ymd &&
                            $result->mode == Config::get('const.C012.attendance')) {
                            $target_flg = false;
                        }
                    }
                    if ($target_flg == true) {
                        // ユーザー休暇区分判定用
                        $before_holiday_date = null;
                        $before_holiday_user_code = null;
                        $before_holiday_department_code = null;
                        $before_holiday_kubun = null;
                        $before_out_flg = true;
                        Log::DEBUG('    当日計算対象データ');
                        // 同じキーの場合
                        if ($current_date == $before_date &&
                            $current_department_code == $before_department_code &&
                            $current_user_code == $before_user_code) {
                            Log::DEBUG('    同じキーの場合 ');
                            // 打刻データ配列の設定
                            $this->pushArrayWorkingTime($result);
                        } elseif ($current_date == $before_date &&
                            $current_department_code == $before_department_code) {
                            // ユーザーが変わった場合
                            Log::DEBUG('    ユーザーが変わった場合 ');
                            // ユーザー労働時間計算(１個前のユーザーを計算する)
                            Log::DEBUG('    temp_calc_workingtimesの登録開始');
                            Log::DEBUG('        １個前のユーザー = '.$before_user_code.' record_time = '.$before_result->record_datetime);
                            $this->calcWorkingTime(
                                $before_date,
                                $before_user_code,
                                $before_department_code,
                                $business_kubun,
                                $before_result->interval,
                                $before_result->user_holiday_kubun);
                            try{
                                // temporaryに登録する
                                $this->insTempCalcItem($before_date, $before_result);
                            }catch(\PDOException $pe){
                                $add_results = false;
                                throw $pe;
                            }
                            Log::DEBUG('    temp_calc_workingtimesの登録終了');
                            // 次データ計算事前処理
                            // beforeArrayWorkingTimeは現データが有効の場合の事前処理
                            $this->beforeArrayWorkingTime($result);
                            // ユーザーを同じく設定
                            $before_user_code = $current_user_code;
                            $before_result = $result;
                            $before_out_flg = true;
                        } elseif ($current_date == $before_date) {
                            // 部署が変わった場合
                            Log::DEBUG('    部署が変わった場合 ');
                            // ユーザー労働時間計算(１個前のユーザーを計算する)
                            Log::DEBUG('    temp_calc_workingtimesの登録開始');
                            Log::DEBUG('        １個前のユーザー = '.$before_user_code.' record_time = '.$before_result->record_datetime);
                            $this->calcWorkingTime(
                                $before_date,
                                $before_user_code,
                                $before_department_code,
                                $business_kubun,
                                $before_result->interval,
                                $before_result->user_holiday_kubun);
                            try{
                                // temporaryに登録する
                                $this->insTempCalcItem($before_date, $before_result);
                            }catch(\PDOException $pe){
                                $add_results = false;
                                throw $pe;
                            }
                            Log::DEBUG('    temp_calc_workingtimesの登録終了');
                            // 次データ計算事前処理
                            // beforeArrayWorkingTimeは現データが有効の場合の事前処理
                            $this->beforeArrayWorkingTime($result);
                            // 部署を同じく設定
                            $before_department_code = $current_department_code;
                            // ユーザーを同じく設定
                            $before_user_code = $current_user_code;
                            $before_result = $result;
                            $before_out_flg = true;
                        } else {
                            // 日付が変わった場合
                            Log::DEBUG('    日付が変わった ');
                            try{
                                // ユーザー労働時間登録(１個前のユーザーを登録する)
                                Log::DEBUG('    １個前のユーザーを登録開始 $before_user_code = '.$before_user_code.' record_time = '.$before_result->record_datetime);
                                $add_results = $this->addWorkingTime(
                                    $before_date,
                                    $before_user_code,
                                    $before_department_code,
                                    $before_result,
                                    $business_kubun,
                                    $before_result->interval,
                                    $before_result->user_holiday_kubun);
                                Log::DEBUG('    １個前のユーザーを登録終了 $before_user_code = '.$before_user_code);
                                // 次データ計算事前処理
                                // beforeArrayWorkingTimeは現データが有効の場合の事前処理
                                $this->beforeArrayWorkingTime($result);
                                // 日付を同じく設定
                                $before_date = $current_date;
                                // 部署を同じく設定
                                $before_department_code = $current_department_code;
                                // ユーザーを同じく設定
                                $before_user_code = $current_user_code;
                                $before_result = $result;
                                $before_out_flg = true;
                            }catch(\PDOException $pe){
                                $add_results = false;
                                throw $pe;
                            }catch(\Exception $e){
                                $add_results = false;
                                throw $e;
                            }
                        }
                    } else {
                        Log::DEBUG('    当日計算対象外データ');
                        // 前のデータが打刻ありであれば計算する
                        $user_holiday_kubun = null;
                        $user_holiday_name = null;
                        $user_working_date = null;
                        if (count($this->array_working_mode) > 0) {
                            try{
                                Log::DEBUG('    １個前のユーザーを登録開始 $before_user_code = '.$before_user_code.' record_time = '.$before_result->record_datetime);
                                // ユーザー労働時間登録(１個前のユーザーを登録する)
                                $add_results = $this->addWorkingTime(
                                    $before_date,
                                    $before_user_code,
                                    $before_department_code,
                                    $before_result,
                                    $business_kubun,
                                    $before_result->interval,
                                    $before_result->user_holiday_kubun);
                                // 次データ計算事前処理(打刻ないデータはbeforeArrayWorkingTimeは使用しない)
                                Log::DEBUG('    １個前のユーザーを登録終了 $before_user_code = '.$before_user_code);
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
                            }catch(\Exception $e){
                                $add_results = false;
                            }
                        }
                        // 打刻ないデータはtempに出力
                        Log::DEBUG('        打刻ないデータはtempに出力 $current_date = '.$current_date);
                        Log::DEBUG('            $before_date = '.$before_date);
                        Log::DEBUG('            打刻時刻      = '.$result->record_datetime);
                        Log::DEBUG('            ユーザー休暇   = '.$user_holiday_kubun);
                        Log::DEBUG('            1件前出力      = '.$before_out_flg);
                        // 1件前の日付がnullである場合、いきなり対象日付がないということなので出力
                        //if (!isset($result->record_datetime) || isset($user_holiday_kubun)) {
                        if (!$before_out_flg || isset($user_holiday_kubun)) {
                            try{
                                // 同じキーの場合
                                if ($current_date == $before_date &&
                                    $current_department_code == $before_department_code &&
                                    $current_user_code == $before_user_code) {
                                    $noinput_user_cnt++;
                                } else {
                                    $noinput_user_cnt = 1;
                                }
                                if ($noinput_user_cnt == 1) {
                                    $ptn = 0;
                                } else {
                                    $ptn = 6;
                                }
                                if(isset($result->user_holiday_kubun)) { $user_holiday_kubun = $result->user_holiday_kubun; }
                                if(isset($result->user_holiday_name)) { $user_holiday_name = $result->user_holiday_name; }
                                if(isset($result->user_working_date)) { $user_working_date = $result->user_working_date; }
                                if ($before_holiday_department_code != $result->department_code ||
                                    $before_holiday_user_code != $result->user_code ||
                                    $before_holiday_date != $result->user_working_date ||
                                    $before_holiday_kubun != $user_holiday_kubun) {
                                    Log::DEBUG('    temp_calc_workingtimesの登録開始');
                                    $dt = date_format(new Carbon($target_date), 'Ymd');
                                    Log::DEBUG('            ターゲット日付 = '.$dt);
                                    Log::DEBUG('            ユーザー休暇   = '.$user_holiday_name);
                                    Log::DEBUG('            　　　　日付   = '.$user_working_date);
                                    $this->pushArrayCalc($this->setNoInputTimePtn($ptn, $user_holiday_name, $dt, $user_working_date));
                                    // temporaryに登録する
                                    Log::DEBUG('    temp_calc_workingtimesの登録開始');
                                    Log::DEBUG('        現ユーザー = '.$current_user_code.' record_time = '.$result->record_datetime);
                                    $this->insTempCalcItem($target_date, $result);
                                    Log::DEBUG('    temp_calc_workingtimesの登録終了');
                                }
                                // 日付とユーザー休暇区分を保存
                                $before_holiday_date = $result->user_working_date;
                                $before_holiday_user_code = $result->user_code;
                                $before_holiday_department_code = $result->department_code;
                                $before_holiday_kubun = $user_holiday_kubun;
                            }catch(\PDOException $pe){
                                $add_results = false;
                                throw $pe;
                            }
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
                        } else {
                            Log::DEBUG('        打刻ないデータはtempに出力しない '.$result->record_datetime);
                        }
                    }
                } else {
                    // 前のデータが打刻ありであれば計算する
                    $user_holiday_kubun = null;
                    $user_holiday_name = null;
                    $user_working_date = null;
                    if (count($this->array_working_mode) > 0) {
                        try{
                            Log::DEBUG('    １個前のユーザーを登録開始 $before_user_code = '.$before_user_code.' record_time = '.$before_result->record_datetime);
                            // ユーザー労働時間登録(１個前のユーザーを登録する)
                            $add_results = $this->addWorkingTime(
                                $before_date,
                                $before_user_code,
                                $before_department_code,
                                $before_result,
                                $business_kubun,
                                $before_result->interval,
                                $before_result->user_holiday_kubun);
                            Log::DEBUG('    １個前のユーザーを登録終了 $before_user_code = '.$before_user_code);
                            // 次データ計算事前処理(打刻ないデータはbeforeArrayWorkingTimeは使用しない)
                            $before_date = null;
                            $before_user_code = null;
                            $before_department_code = null;
                            $before_result = null;
                            // 次データ計算事前処理
                            // 打刻データ配列の初期化
                            $this->iniArrayWorkingTime();
                            // 計算用配列の初期化
                            $this->iniArrayCalc();
                        }catch(\PDOException $pe){
                            $add_results = false;
                        }catch(\Exception $e){
                            $add_results = false;
                        }
                    }
                    try{
                        Log::DEBUG('    temp_calc_workingtimesの登録開始');
                        Log::DEBUG('        現ユーザー = '.$result->user_code.' record_time = '.$result->record_datetime);
                        // temporaryに登録する
                        if(isset($result->user_holiday_kubun)) { $user_holiday_kubun = $result->user_holiday_kubun; }
                        if(isset($result->user_holiday_name)) { $user_holiday_name = $result->user_holiday_name; }
                        if(isset($result->user_working_date)) { $user_working_date = $result->user_working_date; }
                        if ($before_holiday_department_code != $result->department_code ||
                            $before_holiday_user_code != $result->user_code ||
                            $before_holiday_date != $result->user_working_date ||
                            $before_holiday_kubun != $user_holiday_kubun) {
                            Log::DEBUG('    temp_calc_workingtimesの登録開始');
                            $dt = date_format(new Carbon($target_date), 'Ymd');
                            Log::DEBUG('            ターゲット日付 = '.$target_date);
                            Log::DEBUG('            ユーザー休暇  ='.$user_holiday_name);
                            Log::DEBUG('        　　　　    日付  = '.$user_working_date);
                            $ptn = $chk_setting;
                            $this->pushArrayCalc($this->setNoInputTimePtn($ptn, $user_holiday_name, $dt, $user_working_date));
                            $this->insTempCalcItem($result->record_date, $result);
                            Log::DEBUG('    temp_calc_workingtimesの登録終了');
                        }
                        // 日付とユーザー休暇区分を保存
                        $before_holiday_date = $result->user_working_date;
                        $before_holiday_user_code = $result->user_code;
                        $before_holiday_department_code = $result->department_code;
                        $before_holiday_kubun = $user_holiday_kubun;
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
                Log::DEBUG('        打刻データなし ');
                // 前のデータが打刻ありであれば計算する
                $user_holiday_kubun = null;
                $user_holiday_name = null;
                $user_working_date = null;
                if (count($this->array_working_mode) > 0) {
                    try{
                        // ユーザー労働時間登録(１個前のユーザーを登録する)
                        Log::DEBUG('        １個前のユーザーを登録開始 $before_user_code = '.$before_user_code);
                        $add_results = $this->addWorkingTime(
                            $before_date,
                            $before_user_code,
                            $before_department_code,
                            $before_result,
                            $business_kubun,
                            $before_result->interval,
                            $before_result->user_holiday_kubun);
                        Log::DEBUG('        １個前のユーザーを登録終了 $before_user_code = '.$before_user_code);
                        // 次データ計算事前処理(打刻ないデータはbeforeArrayWorkingTimeは使用しない)
                        $before_date = null;
                        $before_user_code = null;
                        $before_department_code = null;
                        $before_result = null;
                        // 打刻データ配列の初期化
                        $this->iniArrayWorkingTime();
                        // 計算用配列の初期化
                        $this->iniArrayCalc();
                    }catch(\PDOException $pe){
                        $add_results = false;
                    }catch(\Exception $e){
                        $add_results = false;
                    }
                }
                // 打刻ないデータはtempに出力
                // ただし、日付とユーザー休暇区分が１件前と同じ場合は出力しない
                Log::DEBUG('        打刻ないデータ = '.$result->user_code.' record_time = '.$result->record_datetime.' before_out_flg = '.$before_out_flg);
                $temp_non_date_flg = false;
                if(isset($result->user_holiday_kubun)) { $user_holiday_kubun = $result->user_holiday_kubun; }
                if(isset($result->user_holiday_name)) { $user_holiday_name = $result->user_holiday_name; }
                if(isset($result->user_working_date)) { $user_working_date = $result->user_working_date; }
                if ($before_holiday_department_code != $result->department_code ||
                    $before_holiday_user_code != $result->user_code ||
                    $before_holiday_date != $result->user_working_date ||
                    $before_holiday_kubun != $user_holiday_kubun) {
                    $temp_non_date_flg = true;
                }
                // 1件前の日付がnullである場合、いきなり対象日付がないということなので出力
                if (!$before_out_flg) {
                    $temp_non_date_flg = true;
                }
                try{
                    if($temp_non_date_flg) {
                        Log::DEBUG('    temp_calc_workingtimesの登録開始');
                        $ptn = 0;
                        $dt = date_format(new Carbon($target_date), 'Ymd');
                        Log::DEBUG('            ターゲット日付 = '.$dt);
                        Log::DEBUG('            ユーザー休暇  = '.$user_holiday_name);
                        Log::DEBUG('            　　　　日付  = '.$user_working_date);
                        $this->pushArrayCalc($this->setNoInputTimePtn($ptn, $user_holiday_name, $dt, $user_working_date));
                        // temporaryに登録する
                        $this->insTempCalcItem($target_date, $result);
                        Log::DEBUG('    temp_calc_workingtimesの登録終了');
                    }
                    // 日付とユーザー休暇区分を保存
                    $before_holiday_date = $result->user_working_date;
                    $before_holiday_user_code = $result->user_code;
                    $before_holiday_department_code = $result->department_code;
                    $before_holiday_kubun = $user_holiday_kubun;
                    $before_out_flg = true;
                }catch(\PDOException $pe){
                    $add_results = false;
                    throw $pe;
                }
                // 次データ計算事前処理(打刻ないデータはbeforeArrayWorkingTimeは使用しない)
                $before_date = null;
                $before_user_code = null;
                $before_department_code = null;
                $before_result = null;
                // 次データ計算事前処理
                // 打刻データ配列の初期化
                $this->iniArrayWorkingTime();
                // 計算用配列の初期化
                $this->iniArrayCalc();
            }
        }

        if (count($this->array_working_mode) > 0) {
            try{
                Log::DEBUG('    最終残のユーザーを登録開始 $current_user_code = '.$current_user_code.' record_time = '.$current_result->record_datetime);
                // ユーザー労働時間登録(１個前のユーザーを登録する)
                $add_results = $this->addWorkingTime(
                    $current_date,
                    $current_user_code,
                    $current_department_code,
                    $current_result,
                    $business_kubun,
                    $current_result->interval,
                    $before_result->user_holiday_kubun);
                // 次データ計算事前処理
                // 打刻データ配列の初期化
                $this->iniArrayWorkingTime();
                // 計算用配列の初期化
                $this->iniArrayCalc();
                Log::DEBUG('    最終残のユーザーを登録終了 $current_user_code = '.$current_user_code);
            }catch(\PDOException $pe){
                $add_results = false;
            }catch(\Exception $e){
                $add_results = false;
            }
        }

        Log::DEBUG('---------------------- calcWorkingTimeDate end ------------------------ ');

        return $add_results;

    }

    /**
     * ユーザー労働時間登録
     *
     * @return 登録結果
     */
    private function addWorkingTime($target_date, $target_user_code, $target_department_code, $target_result, $business_kubun, $interval, $user_holiday_kubun)
    {
        Log::DEBUG('---------------------- addWorkingTime in ------------------------ ');
        Log::DEBUG('    temp_calc_workingtimesの登録開始');
        Log::DEBUG('        １個前のユーザー = '.$target_user_code.' record_time = '.$target_result->record_datetime);
        // ユーザー労働時間計算(１個前のユーザーを計算する)
        $this->calcWorkingTime(
            $target_date,
            $target_user_code,
            $target_department_code,
            $business_kubun,
            $interval,
            $user_holiday_kubun);
        // temporaryに登録する
        try{
            $this->insTempCalcItem($target_date, $target_result);
        }catch(\PDOException $pe){
            throw $pe;
        }
        Log::DEBUG('    temp_calc_workingtimesの登録終了');

        Log::DEBUG('---------------------- addWorkingTime end ------------------------ ');
        return true;

    }

    /**
     * ユーザー労働時間計算
     *
     * @return 労働時間計算結果
     */
    private function calcWorkingTime($target_date, $target_user_code, $target_department_code, $business_kubun, $interval, $user_holiday_kubun)
    {
        Log::DEBUG('---------------------- calcWorkingTime in ---$target_user_code = '.$target_user_code);
        $work_time = new WorkTime();
        $work_time->setParamDepartmentcodeAttribute($target_department_code);
        $work_time->setParamUsercodeAttribute($target_user_code);
        $work_time->setParamDatefromAttribute($target_date);
        $work_time->setParamDatetoAttribute($target_date);
        $collect_calc_times = collect();
        $attendance_time = null;
        $leaving_time = null;
        $missing_middle_time = null;
        $missing_middle_return_time = null;
        $public_going_out_time = null;
        $public_going_out_return_time = null;
        $working_status = null;
        // 事前にテーブル再取得（テーブル取得1日以前のMAX打刻時刻）しておく
        $before_value_mode = null;
        $before_value_datetime = null;
        $before_times = $work_time->getBeforeDailyMaxData();
        foreach ($before_times as $before_result) {
            // 打刻時刻の設定
            $before_value_mode = $before_result->mode;
            $before_value_datetime = $before_result->record_datetime;
            break;
        }
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
        $cnt = 0;
        // 前提 count($array_working_mode) = count($array_working_datetime)
        Log::DEBUG('    array_working_mode count = '.count($this->array_working_mode));
        for($i=0;$i<count($this->array_working_mode);$i++){
            $value_mode = $this->array_working_mode[$i];
            $value_record_datetime = $this->array_working_datetime[$i];
            $value_timetable_from_time = $this->array_timetable_from_time[$i];
            $value_timetable_to_time = $this->array_timetable_to_time[$i];
            $value_check_result = $this->array_check_result[$i];
            $value_check_max_times = $this->array_check_max_times[$i];
            $value_check_interval = $this->array_check_interval[$i];
            Log::DEBUG('    ユーザー労働時間計算 cnt = '.$cnt);
            Log::DEBUG('    ユーザー労働時間計算 value_mode = '.$value_mode);
            Log::DEBUG('    ユーザー労働時間計算 value_record_datetime = '.$value_record_datetime);
            Log::DEBUG('    ユーザー労働時間計算 value_timetable_from_time = '.$value_timetable_from_time);
            Log::DEBUG('    ユーザー労働時間計算 value_timetable_to_time = '.$value_timetable_to_time);
            Log::DEBUG('    ユーザー労働時間計算 value_check_result = '.$value_check_result);
            Log::DEBUG('    ユーザー労働時間計算 value_check_max_times = '.$value_check_max_times);
            Log::DEBUG('    ユーザー労働時間計算 value_check_interval = '.$value_check_interval);
            // 出勤打刻の場合
            if ($value_mode == Config::get('const.C005.attendance_time')) {
                $this->setAttendancetime(
                    $cnt,
                    $work_time,
                    $value_record_datetime,
                    $value_timetable_from_time,
                    $value_timetable_to_time,
                    $value_check_result,
                    $value_check_max_times,
                    $value_check_interval,
                    $before_value_mode,
                    $before_value_datetime,
                    $business_kubun,
                    $interval,
                    $user_holiday_kubun
                );
            } elseif ($value_mode == Config::get('const.C005.leaving_time')) {      // 退勤の場合
                $this->setLeavingtime(
                    $cnt,
                    $work_time,
                    $value_record_datetime,
                    $value_timetable_from_time,
                    $value_timetable_to_time,
                    $value_check_result,
                    $value_check_max_times,
                    $before_value_mode,
                    $before_value_datetime,
                    $business_kubun,
                    $user_holiday_kubun
                );
            } elseif ($value_mode == Config::get('const.C005.missing_middle_time')) {       // 私用外出の場合
                $this->setMissingMiddleTime(
                    $cnt,
                    $work_time,
                    $value_record_datetime,
                    $value_timetable_from_time,
                    $value_timetable_to_time,
                    $value_check_result,
                    $value_check_max_times,
                    $before_value_mode,
                    $before_value_datetime
                );
            } elseif ($value_mode == Config::get('const.C005.missing_middle_return_time')) {        // 私用外出戻りの場合
                $this->setMissingMiddleReturnTime(
                    $cnt,
                    $work_time,
                    $value_record_datetime,
                    $value_timetable_from_time,
                    $value_timetable_to_time,
                    $value_check_result,
                    $value_check_max_times,
                    $before_value_mode,
                    $before_value_datetime
                );
            } elseif ($value_mode == Config::get('const.C005.public_going_out_time')) {             // 公用外出の場合
                $this->setPubliGoingOutTime(
                    $cnt,
                    $work_time,
                    $value_record_datetime,
                    $value_timetable_from_time,
                    $value_timetable_to_time,
                    $value_check_result,
                    $value_check_max_times,
                    $before_value_mode,
                    $before_value_datetime
                );
            } elseif ($value_mode == Config::get('const.C005.public_going_out_return_time')) {      // 公用外出戻りの場合
                $this->setPublicGoingOutReturnTime(
                    $cnt,
                    $work_time,
                    $value_record_datetime,
                    $value_timetable_from_time,
                    $value_timetable_to_time,
                    $value_check_result,
                    $value_check_max_times,
                    $before_value_mode,
                    $before_value_datetime
                );
            }
            $before_value_mode = $value_mode;
            $before_value_datetime = $value_record_datetime;
            $cnt = $cnt + 1;
        }

        Log::DEBUG('---------------------- calcWorkingTime end ------------------------ ');
       
    }

    /**
     * 出勤打刻処理
     *
     * @param  打刻時刻のSEQ
     *         打刻時刻
     *         １個前のモード
     * @return void
     */
    private function setAttendancetime($cnt, $work_time,
        $value_record_datetime, $value_timetable_from_time, $value_timetable_to_time,
        $value_check_result, $value_check_max_times, $value_check_interval, $before_value_mode, $before_value_datetime, $business_kubun, $interval, $user_holiday_kubun)
    {

        Log::DEBUG('---------------------- setAttendancetime in ------------------------ ');
        Log::DEBUG('    出勤打刻処理 start');
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
        Log::DEBUG('    attendance_from_date  = '.$attendance_from_date);
        Log::DEBUG('    timetable_from_date  = '.$timetable_from_date);
        Log::DEBUG('    timetable_to_date  = '.$timetable_to_date);
        Log::DEBUG('    attendance_to_date  = '.$attendance_to_date);
        Log::DEBUG('    record_datetime  = '.$record_datetime);
        Log::DEBUG('    record_before_datetime = '.$record_before_datetime);
        Log::DEBUG('    before_value_mode = '.$before_value_mode);
        Log::DEBUG('    cnt  = '.$cnt);
        // パターン設定
        $ptn = null;

        // ---------------------出勤が最初の場合 -----------------------------------------------------------------------------------
        if ($cnt == 0) {
            if ($before_value_mode == Config::get('const.C005.attendance_time')) {          // １個前のモードが出勤である場合
                Log::DEBUG('    １個前のモードが出勤である');
                if ($record_before_datetime < $attendance_from_date) {                      // １個前の打刻時刻 < 出勤1日のはじめ
                    // パターン２（打刻ミス（出勤済）（要確認）。勤務状態は打刻なし）
                    $ptn = '2';
                }
            } elseif ($before_value_mode == Config::get('const.C005.leaving_time')) {       // １個前のモードが退勤である場合
                Log::DEBUG('    １個前のモードが退勤である');
                if ($record_datetime >= $attendance_from_date &&
                            $record_datetime < $timetable_from_date) {                      // 出勤1日のはじめ <= 打刻時刻 < タイムテーブルの始業時刻
                    if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                        // パターン１（正常出勤。勤務状態は出勤状態）
                        $ptn = '1';
                        // 退勤から出勤までのタイム差を取得しインターバルチェック
                        $value_check_interval = $apicommon->chkInteval($record_datetime, $record_before_datetime);
                    }
                } elseif ($record_datetime >= $timetable_from_date &&
                            $record_datetime < $timetable_to_date) {                        // タイムテーブルの始業時刻 <= 打刻時刻 < タイムテーブルの終業時刻
                    if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                        // パターン３（遅刻出勤（遅刻=1）。勤務状態は出勤状態）
                        $ptn = '3';
                        // 退勤から出勤までのタイム差を取得しインターバルチェック
                        $value_check_interval = $apicommon->chkInteval($record_datetime, $record_before_datetime);
                    }
                } elseif ($record_datetime >= $timetable_to_date &&
                            $record_datetime < $attendance_to_date) {                       // タイムテーブルの終業時刻 <= 打刻時刻 < 出勤1日の終わり
                    if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                        // パターン４（遅刻出勤（要確認）。勤務状態は出勤状態）
                        $ptn = '4';
                        // 退勤から出勤までのタイム差を取得しインターバルチェック
                        $value_check_interval = $apicommon->chkInteval($record_datetime, $record_before_datetime);
                    }
                } elseif ($record_datetime > $attendance_to_date) {                         // 出勤1日の終わり < 打刻時刻
                    if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                        // 不明データ
                        // 出勤打刻時刻が出勤1日の終わりより大きいことはない
                        $log_data = $work_time->getParamDatefromAttribute();
                        $log_data .= $work_time->getParamUsercodeAttribute();
                        $log_data .= $work_time->getParamDepartmentcodeAttribute();
                        Log::error(Config::get('const.MSG_ERROR.mismatch_data').' method = setAttendancetime1 '.$log_data);
                    }
                } else {
                    // 不明データ
                    $log_data = $work_time->getParamDatefromAttribute();
                    $log_data .= $work_time->getParamUsercodeAttribute();
                    $log_data .= $work_time->getParamDepartmentcodeAttribute();
                    Log::error(Config::get('const.MSG_ERROR.mismatch_data').' method = setAttendancetime2 '.$log_data);
                }
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_time')) {    // １個前のモードが私用または公用外出である場合
                Log::DEBUG('    １個前のモードが外出である');
                if ($record_before_datetime < $attendance_from_date) {                      // １個前の打刻時刻 < 出勤1日のはじめ
                    // パターン５（打刻ミス（外出戻りしていない）。勤務状態は打刻なし）
                    $ptn = '5';
                }
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_return_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_return_time')) {     // １個前のモードが戻り
                Log::DEBUG('    １個前のモードが外出戻りである');
                if ($record_before_datetime < $attendance_from_date) {                      // １個前の打刻時刻 < 出勤1日のはじめ
                    // パターン６（打刻ミス（退勤していない）。勤務状態は打刻なし）
                    $ptn = '6';
                }
            } else {                                                                        // １個前のモードがない
                Log::DEBUG('    １個前のモードがない ');
                if ($record_datetime >= $attendance_from_date &&
                    $record_datetime < $timetable_from_date) {                              // 出勤1日のはじめ <= 打刻時刻 < タイムテーブルの始業時刻
                    // パターン１（正常出勤。勤務状態は出勤状態）
                    $ptn = '1';
                } elseif ($record_datetime >= $timetable_from_date &&
                            $record_datetime < $timetable_to_date) {                        // タイムテーブルの始業時刻 <= 打刻時刻 < タイムテーブルの終業時刻
                    // パターン３（遅刻出勤（遅刻=1）。勤務状態は出勤状態）
                    $ptn = '3';
                } elseif ($record_datetime >= $timetable_to_date &&
                            $record_datetime < $attendance_to_date) {                       // タイムテーブルの終業時刻 <= 打刻時刻 < 出勤1日の終わり
                    // パターン４（遅刻出勤（要確認）。勤務状態は出勤状態）
                    $ptn = '4';
                } elseif ($record_datetime > $attendance_to_date) {                         // 出勤1日の終わり < 打刻時刻
                    // 不明データ
                    // 出勤打刻時刻が出勤1日の終わりより大きいことはない
                    $log_data = $work_time->getParamDatefromAttribute();
                    $log_data .= $work_time->getParamUsercodeAttribute();
                    $log_data .= $work_time->getParamDepartmentcodeAttribute();
                    Log::error(Config::get('const.MSG_ERROR.mismatch_data').' method = setAttendancetime3 '.$log_data);
                } else {
                    // 不明データ
                    $log_data = $work_time->getParamDatefromAttribute();
                    $log_data .= $work_time->getParamUsercodeAttribute();
                    $log_data .= $work_time->getParamDepartmentcodeAttribute();
                    Log::error(Config::get('const.MSG_ERROR.mismatch_data').' method = setAttendancetime4 '.$log_data);
                }
            }
        // ---------------------出勤が２番目以降の場合 -----------------------------------------------------------------------------------
        } else {
            if ($before_value_mode == Config::get('const.C005.attendance_time')) {          // １個前のモードが出勤
                if ($record_before_datetime >= $attendance_from_date &&
                            $record_before_datetime < $attendance_to_date) {                // 出勤1日のはじめ <= １個前の打刻時刻 < 出勤1日の終わり
                    // パターン６（打刻ミス（退勤していない）。勤務状態は打刻なし）
                    $ptn = '6';
                }
            } elseif ($before_value_mode == Config::get('const.C005.leaving_time')) {       // １個前のモードが退勤
                if ($record_datetime >= $attendance_from_date &&
                            $record_datetime < $timetable_from_date) {                      // 出勤1日のはじめ <= 打刻時刻 < タイムテーブルの始業時刻
                    if ($record_before_datetime >= $attendance_from_date &&
                                $record_before_datetime < $timetable_from_date) {           // 出勤1日のはじめ <= １個前の打刻時刻 < タイムテーブルの始業時刻
                        // パターン１（正常出勤。勤務状態は出勤状態）
                        $ptn = '1';
                        // 退勤から出勤までのタイム差を取得しインターバルチェック
                        $value_check_interval = $apicommon->chkInteval($record_datetime, $record_before_datetime);
                    }
                } elseif ($record_datetime >= $timetable_from_date &&
                                 $record_datetime < $timetable_to_date) {                   // タイムテーブルの始業時刻 <= 打刻時刻 < タイムテーブルの終業時刻
                    if ($record_before_datetime >= $attendance_from_date &&
                                     $record_before_datetime < $timetable_to_date) {        // 出勤1日のはじめ <= １個前の打刻時刻 < タイムテーブルの終業時刻
                        // パターン３（遅刻出勤（遅刻=1）。勤務状態は出勤状態）
                        $ptn = '3';
                        // 退勤から出勤までのタイム差を取得しインターバルチェック
                        $value_check_interval = $apicommon->chkInteval($record_datetime, $record_before_datetime);
                    }
                } elseif ($record_datetime >= $timetable_to_date &&
                                $record_datetime < $attendance_to_date) {                   // タイムテーブルの終業時刻 <= 打刻時刻 < 出勤1日の終わり
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
                    Log::error(Config::get('const.MSG_ERROR.mismatch_data').' method = setAttendancetime5 '.$log_data);
                } else {
                    // 不明データ
                    $log_data = $work_time->getParamDatefromAttribute();
                    $log_data .= $work_time->getParamUsercodeAttribute();
                    $log_data .= $work_time->getParamDepartmentcodeAttribute();
                    Log::error(Config::get('const.MSG_ERROR.mismatch_data').' method = setAttendancetime6 '.$log_data);
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
                            $record_datetime < $timetable_from_date) {                      // 出勤1日のはじめ <= 打刻時刻 < タイムテーブルの始業時刻
                    if ($record_before_datetime >= $attendance_from_date &&
                                $record_before_datetime < $timetable_from_date) {           // 出勤1日のはじめ <= １個前の打刻時刻 < タイムテーブルの始業時刻
                        // パターン１（正常出勤。勤務状態は出勤状態）
                        $ptn = '1';
                    }
                } elseif ($record_datetime >= $timetable_from_date &&
                                 $record_datetime < $timetable_to_date) {                   // タイムテーブルの始業時刻 <= 打刻時刻 < タイムテーブルの終業時刻
                    if ($record_before_datetime >= $attendance_from_date &&
                                     $record_before_datetime < $timetable_to_date) {        // 出勤1日のはじめ <= １個前の打刻時刻 < タイムテーブルの終業時刻
                        // パターン３（遅刻出勤（遅刻=1）。勤務状態は出勤状態）
                        $ptn = '3';
                    }
                } elseif ($record_datetime >= $timetable_to_date &&
                                $record_datetime < $attendance_to_date) {                   // タイムテーブルの終業時刻 <= 打刻時刻 < 出勤1日の終わり
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
                    Log::error(Config::get('const.MSG_ERROR.mismatch_data').' method = setAttendancetime7 '.$log_data);
                } else {
                    // 不明データ
                    $log_data = $work_time->getParamDatefromAttribute();
                    $log_data .= $work_time->getParamUsercodeAttribute();
                    $log_data .= $work_time->getParamDepartmentcodeAttribute();
                    Log::error(Config::get('const.MSG_ERROR.mismatch_data').' method = setAttendancetime8 '.$log_data);
                }
            }
        }

        if ($ptn != null) {
            $this->pushArrayCalc($this->setAttendanceCollectPtn(
                $ptn,
                $record_datetime,
                $value_check_result,
                $value_check_max_times,
                $value_check_interval,
                $business_kubun,
                $user_holiday_kubun));
        } else {
            // 不明データとして作成する
            $this->pushArrayCalc($this->setAttendanceCollectPtn(
                '',
                $record_datetime,
                $value_check_result,
                $value_check_max_times,
                $value_check_interval,
                $business_kubun,
                $user_holiday_kubun));
        }
        Log::DEBUG('---------------------- setAttendancetime end ------------------------ ');
            
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
    private function setAttendanceCollectPtn(
        $ptn, $record_datetime, $value_check_result, $value_check_max_times, $value_check_interval, $business_kubun, $user_holiday_kubun)
    {
        Log::DEBUG('---------------------- setAttendanceCollectPtn in -- 出勤 ptn = '.$ptn.' ---------------------- '.$record_datetime);
        $temp_calc_model = new TempCalcWorkingTime();

        if ($ptn == '1') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
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
        } elseif ($ptn == '2') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_001'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute($value_check_interval);
        } elseif ($ptn == '3') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.attendance'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            // 遅刻の設定値（休日は"0"）
            if (!isset($business_kubun)) { $business_kubun = Config::get('const.C007.basic'); }
            if (!isset($user_holiday_kubun)) { $user_holiday_kubun = Config::get('const.C013.non_set'); }
            if ($business_kubun == Config::get('const.C007.basic') && 
                ($user_holiday_kubun == Config::get('const.C013.non_set') ||
                $user_holiday_kubun == Config::get('const.C013.morning_off') ||
                $user_holiday_kubun == Config::get('const.C013.afternoon_off'))) {
                $temp_calc_model->setLateAttribute('1');
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
        } elseif ($ptn == '4') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.attendance'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_001'));
            // 遅刻の設定値（休日は"0"）
            if (!isset($business_kubun)) { $business_kubun = Config::get('const.C007.basic'); }
            if (!isset($user_holiday_kubun)) { $user_holiday_kubun = Config::get('const.C013.non_set'); }
            if ($business_kubun == Config::get('const.C007.basic') && 
                ($user_holiday_kubun == Config::get('const.C013.non_set') ||
                $user_holiday_kubun == Config::get('const.C013.morning_off') ||
                $user_holiday_kubun == Config::get('const.C013.afternoon_off'))) {
                $temp_calc_model->setLateAttribute('1');
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
        } elseif ($ptn == '5') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_003'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute($value_check_interval);
        } elseif ($ptn == '6') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_001'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute($value_check_interval);
        } else {
            // 不明データとして作成する
            $temp_calc_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.unknown'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_004'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute($value_check_interval);
        }

        Log::DEBUG('---------------------- setAttendanceCollectPtn end ------------------------ ');
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
    private function setLeavingtime($cnt, $work_time,
        $value_record_datetime, $value_timetable_from_time, $value_timetable_to_time,
        $value_check_result, $value_check_max_times, $before_value_mode, $before_value_datetime, $business_kubun, $user_holiday_kubun)
    {
        Log::DEBUG('---------------------- setLeavingtime in ------------------------ ');
        Log::DEBUG('    退勤打刻処理 start');
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
        Log::DEBUG('    attendance_from_date set = '.$attendance_from_date);
        Log::DEBUG('    timetable_from_date set = '.$timetable_from_date);
        Log::DEBUG('    timetable_to_date set = '.$timetable_to_date);
        Log::DEBUG('    attendance_to_date set = '.$attendance_to_date);
        Log::DEBUG('    record_datetime set = '.$record_datetime);
        Log::DEBUG('    record_before_datetime set = '.$record_before_datetime);
        Log::DEBUG('    before_value_mode set = '.$before_value_mode);
        Log::DEBUG('    before_value_datetime set = '.$before_value_datetime);
        Log::DEBUG('    cnt set = '.$cnt);
        // パターン設定
        $ptn = null;

        // ---------------------退勤が最初の場合 -----------------------------------------------------------------------------------
        if ($cnt == 0) {
            if ($before_value_mode == Config::get('const.C005.attendance_time') ||
                $before_value_mode == Config::get('const.C005.missing_middle_return_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_return_time')) {     // １個前のモードが出勤または戻りである場合
                if ($record_datetime >= $attendance_from_date &&
                            $record_datetime < $timetable_from_date) {                      // 出勤1日のはじめ <= 打刻時刻 < タイムテーブルの始業時刻
                    if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                        // パターン１（正常退勤。勤務状態は退勤状態。当日時間計算なし。）
                        $ptn = '1';
                    }
                } elseif ($record_datetime >= $timetable_from_date &&
                            $record_datetime < $timetable_to_date) {                        // タイムテーブルの始業時刻 <= 打刻時刻 < タイムテーブルの終業時刻
                    if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                        // パターン２３４
                        $ptn = '2';
                        $this->pushArrayCalc(
                            $this->setLeavingCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times, $business_kubun, $user_holiday_kubun));
                        /*$ptn = '3';
                        $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times, $business_kubun));*/
                        // パターン4は下で設定
                        $ptn = '4';
                    }
                } elseif ($record_datetime >= $timetable_to_date &&
                            $record_datetime < $attendance_to_date) {                       // タイムテーブルの終業時刻 <= 打刻時刻 < 出勤1日の終わり
                    if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                        // パターン２３５
                        $ptn = '2';
                        $this->pushArrayCalc(
                            $this->setLeavingCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times, $business_kubun, $user_holiday_kubun));
                        /*$ptn = '3';
                        $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times, $business_kubun));*/
                        // パターン5は下で設定
                        $ptn = '5';
                    }
                } elseif ($record_datetime > $attendance_to_date) {                         // 出勤1日の終わり < 打刻時刻
                    if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                        // パターン２３５
                        $ptn = '2';
                        $this->pushArrayCalc(
                            $this->setLeavingCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times, $business_kubun, $user_holiday_kubun));
                        /*$ptn = '3';
                        $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times, $business_kubun));*/
                        // パターン5は下で設定
                        $ptn = '5';
                    }
                } else {
                    // 不明データとして作成する
                    $log_data = $work_time->getParamDatefromAttribute();
                    $log_data .= $work_time->getParamUsercodeAttribute();
                    $log_data .= $work_time->getParamDepartmentcodeAttribute();
                    Log::error(Config::get('const.MSG_ERROR.mismatch_data').' method = setLeavingtime1 '.$log_data);
                }
            } elseif ($before_value_mode == Config::get('const.C005.leaving_time')) {       // １個前のモードが退勤である場合
                if ($record_before_datetime < $attendance_from_date) {                      // １個前の打刻時刻 < 出勤1日のはじめ
                    // パターン６（打刻ミス（出勤していない）。勤務状態は打刻なし）
                    $ptn = '6';
                }
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_time')) {    // １個前のモードが外出である場合
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
                    $record_datetime < $timetable_from_date) {                              // 出勤1日のはじめ <= 打刻時刻 < タイムテーブルの始業時刻
                    if ($record_before_datetime >= $attendance_from_date &&
                        $record_before_datetime < $timetable_from_date) {                   // 出勤1日のはじめ <= １個前の打刻時刻 < タイムテーブルの始業時刻
                        // パターン４（早退（要確認）。勤務状態は退勤状態。当日計算。）
                        $ptn = '4';
                    }
                } elseif ($record_datetime >= $timetable_from_date &&
                            $record_datetime < $timetable_to_date) {                        // タイムテーブルの始業時刻 <= 打刻時刻 < タイムテーブルの終業時刻
                    if ($record_before_datetime >= $attendance_from_date &&
                        $record_before_datetime < $timetable_to_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻 < タイムテーブルの終業時刻
                        // パターン４（早退（要確認）。勤務状態は退勤状態。当日計算。）
                        $ptn = '4';
                    }
                } elseif ($record_datetime >= $timetable_to_date &&
                            $record_datetime < $attendance_to_date) {                       // タイムテーブルの終業時刻 <= 打刻時刻 < 出勤1日の終わり
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
            } elseif ($before_value_mode == Config::get('const.C005.leaving_time')) {       // １個前のモードが退勤
                if ($record_before_datetime >= $attendance_from_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻
                    // パターン６（打刻ミス（出勤していない）。勤務状態は打刻なし）
                    $ptn = '6';
                }
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_time')) {    // １個前のモードが外出である場合
                if ($record_before_datetime >= $attendance_from_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻
                    // パターン７（打刻ミス（外出戻りしていない）。勤務状態は打刻なし）
                    $ptn = '7';
                }
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_return_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_return_time')) {     // １個前のモードが戻り
                if ($record_datetime >= $attendance_from_date &&
                            $record_datetime < $timetable_from_date) {                      // 出勤1日のはじめ <= 打刻時刻 < タイムテーブルの始業時刻
                    if ($record_before_datetime >= $attendance_from_date &&
                                $record_before_datetime < $timetable_from_date) {           // 出勤1日のはじめ <= １個前の打刻時刻 < タイムテーブルの始業時刻
                        // これより前の出勤打刻履歴を調査
                        if ($cnt > 1) {
                            for($i=$cnt-2;$i<count($this->array_working_mode);$i--){
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
                                 $record_datetime < $timetable_to_date) {                   // タイムテーブルの始業時刻 <= 打刻時刻 < タイムテーブルの終業時刻
                    if ($record_before_datetime >= $attendance_from_date &&
                                     $record_before_datetime < $timetable_to_date) {        // 出勤1日のはじめ <= １個前の打刻時刻 < タイムテーブルの終業時刻
                        // これより前の出勤打刻履歴を調査
                        if ($cnt > 1) {
                            for($i=$cnt-2;$i<count($this->array_working_mode);$i--){
                                if ($this->array_working_mode[$i] == Config::get('const.C005.attendance_time')) {     // ２個前モードが出勤
                                    if ($this->array_working_datetime[$i] < $attendance_from_date) {     // ２個前の出勤時刻 < 出勤1日のはじめ
                                        // パターン２３４
                                        $ptn = '2';
                                        $this->pushArrayCalc(
                                            $this->setLeavingCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times, $business_kubun, $user_holiday_kubun));
                                        /*$ptn = '3';
                                        $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times, $business_kubun));*/
                                        // パターン4は下で設定
                                        $ptn = '4';
                                    } else {
                                        // パターン２３５
                                        $ptn = '2';
                                        $this->pushArrayCalc(
                                            $this->setLeavingCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times, $business_kubun, $user_holiday_kubun));
                                        /*$ptn = '3';
                                        $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times, $business_kubun));*/
                                        // パターン5は下で設定
                                        $ptn = '5';
                                    }
                                    break;
                                }
                            }
                        } else {
                            // パターン２３４
                            $ptn = '2';
                            $this->pushArrayCalc(
                                $this->setLeavingCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times, $business_kubun, $user_holiday_kubun));
                            /*$ptn = '3';
                            $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times, $business_kubun));*/
                            // パターン4は下で設定
                            $ptn = '4';
                        }
                    }
                } elseif ($record_datetime >= $timetable_to_date &&
                                $record_datetime < $attendance_to_date) {                   // タイムテーブルの終業時刻 <= 打刻時刻 < 出勤1日の終わり
                    if ($record_before_datetime >= $attendance_from_date &&
                                    $record_before_datetime < $attendance_to_date) {        // 出勤1日のはじめ <= １個前の打刻時刻 < 出勤1日の終わり
                        // これより前の出勤打刻履歴を調査
                        if ($cnt > 1) {
                            for($i=$cnt-2;$i<count($this->array_working_mode);$i--){
                                if ($this->array_working_mode[$i] == Config::get('const.C005.attendance_time')) {     // ２個前モードが出勤
                                    if ($this->array_working_datetime[$i] < $attendance_from_date) {     // ２個前の出勤時刻 < 出勤1日のはじめ
                                        // パターン２３５
                                        $ptn = '2';
                                        $this->pushArrayCalc(
                                            $this->setLeavingCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times, $business_kubun, $user_holiday_kubun));
                                        /*$ptn = '3';
                                        $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times, $business_kubun));*/
                                        // パターン5は下で設定
                                        $ptn = '5';
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
                            $this->pushArrayCalc(
                                $this->setLeavingCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times, $business_kubun, $user_holiday_kubun));
                            /*$ptn = '3';
                            $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times, $business_kubun));*/
                            // パターン5は下で設定
                            $ptn = '5';
                        }
                    }
                } elseif ($record_datetime > $attendance_to_date) {
                    // これより前の出勤打刻履歴を調査
                    if ($cnt > 1) {
                        for($i=$cnt-2;$i<count($this->array_working_mode);$i--){
                            if ($this->array_working_mode[$i] == Config::get('const.C005.attendance_time')) {     // ２個前モードが出勤
                                if ($this->array_working_datetime[$i] < $attendance_from_date) {     // ２個前の出勤時刻 < 出勤1日のはじめ
                                    // パターン２３５
                                    $ptn = '2';
                                    $this->pushArrayCalc(
                                        $this->setLeavingCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times, $business_kubun, $user_holiday_kubun));
                                    /*$ptn = '3';
                                    $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times, $business_kubun));*/
                                    // パターン5は下で設定
                                    $ptn = '5';
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
                        $this->pushArrayCalc(
                            $this->setLeavingCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times, $business_kubun, $user_holiday_kubun));
                        /*$ptn = '3';
                        $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times, $business_kubun));*/
                        // パターン5は下で設定
                        $ptn = '5';
                    }
                } else {
                    // 不明データとして作成する
                    $log_data = $work_time->getParamDatefromAttribute();
                    $log_data .= $work_time->getParamUsercodeAttribute();
                    $log_data .= $work_time->getParamDepartmentcodeAttribute();
                    Log::error(Config::get('const.MSG_ERROR.mismatch_data').' method = setLeavingtime2 '.$log_data);
                }
            } else {                                                                        // １個前のモードがない
                // 不明データとして作成する
                $log_data = $work_time->getParamDatefromAttribute();
                $log_data .= $work_time->getParamUsercodeAttribute();
                $log_data .= $work_time->getParamDepartmentcodeAttribute();
                Log::error(Config::get('const.MSG_ERROR.mismatch_data').' method = setLeavingtime3 '.$log_data);
            }
        }

        if ($ptn != null) {
            $this->pushArrayCalc(
                $this->setLeavingCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times, $business_kubun, $user_holiday_kubun));
        } else {
            // 不明データとして作成する
            $this->pushArrayCalc(
                $this->setLeavingCollectPtn('', $record_datetime, $value_check_result, $value_check_max_times, $business_kubun, $user_holiday_kubun));
        }
        Log::DEBUG('退勤打刻処理 end');
        Log::DEBUG('---------------------- setLeavingtime end ------------------------ ');
            
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
    private function setLeavingCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times, $business_kubun, $user_holiday_kubun)
    {
        Log::DEBUG('---------------------- setLeavingCollectPtn in -- 退勤 ptn = '.$ptn.' ---------------------- '.$record_datetime);
        $temp_calc_model = new TempCalcWorkingTime();

        if ($ptn == '1') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.leaving'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
        } elseif ($ptn == '2') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.leaving'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
        } elseif ($ptn == '3') {
            // 自動設定はなしにする
            $temp_calc_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.attendance'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_006'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
        } elseif ($ptn == '4') {
            // なしにする
            $temp_calc_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.leaving'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('0');
            // 遅刻の設定値（休日は"0"）
            if (!isset($business_kubun)) { $business_kubun = Config::get('const.C007.basic'); }
            if (!isset($user_holiday_kubun)) { $user_holiday_kubun = Config::get('const.C013.non_set'); }
            if ($business_kubun == Config::get('const.C007.basic') && 
                ($user_holiday_kubun == Config::get('const.C013.non_set') ||
                $user_holiday_kubun == Config::get('const.C013.morning_off') ||
                $user_holiday_kubun == Config::get('const.C013.afternoon_off'))) {
                $temp_calc_model->setLeaveearlyAttribute('1');
            } else {
                $temp_calc_model->setLeaveearlyAttribute('0');
            }
            $temp_calc_model->setCurrentcalcAttribute('1');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
        } elseif ($ptn == '5') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.leaving'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('1');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
        } elseif ($ptn == '6') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
        } elseif ($ptn == '7') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_003'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
        } elseif ($ptn == '8') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.leaving'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('1');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
        } else {
            // 不明データとして作成する
            $temp_calc_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.unknown'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_004'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
        }
            
        Log::DEBUG('---------------------- setLeavingCollectPtn end ------------------------ ');
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
    private function setMissingMiddleTime($cnt, $work_time,
        $value_record_datetime, $value_timetable_from_time, $value_timetable_to_time,
        $value_check_result, $value_check_max_times, $before_value_mode, $before_value_datetime)
    {
        Log::DEBUG('---------------------- setMissingMiddleTime in ------------------------ ');
        Log::DEBUG('私用外出打刻処理');
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
        Log::DEBUG('attendance_from_date set = '.$attendance_from_date);
        Log::DEBUG('timetable_from_date set = '.$timetable_from_date);
        Log::DEBUG('timetable_to_date set = '.$timetable_to_date);
        Log::DEBUG('attendance_to_date set = '.$attendance_to_date);
        Log::DEBUG('record_datetime set = '.$record_datetime);
        Log::DEBUG('record_before_datetime set = '.$record_before_datetime);
        // パターン設定
        $ptn = null;

        // ---------------------私用外出が最初の場合 -----------------------------------------------------------------------------------
        if ($cnt == 0) {
            if ($before_value_mode == Config::get('const.C005.attendance_time') ||
                $before_value_mode == Config::get('const.C005.missing_middle_return_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_return_time')) {     // １個前のモードが出勤または戻りである場合
                if ($record_datetime >= $attendance_from_date &&
                            $record_datetime < $attendance_to_date) {                       // 出勤1日のはじめ <= 打刻時刻 < 出勤1日の終わり
                    if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                        // パターン１（私用外出。勤務状態は私用外出状態。）
                        $ptn = '1';
                    }
                }
            } elseif ($before_value_mode == Config::get('const.C005.leaving_time')) {       // １個前のモードが退勤である場合
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
                Log::error(Config::get('const.MSG_ERROR.mismatch_data').' method = setMissingMiddleTime1 '.$log_data);
            }
        // ---------------------私用外出が２番目以降の場合 -----------------------------------------------------------------------------------
        } else {
            if ($before_value_mode == Config::get('const.C005.attendance_time') ||
                $before_value_mode == Config::get('const.C005.missing_middle_return_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_return_time')) {     // １個前のモードが出勤または戻りである場合
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
                    Log::error(Config::get('const.MSG_ERROR.mismatch_data').' method = setMissingMiddleTime2 '.$log_data);
                }
            } elseif ($before_value_mode == Config::get('const.C005.leaving_time')) {       // １個前のモードが退勤
                if ($record_before_datetime >= $attendance_from_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻
                    // パターン２（打刻ミス（出勤していない）。勤務状態は打刻なし）
                    $ptn = '2';
                }
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_time')) {    // １個前のモードが私用外出
                if ($record_before_datetime >= $attendance_from_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻
                    // 不明データとして作成する
                    $collect_working_times = $this->setMissingmiddleCollectPtn('', $record_datetime, $value_check_result, $value_check_max_times);
                    return $collect_working_times;
                }
            } else {                                                                        // １個前のモードがない
                // 不明データとして作成する
                $log_data = $work_time->getParamDatefromAttribute();
                $log_data .= $work_time->getParamUsercodeAttribute();
                $log_data .= $work_time->getParamDepartmentcodeAttribute();
                Log::error(Config::get('const.MSG_ERROR.mismatch_data').' method = setMissingMiddleTime3 '.$log_data);
            }
        }

        if ($ptn != null) {
            $this->pushArrayCalc($this->setMissingmiddleCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times));
        } else {
            // 不明データとして作成する
            $this->pushArrayCalc($this->setMissingmiddleCollectPtn('', $record_datetime, $value_check_result, $value_check_max_times));
        }
        Log::DEBUG('---------------------- setMissingMiddleTime end ------------------------ ');
        Log::DEBUG('私用外出打刻処理 end');
            
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
    private function setMissingmiddleCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times)
    {
        Log::DEBUG('---------------------- setMissingmiddleCollectPtn in ------------------------ ');
        $temp_calc_model = new TempCalcWorkingTime();

        if ($ptn == '1') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.missing_middle_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.missing_middle'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('1');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
        } elseif ($ptn == '2') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.missing_middle_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_008'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
        } elseif ($ptn == '3') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.missing_middle_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_003'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
        } else {
            // 不明データとして作成する
            $temp_calc_model->setModeAttribute(Config::get('const.C005.missing_middle_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.unknown'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_004'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
        }

        Log::DEBUG('---------------------- setMissingmiddleCollectPtn end ------------------------ ');
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
    private function setMissingMiddleReturnTime($cnt, $work_time,
        $value_record_datetime, $value_timetable_from_time, $value_timetable_to_time,
        $value_check_result, $value_check_max_times, $before_value_mode, $before_value_datetime)
    {
        Log::DEBUG('---------------------- setMissingMiddleReturnTime in ------------------------ ');
        Log::DEBUG('私用外出戻り打刻処理');
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
        Log::DEBUG('attendance_from_date set = '.$attendance_from_date);
        Log::DEBUG('timetable_from_date set = '.$timetable_from_date);
        Log::DEBUG('timetable_to_date set = '.$timetable_to_date);
        Log::DEBUG('attendance_to_date set = '.$attendance_to_date);
        Log::DEBUG('record_datetime set = '.$record_datetime);
        Log::DEBUG('record_before_datetime set = '.$record_before_datetime);
        // パターン設定
        $ptn = null;

        // ---------------------私用外出戻りが最初の場合 -----------------------------------------------------------------------------------
        if ($cnt == 0) {
            if ($before_value_mode == Config::get('const.C005.attendance_time') ||
                $before_value_mode == Config::get('const.C005.leaving_time') ||
                $before_value_mode == Config::get('const.C005.missing_middle_return_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_return_time')) {     // １個前のモードが出勤または退勤または私用外出戻りである場合
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
                Log::error(Config::get('const.MSG_ERROR.mismatch_data').' method = setMissingMiddleReturnTime1 '.$log_data);
            }
        // ---------------------私用外出戻りが２番目以降の場合 -----------------------------------------------------------------------------------
        } else {
            if ($before_value_mode == Config::get('const.C005.attendance_time') ||
                $before_value_mode == Config::get('const.C005.leaving_time') ||
                $before_value_mode == Config::get('const.C005.missing_middle_return_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_return_time')) {     // １個前のモードが出勤または退勤または私用外出戻りである場合
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
                    Log::error(Config::get('const.MSG_ERROR.mismatch_data').' method = setMissingMiddleReturnTime2 '.$log_data);
                }
            } else {                                                                        // １個前のモードがない
                // 不明データとして作成する
                $log_data = $work_time->getParamDatefromAttribute();
                $log_data .= $work_time->getParamUsercodeAttribute();
                $log_data .= $work_time->getParamDepartmentcodeAttribute();
                Log::error(Config::get('const.MSG_ERROR.mismatch_data').' method = setMissingMiddleReturnTime3 '.$log_data);
            }
        }

        if ($ptn != null) {
            $this->pushArrayCalc($this->setMissingmiddleReturnCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times));
        } else {
            // 不明データとして作成する
            $this->pushArrayCalc($this->setMissingmiddleReturnCollectPtn('', $record_datetime, $value_check_result, $value_check_max_times));
        }
        Log::DEBUG('私用外出戻り打刻処理 end');
        Log::DEBUG('---------------------- setMissingMiddleReturnTime end ------------------------ ');
            
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
    private function setMissingmiddleReturnCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times)
    {
        Log::DEBUG('---------------------- setMissingmiddleReturnCollectPtn in ------------------------ ');
        $temp_calc_model = new TempCalcWorkingTime();

        if ($ptn == '1') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.missing_middle_return_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_009'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
        } elseif ($ptn == '2') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.missing_middle_return_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.missing_middle_return'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('1');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
        } else {
            // 不明データとして作成する
            $temp_calc_model->setModeAttribute(Config::get('const.C005.missing_middle_return_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.unknown'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_004'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
        }

        Log::DEBUG('---------------------- setMissingmiddleReturnCollectPtn end ------------------------ ');
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
    private function setPubliGoingOutTime($cnt, $work_time,
        $value_record_datetime, $value_timetable_from_time, $value_timetable_to_time,
        $value_check_result, $value_check_max_times, $before_value_mode, $before_value_datetime)
    {
        Log::DEBUG('---------------------- setPubliGoingOutTime in ------------------------ ');
        Log::DEBUG('公用外出打刻処理');
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
        Log::DEBUG('attendance_from_date set = '.$attendance_from_date);
        Log::DEBUG('timetable_from_date set = '.$timetable_from_date);
        Log::DEBUG('timetable_to_date set = '.$timetable_to_date);
        Log::DEBUG('attendance_to_date set = '.$attendance_to_date);
        Log::DEBUG('record_datetime set = '.$record_datetime);
        Log::DEBUG('record_before_datetime set = '.$record_before_datetime);
        // パターン設定
        $ptn = null;

        // ---------------------公用外出が最初の場合 -----------------------------------------------------------------------------------
        if ($cnt == 0) {
            if ($before_value_mode == Config::get('const.C005.attendance_time') ||
                $before_value_mode == Config::get('const.C005.missing_middle_return_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_return_time')) {     // １個前のモードが出勤または戻りである場合
                if ($record_datetime >= $attendance_from_date &&
                            $record_datetime < $attendance_to_date) {                       // 出勤1日のはじめ <= 打刻時刻 < 出勤1日の終わり
                    if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                        // パターン１（公用外出。勤務状態は公用外出状態。）
                        $ptn = '1';
                    }
                }
            } elseif ($before_value_mode == Config::get('const.C005.leaving_time')) {       // １個前のモードが退勤である場合
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
                Log::error(Config::get('const.MSG_ERROR.mismatch_data').' method = setPubliGoingOutTime1 '.$log_data);
            }
        // ---------------------公用外出が２番目以降の場合 -----------------------------------------------------------------------------------
        } else {
            if ($before_value_mode == Config::get('const.C005.attendance_time') ||
                $before_value_mode == Config::get('const.C005.missing_middle_return_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_return_time')) {     // １個前のモードが出勤または戻りである場合
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
                    Log::error(Config::get('const.MSG_ERROR.mismatch_data').' method = setPubliGoingOutTime2 '.$log_data);
                }
            } elseif ($before_value_mode == Config::get('const.C005.leaving_time')) {       // １個前のモードが退勤
                if ($record_before_datetime >= $attendance_from_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻
                    // パターン２（打刻ミス（出勤していない）。勤務状態は打刻なし）
                    $ptn = '2';
                }
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_time')) {    // １個前のモードが私用外出
                if ($record_before_datetime >= $attendance_from_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻
                    // 不明データとして作成する
                    $collect_working_times = $this->setPublicGoingOutCollectPtn('', $record_datetime, $value_check_result, $value_check_max_times);
                    return $collect_working_times;
                }
            } else {                                                                        // １個前のモードがない
                // 不明データとして作成する
                $log_data = $work_time->getParamDatefromAttribute();
                $log_data .= $work_time->getParamUsercodeAttribute();
                $log_data .= $work_time->getParamDepartmentcodeAttribute();
                Log::error(Config::get('const.MSG_ERROR.mismatch_data').' method = setPubliGoingOutTime3 '.$log_data);
            }
        }

        if ($ptn != null) {
            $this->pushArrayCalc($this->setPublicGoingOutCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times));
        } else {
            // 不明データとして作成する
            $this->pushArrayCalc($this->setPublicGoingOutCollectPtn('', $record_datetime, $value_check_result, $value_check_max_times));
        }

        Log::DEBUG('---------------------- setPubliGoingOutTime end ------------------------ ');
        Log::DEBUG('公用外出打刻処理 end');
            
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
    private function setPublicGoingOutCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times)
    {
        Log::DEBUG('---------------------- setPublicGoingOutCollectPtn in ------------------------ ');
        $temp_calc_model = new TempCalcWorkingTime();

        if ($ptn == '1') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.public_going_out_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.public_going_out'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('1');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
        } elseif ($ptn == '2') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.public_going_out_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_008'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
        } elseif ($ptn == '3') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.public_going_out_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_003'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
        } else {
            // 不明データとして作成する
            $temp_calc_model->setModeAttribute(Config::get('const.C005.public_going_out_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.unknown'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_004'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
        }

        Log::DEBUG('---------------------- setPublicGoingOutCollectPtn end ------------------------ ');
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
    private function setPublicGoingOutReturnTime($cnt, $work_time,
        $value_record_datetime, $value_timetable_from_time, $value_timetable_to_time,
        $value_check_result, $value_check_max_times, $before_value_mode, $before_value_datetime)
    {
        Log::DEBUG('---------------------- setPublicGoingOutReturnTime in ------------------------ ');
        Log::DEBUG('公用外出戻り打刻処理');
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
        Log::DEBUG('attendance_from_date set = '.$attendance_from_date);
        Log::DEBUG('timetable_from_date set = '.$timetable_from_date);
        Log::DEBUG('timetable_to_date set = '.$timetable_to_date);
        Log::DEBUG('attendance_to_date set = '.$attendance_to_date);
        Log::DEBUG('record_datetime set = '.$record_datetime);
        Log::DEBUG('record_before_datetime set = '.$record_before_datetime);
        // パターン設定
        $ptn = null;

        // ---------------------公用外出戻りが最初の場合 -----------------------------------------------------------------------------------
        if ($cnt == 0) {
            if ($before_value_mode == Config::get('const.C005.attendance_time') ||
                $before_value_mode == Config::get('const.C005.leaving_time') ||
                $before_value_mode == Config::get('const.C005.missing_middle_return_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_return_time')) {     // １個前のモードが出勤または退勤または外出戻りである場合
                if ($record_datetime >= $attendance_from_date &&
                            $record_datetime < $attendance_to_date) {                       // 出勤1日のはじめ <= 打刻時刻 < 出勤1日の終わり
                    if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                        // パターン１（打刻ミス（公用外出していない）。勤務状態は打刻なし）
                        $ptn = '1';
                        Log::DEBUG('a $ptn = 1');
                    }
                }
            } elseif ($before_value_mode == Config::get('const.C005.public_going_out_time')) {      // １個前のモードが公用外出である場合
                if ($record_before_datetime < $attendance_from_date) {                      // １個前の打刻時刻 < 出勤1日のはじめ
                    // パターン２（正常戻り。勤務状態は戻り状態。）
                    $ptn = '2';
                    Log::DEBUG('b $ptn = 2');
                }
            } else {                                                                        // １個前のモードがない
                Log::DEBUG('c $ptn = ');
                // 不明データとして作成する
                $log_data = $work_time->getParamDatefromAttribute();
                $log_data .= $work_time->getParamUsercodeAttribute();
                $log_data .= $work_time->getParamDepartmentcodeAttribute();
                Log::error(Config::get('const.MSG_ERROR.mismatch_data').' method = setPublicGoingReturnTime1 '.$log_data);
            }
        // ---------------------公用外出戻りが２番目以降の場合 -----------------------------------------------------------------------------------
        } else {
            if ($before_value_mode == Config::get('const.C005.attendance_time') ||
                $before_value_mode == Config::get('const.C005.leaving_time') ||
                $before_value_mode == Config::get('const.C005.missing_middle_return_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_return_time')) {     // １個前のモードが出勤または退勤または外出戻りである場合
                if ($record_datetime >= $attendance_from_date) {                            // 出勤1日のはじめ <= 打刻時刻
                    if ($record_before_datetime >= $attendance_from_date) {                 // 出勤1日のはじめ <= １個前の打刻時刻
                        // パターン１（打刻ミス（公用外出していない）。勤務状態は打刻なし）
                        $ptn = '1';
                        Log::DEBUG('d $ptn = 1');
                    }
                }
            } elseif ($before_value_mode == Config::get('const.C005.public_going_out_time')) {      // １個前のモードが公用外出
                if ($record_datetime >= $attendance_from_date &&
                    $record_datetime < $timetable_from_date) {                              // 出勤1日のはじめ <= 打刻時刻 < タイムテーブルの始業時刻
                    if ($record_before_datetime >= $attendance_from_date &&
                        $record_before_datetime < $timetable_from_date) {                   // 出勤1日のはじめ <= １個前の打刻時刻 < タイムテーブルの始業時刻
                        // パターン２（正常戻り。勤務状態は戻り状態。）
                        $ptn = '2';
                        Log::DEBUG('e $ptn = 2');
                    }
                } elseif ($record_datetime >= $timetable_from_date &&
                            $record_datetime < $timetable_to_date) {                        // タイムテーブルの始業時刻 <= 打刻時刻 < タイムテーブルの終業時刻
                    if ($record_before_datetime >= $attendance_from_date &&
                        $record_before_datetime < $timetable_to_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻 < タイムテーブルの終業時刻
                        // パターン２（正常戻り。勤務状態は戻り状態。）
                        $ptn = '2';
                        Log::DEBUG('f $ptn = 2');
                    }
                } elseif ($record_datetime >= $timetable_to_date &&
                            $record_datetime < $attendance_to_date) {                       // タイムテーブルの終業時刻 <= 打刻時刻 < 出勤1日の終わり
                    if ($record_before_datetime >= $attendance_from_date &&
                        $record_before_datetime < $timetable_to_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻 < 出勤1日の終わり
                        // パターン２（正常戻り。勤務状態は戻り状態。）
                        $ptn = '2';
                        Log::DEBUG('g $ptn = 2');
                    }
                } elseif ($record_datetime > $timetable_to_date) {                          // 打刻時刻 > 出勤1日の終わり
                    if ($record_before_datetime >= $attendance_from_date) {                 // 出勤1日のはじめ <= １個前の打刻時刻
                        // パターン２（正常戻り。勤務状態は戻り状態。）
                        $ptn = '2';
                        Log::DEBUG('h $ptn = 2');
                    }
                } else {                                                                    // １個前のモードがない
                    // 不明データとして作成する
                    $log_data = $work_time->getParamDatefromAttribute();
                    $log_data .= $work_time->getParamUsercodeAttribute();
                    $log_data .= $work_time->getParamDepartmentcodeAttribute();
                    Log::error(Config::get('const.MSG_ERROR.mismatch_data').' method = setPublicGoingReturnTime2 '.$log_data);
                }
            } else {                                                                        // １個前のモードがない
                // 不明データとして作成する
                $log_data = $work_time->getParamDatefromAttribute();
                $log_data .= $work_time->getParamUsercodeAttribute();
                $log_data .= $work_time->getParamDepartmentcodeAttribute();
                Log::error(Config::get('const.MSG_ERROR.mismatch_data').' method = setPublicGoingOutReturnTime3 '.$log_data);
            }
        }

        if ($ptn != null) {
            $this->pushArrayCalc($this->setPublicGoingOutReturnCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times));
        } else {
            Log::DEBUG('99 $ptn = ');
            // 不明データとして作成する
            $this->pushArrayCalc($this->setPublicGoingOutReturnCollectPtn('', $record_datetime, $value_check_result, $value_check_max_times));
        }
        Log::DEBUG('公用外出戻り打刻処理 end');
        Log::DEBUG('---------------------- setPublicGoingOutReturnTime end ------------------------ ');
            
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
    private function setPublicGoingOutReturnCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times)
    {
        Log::DEBUG('---------------------- setPublicGoingOutReturnCollectPtn in ------------------------ ');
        $temp_calc_model = new TempCalcWorkingTime();

        if ($ptn == '1') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.public_going_out_return_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_009'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
        } elseif ($ptn == '2') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.public_going_out_return_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.public_going_out_return'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('1');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
        } else {
            // 不明データとして作成する
            $temp_calc_model->setModeAttribute(Config::get('const.C005.public_going_out_return_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.unknown'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_004'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute($value_check_result);
            $temp_calc_model->setCheckmaxtimesAttribute($value_check_max_times);
            $temp_calc_model->setCheckintervalAttribute(0);
        }

        Log::DEBUG('---------------------- setPublicGoingOutReturnCollectPtn end ------------------------ ');
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
    private function setNoInputTimePtn($ptn, $user_holiday_name, $target_date, $hpliday_date)
    {
        Log::DEBUG('---------------------- setNoInputTimePtn in ------------------------ ');
        $temp_calc_model = new TempCalcWorkingTime();
        Log::DEBUG('        パターン = '.$ptn);
        Log::DEBUG('        ユーザー休暇区分 = '.$user_holiday_name);
        Log::DEBUG('            ターゲット日付 = '.$target_date);
        Log::DEBUG('            ユーザー休暇日付 = '.$hpliday_date);

        if ($ptn == '0') {
            $temp_calc_model->setModeAttribute('0');
            $temp_calc_model->setRecorddatetimeAttribute('');
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
        } elseif ($ptn == '1') {    // 設定ミス
            $temp_calc_model->setModeAttribute('0');
            $temp_calc_model->setRecorddatetimeAttribute('');
            $temp_calc_model->setWorkingstatusAttribute('0');
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_010'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute(0);
            $temp_calc_model->setCheckmaxtimesAttribute(0);
        } elseif ($ptn == '2') {    // 設定ミス
            $temp_calc_model->setModeAttribute('0');
            $temp_calc_model->setRecorddatetimeAttribute('');
            $temp_calc_model->setWorkingstatusAttribute('0');
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_011'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute(0);
            $temp_calc_model->setCheckmaxtimesAttribute(0);
        } elseif ($ptn == '3') {    // 設定ミス
            $temp_calc_model->setModeAttribute('0');
            $temp_calc_model->setRecorddatetimeAttribute('');
            $temp_calc_model->setWorkingstatusAttribute('0');
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_012'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute(0);
            $temp_calc_model->setCheckmaxtimesAttribute(0);
        } elseif ($ptn == '4') {    // 設定ミス
            $temp_calc_model->setModeAttribute('0');
            $temp_calc_model->setRecorddatetimeAttribute('');
            $temp_calc_model->setWorkingstatusAttribute('0');
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_013'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute(0);
            $temp_calc_model->setCheckmaxtimesAttribute(0);
        } elseif ($ptn == '5') {    // 設定ミス
            $temp_calc_model->setModeAttribute('0');
            $temp_calc_model->setRecorddatetimeAttribute('');
            $temp_calc_model->setWorkingstatusAttribute('0');
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_014'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute(0);
            $temp_calc_model->setCheckmaxtimesAttribute(0);
        } elseif ($ptn == '6') {    // ptn=1のnoteなし
            $temp_calc_model->setModeAttribute('0');
            $temp_calc_model->setRecorddatetimeAttribute('');
            $temp_calc_model->setWorkingstatusAttribute('0');
            $temp_calc_model->setNoteAttribute('');
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute(0);
            $temp_calc_model->setCheckmaxtimesAttribute(0);
        } else {
            // 不明データとして作成する
            $temp_calc_model->setModeAttribute('0');
            $temp_calc_model->setRecorddatetimeAttribute('');
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.unknown'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_004'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
            $temp_calc_model->setCheckresultAttribute(0);
            $temp_calc_model->setCheckmaxtimesAttribute(0);
        }

        Log::DEBUG('---------------------- setNoInputTimePtn end ------------------------ ');
        return $temp_calc_model;
            
    }

    /**
     * 設定値確認
     *
     * @return チェック結果
     */
    private function chkSettingData($chkdata)
    {
        Log::DEBUG('---------------------- chkSettingData in ------------------------ ');
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

        Log::DEBUG('---------------------- chkSettingData end ------------------------ ');
        return $chk_setting;
            
    }

    /**
     * 次データ計算事前処理
     *
     * @return void
     */
    private function beforeArrayWorkingTime($result)
    {
        Log::DEBUG('---------------------- beforeArrayWorkingTime in ------------------------ ');
        // 打刻データ配列の初期化
        $this->iniArrayWorkingTime();
        // 打刻データ配列の設定
        $this->pushArrayWorkingTime($result);
        // 計算用配列の初期化
        $this->iniArrayCalc();
        Log::DEBUG('---------------------- beforeArrayWorkingTime end ------------------------ ');
    }

    /**
     * 打刻データ配列の初期化
     *
     * @return void
     */
    private function iniArrayWorkingTime()
    {
        Log::DEBUG('---------------------- iniArrayWorkingTime in ------------------------ ');
        // 打刻データ配列
        $this->array_messagedata = array();
        $this->array_working_mode = array();
        $this->array_working_datetime = array();
        $this->array_timetable_from_time = array();
        $this->array_timetable_to_time = array();
        $this->array_check_result = array();
        $this->array_check_max_times = array();
        $this->array_check_interval = array();
        Log::DEBUG('---------------------- iniArrayWorkingTime end ------------------------ ');
    }

    /**
     * 打刻データ配列の設定
     *
     * @return void
     */
    private function pushArrayWorkingTime($result)
    {
        Log::DEBUG('---------------------- pushArrayWorkingTime in ------------------------ ');
        // 打刻データ配列
        $this->array_working_mode[] = $result->mode;
        $this->array_working_datetime[] = $result->record_datetime;
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
        Log::DEBUG('---------------------- pushArrayWorkingTime end ------------------------ ');
    }

    /**
     * 計算用配列の初期化
     *
     * @return void
     */
    private function iniArrayCalc()
    {
        Log::DEBUG('---------------------- iniArrayCalc in ------------------------ ');
        // 計算用配列
        $this->array_calc_mode = array();
        $this->array_calc_time = array();
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
        Log::DEBUG('---------------------- iniArrayCalc end ------------------------ ');
    }

    /**
     * 計算用配列の設定
     *
     * @return void
     */
    private function pushArrayCalc($temp_calc_model)
    {
        Log::DEBUG('---------------------- pushArrayCalc in ------------------------ ');
        // 計算用配列配列
        $this->array_calc_mode[] = $temp_calc_model->getModeAttribute();
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
        Log::DEBUG('---------------------- pushArrayCalc end ------------------------ ');
    }

    /**
     * temp日次集計タイムレコードの登録
     *
     * @return void
     */
    private function insTempCalcItem($target_date, $result)
    {
        Log::DEBUG('---------------------- insTempCalcItem in ------------------------ ');
        $temp_calc_model = new TempCalcWorkingTime();
    
        // 計算用配列からtemporary項目を設定する
        $dt = new Carbon($target_date);
        $temp_calc_model->setWorkingdateAttribute(date_format($dt, 'Ymd'));
        $temp_calc_model->setEmploymentstatusAttribute($result->employment_status);
        $temp_calc_model->setDepartmentcodeAttribute($result->department_code);
        $temp_calc_model->setUsercodeAttribute($result->user_code);
        $temp_calc_model->setEmploymentstatusnameAttribute($result->employment_status_name);
        $temp_calc_model->setDepartmentnameAttribute($result->department_name);
        $temp_calc_model->setUsernameAttribute($result->user_name);
        $temp_calc_model->setWorkingtimetablenoAttribute($result->working_timetable_no);
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

        Log::DEBUG('count($this->array_calc_mode) = '.count($this->array_calc_mode));
        for($i=0;$i<count($this->array_calc_mode);$i++){
            Log::DEBUG('$result->holiday_kubun = '.$result->holiday_kubun);
            Log::DEBUG('$this->array_calc_time[$i] = '.$this->array_calc_time[$i]);
            if (isset($result->holiday_kubun)) {
                if ($result->holiday_kubun == Config::get('const.C013.morning_off') || $result->holiday_kubun == Config::get('const.C013.afternoon_off')) {
                    $temp_calc_model->setModeAttribute($this->array_calc_mode[$i]);
                    $temp_calc_model->setRecorddatetimeAttribute($this->array_calc_time[$i]);
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
                } elseif ($result->holiday_kubun == Config::get('const.C013.non_set')) {
                    $temp_calc_model->setModeAttribute($this->array_calc_mode[$i]);
                    $temp_calc_model->setRecorddatetimeAttribute($this->array_calc_time[$i]);
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
                } else {
                    $temp_calc_model->setModeAttribute(null);
                    $temp_calc_model->setRecorddatetimeAttribute(null);
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
                }
            } else {
                $temp_calc_model->setModeAttribute($this->array_calc_mode[$i]);
                $temp_calc_model->setRecorddatetimeAttribute($this->array_calc_time[$i]);
                if (isset($this->array_calc_time[$i]) && $this->array_calc_time[$i] != '') {
                    Log::DEBUG('isset($this->array_calc_time[$i]) = '.$this->array_calc_time[$i]);
                    $edt_calc_datetime = new Carbon($this->array_calc_time[$i]);
                    $temp_calc_model->setRecordyearAttribute($edt_calc_datetime->format('Y'));
                    $temp_calc_model->setRecordmonthAttribute($edt_calc_datetime->format('m'));
                    $temp_calc_model->setRecorddateAttribute($edt_calc_datetime->format('Ymd'));
                    $temp_calc_model->setRecordtimeAttribute($edt_calc_datetime->format('His'));
                } else {
                    Log::DEBUG('isset($this->array_calc_time[$i]) = null'.$this->array_calc_time[$i]);
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
            try{
                $temp_calc_model->insertTempCalcWorkingtime();
            }catch(\PDOException $pe){
                throw $pe;
            }
        }
        Log::DEBUG('---------------------- insTempCalcItem end ------------------------ ');
    }

    /**
     * 日次集計
     *
     *
     * @return 集計結果
     */
    private function calcTempWorkingTimeDate($timetables){

        Log::DEBUG('---------------------- calcTempWorkingTimeDate in ------------------------ ');
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
        $array_calc_time = array();
        for ($i=0;$i<count($array_working_time_kubun);$i++) {
            $array_calc_time[$i] = 0; 
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
        // データ登録用出勤・退勤の労働時刻数配列
        $array_add_attendance_time = array();
        $array_add_leaving_time = array();
        // データ登録用外出・戻りの労働時刻数配列
        $array_add_missing_middle_time = array();
        $array_add_missing_middle_return_time = array();
        $array_add_public_going_out_time = array();
        $array_add_public_going_out_return_time = array();
        // ユーザー休暇区分判定用
        $before_holiday_date = null;
        $before_holiday_user_code = null;
        $before_holiday_department_code = null;
        $before_holiday_kubun = null;
        $before_holiday_set = false;

        // ユーザー単位処理
        $temp_calc_model = new TempCalcWorkingTime();
        $worktimes = $temp_calc_model->getTempCalcWorkingtime();
        $add_result = true;
        if (count($worktimes) == 0) {
            return false;
        }
        $calc_nobreak_cnt = 0;
        foreach ($worktimes as $result) {
            // 現在の情報保存
            Log::DEBUG('日次集計 ユーザー  code = '.$result->user_code.' '.$result->user_name);
            Log::DEBUG('        部署 = '.$result->department_name);
            Log::DEBUG('        打刻日 = '.$result->working_date);
            Log::DEBUG('        モード = '.$result->mode);
            Log::DEBUG('        打刻日時刻 = '.$result->record_datetime);
            Log::DEBUG('        打刻日 = '.$result->record_date);
            Log::DEBUG('        打刻時刻 = '.$result->record_time);
            Log::DEBUG('        ノート = '.$result->note);
            Log::DEBUG('        遅刻 = '.$result->late);
            Log::DEBUG('        早退 = '.$result->leave_early);
            Log::DEBUG('        出勤日 = '.$result->business_kubun);
            Log::DEBUG('        　　　 = '.$result->business_name);
            Log::DEBUG('        休暇 = '.$result->holiday_kubun);
            Log::DEBUG('        　　　= '.$result->holiday_name);
            Log::DEBUG('        当日分計算　=  '.$result->current_calc);
            Log::DEBUG('        当日分計算　=  '.$result->current_calc);
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
            // 同じキーの場合
            if ($current_date == $before_date &&
                $current_department_code == $current_department_code &&
                $current_user_code == $before_user_code) {
                Log::DEBUG('    同じキーの場合  ');
                $calc_nobreak_cnt++;
                if ($result->mode == Config::get('const.C005.attendance_time')) {$attendance_time = $result->record_datetime;}
                if ($result->mode == Config::get('const.C005.leaving_time')) {$leaving_time = $result->record_datetime;}
                if ($result->mode == Config::get('const.C005.missing_middle_time')) {$missing_middle_time = $result->record_datetime;}
                if ($result->mode == Config::get('const.C005.missing_middle_return_time')) {$missing_middle_return_time = $result->record_datetime;}
                if ($result->mode == Config::get('const.C005.public_going_out_time')) {$public_going_out_time = $result->record_datetime;}
                if ($result->mode == Config::get('const.C005.public_going_out_return_time')) {$public_going_out_return_time = $result->record_datetime;}
                $array_notelateetc = $this->setNoteLateEtc($result);
                $note .= $array_notelateetc[0];
                $late = $array_notelateetc[1];
                $leave_early = $array_notelateetc[2];
                $to_be_confirmed = $array_notelateetc[3];
                $working_timetable_no = $result->working_timetable_no;
                $dtNow = new Carbon();
                if ($result->record_datetime < $dtNow) {                            // 打刻時刻 < 現在時刻
                    $working_status = $result->working_status;
                }
                // 労働時間の計算
                if ($result->current_calc == '1') {     // 当日分である場合
                    // ユーザー休暇区分判定用
                    $before_holiday_date = null;
                    $before_holiday_user_code = null;
                    $before_holiday_department_code = null;
                    $before_holiday_kubun = null;
                    // 計算セットフラグ　　calcTimes実行ならtrueにする
                    $set_calcTimes_flg = false;
                    // ----------------------- 私用外出 -------------------------------------------
                    // 私用外出は複数ある可能性があるので私用外出計算は戻り時点で計算する。
                    Log::DEBUG('        私用外出 $missing_middle_time = '.$missing_middle_time);
                    if ($result->mode == Config::get('const.C005.missing_middle_time') && $missing_middle_time <> ''){
                        $array_add_missing_middle_time[] = $missing_middle_time;
                    }
                    Log::DEBUG('        私用外出戻り $missing_middle_return_time = '.$missing_middle_return_time);
                    if ($result->mode == Config::get('const.C005.missing_middle_return_time') && $missing_middle_return_time <> ''){
                        $array_add_missing_middle_return_time[] = $missing_middle_return_time;
                    }
                    if ($missing_middle_time <> '' && $missing_middle_return_time <> ''){
                        Log::DEBUG('        私用外出データ集計  count($array_working_time_kubun) = '.count($array_working_time_kubun));
                        for ($i=0;$i<count($array_working_time_kubun);$i++) {
                            if (($array_working_time_kubun[$i] <> Config::get('const.C004.regular_working_breaks_time')) &&
                                ($array_working_time_kubun[$i] <> Config::get('const.C004.working_breaks_time')))  {
                                $array_missing_middle_time[$i] += 
                                    $this->calcTimes(Config::get('const.INC_NO.missing_return'),
                                        $timetables,
                                        $working_timetable_no,
                                        $array_working_time_kubun[$i],
                                        $current_date,
                                        $missing_middle_time,
                                        $missing_middle_return_time,
                                        $array_calc_time,
                                        $array_missing_middle_time);
                                Log::DEBUG('        私用外出データ　$i =  '.$i);
                                Log::DEBUG('        私用外出データ　array_working_time_kubun[$i] =  '.$array_working_time_kubun[$i]);
                                Log::DEBUG('        私用外出データ　array_missing_middle_time[$i] =  '.$array_missing_middle_time[$i]);
                                Log::DEBUG('        私用外出データ　amissing_middle_time =  '.$missing_middle_time);
                                Log::DEBUG('        私用外出データ　missing_middle_return_time =  '.$missing_middle_return_time);
                                $set_calcTimes_flg = true;
                            }
                        }
                        // 私用外出時刻を初期化して次の計算準備
                        $missing_middle_time = '';
                        $missing_middle_return_time = '';
                    }
                    // ----------------------- 公用外出 -------------------------------------------
                    // 公用外出は複数ある可能性があるので公用外出計算は戻り時点で計算する。
                    Log::DEBUG('        公用外出 $public_going_out_time = '.$public_going_out_time);
                    if ($result->mode == Config::get('const.C005.public_going_out_time') && $public_going_out_time <> ''){
                        $array_add_public_going_out_time[] = $public_going_out_time;
                    }
                    Log::DEBUG('        公用外出戻り $public_going_out_return_time = '.$public_going_out_return_time);
                    if ($result->mode == Config::get('const.C005.public_going_out_return_time') && $public_going_out_return_time <> ''){
                        $array_add_public_going_out_return_time[] = $public_going_out_return_time;
                    }
                    if ($public_going_out_time <> '' && $public_going_out_return_time <> ''){
                        Log::DEBUG('        公用外出データ集計  count($array_working_time_kubun) = '.count($array_working_time_kubun));
                        for ($i=0;$i<count($array_working_time_kubun);$i++) {
                            if (($array_working_time_kubun[$i] <> Config::get('const.C004.regular_working_breaks_time')) &&
                                ($array_working_time_kubun[$i] <> Config::get('const.C004.working_breaks_time')))  {
                                $array_public_going_out_time[$i] += 
                                    $this->calcTimes(Config::get('const.INC_NO.public_going_out_return'),
                                        $timetables,
                                        $working_timetable_no,
                                        $array_working_time_kubun[$i],
                                        $current_date,
                                        $public_going_out_time,
                                        $public_going_out_return_time,
                                        $array_calc_time,
                                        $array_public_going_out_time);
                                Log::DEBUG('        公用外出データ　$i =  '.$i);
                                Log::DEBUG('        公用外出データ　array_working_time_kubun[$i] =  '.$array_working_time_kubun[$i]);
                                Log::DEBUG('        公用外出データ　array_public_going_out_time[$i] =  '.$array_public_going_out_time[$i]);
                                Log::DEBUG('        公用外出データ　public_going_out_time =  '.$public_going_out_time);
                                Log::DEBUG('        公用外出データ　public_going_out_return_time =  '.$public_going_out_return_time);
                                $set_calcTimes_flg = true;
                            }
                        }
                        // 公用外出時刻を初期化して次の計算準備
                        $public_going_out_time = '';
                        $public_going_out_return_time = '';
                    }
                    // ----------------------- 出勤 -------------------------------------------
                    if ($result->mode == Config::get('const.C005.attendance_time') && $attendance_time <> ''){
                        $array_add_attendance_time[] = $attendance_time;
                    }
                    // ----------------------- 退勤 -------------------------------------------
                    // 退勤データの場合計算開始
                    if ($result->mode == Config::get('const.C005.leaving_time') && $leaving_time <> ''){
                        Log::DEBUG('        出勤退勤データ集計  count($array_working_time_kubun) = '.count($array_working_time_kubun));
                        $array_add_leaving_time[] = $leaving_time;
                        $index = (int)(Config::get('const.C004.regular_working_time'))-1;
                        for ($i=0;$i<count($array_working_time_kubun);$i++) {
                            if (($array_working_time_kubun[$i] <> Config::get('const.C004.regular_working_breaks_time')) &&
                                ($array_working_time_kubun[$i] <> Config::get('const.C004.working_breaks_time')))  {
                                $array_calc_time[$i] += 
                                    $this->calcTimes(Config::get('const.INC_NO.attendace_leaving'),
                                        $timetables,
                                        $working_timetable_no,
                                        $array_working_time_kubun[$i],
                                        $current_date,
                                        $attendance_time,
                                        $leaving_time,
                                        $array_calc_time,
                                        $array_missing_middle_time);
                                Log::DEBUG('        退勤データ　$i =  '.$i);
                                Log::DEBUG('        退勤データ　array_working_time_kubun[$i] =  '.$array_working_time_kubun[$i]);
                                Log::DEBUG('        退勤データ　array_calc_time[$i] =  '.$array_calc_time[$i]);
                                Log::DEBUG('        退勤データ　attendance_time =  '.$attendance_time);
                                Log::DEBUG('        退勤データ　leaving_time =  '.$leaving_time);
                                $set_calcTimes_flg = true;
                            } else {
                                // 休憩時間を別途計算する
                                $this->not_employment_working += 
                                    $this->calcBreakTimes(
                                        $timetables,
                                        $working_timetable_no,
                                        $array_working_time_kubun[$i],
                                        $current_date,
                                        $attendance_time,
                                        $leaving_time);
                            }
                        }
                        // 出勤退勤時刻を初期化して次の計算準備
                        $attendance_time = '';
                        $leaving_time = '';
                    }
                    // calcTimes計算対象外であった場合、
                    // 出勤していなく１日休暇設定されていればデータ作成
                    // 出勤していなく休暇設定されていなければデータ作成
                    //if (!$set_calcTimes_flg && isset($result->holiday_kubun)) {
                    if (!$set_calcTimes_flg && !$mode_chk) {
                        Log::DEBUG('        temp_working_time_datesデータ作成開始 ');
                        Log::DEBUG('            calcTimes計算対象外データ作成 =  '.$current_user_code);
                        Log::DEBUG('                休暇区分  = '.$result->holiday_kubun);
                        if (($result->holiday_kubun != Config::get('const.C013.non_set') &&
                            $result->holiday_kubun != Config::get('const.C013.morning_off') &&
                            $result->holiday_kubun != Config::get('const.C013.afternoon_off') &&
                            $result->holiday_kubun != Config::get('const.C013.absence_work') &&
                            $result->holiday_kubun != Config::get('const.C013.late_work') &&
                            $result->holiday_kubun != Config::get('const.C013.leave_early_work')) ||
                            (!isset($result->holiday_kubun) ||
                            $result->holiday_kubun != Config::get('const.C013.non_set'))) {
                            $add_result = $this->addTempWorkingTimeDate(
                                $current_date,
                                $current_user_code,
                                $current_department_code,
                                $current_result,
                                $note,
                                $working_status,
                                $array_calc_time,
                                $array_missing_middle_time,
                                $array_public_going_out_time,
                                $array_add_attendance_time,
                                $array_add_leaving_time,
                                $array_add_missing_middle_time,
                                $array_add_missing_middle_return_time,
                                $array_add_public_going_out_time,
                                $array_add_public_going_out_return_time);
                            Log::DEBUG('        temp_working_time_datesデータ作成終了 '.$current_user_code);
                            // 次データ計算事前処理
                            for ($i=0;$i<count($array_working_time_kubun);$i++) {
                                $array_calc_time[$i] = 0; 
                            }
                            for ($i=0;$i<count($array_working_time_kubun);$i++) {
                                $array_missing_middle_time[$i] = 0; 
                            }
                            for ($i=0;$i<count($array_working_time_kubun);$i++) {
                                $array_public_going_out_time[$i] = 0; 
                            }
                            if ($result->mode == Config::get('const.C005.attendance_time')) {$attendance_time = $result->record_datetime;}
                            if ($result->mode == Config::get('const.C005.leaving_time')) {$leaving_time = $result->record_datetime;}
                            if ($result->mode == Config::get('const.C005.missing_middle_time')) {$missing_middle_time = $result->record_datetime;}
                            if ($result->mode == Config::get('const.C005.missing_middle_return_time')) {$missing_middle_return_time = $result->record_datetime;}
                            if ($result->mode == Config::get('const.C005.public_going_out_time')) {$public_going_out_time = $result->record_datetime;}
                            if ($result->mode == Config::get('const.C005.public_going__outreturn_time')) {$public_going_out_return_time = $result->record_datetime;}
                            $array_add_attendance_time = array();
                            $array_add_leaving_time = array();
                            $array_add_missing_middle_time = array();
                            $array_add_missing_middle_return_time = array();
                            $array_add_public_going_out_time = array();
                            $array_add_public_going_out_return_time = array();
                            if ($result->mode == Config::get('const.C005.missing_middle_time') && $missing_middle_time <> ''){
                                $array_add_missing_middle_time[] = $missing_middle_time;
                            }
                            if ($result->mode == Config::get('const.C005.missing_middle_return_time') && $missing_middle_return_time <> ''){
                                $array_add_missing_middle_return_time[] = $missing_middle_return_time;
                            }
                            if ($result->mode == Config::get('const.C005.public_going_out_time') && $public_going_out_time <> ''){
                                $array_add_public_going_out_time[] = $public_going_out_time;
                            }
                            if ($result->mode == Config::get('const.C005.public_going_out_return_time') && $public_going_out_return_time <> ''){
                                $array_add_public_going_out_return_time[] = $public_going_out_return_time;
                            }
                            if ($result->mode == Config::get('const.C005.attendance_time') && $attendance_time <> ''){
                                $array_add_attendance_time[] = $attendance_time;
                            }
                            if ($result->mode == Config::get('const.C005.leaving_time') && $leaving_time <> ''){
                                $array_add_leaving_time[] = $leaving_time;
                            }
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
                } else {
                    // 時刻だけ設定する
                    Log::DEBUG('    当日分計算対象外');
                    // ----------------------- 私用外出 -------------------------------------------
                    Log::DEBUG('    私用外出打刻時刻 = '.$missing_middle_time);
                    if ($result->mode == Config::get('const.C005.missing_middle_time') && $missing_middle_time <> ''){
                        $array_add_missing_middle_time[] = $missing_middle_time;
                    }
                    Log::DEBUG('    私用外出戻り打刻時刻 = '.$missing_middle_return_time);
                    if ($result->mode == Config::get('const.C005.missing_middle_return_time') && $missing_middle_return_time <> ''){
                        $array_add_missing_middle_return_time[] = $missing_middle_return_time;
                    }
                    // ----------------------- 公用外出 -------------------------------------------
                    Log::DEBUG('    公用外出打刻時刻 = '.$public_going_out_time);
                    if ($result->mode == Config::get('const.C005.public_going_out_time') && $public_going_out_time <> ''){
                        $array_add_public_going_out_time[] = $public_going_out_time;
                    }
                    Log::DEBUG('    公用外出戻り打刻時刻 = '.$public_going_out_return_time);
                    if ($result->mode == Config::get('const.C005.public_going_out_return_time') && $public_going_out_return_time <> ''){
                        $array_add_public_going_out_return_time[] = $public_going_out_return_time;
                    }
                    // ----------------------- 出勤 -------------------------------------------
                    Log::DEBUG('    出勤打刻時刻 = '.$attendance_time);
                    if ($result->mode == Config::get('const.C005.attendance_time') && $attendance_time <> ''){
                        $array_add_attendance_time[] = $attendance_time;
                    }
                    // ----------------------- 退勤 -------------------------------------------
                    Log::DEBUG('    退勤打刻時刻 = '.$leaving_time);
                    if ($result->mode == Config::get('const.C005.leaving_time') && $leaving_time <> ''){
                        $array_add_leaving_time[] = $leaving_time;
                    }
                }
            } elseif ($current_date == $before_date &&
                    $current_department_code == $before_department_code) {
                Log::DEBUG('--- '.$before_result->user_name.' 終了  ------ '.$before_date.' モード  ------ '.$before_result->mode.' ---- $calc_nobreak_cnt = '.$calc_nobreak_cnt );
                Log::DEBUG('--- '.$result->user_name.' 開始  ------ '.$current_date.' モード  ------ '.$result->mode.' ---- $calc_nobreak_cnt = '.$calc_nobreak_cnt );
                // ユーザーが変わった場合
                Log::DEBUG('    ユーザーが変わった場合 ');
                Log::DEBUG('        $result->user_code  '.$before_user_code.'->'.$result->user_code);
                Log::DEBUG('        $result->mode  '.$result->mode);
                Log::DEBUG('        $result->record_datetime  '.$result->record_datetime);
                Log::DEBUG('        $result->working_timetable_from_time  '.$result->working_timetable_from_time);
                Log::DEBUG('        $result->working_timetable_to_time  '.$result->working_timetable_to_time);
                try{
                    Log::DEBUG('        $working_status  '.$working_status);
                    if ($working_status == 0 ) {
                        Log::DEBUG('        当日分　勤務状態 打刻時刻 =  '.$before_result->record_datetime);
                        Log::DEBUG('        当日分　勤務状態 現在時刻 =  '.$dtNow);
                        if ($before_result->record_datetime < $dtNow) {                            // 打刻時刻 < 現在時刻
                            $working_status = $before_result->working_status;
                        }
                        Log::DEBUG('当日分　勤務状態 =  '.$working_status);
                    }
                    Log::DEBUG('        $calc_nobreak_cnt  '.$calc_nobreak_cnt);
                    if ($calc_nobreak_cnt == 0) {
                        $array_notelateetc = $this->setNoteLateEtc($before_result);
                        $note .= $array_notelateetc[0];
                        Log::DEBUG('        setNoteLateEtc $note =  '.$note);
                        $late = $array_notelateetc[1];
                        $leave_early = $array_notelateetc[2];
                        $to_be_confirmed = $array_notelateetc[3];
                    }
                    // １個前のユーザーが休暇設定されていた場合はすでに登録済み
                    Log::DEBUG('        before_holiday_set = '.$before_holiday_set);
                    Log::DEBUG('        before_holiday_kubun = '.$before_holiday_kubun);
                    Log::DEBUG('        result->holiday_kubun = '.$result->holiday_kubun);
                    if ($before_holiday_set == false) {
                        Log::DEBUG('        temp_working_time_datesデータ作成開始 ');
                        Log::DEBUG('            １個前のユーザーを登録 '.$before_user_code);
                        // ユーザー労働時間登録(１個前のユーザーを登録する)
                        $add_result = $this->addTempWorkingTimeDate(
                            $before_date,
                            $before_user_code,
                            $before_department_code,
                            $before_result,
                            $note,
                            $working_status,
                            $array_calc_time,
                            $array_missing_middle_time,
                            $array_public_going_out_time,
                            $array_add_attendance_time,
                            $array_add_leaving_time,
                            $array_add_missing_middle_time,
                            $array_add_missing_middle_return_time,
                            $array_add_public_going_out_time,
                            $array_add_public_going_out_return_time);
                        Log::DEBUG('        temp_working_time_datesデータ作成終了 '.$before_user_code);
                    }
                    $before_holiday_set = false;
                    // 日付とユーザー休暇区分を保存
                    $before_holiday_date = $current_date;
                    $before_holiday_user_code = $current_user_code;
                    $before_holiday_department_code = $current_department_code;
                    $before_holiday_kubun = $result->holiday_kubun;
                    // 次データ計算事前処理
                    for ($i=0;$i<count($array_working_time_kubun);$i++) {
                        $array_calc_time[$i] = 0; 
                    }
                    for ($i=0;$i<count($array_working_time_kubun);$i++) {
                        $array_missing_middle_time[$i] = 0; 
                    }
                    for ($i=0;$i<count($array_working_time_kubun);$i++) {
                        $array_public_going_out_time[$i] = 0; 
                    }
                    if ($result->mode == Config::get('const.C005.attendance_time')) {$attendance_time = $result->record_datetime;}
                    if ($result->mode == Config::get('const.C005.leaving_time')) {$leaving_time = $result->record_datetime;}
                    if ($result->mode == Config::get('const.C005.missing_middle_time')) {$missing_middle_time = $result->record_datetime;}
                    if ($result->mode == Config::get('const.C005.missing_middle_return_time')) {$missing_middle_return_time = $result->record_datetime;}
                    if ($result->mode == Config::get('const.C005.public_going_out_time')) {$public_going_out_time = $result->record_datetime;}
                    if ($result->mode == Config::get('const.C005.public_going_out_return_time')) {$public_going_out_return_time = $result->record_datetime;}
                    $array_add_attendance_time = array();
                    $array_add_leaving_time = array();
                    $array_add_missing_middle_time = array();
                    $array_add_missing_middle_return_time = array();
                    $array_add_public_going_out_time = array();
                    $array_add_public_going_out_return_time = array();
                    Log::DEBUG('        addTempWorkingTimeDate後 $result->current_calc  '.$result->current_calc);
                    Log::DEBUG('        addTempWorkingTimeDate後 $result->mode  '.$result->mode);
                    Log::DEBUG('        addTempWorkingTimeDate後 $leaving_time  '.$leaving_time);
                    //if ($result->current_calc == '1') {             // 当日分である場合
                        if ($result->mode == Config::get('const.C005.missing_middle_time') && $missing_middle_time <> ''){
                            $array_add_missing_middle_time[] = $missing_middle_time;
                        }
                        if ($result->mode == Config::get('const.C005.missing_middle_return_time') && $missing_middle_return_time <> ''){
                            $array_add_missing_middle_return_time[] = $missing_middle_return_time;
                        }
                        if ($result->mode == Config::get('const.C005.public_going_out_time') && $public_going_out_time <> ''){
                            $array_add_public_going_out_time[] = $public_going_out_time;
                        }
                        if ($result->mode == Config::get('const.C005.public_going_out_return_time') && $public_going_out_return_time <> ''){
                            $array_add_public_going_out_return_time[] = $public_going_out_return_time;
                        }
                        if ($result->mode == Config::get('const.C005.attendance_time') && $attendance_time <> ''){
                            $array_add_attendance_time[] = $attendance_time;
                        }
                        if ($result->mode == Config::get('const.C005.leaving_time') && $leaving_time <> ''){
                            $array_add_leaving_time[] = $leaving_time;
                        }
                    //}
                    Log::DEBUG('        $array_add_leaving_time  '.count($array_add_leaving_time));
                    // 同じ値にする
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
                    Log::DEBUG('        setNoteLateEtc $note =  '.$note);
                    $late = $array_notelateetc[1];
                    $leave_early = $array_notelateetc[2];
                    $to_be_confirmed = $array_notelateetc[3];
                    $calc_nobreak_cnt++;
                }catch(\PDOException $pe){
                    $add_result = false;
                    throw $pe;
                }catch(\Exception $e){
                    $add_result = false;
                    throw $e;
                }
                // 現データが当日計算対象で、出勤していない休暇設定されていればデータ作成
                //if ($result->current_calc == '1' && isset($result->holiday_kubun)) {
                if ($result->current_calc == '1' && !$mode_chk) {
                    Log::DEBUG('        temp_working_time_datesデータ作成開始 ');
                    Log::DEBUG('            現データが当日計算対象データ作成 =  '.$current_user_code);
                    Log::DEBUG('                休暇区分  = '.$result->holiday_kubun);
                    if (($result->holiday_kubun != Config::get('const.C013.non_set') &&
                        $result->holiday_kubun != Config::get('const.C013.morning_off') &&
                        $result->holiday_kubun != Config::get('const.C013.afternoon_off') &&
                        $result->holiday_kubun != Config::get('const.C013.absence_work') &&
                        $result->holiday_kubun != Config::get('const.C013.late_work') &&
                        $result->holiday_kubun != Config::get('const.C013.leave_early_work')) ||
                        (!isset($result->holiday_kubun) ||
                        $result->holiday_kubun != Config::get('const.C013.non_set'))) {
                        $add_result = $this->addTempWorkingTimeDate(
                            $current_date,
                            $current_user_code,
                            $current_department_code,
                            $current_result,
                            $note,
                            $working_status,
                            $array_calc_time,
                            $array_missing_middle_time,
                            $array_public_going_out_time,
                            $array_add_attendance_time,
                            $array_add_leaving_time,
                            $array_add_missing_middle_time,
                            $array_add_missing_middle_return_time,
                            $array_add_public_going_out_time,
                            $array_add_public_going_out_return_time);
                        Log::DEBUG('        temp_working_time_datesデータ作成終了 '.$current_user_code);
                        // 次データ計算事前処理
                        for ($i=0;$i<count($array_working_time_kubun);$i++) {
                            $array_calc_time[$i] = 0; 
                        }
                        for ($i=0;$i<count($array_working_time_kubun);$i++) {
                            $array_missing_middle_time[$i] = 0; 
                        }
                        for ($i=0;$i<count($array_working_time_kubun);$i++) {
                            $array_public_going_out_time[$i] = 0; 
                        }
                        if ($result->mode == Config::get('const.C005.attendance_time')) {$attendance_time = $result->record_datetime;}
                        if ($result->mode == Config::get('const.C005.leaving_time')) {$leaving_time = $result->record_datetime;}
                        if ($result->mode == Config::get('const.C005.missing_middle_time')) {$missing_middle_time = $result->record_datetime;}
                        if ($result->mode == Config::get('const.C005.missing_middle_return_time')) {$missing_middle_return_time = $result->record_datetime;}
                        if ($result->mode == Config::get('const.C005.public_going_out_time')) {$public_going_out_time = $result->record_datetime;}
                        if ($result->mode == Config::get('const.C005.public_going__outreturn_time')) {$public_going_out_return_time = $result->record_datetime;}
                        $array_add_attendance_time = array();
                        $array_add_leaving_time = array();
                        $array_add_missing_middle_time = array();
                        $array_add_missing_middle_return_time = array();
                        $array_add_public_going_out_time = array();
                        $array_add_public_going_out_return_time = array();
                        if ($result->mode == Config::get('const.C005.missing_middle_time') && $missing_middle_time <> ''){
                            $array_add_missing_middle_time[] = $missing_middle_time;
                        }
                        if ($result->mode == Config::get('const.C005.missing_middle_return_time') && $missing_middle_return_time <> ''){
                            $array_add_missing_middle_return_time[] = $missing_middle_return_time;
                        }
                        if ($result->mode == Config::get('const.C005.public_going_out_time') && $public_going_out_time <> ''){
                            $array_add_public_going_out_time[] = $public_going_out_time;
                        }
                        if ($result->mode == Config::get('const.C005.public_going_out_return_time') && $public_going_out_return_time <> ''){
                            $array_add_public_going_out_return_time[] = $public_going_out_return_time;
                        }
                        if ($result->mode == Config::get('const.C005.attendance_time') && $attendance_time <> ''){
                            $array_add_attendance_time[] = $attendance_time;
                        }
                        if ($result->mode == Config::get('const.C005.leaving_time') && $leaving_time <> ''){
                            $array_add_leaving_time[] = $leaving_time;
                        }
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
                        Log::DEBUG('setNoteLateEtc $note =  '.$note);
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
                Log::DEBUG('部署が変わった場合 ');
                Log::DEBUG('--- '.$before_result->user_name.' 終了  ------ '.$before_date.' モード  ------ '.$before_result->mode.' ---- $calc_nobreak_cnt = '.$calc_nobreak_cnt );
                Log::DEBUG('--- '.$result->user_name.' 開始  ------ '.$current_date.' モード  ------ '.$result->mode.' ---- $calc_nobreak_cnt = '.$calc_nobreak_cnt );
                try{
                    if ($working_status == 0 ) {
                        Log::DEBUG('当日分　勤務状態 打刻時刻 =  '.$before_result->record_datetime);
                        Log::DEBUG('当日分　勤務状態 現在時刻 =  '.$dtNow);
                        if ($before_result->record_datetime < $dtNow) {                            // 打刻時刻 < 現在時刻
                            $working_status = $before_result->working_status;
                        }
                        Log::DEBUG('当日分　勤務状態 =  '.$working_status);
                    }
                    if ($calc_nobreak_cnt == 0) {
                        $array_notelateetc = $this->setNoteLateEtc($before_result);
                        $note .= $array_notelateetc[0];
                        Log::DEBUG('setNoteLateEtc $note =  '.$note);
                        $late = $array_notelateetc[1];
                        $leave_early = $array_notelateetc[2];
                        $to_be_confirmed = $array_notelateetc[3];
                    }
                    // １個前のユーザーが休暇設定されていた場合はすでに登録済み
                    if ($before_holiday_set == false) {
                        // ユーザー労働時間登録(１個前のユーザーを登録する)
                        Log::DEBUG('        temp_working_time_datesデータ作成開始 ');
                        Log::DEBUG('            １個前のユーザーを登録 '.$before_user_code);
                        $add_result = $this->addTempWorkingTimeDate(
                            $before_date,
                            $before_user_code,
                            $before_department_code,
                            $before_result,
                            $note,
                            $working_status,
                            $array_calc_time,
                            $array_missing_middle_time,
                            $array_public_going_out_time,
                            $array_add_attendance_time,
                            $array_add_leaving_time,
                            $array_add_missing_middle_time,
                            $array_add_missing_middle_return_time,
                            $array_add_public_going_out_time,
                            $array_add_public_going_out_return_time);
                        Log::DEBUG('        temp_working_time_datesデータ作成終了 '.$before_user_code);
                    }
                    $before_holiday_set = false;
                    // 日付とユーザー休暇区分を保存
                    $before_holiday_date = $current_date;
                    $before_holiday_user_code = $current_user_code;
                    $before_holiday_department_code = $current_department_code;
                    $before_holiday_kubun = $result->holiday_kubun;
                    // 次データ計算事前処理
                    for ($i=0;$i<count($array_working_time_kubun);$i++) {
                        $array_calc_time[$i] = 0; 
                    }
                    for ($i=0;$i<count($array_working_time_kubun);$i++) {
                        $array_missing_middle_time[$i] = 0; 
                    }
                    for ($i=0;$i<count($array_working_time_kubun);$i++) {
                        $array_public_going_out_time[$i] = 0; 
                    }
                    if ($result->mode == Config::get('const.C005.attendance_time')) {$attendance_time = $result->record_datetime;}
                    if ($result->mode == Config::get('const.C005.leaving_time')) {$leaving_time = $result->record_datetime;}
                    if ($result->mode == Config::get('const.C005.missing_middle_time')) {$missing_middle_time = $result->record_datetime;}
                    if ($result->mode == Config::get('const.C005.missing_middle_return_time')) {$missing_middle_return_time = $result->record_datetime;}
                    if ($result->mode == Config::get('const.C005.public_going_out_time')) {$public_going_out_time = $result->record_datetime;}
                    if ($result->mode == Config::get('const.C005.public_going_out_return_time')) {$public_going_out_return_time = $result->record_datetime;}
                    $array_add_attendance_time = array();
                    $array_add_leaving_time = array();
                    $array_add_missing_middle_time = array();
                    $array_add_missing_middle_return_time = array();
                    $array_add_public_going_out_time = array();
                    $array_add_public_going_out_return_time = array();
                    //if ($result->current_calc == '1') {             // 当日分である場合
                        if ($result->mode == Config::get('const.C005.missing_middle_time') && $missing_middle_time <> ''){
                            $array_add_missing_middle_time[] = $missing_middle_time;
                        }
                        if ($result->mode == Config::get('const.C005.missing_middle_return_time') && $missing_middle_return_time <> ''){
                            $array_add_missing_middle_return_time[] = $missing_middle_return_time;
                        }
                        if ($result->mode == Config::get('const.C005.public_going_out_time') && $public_going_out_time <> ''){
                            $array_add_public_going_out_time[] = $public_going_out_time;
                        }
                        if ($result->mode == Config::get('const.C005.public_going_out_return_time') && $public_going_out_return_time <> ''){
                            $array_add_public_going_out_return_time[] = $public_going_out_return_time;
                        }
                        if ($result->mode == Config::get('const.C005.attendance_time') && $attendance_time <> ''){
                            $array_add_attendance_time[] = $attendance_time;
                        }
                        if ($result->mode == Config::get('const.C005.leaving_time') && $leaving_time <> ''){
                            $array_add_leaving_time[] = $leaving_time;
                        }
                    //}
                    // 同じ値にする
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
                    Log::DEBUG('setNoteLateEtc $note =  '.$note);
                    $late = $array_notelateetc[1];
                    $leave_early = $array_notelateetc[2];
                    $to_be_confirmed = $array_notelateetc[3];
                    $calc_nobreak_cnt++;
                }catch(\PDOException $pe){
                    $add_result = false;
                    throw $pe;
                }catch(\Exception $e){
                    $add_result = false;
                    throw $e;
                }
                // 現データが当日計算対象で、出勤していない休暇設定されていればデータ作成
                //if ($result->current_calc == '1' && isset($result->holiday_kubun)) {
                if ($result->current_calc == '1' && !$mode_chk) {
                    Log::DEBUG('        temp_working_time_datesデータ作成開始 ');
                    Log::DEBUG('            現データが当日計算対象データ作成 =  '.$current_user_code);
                    Log::DEBUG('                休暇区分  = '.$result->holiday_kubun);
                    if (($result->holiday_kubun != Config::get('const.C013.non_set') &&
                        $result->holiday_kubun != Config::get('const.C013.morning_off') &&
                        $result->holiday_kubun != Config::get('const.C013.afternoon_off') &&
                        $result->holiday_kubun != Config::get('const.C013.absence_work') &&
                        $result->holiday_kubun != Config::get('const.C013.late_work') &&
                        $result->holiday_kubun != Config::get('const.C013.leave_early_work')) ||
                        (!isset($result->holiday_kubun) ||
                        $result->holiday_kubun != Config::get('const.C013.non_set'))) {
                        $add_result = $this->addTempWorkingTimeDate(
                            $current_date,
                            $current_user_code,
                            $current_department_code,
                            $current_result,
                            $note,
                            $working_status,
                            $array_calc_time,
                            $array_missing_middle_time,
                            $array_public_going_out_time,
                            $array_add_attendance_time,
                            $array_add_leaving_time,
                            $array_add_missing_middle_time,
                            $array_add_missing_middle_return_time,
                            $array_add_public_going_out_time,
                            $array_add_public_going_out_return_time);
                        Log::DEBUG('        temp_working_time_datesデータ作成終了 '.$current_user_code);
                        // 次データ計算事前処理
                        for ($i=0;$i<count($array_working_time_kubun);$i++) {
                            $array_calc_time[$i] = 0; 
                        }
                        for ($i=0;$i<count($array_working_time_kubun);$i++) {
                            $array_missing_middle_time[$i] = 0; 
                        }
                        for ($i=0;$i<count($array_working_time_kubun);$i++) {
                            $array_public_going_out_time[$i] = 0; 
                        }
                        if ($result->mode == Config::get('const.C005.attendance_time')) {$attendance_time = $result->record_datetime;}
                        if ($result->mode == Config::get('const.C005.leaving_time')) {$leaving_time = $result->record_datetime;}
                        if ($result->mode == Config::get('const.C005.missing_middle_time')) {$missing_middle_time = $result->record_datetime;}
                        if ($result->mode == Config::get('const.C005.missing_middle_return_time')) {$missing_middle_return_time = $result->record_datetime;}
                        if ($result->mode == Config::get('const.C005.public_going_out_time')) {$public_going_out_time = $result->record_datetime;}
                        if ($result->mode == Config::get('const.C005.public_going__outreturn_time')) {$public_going_out_return_time = $result->record_datetime;}
                        $array_add_attendance_time = array();
                        $array_add_leaving_time = array();
                        $array_add_missing_middle_time = array();
                        $array_add_missing_middle_return_time = array();
                        $array_add_public_going_out_time = array();
                        $array_add_public_going_out_return_time = array();
                        if ($result->mode == Config::get('const.C005.missing_middle_time') && $missing_middle_time <> ''){
                            $array_add_missing_middle_time[] = $missing_middle_time;
                        }
                        if ($result->mode == Config::get('const.C005.missing_middle_return_time') && $missing_middle_return_time <> ''){
                            $array_add_missing_middle_return_time[] = $missing_middle_return_time;
                        }
                        if ($result->mode == Config::get('const.C005.public_going_out_time') && $public_going_out_time <> ''){
                            $array_add_public_going_out_time[] = $public_going_out_time;
                        }
                        if ($result->mode == Config::get('const.C005.public_going_out_return_time') && $public_going_out_return_time <> ''){
                            $array_add_public_going_out_return_time[] = $public_going_out_return_time;
                        }
                        if ($result->mode == Config::get('const.C005.attendance_time') && $attendance_time <> ''){
                            $array_add_attendance_time[] = $attendance_time;
                        }
                        if ($result->mode == Config::get('const.C005.leaving_time') && $leaving_time <> ''){
                            $array_add_leaving_time[] = $leaving_time;
                        }
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
                        Log::DEBUG('setNoteLateEtc $note =  '.$note);
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
                Log::DEBUG('日付が変わった場合 ');
                Log::DEBUG('--- '.$before_result->user_name.' 終了  ------ '.$before_date.' モード  ------ '.$before_result->mode.' ---- $calc_nobreak_cnt = '.$calc_nobreak_cnt );
                Log::DEBUG('--- '.$result->user_name.' 開始  ------ '.$current_date.' モード  ------ '.$result->mode.' ---- $calc_nobreak_cnt = '.$calc_nobreak_cnt );
                try{
                    if ($working_status == 0 ) {
                        Log::DEBUG('当日分　勤務状態 打刻時刻 =  '.$before_result->record_datetime);
                        Log::DEBUG('当日分　勤務状態 現在時刻 =  '.$dtNow);
                        if ($before_result->record_datetime < $dtNow) {                            // 打刻時刻 < 現在時刻
                            $working_status = $before_result->working_status;
                        }
                        Log::DEBUG('当日分　勤務状態 =  '.$working_status);
                    }
                    if ($calc_nobreak_cnt == 0) {
                        $array_notelateetc = $this->setNoteLateEtc($before_result);
                        $note .= $array_notelateetc[0];
                        Log::DEBUG('setNoteLateEtc $note =  '.$note);
                        $late = $array_notelateetc[1];
                        $leave_early = $array_notelateetc[2];
                        $to_be_confirmed = $array_notelateetc[3];
                    }
                    // １個前のユーザーが休暇設定されていた場合はすでに登録済み
                    if ($before_holiday_set == false) {
                        // ユーザー労働時間登録(１個前のユーザーを登録する)
                        Log::DEBUG('        temp_working_time_datesデータ作成開始 ');
                        Log::DEBUG('            １個前のユーザーを登録 '.$before_user_code);
                        $add_result = $this->addTempWorkingTimeDate(
                            $before_date,
                            $before_user_code,
                            $before_department_code,
                            $before_result,
                            $note,
                            $working_status,
                            $array_calc_time,
                            $array_missing_middle_time,
                            $array_public_going_out_time,
                            $array_add_attendance_time,
                            $array_add_leaving_time,
                            $array_add_missing_middle_time,
                            $array_add_missing_middle_return_time,
                            $array_add_public_going_out_time,
                            $array_add_public_going_out_return_time);
                        Log::DEBUG('        temp_working_time_datesデータ作成終了 '.$before_user_code);
                    }
                    $before_holiday_set = false;
                    // 日付とユーザー休暇区分を保存
                    $before_holiday_date = $current_date;
                    $before_holiday_user_code = $current_user_code;
                    $before_holiday_department_code = $current_department_code;
                    $before_holiday_kubun = $result->holiday_kubun;
                    // 次データ計算事前処理
                    for ($i=0;$i<count($array_working_time_kubun);$i++) {
                        $array_calc_time[$i] = 0; 
                    }
                    for ($i=0;$i<count($array_working_time_kubun);$i++) {
                        $array_missing_middle_time[$i] = 0; 
                    }
                    for ($i=0;$i<count($array_working_time_kubun);$i++) {
                        $array_public_going_out_time[$i] = 0; 
                    }
                    if ($result->mode == Config::get('const.C005.attendance_time')) {$attendance_time = $result->record_datetime;}
                    if ($result->mode == Config::get('const.C005.leaving_time')) {$leaving_time = $result->record_datetime;}
                    if ($result->mode == Config::get('const.C005.missing_middle_time')) {$missing_middle_time = $result->record_datetime;}
                    if ($result->mode == Config::get('const.C005.missing_middle_return_time')) {$missing_middle_return_time = $result->record_datetime;}
                    if ($result->mode == Config::get('const.C005.public_going_out_time')) {$public_going_out_time = $result->record_datetime;}
                    if ($result->mode == Config::get('const.C005.public_going__outreturn_time')) {$public_going_out_return_time = $result->record_datetime;}
                    $array_add_attendance_time = array();
                    $array_add_leaving_time = array();
                    $array_add_missing_middle_time = array();
                    $array_add_missing_middle_return_time = array();
                    $array_add_public_going_out_time = array();
                    $array_add_public_going_out_return_time = array();
                    //if ($result->current_calc == '1') {             // 当日分である場合
                        if ($result->mode == Config::get('const.C005.missing_middle_time') && $missing_middle_time <> ''){
                            $array_add_missing_middle_time[] = $missing_middle_time;
                        }
                        if ($result->mode == Config::get('const.C005.missing_middle_return_time') && $missing_middle_return_time <> ''){
                            $array_add_missing_middle_return_time[] = $missing_middle_return_time;
                        }
                        if ($result->mode == Config::get('const.C005.public_going_out_time') && $public_going_out_time <> ''){
                            $array_add_public_going_out_time[] = $public_going_out_time;
                        }
                        if ($result->mode == Config::get('const.C005.public_going_out_return_time') && $public_going_out_return_time <> ''){
                            $array_add_public_going_out_return_time[] = $public_going_out_return_time;
                        }
                        if ($result->mode == Config::get('const.C005.attendance_time') && $attendance_time <> ''){
                            $array_add_attendance_time[] = $attendance_time;
                        }
                        if ($result->mode == Config::get('const.C005.leaving_time') && $leaving_time <> ''){
                            $array_add_leaving_time[] = $leaving_time;
                        }
                    //}
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
                    Log::DEBUG('setNoteLateEtc $note =  '.$note);
                    $late = $array_notelateetc[1];
                    $leave_early = $array_notelateetc[2];
                    $to_be_confirmed = $array_notelateetc[3];
                    $calc_nobreak_cnt++;
                }catch(\PDOException $pe){
                    $add_result = false;
                    throw $pe;
                }catch(\Exception $e){
                    $add_result = false;
                    throw $e;
                }
                // 現データが当日計算対象で、出勤していない休暇設定されていればデータ作成
                //if ($result->current_calc == '1' && isset($result->holiday_kubun)) {
                if ($result->current_calc == '1' && !$mode_chk) {
                    Log::DEBUG('        temp_working_time_datesデータ作成開始 ');
                    Log::DEBUG('            現データが当日計算対象データ作成 =  '.$current_user_code);
                    Log::DEBUG('                休暇区分  = '.$result->holiday_kubun);
                    if (($result->holiday_kubun != Config::get('const.C013.non_set') &&
                        $result->holiday_kubun != Config::get('const.C013.morning_off') &&
                        $result->holiday_kubun != Config::get('const.C013.afternoon_off') &&
                        $result->holiday_kubun != Config::get('const.C013.absence_work') &&
                        $result->holiday_kubun != Config::get('const.C013.late_work') &&
                        $result->holiday_kubun != Config::get('const.C013.leave_early_work')) ||
                        (!isset($result->holiday_kubun) ||
                        $result->holiday_kubun != Config::get('const.C013.non_set'))) {
                        $add_result = $this->addTempWorkingTimeDate(
                            $current_date,
                            $current_user_code,
                            $current_department_code,
                            $current_result,
                            $note,
                            $working_status,
                            $array_calc_time,
                            $array_missing_middle_time,
                            $array_public_going_out_time,
                            $array_add_attendance_time,
                            $array_add_leaving_time,
                            $array_add_missing_middle_time,
                            $array_add_missing_middle_return_time,
                            $array_add_public_going_out_time,
                            $array_add_public_going_out_return_time);
                        Log::DEBUG('        temp_working_time_datesデータ作成終了 '.$current_user_code);
                        // 次データ計算事前処理
                        for ($i=0;$i<count($array_working_time_kubun);$i++) {
                            $array_calc_time[$i] = 0; 
                        }
                        for ($i=0;$i<count($array_working_time_kubun);$i++) {
                            $array_missing_middle_time[$i] = 0; 
                        }
                        for ($i=0;$i<count($array_working_time_kubun);$i++) {
                            $array_public_going_out_time[$i] = 0; 
                        }
                        if ($result->mode == Config::get('const.C005.attendance_time')) {$attendance_time = $result->record_datetime;}
                        if ($result->mode == Config::get('const.C005.leaving_time')) {$leaving_time = $result->record_datetime;}
                        if ($result->mode == Config::get('const.C005.missing_middle_time')) {$missing_middle_time = $result->record_datetime;}
                        if ($result->mode == Config::get('const.C005.missing_middle_return_time')) {$missing_middle_return_time = $result->record_datetime;}
                        if ($result->mode == Config::get('const.C005.public_going_out_time')) {$public_going_out_time = $result->record_datetime;}
                        if ($result->mode == Config::get('const.C005.public_going__outreturn_time')) {$public_going_out_return_time = $result->record_datetime;}
                        $array_add_attendance_time = array();
                        $array_add_leaving_time = array();
                        $array_add_missing_middle_time = array();
                        $array_add_missing_middle_return_time = array();
                        $array_add_public_going_out_time = array();
                        $array_add_public_going_out_return_time = array();
                        if ($result->mode == Config::get('const.C005.missing_middle_time') && $missing_middle_time <> ''){
                            $array_add_missing_middle_time[] = $missing_middle_time;
                        }
                        if ($result->mode == Config::get('const.C005.missing_middle_return_time') && $missing_middle_return_time <> ''){
                            $array_add_missing_middle_return_time[] = $missing_middle_return_time;
                        }
                        if ($result->mode == Config::get('const.C005.public_going_out_time') && $public_going_out_time <> ''){
                            $array_add_public_going_out_time[] = $public_going_out_time;
                        }
                        if ($result->mode == Config::get('const.C005.public_going_out_return_time') && $public_going_out_return_time <> ''){
                            $array_add_public_going_out_return_time[] = $public_going_out_return_time;
                        }
                        if ($result->mode == Config::get('const.C005.attendance_time') && $attendance_time <> ''){
                            $array_add_attendance_time[] = $attendance_time;
                        }
                        if ($result->mode == Config::get('const.C005.leaving_time') && $leaving_time <> ''){
                            $array_add_leaving_time[] = $leaving_time;
                        }
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
                        Log::DEBUG('setNoteLateEtc $note =  '.$note);
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

        Log::DEBUG('        残り　$calc_nobreak_cnt  =  '.$calc_nobreak_cnt);
        Log::DEBUG('        残り　$current_result->current_calc  =  '.$current_result->current_calc);
        Log::DEBUG('        残り　$current_result->holiday_kubun  =  '.$current_result->holiday_kubun);
        Log::DEBUG('        残り　$array_add_missing_middle_time  =  '.count($array_add_missing_middle_time));
        Log::DEBUG('        残り　$array_add_missing_middle_return_time  =  '.count($array_add_missing_middle_return_time));
        Log::DEBUG('        残り　$array_add_public_going_out_time  =  '.count($array_add_public_going_out_time));
        Log::DEBUG('        残り　$array_add_public_going_out_return_time  =  '.count($array_add_public_going_out_return_time));
        Log::DEBUG('        残り　$array_add_attendance_time  =  '.count($array_add_attendance_time));
        Log::DEBUG('        残り　$array_add_leaving_time  =  '.count($array_add_leaving_time));
        if ($calc_nobreak_cnt > 0 && $current_result->current_calc != 0) {
            if (count($array_add_attendance_time) > 0 ||
                count($array_add_leaving_time) > 0 ||
                count($array_add_missing_middle_time) > 0 ||
                count($array_add_missing_middle_return_time) > 0 ||
                count($array_add_public_going_out_time) > 0 ||
                count($array_add_public_going_out_return_time) > 0) {
                try{
                    Log::DEBUG('--- '.$current_result->user_name.' 終了  ------ '.$current_date.' モード  ------ '.$current_result->mode.' ---- $calc_nobreak_cnt = '.$calc_nobreak_cnt );
                    Log::DEBUG('        残り　当日分　勤務状態 =  '.$working_status);
                    Log::DEBUG('            temp_working_time_datesデータ作成開始 ');
                    Log::DEBUG('                現ユーザーを登録 '.$current_user_code);
                    $add_result = $this->addTempWorkingTimeDate(
                        $current_date,
                        $current_user_code,
                        $current_department_code,
                        $current_result,
                        $note,
                        $working_status,
                        $array_calc_time,
                        $array_missing_middle_time,
                        $array_public_going_out_time,
                        $array_add_attendance_time,
                        $array_add_leaving_time,
                        $array_add_missing_middle_time,
                        $array_add_missing_middle_return_time,
                        $array_add_public_going_out_time,
                        $array_add_public_going_out_return_time);
                }catch(\PDOException $pe){
                    $add_result = false;
                    Log::DEBUG('PDOException');
                }catch(\Exception $e){
                    Log::DEBUG('Exception'.$e->getMessage());
                    $add_result = false;
                }
                Log::DEBUG('        temp_working_time_datesデータ作成終了 ');
            }
        }

        Log::DEBUG('---------------------- calcTempWorkingTimeDate end ------------------------ ');
        return $add_result;

    }
    

    /**
     * 労働時間計算
     *
     *
     * @param  $inc              ： 1:出勤・退勤 2:私用外出・私用外出戻り 3:公用外出・公用外出戻り
     * @param  $target_from_time ： 開始時刻（出勤・私用外出）
     * @param  $target_to_time   ： 終了時刻（退勤・私用外出戻り）
     * @return 計算結果時間
     */
    private function calcTimes($inc, $timetables, $working_timetable_no, $working_time_kubun, $current_date,$target_from_time, $target_to_time,
        $array_calc_time, $array_gouing_out_time)
    {
        Log::DEBUG('---------------------- calcTimes in working_time_kubun = '.$working_time_kubun.' ------------------------ ');
        $apicommon = new ApiCommonController();
        $working_times = 0;             // 労働時間
        $calc_times = 0;
        $calc_times_regular = 0;

        // 労働時間区分の開始終了時刻を取得
        // タイムテーブルをnoとworking_time_kubunで特定
        $array_times = $apicommon->analyzeTimeTable($timetables, $working_time_kubun, $working_timetable_no);

        foreach($array_times as $result_time) {
            // 時間登録の開始時間
            $from_time = $result_time['from_time'];
            // 時間登録の終了時間
            $to_time = $result_time['to_time'];
            if (isset($from_time) && isset($to_time)) {
                // from_time日付付与
                $working_time_from_time = $apicommon->convTimeToDateFrom($from_time, $current_date, $target_from_time, $target_to_time);         
                $working_time_calc_from = $working_time_from_time;
                // to_time日付付与
                $working_time_to_time = $apicommon->convTimeToDateTo($from_time, $to_time, $current_date, $target_from_time, $target_to_time);         
                $working_time_calc_to = $working_time_to_time;
                Log::DEBUG('working_time_kubun = '.$working_time_kubun);
                Log::DEBUG('target_from_time = '.$target_from_time);
                Log::DEBUG('target_to_time = '.$target_to_time);
                Log::DEBUG('working_time_calc_from = '.$working_time_calc_from);
                Log::DEBUG('working_time_calc_to = '.$working_time_calc_to);
                Log::DEBUG('inc = '.$inc);
                // 深夜労働残業時間以外の場合
                if ($working_time_kubun != Config::get('const.C004.out_of_regular_night_working_time') ||
                    $inc == Config::get('const.INC_NO.missing_return') ||
                    $inc == Config::get('const.INC_NO.public_going_out_return')) {
                    if ($apicommon->chkBetweenTime($target_from_time, $target_to_time, $working_time_calc_from, $working_time_calc_to)) {
                        if ($working_time_calc_from < $working_time_calc_to) {
                            if ($target_from_time > $working_time_calc_from) {
                                $working_time_calc_from = $target_from_time;
                            }
                            if ($target_to_time < $working_time_calc_to) {
                                $working_time_calc_to = $target_to_time;
                            }
                            Log::DEBUG('diffTimeSerial working_time_calc_from = '.$working_time_calc_from);
                            Log::DEBUG('diffTimeSerial working_time_calc_to = '.$working_time_calc_to);
                            $calc_times = $apicommon->diffTimeSerial($working_time_calc_from, $working_time_calc_to);
                            Log::DEBUG('calc_times = '.$calc_times);
                            $working_times += $calc_times;
                            Log::DEBUG('$working_times = '.$working_times);
                        }
                    }
                    // 夜勤の場合は打刻target_from_time、target_to_timeが翌日の場合があるため
                    // working_time_calc_from、working_time_calc_toを翌日にして計算する
                    $working_time_calc_from_nextday = $apicommon->getNextDay($working_time_from_time, 'Y-m-d H:i:s');
                    $working_time_calc_to_nextday = $apicommon->getNextDay($working_time_to_time, 'Y-m-d H:i:s');
                    Log::DEBUG(' working_time_calc_from_nextday = '.$working_time_calc_from_nextday);
                    Log::DEBUG(' working_time_calc_to_nextday = '.$working_time_calc_to_nextday);
                    if ($apicommon->chkBetweenTime($target_from_time, $target_to_time, $working_time_calc_from_nextday, $working_time_calc_to_nextday)) {
                        Log::DEBUG('夜勤の場合は打刻target_from_time、target_to_timeが翌日の場合があるための計算対象');
                        if ($working_time_calc_from_nextday < $working_time_calc_to_nextday) {
                            if ($target_from_time > $working_time_calc_from) {
                                $working_time_calc_from_nextday = $target_from_time;
                            }
                            if ($target_to_time < $working_time_calc_to_nextday) {
                                $working_time_calc_to_nextday = $target_to_time;
                            }
                            Log::DEBUG('diffTimeSerial working_time_calc_from_nextday = '.$working_time_calc_from_nextday);
                            Log::DEBUG('diffTimeSerial working_time_calc_to_nextday = '.$working_time_calc_to_nextday);
                            $calc_times = $apicommon->diffTimeSerial($working_time_calc_from_nextday, $working_time_calc_to_nextday);
                            Log::DEBUG('calc_times = '.$calc_times);
                            $working_times += $calc_times;
                            Log::DEBUG('$working_times = '.$working_times);
                        }
                    }
                } else {
                    // 深夜労働残業時間
                    Log::DEBUG('【深夜労働残業時間 計算開始】');
                    $w_time = 0;
                    // target_to_timeは退勤時刻
                    Log::DEBUG('$target_to_time = '.$target_to_time);
                    // 退勤時刻 > 深夜残業開始の場合
                    Log::DEBUG('$working_time_calc_from = '.$working_time_calc_from);
                    if ($target_to_time > $working_time_calc_from) {
                        Log::DEBUG('深夜労働残業時間 計算判断対象 $target_to_time > $working_time_calc_from ');
                        // ここまでに計算された労働時間と私用外出時間から労働時間を算出
                        $index = (int)(Config::get('const.C004.regular_working_time'))-1;
                        $w_time += $array_calc_time[$index];
                        Log::DEBUG('$w_time $array_calc_time 1 = '.$w_time.'  '.$array_calc_time[$index]);
                        $index = (int)(Config::get('const.C004.regular_working_breaks_time'))-1;
                        $w_time -= $array_calc_time[$index];
                        Log::DEBUG('$w_time $array_calc_time 2 = '.$w_time.'  '.$array_calc_time[$index]);
                        $index = (int)(Config::get('const.C004.out_of_regular_working_time'))-1;
                        $w_time += $array_calc_time[$index];
                        Log::DEBUG('$w_time $array_calc_time 3 = '.$w_time.'  '.$array_calc_time[$index]);
                        $index = (int)(Config::get('const.C004.regular_working_time'))-1;
                        $w_time -= $array_gouing_out_time[$index];
                        Log::DEBUG('$w_time $array_gouing_out_time = '.$w_time.'  '.$array_gouing_out_time[$index]);
                        $index = (int)(Config::get('const.C004.out_of_regular_working_time'))-1;
                        $w_time -= $array_gouing_out_time[$index];
                        Log::DEBUG('$w_time $array_gouing_out_time = '.$w_time.'  '.$array_gouing_out_time[$index]);
                        Log::DEBUG('legal_working_hours_day = '.(double)Config::get('const.C002.legal_working_hours_day') * 60 * 60);
                        // ここまでに計算された労働時間が8Hを超えている場合は深夜残業を計算する
                        if ($w_time > (double)Config::get('const.C002.legal_working_hours_day') * 60 * 60) {
                            // 退勤時刻<=深夜残業終了
                            Log::DEBUG('深夜労働残業時間 8H超え計算対象 $w_time > 8 ');
                            Log::DEBUG('退勤時刻<=深夜残業終了か？ $target_to_time = '.$target_to_time);
                            Log::DEBUG('退勤時刻<=深夜残業終了か？ $working_time_calc_from = '.$working_time_calc_from);
                            Log::DEBUG('退勤時刻<=深夜残業終了か？ $working_time_calc_to = '.$working_time_calc_to);
                            if ($target_to_time <= $working_time_calc_to) {
                                // 出勤時刻<=深夜残業開始
                                if ($target_from_time <= $working_time_calc_from) {
                                    // 深夜残業開始から退勤時刻を深夜残業とする
                                    $calc_times = $apicommon->diffTimeSerial($working_time_calc_from, $target_to_time);
                                } else {
                                    // 出勤時刻から退勤時刻を深夜残業とする
                                    $calc_times = $apicommon->diffTimeSerial($target_from_time, $target_to_time);
                                }
                            } else {
                                // 出勤時刻<=深夜残業開始
                                if ($target_from_time <= $working_time_calc_from) {
                                    // 深夜残業開始から深夜残業終了を深夜残業とする
                                    $calc_times = $apicommon->diffTimeSerial($working_time_calc_from, $working_time_calc_to);
                                } else {
                                    // 出勤時刻から深夜残業終了を深夜残業とする
                                    $calc_times = $apicommon->diffTimeSerial($target_from_time, $working_time_calc_to);
                                }
                            }
                            Log::DEBUG('$calc_times = '.$calc_times);
                            $working_times += $calc_times;
                            Log::DEBUG('$working_times = '.$working_times);
                        }
                        // さらにシフト勤務などは所定労働時間と重複する時間となりうるので、重複時間があれば減算する。
                        $filtered_regular = 
                            $timetables->where('no', $working_timetable_no)->where('working_time_kubun', Config::get('const.C004.regular_working_time'));
                        foreach($filtered_regular as $result_regular) {
                            Log::DEBUG('重複時間 $result_regular->from_time = '.$result_regular->from_time);
                            Log::DEBUG('重複時間 $result_regular->to_time = '.$result_regular->to_time);
                            // 所定労働時間登録の開始時間
                            $from_time_regular = $result_regular->from_time;
                            // 所定労働時間登録の終了時間
                            $to_time_regular = $result_regular->to_time;
                            if (isset($from_time_regular) && isset($to_time_regular)) {
                                // from_time_regular日付付与
                                // fromdate
                                $working_time_calc_from_regular = 
                                    $apicommon->convTimeToDateFrom($from_time_regular, $current_date, $target_from_time, $target_to_time);         
                                // to_time_regular日付付与
                                $working_time_calc_to_regular = 
                                    $apicommon->convTimeToDateTo($from_time_regular, $to_time_regular, $current_date, $target_from_time, $target_to_time);         
                                Log::DEBUG('重複時間 所定労働時間 $working_time_calc_from_regular = '.$working_time_calc_from_regular);
                                Log::DEBUG('重複時間 所定労働時間 $working_time_calc_to_regular = '.$working_time_calc_to_regular);
                                Log::DEBUG('重複時間 所定労働時間 $working_time_calc_from = '.$working_time_calc_from);
                                Log::DEBUG('重複時間 所定労働時間 $working_time_calc_to = '.$working_time_calc_to);
                                if (($working_time_calc_from > $working_time_calc_from_regular && $working_time_calc_from < $working_time_calc_to_regular) ||
                                    ($working_time_calc_to > $working_time_calc_from_regular && $working_time_calc_to < $working_time_calc_to_regular)) {
                                    // 時間登録の終了時間<=所定労働時間登録終了
                                    if ($working_time_calc_to <= $working_time_calc_to_regular) {
                                        // 所定労働時間登録開始<= 時間登録の開始時間
                                        if ($working_time_calc_from_regular <= $working_time_calc_from) {
                                            // 時間登録の開始時間から時間登録の終了時間を計算する
                                            $calc_times_regular = $apicommon->diffTimeSerial($working_time_calc_from, $working_time_calc_to);
                                        } else {
                                            // 所定労働時間登録開始から時間登録の終了を計算する
                                            $calc_times_regular = $apicommon->diffTimeSerial($working_time_calc_from_regular, $working_time_calc_to);
                                        }
                                    } else {
                                        // 所定労働時間登録開始<=時間登録の開始時間
                                        if ($working_time_calc_from_regular <= $working_time_calc_from) {
                                            // 時間登録の開始時間から所定労働時間登録終了を計算する
                                            $calc_times_regular = $apicommon->diffTimeSerial($working_time_calc_from, $working_time_calc_to_regular);
                                        } else {
                                            // 所定労働時間登録開始から所定労働時間登録終了を計算する
                                            $calc_times_regular = $apicommon->diffTimeSerial($working_time_calc_from_regular, $working_time_calc_to_regular);
                                        }
                                    }
                                }
                            }
                            break;
                        }       
                        Log::DEBUG('$calc_times_regular = '.$calc_times_regular);
                        $working_times -= $calc_times_regular;
                        Log::DEBUG('$working_times = '.$working_times);
                    }
                }
                // 休憩時間を含んでいる場合、休憩時間累計（所定労働時間内の休憩時間を累計することになる）
                if ($working_times != 0) {
                    // 休憩時間を含んでいる場合、休憩時間累計を求めて減算する
                    $filtered = $timetables->where('no', $working_timetable_no)
                        ->where('working_time_kubun', Config::get('const.C004.regular_working_breaks_time'));
                    // 休憩時間帯は複数あるかも
                    foreach($filtered as $result_breaks_time) {
                        $from_time = $result_breaks_time->from_time;        // 休憩開始時刻
                        $to_time = $result_breaks_time->to_time;            // 休憩終了時刻
                        Log::DEBUG('休憩時間 from_time = '.$from_time);
                        Log::DEBUG('休憩時間 to_time = '.$to_time);
                        if (isset($from_time) && isset($to_time)) {
                            // from_time日付付与
                            $time_calc_from = 
                                $apicommon->convTimeToDateFrom($from_time, $current_date, $target_from_time, $target_to_time);         
                            // to_time日付付与
                            $time_calc_to = 
                                $apicommon->convTimeToDateTo($from_time, $to_time, $current_date, $target_from_time, $target_to_time);         
                            Log::DEBUG('休憩時間 time_calc_from = '.$time_calc_from);
                            Log::DEBUG('休憩時間 time_calc_to = '.$time_calc_to);
                            Log::DEBUG('休憩時間 working_time_calc_from = '.$working_time_calc_from);
                            Log::DEBUG('休憩時間 working_time_calc_to = '.$working_time_calc_to);
                            Log::DEBUG('休憩時間 target_from_time = '.$target_from_time);
                            Log::DEBUG('休憩時間 target_to_time = '.$target_to_time);
                            //  計算対象のタイムテーブルの開始終了日時の範囲内に休憩開始終了時刻がある場合で
                            if (($time_calc_from > $working_time_calc_from && $time_calc_from < $working_time_calc_to) ||
                                ($time_calc_to > $working_time_calc_from && $time_calc_to < $working_time_calc_to)) {
                                //  出退勤時間の範囲内に休憩開始終了時刻がある場合に計算する
                                if (($time_calc_from > $target_from_time && $time_calc_from < $target_to_time) ||
                                    ($time_calc_to > $target_from_time && $time_calc_to < $target_to_time)) {
                                    if ($target_from_time > $time_calc_from) {
                                        $time_calc_from = $target_from_time;
                                    }
                                    if ($target_to_time < $time_calc_to) {
                                        $time_calc_to = $target_to_time;
                                    }
                                    Log::DEBUG('time_calc_from = '.$time_calc_from);
                                    Log::DEBUG('time_calc_to = '.$time_calc_to);
                                    if ($time_calc_from < $time_calc_to) {
                                        $calc_times = $apicommon->diffTimeSerial($time_calc_from, $time_calc_to);
                                        Log::DEBUG('休憩時間 calc_times = '.$calc_times);
                                        $working_times -= $calc_times;
                                        Log::DEBUG('休憩時間 $working_times = '.$working_times);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        Log::DEBUG('calcTimes end '.$working_times);
        Log::DEBUG('---------------------- calcTimes end ------------------------ ');

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
        Log::DEBUG('---------------------- calcBreakTimes in working_time_kubun = '.$working_time_kubun.' ------------------------ ');
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
                Log::DEBUG('from_time = '.$from_time);
                Log::DEBUG('to_time = '.$to_time);
                Log::DEBUG('current_date = '.$current_date);
                Log::DEBUG('attendance_time = '.$attendance_time);
                Log::DEBUG('leaving_time = '.$leaving_time);
                Log::DEBUG('working_time_calc_from = '.$working_time_calc_from);
                Log::DEBUG('working_time_calc_to = '.$working_time_calc_to);
                // 出退勤の範囲内であれば計算
                if (($working_time_calc_from > $attendance_time && $working_time_calc_from < $leaving_time) ||
                    ($working_time_calc_to > $attendance_time && $working_time_calc_to < $leaving_time)) {
                    if ($attendance_time > $working_time_calc_from) {
                        $working_time_calc_from = $attendance_time;
                        Log::DEBUG('working_time_calc_from if then= '.$working_time_calc_from);
                    }
                    if ($leaving_time < $working_time_calc_to) {
                        $working_time_calc_to = $leaving_time;
                        Log::DEBUG('working_time_calc_to if then= '.$working_time_calc_to);
                    }
                    Log::DEBUG('working_time_kubun = '.$working_time_kubun);
                    Log::DEBUG('attendance_time = '.$attendance_time);
                    Log::DEBUG('leaving_time = '.$leaving_time);
                    Log::DEBUG('working_time_calc_from = '.$working_time_calc_from);
                    Log::DEBUG('working_time_calc_to = '.$working_time_calc_to);
                    if ($working_time_calc_from < $working_time_calc_to) {
                        $calc_times = $apicommon->diffTimeSerial($working_time_calc_from, $working_time_calc_to);
                        Log::DEBUG('calc_times = '.$calc_times);
                        $working_times += $calc_times;
                        Log::DEBUG('$working_times = '.$working_times);
                    }
                }
            }
        }
        Log::DEBUG('calcBreakTimes end '.$working_times);
        Log::DEBUG('---------------------- calcBreakTimes end '.$working_times.'------------------------ ');

        return $working_times;
    }

    /**
     * ユーザー労働時間登録（temp）
     *
     * @return 登録結果
     */
    private function addTempWorkingTimeDate($target_date, $target_user_code, $target_department_code, $target_result, $note, $working_status,
        $array_calc_time, $array_missing_middle_time, $array_public_going_out_time,
        $array_add_attendance_time, $array_add_leaving_time, $array_add_missing_middle_time, $array_add_missing_middle_return_time,
        $array_add_public_going_out_time, $array_add_public_going_out_return_time)
    {
        Log::DEBUG('---------------------- addTempWorkingTimeDate in ------------------------ ');
        $temp_working_model = new TempWorkingTimeDate();
        $apicommon = new ApiCommonController();

        $temp_working_model->setWorkingdateAttribute(date_format(new Carbon($target_date), 'Ymd'));
        $temp_working_model->setEmploymentstatusAttribute($target_result->employment_status);
        $temp_working_model->setDepartmentcodeAttribute($target_department_code);
        $temp_working_model->setUsercodeAttribute($target_user_code);
        $temp_working_model->setEmploymentstatusnameAttribute($target_result->employment_status_name);
        $temp_working_model->setDepartmentnameAttribute($target_result->department_name);
        $temp_working_model->setUsernameAttribute($target_result->user_name);
        $temp_working_model->setWorkingtimetablenoAttribute($target_result->working_timetable_no);
        $temp_working_model->setWorkingtimetablenameAttribute($target_result->working_timetable_name);
        for ($i=0;$i<count($array_calc_time);$i++) {
            if (count($array_calc_time) > 0 && $i < count($array_calc_time)){
                Log::DEBUG('        $array_calc_time[$i] = '.$array_calc_time[$i].' $i = '.$i);
            } else {
                Log::DEBUG('        $array_calc_time[$i] = null $i = '.$i);
            }
        }
        // 出勤打刻５回までチェック
        $attendence_note_set = false;
        $array_add_attendance_time_cnt = count($array_add_attendance_time);
        if (!$this->chkWorkingTime($array_add_attendance_time, (int)(Config::get('const.ARRAY_MAX_INDEX.attendace_time')) )) {
            $note .= Config::get('const.MEMO_DATA.MEMO_DATA_016').' '; 
            $attendence_note_set = true;
        }
        // 設定
        $array_decide_times = $this->decideWorkingTimeFrom($array_add_attendance_time, count($array_add_attendance_time));
        for ($i=0;$i<count($array_decide_times);$i++) {
            if ($i<count($array_decide_times)) {
                $temp_working_model->setAttendancetimeAttribute($i, $array_decide_times[$i]);
            } else {
                $temp_working_model->setAttendancetimeAttribute($i, null);
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
                $temp_working_model->setLeavingtimeAttribute($i, $array_decide_times[$i]);
            } else {
                $temp_working_model->setLeavingtimeAttribute($i, null);
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
            } else {
                $temp_working_model->setMissingmiddletimeAttribute($i, null);
            }
        }
        // 中抜け戻り打刻５回までチェック
        if (!$this->chkWorkingTime($array_add_missing_middle_return_time,(int)(Config::get('const.ARRAY_MAX_INDEX.missing_middle_return_time')) )) {
            if ($missing_middle_note_set == false) {
                $note .= Config::get('const.MEMO_DATA.MEMO_DATA_018').' '; 
            }
        }
        // 設定
        $array_decide_times = $this->decideWorkingTimeTo(
            $array_add_missing_middle_return_time,
            count($array_add_missing_middle_return_time),
            $array_add_missing_middle_time,
            count($array_add_missing_middle_time));
        for ($i=0;$i<count($array_decide_times);$i++) {
            if ($i<count($array_decide_times)) {
                $temp_working_model->setMissingmiddlereturntimeAttribute($i, $array_decide_times[$i]);
            } else {
                $temp_working_model->setMissingmiddlereturntimeAttribute($i, null);
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
            } else {
                $temp_working_model->setPublicgoingouttimeAttribute($i, null);
            }
        }
        // 公用外出戻り打刻５回までチェック
        if (!$this->chkWorkingTime($array_add_public_going_out_return_time,(int)(Config::get('const.ARRAY_MAX_INDEX.public_going_out_return_time')) )) {
            if ($public_going_out_note_set == false) {
                $note .= Config::get('const.MEMO_DATA.MEMO_DATA_017').' '; 
            }
        }
        // 設定
        $array_decide_times = $this->decideWorkingTimeTo(
            $array_add_public_going_out_return_time,
            count($array_add_public_going_out_return_time),
            $array_add_public_going_out_time,
            count($array_add_public_going_out_time));
        for ($i=0;$i<count($array_decide_times);$i++) {
            if ($i<count($array_decide_times)) {
                $temp_working_model->setPublicgoingoutreturntimeAttribute($i, $array_decide_times[$i]);
            } else {
                $temp_working_model->setPublicgoingoutreturntimeAttribute($i, null);
            }
        }
        // 合計勤務時間
        $total_time = 0;
        // 残業時間
        $overtime_hours = 0;
        // 所定労働時間
        $index = (int)(Config::get('const.C004.regular_working_time'))-1;
        $w_time = $array_calc_time[$index] - $array_missing_middle_time[$index];
        $regular_calc_time = round($apicommon->roundTime($w_time, $target_result->time_unit, $target_result->time_rounding) / 60,2);
        Log::DEBUG('$target_user_code = '.$target_user_code.' 所定労働時間 = $total_time + $regular_calc_time '.$total_time.' '.$regular_calc_time);
        // 時間外労働時間
        $index = (int)(Config::get('const.C004.out_of_regular_working_time'))-1;
        $w_time = $array_calc_time[$index] - $array_missing_middle_time[$index];
        Log::DEBUG('$target_user_code = '.$target_user_code.' 時間外労働時間 = $array + $array '.$array_calc_time[$index].' '.$array_missing_middle_time[$index]);
        $calc_time = round($apicommon->roundTime($w_time, $target_result->time_unit, $target_result->time_rounding) / 60,2);
        // 平日は時間外労働時間＝残業時間
        // ---- 取り消し--休日は所定労働時間+時間外労働時間>8の場合、所定労働時間+時間外労働時間-8=残業時間
        // 休日は残業時間は単価は1.25で休日の労働時間同じなので休日の労働時間に加算
        if ($target_result->business_kubun == Config::get('const.C007.basic')) {
            $temp_working_model->setOffhoursworkinghoursAttribute($calc_time);
        } else {
            $temp_calc = $regular_calc_time + $calc_time;       // 所定労働時間+時間外労働時間
            $calc_time = $temp_calc;
            $regular_calc_time = 0;
            /*if ($temp_calc > Config::get('const.C002.legal_working_hours_day')) {
                $regular_calc_time = Config::get('const.C002.legal_working_hours_day');
                $calc_time = $temp_calc - Config::get('const.C002.legal_working_hours_day');
            } else {
                $regular_calc_time = $temp_calc;
                $calc_time = 0;
            } */
            $temp_working_model->setOffhoursworkinghoursAttribute($temp_calc);
        }
        $temp_working_model->setRegularworkingtimesAttribute($regular_calc_time);   // 所定労働時間
        $total_time = $total_time + $regular_calc_time;
        $total_time = $total_time + $calc_time;
        $overtime_hours = $overtime_hours + $calc_time;
        Log::DEBUG('$target_user_code = '.$target_user_code.' 時間外労働時間 = $overtime_hours + $calc_time '.$overtime_hours.' '.$calc_time);
        // 深夜労働残業時間
        $index = (int)(Config::get('const.C004.out_of_regular_night_working_time'))-1;
        $w_time = $array_calc_time[$index] - $array_missing_middle_time[$index];
        Log::DEBUG('$target_user_code = '.$target_user_code.' 深夜労働残業時間 = $array + $array '.$array_calc_time[$index].' '.$array_missing_middle_time[$index]);
        $calc_time = round($apicommon->roundTime($w_time, $target_result->time_unit, $target_result->time_rounding) / 60,2);
        Log::DEBUG('深夜労働残業時間 $calc_time = '.$calc_time);
        $temp_working_model->setLatenightovertimehoursAttribute($calc_time);
        $total_time = $total_time + $calc_time;
        Log::DEBUG('$total_time = '.$total_time);
        Log::DEBUG('$target_user_code = '.$target_user_code.' 残業時間 = $overtime_hours + $calc_time '.$overtime_hours.' '.$calc_time);
        // 残業時間
        $temp_working_model->setOvertimehoursAttribute($overtime_hours);
        // 所定外労働時間
        $outside_calc_time = 0;
        $default_time = (int)(Config::get('const.C002.legal_working_hours_day'));
        Log::DEBUG('所定外労働時間計算 $default_time = '.$default_time);
        Log::DEBUG('所定外労働時間計算 $regular_calc_time = '.$regular_calc_time);
        Log::DEBUG('所定外労働時間計算 $total_time = '.$total_time);
        if ($regular_calc_time < $default_time && $total_time > $default_time) {    // 所定労働時間 < 8 and 合計勤務時間 > 8 の場合
            $outside_calc_time = $default_time - $regular_calc_time;
        } elseif ($regular_calc_time < $total_time) { 
            $outside_calc_time = $total_time- $regular_calc_time;
        } 

        $temp_working_model->setOutofregularworkingtimesAttribute($outside_calc_time);
        Log::DEBUG('$target_user_code = '.$target_user_code.' 所定外労働時間 = $outside_calc_time '.$outside_calc_time);
        // 法定労働時間 法定外労働時間
        if ($total_time > $default_time) {      // 合計勤務時間 > 8 の場合
            // 法定労働時間
            $temp_working_model->setLegalworkingtimesAttribute($default_time);
            // 法定外労働時間
            $temp_working_model->setOutoflegalworkingtimesAttribute($total_time - $default_time);
            Log::DEBUG('$target_user_code = '.$target_user_code.' 法定労働時間 = $default_time '.$default_time);
            Log::DEBUG('$target_user_code = '.$target_user_code.' 法定外労働時間 = $total_time - $default_time '.$total_time.' '.$default_time);
        } else {
            // 法定労働時間
            $temp_working_model->setLegalworkingtimesAttribute($total_time);
            // 法定外労働時間
            $temp_working_model->setOutoflegalworkingtimesAttribute(0);
            Log::DEBUG('$target_user_code = '.$target_user_code.' 法定労働時間 = $total_time '.$total_time);
            Log::DEBUG('$target_user_code = '.$target_user_code.' 法定外労働時間 =0 ');
        }
        // 未就労時間（規則所定労働時間-実所定労働時間）
        // 規則所定労働時間を求める
        $timetable_model = new WorkingTimeTable();
        $timetable_model->setParamdatefromAttribute($target_date);
        $timetable_model->setParamdatetoAttribute($target_date);
        $timetable_model->setParamemploymentstatusAttribute($target_result->employment_status);
        $timetable_model->setParamDepartmentcodeAttribute($target_department_code);
        $timetable_model->setParamUsercodeAttribute($target_user_code);
        // 平日は設定している所定労働時間を求める
        // 休日は所定労働時間=8時間であるが、（所定という概念ではない）
        if ($target_result->business_kubun == Config::get('const.C007.basic')) {
            $timetables = $timetable_model->getWorkingTimeTableJoin();
            Log::DEBUG('$getWorkingTimeTableJoin  count = '.count($timetables));
            $calc_time = 0;
            if (count($timetables) > 0) {
                $w_time = 0;
                $w_break_time = 0;
                $w_from_time1 = "";
                $w_from_time2 = "";
                $w_to_time1 = "";
                $w_to_time2 = "";
                Log::DEBUG('$target_date = '.$target_date);
                $target_dt = new Carbon($target_date);
                Log::DEBUG('$target_dt = '.$target_dt);
                foreach($timetables as $item) {
                    if ($item->working_time_kubun == Config::get('const.C004.regular_working_time')) {
                        Log::DEBUG('$working_time_kubun = '.$item->working_time_kubun);
                        if (isset($item->from_time) && isset($item->to_time)) {
                            if ($item->from_time < $item->to_time) {
                                Log::DEBUG('$item->from_time = '.$item->from_time);
                                Log::DEBUG('$item->to_time = '.$item->to_time);
                                $from_time = date_format($target_dt, 'Y-m-d').' '.$item->from_time;
                                $to_time = date_format($target_dt, 'Y-m-d').' '.$item->to_time;
                                Log::DEBUG('規則所定労働時間を求める 1 from_time = '.$from_time);
                                Log::DEBUG('規則所定労働時間を求める 2 to_time = '.$to_time);
                                $w_time += $apicommon->diffTimeSerial($from_time, $to_time);
                                $w_from_time1 = $item->from_time;
                                $w_to_time1 = $item->to_time;
                                $w_from_time2 = $item->from_time;
                                $w_to_time2 = $item->to_time;
                            } else {
                                Log::DEBUG('$item->from_time = '.$item->from_time);
                                Log::DEBUG('$item->to_time = '.$item->to_time);
                                $from_time = date_format($target_dt, 'Y-m-d').' '.$item->from_time;
                                $to_time = date_format($target_dt, 'Y-m-d').' '.$item->to_time;
                                $nextdt =$apicommon->getNextDay(new Carbon($to_time), 'Y/m/d');
                                $to_time = date_format(new Carbon($nextdt), 'Y-m-d').' 00:00:00';
                                Log::DEBUG('規則所定労働時間を求める 3 from_time = '.$from_time);
                                Log::DEBUG('規則所定労働時間を求める 4 to_time = '.$to_time);
                                $w_time += $apicommon->diffTimeSerial($from_time, $to_time);
                                $w_from_time1 = $item->from_time;
                                $w_to_time1 = '00:00:00';
                                $from_time = $to_time;
                                $to_time = date_format(new Carbon($nextdt), 'Y-m-d').' '.$item->to_time;
                                Log::DEBUG('規則所定労働時間を求める 5 from_time = '.$from_time);
                                Log::DEBUG('規則所定労働時間を求める 6 to_time = '.$to_time);
                                $w_time += $apicommon->diffTimeSerial($from_time, $to_time);
                                $w_from_time2 = '00:00:00';
                                $w_to_time2 = $item->to_time;
                            }
                        }
                        Log::DEBUG('規則所定労働時間 w_time = '.$w_time);
                        Log::DEBUG('規則所定労働時間 w_from_time1 = '.$w_from_time1);
                        Log::DEBUG('規則所定労働時間 w_to_time1 = '.$w_to_time1);
                        Log::DEBUG('規則所定労働時間 w_from_time2 = '.$w_from_time2);
                        Log::DEBUG('規則所定労働時間 w_to_time2 = '.$w_to_time2);
                    }
                    // 所定労働時間内の休憩の場合はその分を減算する
                    if ($item->working_time_kubun == Config::get('const.C004.regular_working_breaks_time')) {
                        if (isset($item->from_time) && isset($item->to_time)) {
                            Log::DEBUG('規則休憩時間 $item->from_time = '.$item->from_time);
                            Log::DEBUG('規則休憩時間 $item->to_time = '.$item->to_time);
                            Log::DEBUG('規則休憩時間 w_from_time1 = '.$w_from_time1);
                            Log::DEBUG('規則休憩時間 w_to_time1 = '.$w_to_time1);
                            Log::DEBUG('規則休憩時間 w_from_time2 = '.$w_from_time2);
                            Log::DEBUG('規則休憩時間 w_to_time2 = '.$w_to_time2);
                            if (($item->from_time > $w_from_time1 && $item->from_time < $w_to_time1) ||
                                ($item->from_time > $w_from_time2 && $item->from_time < $w_to_time2)) {
                                if ($item->from_time < $item->to_time) {
                                    $from_time = date_format($target_dt, 'Y-m-d').' '.$item->from_time;
                                    $to_time = date_format($target_dt, 'Y-m-d').' '.$item->to_time;
                                    Log::DEBUG('規則休憩時間を求める 1 from_time = '.$from_time);
                                    Log::DEBUG('規則休憩時間を求める 2 to_time = '.$to_time);
                                    $w_break_time += $apicommon->diffTimeSerial($from_time, $to_time);
                                } else {
                                    $from_time = date_format($target_dt, 'Y-m-d').' '.$item->from_time;
                                    $to_time = date_format($target_dt, 'Y-m-d').' '.$item->to_time;
                                    $nextdt =$apicommon->getNextDay(new Carbon($to_time), 'Y/m/d');
                                    $to_time = date_format($nextdt, 'Y-m-d').' 00:00:00';
                                    Log::DEBUG('規則休憩時間を求める 3 from_time = '.$from_time);
                                    Log::DEBUG('規則休憩時間を求める 4 to_time = '.$to_time);
                                    $w_break_time += $apicommon->diffTimeSerial($from_time, $to_time);
                                    $from_time = $to_time;
                                    $to_time = date_format(new Carbon($nextdt), 'Y-m-d').' '.$item->to_time;
                                    Log::DEBUG('規則所定労働時間を求める 5 from_time = '.$from_time);
                                    Log::DEBUG('規則所定労働時間を求める 6 to_time = '.$to_time);
                                    $w_break_time += $apicommon->diffTimeSerial($from_time, $to_time);
                                }
                            }
                        }
                        Log::DEBUG('規則所定労働時間 w_break_time = '.$w_break_time);
                    }
                }
            }
            if ($regular_calc_time > 0) {
                $w_calc_time = ($w_time / 60 / 60) - ($w_break_time / 60 / 60) - $regular_calc_time;
                $temp_working_model->setNotemploymentworkinghoursAttribute($w_calc_time);
                Log::DEBUG('未就労時間  w_time = '.$w_time.' '.($w_time / 60 / 60));
                Log::DEBUG('未就労時間  w_break_time = '.$w_break_time.' '.($w_break_time / 60 / 60));
                Log::DEBUG('未就労時間  regular_calc_time = '.$regular_calc_time);
            } else {
                Log::DEBUG('未就労時間  = 0');
                $temp_working_model->setNotemploymentworkinghoursAttribute(0);
            }
        } else {
            Log::DEBUG('未就労時間  = 0');
            $temp_working_model->setNotemploymentworkinghoursAttribute(0);
        }

        // 休憩時間
        $calc_time = 0;
        if ($regular_calc_time > 0) {
            $calc_time +=  $this->not_employment_working;
            Log::DEBUG('未就労時間 休憩時間 = '.$this->not_employment_working);
        }
        // 私用外出時間
        $calc_missing_time = 0;
        for ($i=0;$i<count($array_missing_middle_time);$i++) {
            $calc_missing_time += $array_missing_middle_time[$i];
            Log::DEBUG('未就労時間 私用外出時間 = '.$array_missing_middle_time[$i]);
        }
        $calc_time = round($apicommon->roundTime($calc_time + $calc_missing_time, $target_result->time_unit, $target_result->time_rounding) / 60,2);
        $calc_missing_time = round($apicommon->roundTime($calc_missing_time, $target_result->time_unit, $target_result->time_rounding) / 60,2);
        Log::DEBUG('$target_user_code = '.$target_user_code.' 未就労時間（休憩時間＋私用外出時間） =  '. $calc_time.' + '.$calc_missing_time);
        $temp_working_model->setMissingmiddlehoursAttribute($calc_missing_time);
        // 公用外出時間
        $calc_time = 0;
        for ($i=0;$i<count($array_public_going_out_time);$i++) {
            $calc_time += $array_public_going_out_time[$i];
            Log::DEBUG('公用外出時間 = '.$array_public_going_out_time[$i]);
        }
        // 合計勤務時間
        $temp_working_model->setTotalworkingtimesAttribute($total_time);

        $calc_time = round($apicommon->roundTime($calc_time, $target_result->time_unit, $target_result->time_rounding) / 60,2);
        $temp_working_model->setPublicgoingouthoursAttribute($calc_time);
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
        $temp_working_model->setCheckintervalAttribute($target_result->check_interval);
        $temp_working_model->setYearAttribute($target_result->year);
        $temp_working_model->setPatternAttribute($target_result->pattern);
        $temp_working_model->setFixedtimeAttribute('0');
        $temp_working_model->setSystemDateAttribute(Carbon::now());
        // 登録する
        try{
            $temp_working_model->setParamdatefromAttribute(date_format(new Carbon($target_date), 'Ymd'));
            $temp_working_model->setParamdatetoAttribute(date_format(new Carbon($target_date), 'Ymd'));
            $temp_working_model->setParamEmploymentStatusAttribute($target_result->employment_status);
            $temp_working_model->setParamDepartmentcodeAttribute($target_department_code);
            $temp_working_model->setParamUsercodeAttribute($target_user_code);
            // insert
            $temp_working_model->insertTempWorkingTimeDate();
        }catch(\PDOException $pe){
            Log::ERROR('insertTempWorkingTimeDate PDOException '.$pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::ERROR('insertTempWorkingTimeDate Exception '.$e->getMessage());
            throw $e;
        }
        Log::DEBUG('---------------------- addTempWorkingTimeDate end ------------------------ ');

        return true;

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
        Log::DEBUG('---------------------- setNoteLateEtc in ------------------------ ');
        $note = '';
        $late = '';
        $leave_early = '';
        $to_be_confirmed = '';
        Log::DEBUG('        before $target_result->note = '.$target_result->note);
        if (isset($target_result->note)) { $note .= $target_result->note.' '; }
        Log::DEBUG('        after $target_result->note = '.$note);
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
        Log::DEBUG('---------------------- setNoteLateEtc end ------------------------ ');

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
        Log::DEBUG('---------------------- chkWorkingTime in ------------------------ ');
        // 打刻５回までチェック
        if (count($target_array_time) > $max_cnt) {
            return false;
        }
        Log::DEBUG('---------------------- chkWorkingTime end ------------------------ ');

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
        Log::DEBUG('---------------------- decideWorkingTimeFrom in ------------------------ ');
        $arrray_decide_times = array();
        for ($i=0;$i<$index;$i++) {
            if (count($target_array_time) > 0 && $i < count($target_array_time)){
                $arrray_decide_times[$i] = $target_array_time[$i];
            } else {
                $arrray_decide_times[$i] = null;
            }
        }
        Log::DEBUG('---------------------- decideWorkingTimeFrom end ------------------------ ');

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
        Log::DEBUG('---------------------- decideWorkingTimeTo in ------------------------ ');
        $arrray_decide_times = array();
        // 開始時刻より過去は設定しないため設定するindexも調整するためindexを別途準備
        $set_index = 0;
        $set_flg = false;
        for ($i=0;$i<$from_array_cnt;$i++) {
            $set_flg = false;
            for ($j=0;$j<$index;$j++) {
                if (count($target_array_time) > 0 && $i < count($target_array_time)){
                    // 開始時刻<target_array_timeを設定
                    if ($target_array_time[$j] > $from_array_time[$i]) {
                        $set_index++;
                        $arrray_decide_times[$set_index-1] = $target_array_time[$j];
                        $set_flg = true;
                        break;
                    }
                }
            }
            if (!$set_flg) {
                $set_index++;
                $arrray_decide_times[$set_index-1] = null;
            }
        }

        if ($from_array_cnt == 0) {
            $arrray_decide_times = $target_array_time;
        }
        Log::DEBUG('---------------------- decideWorkingTimeTo end ------------------------ ');

        return $arrray_decide_times;
    }

}