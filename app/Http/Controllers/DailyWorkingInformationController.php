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
use App\TempCalcWorkingTime;
use App\Http\Controller\ApiCommonController;


class DailyWorkingInformationController extends Controller
{

    // 打刻データ配列
    private $array_working_mode = null;
    private $array_working_time = null;
    private $array_timetable_from_time = null;
    private $array_timetable_to_time = null;
    private $array_interval = null;
    // 計算用配列
    private $array_calc_mode = null;
    private $array_calc_time = null;
    private $array_calc_status = null;
    private $array_calc_note = null;
    private $array_calc_late = null;
    private $array_calc_Leave_early = null;
    private $array_calc_interval = null;
    private $array_calc_calc = null;
    private $array_calc_to_be_confirmed = null;

    private $array_massegedata = array();

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

        $calc_results = null;
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
        $departmentcode = null;
        if(isset($request->departmentcode)){
            $departmentcode = $request->departmentcode;
        }
        $usercode = null;
        if(isset($request->usercode)){
            $usercode = $request->usercode;
        }
        // 打刻時刻を取得
        $work_time = new WorkTime();
        $work_time->setParamDatefromAttribute($datefrom);
        $work_time->setParamDatetoAttribute($dateto);
        $work_time->setParamemploymentstatusAttribute($employment_status);
        $work_time->setParamDepartmentcodeAttribute($departmentcode);
        $work_time->setParamUsercodeAttribute($usercode);
        $chk_result = $work_time->chkWorkingTimeData();
        if ($chk_result) {
            $work_time_result = $work_time->getWorkTimes();
            if(isset($work_time_result)){
                // 日次集計計算登録
                DB::beginTransaction();
                try{
                    // ユーザーの出勤・退勤・中抜・戻り時刻の確定処理
                    $put_results = $this->calcWorkingTimeDates($work_time_result);
                    DB::commit();
                }catch(\PDOException $e){
                    DB::rollBack();
                    array_push($this->array_massegedata, Config::get('const.MSG_ERROR.data_accesee_eror_dailycalc'));
                    Log::error(Config::get('const.MSG_ERROR.data_accesee_eror_dailycalc'));
                }catch(\Exception $e){
                    DB::rollBack();
                    array_push($this->array_massegedata, Config::get('const.MSG_ERROR.data_eror_dailycalc'));
                    Log::error(Config::get('const.MSG_ERROR.data_eror_dailycalc'));
                }
                // 日次集計取得
                /*$working_timedate = new WorkingTimedate();
                $working_timedate->setParamDepartmentcodeAttribute($departmentcode);
                $working_timedate->setParamUsercodeAttribute($usercode);
                $working_timedate->setParamDatefromAttribute($datefrom);
                $working_timedate->setParamDatetoAttribute($dateto);
                $working_timedate->setArrayrecordtimeAttribute($datefrom, $dateto); */
                //$calc_results = $working_timedate->getWorkingTimeDates();
            } else {
                $calc_results = null;
                array_push($this->array_massegedata, Config::get('const.MSG_ERROR.not_workintime'));
            }
        } else {
            $calc_results = null;
            array_push($this->array_massegedata, $work_time->getMassegedataAttribute());
        }
        return response()->json(['calc_results' => $calc_results, 'massegedata' => $this->array_massegedata]);
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
    private function calcWorkingTimeDates($worktimes){

        $working_timedate = new WorkingTimedate();
        $current_date = null;
        $current_department_id = null;
        $current_user_code = null;
        $current_result = null;
        $before_date = null;
        $before_user_code = null;
        $before_department_id = null;
        $before_result = null;
        // 打刻データ配列の初期化
        $this->iniArrayWorkingTime();
        // temporary削除処理
        $temp_model = new TempCalcWorkingTime();
        $temp_model->delTempCalcWorkingtimes();
        // ユーザー単位処理
        foreach ($worktimes as $result) {
            // 現在の情報保存
            $current_date = $result->record_date;
            $current_department_id = $result->department_id;
            $current_user_code = $result->user_code;
            $current_result = $result;
            if ($before_date == null) {$before_date = $current_date;}
            if ($before_department_id == null) {$before_department_id = $current_department_id;}
            if ($before_user_code == null) {$before_user_code = $current_user_code;}
            if ($before_result == null) {$before_result = $result;}
            // 設定値確認
            $chk_result = $this->chkSettingData($result);
            // 設定が正常である場合
            if ($chk_result)  {
                // 同じキーの場合
                if ($current_date == $before_date &&
                    $current_department_id == $before_department_id &&
                    $current_user_code == $before_user_code) {
                    // 打刻データ配列の設定
                    $this->pushArrayWorkingTime($result);
                } elseif ($current_department_id == $before_department_id &&
                            $current_user_code == $before_user_code) {
                    // 日付が変わった場合
                    Log::DEBUG('date break ');
                    // ユーザー労働時間計算(１個前のユーザーを計算する)
                    $this->calcWorkingTime(
                        $before_date,
                        $before_user_code,
                        $before_department_id);
                    // temporaryに登録する
                    $temp_model->insTempCalcItem($before_date, $before_result);
                    // 次データ計算事前処理
                    $this->beforeArrayWorkingTime($result);
                    // 日付を同じく設定
                    $before_date = $current_date;
                } elseif ($current_user_code == $before_user_code)  {
                    // 部署が変わった場合
                    Log::DEBUG('department break ');
                    // ユーザー労働時間計算(１個前のユーザーを計算する)
                    $this->calcWorkingTime(
                        $before_date,
                        $before_user_code,
                        $before_department_id);
                    // temporaryに登録する
                    $temp_model->insTempCalcItem($before_date, $before_result);
                    // 次データ計算事前処理
                    $this->beforeArrayWorkingTime($result);
                    // 部署を同じく設定
                    $before_department_id = $current_department_id;
                } else {
                    // ユーザーが変わった場合
                    Log::DEBUG('user break ');
                    // ユーザー労働時間計算(１個前のユーザーを計算する)
                    $this->calcWorkingTime(
                        $before_date,
                        $before_user_code,
                        $before_department_id);
                    // temporaryに登録する
                    $temp_model->insTempCalcItem($before_date, $before_result);
                    // 次データ計算事前処理
                    $this->beforeArrayWorkingTime($result);
                    // ユーザーを同じく設定
                    $before_user_code = $current_user_code;
                }
            } else {
                array_push($this->array_massegedata, $work_time->getMassegedataAttribute());
                Log::DEBUG('calcWorkingTimeDates error '.$work_time->getMassegedataAttribute());
            }
        }

        if (count($this->array_working_mode) > 0) {
            // ユーザー労働時間計算(currentユーザーを計算する)
            $this->calcWorkingTime(
                $current_date,
                $current_user_code,
                $current_department_id);
            // temporaryに登録する
            $temp_model->insTempCalcItem($current_date, $current_result);
        }

        return true;

    }

    /**
     * ユーザー労働時間計算
     *
     * @return 労働時間計算結果
     */
    private function calcWorkingTime($target_date, $target_user_code, $target_department_id)
    {
        Log::DEBUG('calcWorkingTime  in '.$target_date);
        $work_time = new WorkTime();
        $work_time->setParamDepartmentcodeAttribute($target_department_id);
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
        // 前提 count($array_working_mode) = count($array_working_time)
        Log::DEBUG('array_working_mode count = '.count($this->array_working_mode));
        for($i=0;$i<count($this->array_working_mode);$i++){
            $value_mode = $this->array_working_mode[$i];
            $value_record_datetime = $this->array_working_time[$i];
            $value_timetable_from_time = $this->array_timetable_from_time[$i];
            $value_timetable_to_time = $this->array_timetable_to_time[$i];
            $value_interval = $this->array_interval[$i];
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

        Log::DEBUG('calcWorkingTime end ');
       
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

        $attendance_from_date = new Carbon($work_time->getParamDatefromAttribute());        // 出勤1日のはじめ
        $attendance_from_date_format = date_format($attendance_from_date, 'Y/m/d');
        $timetable_from_date = new Carbon(
            $attendance_from_date_format.' '.$value_timetable_from_time);                   // タイムテーブルの始業時刻
        $timetable_to_date = new Carbon(
            $attendance_from_date_format.' '.$value_timetable_to_time);                     // タイムテーブルの終業時刻
        $attendance_to_date = new Carbon(
            $attendance_from_date_format.' 23:59:59');                                      // 出勤1日の終わり
        $record_datetime = new Carbon(
            $attendance_from_date_format.' '.$value_record_datetime);                       // 打刻日付時刻
        $record_before_datetime = new Carbon($before_value_datetime);                       // １個前の打刻時刻
        Log::DEBUG('attendance_from_date set = '.$attendance_from_date);
        Log::DEBUG('timetable_from_date set = '.$timetable_from_date);
        Log::DEBUG('timetable_to_date set = '.$timetable_to_date);
        Log::DEBUG('attendance_to_date set = '.$attendance_to_date);
        Log::DEBUG('record_datetime set = '.$record_datetime);
        Log::DEBUG('record_before_datetime set = '.$record_before_datetime);
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
                if ($record_before_datetime < $attendance_from_date) {                      // １個前の打刻時刻 < 出勤1日のはじめ
                    // パターン２（打刻ミス（出勤済）（要確認）。勤務状態は打刻なし）
                    $ptn = '2';
                }
            } elseif ($before_value_mode == Config::get('const.C005.leaving_time')) {       // １個前のモードが退勤である場合
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
                if ($record_before_datetime < $attendance_from_date) {                      // １個前の打刻時刻 < 出勤1日のはじめ
                    // パターン５（打刻ミス（中抜け戻りしていない）。勤務状態は打刻なし）
                    $ptn = '5';
                }
            } elseif ($before_value_mode == Config::get('const.C005.missing_middle_return_time')) {     // １個前のモードが戻り
                if ($record_before_datetime < $attendance_from_date) {                      // １個前の打刻時刻 < 出勤1日のはじめ
                    // パターン６（打刻ミス（退勤していない）。勤務状態は打刻なし）
                    $ptn = '6';
                }
            } else {                                                                        // １個前のモードがない
                if ($record_datetime >= $attendance_from_date &&
                            $record_datetime < $timetable_from_date) {                      // 出勤1日のはじめ <= 打刻時刻 < タイムテーブルの始業時刻
                    if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                        // パターン１（正常出勤。勤務状態は出勤状態）
                        $ptn = '1';
                    }
                } elseif ($record_datetime >= $timetable_from_date &&
                            $record_datetime < $timetable_to_date) {                        // タイムテーブルの始業時刻 <= 打刻時刻 < タイムテーブルの終業時刻
                    if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                        // パターン３（遅刻出勤（遅刻=1）。勤務状態は出勤状態）
                        $ptn = '3';
                    }
                } elseif ($record_datetime >= $timetable_to_date &&
                            $record_datetime < $attendance_to_date) {                       // タイムテーブルの終業時刻 <= 打刻時刻 < 出勤1日の終わり
                    if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                        // パターン４（遅刻出勤（要確認）。勤務状態は出勤状態）
                        $ptn = '4';
                    }
                } elseif ($record_datetime > $attendance_to_date) {                         // 出勤1日の終わり < 打刻時刻
                    if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                        // 不明データ
                        // 出勤打刻時刻が出勤1日の終わりより大きいことはない
                        $log_data = $work_time->getParamDatefromAttribute();
                        $log_data .= $work_time->getParamUsercodeAttribute();
                        $log_data .= $work_time->getParamDepartmentcodeAttribute();
                        Log::error(Config::get('const.MSG_ERROR.mismatch_data').' method = setAttendancetime3 '.$log_data);
                    }
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

        $temp_model = new TempCalcWorkingTime();
        if ($ptn != null) {
            $temp_model = $this->setAttendanceCollectPtn($ptn, $record_datetime,$chk_interval);
            $this->pushArrayCalc($temp_model);
        } else {
            // 不明データとして作成する
            $temp_model = $this->setAttendanceCollectPtn('', $record_datetime,$chk_interval);
            $this->pushArrayCalc($temp_model);
        }
            
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
        $temp_model = new TempCalcWorkingTime();

        if ($ptn == '1') {
            $temp_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_model->setRecorddatetimeAttribute($record_datetime);
            $temp_model->setWorkingstatusAttribute(Config::get('const.C012.attendance'));
            $temp_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_model->setLateAttribute('0');
            $temp_model->setLeaveearlyAttribute('0');
            $temp_model->setWorkingintervalAttribute($chk_interval);
            $temp_model->setCurrentcalcAttribute('0');
            $temp_model->setTobeconfirmedAttribute('0');
        } elseif ($ptn == '2') {
            $temp_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_model->setRecorddatetimeAttribute($record_datetime);
            $temp_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_001'));
            $temp_model->setLateAttribute('0');
            $temp_model->setLeaveearlyAttribute('0');
            $temp_model->setWorkingintervalAttribute($chk_interval);
            $temp_model->setCurrentcalcAttribute('0');
            $temp_model->setTobeconfirmedAttribute('1');
        } elseif ($ptn == '3') {
            $temp_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_model->setRecorddatetimeAttribute($record_datetime);
            $temp_model->setWorkingstatusAttribute(Config::get('const.C012.attendance'));
            $temp_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_model->setLateAttribute('1');
            $temp_model->setLeaveearlyAttribute('0');
            $temp_model->setWorkingintervalAttribute($chk_interval);
            $temp_model->setCurrentcalcAttribute('0');
            $temp_model->setTobeconfirmedAttribute('0');
        } elseif ($ptn == '4') {
            $temp_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_model->setRecorddatetimeAttribute($record_datetime);
            $temp_model->setWorkingstatusAttribute(Config::get('const.C012.attendance'));
            $temp_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_002'));
            $temp_model->setLateAttribute('1');
            $temp_model->setLeaveearlyAttribute('0');
            $temp_model->setWorkingintervalAttribute($chk_interval);
            $temp_model->setCurrentcalcAttribute('0');
            $temp_model->setTobeconfirmedAttribute('1');
        } elseif ($ptn == '5') {
            $temp_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_model->setRecorddatetimeAttribute($record_datetime);
            $temp_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_003'));
            $temp_model->setLateAttribute('0');
            $temp_model->setLeaveearlyAttribute('0');
            $temp_model->setWorkingintervalAttribute($chk_interval);
            $temp_model->setCurrentcalcAttribute('0');
            $temp_model->setTobeconfirmedAttribute('1');
        } elseif ($ptn == '6') {
            $temp_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_model->setRecorddatetimeAttribute($record_datetime);
            $temp_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_001'));
            $temp_model->setLateAttribute('0');
            $temp_model->setLeaveearlyAttribute('0');
            $temp_model->setWorkingintervalAttribute($chk_interval);
            $temp_model->setCurrentcalcAttribute('0');
            $temp_model->setTobeconfirmedAttribute('1');
        } else {
            // 不明データとして作成する
            $temp_model->setModeAttribute(Config::get('const.C005.attendance_time'));
            $temp_model->setRecorddatetimeAttribute($record_datetime);
            $temp_model->setWorkingstatusAttribute(Config::get('const.C012.unknown'));
            $temp_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_004'));
            $temp_model->setLateAttribute('0');
            $temp_model->setLeaveearlyAttribute('0');
            $temp_model->setWorkingintervalAttribute($chk_interval);
            $temp_model->setCurrentcalcAttribute('0');
            $temp_model->setTobeconfirmedAttribute('1');
        }

        return $temp_model;
            
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
        $attendance_from_date = new Carbon($work_time->getParamDatefromAttribute());        // 出勤1日のはじめ
        $attendance_from_date_format = date_format($attendance_from_date, 'Y/m/d');
        $timetable_from_date = new Carbon(
            $attendance_from_date_format.' '.$value_timetable_from_time);                   // タイムテーブルの始業時刻
        $timetable_to_date = new Carbon(
            $attendance_from_date_format.' '.$value_timetable_to_time);                     // タイムテーブルの終業時刻
        $attendance_to_date = new Carbon(
            $attendance_from_date_format.' 23:59:59');                                      // 出勤1日の終わり
        $record_datetime = new Carbon(
            $attendance_from_date_format.' '.$value_record_datetime);                       // 打刻日付時刻
        $record_before_datetime = new Carbon($before_value_datetime);                       // １個前の打刻時刻
        Log::DEBUG('attendance_from_date set = '.$attendance_from_date);
        Log::DEBUG('timetable_from_date set = '.$timetable_from_date);
        Log::DEBUG('timetable_to_date set = '.$timetable_to_date);
        Log::DEBUG('attendance_to_date set = '.$attendance_to_date);
        Log::DEBUG('record_datetime set = '.$record_datetime);
        Log::DEBUG('record_before_datetime set = '.$record_before_datetime);
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
                if ($record_before_datetime < $attendance_from_date) {                      // １個前の打刻時刻 < 出勤1日のはじめ
                    // パターン６（打刻ミス（出勤していない）。勤務状態は打刻なし）
                    $ptn = '6';
                }
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
                            for($i=cnt-2;$i<count($array_working_mode);$i--){
                                if ($array_working_mode[$i] == Config::get('const.C005.attendance_time')) {     // ２個前モードが出勤
                                    if ($$array_working_time[$i] < $attendance_from_date) {     // ２個前の出勤時刻 < 出勤1日のはじめ
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
                            for($i=cnt-2;$i<count($array_working_mode);$i--){
                                if ($array_working_mode[$i] == Config::get('const.C005.attendance_time')) {     // ２個前モードが出勤
                                    if ($$array_working_time[$i] < $attendance_from_date) {     // ２個前の出勤時刻 < 出勤1日のはじめ
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
                            for($i=cnt-2;$i<count($array_working_mode);$i--){
                                if ($array_working_mode[$i] == Config::get('const.C005.attendance_time')) {     // ２個前モードが出勤
                                    if ($$array_working_time[$i] < $attendance_from_date) {     // ２個前の出勤時刻 < 出勤1日のはじめ
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
                        for($i=cnt-2;$i<count($array_working_mode);$i--){
                            if ($array_working_mode[$i] == Config::get('const.C005.attendance_time')) {     // ２個前モードが出勤
                                if ($$array_working_time[$i] < $attendance_from_date) {     // ２個前の出勤時刻 < 出勤1日のはじめ
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
        $temp_model = new TempCalcWorkingTime();
        $chk_interval = '0';

        if ($ptn == '1') {
            $temp_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_model->setRecorddatetimeAttribute($record_datetime);
            $temp_model->setWorkingstatusAttribute(Config::get('const.C012.leaving'));
            $temp_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_005'));
            $temp_model->setLateAttribute('0');
            $temp_model->setLeaveearlyAttribute('0');
            $temp_model->setWorkingintervalAttribute($chk_interval);
            $temp_model->setCurrentcalcAttribute('0');
            $temp_model->setTobeconfirmedAttribute('0');
        } elseif ($ptn == '2') {
            $temp_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_model->setRecorddatetimeAttribute($record_datetime);
            $temp_model->setWorkingstatusAttribute(Config::get('const.C012.leaving'));
            $temp_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_005'));
            $temp_model->setLateAttribute('0');
            $temp_model->setLeaveearlyAttribute('0');
            $temp_model->setWorkingintervalAttribute($chk_interval);
            $temp_model->setCurrentcalcAttribute('0');
            $temp_model->setTobeconfirmedAttribute('1');
        } elseif ($ptn == '3') {
            $temp_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_model->setRecorddatetimeAttribute($record_datetime);
            $temp_model->setWorkingstatusAttribute(Config::get('const.C012.attendance'));
            $temp_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_006'));
            $temp_model->setLateAttribute('0');
            $temp_model->setLeaveearlyAttribute('0');
            $temp_model->setWorkingintervalAttribute($chk_interval);
            $temp_model->setCurrentcalcAttribute('0');
            $temp_model->setTobeconfirmedAttribute('1');
        } elseif ($ptn == '4') {
            $temp_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_model->setRecorddatetimeAttribute($record_datetime);
            $temp_model->setWorkingstatusAttribute(Config::get('const.C012.leaving'));
            $temp_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_006'));
            $temp_model->setLateAttribute('0');
            $temp_model->setLeaveearlyAttribute('1');
            $temp_model->setWorkingintervalAttribute($chk_interval);
            $temp_model->setCurrentcalcAttribute('1');
            $temp_model->setTobeconfirmedAttribute('1');
        } elseif ($ptn == '5') {
            $temp_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_model->setRecorddatetimeAttribute($record_datetime);
            $temp_model->setWorkingstatusAttribute(Config::get('const.C012.leaving'));
            $temp_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_006'));
            $temp_model->setLateAttribute('0');
            $temp_model->setLeaveearlyAttribute('0');
            $temp_model->setWorkingintervalAttribute($chk_interval);
            $temp_model->setCurrentcalcAttribute('1');
            $temp_model->setTobeconfirmedAttribute('1');
        } elseif ($ptn == '6') {
            $temp_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_model->setRecorddatetimeAttribute($record_datetime);
            $temp_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_002'));
            $temp_model->setLateAttribute('0');
            $temp_model->setLeaveearlyAttribute('0');
            $temp_model->setWorkingintervalAttribute($chk_interval);
            $temp_model->setCurrentcalcAttribute('0');
            $temp_model->setTobeconfirmedAttribute('1');
        } elseif ($ptn == '7') {
            $temp_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_model->setRecorddatetimeAttribute($record_datetime);
            $temp_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_003'));
            $temp_model->setLateAttribute('0');
            $temp_model->setLeaveearlyAttribute('0');
            $temp_model->setWorkingintervalAttribute($chk_interval);
            $temp_model->setCurrentcalcAttribute('0');
            $temp_model->setTobeconfirmedAttribute('1');
        } elseif ($ptn == '8') {
            $temp_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_model->setRecorddatetimeAttribute($record_datetime);
            $temp_model->setWorkingstatusAttribute(Config::get('const.C012.leaving'));
            $temp_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_model->setLateAttribute('0');
            $temp_model->setLeaveearlyAttribute('0');
            $temp_model->setWorkingintervalAttribute($chk_interval);
            $temp_model->setCurrentcalcAttribute('1');
            $temp_model->setTobeconfirmedAttribute('0');
        } else {
            // 不明データとして作成する
            $temp_model->setModeAttribute(Config::get('const.C005.leaving_time'));
            $temp_model->setRecorddatetimeAttribute($record_datetime);
            $temp_model->setWorkingstatusAttribute(Config::get('const.C012.unknown'));
            $temp_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_004'));
            $temp_model->setLateAttribute('0');
            $temp_model->setLeaveearlyAttribute('0');
            $temp_model->setWorkingintervalAttribute($chk_interval);
            $temp_model->setCurrentcalcAttribute('0');
            $temp_model->setTobeconfirmedAttribute('1');
        }
            
        return $temp_model;
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
        $attendance_from_date = new Carbon($work_time->getParamDatefromAttribute());        // 出勤1日のはじめ
        $attendance_from_date_format = date_format($attendance_from_date, 'Y/m/d');
        $timetable_from_date = new Carbon(
            $attendance_from_date_format.' '.$value_timetable_from_time);                   // タイムテーブルの始業時刻
        $timetable_to_date = new Carbon(
            $attendance_from_date_format.' '.$value_timetable_to_time);                     // タイムテーブルの終業時刻
        $attendance_to_date = new Carbon(
            $attendance_from_date_format.' 23:59:59');                                      // 出勤1日の終わり
        $record_datetime = new Carbon(
            $attendance_from_date_format.' '.$value_record_datetime);                       // 打刻日付時刻
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
        $temp_model = new TempCalcWorkingTime();
        $chk_interval = '0';

        if ($ptn == '1') {
            $temp_model->setModeAttribute(Config::get('const.C005.missing_middle_time'));
            $temp_model->setRecorddatetimeAttribute($record_datetime);
            $temp_model->setWorkingstatusAttribute(Config::get('const.C012.missing_middle'));
            $temp_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_model->setLateAttribute('0');
            $temp_model->setLeaveearlyAttribute('0');
            $temp_model->setWorkingintervalAttribute($chk_interval);
            $temp_model->setCurrentcalcAttribute('0');
            $temp_model->setTobeconfirmedAttribute('0');
        } elseif ($ptn == '2') {
            $temp_model->setModeAttribute(Config::get('const.C005.missing_middle_time'));
            $temp_model->setRecorddatetimeAttribute($record_datetime);
            $temp_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_008'));
            $temp_model->setLateAttribute('0');
            $temp_model->setLeaveearlyAttribute('0');
            $temp_model->setWorkingintervalAttribute($chk_interval);
            $temp_model->setCurrentcalcAttribute('0');
            $temp_model->setTobeconfirmedAttribute('1');
        } elseif ($ptn == '3') {
            $temp_model->setModeAttribute(Config::get('const.C005.missing_middle_time'));
            $temp_model->setRecorddatetimeAttribute($record_datetime);
            $temp_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_003'));
            $temp_model->setLateAttribute('0');
            $temp_model->setLeaveearlyAttribute('0');
            $temp_model->setWorkingintervalAttribute($chk_interval);
            $temp_model->setCurrentcalcAttribute('0');
            $temp_model->setTobeconfirmedAttribute('1');
        } else {
            // 不明データとして作成する
            $temp_model->setModeAttribute(Config::get('const.C005.missing_middle_time'));
            $temp_model->setRecorddatetimeAttribute($record_datetime);
            $temp_model->setWorkingstatusAttribute(Config::get('const.C012.unknown'));
            $temp_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_004'));
            $temp_model->setLateAttribute('0');
            $temp_model->setLeaveearlyAttribute('0');
            $temp_model->setWorkingintervalAttribute($chk_interval);
            $temp_model->setCurrentcalcAttribute('0');
            $temp_model->setTobeconfirmedAttribute('1');
        }

        return $temp_model;
            
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
        $attendance_from_date = new Carbon($work_time->getParamDatefromAttribute());        // 出勤1日のはじめ
        $attendance_from_date_format = date_format($attendance_from_date, 'Y/m/d');
        $timetable_from_date = new Carbon(
            $attendance_from_date_format.' '.$value_timetable_from_time);                   // タイムテーブルの始業時刻
        $timetable_to_date = new Carbon(
            $attendance_from_date_format.' '.$value_timetable_to_time);                     // タイムテーブルの終業時刻
        $attendance_to_date = new Carbon(
            $attendance_from_date_format.' 23:59:59');                                      // 出勤1日の終わり
        $record_datetime = new Carbon(
            $attendance_from_date_format.' '.$value_record_datetime);                       // 打刻日付時刻
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

        if ($ptn == '1') {
            $temp_model->setModeAttribute(Config::get('const.C005.missing_middle_return_time'));
            $temp_model->setRecorddatetimeAttribute($record_datetime);
            $temp_model->setWorkingstatusAttribute(Config::get('const.C012.forget'));
            $temp_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_009'));
            $temp_model->setLateAttribute('0');
            $temp_model->setLeaveearlyAttribute('0');
            $temp_model->setWorkingintervalAttribute($chk_interval);
            $temp_model->setCurrentcalcAttribute('0');
            $temp_model->setTobeconfirmedAttribute('1');
        } elseif ($ptn == '2') {
            $temp_model->setModeAttribute(Config::get('const.C005.missing_middle_return_time'));
            $temp_model->setRecorddatetimeAttribute($record_datetime);
            $temp_model->setWorkingstatusAttribute(Config::get('const.C012.missing_middle_return'));
            $temp_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_NON'));
            $temp_model->setLateAttribute('0');
            $temp_model->setLeaveearlyAttribute('0');
            $temp_model->setWorkingintervalAttribute($chk_interval);
            $temp_model->setCurrentcalcAttribute('0');
            $temp_model->setTobeconfirmedAttribute('0');
        } else {
            // 不明データとして作成する
            $temp_model->setModeAttribute(Config::get('const.C005.missing_middle_return_time'));
            $temp_model->setRecorddatetimeAttribute($record_datetime);
            $temp_model->setWorkingstatusAttribute(Config::get('const.C012.unknown'));
            $temp_model->setNoteAttribute(Config::get('const.MEMO_DATA.MEMO_DATA_004'));
            $temp_model->setLateAttribute('0');
            $temp_model->setLeaveearlyAttribute('0');
            $temp_model->setWorkingintervalAttribute($chk_interval);
            $temp_model->setCurrentcalcAttribute('0');
            $temp_model->setTobeconfirmedAttribute('1');
        }

        return $collect_working_times;
            
    }

    /**
     * 設定値確認
     *
     * @return チェック結果
     */
    private function chkSettingData($chkdata)
    {
        Log::error('chkSettingData in');
        $chk_result = true;
        $array_working_times = array();
        // 部署設定されているか
        if ($chkdata->department_id == null) {
            if ($chkdata->user_code != null) {
                array_push($this->array_massegedata, $chkdata->user_name.Config::get('const.MSG_ERROR.not_setting_department_id'));
                Log::error($chkdata->user_name.Config::get('const.MSG_ERROR.not_setting_department_id'));
            } else {
                array_push($this->array_massegedata, Config::get('const.MSG_ERROR.not_setting_department_id_nouser'));
                Log::error(Config::get('const.MSG_ERROR.not_setting_department_id_nouser'));
            }
            $chk_result = false;
        }
        // 締日設定されているか
        if ($chkdata->closing == null) {
            array_push($this->array_massegedata, Config::get('const.MSG_ERROR.not_setting_closing'));
            Log::error(Config::get('const.MSG_ERROR.not_setting_closing'));
            $chk_result = false;
        }
        // 時間単位設定されているか
        if ($chkdata->time_unit == null) {
            array_push($this->array_massegedata, Config::get('const.MSG_ERROR.not_setting_time_unit'));
            Log::error(Config::get('const.MSG_ERROR.not_setting_time_unit'));
            $chk_result = false;
        }
        // 時間の丸め設定されているか
        if ($chkdata->time_rounding == null) {
            array_push($this->array_massegedata, Config::get('const.MSG_ERROR.not_setting_time_rounding'));
            Log::error(Config::get('const.MSG_ERROR.not_setting_time_rounding'));
            $chk_result = false;
        }
        // 期首月設定されているか
        if ($chkdata->beginning_month == null) {
            array_push($this->array_massegedata, Config::get('const.MSG_ERROR.not_setting_beginning_month'));
            Log::error(Config::get('const.MSG_ERROR.not_setting_beginning_month'));
            $chk_result = false;
        }

        return $chk_result;
            
    }

    /**
     * 次データ計算事前処理
     *
     * @return void
     */
    private function beforeArrayWorkingTime($result)
    {
        // 打刻データ配列の初期化
        $this->iniArrayWorkingTime();
        // 打刻データ配列の設定
        $this->pushArrayWorkingTime($result);
    }

    /**
     * 打刻データ配列の初期化
     *
     * @return void
     */
    private function iniArrayWorkingTime()
    {
        // 打刻データ配列
        $this->array_massegedata = array();
        $this->array_working_mode = array();
        $this->array_working_time = array();
        $this->array_timetable_from_time = array();
        $this->array_timetable_to_time = array();
        $this->array_interval = array();
    }

    /**
     * 打刻データ配列の設定
     *
     * @return void
     */
    private function pushArrayWorkingTime($result)
    {
        // 打刻データ配列
        $this->array_working_mode[] = $result->mode;
        $this->array_working_time[] = $result->record_time;
        $this->array_timetable_from_time[] = $result->working_timetable_from_time;
        $this->array_timetable_to_time[] = $result->working_timetable_to_time;
        $this->array_interval[] = $result->interval;
    }

    /**
     * 計算用配列の初期化
     *
     * @return void
     */
    private function iniArrayCalc()
    {
        // 計算用配列配列
        $this->array_calc_mode = array();
        $this->array_calc_time = array();
        $this->array_calc_status = array();
        $this->array_calc_note = array();
        $this->array_calc_late = array();
        $this->array_calc_Leave_early = array();
        $this->array_calc_interval = array();
        $this->array_calc_calc = array();
        $this->array_calc_to_be_confirmed = array();
    }

    /**
     * 計算用配列の設定
     *
     * @return void
     */
    private function pushArrayCalc($temp_model)
    {
        // 計算用配列配列
        $this->array_calc_mode[] = $temp_model->getModeAttribute();
        $this->array_calc_time[] = $temp_model->getRecorddatetimeAttribute();
        $this->array_calc_status[] = $temp_model->getWorkingstatusAttribute();
        $this->array_calc_note[] = $temp_model->getNoteAttribute();
        $this->array_calc_late[] = $temp_model->getLateAttribute();
        $this->array_calc_Leave_early[] = $temp_model->getLeaveearlyAttribute();
        $this->array_calc_interval[] = $temp_model->getWorkingintervalAttribute();
        $this->array_calc_calc[] = $temp_model->getCurrentcalcAttribute();
        $this->array_calc_to_be_confirmed[] = $temp_model->getTobeconfirmedAttribute();
    }

    /**
     * temp日次集計タイムレコードの登録
     *
     * @return void
     */
    private function insTempCalcItem($target_date, $result)
    {
    
        // 計算用配列からtemporary項目を設定する
        $temp_model->setWorkingdateAttribute($target_date);
        $temp_model->setEmploymentstatusAttribute($result->employment_status);
        $temp_model->setDepartmentidAttribute($result->department_id);
        $temp_model->setUsercodeAttribute($result->user_code);
        $temp_model->setEmploymentstatusnameAttribute($result->employment_status_name);
        $temp_model->setDepartmentnameAttribute($result->department_name);
        $temp_model->setUsernameAttribute($result->user_name);
        $temp_model->setWorkingtimetablenoAttribute($result->working_timetable_no);
        $temp_model->setWorkingtimetablenameAttribute($result->working_timetable_name);
        $temp_model->setWorkingtimetablefromtimeAttribute($result->working_timetable_from_time);
        $temp_model->setWorkingtimetabletotimeAttribute($result->working_timetable_to_time);
        $temp_model->setShiftnoAttribute($result->shift_no);
        $temp_model->setShiftnameAttribute($result->shift_name);
        $temp_model->setShiftfromtimeAttribute($result->shift_from_time);
        $temp_model->setShifttotimeAttribute($result->shift_to_time);
        $temp_model->setRecordyearAttribute($result->record_year);
        $temp_model->setRecordmonthAttribute($result->record_month);
        $temp_model->setRecorddateAttribute($result->record_date);
        $temp_model->setRecordtimeAttribute($result->record_time);
        $temp_model->setWeekdaykubunAttribute($result->weekday_kubun);
        $temp_model->setWeekdaynameAttribute($result->weekday_name);
        $temp_model->setBusinesskubunAttribute($result->business_kubun);
        $temp_model->setBusinessnameAttribute($result->business_name);
        $temp_model->setHolidaykubunAttribute($result->holiday_kubun);
        $temp_model->setHolidaynameAttribute($result->holiday_name);
        $temp_model->setClosingAttribute($result->closing);
        $temp_model->setUplimittimeAttribute($result->uplimit_time);
        $temp_model->setStatutoryuplimittimeAttribute($result->statutory_uplimit_time);
        $temp_model->setTimeunitAttribute($result->time_unit);
        $temp_model->setTimeroundingAttribute($result->time_rounding);
        $temp_model->setMax3MonthtotalAttribute($result->max_3month_total);
        $temp_model->setMax6MonthtotalAttribute($result->max_6month_total);
        $temp_model->setMax12MonthtotalAttribute($result->max_12month_total);
        $temp_model->setBeginningmonthAttribute($result->beginning_month);
        $temp_model->setYearAttribute($result->year);

        for($i=0;$i<count($this->array_calc_mode);$i++){
            $temp_model->setModeAttribute($array_calc_mode[$i]);
            $temp_model->setRecorddatetimeAttribute($array_calc_time[$i]);
            $temp_model->setWorkingstatusAttribute($array_calc_status[$i]);
            $temp_model->setNoteAttribute($array_calc_note[$i]);
            $temp_model->setLateAttribute($array_calc_late[$i]);
            $temp_model->setLeaveearlyAttribute($array_calc_Leave_early[$i]);
            $temp_model->setCurrentcalcAttribute($array_calc_calc[$i]);
            $temp_model->setTobeconfirmedAttribute($array_calc_to_be_confirmed[$i]);
            $temp_model->setWorkingintervalAttribute($array_calc_interval[$i]);
            $temp_model->insertTempCalcWorkingtimes();
            Log::DEBUG('insTempCalcItem');
        }
    }
}
