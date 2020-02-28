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
use App\Calendar;
use App\Http\Controllers\ApiCommonController;


class DailyWorkingInformationController extends Controller
{

    // 打刻データ配列
    private $array_working_mode = array();
    private $array_working_datetime = array();
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
        $working_time_dates = new collection();
        $array_working_time_dates = array();
        $array_working_time_attendances = array();
        $working_time_sum = new collection();

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
            // -------------- debug -------------- start --------
            if ($business_kubun == 1) {
                Log::debug('------------- 集計開始 日付 = '.$datefrom.' 出勤日　business_kubun = '.$business_kubun );
            } else if($business_kubun == 2) {
                Log::debug('------------- 集計開始 日付 = '.$datefrom.' 法定外休日　business_kubun = '.$business_kubun );
            } else {
                Log::debug('------------- 集計開始 日付 = '.$datefrom.' 法定休日　business_kubun = '.$business_kubun );
            }
            // -------------- debug -------------- end --------
            // パラメータの内容でworking_time_datesを削除
            $working_model = new WorkingTimedate();
            DB::beginTransaction();
            try{
                $working_model->setParamdatefromAttribute(date_format(new Carbon($datefrom), 'Ymd'));
                $working_model->setParamdatetoAttribute(date_format(new Carbon($dateto), 'Ymd'));
                $working_model->setParamEmploymentStatusAttribute($employmentstatus);
                $working_model->setParamDepartmentcodeAttribute($departmentcode);
                if ($working_model->isExistsWorkingTimeDate()) {
                    $working_model->delWorkingTimeDate();
                }
                // 日次集計
                $addCalc = $this->addDailyCalc(
                    $work_time,
                    $datefrom,
                    $dateto,
                    $employmentstatus,
                    $departmentcode,
                    $usercode,
                    $business_kubun);
                if ($addCalc) {
                    $working_model->setParamdatefromAttribute(date_format(new Carbon($datefrom), 'Ymd'));
                    $working_model->setParamdatetoAttribute(date_format(new Carbon($dateto), 'Ymd'));
                    $working_model->setParamEmploymentStatusAttribute($employmentstatus);
                    $working_model->setParamDepartmentcodeAttribute($departmentcode);
                    $working_model->setParamUsercodeAttribute($usercode);
                    // 集計結果
                    $working_time_dates = 
                        $working_model->getWorkingTimeDateTimeFormat(
                            Config::get('const.WORKINGTIME_DAY_OR_MONTH.daily_basic'),
                            $working_model->getParamdatefromAttribute(), $business_kubun);
                    // 合計結果
                    if (count($working_time_dates) > 0) {
                        $working_time_sum = $working_model->getWorkingTimeDateTimeSum(Config::get('const.WORKINGTIME_DAY_OR_MONTH.daily_basic'));
                    } else {
                        $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.not_workintime'));
                    }
                } else {
                    $add_result = false;
                }
                DB::commit();
                Log::debug('------------- 集計終了 日付 = '.$datefrom.' business_kubun = '.$business_kubun );
            }catch(\PDOException $pe){
                DB::rollBack();
                $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_error_dailycalc'));
            }catch(\Exception $e){
                DB::rollBack();
                $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_accesee_eror_dailycalc'));
                $add_result = false;
            }
        } else {
            Log::debug('------------- パラメータのチェック NG  ----------------');
            $add_result = false;
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
            // 集計結果配列設定
            $array_w = array();
            $array_w = $this->setArray_Working_time($value, $array_working_time_attendances);
            for ($i=0;$i<count($array_w);$i++) {
                $array_working_time_dates[] = $array_w[$i];
            }
        }

        // 開始日付のフォーマット 2019年10月01日(火)
        $date_name = $apicommon->getYMDWeek($datefrom);

        Log::debug('    集計結果　$array_working_time_dates = '.count($array_working_time_dates));
        Log::debug('    合計結果  $working_time_sum = '.count($working_time_sum));
        Log::debug('    メッセージ  $array_messagedata = '.count($this->array_messagedata));

        return response()->json(
            ['calcresults' => $array_working_time_dates,
                'sumresults' => $working_time_sum,
                'datename' => $date_name,
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
            // temporary削除処理
            try{
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
                            Log::debug('---------------- タイムテーブルあり -----------------------');
                            Log::debug('---------------- 日次集計登録(temp_working_time_dates) start -----------------------');
                            // 日次集計
                            $add_result = $this->calcTempWorkingTimeDate($timetables);
                            Log::debug('---------------- 日次集計登録(temp_working_time_dates) end -----------------------');
                        } else {
                            Log::debug('---------------- タイムテーブルなし -----------------------');
                            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.not_setting_timetable'));
                            Log::error(Config::get('const.LOG_MSG.not_setting_timetable'));
                            $add_result = false;
                        }
                    } else {
                        $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.not_workintime'));
                        Log::error(Config::get('const.LOG_MSG.not_workintime'));
                        $add_result = false;
                    }
                    //DB::commit();
                    //Log::debug('temporary commit');
                }catch(\PDOException $pe){
                    //DB::rollBack();
                    //Log::debug('temporary rollBack');
                    $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_accesee_eror_dailycalc'));
                    Log::error(Config::get('const.MSG_ERROR.data_accesee_eror_dailycalc'));
                    Log::error($pe->getMessage());
                    $add_result = false;
                    throw $pe;
                }catch(\Exception $e){
                    //DB::rollBack();
                    //Log::debug('temporary rollBack');
                    $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_error_dailycalc'));
                    Log::error(Config::get('const.LOG_MSG.data_error_dailycalc'));
                    Log::error($e->getMessage());
                    $add_result = false;
                    throw $e;
                }
            }catch(\PDOException $pe){
                //DB::rollBack();
                //Log::debug('temporary rollBack');
                $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_accesee_eror_dailycalc'));
                Log::error($pe->getMessage());
                $add_result = false;
                throw $pe;
            }
        } else {
            $add_result = false;
            Log::debug(Config::get('const.MSG_ERROR.not_workintime'));
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.not_workintime'));
        }

        // 出勤・退勤データtempから登録
        $working_time_dates = new Collection();
        $working_time_sum = new Collection();
        if ($add_result) {
            Log::debug(' ---- datefrom = '.$datefrom);
            Log::debug(' ---- dateto = '.$dateto);
            $temp_working_model->setParamdatefromAttribute(date_format(new Carbon($datefrom), 'Ymd'));
            $temp_working_model->setParamdatetoAttribute(date_format(new Carbon($dateto), 'Ymd'));
            $temp_working_model->setParamEmploymentStatusAttribute($employmentstatus);
            $temp_working_model->setParamDepartmentcodeAttribute($departmentcode);
            $temp_working_model->setParamUsercodeAttribute($usercode);
            try{
                Log::debug('出勤・退勤データtempから登録 ');
                $temp_working_time_dates = $temp_working_model->getTempWorkingTimeDateUserJoin($dateto);
                Log::debug('temp_working_time_dates =  '.count($temp_working_time_dates));
                if (count($temp_working_time_dates) > 0) {
                    Log::debug('isset $temp_working_time_dates true ');
                    $working_model = new WorkingTimedate();
                    $working_model->setParamdatefromAttribute(date_format(new Carbon($datefrom), 'Ymd'));
                    $working_model->setParamdatetoAttribute(date_format(new Carbon($dateto), 'Ymd'));
                    $working_model->setParamEmploymentStatusAttribute($employmentstatus);
                    $working_model->setParamDepartmentcodeAttribute($departmentcode);
                    $working_model->setParamUsercodeAttribute($usercode);
                    //DB::beginTransaction();
                    //Log::debug(' calc beginTransaction ');
                    try{
                        //if ($working_model->isExistsWorkingTimeDate()) {
                        //    Log::debug(' $delWorkingTimeDate  ');
                         //   $working_model->delWorkingTimeDate();
                        //};
                        Log::debug(' $insertWorkingTimeDateFromTemp  ');
                        $working_model->insertWorkingTimeDateFromTemp($temp_working_time_dates);
                        //DB::commit();
                        //Log::debug(' calc commit ');
                    }catch(\PDOException $pe){
                        //DB::rollBack();
                        //Log::debug(' calc rollBack ');
                        $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_error_dailycalc'));
                        throw $pe;
                    }catch(\Exception $e){
                        //DB::rollBack();
                        //Log::debug(' calc rollBack ');
                        $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_accesee_eror_dailycalc'));
                        $add_result = false;
                        throw $e;
                    }
                }
            }catch(\PDOException $pe){
                $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_error_dailycalc'));
                Log::error(Config::get('const.LOG_MSG.data_error_dailycalc'));
                Log::error($e->getMessage());
                $add_result = false;
                throw $pe;
            }catch(\Exception $e){
                $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_accesee_eror_dailycalc'));
                $add_result = false;
                throw $e;
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

        Log::DEBUG('---------------------- temp_calc_workingtimes登録 calcWorkingTimeDate in データ件数 = --------- '.count($worktimes));
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
        $target_flg = false;
        $attendance_target_flg = false;      // 出勤のときの集計対象FLG
        $this->user_temp_seq = 0;           // ユーザー単位のtemp出力時のseq
        $before_out_flg = false;
        // ユーザー単位処理
        foreach ($worktimes as $result) {
            // 打刻データありの場合
            Log::DEBUG('----日次労働時間取得 code = '.$result->user_code.' '.$result->user_name. ' 開始   計算ターゲット日付'.$target_date_ymd.' ------------------------ ');
            Log::DEBUG('        部署  $result->department_name   = '.$result->department_name);
            Log::DEBUG('        打刻時刻 $result->record_datetime = '.$result->record_datetime);
            Log::DEBUG('        打刻日  $result->record_date = '.$result->record_date);
            Log::DEBUG('        モード  $result->mode = '.$result->mode);
            if (isset($before_result)) {
                Log::DEBUG('        　前回モード $before_result->mode  = '.$before_result->mode);
            } else {
                Log::DEBUG('        　前回モード   なし');
            }
            Log::DEBUG('        出勤区分 $result->business_kubun = '.$result->business_kubun);
            Log::DEBUG('                $result->business_name  = '.$result->business_name);
            Log::DEBUG('        ユーザー休暇区分 $result->user_holiday_kubun = '.$result->user_holiday_kubun);
            Log::DEBUG('                        $result->user_holiday_name  = '.$result->user_holiday_name);
            Log::DEBUG('        タイムテーブル　開始時刻　$result->working_timetable_from_time = '.$result->working_timetable_from_time);
            Log::DEBUG('        タイムテーブル　終了時刻　result->working_timetable_to_time    = '.$result->working_timetable_to_time);
            if ($target_flg) {
                Log::DEBUG('        集計対象判断前 target_flg = true');
            } else {
                Log::DEBUG('        集計対象判断前 target_flg = false');
            }
            if ($result->record_datetime != null && $result->mode != null) {
                // 設定値確認
                $chk_setting = $this->chkSettingData($result);
                // 設定が正常である場合
                if ($chk_setting == 0)  {
                    // 翌日退勤した場合を考慮し、同日処理を行うようにするため、$current_dateは$target_date_ymdとする
                    // よって日付ブレーク処理は無意味となる
                    $current_date = $target_date_ymd;
                    $current_department_code = $result->department_code;
                    $current_user_code = $result->user_code;
                    $current_result = $result;
                    // 指定日付<=であれば集計対象、>であれば打刻なしとして登録
                    if ($result->mode == Config::get('const.C012.attendance')) {
                        if ($result->record_date > $target_date_ymd) {
                            $target_flg = false;
                        } else {
                            $target_flg = true;
                        }
                        $attendance_target_flg = $target_flg;
                    } else {
                        // 出勤ではない場合は前の出勤モードの集計対象
                        if ($current_department_code != $before_department_code ||
                            $current_user_code != $before_user_code) {
                            $attendance_target_flg = false;
                        }
                        $target_flg = $attendance_target_flg;
                        // -----------------------　20200215コメント化 --------------------- 
                        // if ($result->mode == Config::get('const.C012.leaving')) {
                        //     $attendance_target_flg = false;
                        // }
                    }
                    if ($target_flg) {
                        Log::DEBUG('        集計対象判断 target_flg = true');
                    } else {
                        Log::DEBUG('        集計対象判断 target_flg = false');
                    }
                    if ($attendance_target_flg) {
                        Log::DEBUG('        集計対象判断 出勤　attendance_target_flg = true');
                    } else {
                        Log::DEBUG('        集計対象判断 出勤　attendance_target_flg = false');
                    }
                            // 20191012 end
                    if ($before_date == null) {$before_date = $current_date;}
                    if ($before_department_code == null) {$before_department_code = $current_department_code;}
                    if ($before_user_code == null) {$before_user_code = $current_user_code;}
                    if ($before_result == null) {$before_result = $result;}
                    if ($target_flg == true) {
                        Log::DEBUG('        打刻あり、当日計算対象データ');
                        // ユーザー休暇区分判定用
                        $before_holiday_date = null;
                        $before_holiday_user_code = null;
                        $before_holiday_department_code = null;
                        $before_holiday_kubun = null;
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
                            Log::DEBUG('    ユーザーが変わった場合 ');
                            // ユーザー労働時間計算(１個前のユーザーを計算する)
                            Log::DEBUG('        temp_calc_workingtimesの登録開始');
                            Log::DEBUG('        １個前のユーザーを登録開始 $before_user_code = '.$before_user_code);
                            Log::DEBUG('            部署     = '.$before_result->department_name);
                            Log::DEBUG('            打刻時刻 = '.$before_result->record_datetime);
                            Log::DEBUG('            モード   = '.$before_result->mode);
                            Log::DEBUG('            打刻日   = '.$before_result->record_date);
                            $this->calcWorkingTime(
                                $before_date,
                                $before_user_code,
                                $before_department_code,
                                $business_kubun,
                                $before_result->interval,
                                $before_result->user_holiday_kubun,
                                $before_result->record_datetime
                            );
                            try{
                                // temporaryに登録する
                                $this->insTempCalcItem($before_date, $before_result);
                            }catch(\PDOException $pe){
                                $add_results = false;
                                throw $pe;
                            }
                            Log::DEBUG('        temp_calc_workingtimesの登録終了');
                            // 次データ計算事前処理
                            // beforeArrayWorkingTimeは現データが有効の場合の事前処理
                            $this->beforeArrayWorkingTime($result);
                            // ユーザーを同じく設定
                            $before_user_code = $current_user_code;
                            $before_out_flg = true;
                            $target_flg = false;
                            $this->user_temp_seq = 0;
                            if ($target_flg) {
                                Log::DEBUG('        集計対象処理後 target_flg = true');
                            } else {
                                Log::DEBUG('        集計対象処理後 target_flg = false');
                            }
                        } elseif ($current_date == $before_date) {
                            // 部署が変わった場合
                            Log::DEBUG('    部署が変わった場合 ');
                            // ユーザー労働時間計算(１個前のユーザーを計算する)
                            Log::DEBUG('    temp_calc_workingtimesの登録開始');
                            Log::DEBUG('        １個前のユーザーを登録開始 $before_user_code = '.$before_user_code);
                            Log::DEBUG('            部署     = '.$before_result->department_name);
                            Log::DEBUG('            打刻時刻 = '.$before_result->record_datetime);
                            Log::DEBUG('            モード   = '.$before_result->mode);
                            Log::DEBUG('            打刻日   = '.$before_result->record_date);
                            $this->calcWorkingTime(
                                $before_date,
                                $before_user_code,
                                $before_department_code,
                                $business_kubun,
                                $before_result->interval,
                                $before_result->user_holiday_kubun,
                                $before_result->record_datetime
                            );
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
                            $before_out_flg = true;
                            $target_flg = false;
                            $this->user_temp_seq = 0;
                            if ($target_flg) {
                                Log::DEBUG('        集計対象処理後 target_flg = true');
                            } else {
                                Log::DEBUG('        集計対象処理後 target_flg = false');
                            }
                        } else {
                            // 日付が変わった場合
                            Log::DEBUG('    日付が変わった ');
                            try{
                                // ユーザー労働時間登録(１個前のユーザーを登録する)
                                Log::DEBUG('        １個前のユーザーを登録開始 $before_user_code = '.$before_user_code);
                                Log::DEBUG('            部署     = '.$before_result->department_name);
                                Log::DEBUG('            打刻時刻 = '.$before_result->record_datetime);
                                Log::DEBUG('            モード   = '.$before_result->mode);
                                Log::DEBUG('            打刻日   = '.$before_result->record_date);
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
                                $before_out_flg = true;
                                $target_flg = false;
                                if ($target_flg) {
                                    Log::DEBUG('        集計対象処理後 target_flg = true');
                                } else {
                                    Log::DEBUG('        集計対象処理後 target_flg = false');
                                }
                            }catch(\PDOException $pe){
                                $add_results = false;
                                throw $pe;
                            }catch(\Exception $e){
                                $add_results = false;
                                throw $e;
                            }
                        }
                    } else {
                        Log::DEBUG('        打刻なし、当日計算対象データ');
                        // 前のデータが打刻ありであれば計算する
                        $user_holiday_kubun = null;
                        $user_holiday_name = null;
                        $user_working_date = null;
                        if (count($this->array_working_mode) > 0) {
                            try{
                                Log::DEBUG('        １個前のユーザーを登録開始 $before_user_code = '.$before_user_code);
                                Log::DEBUG('            部署     = '.$before_result->department_name);
                                Log::DEBUG('            打刻時刻 = '.$before_result->record_datetime);
                                Log::DEBUG('            モード   = '.$before_result->mode);
                                Log::DEBUG('            打刻日   = '.$before_result->record_date);
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
                                Log::DEBUG('        １個前のユーザーを登録終了 $before_user_code = '.$before_user_code);
                                $before_date = null;
                                $before_user_code = null;
                                $before_department_code = null;
                                $before_result = null;
                                // 打刻データ配列の初期化
                                $this->iniArrayWorkingTime();
                                // 計算用配列の初期化
                                $this->iniArrayCalc();
                                $before_out_flg = true;
                                $target_flg = false;
                            }catch(\PDOException $pe){
                                $add_results = false;
                                throw $pe;
                            }catch(\Exception $e){
                                $add_results = false;
                                throw $e;
                            }
                        }
                        // 打刻ないデータはtempに出力
                        Log::DEBUG('        打刻ないデータはtempに出力するか判定 $current_date = '.$current_date);
                        Log::DEBUG('            $before_date = '.$before_date);
                        Log::DEBUG('            打刻時刻      = '.$result->record_datetime);
                        Log::DEBUG('            打刻日付      = '.$result->record_date);
                        Log::DEBUG('            ターゲット日付   = '.$target_date_ymd);
                        Log::DEBUG('            ユーザー休暇   = '.$user_holiday_kubun);
                        Log::DEBUG('            1件前出力      = '.$before_out_flg);
                        // 1件前の日付がnullである場合、いきなり対象日付がないということなので出力
                        //if (!isset($result->record_datetime) || isset($user_holiday_kubun)) {
                        //if (!$before_out_flg || isset($user_holiday_kubun)) {
                        if (!isset($result->record_datetime) && (!$before_out_flg || isset($user_holiday_kubun))) {
                        //if ($temp_out_flg ) {   // 20191012
                            try{
                                Log::DEBUG('        打刻ないデータはtempに出力 $current_date = ');
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
                                    $this->pushArrayCalc($this->setNoInputTimePtn($ptn, $user_holiday_name, $dt, $user_working_date, $result->working_timetable_no));
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
                            $target_flg = false;
                            // 次データ計算事前処理
                            // 打刻データ配列の初期化
                            $this->iniArrayWorkingTime();
                            // 計算用配列の初期化
                            $this->iniArrayCalc();
                        } else {
                            $before_out_flg = true;
                            $target_flg = false;
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
                            Log::DEBUG('        １個前のユーザーを登録開始 $before_user_code = '.$before_user_code);
                            Log::DEBUG('            部署     = '.$before_result->department_name);
                            Log::DEBUG('            打刻時刻 = '.$before_result->record_datetime);
                            Log::DEBUG('            モード   = '.$before_result->mode);
                            Log::DEBUG('            打刻日   = '.$before_result->record_date);
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
                            $before_out_flg = true;
                            $target_flg = false;
                            // 次データ計算事前処理
                            // 打刻データ配列の初期化
                            $this->iniArrayWorkingTime();
                            // 計算用配列の初期化
                            $this->iniArrayCalc();
                        }catch(\PDOException $pe){
                            $add_results = false;
                            throw $pe;
                        }catch(\Exception $e){
                            $add_results = false;
                            throw $e;
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
                            $this->pushArrayCalc($this->setNoInputTimePtn($ptn, $user_holiday_name, $dt, $user_working_date, $result->working_timetable_no));
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
                        Log::DEBUG('            部署     = '.$before_result->department_name);
                        Log::DEBUG('            打刻時刻 = '.$before_result->record_datetime);
                        Log::DEBUG('            モード   = '.$before_result->mode);
                        Log::DEBUG('            打刻日   = '.$before_result->record_date);
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
                        /*$before_date = null;
                        $before_user_code = null;
                        $before_department_code = null;
                        $before_result = null;*/
                        $before_out_flg = true;
                        $target_flg = false;
                        if ($target_flg) {
                            Log::DEBUG('        集計対象処理後 target_flg = true');
                        } else {
                            Log::DEBUG('        集計対象処理後 target_flg = false');
                        }
                    // 打刻データ配列の初期化
                        $this->iniArrayWorkingTime();
                        // 計算用配列の初期化
                        $this->iniArrayCalc();
                    }catch(\PDOException $pe){
                        $add_results = false;
                        throw $pe;
                    }catch(\Exception $e){
                        $add_results = false;
                        throw $e;
                    }
                }
                // 打刻ないデータはtempに出力
                // ただし、日付とユーザー休暇区分が１件前と同じ場合は出力しない
                Log::DEBUG('        打刻ないデータ = '.$result->user_code.' record_time = '.$result->record_datetime.' before_out_flg = '.$before_out_flg);
                $temp_non_date_flg = false;
                if(isset($result->user_holiday_kubun)) { $user_holiday_kubun = $result->user_holiday_kubun; }
                if(isset($result->user_holiday_name)) { $user_holiday_name = $result->user_holiday_name; }
                if(isset($result->user_working_date)) { $user_working_date = $result->user_working_date; }
                if (isset($before_result)) {
                    if ($before_result->department_code != $result->department_code ||
                        $before_result->user_code != $result->user_code ||
                        $before_result->user_working_date != $result->user_working_date ||
                        $before_holiday_kubun != $user_holiday_kubun) {
                        if (!$before_out_flg) {
                            $temp_non_date_flg = true;
                        }
                    } else {
                        if (!$before_out_flg) {
                            $temp_non_date_flg = true;
                        }
                    }
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
                        $this->pushArrayCalc($this->setNoInputTimePtn($ptn, $user_holiday_name, $dt, $user_working_date, $result->working_timetable_no));
                        // temporaryに登録する
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
                $target_flg = false;
                // 次データ計算事前処理
                // 打刻データ配列の初期化
                $this->iniArrayWorkingTime();
                // 計算用配列の初期化
                $this->iniArrayCalc();
            }
            $before_result = $result;
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
                    $current_result->user_holiday_kubun);
                    // 次データ計算事前処理
                // 打刻データ配列の初期化
                $this->iniArrayWorkingTime();
                // 計算用配列の初期化
                $this->iniArrayCalc();
                Log::DEBUG('    最終残のユーザーを登録終了 $current_user_code = '.$current_user_code);
            }catch(\PDOException $pe){
                $add_results = false;
                throw $pe;
            }catch(\Exception $e){
                $add_results = false;
                throw $e;
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
    private function addWorkingTime(
        $target_date, $target_user_code, $target_department_code, $target_result, $business_kubun, $interval, $user_holiday_kubun)
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
            $user_holiday_kubun,
            $target_result->record_datetime
        );
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
    private function calcWorkingTime(
        $target_date, $target_user_code, $target_department_code, $business_kubun, $interval, $user_holiday_kubun, $target_record_time)
    {
        Log::DEBUG('---------------------- calcWorkingTime in ---$target_user_code = '.$target_user_code);
        Log::DEBUG('                       calcWorkingTime in ---$target_record_time = '.$target_record_time);
        $work_time = new WorkTime();
        $work_time->setParamDepartmentcodeAttribute($target_department_code);
        $work_time->setParamUsercodeAttribute($target_user_code);
        $work_time->setParamStartDateAttribute($target_date);
        $work_time->setParamDatetoAttribute($target_date);
        $attendance_time = null;
        $leaving_time = null;
        $missing_middle_time = null;
        $missing_middle_return_time = null;
        $public_going_out_time = null;
        $public_going_out_return_time = null;
        $working_status = null;
        $cnt = 0;
        // 前提 count($array_working_mode) = count($array_working_datetime)
        Log::DEBUG('        array_working_mode count = '.count($this->array_working_mode));
        // working_timetable_noは出勤時刻のworking_timetable_noとする（日付が変わって退勤などがあるとworking_timetable_noが異なっている場合があるため）
        // また出勤時刻のworking_timetable_noがない場合はresultのworking_timetable_noとする
        $working_timetable_no_set = false;
        $attendance_time_index = -1;
        $value_working_timetable_no = 0;
        for($i=0;$i<count($this->array_working_mode);$i++){
            $value_mode = $this->array_working_mode[$i];
            $value_record_datetime = $this->array_working_datetime[$i];
            $value_timetable_from_time = $this->array_timetable_from_time[$i];
            $value_timetable_to_time = $this->array_timetable_to_time[$i];
            $value_check_result = $this->array_check_result[$i];
            $value_check_max_times = $this->array_check_max_times[$i];
            $value_check_interval = $this->array_check_interval[$i];
            $value_mobile_positions = $this->array_mobile_positions[$i];
            $dt = new Carbon($value_record_datetime);
            $record_date = date_format($dt, 'Ymd');
            Log::DEBUG('        ユーザー労働時間計算 cnt = '.$cnt);
            Log::DEBUG('        ユーザー労働時間計算 value_mode = '.$value_mode);
            Log::DEBUG('        ユーザー労働時間計算 value_record_datetime = '.$value_record_datetime);
            Log::DEBUG('        ユーザー労働時間計算 value_timetable_from_time = '.$value_timetable_from_time);
            Log::DEBUG('        ユーザー労働時間計算 value_timetable_to_time = '.$value_timetable_to_time);
            Log::DEBUG('        ユーザー労働時間計算 value_check_result = '.$value_check_result);
            Log::DEBUG('        ユーザー労働時間計算 value_check_max_times = '.$value_check_max_times);
            Log::DEBUG('        ユーザー労働時間計算 value_check_interval = '.$value_check_interval);
            Log::DEBUG('        ユーザー労働時間計算 value_mobile_positions = '.$value_mobile_positions);
            Log::DEBUG('        ユーザー労働時間計算 value_working_timetable_no = '.$value_working_timetable_no);
            Log::DEBUG('        ユーザー労働時間計算 target_date = '.$target_date);
            Log::DEBUG('        ユーザー労働時間計算 record_date = '.$record_date);
            // 事前にテーブル再取得（テーブル取得1日以前のMAX打刻時刻）しておく
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
            Log::DEBUG('        ユーザー労働時間計算 $before_value_mode = '.$before_value_mode);
            Log::DEBUG('        ユーザー労働時間計算 $before_value_datetime = '.$before_value_datetime);
            Log::DEBUG('        ユーザー労働時間計算 $after_value_mode = '.$after_value_mode);
            Log::DEBUG('        ユーザー労働時間計算 $after_value_datetime = '.$after_value_datetime);
            $work_time->setParamDatefromAttribute($target_date);
            $work_time->setParamDatetoAttribute($target_date);
            // 出勤打刻の場合
            if ($value_mode == Config::get('const.C005.attendance_time')) {
                $value_working_timetable_no = $this->array_working_timetable_no[$i];
                $working_timetable_no_set = true;
                $this->setAttendancetime(
                    $cnt,
                    $work_time,
                    $value_record_datetime,
                    $value_timetable_from_time,
                    $value_timetable_to_time,
                    $value_check_result,
                    $value_check_max_times,
                    $value_check_interval,
                    $value_mobile_positions,
                    $before_value_mode,
                    $before_value_datetime,
                    $business_kubun,
                    $interval,
                    $user_holiday_kubun,
                    $value_working_timetable_no
                );
                if ($target_date == $record_date) {
                    $attendance_time_index = $i;
                }
            } elseif ($value_mode == Config::get('const.C005.leaving_time')) {      // 退勤の場合
                if (!$working_timetable_no_set) {
                    $value_working_timetable_no = $this->array_working_timetable_no[$i];
                }
                $this->setLeavingtime(
                    $cnt,
                    $work_time,
                    $value_record_datetime,
                    $value_timetable_from_time,
                    $value_timetable_to_time,
                    $value_check_result,
                    $value_check_max_times,
                    $value_mobile_positions,
                    $before_value_mode,
                    $before_value_datetime,
                    $business_kubun,
                    $user_holiday_kubun,
                    $value_working_timetable_no,
                    $attendance_time_index
                );
                $working_timetable_no_set = false;
            } elseif ($value_mode == Config::get('const.C005.missing_middle_time')) {       // 私用外出の場合
                if (!$working_timetable_no_set) {
                    $value_working_timetable_no = $this->array_working_timetable_no[$i];
                }
                $this->setMissingMiddleTime(
                    $cnt,
                    $work_time,
                    $value_record_datetime,
                    $value_timetable_from_time,
                    $value_timetable_to_time,
                    $value_check_result,
                    $value_check_max_times,
                    $value_mobile_positions,
                    $before_value_mode,
                    $before_value_datetime,
                    $value_working_timetable_no,
                    $attendance_time_index
                );
            } elseif ($value_mode == Config::get('const.C005.missing_middle_return_time')) {        // 私用外出戻りの場合
                if (!$working_timetable_no_set) {
                    $value_working_timetable_no = $this->array_working_timetable_no[$i];
                }
                $this->setMissingMiddleReturnTime(
                    $cnt,
                    $work_time,
                    $value_record_datetime,
                    $value_timetable_from_time,
                    $value_timetable_to_time,
                    $value_check_result,
                    $value_check_max_times,
                    $value_mobile_positions,
                    $before_value_mode,
                    $before_value_datetime,
                    $value_working_timetable_no,
                    $attendance_time_index
                );
            } elseif ($value_mode == Config::get('const.C005.public_going_out_time')) {             // 公用外出の場合
                if (!$working_timetable_no_set) {
                    $value_working_timetable_no = $this->array_working_timetable_no[$i];
                }
                $this->setPubliGoingOutTime(
                    $cnt,
                    $work_time,
                    $value_record_datetime,
                    $value_timetable_from_time,
                    $value_timetable_to_time,
                    $value_check_result,
                    $value_check_max_times,
                    $value_mobile_positions,
                    $before_value_mode,
                    $before_value_datetime,
                    $value_working_timetable_no,
                    $attendance_time_index
                );
            } elseif ($value_mode == Config::get('const.C005.public_going_out_return_time')) {      // 公用外出戻りの場合
                if (!$working_timetable_no_set) {
                    $value_working_timetable_no = $this->array_working_timetable_no[$i];
                }
                $this->setPublicGoingOutReturnTime(
                    $cnt,
                    $work_time,
                    $value_record_datetime,
                    $value_timetable_from_time,
                    $value_timetable_to_time,
                    $value_check_result,
                    $value_check_max_times,
                    $value_mobile_positions,
                    $before_value_mode,
                    $before_value_datetime,
                    $value_working_timetable_no,
                    $attendance_time_index
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
        $value_check_result, $value_check_max_times, $value_check_interval, $value_mobile_positions, $before_value_mode,
        $before_value_datetime, $business_kubun, $interval, $user_holiday_kubun, $working_timetable_no)
    {

        Log::DEBUG('---------------------- setAttendancetime in ------------------------ ');
        Log::DEBUG('        出勤打刻処理 start');
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
        Log::DEBUG('            attendance_from_date  = '.$attendance_from_date);
        Log::DEBUG('            timetable_from_date  = '.$timetable_from_date);
        Log::DEBUG('            timetable_to_date  = '.$timetable_to_date);
        Log::DEBUG('            attendance_to_date  = '.$attendance_to_date);
        Log::DEBUG('            record_datetime  = '.$record_datetime);
        Log::DEBUG('            record_before_datetime = '.$record_before_datetime);
        Log::DEBUG('            before_value_mode = '.$before_value_mode);
        Log::DEBUG('            cnt  = '.$cnt);
        // パターン設定
        $ptn = null;

        // ---------------------出勤が最初の場合 -----------------------------------------------------------------------------------
        if ($cnt == 0) {
            if ($before_value_mode == Config::get('const.C005.attendance_time')) {          // １個前のモードが出勤である場合
                Log::DEBUG('        １個前のモードが出勤である');
                if ($record_before_datetime < $attendance_from_date) {                      // １個前の打刻時刻 < 出勤1日のはじめ
                    // パターン２（打刻ミス（出勤済）（要確認）。勤務状態は打刻なし）
                    $ptn = '2';
                }
            } elseif ($before_value_mode == Config::get('const.C005.leaving_time')) {       // １個前のモードが退勤である場合
                Log::DEBUG('        １個前のモードが退勤である');
                if ($record_datetime >= $attendance_from_date &&
                            $record_datetime < $timetable_from_date) {                      // 出勤1日のはじめ <= 打刻時刻 < タイムテーブルの始業時刻
                    $ptn = '1';
                    // 退勤から出勤までのタイム差を取得しインターバルチェック
                    $value_check_interval = $apicommon->chkInteval($record_datetime, $record_before_datetime);
                } elseif ($record_datetime >= $timetable_from_date &&
                            $record_datetime < $timetable_to_date) {                        // タイムテーブルの始業時刻 <= 打刻時刻 < タイムテーブルの終業時刻
                    // パターン３（遅刻出勤（遅刻=1）。勤務状態は出勤状態）
                    $ptn = '3';
                    // 退勤から出勤までのタイム差を取得しインターバルチェック
                    $value_check_interval = $apicommon->chkInteval($record_datetime, $record_before_datetime);
                } elseif ($record_datetime >= $timetable_to_date &&
                            $record_datetime < $attendance_to_date) {                       // タイムテーブルの終業時刻 <= 打刻時刻 < 出勤1日の終わり
                    //if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
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
                Log::DEBUG('        １個前のモードが外出である');
                if ($record_before_datetime < $attendance_from_date) {                      // １個前の打刻時刻 < 出勤1日のはじめ
                    // パターン５（打刻ミス（外出戻りしていない）。勤務状態は打刻なし）
                    $ptn = '5';
                }
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_return_time') ||
                $before_value_mode == Config::get('const.C005.public_going_out_return_time')) {     // １個前のモードが戻り
                Log::DEBUG('        １個前のモードが外出戻りである');
                if ($record_before_datetime < $attendance_from_date) {                      // １個前の打刻時刻 < 出勤1日のはじめ
                    // パターン６（打刻ミス（退勤していない）。勤務状態は打刻なし）
                    $ptn = '6';
                }
            } else {                                                                        // １個前のモードがない
                Log::DEBUG('        １個前のモードがない ');
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
                $value_mobile_positions,
                $business_kubun,
                $user_holiday_kubun,
                $working_timetable_no));
        } else {
            // 不明データとして作成する
            $this->pushArrayCalc($this->setAttendanceCollectPtn(
                '',
                $record_datetime,
                $value_check_result,
                $value_check_max_times,
                $value_check_interval,
                $value_mobile_positions,
                $business_kubun,
                $user_holiday_kubun,
                $working_timetable_no));
        }
        $this->check_interval2 = $value_check_interval;
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
        $ptn, $record_datetime, $value_check_result, $value_check_max_times, $value_check_interval,$value_mobile_positions,
        $business_kubun, $user_holiday_kubun, $working_timetable_no)
    {
        Log::DEBUG('---------------------- setAttendanceCollectPtn in -- 出勤 ptn = '.$ptn.' ---------------------- '.$record_datetime);
        Log::DEBUG('                                                  -- working_timetable_no = '.$working_timetable_no);
        $temp_calc_model = new TempCalcWorkingTime();

        if ($ptn == '1') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
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
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
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
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } elseif ($ptn == '4') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
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
            $temp_calc_model->setPositionsAttribute($value_mobile_positions);
        } elseif ($ptn == '5') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
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
        $value_check_result, $value_check_max_times, $value_mobile_positions, $before_value_mode, $before_value_datetime,
        $business_kubun, $user_holiday_kubun, $working_timetable_no, $attendance_time_index)
    {
        Log::DEBUG('---------------------- setLeavingtime in ------------------------ ');
        Log::DEBUG('        退勤打刻処理 start');
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
        Log::DEBUG('            attendance_from_date set = '.$attendance_from_date);
        Log::DEBUG('            timetable_from_date set = '.$timetable_from_date);
        Log::DEBUG('            timetable_to_date set = '.$timetable_to_date);
        Log::DEBUG('            attendance_to_date set = '.$attendance_to_date);
        Log::DEBUG('            record_datetime set = '.$record_datetime);
        Log::DEBUG('            record_before_datetime set = '.$record_before_datetime);
        Log::DEBUG('            before_value_mode set = '.$before_value_mode);
        Log::DEBUG('            before_value_datetime set = '.$before_value_datetime);
        Log::DEBUG('            cnt set = '.$cnt);
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
                            $this->setLeavingCollectPtn(
                                $ptn, $record_datetime, $value_check_result, $value_check_max_times, $value_mobile_positions,
                                $business_kubun, $user_holiday_kubun, $working_timetable_no, $attendance_time_index));
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
                            $this->setLeavingCollectPtn(
                                $ptn, $record_datetime, $value_check_result, $value_check_max_times, $value_mobile_positions,
                                $business_kubun, $user_holiday_kubun, $working_timetable_no, $attendance_time_index));
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
                            $this->setLeavingCollectPtn(
                                $ptn, $record_datetime, $value_check_result, $value_check_max_times, $value_mobile_positions,
                                $business_kubun, $user_holiday_kubun, $working_timetable_no, $attendance_time_index));
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
                                            $this->setLeavingCollectPtn(
                                                $ptn, $record_datetime, $value_check_result, $value_check_max_times, $value_mobile_positions,
                                                $business_kubun, $user_holiday_kubun, $working_timetable_no, $attendance_time_index));
                                        /*$ptn = '3';
                                        $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime, $value_check_result, $value_check_max_times, $business_kubun));*/
                                        // パターン4は下で設定
                                        $ptn = '4';
                                    } else {
                                        // パターン２３５
                                        $ptn = '2';
                                        $this->pushArrayCalc(
                                            $this->setLeavingCollectPtn(
                                                $ptn, $record_datetime, $value_check_result, $value_check_max_times, $value_mobile_positions,
                                                $business_kubun, $user_holiday_kubun, $working_timetable_no, $attendance_time_index));
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
                                $this->setLeavingCollectPtn(
                                    $ptn, $record_datetime, $value_check_result, $value_check_max_times, $value_mobile_positions,
                                    $business_kubun, $user_holiday_kubun, $working_timetable_no, $attendance_time_index));
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
                                            $this->setLeavingCollectPtn(
                                                $ptn, $record_datetime, $value_check_result, $value_check_max_times, $value_mobile_positions,
                                                $business_kubun, $user_holiday_kubun, $working_timetable_no, $attendance_time_index));
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
                                $this->setLeavingCollectPtn(
                                    $ptn, $record_datetime, $value_check_result, $value_check_max_times, $value_mobile_positions,
                                    $business_kubun, $user_holiday_kubun, $working_timetable_no, $attendance_time_index));
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
                                        $this->setLeavingCollectPtn(
                                            $ptn, $record_datetime, $value_check_result, $value_check_max_times, $value_mobile_positions,
                                            $business_kubun, $user_holiday_kubun, $working_timetable_no, $attendance_time_index));
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
                            $this->setLeavingCollectPtn(
                                $ptn, $record_datetime, $value_check_result, $value_check_max_times, $value_mobile_positions,
                                $business_kubun, $user_holiday_kubun, $working_timetable_no, $attendance_time_index));
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
                $this->setLeavingCollectPtn(
                    $ptn, $record_datetime, $value_check_result, $value_check_max_times, $value_mobile_positions,
                    $business_kubun, $user_holiday_kubun, $working_timetable_no, $attendance_time_index));
        } else {
            // 不明データとして作成する
            $this->pushArrayCalc(
                $this->setLeavingCollectPtn(
                    '', $record_datetime, $value_check_result, $value_check_max_times, $value_mobile_positions,
                    $business_kubun, $user_holiday_kubun, $working_timetable_no, $attendance_time_index));
        }
        Log::DEBUG('        退勤打刻処理 end');
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
    private function setLeavingCollectPtn($ptn, $record_datetime, $value_check_result, 
         $value_check_max_times, $value_mobile_positions, $business_kubun, $user_holiday_kubun, $working_timetable_no, $attendance_time_index)
    {
        Log::DEBUG('---------------------- setLeavingCollectPtn in -- 退勤 ptn = '.$ptn.' ---------------------- '.$record_datetime);
        Log::DEBUG('                                                  -- working_timetable_no = '.$working_timetable_no);
        Log::DEBUG('                                                  -- attendance_time_index = '.$attendance_time_index);
        $temp_calc_model = new TempCalcWorkingTime();

        if ($ptn == '1') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
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
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
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
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
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
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
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
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
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
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
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
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
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
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
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
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
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
        $value_check_result, $value_check_max_times, $value_mobile_positions, $before_value_mode, $before_value_datetime, $working_timetable_no, $attendance_time_index)
    {
        Log::DEBUG('---------------------- setMissingMiddleTime in ------------------------ ');
        Log::DEBUG('        私用外出打刻処理');
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
        Log::DEBUG('            attendance_from_date set = '.$attendance_from_date);
        Log::DEBUG('            timetable_from_date set = '.$timetable_from_date);
        Log::DEBUG('            timetable_to_date set = '.$timetable_to_date);
        Log::DEBUG('            attendance_to_date set = '.$attendance_to_date);
        Log::DEBUG('            record_datetime set = '.$record_datetime);
        Log::DEBUG('            record_before_datetime set = '.$record_before_datetime);
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
                    $collect_working_times = $this->setMissingmiddleCollectPtn(
                        '', $record_datetime, $value_check_result, $value_check_max_times, $value_mobile_positions, $working_timetable_no, $attendance_time_index);
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
            $this->pushArrayCalc($this->setMissingmiddleCollectPtn(
                $ptn, $record_datetime, $value_check_result, $value_check_max_times, $value_mobile_positions, $working_timetable_no, $attendance_time_index));
        } else {
            // 不明データとして作成する
            $this->pushArrayCalc($this->setMissingmiddleCollectPtn(
                '', $record_datetime, $value_check_result, $value_check_max_times, $value_mobile_positions, $working_timetable_no, $attendance_time_index));
        }
        Log::DEBUG('---------------------- setMissingMiddleTime end ------------------------ ');
        Log::DEBUG('        私用外出打刻処理 end');
            
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
    private function setMissingmiddleCollectPtn(
        $ptn, $record_datetime, $value_check_result, $value_check_max_times, $value_mobile_positions, $working_timetable_no, $attendance_time_index)
    {
        Log::DEBUG('---------------------- setMissingmiddleCollectPtn in ------------------------ ');
        $temp_calc_model = new TempCalcWorkingTime();

        if ($ptn == '1') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.missing_middle_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
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
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
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
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
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
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
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
        $value_check_result, $value_check_max_times, $value_mobile_positions,
        $before_value_mode, $before_value_datetime, $working_timetable_no, $attendance_time_index)
    {
        Log::DEBUG('---------------------- setMissingMiddleReturnTime in ------------------------ ');
        Log::DEBUG('        私用外出戻り打刻処理');
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
        Log::DEBUG('            attendance_from_date set = '.$attendance_from_date);
        Log::DEBUG('            timetable_from_date set = '.$timetable_from_date);
        Log::DEBUG('            timetable_to_date set = '.$timetable_to_date);
        Log::DEBUG('            attendance_to_date set = '.$attendance_to_date);
        Log::DEBUG('            record_datetime set = '.$record_datetime);
        Log::DEBUG('            record_before_datetime set = '.$record_before_datetime);
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
            $this->pushArrayCalc($this->setMissingmiddleReturnCollectPtn(
                $ptn, $record_datetime, $value_check_result, $value_check_max_times, $value_mobile_positions, $working_timetable_no, $attendance_time_index));
        } else {
            // 不明データとして作成する
            $this->pushArrayCalc($this->setMissingmiddleReturnCollectPtn(
                '', $record_datetime, $value_check_result, $value_check_max_times, $value_mobile_positions, $working_timetable_no, $attendance_time_index));
        }
        Log::DEBUG('        私用外出戻り打刻処理 end');
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
    private function setMissingmiddleReturnCollectPtn(
        $ptn, $record_datetime, $value_check_result, $value_check_max_times, $value_mobile_positions, $working_timetable_no, $attendance_time_index)
    {
        Log::DEBUG('---------------------- setMissingmiddleReturnCollectPtn in ------------------------ ');
        $temp_calc_model = new TempCalcWorkingTime();

        if ($ptn == '1') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.missing_middle_return_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
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
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.missing_middle_return'));
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
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
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
        $value_check_result, $value_check_max_times, $value_mobile_positions, 
        $before_value_mode, $before_value_datetime, $working_timetable_no, $attendance_time_index)
    {
        Log::DEBUG('---------------------- setPubliGoingOutTime in ------------------------ ');
        Log::DEBUG('        公用外出打刻処理');
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
        Log::DEBUG('            attendance_from_date set = '.$attendance_from_date);
        Log::DEBUG('            timetable_from_date set = '.$timetable_from_date);
        Log::DEBUG('            timetable_to_date set = '.$timetable_to_date);
        Log::DEBUG('            attendance_to_date set = '.$attendance_to_date);
        Log::DEBUG('            record_datetime set = '.$record_datetime);
        Log::DEBUG('            record_before_datetime set = '.$record_before_datetime);
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
                    $collect_working_times = $this->setPublicGoingOutCollectPtn(
                        '', $record_datetime, $value_check_result, $value_check_max_times, $value_mobile_positions, $working_timetable_no, $attendance_time_index);
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
            $this->pushArrayCalc($this->setPublicGoingOutCollectPtn(
                $ptn, $record_datetime, $value_check_result, $value_check_max_times, $value_mobile_positions, $working_timetable_no, $attendance_time_index));
        } else {
            // 不明データとして作成する
            $this->pushArrayCalc($this->setPublicGoingOutCollectPtn(
                '', $record_datetime, $value_check_result, $value_check_max_times, $value_mobile_positions, $working_timetable_no, $attendance_time_index));
        }

        Log::DEBUG('---------------------- setPubliGoingOutTime end ------------------------ ');
        Log::DEBUG('        公用外出打刻処理 end');
            
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
    private function setPublicGoingOutCollectPtn(
        $ptn, $record_datetime, $value_check_result, $value_check_max_times, $value_mobile_positions, $working_timetable_no, $attendance_time_index)
    {
        Log::DEBUG('---------------------- setPublicGoingOutCollectPtn in ------------------------ ');
        $temp_calc_model = new TempCalcWorkingTime();

        if ($ptn == '1') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.public_going_out_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
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
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
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
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
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
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
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
        $value_check_result, $value_check_max_times, $value_mobile_positions, 
        $before_value_mode, $before_value_datetime, $working_timetable_no, $attendance_time_index)
    {
        Log::DEBUG('---------------------- setPublicGoingOutReturnTime in ------------------------ ');
        Log::DEBUG('        公用外出戻り打刻処理');
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
        Log::DEBUG('            attendance_from_date set = '.$attendance_from_date);
        Log::DEBUG('            timetable_from_date set = '.$timetable_from_date);
        Log::DEBUG('            timetable_to_date set = '.$timetable_to_date);
        Log::DEBUG('            attendance_to_date set = '.$attendance_to_date);
        Log::DEBUG('            record_datetime set = '.$record_datetime);
        Log::DEBUG('            record_before_datetime set = '.$record_before_datetime);
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
                    }
                }
            } elseif ($before_value_mode == Config::get('const.C005.public_going_out_time')) {      // １個前のモードが公用外出である場合
                if ($record_before_datetime < $attendance_from_date) {                      // １個前の打刻時刻 < 出勤1日のはじめ
                    // パターン２（正常戻り。勤務状態は戻り状態。）
                    $ptn = '2';
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
            $this->pushArrayCalc($this->setPublicGoingOutReturnCollectPtn(
                $ptn, $record_datetime, $value_check_result, $value_check_max_times, $value_mobile_positions, $working_timetable_no, $attendance_time_index));
        } else {
            // 不明データとして作成する
            $this->pushArrayCalc($this->setPublicGoingOutReturnCollectPtn(
                '', $record_datetime, $value_check_result, $value_check_max_times, $value_mobile_positions, $working_timetable_no, $attendance_time_index));
        }
        Log::DEBUG('        公用外出戻り打刻処理 end');
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
    private function setPublicGoingOutReturnCollectPtn(
        $ptn, $record_datetime, $value_check_result, $value_check_max_times, $value_mobile_positions, $working_timetable_no, $attendance_time_index)
    {
        Log::DEBUG('---------------------- setPublicGoingOutReturnCollectPtn in ------------------------ ');
        $temp_calc_model = new TempCalcWorkingTime();

        if ($ptn == '1') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.public_going_out_return_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
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
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.public_going_out_return'));
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
            $temp_calc_model->setWorkingtimetablenoAttribute($working_timetable_no);
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
    private function setNoInputTimePtn($ptn, $user_holiday_name, $target_date, $hpliday_date, $working_time_no)
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
            $temp_calc_model->setWorkingtimetablenoAttribute($working_time_no);
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
            $temp_calc_model->setWorkingtimetablenoAttribute($working_time_no);
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
            $temp_calc_model->setWorkingtimetablenoAttribute($working_time_no);
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
            $temp_calc_model->setWorkingtimetablenoAttribute($working_time_no);
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
            $temp_calc_model->setWorkingtimetablenoAttribute($working_time_no);
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
            $temp_calc_model->setWorkingtimetablenoAttribute($working_time_no);
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
            $temp_calc_model->setWorkingtimetablenoAttribute($working_time_no);
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
            $temp_calc_model->setWorkingtimetablenoAttribute($working_time_no);
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
        $this->array_working_timetable_no = array();
        $this->array_mobile_positions = array();
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
        if (isset($result->working_timetable_no)) {
            $this->array_working_timetable_no[] = $result->working_timetable_no;
        } else {
            $this->array_working_timetable_no[] = 0;
        }
        Log::DEBUG('------$result->mode = '.$result->mode);
        Log::DEBUG('------$result->record_datetime = '.$result->record_datetime);
        Log::DEBUG('------$result->x_positions = '.$result->x_positions);
        Log::DEBUG('------$result->y_positions = '.$result->y_positions);
        if (isset($result->x_positions) && isset($result->y_positions)) {
            $this->array_mobile_positions[] = $result->x_positions.' '.$result->y_positions;
        } else {
            $this->array_mobile_positions[] = null;
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
        $this->array_calc_working_timetable_no[] = $temp_calc_model->getWorkingtimetablenoAttribute();
        Log::DEBUG('      ◇◇◇◇◇◇◇◇◇◇◇ getWorkingtimetablenoAttribute '.$temp_calc_model->getWorkingtimetablenoAttribute());
        $this->array_calc_time[] = $temp_calc_model->getRecorddatetimeAttribute();
        $this->array_calc_status[] = $temp_calc_model->getWorkingstatusAttribute();
        Log::DEBUG('      ◇◇◇◇◇◇◇◇◇◇◇ getWorkingstatusAttribute '.$temp_calc_model->getWorkingstatusAttribute());
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

        Log::DEBUG('    登録処理　：　登録件数 count($this->array_calc_mode) = '.count($this->array_calc_mode));
        for($i=0;$i<count($this->array_calc_mode);$i++){
            $this->user_temp_seq++;
            $temp_calc_model->setSeqAttribute($this->user_temp_seq);
            Log::DEBUG('        $result->holiday_kubun = '.$result->holiday_kubun);
            Log::DEBUG('        $this->array_calc_time[$i] = '.$this->array_calc_time[$i]);
            Log::DEBUG('        $this->array_calc_status[$i] = '.$this->array_calc_status[$i]);
            Log::DEBUG('        $this->array_calc_working_timetable_no[$i] = '.$this->array_calc_working_timetable_no[$i]);
            if (isset($result->holiday_kubun)) {
                if ($result->holiday_kubun == Config::get('const.C013.morning_off') || $result->holiday_kubun == Config::get('const.C013.afternoon_off')) {
                    $temp_calc_model->setModeAttribute($this->array_calc_mode[$i]);
                    $temp_calc_model->setRecorddatetimeAttribute($this->array_calc_time[$i]);
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
                } elseif ($result->holiday_kubun == Config::get('const.C013.non_set')) {
                    $temp_calc_model->setModeAttribute($this->array_calc_mode[$i]);
                    $temp_calc_model->setRecorddatetimeAttribute($this->array_calc_time[$i]);
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
                } else {
                    $temp_calc_model->setModeAttribute(null);
                    $temp_calc_model->setRecorddatetimeAttribute(null);
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
                    $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.user_holiday'));
                }
            } else {
                $temp_calc_model->setModeAttribute($this->array_calc_mode[$i]);
                $temp_calc_model->setRecorddatetimeAttribute($this->array_calc_time[$i]);
                $temp_calc_model->setWorkingtimetablenoAttribute($this->array_calc_working_timetable_no[$i]);
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
            $temp_calc_model->setPositionsAttribute($this->array_calc_mobile_positions[$i]);

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
        // モバイル用時刻保存
        $attendance_time_positions = null;
        $leaving_time_positions = null;
        $missing_middle_time_positions = null;
        $missing_middle_return_time_positions = null;
        $public_going_out_time_positions = null;
        $public_going_out_return_time_positions = null;
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
        // 深夜労働時間
        $this->calc_late_night_working_hours = 0;
        // データ登録用出勤・退勤の労働時刻数配列
        $array_add_attendance_time = array();
        $array_add_attendance_time_positions = array();
        $array_add_leaving_time = array();
        $array_add_leaving_time_positions = array();
        // データ登録用外出・戻りの労働時刻数配列
        $array_add_missing_middle_time = array();
        $array_add_missing_middle_time_positions = array();
        $array_add_missing_middle_return_time = array();
        $array_add_missing_middle_return_time_positions = array();
        $array_add_public_going_out_time = array();
        $array_add_public_going_out_time_positions = array();
        $array_add_public_going_out_return_time = array();
        $array_add_public_going_out_return_time_positions = array();
        // ユーザー休暇区分判定用
        $before_holiday_date = null;
        $before_holiday_user_code = null;
        $before_holiday_department_code = null;
        $before_holiday_kubun = null;
        $before_holiday_set = false;

        $apicommon = new ApiCommonController();
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
            Log::DEBUG('        勤務状態 =  '.$result->working_status);
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
                if ($result->mode == Config::get('const.C005.attendance_time')) {
                    $attendance_time = $result->record_datetime;
                    if (isset($result->x_positions) && isset($result->y_positions)) {
                        $attendance_time_positions = $result->x_positions.' '.$result->y_positions;
                    } else {
                        $attendance_time_positions = null;
                    }               
                }
                if ($result->mode == Config::get('const.C005.leaving_time')) {
                    $leaving_time = $result->record_datetime;
                    if (isset($result->x_positions) && isset($result->y_positions)) {
                        $leaving_time_positions = $result->x_positions.' '.$result->y_positions;
                    } else {
                        $leaving_time_positions = null;
                    }               
                }
                if ($result->mode == Config::get('const.C005.missing_middle_time')) {
                    $missing_middle_time = $result->record_datetime;
                    if (isset($result->x_positions) && isset($result->y_positions)) {
                        $missing_middle_time_positions = $result->x_positions.' '.$result->y_positions;
                    } else {
                        $missing_middle_time_positions = null;
                    }               
                }
                Log::DEBUG('    私用外出戻り?? = '.$result->mode);
                if ($result->mode == Config::get('const.C005.missing_middle_return_time')) {
                    Log::DEBUG('    私用外出戻り = '.$result->record_datetime);
                    Log::DEBUG('    私用外出戻り = '.$result->x_positions);
                    Log::DEBUG('    私用外出戻り = '.$result->y_positions);
                    $missing_middle_return_time = $result->record_datetime;
                    if (isset($result->x_positions) && isset($result->y_positions)) {
                        $missing_middle_return_time_positions = $result->x_positions.' '.$result->y_positions;
                    } else {
                        $missing_middle_return_time_positions = null;
                    }               
                    Log::DEBUG('    私用外出戻り missing_middle_return_time_positions = '.$missing_middle_return_time_positions);
                }
                if ($result->mode == Config::get('const.C005.public_going_out_time')) {
                    $public_going_out_time = $result->record_datetime;
                    if (isset($result->x_positions) && isset($result->y_positions)) {
                        $public_going_out_time_positions = $result->x_positions.' '.$result->y_positions;
                    } else {
                        $public_going_out_time_positions = null;
                    }               
                }
                if ($result->mode == Config::get('const.C005.public_going_out_return_time')) {
                    $public_going_out_return_time = $result->record_datetime;
                    if (isset($result->x_positions) && isset($result->y_positions)) {
                        $public_going_out_return_time_positions = $result->x_positions.' '.$result->y_positions;
                    } else {
                        $public_going_out_return_time_positions = null;
                    }               
                }
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
                $set_calcTimes_flg = false;
                if ($result->current_calc == '1') {     // 当日分である場合
                    // ユーザー休暇区分判定用
                    $before_holiday_date = null;
                    $before_holiday_user_code = null;
                    $before_holiday_department_code = null;
                    $before_holiday_kubun = null;
                    // 計算セットフラグ　　calcTimes実行ならtrueにする
                    // ----------------------- 私用外出 -------------------------------------------
                    // 私用外出は複数ある可能性があるので私用外出計算は戻り時点で計算する。
                    if ($result->mode == Config::get('const.C005.missing_middle_time') && $missing_middle_time <> ''){
                        Log::DEBUG('        私用外出 $missing_middle_time = '.$missing_middle_time);
                        Log::DEBUG('        私用外出 $missing_middle_time_positions = '.$missing_middle_time_positions);
                        $array_add_missing_middle_time[] = $missing_middle_time;
                        $array_add_missing_middle_time_positions[] = $missing_middle_time_positions;
                    }
                    if ($result->mode == Config::get('const.C005.missing_middle_return_time') && $missing_middle_return_time <> ''){
                        Log::DEBUG('        私用外出戻り $missing_middle_return_time = '.$missing_middle_return_time);
                        Log::DEBUG('        私用外出戻り $array_add_missing_middle_return_time_positions = '.$missing_middle_return_time_positions);
                        $array_add_missing_middle_return_time[] = $missing_middle_return_time;
                        $array_add_missing_middle_return_time_positions[] = $missing_middle_return_time_positions;
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
                                        $array_missing_middle_time
                                    );
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
                        $array_add_public_going_out_time_positions[] = $public_going_out_time_positions;
                    }
                    Log::DEBUG('        公用外出戻り $public_going_out_return_time = '.$public_going_out_return_time);
                    if ($result->mode == Config::get('const.C005.public_going_out_return_time') && $public_going_out_return_time <> ''){
                        $array_add_public_going_out_return_time[] = $public_going_out_return_time;
                        $array_add_public_going_out_return_time_positions[] = $public_going_out_return_time_positions;
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
                                        $array_public_going_out_time
                                    );
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
                        $array_add_attendance_time_positions[] = $attendance_time_positions;
                    }
                    // ----------------------- 退勤 -------------------------------------------
                    // 退勤データの場合計算開始
                    if ($result->mode == Config::get('const.C005.leaving_time') && $leaving_time <> ''){
                        Log::DEBUG('        出勤退勤データ集計  count($array_working_time_kubun) = '.count($array_working_time_kubun));
                        $array_add_leaving_time[] = $leaving_time;
                        $array_add_leaving_time_positions[] = $leaving_time_positions;
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
                                        $apicommon->roundTimeByTime($current_date, $leaving_time, $result->time_unit, $result->time_rounding),
                                        $array_calc_time,
                                        $array_missing_middle_time
                                    );
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
                                        $apicommon->roundTimeByTime($current_date, $leaving_time, $result->time_unit, $result->time_rounding));
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
                    Log::DEBUG('        temp_working_time_datesデータ作成事前条件チェック $set_calcTimes_flg = '.$set_calcTimes_flg);
                    Log::DEBUG('        temp_working_time_datesデータ作成事前条件チェック $mode_chk = '.$mode_chk);
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
                                $array_add_attendance_time_positions,
                                $array_add_leaving_time,
                                $array_add_leaving_time_positions,
                                $array_add_missing_middle_time,
                                $array_add_missing_middle_time_positions,
                                $array_add_missing_middle_return_time,
                                $array_add_missing_middle_return_time_positions,
                                $array_add_public_going_out_time,
                                $array_add_public_going_out_time_positions,
                                $array_add_public_going_out_return_time,
                                $array_add_public_going_out_return_time_positions);
                            Log::DEBUG('        temp_working_time_datesデータ作成終了 '.$current_user_code);
                            // 次データ計算事前処理
                            $array_result_NextData =
                                $this->calcTempWorkingTimeDateNextData(
                                    $array_working_time_kubun,
                                    $result
                                );
                            $array_calc_time = $array_result_NextData['array_calc_time'];
                            $array_missing_middle_time = $array_result_NextData['array_missing_middle_time'];
                            $array_public_going_out_time = $array_result_NextData['array_public_going_out_time'];
                            $array_add_attendance_time = $array_result_NextData['array_add_attendance_time'];
                            $array_add_leaving_time = $array_result_NextData['array_add_leaving_time'];
                            $array_add_missing_middle_time = $array_result_NextData['array_add_missing_middle_time'];
                            $array_add_missing_middle_return_time = $array_result_NextData['array_add_missing_middle_return_time'];
                            $array_add_public_going_out_time = $array_result_NextData['array_add_public_going_out_time'];
                            $array_add_public_going_out_return_time = $array_result_NextData['array_add_public_going_out_return_time'];
                            $array_add_attendance_time_positions = $array_result_NextData['array_add_attendance_time_positions'];
                            $array_add_leaving_time_positions = $array_result_NextData['array_add_leaving_time_positions'];
                            $array_add_missing_middle_time_positions = $array_result_NextData['array_add_missing_middle_time_positions'];
                            $array_add_missing_middle_return_time_positions = $array_result_NextData['array_add_missing_middle_return_time_positions'];
                            $array_add_public_going_out_time_positions = $array_result_NextData['array_add_public_going_out_time_positions'];
                            $array_add_public_going_out_return_time_positions = $array_result_NextData['array_add_public_going_out_return_time_positions'];
                            $attendance_time = $array_result_NextData['attendance_time'];
                            $leaving_time = $array_result_NextData['leaving_time'];
                            $missing_middle_time = $array_result_NextData['missing_middle_time'];
                            $missing_middle_return_time = $array_result_NextData['missing_middle_return_time'];
                            $public_going_out_time = $array_result_NextData['public_going_out_time'];
                            $public_going_out_return_time = $array_result_NextData['public_going_out_return_time'];
                            $attendance_time_positions = $array_result_NextData['attendance_time_positions'];
                            $leaving_time_positions = $array_result_NextData['leaving_time_positions'];
                            $missing_middle_time_positions = $array_result_NextData['missing_middle_time_positions'];
                            $missing_middle_return_time_positions = $array_result_NextData['missing_middle_return_time_positions'];
                            $public_going_out_time_positions = $array_result_NextData['public_going_out_time_positions'];
                            $public_going_out_return_time_positions = $array_result_NextData['public_going_out_return_time_positions'];
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
                    Log::DEBUG('    当日分計算対象外');
                    // ----------------------- 私用外出 -------------------------------------------
                    Log::DEBUG('    私用外出打刻時刻 = '.$missing_middle_time);
                    if ($result->mode == Config::get('const.C005.missing_middle_time') && $missing_middle_time <> ''){
                        $array_add_missing_middle_time[] = $missing_middle_time;
                        $array_add_missing_middle_time_positions[] = $missing_middle_time_positions;
                    }
                    Log::DEBUG('    私用外出戻り打刻時刻 = '.$missing_middle_return_time);
                    if ($result->mode == Config::get('const.C005.missing_middle_return_time') && $missing_middle_return_time <> ''){
                        $array_add_missing_middle_return_time[] = $missing_middle_return_time;
                        $array_add_missing_middle_return_time_positions[] = $missing_middle_return_time_positions;
                    }
                    // ----------------------- 公用外出 -------------------------------------------
                    Log::DEBUG('    公用外出打刻時刻 = '.$public_going_out_time);
                    if ($result->mode == Config::get('const.C005.public_going_out_time') && $public_going_out_time <> ''){
                        $array_add_public_going_out_time[] = $public_going_out_time;
                        $array_add_public_going_out_time_positions[] = $public_going_out_time_positions;
                    }
                    Log::DEBUG('    公用外出戻り打刻時刻 = '.$public_going_out_return_time);
                    if ($result->mode == Config::get('const.C005.public_going_out_return_time') && $public_going_out_return_time <> ''){
                        $array_add_public_going_out_return_time[] = $public_going_out_return_time;
                        $array_add_public_going_out_return_time_positions[] = $public_going_out_return_time_positions;
                    }
                    // ----------------------- 出勤 -------------------------------------------
                    Log::DEBUG('    出勤打刻時刻 = '.$attendance_time);
                    if ($result->mode == Config::get('const.C005.attendance_time') && $attendance_time <> ''){
                        $array_add_attendance_time[] = $attendance_time;
                        $array_add_attendance_time_positions[] = $attendance_time_positions;
                    }
                    // ----------------------- 退勤 -------------------------------------------
                    Log::DEBUG('    退勤打刻時刻 = '.$leaving_time);
                    if ($result->mode == Config::get('const.C005.leaving_time') && $leaving_time <> ''){
                        $array_add_leaving_time[] = $leaving_time;
                        $array_add_leaving_time_positions[] = $leaving_time_positions;
                    }
                    // 前のデータが計算対象であれば出力する
                    // 計算セットフラグ
                    if ($set_calcTimes_flg) {
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
                            $array_add_attendance_time_positions,
                            $array_add_leaving_time,
                            $array_add_leaving_time_positions,
                            $array_add_missing_middle_time,
                            $array_add_missing_middle_time_positions,
                            $array_add_missing_middle_return_time,
                            $array_add_missing_middle_return_time_positions,
                            $array_add_public_going_out_time,
                            $array_add_public_going_out_time_positions,
                            $array_add_public_going_out_return_time,
                            $array_add_public_going_out_return_time_positions);
                        Log::DEBUG('        temp_working_time_datesデータ作成終了 '.$before_user_code);
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
                            $array_add_attendance_time_positions,
                            $array_add_leaving_time,
                            $array_add_leaving_time_positions,
                            $array_add_missing_middle_time,
                            $array_add_missing_middle_time_positions,
                            $array_add_missing_middle_return_time,
                            $array_add_missing_middle_return_time_positions,
                            $array_add_public_going_out_time,
                            $array_add_public_going_out_time_positions,
                            $array_add_public_going_out_return_time,
                            $array_add_public_going_out_return_time_positions);
                        Log::DEBUG('        temp_working_time_datesデータ作成終了 '.$before_user_code);
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
                    $array_calc_time = $array_result_NextData['array_calc_time'];
                    $array_missing_middle_time = $array_result_NextData['array_missing_middle_time'];
                    $array_public_going_out_time = $array_result_NextData['array_public_going_out_time'];
                    $array_add_attendance_time = $array_result_NextData['array_add_attendance_time'];
                    $array_add_leaving_time = $array_result_NextData['array_add_leaving_time'];
                    $array_add_missing_middle_time = $array_result_NextData['array_add_missing_middle_time'];
                    $array_add_missing_middle_return_time = $array_result_NextData['array_add_missing_middle_return_time'];
                    $array_add_public_going_out_time = $array_result_NextData['array_add_public_going_out_time'];
                    $array_add_public_going_out_return_time = $array_result_NextData['array_add_public_going_out_return_time'];
                    $array_add_attendance_time_positions = $array_result_NextData['array_add_attendance_time_positions'];
                    $array_add_leaving_time_positions = $array_result_NextData['array_add_leaving_time_positions'];
                    $array_add_missing_middle_time_positions = $array_result_NextData['array_add_missing_middle_time_positions'];
                    $array_add_missing_middle_return_time_positions = $array_result_NextData['array_add_missing_middle_return_time_positions'];
                    $array_add_public_going_out_time_positions = $array_result_NextData['array_add_public_going_out_time_positions'];
                    $array_add_public_going_out_return_time_positions = $array_result_NextData['array_add_public_going_out_return_time_positions'];
                    $attendance_time = $array_result_NextData['attendance_time'];
                    $leaving_time = $array_result_NextData['leaving_time'];
                    $missing_middle_time = $array_result_NextData['missing_middle_time'];
                    $missing_middle_return_time = $array_result_NextData['missing_middle_return_time'];
                    $public_going_out_time = $array_result_NextData['public_going_out_time'];
                    $public_going_out_return_time = $array_result_NextData['public_going_out_return_time'];
                    $attendance_time_positions = $array_result_NextData['attendance_time_positions'];
                    $leaving_time_positions = $array_result_NextData['leaving_time_positions'];
                    $missing_middle_time_positions = $array_result_NextData['missing_middle_time_positions'];
                    $missing_middle_return_time_positions = $array_result_NextData['missing_middle_return_time_positions'];
                    $public_going_out_time_positions = $array_result_NextData['public_going_out_time_positions'];
                    $public_going_out_return_time_positions = $array_result_NextData['public_going_out_return_time_positions'];
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
                            $array_add_attendance_time_positions,
                            $array_add_leaving_time,
                            $array_add_leaving_time_positions,
                            $array_add_missing_middle_time,
                            $array_add_missing_middle_time_positions,
                            $array_add_missing_middle_return_time,
                            $array_add_missing_middle_return_time_positions,
                            $array_add_public_going_out_time,
                            $array_add_public_going_out_time_positions,
                            $array_add_public_going_out_return_time,
                            $array_add_public_going_out_return_time_positions);
                        Log::DEBUG('        temp_working_time_datesデータ作成終了 '.$current_user_code);
                        // 次データ計算事前処理
                        $array_result_NextData =
                            $this->calcTempWorkingTimeDateNextData(
                                $array_working_time_kubun,
                                $result
                            );
                        $array_calc_time = $array_result_NextData['array_calc_time'];
                        $array_missing_middle_time = $array_result_NextData['array_missing_middle_time'];
                        $array_public_going_out_time = $array_result_NextData['array_public_going_out_time'];
                        $array_add_attendance_time = $array_result_NextData['array_add_attendance_time'];
                        $array_add_leaving_time = $array_result_NextData['array_add_leaving_time'];
                        $array_add_missing_middle_time = $array_result_NextData['array_add_missing_middle_time'];
                        $array_add_missing_middle_return_time = $array_result_NextData['array_add_missing_middle_return_time'];
                        $array_add_public_going_out_time = $array_result_NextData['array_add_public_going_out_time'];
                        $array_add_public_going_out_return_time = $array_result_NextData['array_add_public_going_out_return_time'];
                        $array_add_attendance_time_positions = $array_result_NextData['array_add_attendance_time_positions'];
                        $array_add_leaving_time_positions = $array_result_NextData['array_add_leaving_time_positions'];
                        $array_add_missing_middle_time_positions = $array_result_NextData['array_add_missing_middle_time_positions'];
                        $array_add_missing_middle_return_time_positions = $array_result_NextData['array_add_missing_middle_return_time_positions'];
                        $array_add_public_going_out_time_positions = $array_result_NextData['array_add_public_going_out_time_positions'];
                        $array_add_public_going_out_return_time_positions = $array_result_NextData['array_add_public_going_out_return_time_positions'];
                        $attendance_time = $array_result_NextData['attendance_time'];
                        $leaving_time = $array_result_NextData['leaving_time'];
                        $missing_middle_time = $array_result_NextData['missing_middle_time'];
                        $missing_middle_return_time = $array_result_NextData['missing_middle_return_time'];
                        $public_going_out_time = $array_result_NextData['public_going_out_time'];
                        $public_going_out_return_time = $array_result_NextData['public_going_out_return_time'];
                        $attendance_time_positions = $array_result_NextData['attendance_time_positions'];
                        $leaving_time_positions = $array_result_NextData['leaving_time_positions'];
                        $missing_middle_time_positions = $array_result_NextData['missing_middle_time_positions'];
                        $missing_middle_return_time_positions = $array_result_NextData['missing_middle_return_time_positions'];
                        $public_going_out_time_positions = $array_result_NextData['public_going_out_time_positions'];
                        $public_going_out_return_time_positions = $array_result_NextData['public_going_out_return_time_positions'];
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
                        Log::DEBUG('当日分　勤務状態 現在���刻 =  '.$dtNow);
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
                            $array_add_attendance_time_positions,
                            $array_add_leaving_time,
                            $array_add_leaving_time_positions,
                            $array_add_missing_middle_time,
                            $array_add_missing_middle_time_positions,
                            $array_add_missing_middle_return_time,
                            $array_add_missing_middle_return_time_positions,
                            $array_add_public_going_out_time,
                            $array_add_public_going_out_time_positions,
                            $array_add_public_going_out_return_time,
                            $array_add_public_going_out_return_time_positions);
                    Log::DEBUG('        temp_working_time_datesデータ作成終了 '.$before_user_code);
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
                    $array_calc_time = $array_result_NextData['array_calc_time'];
                    $array_missing_middle_time = $array_result_NextData['array_missing_middle_time'];
                    $array_public_going_out_time = $array_result_NextData['array_public_going_out_time'];
                    $array_add_attendance_time = $array_result_NextData['array_add_attendance_time'];
                    $array_add_leaving_time = $array_result_NextData['array_add_leaving_time'];
                    $array_add_missing_middle_time = $array_result_NextData['array_add_missing_middle_time'];
                    $array_add_missing_middle_return_time = $array_result_NextData['array_add_missing_middle_return_time'];
                    $array_add_public_going_out_time = $array_result_NextData['array_add_public_going_out_time'];
                    $array_add_public_going_out_return_time = $array_result_NextData['array_add_public_going_out_return_time'];
                    $array_add_attendance_time_positions = $array_result_NextData['array_add_attendance_time_positions'];
                    $array_add_leaving_time_positions = $array_result_NextData['array_add_leaving_time_positions'];
                    $array_add_missing_middle_time_positions = $array_result_NextData['array_add_missing_middle_time_positions'];
                    $array_add_missing_middle_return_time_positions = $array_result_NextData['array_add_missing_middle_return_time_positions'];
                    $array_add_public_going_out_time_positions = $array_result_NextData['array_add_public_going_out_time_positions'];
                    $array_add_public_going_out_return_time_positions = $array_result_NextData['array_add_public_going_out_return_time_positions'];
                    $attendance_time = $array_result_NextData['attendance_time'];
                    $leaving_time = $array_result_NextData['leaving_time'];
                    $missing_middle_time = $array_result_NextData['missing_middle_time'];
                    $missing_middle_return_time = $array_result_NextData['missing_middle_return_time'];
                    $public_going_out_time = $array_result_NextData['public_going_out_time'];
                    $public_going_out_return_time = $array_result_NextData['public_going_out_return_time'];
                    $attendance_time_positions = $array_result_NextData['attendance_time_positions'];
                    $leaving_time_positions = $array_result_NextData['leaving_time_positions'];
                    $missing_middle_time_positions = $array_result_NextData['missing_middle_time_positions'];
                    $missing_middle_return_time_positions = $array_result_NextData['missing_middle_return_time_positions'];
                    $public_going_out_time_positions = $array_result_NextData['public_going_out_time_positions'];
                    $public_going_out_return_time_positions = $array_result_NextData['public_going_out_return_time_positions'];
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
                            $array_add_attendance_time_positions,
                            $array_add_leaving_time,
                            $array_add_leaving_time_positions,
                            $array_add_missing_middle_time,
                            $array_add_missing_middle_time_positions,
                            $array_add_missing_middle_return_time,
                            $array_add_missing_middle_return_time_positions,
                            $array_add_public_going_out_time,
                            $array_add_public_going_out_time_positions,
                            $array_add_public_going_out_return_time,
                            $array_add_public_going_out_return_time_positions);
                        Log::DEBUG('        temp_working_time_datesデータ作成終了 '.$current_user_code);
                        // 次データ計算事前処理
                        $array_result_NextData =
                            $this->calcTempWorkingTimeDateNextData(
                                $array_working_time_kubun,
                                $result
                            );
                        $array_calc_time = $array_result_NextData['array_calc_time'];
                        $array_missing_middle_time = $array_result_NextData['array_missing_middle_time'];
                        $array_public_going_out_time = $array_result_NextData['array_public_going_out_time'];
                        $array_add_attendance_time = $array_result_NextData['array_add_attendance_time'];
                        $array_add_leaving_time = $array_result_NextData['array_add_leaving_time'];
                        $array_add_missing_middle_time = $array_result_NextData['array_add_missing_middle_time'];
                        $array_add_missing_middle_return_time = $array_result_NextData['array_add_missing_middle_return_time'];
                        $array_add_public_going_out_time = $array_result_NextData['array_add_public_going_out_time'];
                        $array_add_public_going_out_return_time = $array_result_NextData['array_add_public_going_out_return_time'];
                        $array_add_attendance_time_positions = $array_result_NextData['array_add_attendance_time_positions'];
                        $array_add_leaving_time_positions = $array_result_NextData['array_add_leaving_time_positions'];
                        $array_add_missing_middle_time_positions = $array_result_NextData['array_add_missing_middle_time_positions'];
                        $array_add_missing_middle_return_time_positions = $array_result_NextData['array_add_missing_middle_return_time_positions'];
                        $array_add_public_going_out_time_positions = $array_result_NextData['array_add_public_going_out_time_positions'];
                        $array_add_public_going_out_return_time_positions = $array_result_NextData['array_add_public_going_out_return_time_positions'];
                        $attendance_time = $array_result_NextData['attendance_time'];
                        $leaving_time = $array_result_NextData['leaving_time'];
                        $missing_middle_time = $array_result_NextData['missing_middle_time'];
                        $missing_middle_return_time = $array_result_NextData['missing_middle_return_time'];
                        $public_going_out_time = $array_result_NextData['public_going_out_time'];
                        $public_going_out_return_time = $array_result_NextData['public_going_out_return_time'];
                        $attendance_time_positions = $array_result_NextData['attendance_time_positions'];
                        $leaving_time_positions = $array_result_NextData['leaving_time_positions'];
                        $missing_middle_time_positions = $array_result_NextData['missing_middle_time_positions'];
                        $missing_middle_return_time_positions = $array_result_NextData['missing_middle_return_time_positions'];
                        $public_going_out_time_positions = $array_result_NextData['public_going_out_time_positions'];
                        $public_going_out_return_time_positions = $array_result_NextData['public_going_out_return_time_positions'];
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
                            $array_add_attendance_time_positions,
                            $array_add_leaving_time,
                            $array_add_leaving_time_positions,
                            $array_add_missing_middle_time,
                            $array_add_missing_middle_time_positions,
                            $array_add_missing_middle_return_time,
                            $array_add_missing_middle_return_time_positions,
                            $array_add_public_going_out_time,
                            $array_add_public_going_out_time_positions,
                            $array_add_public_going_out_return_time,
                            $array_add_public_going_out_return_time_positions);
                    Log::DEBUG('        temp_working_time_datesデータ作成終了 '.$before_user_code);
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
                    $array_calc_time = $array_result_NextData['array_calc_time'];
                    $array_missing_middle_time = $array_result_NextData['array_missing_middle_time'];
                    $array_public_going_out_time = $array_result_NextData['array_public_going_out_time'];
                    $array_add_attendance_time = $array_result_NextData['array_add_attendance_time'];
                    $array_add_leaving_time = $array_result_NextData['array_add_leaving_time'];
                    $array_add_missing_middle_time = $array_result_NextData['array_add_missing_middle_time'];
                    $array_add_missing_middle_return_time = $array_result_NextData['array_add_missing_middle_return_time'];
                    $array_add_public_going_out_time = $array_result_NextData['array_add_public_going_out_time'];
                    $array_add_public_going_out_return_time = $array_result_NextData['array_add_public_going_out_return_time'];
                    $array_add_attendance_time_positions = $array_result_NextData['array_add_attendance_time_positions'];
                    $array_add_leaving_time_positions = $array_result_NextData['array_add_leaving_time_positions'];
                    $array_add_missing_middle_time_positions = $array_result_NextData['array_add_missing_middle_time_positions'];
                    $array_add_missing_middle_return_time_positions = $array_result_NextData['array_add_missing_middle_return_time_positions'];
                    $array_add_public_going_out_time_positions = $array_result_NextData['array_add_public_going_out_time_positions'];
                    $array_add_public_going_out_return_time_positions = $array_result_NextData['array_add_public_going_out_return_time_positions'];
                    $attendance_time = $array_result_NextData['attendance_time'];
                    $leaving_time = $array_result_NextData['leaving_time'];
                    $missing_middle_time = $array_result_NextData['missing_middle_time'];
                    $missing_middle_return_time = $array_result_NextData['missing_middle_return_time'];
                    $public_going_out_time = $array_result_NextData['public_going_out_time'];
                    $public_going_out_return_time = $array_result_NextData['public_going_out_return_time'];
                    $attendance_time_positions = $array_result_NextData['attendance_time_positions'];
                    $leaving_time_positions = $array_result_NextData['leaving_time_positions'];
                    $missing_middle_time_positions = $array_result_NextData['missing_middle_time_positions'];
                    $missing_middle_return_time_positions = $array_result_NextData['missing_middle_return_time_positions'];
                    $public_going_out_time_positions = $array_result_NextData['public_going_out_time_positions'];
                    $public_going_out_return_time_positions = $array_result_NextData['public_going_out_return_time_positions'];
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
                            $array_add_attendance_time_positions,
                            $array_add_leaving_time,
                            $array_add_leaving_time_positions,
                            $array_add_missing_middle_time,
                            $array_add_missing_middle_time_positions,
                            $array_add_missing_middle_return_time,
                            $array_add_missing_middle_return_time_positions,
                            $array_add_public_going_out_time,
                            $array_add_public_going_out_time_positions,
                            $array_add_public_going_out_return_time,
                            $array_add_public_going_out_return_time_positions);
                        Log::DEBUG('        temp_working_time_datesデータ作成終了 '.$current_user_code);
                        // 次データ計算事前処理
                        $array_result_NextData =
                            $this->calcTempWorkingTimeDateNextData(
                                $array_working_time_kubun,
                                $result
                            );
                        $array_calc_time = $array_result_NextData['array_calc_time'];
                        $array_missing_middle_time = $array_result_NextData['array_missing_middle_time'];
                        $array_public_going_out_time = $array_result_NextData['array_public_going_out_time'];
                        $array_add_attendance_time = $array_result_NextData['array_add_attendance_time'];
                        $array_add_leaving_time = $array_result_NextData['array_add_leaving_time'];
                        $array_add_missing_middle_time = $array_result_NextData['array_add_missing_middle_time'];
                        $array_add_missing_middle_return_time = $array_result_NextData['array_add_missing_middle_return_time'];
                        $array_add_public_going_out_time = $array_result_NextData['array_add_public_going_out_time'];
                        $array_add_public_going_out_return_time = $array_result_NextData['array_add_public_going_out_return_time'];
                        $array_add_attendance_time_positions = $array_result_NextData['array_add_attendance_time_positions'];
                        $array_add_leaving_time_positions = $array_result_NextData['array_add_leaving_time_positions'];
                        $array_add_missing_middle_time_positions = $array_result_NextData['array_add_missing_middle_time_positions'];
                        $array_add_missing_middle_return_time_positions = $array_result_NextData['array_add_missing_middle_return_time_positions'];
                        $array_add_public_going_out_time_positions = $array_result_NextData['array_add_public_going_out_time_positions'];
                        $array_add_public_going_out_return_time_positions = $array_result_NextData['array_add_public_going_out_return_time_positions'];
                        $attendance_time = $array_result_NextData['attendance_time'];
                        $leaving_time = $array_result_NextData['leaving_time'];
                        $missing_middle_time = $array_result_NextData['missing_middle_time'];
                        $missing_middle_return_time = $array_result_NextData['missing_middle_return_time'];
                        $public_going_out_time = $array_result_NextData['public_going_out_time'];
                        $public_going_out_return_time = $array_result_NextData['public_going_out_return_time'];
                        $attendance_time_positions = $array_result_NextData['attendance_time_positions'];
                        $leaving_time_positions = $array_result_NextData['leaving_time_positions'];
                        $missing_middle_time_positions = $array_result_NextData['missing_middle_time_positions'];
                        $missing_middle_return_time_positions = $array_result_NextData['missing_middle_return_time_positions'];
                        $public_going_out_time_positions = $array_result_NextData['public_going_out_time_positions'];
                        $public_going_out_return_time_positions = $array_result_NextData['public_going_out_return_time_positions'];
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
                        $array_add_attendance_time_positions,
                        $array_add_leaving_time,
                        $array_add_leaving_time_positions,
                        $array_add_missing_middle_time,
                        $array_add_missing_middle_time_positions,
                        $array_add_missing_middle_return_time,
                        $array_add_missing_middle_return_time_positions,
                        $array_add_public_going_out_time,
                        $array_add_public_going_out_time_positions,
                        $array_add_public_going_out_return_time,
                        $array_add_public_going_out_return_time_positions);
        }catch(\PDOException $pe){
                    $add_result = false;
                    throw $pe;
                }catch(\Exception $e){
                    Log::DEBUG('Exception'.$e->getMessage());
                    $add_result = false;
                    throw $e;
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
            Log::DEBUG(' ◆◆◆◆◆　労働時間計算　 ◆◆◆◆◆◆');
            Log::DEBUG('            from_time = '.$from_time);
            Log::DEBUG('            to_time = '.$to_time);
            if (isset($from_time) && isset($to_time)) {
                // from_time日付付与
                $working_time_from_time = $apicommon->convTimeToDateFrom($from_time, $current_date, $target_from_time, $target_to_time);         
                $working_time_calc_from = $working_time_from_time;
                // to_time日付付与
                $working_time_to_time = $apicommon->convTimeToDateTo($from_time, $to_time, $current_date, $target_from_time, $target_to_time);         
                $working_time_calc_to = $working_time_to_time;
                // ------------------ DEBUG strat ----------------------------------------
                Log::DEBUG(' 　　　　　　working_time_kubun = '.$working_time_kubun);
                Log::DEBUG('            working_time_from_time = '.$working_time_from_time);
                Log::DEBUG('　　　　　　 出勤時刻または外出  target_from_time = '.$target_from_time);
                Log::DEBUG('            退勤時刻または戻り  target_to_time = '.$target_to_time);
                Log::DEBUG('            設定開始時刻  working_time_calc_from = '.$working_time_calc_from);
                Log::DEBUG('            設定終了時刻  working_time_calc_to = '.$working_time_calc_to);
                Log::DEBUG('            inc = '.$inc);
                // ------------------ DEBUG end ----------------------------------------
                // 深夜労働残業時間以外の場合
                if ($working_time_kubun != Config::get('const.C004.out_of_regular_night_working_time') ||
                    $inc == Config::get('const.INC_NO.missing_return') ||
                    $inc == Config::get('const.INC_NO.public_going_out_return')) {
                    // 打刻時刻$targetが所定時間内$working_timeの場合
                    if ($apicommon->chkBetweenTime($target_from_time, $target_to_time, $working_time_calc_from, $working_time_calc_to)) {
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
                            Log::DEBUG('          　深夜労働残業時間以外の場合');
                            Log::DEBUG(' 　　　　　　打刻時刻が所定時間内の場合 ');
                            Log::DEBUG('　　　　　　 計算開始時刻  working_time_calc_from = '.$working_time_calc_from);
                            Log::DEBUG('　　　　　　 計算終了時刻  working_time_calc_to = '.$working_time_calc_to);
                            Log::DEBUG('            労働時間      calc_times = '.$calc_times."  ".$calc_times / 60 / 60);
                            Log::DEBUG('            累計労働時間  working_times = '.$working_times."  ".$working_times / 60 / 60);
                            // ------------------ DEBUG end ----------------------------------------
                        }
                    }
                    // 夜勤の場合は打刻target_from_time、target_to_timeが翌日の場合があるため
                    // working_time_calc_from、working_time_calc_toを翌日にして計算する
                    $working_time_calc_from_nextday = $apicommon->getNextDay($working_time_from_time, 'Y-m-d H:i:s');
                    $working_time_calc_to_nextday = $apicommon->getNextDay($working_time_to_time, 'Y-m-d H:i:s');
                    if ($apicommon->chkBetweenTime($target_from_time, $target_to_time, $working_time_calc_from_nextday, $working_time_calc_to_nextday)) {
                        // ------------------ DEBUG strat ----------------------------------------
                        Log::DEBUG('          　夜勤の場合の翌日労働時間計算');
                        Log::DEBUG('          　打刻開始時刻  target_from_time = '.$target_from_time);
                        Log::DEBUG('          　打刻計算終了時刻  target_to_time = '.$target_to_time);
                        Log::DEBUG('          　当日計算開始時刻  working_time_calc_from = '.$working_time_calc_from);
                        Log::DEBUG('          　当日計算終了時刻  working_time_calc_to = '.$working_time_calc_to);
                        Log::DEBUG('          　翌日計算開始時刻  working_time_calc_from_nextday = '.$working_time_calc_from_nextday);
                        Log::DEBUG('          　翌日計算終了時刻  working_time_calc_to_nextday = '.$working_time_calc_to_nextday);
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
                            Log::DEBUG('          　翌日計算開始時刻 調整 working_time_calc_from_nextday = '.$working_time_calc_from_nextday);
                            Log::DEBUG('          　翌日計算終了時刻 調整 working_time_calc_to_nextday = '.$working_time_calc_to_nextday);
                            Log::DEBUG('            労働時間      calc_times = '.$calc_times."  ".$calc_times / 60 / 60);
                            Log::DEBUG('            累計労働時間  working_times = '.$working_times."  ".$working_times / 60 / 60);
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
                        Log::DEBUG('            休憩時間累計      braek_time = '.$braek_time."  ".$braek_time / 60 / 60);
                        $working_times -= $braek_time;
                    }
                } else {
                    // 深夜労働残業時間
                    // ------------------ DEBUG strat ----------------------------------------
                    Log::DEBUG('           【深夜労働残業時間 計算開始】');
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
                            Log::DEBUG('　　　　　　 計算後時刻  = '.$after_daytime);
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
                            Log::DEBUG('　　　　　　 休憩時間  = '.$braek_time);
                            $after_target_from_time = $after_daytime;
                            // $braek_timeは秒数なので
                            $after_legal_working_hours_day = $braek_time;
                        }
                        Log::DEBUG('　　　　　　 出勤時刻から休憩時間を含めた基準時間（8時間後）を求める ');
                        Log::DEBUG('　　　　　　 実働8時間後時刻  = '.$after_daytime);
                        // 実働8時間後時刻 < 深夜労働時間開始時刻
                        if ($after_daytime < $working_time_calc_from) {
                            // 退勤時刻 > 深夜労働時間開始時刻
                            if ($target_to_time > $working_time_calc_from) {
                                // 退勤時刻 < 深夜労働時間終了時刻
                                if ($target_to_time < $working_time_calc_to) {
                                    // 深夜労働時間開始時刻から退勤時刻を深夜残業時間とする
                                    $calc_times = $apicommon->diffTimeSerial($working_time_calc_from, $target_to_time);
                                    Log::DEBUG('　　　　　　深夜労働時間開始時刻から退勤時刻を深夜残業時間とする = '.$calc_times);
                                } else {
                                    // 深夜労働時間開始時刻から深夜労働時間終了時刻を深夜残業時間とする
                                    $calc_times = $apicommon->diffTimeSerial($working_time_calc_from, $working_time_calc_to);
                                    Log::DEBUG('　　　　　　深夜労働時間開始時刻から深夜労働時間終了時刻を深夜残業時間とする = '.$calc_times);
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
                                        Log::DEBUG('　　　　　　実働8時間後時刻から退勤時刻を深夜残業時間とする = '.$calc_times);
                                    } else {
                                        // 深夜労働時間開始時刻から深夜労働時間終了時刻を深夜残業時間とする
                                        $calc_times = $apicommon->diffTimeSerial($after_daytime, $working_time_calc_to);
                                        Log::DEBUG('　　　　　　実働8時間後時刻から深夜労働時間終了時刻を深夜残業時間とする = '.$calc_times);
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
                                Log::DEBUG('　　　　　　深夜労働時間開始時刻から退勤時刻を深夜労働時間とする = '.$w_time);
                            } else {
                                // 出勤時刻から退勤時刻を深夜労働時間とする
                                $w_time = $apicommon->diffTimeSerial($target_from_time, $target_to_time);
                                Log::DEBUG('　　　　　　出勤時刻から退勤時刻を深夜労働時間とする = '.$w_time);
                            }
                        } else {
                            // 出勤時刻 <= 深夜労働時間開始時刻
                            if ($target_from_time <= $working_time_calc_from) {
                                // 深夜労働時間開始時刻から深夜労働時間終了時刻を深夜労働時間とする
                                $w_time = $apicommon->diffTimeSerial($working_time_calc_from, $working_time_calc_to);
                                Log::DEBUG('　　　　　　深夜労働時間開始時刻から深夜労働時間終了時刻を深夜労働時間とする = '.$w_time);
                            } else {
                                // 出勤時刻から深夜労働時間終了時刻を深夜労働時間とする
                                $w_time = $apicommon->diffTimeSerial($target_from_time, $working_time_calc_to);
                                Log::DEBUG('　　　　　　出勤時刻から深夜労働時間終了時刻を深夜労働時間とする = '.$w_time);
                            }
                        }
                        $this->calc_late_night_working_hours += ($w_time - $calc_times);
                        $working_times += $calc_times;
                        // ------------------ DEBUG strat ----------------------------------------
                        Log::DEBUG('　　　　　　 深夜労働残業時間を計算 = '.$calc_times );
                        Log::DEBUG('　　　　　　 深夜労働残業時間を累計計算 = '.$working_times );
                        Log::DEBUG('　　　　　　 w_time = '.$w_time );
                        Log::DEBUG('　　　　　　 深夜労働時間集計を計算 $w_time - $calc_times = '.($w_time - $calc_times));
                        Log::DEBUG('　　　　　　 深夜労働時間集計を累計計算 = '.$this->calc_late_night_working_hours);
                        // ------------------ DEBUG end ----------------------------------------
                        // さらにシフト勤務などは所定労働時間と重複する時間となりうるので、重複時間があれば減算する。
                        // Log::DEBUG('　　　　　　 タイムテーブルNO = '.$working_timetable_no);
                        // $filtered_regular = 
                        //     $timetables->where('no', $working_timetable_no)->where('working_time_kubun', Config::get('const.C004.regular_working_time'));
                        // foreach($filtered_regular as $result_regular) {
                        //     Log::DEBUG('　　　　　　 重複時間 $result_regular->from_time = '.$result_regular->from_time);
                        //     Log::DEBUG('　　　　　　 重複時間 $result_regular->to_time = '.$result_regular->to_time);
                        //     // 所定労働時間登録の開始時間
                        //     $from_time_regular = $result_regular->from_time;
                        //     // 所定労働時間登録の終了時間
                        //     $to_time_regular = $result_regular->to_time;
                        //     if (isset($from_time_regular) && isset($to_time_regular)) {
                        //         // from_time_regular日付付与
                        //         // fromdate
                        //         $working_time_calc_from_regular = 
                        //             $apicommon->convTimeToDateFrom($from_time_regular, $current_date, $target_from_time, $target_to_time);         
                        //         // to_time_regular日付付与
                        //         $working_time_calc_to_regular = 
                        //             $apicommon->convTimeToDateTo($from_time_regular, $to_time_regular, $current_date, $target_from_time, $target_to_time);         
                        //         Log::DEBUG('　　　　　　 重複時間 所定労働時間 $working_time_calc_from_regular = '.$working_time_calc_from_regular);
                        //         Log::DEBUG('　　　　　　 重複時間 所定労働時間 $working_time_calc_to_regular = '.$working_time_calc_to_regular);
                        //         Log::DEBUG('　　　　　　 重複時間 所定労働時間 $working_time_calc_from = '.$working_time_calc_from);
                        //         Log::DEBUG('　　　　　　 重複時間 所定労働時間 $working_time_calc_to = '.$working_time_calc_to);
                        //         if (($working_time_calc_from > $working_time_calc_from_regular && $working_time_calc_from < $working_time_calc_to_regular) ||
                        //             ($working_time_calc_to > $working_time_calc_from_regular && $working_time_calc_to < $working_time_calc_to_regular)) {
                        //             // 時間登録の終了時間<=所定労働時間登録終了
                        //             if ($working_time_calc_to <= $working_time_calc_to_regular) {
                        //                 // 所定労働時間登録開始<= 時間登録の開始時間
                        //                 if ($working_time_calc_from_regular <= $working_time_calc_from) {
                        //                     // 時間登録の開始時間から時間登録の終了時間を計算する
                        //                     $calc_times_regular = $apicommon->diffTimeSerial($working_time_calc_from, $working_time_calc_to);
                        //                 } else {
                        //                     // 所定労働時間登録開始から時間登録の終了を計算する
                        //                     $calc_times_regular = $apicommon->diffTimeSerial($working_time_calc_from_regular, $working_time_calc_to);
                        //                 }
                        //             } else {
                        //                 // 所定労働時間登録開始<=時間登録の開始時間
                        //                 if ($working_time_calc_from_regular <= $working_time_calc_from) {
                        //                     // 時間登録の開始時間から所定労働時間登録終了を計算する
                        //                     $calc_times_regular = $apicommon->diffTimeSerial($working_time_calc_from, $working_time_calc_to_regular);
                        //                 } else {
                        //                     // 所定労働時間登録開始から所定労働時間登録終了を計算する
                        //                     $calc_times_regular = $apicommon->diffTimeSerial($working_time_calc_from_regular, $working_time_calc_to_regular);
                        //                 }
                        //             }
                        //         }
                        //     }
                        //     break;
                        // }       
                        // Log::DEBUG('$calc_times_regular = '.$calc_times_regular);
                        // $working_times -= $calc_times_regular;
                        // Log::DEBUG('$working_times = '.$working_times);
                    }
                }
                // 休憩時間を含んでいる場合、休憩時間累計（所定労働時間内の休憩時間を累計することになる）
                // if ($working_times != 0) {
                //     // 休憩時間を含んでいる場合、休憩時間累計を求めて減算する
                //     $filtered = $timetables->where('no', $working_timetable_no)
                //         ->where('working_time_kubun', Config::get('const.C004.regular_working_breaks_time'));
                //     // 休憩時間帯は複数あるかも
                //     foreach($filtered as $result_breaks_time) {
                //         $from_time = $result_breaks_time->from_time;        // 休憩開始時刻
                //         $to_time = $result_breaks_time->to_time;            // 休憩終了時刻
                //         Log::DEBUG('休憩時間 from_time = '.$from_time);
                //         Log::DEBUG('休憩時間 to_time = '.$to_time);
                //         if (isset($from_time) && isset($to_time)) {
                //             // from_time日付付与
                //             $time_calc_from = 
                //                 $apicommon->convTimeToDateFrom($from_time, $current_date, $target_from_time, $target_to_time);         
                //             // to_time日付付与
                //             $time_calc_to = 
                //                 $apicommon->convTimeToDateTo($from_time, $to_time, $current_date, $target_from_time, $target_to_time);         
                //             Log::DEBUG('休憩時間 time_calc_from = '.$time_calc_from);
                //             Log::DEBUG('休憩時間 time_calc_to = '.$time_calc_to);
                //             Log::DEBUG('休憩時間 working_time_calc_from = '.$working_time_calc_from);
                //             Log::DEBUG('休憩時間 working_time_calc_to = '.$working_time_calc_to);
                //             Log::DEBUG('休憩時間 target_from_time = '.$target_from_time);
                //             Log::DEBUG('休憩時間 target_to_time = '.$target_to_time);
                //             //  計算対象のタイムテーブルの開始終了日時の範囲内に休憩開始終了時刻がある場合で
                //             if (($time_calc_from > $working_time_calc_from && $time_calc_from < $working_time_calc_to) ||
                //                 ($time_calc_to > $working_time_calc_from && $time_calc_to < $working_time_calc_to)) {
                //                 //  出退勤時間の範囲内に休憩開始終了時刻がある場合に計算する
                //                 if (($time_calc_from > $target_from_time && $time_calc_from < $target_to_time) ||
                //                     ($time_calc_to > $target_from_time && $time_calc_to < $target_to_time)) {
                //                     if ($target_from_time > $time_calc_from) {
                //                         $time_calc_from = $target_from_time;
                //                     }
                //                     if ($target_to_time < $time_calc_to) {
                //                         $time_calc_to = $target_to_time;
                //                     }
                //                     Log::DEBUG('time_calc_from = '.$time_calc_from);
                //                     Log::DEBUG('time_calc_to = '.$time_calc_to);
                //                     if ($time_calc_from < $time_calc_to) {
                //                         $calc_times = $apicommon->diffTimeSerial($time_calc_from, $time_calc_to);
                //                         Log::DEBUG('休憩時間 calc_times = '.$calc_times);
                //                         $working_times -= $calc_times;
                //                         Log::DEBUG('休憩時間 $working_times = '.$working_times);
                //                     }
                //                 }
                //             }
                //         }
                //     }
                // }
            }
        }
        // ------------------ DEBUG strat ----------------------------------------
        Log::DEBUG('            累計労働時間  working_times = '.$working_times."  ".$working_times / 60 / 60);
        Log::DEBUG('---------------------- calcTimes end ------------------------ ');
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
        $array_add_attendance_time, $array_add_attendance_time_positions,
        $array_add_leaving_time, $array_add_leaving_time_positions,
        $array_add_missing_middle_time, $array_add_missing_middle_time_positions,
        $array_add_missing_middle_return_time, $array_add_missing_middle_return_time_positions,
        $array_add_public_going_out_time, $array_add_public_going_out_time_positions,
        $array_add_public_going_out_return_time, $array_add_public_going_out_return_time_positions)
    {
        Log::DEBUG('---------------------- addTempWorkingTimeDate in ------------------------ ');
        $temp_working_model = new TempWorkingTimeDate();
        $apicommon = new ApiCommonController();

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
        Log::DEBUG('  出勤時刻設定 START ');
        $attendence_note_set = false;
        $array_add_attendance_time_cnt = count($array_add_attendance_time);
        if (!$this->chkWorkingTime($array_add_attendance_time, (int)(Config::get('const.ARRAY_MAX_INDEX.attendace_time')) )) {
            $note .= Config::get('const.MEMO_DATA.MEMO_DATA_016').' '; 
            $attendence_note_set = true;
        }
        // 設定
        $array_decide_times = $this->decideWorkingTimeFrom($array_add_attendance_time, count($array_add_attendance_time));
        for ($i=0;$i<count($array_decide_times);$i++) {
            Log::DEBUG('$array_decide_times[$i] = '.$array_decide_times[$i]);
            if ($i<count($array_decide_times)) {
                $temp_working_model->setAttendancetimeAttribute($i, $array_decide_times[$i]);
                if (isset($array_add_attendance_time_positions[$i])) {
                    $temp_working_model->setAttendancetimepositionsAttribute($i, $array_add_attendance_time_positions[$i]);
                } else {
                    $temp_working_model->setAttendancetimepositionsAttribute($i, null);
                }
            } else {
                $temp_working_model->setAttendancetimeAttribute($i, null);
                $temp_working_model->setAttendancetimepositionsAttribute($i, null);
            }
        }
        Log::DEBUG('  出勤時刻設定 END ');
        Log::DEBUG('  退勤時刻設定 START ');
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
                if (isset($array_add_leaving_time_positions[$i])) {
                    $temp_working_model->setLeavingtimepositionsAttribute($i, $array_add_leaving_time_positions[$i]);
                } else {
                    $temp_working_model->setLeavingtimepositionsAttribute($i, null);
                }
            } else {
                $temp_working_model->setLeavingtimeAttribute($i, null);
                $temp_working_model->setLeavingtimepositionsAttribute($i, null);
            }
        }
        Log::DEBUG('  退勤時刻設定 END ');
        Log::DEBUG('  中抜け時刻設定 START ');
        // 中抜け打刻５回までチェック
        $missing_middle_note_set = false;
        if (!$this->chkWorkingTime($array_add_missing_middle_time,(int)(Config::get('const.ARRAY_MAX_INDEX.missing_middle_time')) )) {
            $note .= Config::get('const.MEMO_DATA.MEMO_DATA_018').' '; 
            $missing_middle_note_set = true;
        }
        // 設定
        $array_decide_times = $this->decideWorkingTimeFrom($array_add_missing_middle_time, count($array_add_missing_middle_time));
        Log::DEBUG('    中抜け count($array_decide_times) =  '.count($array_decide_times));
        for ($i=0;$i<count($array_decide_times);$i++) {
            if ($i<count($array_decide_times)) {
                $temp_working_model->setMissingmiddletimeAttribute($i, $array_decide_times[$i]);
                Log::DEBUG('    中抜け count($array_add_missing_middle_time_positions) =  '.count($array_add_missing_middle_time_positions));
                Log::DEBUG('    中抜け $array_add_missing_middle_time_positions =  '.$array_add_missing_middle_time_positions[$i]);
                if (isset($array_add_missing_middle_time_positions[$i])) {
                    $temp_working_model->setMissingmiddletimepositionsAttribute($i, $array_add_missing_middle_time_positions[$i]);
                } else {
                    $temp_working_model->setMissingmiddletimepositionsAttribute($i, null);
                }
            } else {
                $temp_working_model->setMissingmiddletimeAttribute($i, null);
                $temp_working_model->setMissingmiddletimepositionsAttribute($i, null);
            }
        }
        Log::DEBUG('  中抜け時刻設定 END ');
        Log::DEBUG('  中抜け戻り時刻設定 START ');
        // 中抜け戻り打刻５回までチェック
        if (!$this->chkWorkingTime($array_add_missing_middle_return_time,(int)(Config::get('const.ARRAY_MAX_INDEX.missing_middle_return_time')) )) {
            if ($missing_middle_note_set == false) {
                $note .= Config::get('const.MEMO_DATA.MEMO_DATA_018').' '; 
            }
        }
        // 設定
        Log::DEBUG('    before count($array_add_missing_middle_return_time) =  '.count($array_add_missing_middle_return_time));
        Log::DEBUG('    before count($array_add_missing_middle_time) =  '.count($array_add_missing_middle_time));
        $array_decide_times = $this->decideWorkingTimeTo(
            $array_add_missing_middle_return_time,
            count($array_add_missing_middle_return_time),
            $array_add_missing_middle_time,
            count($array_add_missing_middle_time));
        Log::DEBUG('    中抜け戻り count($array_decide_times) =  '.count($array_decide_times));
        for ($i=0;$i<count($array_decide_times);$i++) {
            if ($i<count($array_decide_times)) {
                $temp_working_model->setMissingmiddlereturntimeAttribute($i, $array_decide_times[$i]);
                if (isset($array_add_missing_middle_return_time_positions[$i])) {
                    $temp_working_model->setMissingmiddlereturntimepositionsAttribute($i, $array_add_missing_middle_return_time_positions[$i]);
                } else {
                    $temp_working_model->setMissingmiddlereturntimepositionsAttribute($i, null);
                }
            } else {
                $temp_working_model->setMissingmiddlereturntimeAttribute($i, null);
                $temp_working_model->setMissingmiddlereturntimepositionsAttribute($i, null);
            }
        }
        Log::DEBUG('  中抜け戻り時刻設定 END ');
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
                if (isset($array_add_public_going_out_time_positions[$i])) {
                    $temp_working_model->setPublicgoingouttimepositionsAttribute($i, $array_add_public_going_out_time_positions[$i]);
                } else {
                    $temp_working_model->setPublicgoingouttimepositionsAttribute($i, null);
                }
            } else {
                $temp_working_model->setPublicgoingouttimeAttribute($i, null);
                $temp_working_model->setPublicgoingouttimepositionsAttribute($i, null);
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
                if (isset($array_add_public_going_out_return_time_positions[$i])) {
                    $temp_working_model->setPublicgoingoutreturntimepositionsAttribute($i, $array_add_public_going_out_return_time_positions[$i]);
                } else {
                    $temp_working_model->setPublicgoingoutreturntimepositionsAttribute($i, null);
                }
            } else {
                $temp_working_model->setPublicgoingoutreturntimeAttribute($i, null);
                $temp_working_model->setPublicgoingoutreturntimepositionsAttribute($i, null);
            }
        }
        // 合計勤務時間
        $total_time = 0;
        // 残業時間
        $overtime_hours = 0;
        $index = (int)(Config::get('const.C004.regular_working_time'))-1;
        // -------------  debug ---------------------- start --------------
        Log::DEBUG('    <<< ユーザー労働時間計算 >>> '.$target_user_code);
        Log::DEBUG('        所定労働時間の計算 ');
        Log::DEBUG('        所定労働時間 10進数　$array_calc_time[$index]           = '.$array_calc_time[$index]);
        Log::DEBUG('        未就労時間   10進数 $array_missing_middle_time[$index]  = '.$array_missing_middle_time[$index]);
        // -------------  debug ---------------------- end --------------
        $w_time = 0;
        $regular_calc_time = 0;
        // $array_calc_time[$index]=0はまだ退勤していないということ
        if ($array_calc_time[$index] > 0) {
            $w_time = $array_calc_time[$index] - $array_missing_middle_time[$index];
            //$regular_calc_time = round($apicommon->roundTime($w_time, $target_result->time_unit, $target_result->time_rounding) / 60,2);
            $regular_calc_time = round($w_time / 60 / 60,2);
        }
        // -------------  debug ---------------------- start --------------
        Log::DEBUG('        所定労働時間　- 未就労時間　10進数　$w_time　　　　　　　　= '.$w_time);
        Log::DEBUG('        所定労働時間　- 未就労時間　60進数　$regular_calc_time    = '.$regular_calc_time);
        // -------------  debug ---------------------- end --------------
        // 時間外労働時間
        $index = (int)(Config::get('const.C004.out_of_regular_working_time'))-1;
        // -------------  debug ---------------------- start --------------
        Log::DEBUG('        時間外労働時間の計算 ');
        Log::DEBUG('        時間外労働時間 10進数　$array_calc_time[$index]           = '.$array_calc_time[$index]);
        Log::DEBUG('        未就労時間   10進数 $array_missing_middle_time[$index]  = '.$array_missing_middle_time[$index]);
        // -------------  debug ---------------------- end --------------
        $w_time = 0;
        $calc_time = 0;
        // $array_calc_time[$index]=0はまだ退勤していないということ
        if ($array_calc_time[$index] > 0) {
            $w_time = $array_calc_time[$index] - $array_missing_middle_time[$index];
            // $calc_time = round($apicommon->roundTime($w_time, $target_result->time_unit, $target_result->time_rounding) / 60,2);
            $calc_time = round($w_time / 60 / 60,2);
        }
        // -------------  debug ---------------------- start --------------
        Log::DEBUG('        時間外労働時間- 未就労時間　10進数　$w_time　　　　　　　　= '.$w_time);
        Log::DEBUG('        時間外労働時間- 未就労時間　60進数　$calc_time            = '.$calc_time);
        // -------------  debug ---------------------- end --------------
        // 平日は時間外労働時間＝残業時間
        // ---- 取り消し--休日は所定労働時間+時間外労働時間>8の場合、所定労働時間+時間外労働時間-8=残業時間
        // 休日は残業時間は単価は1.25で休日の労働時間同じなので休日の労働時間に加算
        $out_of_legal_working_holiday_hours = 0;        // 法定外休日労働時間
        $legal_working_holiday_hours = 0;               // 法定休日労働時間
        if ($target_result->business_kubun == Config::get('const.C007.basic')) {
            $temp_working_model->setOffhoursworkinghoursAttribute($calc_time);
            // -------------  debug ---------------------- start --------------
            Log::DEBUG('        出勤日 時間外労働時間　$calc_time       = '.$calc_time);
            // -------------  debug ---------------------- end --------------
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
            // -------------  debug ---------------------- start --------------
            Log::DEBUG('        休日 時間外労働時間　$calc_time       = '.$temp_calc);
            // -------------  debug ---------------------- end --------------
            if ($target_result->business_kubun == Config::get('const.C007.legal_holoday')) {
                $legal_working_holiday_hours = $temp_calc;
                $temp_working_model->setLegalworkingholidayhoursAttribute($legal_working_holiday_hours);
                // -------------  debug ---------------------- start --------------
                Log::DEBUG('        法定休日労働時間 時間外労働時間　$legal_working_holiday_hours       = '.$legal_working_holiday_hours);
                // -------------  debug ---------------------- end --------------
            } elseif($target_result->business_kubun == Config::get('const.C007.legal_out_holoday')) {
                $out_of_legal_working_holiday_hours = $temp_calc;
                $temp_working_model->setOutoflegalworkingholidayhoursAttribute($out_of_legal_working_holiday_hours);
                // -------------  debug ---------------------- start --------------
                Log::DEBUG('        法定外休日労働時間 時間外労働時間　$out_of_legal_working_holiday_hours       = '.$out_of_legal_working_holiday_hours);
                // -------------  debug ---------------------- end --------------
            }
        }
        $temp_working_model->setRegularworkingtimesAttribute($regular_calc_time);   // 所定労働時間
        // -------------  debug ---------------------- start --------------
        Log::DEBUG('        所定労働時間 $regular_calc_time       = '.$regular_calc_time);
        // -------------  debug ---------------------- end --------------
        $total_time = $total_time + $regular_calc_time;
        $total_time = $total_time + $calc_time;
        $overtime_hours = $overtime_hours + $calc_time;
        // -------------  debug ---------------------- start --------------
        Log::DEBUG('        時間外労働時間 = $overtime_hours + $calc_time '.$overtime_hours.' '.$calc_time);
        Log::DEBUG('        法定外休日労働時間 $out_of_legal_working_holiday_hours       = '.$out_of_legal_working_holiday_hours);
        Log::DEBUG('        法定休日労働時間   $legal_working_holiday_hours              = '.$legal_working_holiday_hours);
        Log::DEBUG('        トータル労働時間   $total_time              = '.$total_time);
        // -------------  debug ---------------------- end --------------
        // 深夜労働残業時間
        $index = (int)(Config::get('const.C004.out_of_regular_night_working_time'))-1;
        // -------------  debug ---------------------- start --------------
        Log::DEBUG('        深夜労働残業時間の計算 ');
        Log::DEBUG('        深夜労働残業時間 $array_calc_time[$index]           = '.$array_calc_time[$index]);
        Log::DEBUG('        未就労時間       $array_missing_middle_time[$index]  = '.$array_missing_middle_time[$index]);
        // -------------  debug ---------------------- end --------------
        $w_time = 0;
        $calc_time = 0;
        // $array_calc_time[$index]=0はまだ退勤していないということ
        if ($array_calc_time[$index] > 0) {
            $w_time = $array_calc_time[$index] - $array_missing_middle_time[$index];
            // $calc_time = round($apicommon->roundTime($w_time, $target_result->time_unit, $target_result->time_rounding) / 60,2);
            $calc_time = round($w_time / 60 / 60,2);
        }
        // -------------  debug ---------------------- start --------------
        Log::DEBUG('        深夜労働残業時間 10進数 $w_time           = '.$w_time);
        Log::DEBUG('        深夜労働残業時間 60進数 $calc_time        = '.$calc_time);
        // -------------  debug ---------------------- end --------------
        $temp_working_model->setLatenightovertimehoursAttribute($calc_time);
        $total_time = $total_time + $calc_time;
        // 深夜労働時間
        $w_time = round($this->calc_late_night_working_hours / 60 / 60,2);
        $temp_working_model->setLatenightworkinghoursAttribute($w_time);
        Log::DEBUG('        深夜労働時間   $this->calc_late_night_working_hours              = '.$this->calc_late_night_working_hours);
        Log::DEBUG('        深夜労働時間   $w_time              = '.$w_time);
        $total_time = $total_time + $w_time;
        // -------------  debug ---------------------- start --------------
        Log::DEBUG('        トータル労働時間   $total_time              = '.$total_time);
        // -------------  debug ---------------------- end --------------
        // 残業時間
        $temp_working_model->setOvertimehoursAttribute($overtime_hours);
        // 所定外労働時間
        // -------------  debug ---------------------- start --------------
        Log::DEBUG('        所定外労働時間計算の計算 ');
        // -------------  debug ---------------------- end --------------
        $outside_calc_time = 0;
        $default_time = (int)(Config::get('const.C002.legal_working_hours_day'));
        if ($regular_calc_time < $default_time && $total_time > $default_time) {    // 所定労働時間 < 8 and 合計勤務時間 > 8 の場合
            $outside_calc_time = $default_time - $regular_calc_time;
            // -------------  debug ---------------------- start --------------
            Log::DEBUG('        所定労働時間 < 8 and 合計勤務時間 > 8 の場合 ');
            // -------------  debug ---------------------- end --------------
        } elseif ($regular_calc_time < $total_time) { 
            // -------------  debug ---------------------- start --------------
            Log::DEBUG('        所定労働時間 < 合計勤務時間 の場合 ');
            // -------------  debug ---------------------- end --------------
            $outside_calc_time = $total_time- $regular_calc_time;
        } 
        // -------------  debug ---------------------- start --------------
        Log::DEBUG('        所定外労働時間計算の計算 ');
        Log::DEBUG('        所定外労働時間計算 $default_time = '.$default_time);
        Log::DEBUG('        所定外労働時間計算 $outside_calc_time        = '.$outside_calc_time);
        // -------------  debug ---------------------- end --------------

        $temp_working_model->setOutofregularworkingtimesAttribute($outside_calc_time);
        // 法定労働時間 法定外労働時間
        if ($total_time > $default_time) {      // 合計勤務時間 > 8 の場合
            // 法定労働時間
            $temp_working_model->setLegalworkingtimesAttribute($default_time);
            // 法定外労働時間
            $temp_working_model->setOutoflegalworkingtimesAttribute($total_time - $default_time);
            Log::DEBUG('        $target_user_code = '.$target_user_code.' 法定労働時間 = $default_time '.$default_time);
            Log::DEBUG('        $target_user_code = '.$target_user_code.' 法定外労働時間 = $total_time - $default_time '.$total_time.' '.$default_time);
        } else {
            // 法定労働時間
            $temp_working_model->setLegalworkingtimesAttribute($total_time);
            // 法定外労働時間
            $temp_working_model->setOutoflegalworkingtimesAttribute(0);
            Log::DEBUG('        $target_user_code = '.$target_user_code.' 法定労働時間 = $total_time '.$total_time);
            Log::DEBUG('        $target_user_code = '.$target_user_code.' 法定外労働時間 =0 ');
        }
        // 不就労時間（規則所定労働時間-実所定労働時間）
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
            Log::DEBUG('        $getWorkingTimeTableJoin  count = '.count($timetables));
            $calc_time = 0;
            if (count($timetables) > 0) {
                $w_time = 0;
                $w_break_time = 0;
                $w_from_time1 = "";
                $w_from_time2 = "";
                $w_to_time1 = "";
                $w_to_time2 = "";
                $target_dt = new Carbon($target_date);
                Log::DEBUG('        $target_dt = '.$target_dt);
                foreach($timetables as $item) {
                    if ($item->working_time_kubun == Config::get('const.C004.regular_working_time')) {
                        Log::DEBUG('        $working_time_kubun = '.$item->working_time_kubun);
                        if (isset($item->from_time) && isset($item->to_time)) {
                            if ($item->from_time < $item->to_time) {
                                $from_time = date_format($target_dt, 'Y-m-d').' '.$item->from_time;
                                $to_time = date_format($target_dt, 'Y-m-d').' '.$item->to_time;
                                Log::DEBUG('        規則所定労働時間を求める 1 from_time = '.$from_time);
                                Log::DEBUG('        規則所定労働時間を求める 2 to_time = '.$to_time);
                                $w_time += $apicommon->diffTimeSerial($from_time, $to_time);
                                $w_from_time1 = $item->from_time;
                                $w_to_time1 = $item->to_time;
                                $w_from_time2 = $item->from_time;
                                $w_to_time2 = $item->to_time;
                            } else {
                                $from_time = date_format($target_dt, 'Y-m-d').' '.$item->from_time;
                                $to_time = date_format($target_dt, 'Y-m-d').' '.$item->to_time;
                                $nextdt =$apicommon->getNextDay(new Carbon($to_time), 'Y/m/d');
                                $to_time = date_format(new Carbon($nextdt), 'Y-m-d').' 00:00:00';
                                Log::DEBUG('        規則所定労働時間を求める 3 from_time = '.$from_time);
                                Log::DEBUG('        規則所定労働時間を求める 4 to_time = '.$to_time);
                                $w_time += $apicommon->diffTimeSerial($from_time, $to_time);
                                $w_from_time1 = $item->from_time;
                                $w_to_time1 = '00:00:00';
                                $from_time = $to_time;
                                $to_time = date_format(new Carbon($nextdt), 'Y-m-d').' '.$item->to_time;
                                Log::DEBUG('        規則所定労働時間を求める 5 from_time = '.$from_time);
                                Log::DEBUG('        規則所定労働時間を求める 6 to_time = '.$to_time);
                                $w_time += $apicommon->diffTimeSerial($from_time, $to_time);
                                $w_from_time2 = '00:00:00';
                                $w_to_time2 = $item->to_time;
                            }
                        }
                        Log::DEBUG('        規則所定労働時間 w_time = '.$w_time);
                        Log::DEBUG('        規則所定労働時間 w_time(H) = '.($w_time / 60 / 60));
                        Log::DEBUG('        規則所定労働時間 w_from_time1 = '.$w_from_time1);
                        Log::DEBUG('        規則所定労働時間 w_to_time1 = '.$w_to_time1);
                        Log::DEBUG('        規則所定労働時間 w_from_time2 = '.$w_from_time2);
                        Log::DEBUG('        規則所定労働時間 w_to_time2 = '.$w_to_time2);
                    }
                    // 所定労働時間内の休憩の場合はその分を減算する
                    if ($item->working_time_kubun == Config::get('const.C004.regular_working_breaks_time')) {
                        if (isset($item->from_time) && isset($item->to_time)) {
                            Log::DEBUG('        規則休憩時間 $item->from_time = '.$item->from_time);
                            Log::DEBUG('        規則休憩時間 $item->to_time = '.$item->to_time);
                            Log::DEBUG('        規則休憩時間 w_from_time1 = '.$w_from_time1);
                            Log::DEBUG('        規則休憩時間 w_to_time1 = '.$w_to_time1);
                            Log::DEBUG('        規則休憩時間 w_from_time2 = '.$w_from_time2);
                            Log::DEBUG('        規則休憩時間 w_to_time2 = '.$w_to_time2);
                            if (($item->from_time > $w_from_time1 && $item->from_time < $w_to_time1) ||
                                ($item->from_time > $w_from_time2 && $item->from_time < $w_to_time2)) {
                                if ($item->from_time < $item->to_time) {
                                    $from_time = date_format($target_dt, 'Y-m-d').' '.$item->from_time;
                                    $to_time = date_format($target_dt, 'Y-m-d').' '.$item->to_time;
                                    Log::DEBUG('        規則休憩時間を求める 1 from_time = '.$from_time);
                                    Log::DEBUG('        規則休憩時間を求める 2 to_time = '.$to_time);
                                    $w_break_time += $apicommon->diffTimeSerial($from_time, $to_time);
                                } else {
                                    $from_time = date_format($target_dt, 'Y-m-d').' '.$item->from_time;
                                    $to_time = date_format($target_dt, 'Y-m-d').' '.$item->to_time;
                                    $nextdt =$apicommon->getNextDay(new Carbon($to_time), 'Y/m/d');
                                    $to_time = date_format($nextdt, 'Y-m-d').' 00:00:00';
                                    Log::DEBUG('        規則休憩時間を求める 3 from_time = '.$from_time);
                                    Log::DEBUG('        規則休憩時間を求める 4 to_time = '.$to_time);
                                    $w_break_time += $apicommon->diffTimeSerial($from_time, $to_time);
                                    $from_time = $to_time;
                                    $to_time = date_format(new Carbon($nextdt), 'Y-m-d').' '.$item->to_time;
                                    Log::DEBUG('        規則所定労働時間を求める 5 from_time = '.$from_time);
                                    Log::DEBUG('        規則所定労働時間を求める 6 to_time = '.$to_time);
                                    $w_break_time += $apicommon->diffTimeSerial($from_time, $to_time);
                                }
                            }
                        }
                        Log::DEBUG('        規則所定労働時間 w_break_time = '.$w_break_time);
                    }
                }
            }

            if ($regular_calc_time > 0) {
                $w_calc_time = ($w_time / 60 / 60) - ($w_break_time / 60 / 60) - $regular_calc_time;
                $temp_working_model->setNotemploymentworkinghoursAttribute($w_calc_time);
                Log::DEBUG('        不就労時間  w_time = '.$w_time.' '.($w_time / 60 / 60));
                Log::DEBUG('        不就労時間  w_break_time = '.$w_break_time.' '.($w_break_time / 60 / 60));
                Log::DEBUG('        不就労時間  regular_calc_time = '.$regular_calc_time);
            } else {
                // 欠勤の場合は規則所定労働時間を不就労に設定
                if ($target_result->holiday_kubun == Config::get('const.C013.absence_work')) {
                    $w_calc_time = ($w_time / 60 / 60) - ($w_break_time / 60 / 60);
                    $temp_working_model->setNotemploymentworkinghoursAttribute($w_calc_time);
                } else {
                    Log::DEBUG('        不就労時間  = 0');
                    $temp_working_model->setNotemploymentworkinghoursAttribute(0);
                }
            }
        } else {
            Log::DEBUG('        不就労時間  = 0');
            $temp_working_model->setNotemploymentworkinghoursAttribute(0);
        }

        // 休憩時間
        $calc_time = 0;
        if ($regular_calc_time > 0) {
            $calc_time +=  $this->not_employment_working;
            Log::DEBUG('        不就労時間 休憩時間 = '.$this->not_employment_working);
        }
        // 私用外出時間
        $calc_missing_time = 0;
        for ($i=0;$i<count($array_missing_middle_time);$i++) {
            $calc_missing_time += $array_missing_middle_time[$i];
            Log::DEBUG('        不就労時間 私用外出時間 = '.$array_missing_middle_time[$i]);
        }
        // $calc_time = round($apicommon->roundTime($calc_time + $calc_missing_time, $target_result->time_unit, $target_result->time_rounding) / 60,2);
        $calc_time = round(($calc_time + $calc_missing_time) / 60 / 60,2);
        $calc_missing_time = round($calc_missing_time / 60 / 60,2);
        // $calc_missing_time = round($apicommon->roundTime($calc_missing_time, $target_result->time_unit, $target_result->time_rounding) / 60,2);
        Log::DEBUG('$target_user_code = '.$target_user_code.'        不就労時間（休憩時間＋私用外出時間） =  '. $calc_time.' + '.$calc_missing_time);
        $temp_working_model->setMissingmiddlehoursAttribute($calc_missing_time);
        // 公用外出時間
        $calc_time = 0;
        for ($i=0;$i<count($array_public_going_out_time);$i++) {
            $calc_time += $array_public_going_out_time[$i];
            Log::DEBUG('        公用外出時間 = '.$array_public_going_out_time[$i]);
        }
        // 合計勤務時間
        $temp_working_model->setTotalworkingtimesAttribute($total_time);

        // $calc_time = round($apicommon->roundTime($calc_time, $target_result->time_unit, $target_result->time_rounding) / 60,2);
        $calc_time = round($calc_time / 60 / 60,2);
        $temp_working_model->setPublicgoingouthoursAttribute($calc_time);
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
            Log::DEBUG('  $target_array_time =  '.$target_array_time[$i]);
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
        for ($i=0;$i<$index;$i++) {
            Log::DEBUG('  $target_array_time =  '.$target_array_time[$i]);
            if (count($target_array_time) > 0 && $i < count($target_array_time)){
                $arrray_decide_times[$i] = $target_array_time[$i];
            } else {
                $arrray_decide_times[$i] = null;
            }
        }
        Log::DEBUG('---------------------- decideWorkingTimeTo end ------------------------ ');

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
        Log::DEBUG('---------------------- decideWorkingTimeTo in ------------------------ ');
        $arrray_decide_times = array();
        // 開始時刻より過去は設定しないため設定するindexも調整するためindexを別途準備
        $set_index = 0;
        $set_j = 0;
        $set_strat_j = 0;
        $set_flg = false;
        Log::DEBUG('                 from_array_cnt = '.$from_array_cnt);
        Log::DEBUG('                 count($target_array_time) = '.count($target_array_time));
        for ($i=0;$i<$from_array_cnt;$i++) {
            $set_flg = false;
            for ($j=$set_strat_j;$j<$index;$j++) {
                if (count($target_array_time) > 0 && $i < count($target_array_time)){
                    // 開始時刻<target_array_timeを設定
                    Log::DEBUG('                 $target_array_time[$j] = '.$target_array_time[$j]);
                    Log::DEBUG('                 $from_array_time[$i] = '.$from_array_time[$i]);
                    if ($target_array_time[$j] > $from_array_time[$i]) {
                        $set_index++;
                        Log::DEBUG('                 $set_index = '.$set_index);
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
                Log::DEBUG('                 $set_index = '.$set_index);
                $arrray_decide_times[$set_index-1] = $target_array_time[$set_j];
                Log::DEBUG('                 $arrray_decide_times[$set_index-1] = '.$arrray_decide_times[$set_index-1]);
                $set_strat_j = $set_j+1;
            }
        }

        if ($from_array_cnt == 0) {
            $arrray_decide_times = $target_array_time;
        }
        Log::DEBUG('                 count($arrray_decide_times) = '.count($arrray_decide_times));
        Log::DEBUG('---------------------- decideWorkingTimeTo end ------------------------ ');

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
        $array_calc_time = $array_result_set0['array_calc_time'];
        $array_missing_middle_time = $array_result_set0['array_missing_middle_time'];
        $array_public_going_out_time = $array_result_set0['array_public_going_out_time'];
        $array_add_attendance_time = array();
        $array_add_leaving_time = array();
        $array_add_missing_middle_time = array();
        $array_add_missing_middle_return_time = array();
        $array_add_public_going_out_time = array();
        $array_add_public_going_out_return_time = array();
        $array_add_attendance_time_positions = array();
        $array_add_leaving_time_positions = array();
        $array_add_missing_middle_time_positions = array();
        $array_add_missing_middle_return_time_positions = array();
        $array_add_public_going_out_time_positions = array();
        $array_add_public_going_out_return_time_positions = array();
        $attendance_time = "";
        $leaving_time = "";
        $missing_middle_time = "";
        $missing_middle_return_time = "";
        $public_going_out_time = "";
        $public_going_out_return_time = "";
        $attendance_time_positions = "";
        $leaving_time_positions = "";
        $missing_middle_time_positions = "";
        $missing_middle_return_time_positions = "";
        $public_going_out_time_positions = "";
        $public_going_out_return_time_positions = "";

        $array_time_position = array();
        if ($result->mode == Config::get('const.C005.attendance_time')) {
            $array_time_position = $this->setTimePosition($result);
            $attendance_time = $array_time_position['result_time'];
            $attendance_time_positions =  $array_time_position['result_time_positions'];
            if ($attendance_time <> ''){
                $array_add_attendance_time[] = $attendance_time;
                $array_add_attendance_time_positions[] = $attendance_time_positions;
            }
            Log::DEBUG('                 attendance_time = '.$attendance_time);
            Log::DEBUG('                 count array_add_attendance_time = '.count($array_add_attendance_time));
        }
        if ($result->mode == Config::get('const.C005.leaving_time')) {
            $array_time_position = $this->setTimePosition($result);
            $leaving_time = $array_time_position['result_time'];
            $leaving_time_positions =  $array_time_position['result_time_positions'];
            if ($leaving_time <> ''){
                $array_add_leaving_time[] = $leaving_time;
                $array_add_leaving_time_positions[] = $leaving_time_positions;
            }
            Log::DEBUG('                 leaving_time = '.$leaving_time);
        }
        if ($result->mode == Config::get('const.C005.missing_middle_time')) {
            $array_time_position = $this->setTimePosition($result);
            $missing_middle_time = $array_time_position['result_time'];
            $missing_middle_time_positions =  $array_time_position['result_time_positions'];
            if ($missing_middle_time <> ''){
                $array_add_missing_middle_time[] = $missing_middle_time;
                $array_add_missing_middle_time_positions[] = $missing_middle_time_positions;
            }
            Log::DEBUG('                 missing_middle_time = '.$missing_middle_time);
        }
        if ($result->mode == Config::get('const.C005.missing_middle_return_time')) {
            $array_time_position = $this->setTimePosition($result);
            $missing_middle_return_time = $array_time_position['result_time'];
            $missing_middle_return_time_positions =  $array_time_position['result_time_positions'];
            if ($missing_middle_return_time <> ''){
                $array_add_missing_middle_return_time[] = $missing_middle_return_time;
                $array_add_missing_middle_return_time_positions[] = $missing_middle_return_time_positions;
            }
            Log::DEBUG('                 missing_middle_return_time = '.$missing_middle_return_time);
        }
        if ($result->mode == Config::get('const.C005.public_going_out_time')) {
            $array_time_position = $this->setTimePosition($result);
            $public_going_out_time = $array_time_position['result_time'];
            $public_going_out_time_positions =  $array_time_position['result_time_positions'];
            if ($public_going_out_time <> ''){
                $array_add_public_going_out_time[] = $public_going_out_time;
                $array_add_public_going_out_time_positions[] = $public_going_out_time_positions;
            }
            Log::DEBUG('                 public_going_out_time = '.$public_going_out_time);
        }
        if ($result->mode == Config::get('const.C005.public_going_out_return_time')) {
            $array_time_position = $this->setTimePosition($result);
            $public_going_out_return_time = $array_time_position['result_time'];
            $public_going_out_return_time_positions =  $array_time_position['result_time_positions'];
            if ($public_going_out_return_time <> ''){
                $array_add_public_going_out_return_time[] = $public_going_out_return_time;
                $array_add_public_going_out_return_time_positions[] = $public_going_out_return_time_positions;
            }
            Log::DEBUG('                 public_going_out_return_time = '.$public_going_out_return_time);
        }

        $this->calc_late_night_working_hours = 0;
        return array(
            'array_calc_time' => $array_calc_time,
            'array_missing_middle_time' => $array_missing_middle_time,
            'array_public_going_out_time' => $array_public_going_out_time,
            'array_add_attendance_time' => $array_add_attendance_time,
            'array_add_leaving_time' => $array_add_leaving_time,
            'array_add_missing_middle_time' => $array_add_missing_middle_time,
            'array_add_missing_middle_return_time' => $array_add_missing_middle_return_time,
            'array_add_public_going_out_time' => $array_add_public_going_out_time,
            'array_add_public_going_out_return_time' => $array_add_public_going_out_return_time,
            'array_add_attendance_time_positions' => $array_add_attendance_time_positions,
            'array_add_leaving_time_positions' => $array_add_leaving_time_positions,
            'array_add_missing_middle_time_positions' => $array_add_missing_middle_time_positions,
            'array_add_missing_middle_return_time_positions' => $array_add_missing_middle_return_time_positions,
            'array_add_public_going_out_time_positions' => $array_add_public_going_out_time_positions,
            'array_add_public_going_out_return_time_positions' => $array_add_public_going_out_return_time_positions,
            'attendance_time' => $attendance_time,
            'leaving_time' => $leaving_time,
            'missing_middle_time' => $missing_middle_time,
            'missing_middle_return_time' => $missing_middle_return_time,
            'public_going_out_time' => $public_going_out_time,
            'public_going_out_return_time' => $public_going_out_return_time,
            'attendance_time_positions' => $attendance_time_positions,
            'leaving_time_positions' => $leaving_time_positions,
            'missing_middle_time_positions' => $missing_middle_time_positions,
            'missing_middle_return_time_positions' => $missing_middle_return_time_positions,
            'public_going_out_time_positions' => $public_going_out_time_positions,
            'public_going_out_return_time_positions' => $public_going_out_return_time_positions
        );
    }

    /**
     * 労働時間数配列初期化
     * 
     */
    private function setArrayTimeSet0(
        $array_working_time_kubun
        )
    {
        $array_calc_time = array(); 
        $array_missing_middle_time = array(); 
        $array_public_going_out_time = array(); 
        for ($i=0;$i<count($array_working_time_kubun);$i++) {
            $array_calc_time[$i] = 0; 
            $array_missing_middle_time[$i] = 0; 
            $array_public_going_out_time[$i] = 0; 
        }

        return array(
            'array_calc_time' => $array_calc_time,
            'array_missing_middle_time' => $array_missing_middle_time,
            'array_public_going_out_time' => $array_public_going_out_time
        );
    }
 
    /**
     * 時刻と位置情報の設定
     * 
     */
    private function setTimePosition($result)
    {
        $result_time = $result->record_datetime;
        if (isset($result->x_positions) && isset($result->y_positions)) {
            $result_time_positions = $result->x_positions.' '.$result->y_positions;
        } else {
            $result_time_positions = null;
        }

        return array('result_time' => $result_time , 'result_time_positions' => $result_time_positions);
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
                'x_attendance_time_positions' => $array_setting_time['x_attendance_time_positions_'.$time_cnt],
                'y_attendance_time_positions' => $array_setting_time['y_attendance_time_positions_'.$time_cnt],
                'leaving_time' => $array_setting_time['leaving_time_'.$time_cnt],
                'x_leaving_time_positions' => $array_setting_time['x_leaving_time_positions_'.$time_cnt],
                'y_leaving_time_positions' => $array_setting_time['y_leaving_time_positions_'.$time_cnt],
                'missing_middle_time' => $array_setting_time['missing_middle_time_'.$time_cnt],
                'x_missing_middle_time_positions' => $array_setting_time['x_missing_middle_time_positions_'.$time_cnt],
                'y_missing_middle_time_positions' => $array_setting_time['y_missing_middle_time_positions_'.$time_cnt],
                'missing_middle_return_time' => $array_setting_time['missing_middle_return_time_'.$time_cnt],
                'x_missing_middle_return_time_positions' => $array_setting_time['x_missing_middle_return_time_positions_'.$time_cnt],
                'y_missing_middle_return_time_positions' => $array_setting_time['y_missing_middle_return_time_positions_'.$time_cnt],
                'public_going_out_time' => $array_setting_time['public_going_out_time_'.$time_cnt],
                'x_public_going_out_time_positions' => $array_setting_time['x_public_going_out_time_positions_'.$time_cnt],
                'y_public_going_out_time_positions' => $array_setting_time['y_public_going_out_time_positions_'.$time_cnt],
                'public_going_out_return_time' => $array_setting_time['public_going_out_return_time_'.$time_cnt],
                'x_public_going_out_return_time_positions' => $array_setting_time['x_public_going_out_return_time_positions_'.$time_cnt],
                'y_public_going_out_return_time_positions' => $array_setting_time['y_public_going_out_return_time_positions_'.$time_cnt]
            );
        } else {
            if ($issetspace) {
                $array_working_time_attendances[] = array(
                    'attendance_time' => '',
                    'x_attendance_time_positions' => '',
                    'y_attendance_time_positions' => '',
                    'leaving_time' => '',
                    'x_leaving_time_positions' => '',
                    'y_leaving_time_positions' => '',
                    'missing_middle_time' => '',
                    'x_missing_middle_time_positions' => '',
                    'y_missing_middle_time_positions' => '',
                    'missing_middle_return_time' => '',
                    'x_missing_middle_return_time_positions' => '',
                    'y_missing_middle_return_time_positions' => '',
                    'public_going_out_time' => '',
                    'x_public_going_out_time_positions' => '',
                    'y_public_going_out_time_positions' => '',
                    'public_going_out_return_time' => '',
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
                    'x_attendance_time_positions' => $array_attendance_time[$i]["x_attendance_time_positions"],
                    'y_attendance_time_positions' => $array_attendance_time[$i]["y_attendance_time_positions"],
                    'leaving_time' => $array_attendance_time[$i]["leaving_time"],
                    'x_leaving_time_positions' => $array_attendance_time[$i]["x_leaving_time_positions"],
                    'y_leaving_time_positions' => $array_attendance_time[$i]["y_leaving_time_positions"],
                    'missing_middle_time' => $array_attendance_time[$i]["missing_middle_time"],
                    'x_missing_middle_time_positions' => $array_attendance_time[$i]["x_missing_middle_time_positions"],
                    'y_missing_middle_time_positions' => $array_attendance_time[$i]["y_missing_middle_time_positions"],
                    'missing_middle_return_time' => $array_attendance_time[$i]["missing_middle_return_time"],
                    'x_missing_middle_return_time_positions' => $array_attendance_time[$i]["x_missing_middle_return_time_positions"],
                    'y_missing_middle_return_time_positions' => $array_attendance_time[$i]["y_missing_middle_return_time_positions"],
                    'public_going_out_time' => $array_attendance_time[$i]["public_going_out_time"],
                    'x_public_going_out_time_positions' => $array_attendance_time[$i]["x_public_going_out_time_positions"],
                    'y_public_going_out_time_positions' => $array_attendance_time[$i]["y_public_going_out_time_positions"],
                    'public_going_out_return_time' => $array_attendance_time[$i]["public_going_out_return_time"],
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
                    'legal_working_holiday_hours' => $working_time["legal_working_holiday_hours"],
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
                    'unused_holiday_kubun' => $working_time["unused_holiday_kubun"],
                    'unused_holiday_name' => $working_time["unused_holiday_name"],
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
                    'calendars_business_kubun' => $working_time["calendars_business_kubun"],
                    'working_time_name' => $working_time["working_time_name"],
                    'predeter_time_name' => $working_time["predeter_time_name"],
                    'predeter_night_time_name' => $working_time["predeter_night_time_name"]
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
                    'x_attendance_time_positions' => $array_attendance_time[$i]["x_attendance_time_positions"],
                    'y_attendance_time_positions' => $array_attendance_time[$i]["y_attendance_time_positions"],
                    'leaving_time' => $array_attendance_time[$i]["leaving_time"],
                    'x_leaving_time_positions' => $array_attendance_time[$i]["x_leaving_time_positions"],
                    'y_leaving_time_positions' => $array_attendance_time[$i]["y_leaving_time_positions"],
                    'missing_middle_time' => $array_attendance_time[$i]["missing_middle_time"],
                    'x_missing_middle_time_positions' => $array_attendance_time[$i]["x_missing_middle_time_positions"],
                    'y_missing_middle_time_positions' => $array_attendance_time[$i]["y_missing_middle_time_positions"],
                    'missing_middle_return_time' => $array_attendance_time[$i]["missing_middle_return_time"],
                    'x_missing_middle_return_time_positions' => $array_attendance_time[$i]["x_missing_middle_return_time_positions"],
                    'y_missing_middle_return_time_positions' => $array_attendance_time[$i]["y_missing_middle_return_time_positions"],
                    'public_going_out_time' => $array_attendance_time[$i]["public_going_out_time"],
                    'x_public_going_out_time_positions' => $array_attendance_time[$i]["x_public_going_out_time_positions"],
                    'y_public_going_out_time_positions' => $array_attendance_time[$i]["y_public_going_out_time_positions"],
                    'public_going_out_return_time' => $array_attendance_time[$i]["public_going_out_return_time"],
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
                    'legal_working_holiday_hours' => '',
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
                    'unused_holiday_kubun' => '',
                    'unused_holiday_name' => '',
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
                    'calendars_business_kubun' => '',
                    'working_time_name' => '',
                    'predeter_time_name' => '',
                    'predeter_night_time_name' => ''
                );
            }
        }

        return $array_working_time_dates;
    }

}