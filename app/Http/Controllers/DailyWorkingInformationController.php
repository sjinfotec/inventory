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

    // 打刻データ配列
    private $array_working_mode = null;
    private $array_working_datetime = null;
    private $array_timetable_from_time = null;
    private $array_timetable_to_time = null;
    private $array_interval = null;
    // 計算用配列
    private $array_calc_mode = null;
    private $array_calc_time = null;
    private $array_calc_status = null;
    private $array_calc_note = null;
    private $array_calc_late = null;
    private $array_calc_leave_early = null;
    private $array_calc_interval = null;
    private $array_calc_calc = null;
    private $array_calc_to_be_confirmed = null;
    private $array_calc_pattern = null;


    private $not_employment_working = 0;

    private $array_massegedata = array();
    private $collect_massegedata = null;

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
        $employment_status = null;
        if(isset($request->employmentstatus)){
            $employment_status = $request->employmentstatus;
        }
        $Departmentcode = null;
        if(isset($request->departmentcode)){
            $Departmentcode =$request->departmentcode;
        }
        $usercode = null;
        if(isset($request->usercode)){
            $usercode = $request->usercode;
        }
        $this->collect_massegedata = collect();

        // 打刻時刻を取得
        $work_time = new WorkTime();
        $work_time->setParamDatefromAttribute($datefrom);
        $work_time->setParamDatetoAttribute($dateto);
        $work_time->setParamemploymentstatusAttribute($employment_status);
        $work_time->setParamDepartmentcodeAttribute($Departmentcode);
        $work_time->setParamUsercodeAttribute($usercode);
        // パラメータのチェック
        $chk_result = $work_time->chkWorkingTimeData();
        $temp_working_model = new TempWorkingTimeDate();
        $apicommon = new ApiCommonController();
        if ($chk_result) {
            // シフト打刻を取得するために$datetoの翌日をParamDatetoを再設定
            $nextdt =$apicommon->getNextDay($dateto, 'Y/m/d');
            $work_time->setParamDatetoAttribute($nextdt);
            $work_time_result = $work_time->getWorkTimes();
            if(isset($work_time_result)){
                $temp_calc_model = new TempCalcWorkingTime();
                try{
                    // temporary削除処理
                    DB::beginTransaction();
                    Log::debug('temporary beginTransaction');
                    $temp_calc_model->delTempCalcWorkingtime();
                    $temp_working_model->delTempWorkingTimeDate();
                    try{
                        // 日次集計計算登録
                        $calc_result = $this->calcWorkingTimeDate($work_time_result, $work_time->getParamDatefromAttribute());
                        if ($calc_result) {
                            // タイムテーブルを取得
                            $timetable_model = new WorkingTimeTable();
                            $timetable_model->setParamdatefromAttribute($datefrom);
                            $timetable_model->setParamdatetoAttribute($dateto);
                            $timetable_model->setParamemploymentstatusAttribute($employment_status);
                            $timetable_model->setParamDepartmentcodeAttribute($Departmentcode);
                            $timetables = $timetable_model->getWorkingTimeTableJoin();
                            if (isset($timetables)) {
                                // 日次集計
                                $add_result = $this->calcTempWorkingTimeDate($timetables);
                            } else {
                                array_push($this->array_massegedata, Config::get('const.MSG_ERROR.not_setting_timetable'));
                                $this->collect_massegedata->put(Config::get('const.RESPONCE_ITEM.messagedata'),Config::get('const.MSG_ERROR.not_setting_timetable'));
                                Log::error(Config::get('const.LOG_MSG.not_setting_timetable'));
                                $add_result = false;
                            }
                        }
                        DB::commit();
                        Log::debug('temporary commit');
                    }catch(\PDOException $pe){
                        DB::rollBack();
                        Log::debug('temporary rollBack');
                        array_push($this->array_massegedata, Config::get('const.MSG_ERROR.data_accesee_eror_dailycalc'));
                        Log::error(Config::get('const.MSG_ERROR.data_accesee_eror_dailycalc'));
                        Log::error($pe->getMessage());
                        $add_result = false;
                    }catch(\Exception $e){
                        DB::rollBack();
                        Log::debug('temporary rollBack');
                        array_push($this->array_massegedata, Config::get('const.MSG_ERROR.data_error_dailycalc'));
                        Log::error(Config::get('const.LOG_MSG.data_error_dailycalc'));
                        Log::error($e->getMessage());
                        $add_result = false;
                    }
                }catch(\PDOException $pe){
                    DB::rollBack();
                    Log::debug('temporary rollBack');
                    array_push($this->array_massegedata, Config::get('const.MSG_ERROR.data_accesee_eror_dailycalc'));
                    Log::error($pe->getMessage());
                    $add_result = false;
                }
            } else {
                $add_result = false;
                array_push($this->array_massegedata, Config::get('const.MSG_ERROR.not_workintime'));
            }
        } else {
            $add_result = false;
            array_push($this->array_massegedata, $work_time->getMassegedataAttribute());
        }

        // 出勤・退勤データtempから登録
        $working_time_dates = null;
        $working_time_sum = null;
        if ($add_result) {
            $temp_working_model->setParamdatefromAttribute(date_format(new Carbon($datefrom), 'Ymd'));
            $temp_working_model->setParamdatetoAttribute(date_format(new Carbon($dateto), 'Ymd'));
            $temp_working_model->setParamEmploymentStatusAttribute($employment_status);
            $temp_working_model->setParamDepartmentcodeAttribute($Departmentcode);
            $temp_working_model->setParamUsercodeAttribute($usercode);
            try{
                Log::debug('getTempWorkingTimeDateUserJoin ');
                $temp_working_time_dates = $temp_working_model->getTempWorkingTimeDateUserJoin();
                if (isset($temp_working_time_dates)) {
                    Log::debug('isset $temp_working_time_dates true ');
                    $working_model = new WorkingTimedate();
                    $working_model->setParamdatefromAttribute(date_format(new Carbon($datefrom), 'Ymd'));
                    $working_model->setParamdatetoAttribute(date_format(new Carbon($dateto), 'Ymd'));
                    $working_model->setParamEmploymentStatusAttribute($employment_status);
                    $working_model->setParamDepartmentcodeAttribute($Departmentcode);
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
                        $working_time_dates = $working_model->getWorkingTimeDateTimeFormat();
                        $working_time_sum = $working_model->getWorkingTimeDateTimeSum();
                        DB::commit();
                        Log::debug(' calc commit ');
                    }catch(\PDOException $pe){
                        DB::rollBack();
                        Log::debug(' calc rollBack ');
                        array_push($this->array_massegedata, Config::get('const.MSG_ERROR.data_error_dailycalc'));
                    }catch(\Exception $e){
                        DB::rollBack();
                        Log::debug(' calc rollBack ');
                        array_push($this->array_massegedata, Config::get('const.MSG_ERROR.data_accesee_eror_dailycalc'));
                    }
                }
            }catch(\PDOException $pe){
                array_push($this->array_massegedata, Config::get('const.MSG_ERROR.data_error_dailycalc'));
                Log::error(Config::get('const.LOG_MSG.data_error_dailycalc'));
                Log::error($e->getMessage());
            }catch(\Exception $e){
                array_push($this->array_massegedata, Config::get('const.MSG_ERROR.data_accesee_eror_dailycalc'));
            }
        }

        //$this->collect_massegedata->put(Config::get('const.RESPONCE_ITEM.messagedata'),Config::get('const.MSG_ERROR.not_setting_timetable'));
        return response()->json(['calcresults' => $working_time_dates, 'sumresults' => $working_time_sum, 'massegedata' => $this->array_massegedata]);
        //return response()->json(['calcresults' => $working_time_dates, 'sumresults' => $working_time_sum, 'massegedata' => $this->collect_massegedata]);
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
    private function calcWorkingTimeDate($worktimes, $target_date){

        Log::DEBUG('---------------------- calcWorkingTimeDate in ------------------------ ');
        $current_date = null;
        $current_department_code = null;
        $current_user_code = null;
        $current_result = null;
        $before_date = null;
        $before_user_code = null;
        $before_department_code = null;
        $before_result = null;
        $target_date_ymd = date_format(new Carbon($target_date), 'Ymd');
        // 打刻データ配列の初期化
        $this->iniArrayWorkingTime();
        $add_results = true;
        // ユーザー単位処理
        foreach ($worktimes as $result) {
            // 打刻データありの場合
            if ($result->record_datetime != null) {
                // 設定値確認
                $chk_result = $this->chkSettingData($result);
                // 設定が正常である場合
                if ($chk_result == 0)  {
                    // 現在の情報保存
                    Log::DEBUG('$result->record_date = '.$result->record_date);
                    Log::DEBUG('$target_date = '.$target_date_ymd);
                    // 翌日の出勤データ以降はいらない
                    if ($result->record_date <= $target_date_ymd) {
                        $target_flg = true;
                    } elseif ($result->mode == Config::get('const.C012.attendance')) {
                        $target_flg = false;
                    }
                    if ($target_flg == true) {
                        // 翌日退勤した場合を考慮し、同日処理を行うようにするため、$current_dateは$target_date_ymdとする
                        $current_date = $target_date_ymd;
                        $current_department_code = $result->department_code;
                        $current_user_code = $result->user_code;
                        $current_result = $result;
                        if ($before_date == null) {$before_date = $current_date;}
                        if ($before_department_code == null) {$before_department_code = $current_department_code;}
                        if ($before_user_code == null) {$before_user_code = $current_user_code;}
                        if ($before_result == null) {$before_result = $result;}
                        // 同じキーの場合
                        if ($current_date == $before_date &&
                            $current_department_code == $before_department_code &&
                            $current_user_code == $before_user_code) {
                            // 打刻データ配列の設定
                            $this->pushArrayWorkingTime($result);
                        } elseif ($current_department_code == $before_department_code &&
                                    $current_user_code == $before_user_code)    {
                            // 日付が変わった場合
                            Log::DEBUG('date break ');
                            try{
                                // ユーザー労働時間登録(１個前のユーザーを登録する)
                                $add_results = $this->addWorkingTime(
                                    $before_date,
                                    $before_user_code,
                                    $before_department_code,
                                    $before_result);
                                // 次データ計算事前処理
                                $this->beforeArrayWorkingTime($result);
                                // 日付を同じく設定
                                $before_date = $current_date;
                                $before_result = $result;
                            }catch(\PDOException $pe){
                                $add_results = false;
                                throw $pe;
                            }catch(\Exception $e){
                                $add_results = false;
                                throw $e;
                            }
                        } elseif ($current_user_code == $before_user_code)  {
                            // 部署が変わった場合
                            Log::DEBUG('department break ');
                            // ユーザー労働時間計算(１個前のユーザーを計算する)
                            $this->calcWorkingTime(
                                $before_date,
                                $before_user_code,
                                $before_department_code);
                            try{
                                // temporaryに登録する
                                $this->insTempCalcItem($before_date, $before_result);
                            }catch(\PDOException $pe){
                                $add_results = false;
                                throw $pe;
                            }
                            // 次データ計算事前処理
                            $this->beforeArrayWorkingTime($result);
                            // 日付を同じく設定
                            $before_date = $current_date;
                            // 部署を同じく設定
                            $before_department_code = $current_department_code;
                            $before_result = $result;
                        } else {
                            // ユーザーが変わった場合
                            Log::DEBUG('user break ');
                            Log::DEBUG('$result->user_code  '.$result->user_code);
                            Log::DEBUG('$result->mode  '.$result->mode);
                            Log::DEBUG('$result->record_datetime  '.$result->record_datetime);
                            Log::DEBUG('$result->working_timetable_from_time  '.$result->working_timetable_from_time);
                            Log::DEBUG('$result->working_timetable_to_time  '.$result->working_timetable_to_time);
                            // ユーザー労働時間計算(１個前のユーザーを計算する)
                            $this->calcWorkingTime(
                                $before_date,
                                $before_user_code,
                                $before_department_code);
                            try{
                                // temporaryに登録する
                                $this->insTempCalcItem($before_date, $before_result);
                            }catch(\PDOException $pe){
                                $add_results = false;
                                throw $pe;
                            }
                            // 次データ計算事前処理
                            $this->beforeArrayWorkingTime($result);
                            // 日付を同じく設定
                            $before_date = $current_date;
                            // 部署を同じく設定
                            $before_department_code = $current_department_code;
                            // ユーザーを同じく設定
                            $before_user_code = $current_user_code;
                            $before_result = $result;
                        }
                    }
                } else {
                    // 前のデータが打刻ありであれば計算する
                    if (count($this->array_working_mode) > 0) {
                        try{
                            // ユーザー労働時間登録(１個前のユーザーを登録する)
                            $add_results = $this->addWorkingTime(
                                $before_date,
                                $before_user_code,
                                $before_department_code,
                                $before_result);
                            // 次データ計算事前処理(刻ないデータはbeforeArrayWorkingTimeは使用しない)
                            $before_date = null;
                            $before_user_code = null;
                            $before_department_code = null;
                            $before_result = null;
                            // 次データ計算事前処理
                            $this->beforeArrayWorkingTime($result);
                        }catch(\PDOException $pe){
                            $add_results = false;
                        }catch(\Exception $e){
                            $add_results = false;
                        }
                    }
                    $ptn = $chk_result;
                    $this->pushArrayCalc($this->setNoInputTimePtn($ptn));
                    Log::DEBUG('calcWorkingTimeDate error ptn = '.$ptn.' date = '.$result->record_date.' dapartment = '.$result->department_code.' user = '.$result->user_code);
                    try{
                        // temporaryに登録する
                        $this->insTempCalcItem($result->record_date, $result);
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
                Log::DEBUG('no input time target_date = '.$target_date.' dapartment = '.$result->department_code.' user = '.$result->user_code);
                // 前のデータが打刻ありであれば計算する
                Log::DEBUG('count($this->array_working_mode)e = '.count($this->array_working_mode));
                if (count($this->array_working_mode) > 0) {
                    try{
                        Log::DEBUG('$before_user_code = '.$before_user_code);
                        // ユーザー労働時間登録(１個前のユーザーを登録する)
                        $add_results = $this->addWorkingTime(
                            $before_date,
                            $before_user_code,
                            $before_department_code,
                            $before_result);
                        // 次データ計算事前処理(刻ないデータはbeforeArrayWorkingTimeは使用しない)
                        $before_date = null;
                        $before_user_code = null;
                        $before_department_code = null;
                        $before_result = null;
                        // 次データ計算事前処理
                        $this->beforeArrayWorkingTime($result);
                    }catch(\PDOException $pe){
                        $add_results = false;
                    }catch(\Exception $e){
                        $add_results = false;
                    }
                }
                // 打刻ないデータはtempに出力
                $ptn = 0;
                $this->pushArrayCalc($this->setNoInputTimePtn($ptn));
                // temporaryに登録する
                $this->insTempCalcItem($target_date, $result);
                // 次データ計算事前処理(刻ないデータはbeforeArrayWorkingTimeは使用しない)
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
                // ユーザー労働時間登録(１個前のユーザーを登録する)
                $add_results = $this->addWorkingTime(
                    $current_date,
                    $current_user_code,
                    $current_department_code,
                    $current_result);
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
    private function addWorkingTime($target_date, $target_user_code, $target_department_code, $target_result)
    {
        Log::DEBUG('---------------------- addWorkingTime in ------------------------ ');
        // ユーザー労働時間計算(１個前のユーザーを計算する)
        $this->calcWorkingTime(
            $target_date,
            $target_user_code,
            $target_department_code);
        // temporaryに登録する
        try{
            $this->insTempCalcItem($target_date, $target_result);
        }catch(\PDOException $pe){
            throw $pe;
        }

        Log::DEBUG('---------------------- addWorkingTime end ------------------------ ');
        return true;

    }

    /**
     * ユーザー労働時間計算
     *
     * @return 労働時間計算結果
     */
    private function calcWorkingTime($target_date, $target_user_code, $target_department_code)
    {
        Log::DEBUG('---------------------- calcWorkingTime in ------------------------ ');
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
        $working_status = null;
        // 事前にテーブル再取得（テーブル取得1日以前のMAX打刻時刻）しておく
        $before_value_mode = null;
        $before_value_datetime = null;
        $before_times = $work_time->getBeforeDailyMaxData();
        if(isset($before_times)){
            foreach ($before_times as $before_result) {
                // 打刻時刻の設定
                $before_value_mode = $before_result->mode;
                $before_value_datetime = $before_result->record_datetime;
                break;
            }
        }
        // 事前にテーブル再取得（テーブル取得1日以降のMIN打刻時刻）しておく
        $after_value_mode = null;
        $after_value_datetime = null;
        $after_times = $work_time->getAfterDailyMinData();
        if(isset($after_times)){
            foreach ($after_times as $after_result) {
                // 打刻時刻の設定
                $after_value_mode = $after_result->mode;
                $after_value_datetime = $after_result->record_datetime;
                break;
            }
        }
        $cnt = 0;
        // 前提 count($array_working_mode) = count($array_working_datetime)
        Log::DEBUG('array_working_mode count = '.count($this->array_working_mode));
        for($i=0;$i<count($this->array_working_mode);$i++){
            $value_mode = $this->array_working_mode[$i];
            $value_record_datetime = $this->array_working_datetime[$i];
            $value_timetable_from_time = $this->array_timetable_from_time[$i];
            $value_timetable_to_time = $this->array_timetable_to_time[$i];
            $value_interval = $this->array_interval[$i];
            Log::DEBUG('ユーザー労働時間計算 cnt = '.$cnt);
            Log::DEBUG('ユーザー労働時間計算 value_mode = '.$value_mode);
            Log::DEBUG('ユーザー労働時間計算 value_record_datetime = '.$value_record_datetime);
            Log::DEBUG('ユーザー労働時間計算 value_timetable_from_time = '.$value_timetable_from_time);
            Log::DEBUG('ユーザー労働時間計算 value_timetable_to_time = '.$value_timetable_to_time);
            Log::DEBUG('ユーザー労働時間計算 value_interval = '.$value_interval);
            // 出勤打刻の場合
            if ($value_mode == Config::get('const.C005.attendance_time')) {
                $this->setAttendancetime(
                    $cnt,
                    $work_time,
                    $value_record_datetime,
                    $value_timetable_from_time,
                    $value_timetable_to_time,
                    $value_interval,
                    $before_value_mode,
                    $before_value_datetime
                );
                $before_value_mode = $value_mode;
                $before_value_datetime = $value_record_datetime;
            } elseif ($value_mode == Config::get('const.C005.leaving_time')) {      // 退勤の場合
                $this->setLeavingtime(
                    $cnt,
                    $work_time,
                    $value_record_datetime,
                    $value_timetable_from_time,
                    $value_timetable_to_time,
                    $before_value_mode,
                    $before_value_datetime
                );
            } elseif ($value_mode == Config::get('const.C005.missing_middle_time')) {       // 中抜けの場合
                $this->setMissingMiddleTime(
                    $cnt,
                    $work_time,
                    $value_record_datetime,
                    $value_timetable_from_time,
                    $value_timetable_to_time,
                    $before_value_mode,
                    $before_value_datetime
                );
            } elseif ($value_mode == Config::get('const.C005.missing_middle_return_time')) {    // 中抜け戻りの場合
                $this->setMissingMiddleReturnTime(
                    $cnt,
                    $work_time,
                    $value_record_datetime,
                    $value_timetable_from_time,
                    $value_timetable_to_time,
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
        $value_record_datetime, $value_timetable_from_time, $value_timetable_to_time, $value_interval,
        $before_value_mode, $before_value_datetime)
    {

        Log::DEBUG('---------------------- setAttendancetime in ------------------------ ');
        Log::DEBUG('出勤打刻処理');
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
        Log::DEBUG('before_value_mode set = '.$before_value_mode);
        Log::DEBUG('cnt set = '.$cnt);
        // パターン設定
        $ptn = null;
        // インターバルチェック設定
        $chk_interval = '0';
        // インターバル時間を分に変換
        if ($value_interval > 0) {
            $value_interval_minute = floor($value_interval) * 60 + ($value_interval-floor($value_interval)) * 60;
            Log::DEBUG('value_interval_minute = '.$value_interval_minute);
        }

        // ---------------------出勤が最初の場合 -----------------------------------------------------------------------------------
        if ($cnt == 0) {
            if ($before_value_mode == Config::get('const.C005.attendance_time')) {          // １個前のモードが出勤である場合
                Log::DEBUG('１個前のモードが出勤である');
                if ($record_before_datetime < $attendance_from_date) {                      // １個前の打刻時刻 < 出勤1日のはじめ
                    // パターン２（打刻ミス（出勤済）（要確認）。勤務状態は打刻なし）
                    $ptn = '2';
                }
            } elseif ($before_value_mode == Config::get('const.C005.leaving_time')) {       // １個前のモードが退勤である場合
                Log::DEBUG('１個前のモードが退勤である');
                if ($record_datetime >= $attendance_from_date &&
                            $record_datetime < $timetable_from_date) {                      // 出勤1日のはじめ <= 打刻時刻 < タイムテーブルの始業時刻
                    if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                        // パターン１（正常出勤。勤務状態は出勤状態）
                        $ptn = '1';
                        // インターバル時間が設定されていたら時間差を求める
                        if ($value_interval > 0) {
                            $calc_diffmin = 
                                $record_before_datetime->diffInMinutes($attendance_from_date);      // value_interval時間後
                            if ($calc_diffmin > $value_interval) {
                                $chk_interval = '1';
                            }
                        }
                    }
                } elseif ($record_datetime >= $timetable_from_date &&
                            $record_datetime < $timetable_to_date) {                        // タイムテーブルの始業時刻 <= 打刻時刻 < タイムテーブルの終業時刻
                    if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                        // パターン３（遅刻出勤（遅刻=1）。勤務状態は出勤状態）
                        $ptn = '3';
                        // インターバル時間が設定されていたら時間差を求める
                        if ($value_interval > 0) {
                            $calc_diffmin = 
                                $record_before_datetime->diffInMinutes($attendance_from_date);      // value_interval時間後
                            if ($calc_diffmin > $value_interval) {
                                $chk_interval = '1';
                            }
                        }
                    }
                } elseif ($record_datetime >= $timetable_to_date &&
                            $record_datetime < $attendance_to_date) {                       // タイムテーブルの終業時刻 <= 打刻時刻 < 出勤1日の終わり
                    if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                        // パターン４（遅刻出勤（要確認）。勤務状態は出勤状態）
                        $ptn = '4';
                        // インターバル時間が設定されていたら時間差を求める
                        if ($value_interval > 0) {
                            $calc_diffmin = 
                                $record_before_datetime->diffInMinutes($attendance_from_date);      // value_interval時間後
                            if ($calc_diffmin > $value_interval) {
                                $chk_interval = '1';
                            }
                        }
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
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_time')) {            // １個前のモードが中抜である場合
                Log::DEBUG('１個前のモードが中抜である');
                if ($record_before_datetime < $attendance_from_date) {                      // １個前の打刻時刻 < 出勤1日のはじめ
                    // パターン５（打刻ミス（中抜け戻りしていない）。勤務状態は打刻なし）
                    $ptn = '5';
                }
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_return_time')) {     // １個前のモードが戻り
                Log::DEBUG('１個前のモードが中抜戻りである');
                if ($record_before_datetime < $attendance_from_date) {                      // １個前の打刻時刻 < 出勤1日のはじめ
                    // パターン６（打刻ミス（退勤していない）。勤務状態は打刻なし）
                    $ptn = '6';
                }
            } else {                                                                        // １個前のモードがない
                Log::DEBUG('１個前のモードがない ');
                Log::DEBUG('record_datetime  = '.$record_datetime);
                Log::DEBUG('attendance_from_date = '.$attendance_from_date);
                Log::DEBUG('timetable_from_date = '.$timetable_from_date);
                Log::DEBUG('timetable_to_date = '.$timetable_to_date);
                Log::DEBUG('attendance_to_date = '.$attendance_to_date);
                Log::DEBUG('record_before_datetime set = '.$record_before_datetime);
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
                        // インターバル時間が設定されていたら時間差を求める
                        if ($value_interval > 0) {
                            $calc_diffmin = 
                                $record_before_datetime->diffInMinutes($attendance_from_date);      // value_interval時間後
                            if ($calc_diffmin > $value_interval) {
                                $chk_interval = '1';
                            }
                        }
                    }
                } elseif ($record_datetime >= $timetable_from_date &&
                                 $record_datetime < $timetable_to_date) {                   // タイムテーブルの始業時刻 <= 打刻時刻 < タイムテーブルの終業時刻
                    if ($record_before_datetime >= $attendance_from_date &&
                                     $record_before_datetime < $timetable_to_date) {        // 出勤1日のはじめ <= １個前の打刻時刻 < タイムテーブルの終業時刻
                        // パターン３（遅刻出勤（遅刻=1）。勤務状態は出勤状態）
                        $ptn = '3';
                        // インターバル時間が設定されていたら時間差を求める
                        if ($value_interval > 0) {
                            $calc_diffmin = 
                                $record_before_datetime->diffInMinutes($attendance_from_date);      // value_interval時間後
                            if ($calc_diffmin > $value_interval) {
                                $chk_interval = '1';
                            }
                        }
                    }
                } elseif ($record_datetime >= $timetable_to_date &&
                                $record_datetime < $attendance_to_date) {                   // タイムテーブルの終業時刻 <= 打刻時刻 < 出勤1日の終わり
                    if ($record_before_datetime >= $attendance_from_date &&
                                    $record_before_datetime < $attendance_to_date) {        // 出勤1日のはじめ <= １個前の打刻時刻 < 出勤1日の終わり
                        // パターン４（遅刻出勤（要確認）。勤務状態は出勤状態）
                        $ptn = '4';
                        // インターバル時間が設定されていたら時間差を求める
                        if ($value_interval > 0) {
                            $calc_diffmin = 
                                $record_before_datetime->diffInMinutes($attendance_from_date);      // value_interval時間後
                            if ($calc_diffmin > $value_interval) {
                                $chk_interval = '1';
                            }
                        }
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
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_time')) {            // １個前のモードが中抜
                if ($record_before_datetime >= $attendance_from_date &&
                            $record_before_datetime < $attendance_to_date) {                // 出勤1日のはじめ <= １個前の打刻時刻 < 出勤1日の終わり
                    // パターン５（打刻ミス（中抜け戻りしていない）。勤務状態は打刻なし）
                    $ptn = '5';
                }
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_return_time')) {     // １個前のモードが戻り
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
            $this->pushArrayCalc($this->setAttendanceCollectPtn($ptn, $record_datetime,$chk_interval));
        } else {
            // 不明データとして作成する
            $this->pushArrayCalc($this->setAttendanceCollectPtn('', $record_datetime,$chk_interval));
        }
        Log::DEBUG('出勤打刻処理 end');
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
    private function setAttendanceCollectPtn($ptn, $record_datetime, $chk_interval)
    {
        Log::DEBUG('---------------------- setAttendanceCollectPtn in ------------------------ ');
        $temp_calc_model = new TempCalcWorkingTime();

        if ($ptn == '1') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.attendance'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setWorkingintervalAttribute($chk_interval);
            $temp_calc_model->setCurrentcalcAttribute('1');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
        } elseif ($ptn == '2') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_001'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setWorkingintervalAttribute($chk_interval);
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
        } elseif ($ptn == '3') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.attendance'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('1');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setWorkingintervalAttribute($chk_interval);
            $temp_calc_model->setCurrentcalcAttribute('1');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
        } elseif ($ptn == '4') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.attendance'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_002'));
            $temp_calc_model->setLateAttribute('1');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setWorkingintervalAttribute($chk_interval);
            $temp_calc_model->setCurrentcalcAttribute('1');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
        } elseif ($ptn == '5') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_003'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setWorkingintervalAttribute($chk_interval);
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
        } elseif ($ptn == '6') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_001'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setWorkingintervalAttribute($chk_interval);
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
        } else {
            // 不明データとして作成する
            $temp_calc_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.unknown'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_004'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setWorkingintervalAttribute($chk_interval);
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
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
        $before_value_mode, $before_value_datetime)
    {
        Log::DEBUG('---------------------- setLeavingtime in ------------------------ ');
        Log::DEBUG('退勤打刻処理');
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
        Log::DEBUG('before_value_mode set = '.$before_value_mode);
        Log::DEBUG('before_value_datetime set = '.$before_value_datetime);
        // パターン設定
        $ptn = null;

        // ---------------------退勤が最初の場合 -----------------------------------------------------------------------------------
        if ($cnt == 0) {
            if ($before_value_mode == Config::get('const.C005.attendance_time') ||
                $before_value_mode == Config::get('const.C005.missing_middle_return_time')) {   // １個前のモードが出勤または戻りである場合
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
                        $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime));
                        $ptn = '3';
                        $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime));
                        // パターン4は下で設定
                        $ptn = '4';
                    }
                } elseif ($record_datetime >= $timetable_to_date &&
                            $record_datetime < $attendance_to_date) {                       // タイムテーブルの終業時刻 <= 打刻時刻 < 出勤1日の終わり
                    if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                        // パターン２３５
                        $ptn = '2';
                        $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime));
                        $ptn = '3';
                        $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime));
                        // パターン5は下で設定
                        $ptn = '5';
                    }
                } elseif ($record_datetime > $attendance_to_date) {                         // 出勤1日の終わり < 打刻時刻
                    if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                        // パターン２３５
                        $ptn = '2';
                        $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime));
                        $ptn = '3';
                        $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime));
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
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_time')) {    // １個前のモードが中抜である場合
                if ($record_before_datetime < $attendance_from_date) {                      // １個前の打刻時刻 < 出勤1日のはじめ
                    // パターン７（打刻ミス（中抜け戻りしていない）。勤務状態は打刻なし）
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
                } elseif ($record_datetime > $timetable_to_date) {                          // 打刻時刻 < 出勤1日の終わり
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
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_time')) {    // １個前のモードが中抜
                if ($record_before_datetime >= $attendance_from_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻
                    // パターン７（打刻ミス（中抜け戻りしていない）。勤務状態は打刻なし）
                    $ptn = '7';
                }
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_return_time')) {     // １個前のモードが戻り
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
                                        $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime));
                                        $ptn = '3';
                                        $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime));
                                        // パターン4は下で設定
                                        $ptn = '4';
                                    } else {
                                        // パターン２３５
                                        $ptn = '2';
                                        $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime));
                                        $ptn = '3';
                                        $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime));
                                        // パターン5は下で設定
                                        $ptn = '5';
                                    }
                                    break;
                                }
                            }
                        } else {
                            // パターン２３４
                            $ptn = '2';
                            $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime));
                            $ptn = '3';
                            $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime));
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
                                        $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime));
                                        $ptn = '3';
                                        $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime));
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
                            $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime));
                            $ptn = '3';
                            $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime));
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
                                    $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime));
                                    $ptn = '3';
                                    $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime));
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
                        $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime));
                        $ptn = '3';
                        $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime));
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
            $this->pushArrayCalc($this->setLeavingCollectPtn($ptn, $record_datetime));
        } else {
            // 不明データとして作成する
            $this->pushArrayCalc($this->setLeavingCollectPtn('', $record_datetime));
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
    private function setLeavingCollectPtn($ptn, $record_datetime)
    {
        Log::DEBUG('---------------------- setLeavingCollectPtn in ------------------------ ');
        $temp_calc_model = new TempCalcWorkingTime();
        $chk_interval = '0';

        if ($ptn == '1') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.leaving'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_005'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setWorkingintervalAttribute($chk_interval);
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
        } elseif ($ptn == '2') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.leaving'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_005'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setWorkingintervalAttribute($chk_interval);
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
        } elseif ($ptn == '3') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.attendance'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_006'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setWorkingintervalAttribute($chk_interval);
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
        } elseif ($ptn == '4') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.leaving'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('1');
            $temp_calc_model->setWorkingintervalAttribute($chk_interval);
            $temp_calc_model->setCurrentcalcAttribute('1');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
        } elseif ($ptn == '5') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.leaving'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setWorkingintervalAttribute($chk_interval);
            $temp_calc_model->setCurrentcalcAttribute('1');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
        } elseif ($ptn == '6') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_002'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setWorkingintervalAttribute($chk_interval);
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
        } elseif ($ptn == '7') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_003'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setWorkingintervalAttribute($chk_interval);
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
        } elseif ($ptn == '8') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.leaving'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setWorkingintervalAttribute($chk_interval);
            $temp_calc_model->setCurrentcalcAttribute('1');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
        } else {
            // 不明データとして作成する
            $temp_calc_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.unknown'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_004'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setWorkingintervalAttribute($chk_interval);
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
        }
            
        Log::DEBUG('---------------------- setLeavingCollectPtn end ------------------------ ');
        return $temp_calc_model;
    }

    /**
     * 中抜打刻処理
     *
     * @param  打刻時刻のSEQ
     *         打刻時刻
     *         １個前のモード
     * @return チェック結果
     */
    private function setMissingMiddleTime($cnt, $work_time,
        $value_record_datetime, $value_timetable_from_time, $value_timetable_to_time,
        $before_value_mode, $before_value_datetime)
    {
        Log::DEBUG('---------------------- setMissingMiddleTime in ------------------------ ');
        Log::DEBUG('中抜打刻処理');
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

        // ---------------------中抜が最初の場合 -----------------------------------------------------------------------------------
        if ($cnt == 0) {
            if ($before_value_mode == Config::get('const.C005.attendance_time') ||
                $before_value_mode == Config::get('const.C005.missing_middle_return_time')) {   // １個前のモードが出勤または戻りである場合
                if ($record_datetime >= $attendance_from_date &&
                            $record_datetime < $attendance_to_date) {                       // 出勤1日のはじめ <= 打刻時刻 < 出勤1日の終わり
                    if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                        // パターン１（正常中抜。勤務状態は中抜状態。）
                        $ptn = '1';
                    }
                }
            } elseif ($before_value_mode == Config::get('const.C005.leaving_time')) {       // １個前のモードが退勤である場合
                if ($record_before_datetime < $attendance_from_date) {                      // １個前の打刻時刻 < 出勤1日のはじめ
                    // パターン２（打刻ミス（出勤していない）。勤務状態は打刻なし）
                    $ptn = '2';
                }
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_time')) {    // １個前のモードが中抜である場合
                if ($record_before_datetime < $attendance_from_date) {                      // １個前の打刻時刻 < 出勤1日のはじめ
                    // パターン３（打刻ミス（中抜け戻りしていない）。勤務状態は打刻なし）
                    $ptn = '3';
                }
            } else {                                                                        // １個前のモードがない
                // 不明データとして作成する
                $log_data = $work_time->getParamDatefromAttribute();
                $log_data .= $work_time->getParamUsercodeAttribute();
                $log_data .= $work_time->getParamDepartmentcodeAttribute();
                Log::error(Config::get('const.MSG_ERROR.mismatch_data').' method = setMissingMiddleTime1 '.$log_data);
            }
        // ---------------------中抜が２番目以降の場合 -----------------------------------------------------------------------------------
        } else {
            if ($before_value_mode == Config::get('const.C005.attendance_time') ||
                $before_value_mode == Config::get('const.C005.missing_middle_return_time')) {   // １個前のモードが出勤または戻りである場合
                if ($record_datetime >= $attendance_from_date &&
                    $record_datetime < $timetable_from_date) {                              // 出勤1日のはじめ <= 打刻時刻 < タイムテーブルの始業時刻
                    if ($record_before_datetime >= $attendance_from_date &&
                        $record_before_datetime < $timetable_from_date) {                   // 出勤1日のはじめ <= １個前の打刻時刻 < タイムテーブルの始業時刻
                        // パターン１（正常中抜。勤務状態は中抜状態。）
                        $ptn = '1';
                    }
                } elseif ($record_datetime >= $timetable_from_date &&
                            $record_datetime < $timetable_to_date) {                        // タイムテーブルの始業時刻 <= 打刻時刻 < タイムテーブルの終業時刻
                    if ($record_before_datetime >= $attendance_from_date &&
                        $record_before_datetime < $timetable_to_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻 < タイムテーブルの終業時刻
                        // パターン１（正常中抜。勤務状態は中抜状態。）
                        $ptn = '1';
                    }
                } elseif ($record_datetime >= $timetable_to_date &&
                            $record_datetime < $attendance_to_date) {                       // タイムテーブルの終業時刻 <= 打刻時刻 < 出勤1日の終わり
                    if ($record_before_datetime >= $attendance_from_date &&
                        $record_before_datetime < $timetable_to_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻 < 出勤1日の終わり
                        // パターン１（正常中抜。勤務状態は中抜状態。）
                        $ptn = '1';
                    }
                } elseif ($record_datetime > $timetable_to_date) {                          // 打刻時刻 > 出勤1日の終わり
                    if ($record_before_datetime >= $attendance_from_date &&
                        $record_before_datetime < $timetable_to_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻 < 出勤1日の終わり
                        // パターン１（正常中抜。勤務状態は中抜状態。）
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
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_time')) {    // １個前のモードが中抜
                if ($record_before_datetime >= $attendance_from_date) {                     // 出勤1日のはじめ <= １個前の打刻時刻
                    // 不明データとして作成する
                    $collect_working_times = $this->setMissingmiddleCollectPtn('', $record_datetime);
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
            $this->pushArrayCalc($this->setMissingmiddleCollectPtn($ptn, $record_datetime));
        } else {
            // 不明データとして作成する
            $this->pushArrayCalc($this->setMissingmiddleCollectPtn('', $record_datetime));
        }
        Log::DEBUG('---------------------- setMissingMiddleTime end ------------------------ ');
        Log::DEBUG('中抜打刻処理 end');
            
    }

    /**
     * 中抜打刻時刻テーブル設定
     *
     *        $collect_working_times
     *          'mode': 打刻モード（中抜）
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
    private function setMissingmiddleCollectPtn($ptn, $record_datetime)
    {
        Log::DEBUG('---------------------- setMissingmiddleCollectPtn in ------------------------ ');
        $temp_calc_model = new TempCalcWorkingTime();
        $chk_interval = '0';

        if ($ptn == '1') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.missing_middle_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.missing_middle'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setWorkingintervalAttribute($chk_interval);
            $temp_calc_model->setCurrentcalcAttribute('1');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
        } elseif ($ptn == '2') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.missing_middle_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_008'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setWorkingintervalAttribute($chk_interval);
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
        } elseif ($ptn == '3') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.missing_middle_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_003'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setWorkingintervalAttribute($chk_interval);
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
        } else {
            // 不明データとして作成する
            $temp_calc_model->setModeAttribute(Config::get('const.C005.missing_middle_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.unknown'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_004'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setWorkingintervalAttribute($chk_interval);
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
        }

        Log::DEBUG('---------------------- setMissingmiddleCollectPtn end ------------------------ ');
        return $temp_calc_model;
            
    }

    /**
     * 中抜戻り打刻処理
     *
     * @param  打刻時刻のSEQ
     *         打刻時刻
     *         １個前のモード
     * @return チェック結果
     */
    private function setMissingMiddleReturnTime($cnt, $work_time,
        $value_record_datetime, $value_timetable_from_time, $value_timetable_to_time,
        $before_value_mode, $before_value_datetime)
    {
        Log::DEBUG('---------------------- setMissingMiddleReturnTime in ------------------------ ');
        Log::DEBUG('中抜戻り打刻処理');
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

        // ---------------------中抜戻りが最初の場合 -----------------------------------------------------------------------------------
        if ($cnt == 0) {
            if ($before_value_mode == Config::get('const.C005.attendance_time') ||
                $before_value_mode == Config::get('const.C005.leaving_time') ||
                $before_value_mode == Config::get('const.C005.missing_middle_return_time')) {   // １個前のモードが出勤または退勤または中抜戻りである場合
                if ($record_datetime >= $attendance_from_date &&
                            $record_datetime < $attendance_to_date) {                       // 出勤1日のはじめ <= 打刻時刻 < 出勤1日の終わり
                    if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                        // パターン１（打刻ミス（中抜けしていない）。勤務状態は打刻なし）
                        $ptn = '1';
                    }
                }
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_time')) {    // １個前のモードが中抜である場合
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
        // ---------------------中抜戻りが２番目以降の場合 -----------------------------------------------------------------------------------
        } else {
            if ($before_value_mode == Config::get('const.C005.attendance_time') ||
                $before_value_mode == Config::get('const.C005.leaving_time') ||
                $before_value_mode == Config::get('const.C005.missing_middle_return_time')) {   // １個前のモードが出勤または退勤または中抜戻りである場合
                if ($record_datetime >= $attendance_from_date) {                            // 出勤1日のはじめ <= 打刻時刻
                    if ($record_before_datetime >= $attendance_from_date) {                 // 出勤1日のはじめ <= １個前の打刻時刻
                        // パターン１（打刻ミス（中抜けしていない）。勤務状態は打刻なし）
                        $ptn = '1';
                    }
                }
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_time')) {    // １個前のモードが中抜
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
            $this->pushArrayCalc($this->setMissingmiddleReturnCollectPtn($ptn, $record_datetime));
        } else {
            // 不明データとして作成する
            $this->pushArrayCalc($this->setMissingmiddleReturnCollectPtn('', $record_datetime));
        }
        Log::DEBUG('中抜戻り打刻処理 end');
        Log::DEBUG('---------------------- setMissingMiddleReturnTime end ------------------------ ');
            
    }

    /**
     * 中抜戻り打刻時刻テーブル設定
     *
     *        $collect_working_times
     *          'mode': 打刻モード（中抜戻り）
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
    private function setMissingmiddleReturnCollectPtn($ptn, $record_datetime)
    {
        Log::DEBUG('---------------------- setMissingmiddleReturnCollectPtn in ------------------------ ');
        $temp_calc_model = new TempCalcWorkingTime();
        $chk_interval = '0';

        if ($ptn == '1') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.missing_middle_return_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_009'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setWorkingintervalAttribute($chk_interval);
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
        } elseif ($ptn == '2') {
            $temp_calc_model->setModeAttribute(Config::get('const.C005.missing_middle_return_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.missing_middle_return'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setWorkingintervalAttribute($chk_interval);
            $temp_calc_model->setCurrentcalcAttribute('1');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
        } else {
            // 不明データとして作成する
            $temp_calc_model->setModeAttribute(Config::get('const.C005.missing_middle_return_time'));
            $temp_calc_model->setRecorddatetimeAttribute($record_datetime);
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.unknown'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_004'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setWorkingintervalAttribute($chk_interval);
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
        }

        Log::DEBUG('---------------------- setMissingmiddleReturnCollectPtn end ------------------------ ');
        return $temp_calc_model;
            
    }

    /**
     * 打刻データない場合の打刻時刻テーブル設定
     *
     *        $collect_working_times
     *          'mode': 打刻モード（中抜戻り）
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
    private function setNoInputTimePtn($ptn)
    {
        Log::DEBUG('---------------------- setNoInputTimePtn in ------------------------ ');
        $temp_calc_model = new TempCalcWorkingTime();
        $chk_interval = '0';

        if ($ptn == '0') {
            $temp_calc_model->setModeAttribute('0');
            $temp_calc_model->setRecorddatetimeAttribute('');
            $temp_calc_model->setWorkingstatusAttribute('0');
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_008'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setWorkingintervalAttribute($chk_interval);
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
        } elseif ($ptn == '1') {    // 設定ミス
            $temp_calc_model->setModeAttribute('0');
            $temp_calc_model->setRecorddatetimeAttribute('');
            $temp_calc_model->setWorkingstatusAttribute('0');
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_010'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setWorkingintervalAttribute($chk_interval);
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
        } elseif ($ptn == '2') {    // 設定ミス
            $temp_calc_model->setModeAttribute('0');
            $temp_calc_model->setRecorddatetimeAttribute('');
            $temp_calc_model->setWorkingstatusAttribute('0');
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_011'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setWorkingintervalAttribute($chk_interval);
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
        } elseif ($ptn == '3') {    // 設定ミス
            $temp_calc_model->setModeAttribute('0');
            $temp_calc_model->setRecorddatetimeAttribute('');
            $temp_calc_model->setWorkingstatusAttribute('0');
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_012'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setWorkingintervalAttribute($chk_interval);
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
        } elseif ($ptn == '4') {    // 設定ミス
            $temp_calc_model->setModeAttribute('0');
            $temp_calc_model->setRecorddatetimeAttribute('');
            $temp_calc_model->setWorkingstatusAttribute('0');
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_013'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setWorkingintervalAttribute($chk_interval);
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
        } elseif ($ptn == '5') {    // 設定ミス
            $temp_calc_model->setModeAttribute('0');
            $temp_calc_model->setRecorddatetimeAttribute('');
            $temp_calc_model->setWorkingstatusAttribute('0');
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_014'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setWorkingintervalAttribute($chk_interval);
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('0');
            $temp_calc_model->setPatternAttribute($ptn);
        } else {
            // 不明データとして作成する
            $temp_calc_model->setModeAttribute('0');
            $temp_calc_model->setRecorddatetimeAttribute('');
            $temp_calc_model->setWorkingstatusAttribute(Config::get('const.C012.unknown'));
            $temp_calc_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_004'));
            $temp_calc_model->setLateAttribute('0');
            $temp_calc_model->setLeaveearlyAttribute('0');
            $temp_calc_model->setWorkingintervalAttribute($chk_interval);
            $temp_calc_model->setCurrentcalcAttribute('0');
            $temp_calc_model->setTobeconfirmedAttribute('1');
            $temp_calc_model->setPatternAttribute($ptn);
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
        $chk_result = 0;
        // 部署設定されているか
        if ($chkdata->department_code == null) {
            if ($chkdata->user_code != null) {
                array_push($this->array_massegedata, $chkdata->user_name.Config::get('const.MSG_ERROR.not_setting_department_code'));
                Log::error($chkdata->user_name.Config::get('const.MSG_ERROR.not_setting_department_code'));
            } else {
                array_push($this->array_massegedata, Config::get('const.MSG_ERROR.not_setting_department_code_nouser'));
                Log::error(Config::get('const.MSG_ERROR.not_setting_department_code_nouser'));
            }
            $chk_result = 1;
        }
        // 締日設定されているか
        if ($chkdata->closing == null) {
            array_push($this->array_massegedata, Config::get('const.MSG_ERROR.not_setting_closing'));
            Log::error(Config::get('const.MSG_ERROR.not_setting_closing'));
            $chk_result = 2;
        }
        // 時間単位設定されているか
        if ($chkdata->time_unit == null) {
            array_push($this->array_massegedata, Config::get('const.MSG_ERROR.not_setting_time_unit'));
            Log::error(Config::get('const.MSG_ERROR.not_setting_time_unit'));
            $chk_result = 3;
        }
        // 時間の丸め設定されているか
        if ($chkdata->time_rounding == null) {
            array_push($this->array_massegedata, Config::get('const.MSG_ERROR.not_setting_time_rounding'));
            Log::error(Config::get('const.MSG_ERROR.not_setting_time_rounding'));
            $chk_result = 4;
        }
        // 期首月設定されているか
        if ($chkdata->beginning_month == null) {
            array_push($this->array_massegedata, Config::get('const.MSG_ERROR.not_setting_beginning_month'));
            Log::error(Config::get('const.MSG_ERROR.not_setting_beginning_month'));
            $chk_result = 5;
        }

        Log::DEBUG('---------------------- chkSettingData end ------------------------ ');
        return $chk_result;
            
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
        $this->array_massegedata = array();
        $this->array_working_mode = array();
        $this->array_working_datetime = array();
        $this->array_timetable_from_time = array();
        $this->array_timetable_to_time = array();
        $this->array_interval = array();
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
        $this->array_interval[] = $result->interval;
        Log::DEBUG('pushArrayWorkingTime  $result->mode '.$result->mode);
        Log::DEBUG('pushArrayWorkingTime  $result->record_datetime '.$result->record_datetime);
        Log::DEBUG('pushArrayWorkingTime  count $array_working_mode '.count($this->array_working_mode));
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
        $this->array_calc_interval = array();
        $this->array_calc_calc = array();
        $this->array_calc_to_be_confirmed = array();
        $this->array_calc_pattern = array();
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
        Log::DEBUG('pushArrayCalc getModeAttribute = '.$temp_calc_model->getModeAttribute());
        $this->array_calc_mode[] = $temp_calc_model->getModeAttribute();
        $this->array_calc_time[] = $temp_calc_model->getRecorddatetimeAttribute();
        $this->array_calc_status[] = $temp_calc_model->getWorkingstatusAttribute();
        $this->array_calc_note[] = $temp_calc_model->getNoteAttribute();
        $this->array_calc_late[] = $temp_calc_model->getLateAttribute();
        $this->array_calc_leave_early[] = $temp_calc_model->getLeaveearlyAttribute();
        $this->array_calc_interval[] = $temp_calc_model->getWorkingintervalAttribute();
        $this->array_calc_calc[] = $temp_calc_model->getCurrentcalcAttribute();
        $this->array_calc_to_be_confirmed[] = $temp_calc_model->getTobeconfirmedAttribute();
        $this->array_calc_pattern[] = $temp_calc_model->getPatternAttribute();
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
        $temp_calc_model->setWorkingdateAttribute($target_date);
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
            Log::DEBUG('$result->shift_no = '.$result->shift_no);
            $temp_calc_model->setShiftnoAttribute($result->shift_no);
        } else {
            Log::DEBUG('$result->shift_no = null');
            $temp_calc_model->setShiftnoAttribute('');
        }
        $temp_calc_model->setShiftnameAttribute($result->shift_name);
        $temp_calc_model->setShiftfromtimeAttribute($result->shift_from_time);
        $temp_calc_model->setShifttotimeAttribute($result->shift_to_time);
        $temp_calc_model->setRecordyearAttribute($result->record_year);
        $temp_calc_model->setRecordmonthAttribute($result->record_month);
        $temp_calc_model->setRecorddateAttribute($result->record_date);
        $temp_calc_model->setRecordtimeAttribute($result->record_time);
        $temp_calc_model->setWeekdaykubunAttribute($result->weekday_kubun);
        $temp_calc_model->setWeekdaynameAttribute($result->weekday_name);
        $temp_calc_model->setBusinesskubunAttribute($result->business_kubun);
        $temp_calc_model->setBusinessnameAttribute($result->business_name);
        $temp_calc_model->setHolidaykubunAttribute($result->holiday_kubun);
        $temp_calc_model->setHolidaynameAttribute($result->holiday_name);
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
            $temp_calc_model->setModeAttribute($this->array_calc_mode[$i]);
            $temp_calc_model->setRecorddatetimeAttribute($this->array_calc_time[$i]);
            $temp_calc_model->setWorkingstatusAttribute($this->array_calc_status[$i]);
            $temp_calc_model->setNoteAttribute($this->array_calc_note[$i]);
            $temp_calc_model->setLateAttribute($this->array_calc_late[$i]);
            $temp_calc_model->setLeaveearlyAttribute($this->array_calc_leave_early[$i]);
            $temp_calc_model->setCurrentcalcAttribute($this->array_calc_calc[$i]);
            $temp_calc_model->setTobeconfirmedAttribute($this->array_calc_to_be_confirmed[$i]);
            $temp_calc_model->setWorkingintervalAttribute($this->array_calc_interval[$i]);
            $temp_calc_model->setPatternAttribute($this->array_calc_pattern[$i]);
            try{
                $temp_calc_model->insertTempCalcWorkingtime();
            }catch(\PDOException $pe){
                throw $pe;
            }
            Log::DEBUG('insTempCalcItem end');
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
        // 計算用時刻保存
        $add_cnt = 0;
        $attendance_time = '';
        $leaving_time = '';
        $missing_middle_time = '';
        $missing_middle_return_time = '';
        $working_status = '';
        $array_working_time_kubun = array(Config::get('const.C004.regular_working_time'),
            '',
            Config::get('const.C004.out_of_regular_working_time'),
            Config::get('const.C004.out_of_regular_night_working_time'),
            Config::get('const.C004.statutory_working_time'),
            Config::get('const.C004.out_of_statutory_working_time'),
            Config::get('const.C004.out_of_statutory_night_working_time'),
            ''
        );
        // 出勤・退勤の労働時間数配列
        $array_calc_time = array();
        for ($i=0;$i<count($array_working_time_kubun);$i++) {
            $array_calc_time[$i] = 0; 
        }
        // 中抜け・戻りの時間数配列
        $array_missing_middle_time = array();
        for ($i=0;$i<count($array_working_time_kubun);$i++) {
            $array_missing_middle_time[$i] = 0; 
        }
        // データ登録用出勤・退勤の労働時刻数配列
        $array_add_attendance_time = array();
        $array_add_leaving_time = array();
        // データ登録用中抜け・戻りの労働時刻数配列
        $array_add_missing_middle_time = array();
        $array_add_missing_middle_return_time = array();

        // ユーザー単位処理
        $temp_calc_model = new TempCalcWorkingTime();
        $worktimes = $temp_calc_model->getTempCalcWorkingtime();
        $add_result = true;
        foreach ($worktimes as $result) {
            // 現在の情報保存
            $current_date = $result->working_date;
            $current_department_code = $result->department_code;
            $current_user_code = $result->user_code;
            $current_result = $result;
            if ($before_date == null) {$before_date = $current_date;}
            if ($before_department_code == null) {$before_department_code = $current_department_code;}
            if ($before_user_code == null) {$before_user_code = $current_user_code;}
            if ($before_result == null) {$before_result = $result;}
            // 同じキーの場合
            if ($current_date == $before_date &&
                $current_department_code == $before_department_code &&
                $current_user_code == $before_user_code) {
                $add_cnt++;
                if ($result->mode == Config::get('const.C005.attendance_time')) {$attendance_time = $result->record_datetime;}
                if ($result->mode == Config::get('const.C005.leaving_time')) {$leaving_time = $result->record_datetime;}
                if ($result->mode == Config::get('const.C005.missing_middle_time')) {$missing_middle_time = $result->record_datetime;}
                if ($result->mode == Config::get('const.C005.missing_middle_return_time')) {$missing_middle_return_time = $result->record_datetime;}
                $note .= $result->note.' ';
                if ($result->late == '1') {$late = '1';}
                if ($result->leave_early == '1') {$leave_early = '1';}
                if ($result->to_be_confirmed == '1') {$to_be_confirmed = '1';}
                $working_timetable_no = $result->working_timetable_no;
                $dtNow = new Carbon();
                Log::DEBUG('$result->record_datetime < $dtNow '.$result->record_datetime.' '.$dtNow);
                if ($result->record_datetime < $dtNow) {                            // 打刻時刻 < 現在時刻
                    $working_status = $result->working_status;
                }
        
                // 労働時間の計算
                Log::DEBUG('当日分　$result->current_calc =  '.$result->current_calc);
                if ($result->current_calc == '1') {     // 当日分である場合
                    // 中抜けは複数ある可能性があるので中抜け計算は戻り時点で計算する。
                    Log::DEBUG('$missing_middle_time = '.$missing_middle_time);
                    if ($result->mode == Config::get('const.C005.missing_middle_time') && $missing_middle_time <> ''){
                        $array_add_missing_middle_time[] = $missing_middle_time;
                        Log::DEBUG('$array_add_missing_middle_time count = '.count($array_add_missing_middle_time));
                    }
                    Log::DEBUG('$missing_middle_return_time = '.$missing_middle_return_time);
                    if ($result->mode == Config::get('const.C005.missing_middle_return_time') && $missing_middle_return_time <> ''){
                        $array_add_missing_middle_return_time[] = $missing_middle_return_time;
                        Log::DEBUG('$array_add_missing_middle_return_time count = '.count($array_add_missing_middle_return_time));
                    }
                    if ($missing_middle_time <> '' && $missing_middle_return_time <> ''){
                        Log::DEBUG('count($array_working_time_kubun) =  '.count($array_working_time_kubun));
                        for ($i=0;$i<count($array_working_time_kubun);$i++) {
                            if ($array_working_time_kubun[$i] <> '') {
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
                                Log::DEBUG('中抜けデータ　$i =  '.$i);
                                Log::DEBUG('中抜けデータ　array_working_time_kubun[$i] =  '.$array_working_time_kubun[$i]);
                                Log::DEBUG('中抜けデータ　array_missing_middle_time[$i] =  '.$array_missing_middle_time[$i]);
                                Log::DEBUG('中抜けデータ　amissing_middle_time =  '.$missing_middle_time);
                                Log::DEBUG('中抜けデータ　missing_middle_return_time =  '.$missing_middle_return_time);
                            }
                        }
                        // 中抜け時刻を初期化して次の計算準備
                        $missing_middle_time = '';
                        $missing_middle_return_time = '';
                    }
                    if ($result->mode == Config::get('const.C005.attendance_time') && $attendance_time <> ''){
                        $array_add_attendance_time[] = $attendance_time;
                    }
                    // 退勤データの場合計算開始
                    if ($result->mode == Config::get('const.C005.leaving_time') && $leaving_time <> ''){
                        $array_add_leaving_time[] = $leaving_time;
                        $index = (int)(Config::get('const.C004.regular_working_time'))-1;
                        for ($i=0;$i<count($array_working_time_kubun);$i++) {
                            if ($array_working_time_kubun[$i] <> '') {
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
                                Log::DEBUG('退勤データ　$i =  '.$i);
                                Log::DEBUG('退勤データ　array_working_time_kubun[$i] =  '.$array_working_time_kubun[$i]);
                                Log::DEBUG('退勤データ　array_calc_time[$i] =  '.$array_calc_time[$i]);
                                Log::DEBUG('退勤データ　attendance_time =  '.$attendance_time);
                                Log::DEBUG('退勤データ　leaving_time =  '.$leaving_time);
                            }
                        }
                        // 出勤退勤時刻を初期化して次の計算準備
                        $attendance_time = '';
                        $leaving_time = '';
                    }
                }
            } elseif ($current_department_code == $before_department_code &&
                        $current_user_code == $before_user_code) {
                // 日付が変わった場合
                Log::DEBUG('date break ');
                try{
                    // ユーザー労働時間登録(１個前のユーザーを登録する)
                    $add_result = $this->addTempWorkingTimeDate(
                        $before_date,
                        $before_user_code,
                        $before_department_code,
                        $before_result,
                        $working_status,
                        $array_calc_time,
                        $array_missing_middle_time,
                        $array_add_attendance_time,
                        $array_add_leaving_time,
                        $array_add_missing_middle_time,
                        $array_add_missing_middle_return_time);
                    // 次データ計算事前処理
                    for ($i=0;$i<count($array_working_time_kubun);$i++) {
                        $array_calc_time[$i] = 0; 
                    }
                    for ($i=0;$i<count($array_working_time_kubun);$i++) {
                        $array_missing_middle_time[$i] = 0; 
                    }
                    if ($result->mode == Config::get('const.C005.attendance_time')) {$attendance_time = $result->record_datetime;}
                    if ($result->mode == Config::get('const.C005.leaving_time')) {$leaving_time = $result->record_datetime;}
                    if ($result->mode == Config::get('const.C005.missing_middle_time')) {$missing_middle_time = $result->record_datetime;}
                    if ($result->mode == Config::get('const.C005.missing_middle_return_time')) {$missing_middle_return_time = $result->record_datetime;}
                    $array_add_attendance_time = array();
                    $array_add_leaving_time = array();
                    $array_add_missing_middle_time = array();
                    $array_add_missing_middle_return_time = array();
                    if ($result->current_calc == '1') {             // 当日分である場合
                        if ($result->mode == Config::get('const.C005.missing_middle_time') && $missing_middle_time <> ''){
                            $array_add_missing_middle_time[] = $missing_middle_time;
                        }
                        if ($result->mode == Config::get('const.C005.missing_middle_return_time') && $missing_middle_return_time <> ''){
                            $array_add_missing_middle_return_time[] = $missing_middle_return_time;
                        }
                        if ($result->mode == Config::get('const.C005.attendance_time') && $attendance_time <> ''){
                            $array_add_attendance_time[] = $attendance_time;
                        }
                        if ($leaving_time <> ''){
                            $array_add_leaving_time[] = $leaving_time;
                        }
                    }
                    // 日付を同じく設定
                    $before_date = $current_date;
                    $before_result = $result;
                    $this->not_employment_working = 0;
                }catch(\PDOException $pe){
                    $add_result = false;
                    throw $pe;
                }catch(\Exception $e){
                    $add_result = false;
                    throw $e;
                }
            } elseif ($current_user_code == $before_user_code)  {
                // 部署が変わった場合
                Log::DEBUG('department break ');
                try{
                    // ユーザー労働時間登録(１個前のユーザーを登録する)
                    $add_result = $this->addTempWorkingTimeDate(
                        $before_date,
                        $before_user_code,
                        $before_department_code,
                        $before_result,
                        $working_status,
                        $array_calc_time,
                        $array_missing_middle_time,
                        $array_add_attendance_time,
                        $array_add_leaving_time,
                        $array_add_missing_middle_time,
                        $array_add_missing_middle_return_time);
                    // 次データ計算事前処理
                    for ($i=0;$i<count($array_working_time_kubun);$i++) {
                        $array_calc_time[$i] = 0; 
                    }
                    for ($i=0;$i<count($array_working_time_kubun);$i++) {
                        $array_missing_middle_time[$i] = 0; 
                    }
                    if ($result->mode == Config::get('const.C005.attendance_time')) {$attendance_time = $result->record_datetime;}
                    if ($result->mode == Config::get('const.C005.leaving_time')) {$leaving_time = $result->record_datetime;}
                    if ($result->mode == Config::get('const.C005.missing_middle_time')) {$missing_middle_time = $result->record_datetime;}
                    if ($result->mode == Config::get('const.C005.missing_middle_return_time')) {$missing_middle_return_time = $result->record_datetime;}
                    $array_add_attendance_time = array();
                    $array_add_leaving_time = array();
                    $array_add_missing_middle_time = array();
                    $array_add_missing_middle_return_time = array();
                    if ($result->current_calc == '1') {             // 当日分である場合
                        if ($result->mode == Config::get('const.C005.missing_middle_time') && $missing_middle_time <> ''){
                            $array_add_missing_middle_time[] = $missing_middle_time;
                        }
                        if ($result->mode == Config::get('const.C005.missing_middle_return_time') && $missing_middle_return_time <> ''){
                            $array_add_missing_middle_return_time[] = $missing_middle_return_time;
                        }
                        if ($result->mode == Config::get('const.C005.attendance_time') && $attendance_time <> ''){
                            $array_add_attendance_time[] = $attendance_time;
                        }
                        if ($leaving_time <> ''){
                            $array_add_leaving_time[] = $leaving_time;
                        }
                    }
                    // 日付を同じく設定
                    $before_date = $current_date;
                    // 部署を同じく設定
                    $before_department_code = $current_department_code;
                    $before_result = $result;
                    $this->not_employment_working = 0;
                }catch(\PDOException $pe){
                    $add_result = false;
                    throw $pe;
                }catch(\Exception $e){
                    $add_result = false;
                    throw $e;
                }
            } else {
                // ユーザーが変わった場合
                Log::DEBUG('calcTempWorkingTimeDate user break ');
                Log::DEBUG('calcTempWorkingTimeDate $result->user_code  '.$result->user_code);
                Log::DEBUG('calcTempWorkingTimeDate $result->mode  '.$result->mode);
                Log::DEBUG('calcTempWorkingTimeDate $result->record_datetime  '.$result->record_datetime);
                Log::DEBUG('calcTempWorkingTimeDate $result->working_timetable_from_time  '.$result->working_timetable_from_time);
                Log::DEBUG('calcTempWorkingTimeDate $result->working_timetable_to_time  '.$result->working_timetable_to_time);
                try{
                    // ユーザー労働時間登録(１個前のユーザーを登録する)
                    $add_result = $this->addTempWorkingTimeDate(
                        $before_date,
                        $before_user_code,
                        $before_department_code,
                        $before_result,
                        $working_status,
                        $array_calc_time,
                        $array_missing_middle_time,
                        $array_add_attendance_time,
                        $array_add_leaving_time,
                        $array_add_missing_middle_time,
                        $array_add_missing_middle_return_time);
                    // 次データ計算事前処理
                    for ($i=0;$i<count($array_working_time_kubun);$i++) {
                        $array_calc_time[$i] = 0; 
                    }
                    for ($i=0;$i<count($array_working_time_kubun);$i++) {
                        $array_missing_middle_time[$i] = 0; 
                    }
                    if ($result->mode == Config::get('const.C005.attendance_time')) {$attendance_time = $result->record_datetime;}
                    if ($result->mode == Config::get('const.C005.leaving_time')) {$leaving_time = $result->record_datetime;}
                    if ($result->mode == Config::get('const.C005.missing_middle_time')) {$missing_middle_time = $result->record_datetime;}
                    if ($result->mode == Config::get('const.C005.missing_middle_return_time')) {$missing_middle_return_time = $result->record_datetime;}
                    $array_add_attendance_time = array();
                    $array_add_leaving_time = array();
                    $array_add_missing_middle_time = array();
                    $array_add_missing_middle_return_time = array();
                    if ($result->current_calc == '1') {             // 当日分である場合
                        if ($result->mode == Config::get('const.C005.missing_middle_time') && $missing_middle_time <> ''){
                            $array_add_missing_middle_time[] = $missing_middle_time;
                        }
                        if ($result->mode == Config::get('const.C005.missing_middle_return_time') && $missing_middle_return_time <> ''){
                            $array_add_missing_middle_return_time[] = $missing_middle_return_time;
                        }
                        if ($result->mode == Config::get('const.C005.attendance_time') && $attendance_time <> ''){
                            $array_add_attendance_time[] = $attendance_time;
                        }
                        if ($leaving_time <> ''){
                            $array_add_leaving_time[] = $leaving_time;
                        }
                    }
                    // 日付を同じく設定
                    $before_date = $current_date;
                    // 部署を同じく設定
                    $before_department_code = $current_department_code;
                    // ユーザーを同じく設定
                    $before_user_code = $current_user_code; 
                    $before_result = $result;
                    $this->not_employment_working = 0;
                }catch(\PDOException $pe){
                    $add_result = false;
                    throw $pe;
                }catch(\Exception $e){
                    $add_result = false;
                    throw $e;
                }
            }
        }

        if (count($array_calc_time) > 0) {
            try{
                $add_result = $this->addTempWorkingTimeDate(
                    $before_date,
                    $before_user_code,
                    $before_department_code,
                    $before_result,
                    $working_status,
                    $array_calc_time,
                    $array_missing_middle_time,
                    $array_add_attendance_time,
                    $array_add_leaving_time,
                    $array_add_missing_middle_time,
                    $array_add_missing_middle_return_time);
            }catch(\PDOException $pe){
                $add_result = false;
            }catch(\Exception $e){
                $add_result = false;
            }
        }

        Log::DEBUG('---------------------- calcTempWorkingTimeDate end ------------------------ ');
        return $add_result;

    }

    /**
     * 労働時間計算
     *
     *
     * @param  $inc              ： 1:出勤・退勤 2:中抜け・中抜け戻り
     * @param  $target_from_time ： 開始時刻（出勤・中抜け）
     * @param  $target_to_time   ： 終了時刻（退勤・中抜け戻り）
     * @return 計算結果時間
     */
    private function calcTimes($inc, $timetables, $working_timetable_no, $working_time_kubun, $current_date,$target_from_time, $target_to_time,
        $array_calc_time、array_missing_middle_time)
    {
        Log::DEBUG('---------------------- calcTimes in ------------------------ ');
        $apicommon = new ApiCommonController();
        $working_times = 0;             // 労働時間

        // 労働時間区分の開始終了時刻を取得
        // タイムテーブルをnoとworking_time_kubunで特定
        $filtered = $timetables->where('no', $working_timetable_no)->where('working_time_kubun', $working_time_kubun);
        foreach($filtered as $result_time) {
            // 時間登録の開始時間
            $from_time = $result_time->from_time;
            // 時間登録の終了時間
            $to_time = $result_time->to_time;
            if (isset($from_time) && isset($to_time)) {
                // 日付付与
                // $from_timeが当日は当日+開始時刻、翌日は翌日+開始時刻
                $working_time_calc_from = $apicommon->convTimeToDate($from_time, $target_from_time, $current_date);         
                // $to_timeが当日は当日+開始時刻、翌日は翌日+開始時刻
                $working_time_calc_to = $apicommon->convTimeToDate($to_time, $target_from_time, $current_date);         
                Log::DEBUG('from_time = '.$from_time);
                Log::DEBUG('to_time = '.$to_time);
                Log::DEBUG('current_date = '.$current_date);
                Log::DEBUG('target_from_time = '.$target_from_time);
                Log::DEBUG('target_to_time = '.$target_to_time);
                Log::DEBUG('working_time_calc_from = '.$working_time_calc_from);
                Log::DEBUG('working_time_calc_to = '.$working_time_calc_to);
                if ($target_from_time > $working_time_calc_from) {
                    $working_time_calc_from = $target_from_time;
                    Log::DEBUG('working_time_calc_from if then= '.$working_time_calc_from);
                }
                if ($target_to_time < $working_time_calc_to) {
                    $working_time_calc_to = $target_to_time;
                    Log::DEBUG('working_time_calc_to if then= '.$working_time_calc_to);
                }
                Log::DEBUG('working_time_kubun = '.$working_time_kubun);
                Log::DEBUG('target_from_time = '.$target_from_time);
                Log::DEBUG('target_to_time = '.$target_to_time);
                Log::DEBUG('working_time_calc_from = '.$working_time_calc_from);
                Log::DEBUG('working_time_calc_to = '.$working_time_calc_to);
                Log::DEBUG('inc = '.$inc);
                // 深夜労働残業時間以外の場合
                if ($working_time_kubun != Config::get('const.C004.out_of_regular_night_working_time') || $inc == Config::get('const.INC_NO.missing_return')) {
                    if ($working_time_calc_from < $working_time_calc_to) {
                        $calc_times = $apicommon->diffTimeSerial($working_time_calc_from, $working_time_calc_to);
                        Log::DEBUG('calc_times = '.$calc_times);
                        $working_times += $calc_times;
                        Log::DEBUG('$working_times = '.$working_times);
                    }
                } else {
                    // 深夜労働残業時間
                    Log::DEBUG('【深夜労働残業時間 計算開始】');
                    $w_time = 0;
                    // target_to_timeは退勤時刻
                    Log::DEBUG('$target_to_time = '.$target_to_time);
                    // ここまでに計算された労働時間と中抜け時間から
                    // $array_calc_time、array_missing_middle_time
                    // 休憩とか中抜けとかも考慮しなければ正確な8Hを計算できない
                    // 退勤時刻 > 深夜残業開始の場合、出勤->深夜残業開始の時間が8Hを超えている場合は深夜残業を計算する
                    /*if ($attendance_time > Config::get('const.C002.legal_working_hours_day') * 60 * 60) {
                        // 出勤時刻の8時間後が深夜労働時間帯が含まれる場合
                        if ($attendance_8after > $working_time_calc_from) {     // 同じ時刻はなし
                            // 退勤時刻 >= 出勤時刻の8時間後
                            Log::DEBUG('$target_to_time = '.$target_to_time);
                            if ($target_to_time >= $attendance_8after) {
                                Log::DEBUG('$working_time_calc_to = '.$working_time_calc_to);
                                // 退勤時刻<=深夜残業終了
                                if ($target_to_time <= $working_time_calc_to) {
                                    // 退勤時刻-出勤時刻の8時間後を深夜残業とする
                                    $calc_times = $apicommon->diffTimeSerial($target_to_time, $attendance_8after);
                                    Log::DEBUG('$calc_times = '.$calc_times);
                                } else {
                                    // 出勤時刻の8時間後-深夜残業終了を深夜残業とする
                                    $calc_times = $apicommon->diffTimeSerial($attendance_8after, $working_time_calc_to);
                                    Log::DEBUG('$calc_times = '.$calc_times);
                                }
                                $working_times += $calc_times;
                                Log::DEBUG('$working_times = '.$working_times);
                            }
                        }
                    } */
                }
                // 休憩時間を含んでいる場合、休憩時間累計（所定労働時間内の休憩時間を累計することになる）
                $filtered = $timetables->where('no', $working_timetable_no)
                    ->where('working_time_kubun', Config::get('const.C004.regular_working_breaks_time'));
                // 休憩時間帯は複数あるかも
                if ($this->not_employment_working == 0) {
                    foreach($filtered as $result_breaks_time) {
                        $from_time = $result_breaks_time->from_time;
                        $to_time = $result_breaks_time->to_time;
                        Log::DEBUG('休憩時間 from_time = '.$from_time);
                        Log::DEBUG('休憩時間 to_time = '.$to_time);
                        if (isset($from_time) && isset($to_time)) {
                            $dt = new Carbon($working_time_calc_from);
                            $dttime1 = date_format($dt, 'H:i:s');
                            Log::DEBUG('休憩時間 dttime1 = '.$dttime1);
                            // $from_timeが当日は当日+開始時刻、翌日は翌日+開始時刻
                            $dt = new Carbon($from_time);
                            $dttime2 = date_format($dt, 'H:i:s');
                            Log::DEBUG('休憩時間 dttime2 = '.$dttime2);
                            $time_calc_from = $apicommon->convTimeToDate($dttime2, $dttime1, $current_date);         
                            // $to_timeが当日は当日+開始時刻、翌日は翌日+開始時刻
                            $dt = new Carbon($to_time);
                            $dttime2 = date_format($dt, 'H:i:s');
                            Log::DEBUG('休憩時間 dttime2 = '.$dttime2);
                            $time_calc_to = $apicommon->convTimeToDate($dttime2, $dttime1, $current_date);         
                            Log::DEBUG('休憩時間 time_calc_from = '.$time_calc_from);
                            Log::DEBUG('休憩時間 time_calc_to = '.$time_calc_to);
                            Log::DEBUG('休憩時間 target_from_time = '.$target_from_time);
                            Log::DEBUG('休憩時間 target_to_time = '.$target_to_time);
                            if ($time_calc_from >= $target_from_time && $time_calc_to <= $target_to_time) {
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
                                    $this->not_employment_working += $calc_times;
                                }
                            }
                        }
                    }
                }
            }
        }
        Log::DEBUG('calcTimes end '.$working_times);
        Log::DEBUG('休憩時間 not_employment_working = '.$this->not_employment_working);
        Log::DEBUG('---------------------- calcTimes end ------------------------ ');

        return $working_times;
    }

    /**
     * ユーザー労働時間登録（temp）
     *
     * @return 登録結果
     */
    private function addTempWorkingTimeDate($target_date, $target_user_code, $target_department_code, $target_result, $working_status,
        $array_calc_time, $array_missing_middle_time,
        $array_add_attendance_time, $array_add_leaving_time, $array_add_missing_middle_time, $array_add_missing_middle_return_time)
    {
        Log::DEBUG('---------------------- addTempWorkingTimeDate in ------------------------ ');
        $temp_working_model = new TempWorkingTimeDate();
        $apicommon = new ApiCommonController();
        $outside_calc_time = 0;     // 所定外労働時間
        $onday_basic_time_serial = Config::get('const.C002.legal_working_hours_day')  * 60 * 60;        // １日の労働時間基準(=8H)

        $temp_working_model->setWorkingdateAttribute(date_format(new Carbon($target_date), 'Ymd'));
        $temp_working_model->setEmploymentstatusAttribute($target_result->employment_status);
        $temp_working_model->setDepartmentcodeAttribute($target_department_code);
        $temp_working_model->setUsercodeAttribute($target_user_code);
        $temp_working_model->setEmploymentstatusnameAttribute($target_result->employment_status_name);
        $temp_working_model->setDepartmentnameAttribute($target_result->department_name);
        $temp_working_model->setUsernameAttribute($target_result->user_name);
        $temp_working_model->setWorkingtimetablenoAttribute($target_result->working_timetable_no);
        $temp_working_model->setWorkingtimetablenameAttribute($target_result->working_timetable_name);
        Log::DEBUG('count($array_calc_time = '.count($array_calc_time));
        for ($i=0;$i<count($array_calc_time)-1;$i++) {
            if (count($array_calc_time) > 0 && $i < count($array_calc_time)){
                Log::DEBUG('$array_calc_time[$i] = '.$array_calc_time[$i].' $i = '.$i);
            } else {
                Log::DEBUG('$array_calc_time[$i] = null $i = '.$i);
            }
        }
        $index = (int)(Config::get('const.ARRAY_MAX_INDEX.attendace_time'));
        Log::DEBUG('$index = '.$index);
        Log::DEBUG('count($array_add_attendance_time = '.count($array_add_attendance_time));
        for ($i=0;$i<$index;$i++) {
            if (count($array_add_attendance_time) > 0 && $i < count($array_add_attendance_time)){
                Log::DEBUG('$array_add_attendance_time[$i] = '.$array_add_attendance_time[$i].' $i = '.$i);
                $temp_working_model->setAttendancetimeAttribute($i, $array_add_attendance_time[$i]);
            } else {
                Log::DEBUG('$array_add_attendance_time[$i] = null $i = '.$i);
                $temp_working_model->setAttendancetimeAttribute($i, null);
            }
        }
        $index = (int)(Config::get('const.ARRAY_MAX_INDEX.leaving_time'));
        for ($i=0;$i<$index;$i++) {
            if (count($array_add_leaving_time) > 0 && $i < count($array_add_leaving_time)){
                $temp_working_model->setLeavingtimeAttribute($i, $array_add_leaving_time[$i]);
            } else {
                $temp_working_model->setLeavingtimeAttribute($i, null);
            }
        }
        $index = (int)(Config::get('const.ARRAY_MAX_INDEX.missing_middle_time'));
        Log::DEBUG('$index = '.$index);
        Log::DEBUG('count($array_add_missing_middle_time = '.count($array_add_missing_middle_time));
        for ($i=0;$i<$index;$i++) {
            if (count($array_add_missing_middle_time) > 0 && $i < count($array_add_missing_middle_time)){
                Log::DEBUG('$array_add_missing_middle_time[$i] = '.$array_add_missing_middle_time[$i].' $i = '.$i);
                $temp_working_model->setMissingmiddletimeAttribute($i, $array_add_missing_middle_time[$i]);
            } else {
                $temp_working_model->setMissingmiddletimeAttribute($i, null);
            }
        }
        $index = (int)(Config::get('const.ARRAY_MAX_INDEX.missing_middle_return_time'));
        for ($i=0;$i<$index;$i++) {
            if (count($array_add_missing_middle_return_time) > 0 && $i < count($array_add_missing_middle_return_time)){
                $temp_working_model->setMissingmiddlereturntimeAttribute($i, $array_add_missing_middle_return_time[$i]);
            } else {
                $temp_working_model->setMissingmiddlereturntimeAttribute($i, null);
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
        $temp_working_model->setRegularworkingtimesAttribute($regular_calc_time);
        $total_time = $total_time + $regular_calc_time;
        Log::DEBUG('$target_user_code = '.$target_user_code.' 所定労働時間 = $total_time + $regular_calc_time '.$total_time.' '.$regular_calc_time);
        // 時間外労働時間
        $index = (int)(Config::get('const.C004.out_of_regular_working_time'))-1;
        $w_time = $array_calc_time[$index] - $array_missing_middle_time[$index];
        Log::DEBUG('$target_user_code = '.$target_user_code.' 時間外労働時間 = $array + $array '.$array_calc_time[$index].' '.$array_missing_middle_time[$index]);
        $calc_time = round($apicommon->roundTime($w_time, $target_result->time_unit, $target_result->time_rounding) / 60,2);
        $temp_working_model->setOffhoursworkinghoursAttribute($calc_time);
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
        $overtime_hours = $overtime_hours + $calc_time;
        Log::DEBUG('$target_user_code = '.$target_user_code.' 残業時間 = $overtime_hours + $calc_time '.$overtime_hours.' '.$calc_time);
        // 合計勤務時間
        $temp_working_model->setTotalworkingtimesAttribute($total_time);
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
        // 未就労時間（休憩時間＋中抜け時間）
        // 休憩時間
        $calc_time = 0;
        if ($regular_calc_time > 0) {
            $calc_time +=  $this->not_employment_working;
            Log::DEBUG('未就労時間 休憩時間 = '.$this->not_employment_working);
        }
        // 中抜け時間
        for ($i=0;$i<count($array_missing_middle_time);$i++) {
            $calc_time += $array_missing_middle_time[$i];
            Log::DEBUG('未就労時間 中抜け時間 = '.$array_missing_middle_time[$i]);
        }
        $calc_time = round($apicommon->roundTime($calc_time, $target_result->time_unit, $target_result->time_rounding) / 60,2);
        Log::DEBUG('$target_user_code = '.$target_user_code.' 未就労時間（休憩時間＋中抜け時間） =  '. $calc_time);
        $temp_working_model->setNotemploymentworkinghoursAttribute($calc_time);
        $temp_working_model->setWorkingstatusAttribute($working_status);
        $temp_working_model->setNoteAttribute($target_result->note);
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
        $temp_working_model->setWorkingintervalAttribute($target_result->working_interval);
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

}