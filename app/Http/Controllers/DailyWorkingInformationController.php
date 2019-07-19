<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\WorkTime;
use App\WorkingTimedate;
use Carbon\Carbon;
use App\Http\Controller\ApiCommonController;


class DailyWorkingInformationController extends Controller
{
    // 計算用打刻データ配列
    $array_working_mode;
    $array_working_time;
    $array_timetable_from_time;
    $array_timetable_to_time;
    $array_interval = array();

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

        // reqestクエリーセット
        $departmentcode = null;
        if(isset($request->departmentcode)){
            $departmentcode = $request->departmentcode;
        }
        $usercode = null;
        if(isset($request->usercode)){
            $usercode = $request->usercode;
        }
        $datefrom = null;
        if(isset($request->datefrom)){
            $datefrom = $request->datefrom;
        }
        $dateto = null;
        if(isset($request->dateto)){
            $dateto = $request->dateto;
        }
        // 打刻時刻を取得
        $array_massegedata = array();
        $work_time = new WorkTime();
        $work_time->setParamDepartmentcodeAttribute($departmentcode);
        $work_time->setParamUsercodeAttribute($usercode);
        $work_time->setParamDatefromAttribute($datefrom);
        $work_time->setParamDatetoAttribute($dateto);
        $chk_result = $work_time->chkWorkingTimeData();
        if ($chk_result) {
            $work_time_result = $work_time->getWorkTimes();
            if(isset($work_time_result)){
                // 日次集計計算登録
                // ユーザーの出勤・退勤・中抜・戻り時刻の確定処理
                $put_results = $this->calcWorkingTimeDates($work_time_result);
                // 日次集計取得
                $working_timedate = new WorkingTimedate();
                $working_timedate->setParamDepartmentcodeAttribute($departmentcode);
                $working_timedate->setParamUsercodeAttribute($usercode);
                $working_timedate->setParamDatefromAttribute($datefrom);
                $working_timedate->setParamDatetoAttribute($dateto);
                $working_timedate->setArrayrecordtimeAttribute($datefrom, $dateto);
                $calc_results = $working_timedate->getWorkingTimeDates();
            } else {
                $calc_results = null;
                $array_massegedata = array_add(['msg' -> Config::get('const.MSG_ERROR.not_workintime')]);
            }
        } else {
            $calc_results = null;
            array_push($array_massegedata, $work_time->getMassegedataAttribute());
        }
        return response()->json(['calc_results' => $calc_results, 'massegedata' => $array_massegedata]);
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
        $before_date = null;
        $before_user_code = null;
        $before_department_id = null;
        $array_massegedata = array();
        $array_working_mode = array();
        $array_working_time = array();
        $array_interval = array();
        $array_timetable_from_time = array();
        $array_timetable_to_time = array();
        $array_interval = array();
        // ユーザー単位処理
        foreach ($worktimes as $result) {
            // 現在の情報保存
            $current_date = $result->record_date;
            $current_user_code = $result->user_code;
            $current_department_id = $result->department_id;
            if ($before_date == null) {$before_date = $current_date;}
            if ($before_user_code == null) {$before_user_code = $current_user_code;}
            if ($before_department_id == null) {$before_department_id = $current_department_id;}
            // 設定値確認
            $chk_result = $this->chkSettingData($result);
            // 設定が正常である場合
            if ($chk_result)  {
                // 同じキーの場合
                if ($current_date == $before_date &&
                    $current_user_code == $before_user_code &&
                    $current_department_id == $before_department_id) {
                    // 出勤・退勤・中抜・戻り時刻の設定
                    array_push($array_working_mode, $result->mode);
                    array_push($array_working_time, $result->record_time);
                    array_push($array_timetable_from_time, $result->working_timetable_from_time);
                    array_push($array_timetable_to_time, $result->working_timetable_to_time);
                    array_push($array_interval, $result->interval);
                } elseif ($current_user_code == $before_user_code &&
                    $current_department_id == $before_department_id) {
                    // 日付が変わった場合
                    Log::DEBUG('date break ');
                    // ユーザー労働時間計算(１個前のユーザーを計算する)
                    $calc_result = 
                        $this->calcWorkingTime(
                            $before_date,
                            $before_user_code,
                            $before_department_id,
                            $array_working_mode,
                            $array_working_time,
                            $array_timetable_from_time,
                            $array_timetable_to_time,
                            $array_interval);
                    // 次の計算処理準備
                    $array_working_mode = array();
                    $array_working_time = array();
                    $array_timetable_from_time = array();
                    $array_timetable_to_time = array();
                    $array_interval = array();
                    // 出勤・退勤・中抜・戻り時刻の設定
                    array_push($array_working_mode, $result->mode);
                    array_push($array_working_time, $result->record_time);
                    array_push($array_timetable_from_time, $result->working_timetable_from_time);
                    array_push($array_timetable_to_time, $result->working_timetable_to_time);
                    array_push($array_interval, $result->interval);
                    $before_date = $current_date;
                } elseif ($current_department_id == $before_department_id)  {
                    // ユーザーが変わった場合
                    Log::DEBUG('user break ');
                    // ユーザー労働時間計算(１個前のユーザーを計算する)
                    $calc_result = 
                        $this->calcWorkingTime(
                            $before_date,
                            $before_user_code,
                            $before_department_id,
                            $array_working_mode,
                            $array_working_time,
                            $array_timetable_from_time,
                            $array_timetable_to_time,
                            $array_interval);
                    // 次の計算処理準備
                    $array_working_mode = array();
                    $array_working_time = array();
                    $array_timetable_from_time = array();
                    $array_timetable_to_time = array();
                    $array_interval = array();
                    // 出勤・退勤・中抜・戻り時刻の設定
                    array_push($array_working_mode, $result->mode);
                    array_push($array_working_time, $result->record_time);
                    array_push($array_timetable_from_time, $result->working_timetable_from_time);
                    array_push($array_timetable_to_time, $result->working_timetable_to_time);
                    array_push($array_interval, $result->interval);
                    $before_user_code = $current_user_code;
                } else {
                    // 部署が変わっても（兼任）ユーザーが変わっていない場合は継続
                    array_push($array_working_mode, $result->mode);
                    array_push($array_working_time, $result->record_time);
                    $before_department_id = $current_department_id;
                }
            } else {
                array_push($array_massegedata, $work_time->getMassegedataAttribute());
                Log::DEBUG('calcWorkingTimeDates error '.$work_time->getMassegedataAttribute());
            }
        }

        if (count($array_working_mode) > 0) {
            // ユーザー労働時間計算(１個前のユーザーを計算する)
            $calc_result = 
            $this->calcWorkingTime(
                $before_date,
                $before_user_code,
                $before_department_id,
                $array_working_mode,
                $array_working_time,
                $array_timetable_from_time,
                $array_timetable_to_time,
                $array_interval);
        }

        return DB::table('users')->get();

    }

    /**
     * ユーザー労働時間計算
     *
     * @return 労働時間計算結果
     */
    private function calcWorkingTime($target_date, $target_user_code, $target_department_id,
        $array_working_mode, $array_working_time, $array_timetable_from_time, $array_timetable_to_time, $array_interval)
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
        Log::DEBUG('array_working_mode count = '.count($array_working_mode));
        for($i=0;$i<count($array_working_mode);$i++){
            $value_mode = $array_working_mode[$i];
            $value_record_datetime = $array_working_time[$i];
            $value_timetable_from_time = $array_timetable_from_time[$i];
            $value_timetable_to_time = $array_timetable_to_time[$i];
            $value_interval = $array_interval[$i];
            // 出勤打刻の場合
            if ($value_mode == Config::get('const.C005.attendance_time')) {
                $collect_merge = $collect_calc_times->merge(
                    $this->setAttendancetime(
                        $cnt,
                        $work_time,
                        $value_record_datetime,
                        $value_timetable_from_time,
                        $value_timetable_to_time,
                        $value_interval,
                        $before_value_mode,
                        $before_value_datetime
                    )
                );
                $collect_calc_times = $collect_merge;
                if(isset($collect_calc_times)){
                    $calc_mode = $collect_calc_times->get('mode');
                    $calc_time = $collect_calc_times->get('time');
                    $calc_status = $collect_calc_times->get('status');
                    $calc_note = $collect_calc_times->get('note');
                    $calc_late = $collect_calc_times->get('late');
                    $calc_Leave_early= $collect_calc_times->get('Leave_early');
                    $calc_to_be_confirmed = $collect_calc_times->get('to_be_confirmed');
                    $calc_interval = $collect_calc_times->get('interval');
                    Log::DEBUG('calc_value_mode = '.$calc_mode);
                    Log::DEBUG('calc_time = '.$calc_time);
                    Log::DEBUG('calc_status = '.$calc_status);
                    Log::DEBUG('calc_note = '.$calc_note);
                    Log::DEBUG('calc_late = '.$calc_late);
                    Log::DEBUG('calc_Leave_early = '.$calc_Leave_early);
                    Log::DEBUG('calc_to_be_confirmed = '.$calc_to_be_confirmed);
                    Log::DEBUG('calc_interval = '.$calc_interval);
                }
                $before_value_mode = $value_mode;
                $before_value_datetime = $value_record_datetime;
            } elseif ($value_mode == Config::get('const.C005.leaving_time')) {      // 退勤の場合
                $collect_merge = $collect_calc_times->merge(
                    $this->setLeavingtime(
                        $cnt,
                        $work_time,
                        $value_record_datetime,
                        $value_timetable_from_time,
                        $value_timetable_to_time,
                        $array_working_mode,
                        $array_working_time,
                        $before_value_mode,
                        $before_value_datetime
                    )
                );
                $collect_calc_times = $collect_merge;
                if(isset($collect_calc_times)){
                    $calc_mode = $collect_calc_times->get('mode');
                    $calc_time = $collect_calc_times->get('time');
                    $calc_status = $collect_calc_times->get('status');
                    $calc_note = $collect_calc_times->get('note');
                    $calc_late = $collect_calc_times->get('late');
                    $calc_Leave_early= $collect_calc_times->get('Leave_early');
                    $calc_to_be_confirmed = $collect_calc_times->get('to_be_confirmed');
                    $calc_interval = $collect_calc_times->get('interval');
                    Log::DEBUG('calc_value_mode = '.$calc_mode);
                    Log::DEBUG('calc_time = '.$calc_time);
                    Log::DEBUG('calc_status = '.$calc_status);
                    Log::DEBUG('calc_note = '.$calc_note);
                    Log::DEBUG('calc_late = '.$calc_late);
                    Log::DEBUG('calc_Leave_early = '.$calc_Leave_early);
                    Log::DEBUG('calc_to_be_confirmed = '.$calc_to_be_confirmed);
                    Log::DEBUG('calc_interval = '.$calc_interval);
                }
            } elseif ($value_mode == Config::get('const.C005.missing_middle_time')) {       // 中抜けの場合
                $collect_merge = $collect_calc_times->merge(
                    $this->setMissingMiddleTime(
                        $cnt,
                        $work_time,
                        $value_record_datetime,
                        $value_timetable_from_time,
                        $value_timetable_to_time,
                        $before_value_mode,
                        $before_value_datetime
                    )
                );
                $collect_calc_times = $collect_merge;
                if(isset($collect_calc_times)){
                    $calc_mode = $collect_calc_times->get('mode');
                    $calc_time = $collect_calc_times->get('time');
                    $calc_status = $collect_calc_times->get('status');
                    $calc_note = $collect_calc_times->get('note');
                    $calc_late = $collect_calc_times->get('late');
                    $calc_Leave_early= $collect_calc_times->get('Leave_early');
                    $calc_to_be_confirmed = $collect_calc_times->get('to_be_confirmed');
                    $calc_interval = $collect_calc_times->get('interval');
                    Log::DEBUG('calc_value_mode = '.$calc_mode);
                    Log::DEBUG('calc_time = '.$calc_time);
                    Log::DEBUG('calc_status = '.$calc_status);
                    Log::DEBUG('calc_note = '.$calc_note);
                    Log::DEBUG('calc_late = '.$calc_late);
                    Log::DEBUG('calc_Leave_early = '.$calc_Leave_early);
                    Log::DEBUG('calc_to_be_confirmed = '.$calc_to_be_confirmed);
                    Log::DEBUG('calc_interval = '.$calc_interval);
                }
            } elseif ($value_mode == Config::get('const.C005.missing_middle_return_time')) {    // 中抜け戻りの場合
                $collect_merge = $collect_calc_times->merge(
                    $this->setMissingMiddleReturnTime(
                        $cnt,
                        $work_time,
                        $value_record_datetime,
                        $value_timetable_from_time,
                        $value_timetable_to_time,
                        $before_value_mode,
                        $before_value_datetime
                    )
                );
                $collect_calc_times = $collect_merge;
                if(isset($collect_calc_times)){
                    $calc_mode = $collect_calc_times->get('mode');
                    $calc_time = $collect_calc_times->get('time');
                    $calc_status = $collect_calc_times->get('status');
                    $calc_note = $collect_calc_times->get('note');
                    $calc_late = $collect_calc_times->get('late');
                    $calc_Leave_early= $collect_calc_times->get('Leave_early');
                    $calc_to_be_confirmed = $collect_calc_times->get('to_be_confirmed');
                    $calc_interval = $collect_calc_times->get('interval');
                    Log::DEBUG('calc_value_mode = '.$calc_mode);
                    Log::DEBUG('calc_time = '.$calc_time);
                    Log::DEBUG('calc_status = '.$calc_status);
                    Log::DEBUG('calc_note = '.$calc_note);
                    Log::DEBUG('calc_late = '.$calc_late);
                    Log::DEBUG('calc_Leave_early = '.$calc_Leave_early);
                    Log::DEBUG('calc_to_be_confirmed = '.$calc_to_be_confirmed);
                    Log::DEBUG('calc_interval = '.$calc_interval);
                }
            }
            $before_value_mode = $value_mode;
            $before_value_datetime = $value_record_datetime;
            $cnt = $cnt + 1;
        }

        Log::DEBUG('calcWorkingTime end ');

        return $collect_calc_times;
            
    }

    /**
     * 出勤打刻処理
     *
     * @param  打刻時刻のSEQ
     *         打刻時刻
     *         １個前のモード
     * @return チェック結果
     */
    private function setAttendancetime($cnt, $work_time,
        $value_record_datetime, $value_timetable_from_time, $value_timetable_to_time, $value_interval,
        $before_value_mode, $before_value_datetime)
    {
        $collect_working_times = collect();

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
                        // 不明データとして作成する
                        $collect_working_times = $this->setAttendanceCollectPtn('', $record_datetime,$chk_interval);
                        // 出勤打刻時刻が出勤1日の終わりより大きいことはない
                        $message_data = $work_time->getParamDatefromAttribute();
                        $message_data .= $work_time->getParamUsercodeAttribute();
                        $message_data .= $work_time->getParamDepartmentcodeAttribute();
                        Log::error(Config::get('const.MSG_ERROR.mismatch_data') + 'method = setAttendancetime1 ' + $message_data);
                        return $collect_working_times;
                    }
                } else {
                    // 不明データとして作成する
                    $collect_working_times = $this->setAttendanceCollectPtn('', $record_datetime,$chk_interval);
                    $message_data = $work_time->getParamDatefromAttribute();
                    $message_data .= $work_time->getParamUsercodeAttribute();
                    $message_data .= $work_time->getParamDepartmentcodeAttribute();
                    Log::error(Config::get('const.MSG_ERROR.mismatch_data') + 'method = setAttendancetime2 ' + $message_data);
                    return $collect_working_times;
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
                        // 不明データとして作成する
                        $collect_working_times = $this->setAttendanceCollectPtn('', $record_datetime,$chk_interval);
                        // 出勤打刻時刻が出勤1日の終わりより大きいことはない
                        $message_data = $work_time->getParamDatefromAttribute();
                        $message_data .= $work_time->getParamUsercodeAttribute();
                        $message_data .= $work_time->getParamDepartmentcodeAttribute();
                        Log::error(Config::get('const.MSG_ERROR.mismatch_data') + 'method = setAttendancetime3 ' + $message_data);
                        return $collect_working_times;
                    }
                } else {
                    // 不明データとして作成する
                    $collect_working_times = $this->setAttendanceCollectPtn('', $record_datetime,$chk_interval);
                    $message_data = $work_time->getParamDatefromAttribute();
                    $message_data .= $work_time->getParamUsercodeAttribute();
                    $message_data .= $work_time->getParamDepartmentcodeAttribute();
                    Log::error(Config::get('const.MSG_ERROR.mismatch_data') + 'method = setAttendancetime4 ' + $message_data);
                    return $collect_working_times;
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
                    // 不明データとして作成する
                    $collect_working_times = $this->setAttendanceCollectPtn('', $record_datetime,$chk_interval);
                    // 出勤打刻時刻が出勤1日の終わりより大きいことはない
                    $message_data = $work_time->getParamDatefromAttribute();
                    $message_data .= $work_time->getParamUsercodeAttribute();
                    $message_data .= $work_time->getParamDepartmentcodeAttribute();
                    Log::error(Config::get('const.MSG_ERROR.mismatch_data') + 'method = setAttendancetime5 ' + $message_data);
                    return $collect_working_times;
                } else {
                    // 不明データとして作成する
                    $collect_working_times = $this->setAttendanceCollectPtn('', $record_datetime,$chk_interval);
                    $message_data = $work_time->getParamDatefromAttribute();
                    $message_data .= $work_time->getParamUsercodeAttribute();
                    $message_data .= $work_time->getParamDepartmentcodeAttribute();
                    Log::error(Config::get('const.MSG_ERROR.mismatch_data') + 'method = setAttendancetime6 ' + $message_data);
                    return $collect_working_times;
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
                    // 不明データとして作成する
                    $collect_working_times = $this->setAttendanceCollectPtn('', $record_datetime,$chk_interval);
                    // 出勤打刻時刻が出勤1日の終わりより大きいことはない
                    $message_data = $work_time->getParamDatefromAttribute();
                    $message_data .= $work_time->getParamUsercodeAttribute();
                    $message_data .= $work_time->getParamDepartmentcodeAttribute();
                    Log::error(Config::get('const.MSG_ERROR.mismatch_data') + 'method = setAttendancetime7 ' + $message_data);
                    return $collect_working_times;
                } else {
                    // 不明データとして作成する
                    $collect_working_times = $this->setAttendanceCollectPtn('', $record_datetime,$chk_interval);
                    $message_data = $work_time->getParamDatefromAttribute();
                    $message_data .= $work_time->getParamUsercodeAttribute();
                    $message_data .= $work_time->getParamDepartmentcodeAttribute();
                    Log::error(Config::get('const.MSG_ERROR.mismatch_data') + 'method = setAttendancetime8 ' + $message_data);
                    return $collect_working_times;
                }
            }
        }

        if ($ptn != null) {
            $collect_working_times = $this->setAttendanceCollectPtn($ptn, $record_datetime,$chk_interval);
        } else {
            // 不明データとして作成する
            $collect_working_times = $this->setAttendanceCollectPtn('', $record_datetime,$chk_interval);
        }
        
        return $collect_working_times;
            
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

        if ($ptn == '1') {
            $collect_working_times = collect([
                'mode' => Config::get('const.C005.attendance_time'),
                'time' => $record_datetime,
                'status' => Config::get('const.C012.attendance'),
                'note' => Config::get('const.MEMO_DATA.MEMO_DATA_NON'),
                'late' => '0',
                'Leave_early' => '0',
                'interval' => $chk_interval,
                'calc' => '0',
                'to_be_confirmed' => '0']);
        } elseif ($ptn == '2') {
            $collect_working_times = collect([
                'mode' => Config::get('const.C005.attendance_time'),
                'time' => $record_datetime,
                'status' => Config::get('const.C012.forget'),
                'note' => Config::get('const.MEMO_DATA.MEMO_DATA_001'),
                'late' => '0',
                'Leave_early' => '0',
                'interval' => $chk_interval,
                'calc' => '0',
                'to_be_confirmed' => '1']);
        } elseif ($ptn == '3') {
            $collect_working_times = collect([
                'mode' => Config::get('const.C005.attendance_time'),
                'time' => $record_datetime,
                'status' => Config::get('const.C012.attendance'),
                'note' => Config::get('const.MEMO_DATA.MEMO_DATA_NON'),
                'late' => '1',
                'Leave_early' => '0',
                'interval' => $chk_interval,
                'calc' => '0',
                'to_be_confirmed' => '0']);
        } elseif ($ptn == '4') {
            $collect_working_times = collect([
                'mode' => Config::get('const.C005.attendance_time'),
                'time' => $record_datetime,
                'status' => Config::get('const.C012.attendance'),
                'note' => Config::get('const.MEMO_DATA.MEMO_DATA_002'),
                'late' => '1',
                'Leave_early' => '0',
                'interval' => $chk_interval,
                'calc' => '0',
                'to_be_confirmed' => '1']);
        } elseif ($ptn == '5') {
            $collect_working_times = collect([
                'mode' => Config::get('const.C005.attendance_time'),
                'time' => $record_datetime,
                'status' => Config::get('const.C012.forget'),
                'note' => Config::get('const.MEMO_DATA.MEMO_DATA_003'),
                'late' => '0',
                'Leave_early' => '0',
                'interval' => $chk_interval,
                'calc' => '0',
                'to_be_confirmed' => '1']);
        } elseif ($ptn == '6') {
            $collect_working_times = collect([
                'mode' => Config::get('const.C005.attendance_time'),
                'time' => $record_datetime,
                'status' => Config::get('const.C012.forget'),
                'note' => Config::get('const.MEMO_DATA.MEMO_DATA_001'),
                'late' => '0',
                'Leave_early' => '0',
                'interval' => $chk_interval,
                'calc' => '0',
                'to_be_confirmed' => '1']);
        } else {
            // 不明データとして作成する
            $collect_working_times = collect([
                'mode' => Config::get('const.C005.attendance_time'),
                'time' => $record_datetime,
                'status' => Config::get('const.C012.unknown'),
                'note' => Config::get('const.MEMO_DATA.MEMO_DATA_004'),
                'late' => '0',
                'Leave_early' => '0',
                'interval' => $chk_interval,
                'calc' => '0',
                'to_be_confirmed' => '1']);
        }

        return $collect_working_times;
            
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
        $array_working_mode,$array_working_time,
        $before_value_mode, $before_value_datetime)
    {
        $collect_working_times = collect();

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
                        $collect_merge = $collect_working_times->merge(
                            $this->setLeavingCollectPtn($ptn, $timetable_from_date)
                        );
                        $collect_working_times = $collect_merge;
                        $ptn = '3';
                        $collect_merge = $collect_working_times->merge(
                            $this->setLeavingCollectPtn($ptn, $timetable_from_date)
                        );
                        $collect_working_times = $collect_merge;
                        $ptn = '4';
                        $collect_merge = $collect_working_times->merge(
                            $this->setLeavingCollectPtn($ptn, $record_datetime)
                        );
                        $collect_working_times = $collect_merge;
                        // もう設定したのでここでreturn
                        return $collect_working_times;
                    }
                } elseif ($record_datetime >= $timetable_to_date &&
                            $record_datetime < $attendance_to_date) {                       // タイムテーブルの終業時刻 <= 打刻時刻 < 出勤1日の終わり
                    if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                        // パターン２３５
                        $ptn = '2';
                        $collect_merge = $collect_working_times->merge(
                            $this->setLeavingCollectPtn($ptn, $timetable_from_date)
                        );
                        $collect_working_times = $collect_merge;
                        $ptn = '3';
                        $collect_merge = $collect_working_times->merge(
                            $this->setLeavingCollectPtn($ptn, $timetable_from_date)
                        );
                        $collect_working_times = $collect_merge;
                        $ptn = '5';
                        $collect_merge = $collect_working_times->merge(
                            $this->setLeavingCollectPtn($ptn, $record_datetime)
                        );
                        $collect_working_times = $collect_merge;
                        // もう設定したのでここでreturn
                        return $collect_working_times;
                    }
                } elseif ($record_datetime > $attendance_to_date) {                         // 出勤1日の終わり < 打刻時刻
                    if ($record_before_datetime < $attendance_from_date) {                  // １個前の打刻時刻 < 出勤1日のはじめ
                        // パターン２３５
                        $ptn = '2';
                        $collect_merge = $collect_working_times->merge(
                            $this->setLeavingCollectPtn($ptn, $timetable_from_date)
                        );
                        $collect_working_times = $collect_merge;
                        $ptn = '3';
                        $collect_merge = $collect_working_times->merge(
                            $this->setLeavingCollectPtn($ptn, $timetable_from_date)
                        );
                        $collect_working_times = $collect_merge;
                        $ptn = '5';
                        $collect_merge = $collect_working_times->merge(
                            $this->setLeavingCollectPtn($ptn, $record_datetime)
                        );
                        $collect_working_times = $collect_merge;
                        // もう設定したのでここでreturn
                        return $collect_working_times;
                    }
                } else {
                    // 不明データとして作成する
                    $collect_working_times = $this->setLeavingCollectPtn('', $record_datetime);
                    $message_data = $work_time->getParamDatefromAttribute();
                    $message_data .= $work_time->getParamUsercodeAttribute();
                    $message_data .= $work_time->getParamDepartmentcodeAttribute();
                    Log::error(Config::get('const.MSG_ERROR.mismatch_data') + 'method = setLeavingtime1 ' + $message_data);
                    return $collect_working_times;
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
                                        $collect_merge = $collect_working_times->merge(
                                            $this->setLeavingCollectPtn($ptn, $timetable_from_date)
                                        );
                                        $collect_working_times = $collect_merge;
                                        $ptn = '3';
                                        $collect_merge = $collect_working_times->merge(
                                            $this->setLeavingCollectPtn($ptn, $timetable_from_date)
                                        );
                                        $collect_working_times = $collect_merge;
                                        $ptn = '4';
                                        $collect_merge = $collect_working_times->merge(
                                            $this->setLeavingCollectPtn($ptn, $record_datetime)
                                        );
                                        $collect_working_times = $collect_merge;
                                        // もう設定したのでここでreturn
                                        return $collect_working_times;
                                    } else {
                                        // パターン２３５
                                        $ptn = '2';
                                        $collect_merge = $collect_working_times->merge(
                                            $this->setLeavingCollectPtn($ptn, $timetable_from_date)
                                        );
                                        $collect_working_times = $collect_merge;
                                        $ptn = '3';
                                        $collect_merge = $collect_working_times->merge(
                                            $this->setLeavingCollectPtn($ptn, $timetable_from_date)
                                        );
                                        $collect_working_times = $collect_merge;
                                        $ptn = '5';
                                        $collect_merge = $collect_working_times->merge(
                                            $this->setLeavingCollectPtn($ptn, $record_datetime)
                                        );
                                        $collect_working_times = $collect_merge;
                                        // もう設定したのでここでreturn
                                        return $collect_working_times;
                                    }
                                    break;
                                }
                            }
                        } else {
                            // パターン２３４
                            $ptn = '2';
                            $collect_merge = $collect_working_times->merge(
                                $this->setLeavingCollectPtn($ptn, $timetable_from_date)
                            );
                            $collect_working_times = $collect_merge;
                            $ptn = '3';
                            $collect_merge = $collect_working_times->merge(
                                $this->setLeavingCollectPtn($ptn, $timetable_from_date)
                            );
                            $collect_working_times = $collect_merge;
                            $ptn = '4';
                            $collect_merge = $collect_working_times->merge(
                                $this->setLeavingCollectPtn($ptn, $record_datetime)
                            );
                            $collect_working_times = $collect_merge;
                            // もう設定したのでここでreturn
                            return $collect_working_times;
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
                                        $collect_merge = $collect_working_times->merge(
                                            $this->setLeavingCollectPtn($ptn, $timetable_from_date)
                                        );
                                        $collect_working_times = $collect_merge;
                                        $ptn = '3';
                                        $collect_merge = $collect_working_times->merge(
                                            $this->setLeavingCollectPtn($ptn, $timetable_from_date)
                                        );
                                        $collect_working_times = $collect_merge;
                                        $ptn = '5';
                                        $collect_merge = $collect_working_times->merge(
                                            $this->setLeavingCollectPtn($ptn, $record_datetime)
                                        );
                                        $collect_working_times = $collect_merge;
                                        // もう設定したのでここでreturn
                                        return $collect_working_times;
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
                            $collect_merge = $collect_working_times->merge(
                                $this->setLeavingCollectPtn($ptn, $timetable_from_date)
                            );
                            $collect_working_times = $collect_merge;
                            $ptn = '3';
                            $collect_merge = $collect_working_times->merge(
                                $this->setLeavingCollectPtn($ptn, $timetable_from_date)
                            );
                            $collect_working_times = $collect_merge;
                            $ptn = '5';
                            $collect_merge = $collect_working_times->merge(
                                $this->setLeavingCollectPtn($ptn, $record_datetime)
                            );
                            $collect_working_times = $collect_merge;
                            // もう設定したのでここでreturn
                            return $collect_working_times;
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
                                    $collect_merge = $collect_working_times->merge(
                                        $this->setLeavingCollectPtn($ptn, $timetable_from_date)
                                    );
                                    $collect_working_times = $collect_merge;
                                    $ptn = '3';
                                    $collect_merge = $collect_working_times->merge(
                                        $this->setLeavingCollectPtn($ptn, $timetable_from_date)
                                    );
                                    $collect_working_times = $collect_merge;
                                    $ptn = '5';
                                    $collect_merge = $collect_working_times->merge(
                                        $this->setLeavingCollectPtn($ptn, $record_datetime)
                                    );
                                    $collect_working_times = $collect_merge;
                                    // もう設定したのでここでreturn
                                    return $collect_working_times;
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
                        $collect_merge = $collect_working_times->merge(
                            $this->setLeavingCollectPtn($ptn, $timetable_from_date)
                        );
                        $collect_working_times = $collect_merge;
                        $ptn = '3';
                        $collect_merge = $collect_working_times->merge(
                            $this->setLeavingCollectPtn($ptn, $timetable_from_date)
                        );
                        $collect_working_times = $collect_merge;
                        $ptn = '5';
                        $collect_merge = $collect_working_times->merge(
                            $this->setLeavingCollectPtn($ptn, $record_datetime)
                        );
                        $collect_working_times = $collect_merge;
                        // もう設定したのでここでreturn
                        return $collect_working_times;
                    }
                } else {
                    // 不明データとして作成する
                    $collect_working_times = $this->setLeavingCollectPtn('', $record_datetime);
                    $message_data = $work_time->getParamDatefromAttribute();
                    $message_data .= $work_time->getParamUsercodeAttribute();
                    $message_data .= $work_time->getParamDepartmentcodeAttribute();
                    Log::error(Config::get('const.MSG_ERROR.mismatch_data') + 'method = setLeavingtime2 ' + $message_data);
                    return $collect_working_times;
                }
            } else {                                                                        // １個前のモードがない
                // 不明データとして作成する
                $collect_working_times = $this->setLeavingCollectPtn('', $record_datetime);
                $message_data = $work_time->getParamDatefromAttribute();
                $message_data .= $work_time->getParamUsercodeAttribute();
                $message_data .= $work_time->getParamDepartmentcodeAttribute();
                Log::error(Config::get('const.MSG_ERROR.mismatch_data') + 'method = setLeavingtime3 ' + $message_data);
                return $collect_working_times;
            }
        }

        if ($ptn != null) {
            $collect_working_times = $this->setLeavingCollectPtn($ptn, $record_datetime);
        } else {
            // 不明データとして作成する
            $collect_working_times = $this->setLeavingCollectPtn('', $record_datetime);
        }
        
        return $collect_working_times;
            
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

        if ($ptn == '1') {
            $collect_working_times = collect([
                'mode' => Config::get('const.C005.leaving_time'),
                'time' => $record_datetime,
                'status' => Config::get('const.C012.leaving'),
                'note' => Config::get('const.MEMO_DATA.MEMO_DATA_005'),
                'late' => '0',
                'Leave_early' => '0',
                'interval' => '0',
                'calc' => '0',
                'to_be_confirmed' => '0']);
        } elseif ($ptn == '2') {
            $collect_working_times = collect([
                'mode' => Config::get('const.C005.leaving_time'),
                'time' => $record_datetime,
                'status' => Config::get('const.C012.leaving'),
                'note' => Config::get('const.MEMO_DATA.MEMO_DATA_005').' '.
                            Config::get('const.MEMO_DATA.MEMO_DATA_006'),
                'late' => '0',
                'Leave_early' => '0',
                'interval' => '0',
                'calc' => '0',
                'to_be_confirmed' => '1']);
        } elseif ($ptn == '3') {
            $collect_working_times = collect([
                'mode' => Config::get('const.C005.attendance_time'),
                'time' => $record_datetime,
                'status' => Config::get('const.C012.attendance'),
                'note' => Config::get('const.MEMO_DATA.MEMO_DATA_006'),
                'late' => '0',
                'Leave_early' => '0',
                'interval' => '0',
                'calc' => '0',
                'to_be_confirmed' => '1']);
        } elseif ($ptn == '4') {
            $collect_working_times = collect([
                'mode' => Config::get('const.C005.leaving_time'),
                'time' => $record_datetime,
                'status' => Config::get('const.C012.leaving'),
                'note' => Config::get('const.MEMO_DATA.MEMO_DATA_006'),
                'late' => '0',
                'Leave_early' => '1',
                'interval' => '0',
                'calc' => '1',
                'to_be_confirmed' => '1']);
        } elseif ($ptn == '5') {
            $collect_working_times = collect([
                'mode' => Config::get('const.C005.leaving_time'),
                'time' => $record_datetime,
                'status' => Config::get('const.C012.leaving'),
                'note' => Config::get('const.MEMO_DATA.MEMO_DATA_006'),
                'late' => '0',
                'Leave_early' => '0',
                'interval' => '0',
                'calc' => '1',
                'to_be_confirmed' => '1']);
        } elseif ($ptn == '6') {
            $collect_working_times = collect([
                'mode' => Config::get('const.C005.leaving_time'),
                'time' => $record_datetime,
                'status' => Config::get('const.C012.forget'),
                'note' => Config::get('const.MEMO_DATA.MEMO_DATA_002'),
                'late' => '0',
                'Leave_early' => '0',
                'interval' => '0',
                'calc' => '0',
                'to_be_confirmed' => '1']);
        } elseif ($ptn == '7') {
            $collect_working_times = collect([
                'mode' => Config::get('const.C005.leaving_time'),
                'time' => $record_datetime,
                'status' => Config::get('const.C012.forget'),
                'note' => Config::get('const.MEMO_DATA.MEMO_DATA_003'),
                'late' => '0',
                'Leave_early' => '0',
                'interval' => '0',
                'calc' => '0',
                'to_be_confirmed' => '1']);
        } elseif ($ptn == '8') {
            $collect_working_times = collect([
                'mode' => Config::get('const.C005.leaving_time'),
                'time' => $record_datetime,
                'status' => Config::get('const.C012.leaving'),
                'note' => Config::get('const.MEMO_DATA.MEMO_DATA_NON'),
                'late' => '0',
                'Leave_early' => '0',
                'interval' => '0',
                'calc' => '1',
                'to_be_confirmed' => '0']);
        } else {
            // 不明データとして作成する
            $collect_working_times = collect([
                'mode' => Config::get('const.C005.leaving_time'),
                'time' => $record_datetime,
                'status' => Config::get('const.C012.unknown'),
                'note' => Config::get('const.MEMO_DATA.MEMO_DATA_004'),
                'late' => '0',
                'Leave_early' => '0',
                'to_be_confirmed' => '1',
                'interval' => '0']);
        }

        return $collect_working_times;
            
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
        $collect_working_times = collect();

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
                $collect_working_times = $this->setMissingmiddleCollectPtn('', $record_datetime);
                $message_data = $work_time->getParamDatefromAttribute();
                $message_data .= $work_time->getParamUsercodeAttribute();
                $message_data .= $work_time->getParamDepartmentcodeAttribute();
                Log::error(Config::get('const.MSG_ERROR.mismatch_data') + 'method = setMissingMiddleTime1 ' + $message_data);
                return $collect_working_times;
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
                    $collect_working_times = $this->setMissingmiddleCollectPtn('', $record_datetime);
                    $message_data = $work_time->getParamDatefromAttribute();
                    $message_data .= $work_time->getParamUsercodeAttribute();
                    $message_data .= $work_time->getParamDepartmentcodeAttribute();
                    Log::error(Config::get('const.MSG_ERROR.mismatch_data') + 'method = setMissingMiddleTime2 ' + $message_data);
                    return $collect_working_times;
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
                $collect_working_times = $this->setMissingmiddleCollectPtn('', $record_datetime);
                $message_data = $work_time->getParamDatefromAttribute();
                $message_data .= $work_time->getParamUsercodeAttribute();
                $message_data .= $work_time->getParamDepartmentcodeAttribute();
                Log::error(Config::get('const.MSG_ERROR.mismatch_data') + 'method = setMissingMiddleTime3 ' + $message_data);
                return $collect_working_times;
            }
        }

        if ($ptn != null) {
            $collect_working_times = $this->setMissingmiddleCollectPtn($ptn, $record_datetime);
        } else {
            // 不明データとして作成する
            $collect_working_times = $this->setMissingmiddleCollectPtn('', $record_datetime);
        }
        
        return $collect_working_times;
            
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

        if ($ptn == '1') {
            $collect_working_times = collect([
                'mode' => Config::get('const.C005.missing_middle_time'),
                'time' => $record_datetime,
                'status' => Config::get('const.C012.missing_middle'),
                'note' => Config::get('const.MEMO_DATA.MEMO_DATA_NON'),
                'late' => '0',
                'Leave_early' => '0',
                'interval' => '0',
                'calc' => '0',
                'to_be_confirmed' => '0']);
        } elseif ($ptn == '2') {
            $collect_working_times = collect([
                'mode' => Config::get('const.C005.missing_middle_time'),
                'time' => $record_datetime,
                'status' => Config::get('const.C012.forget'),
                'note' => Config::get('const.MEMO_DATA.MEMO_DATA_008'),
                'late' => '0',
                'Leave_early' => '0',
                'interval' => '0',
                'calc' => '0',
                'to_be_confirmed' => '1']);
        } elseif ($ptn == '3') {
            $collect_working_times = collect([
                'mode' => Config::get('const.C005.missing_middle_time'),
                'time' => $record_datetime,
                'status' => Config::get('const.C012.forget'),
                'note' => Config::get('const.MEMO_DATA.MEMO_DATA_003'),
                'late' => '0',
                'Leave_early' => '0',
                'interval' => '0',
                'calc' => '0',
                'to_be_confirmed' => '1']);
        } else {
            // 不明データとして作成する
            $collect_working_times = collect([
                'mode' => Config::get('const.C005.leaving_time'),
                'time' => $record_datetime,
                'status' => Config::get('const.C012.unknown'),
                'note' => Config::get('const.MEMO_DATA.MEMO_DATA_004'),
                'late' => '0',
                'Leave_early' => '0',
                'to_be_confirmed' => '1',
                'interval' => '0']);
        }

        return $collect_working_times;
            
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
        $collect_working_times = collect();

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
                $collect_working_times = $this->setMissingmiddleReturnCollectPtn('', $record_datetime);
                $message_data = $work_time->getParamDatefromAttribute();
                $message_data .= $work_time->getParamUsercodeAttribute();
                $message_data .= $work_time->getParamDepartmentcodeAttribute();
                Log::error(Config::get('const.MSG_ERROR.mismatch_data') + 'method = setMissingMiddleReturnTime1 ' + $message_data);
                return $collect_working_times;
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
                    $collect_working_times = $this->setMissingmiddleReturnCollectPtn('', $record_datetime);
                    $message_data = $work_time->getParamDatefromAttribute();
                    $message_data .= $work_time->getParamUsercodeAttribute();
                    $message_data .= $work_time->getParamDepartmentcodeAttribute();
                    Log::error(Config::get('const.MSG_ERROR.mismatch_data') + 'method = setMissingMiddleReturnTime2 ' + $message_data);
                    return $collect_working_times;
            } else {                                                                        // １個前のモードがない
                // 不明データとして作成する
                $collect_working_times = $this->setMissingmiddleReturnCollectPtn('', $record_datetime);
                $message_data = $work_time->getParamDatefromAttribute();
                $message_data .= $work_time->getParamUsercodeAttribute();
                $message_data .= $work_time->getParamDepartmentcodeAttribute();
                Log::error(Config::get('const.MSG_ERROR.mismatch_data') + 'method = setMissingMiddleReturnTime3 ' + $message_data);
                return $collect_working_times;
            }
        }

        if ($ptn != null) {
            $collect_working_times = $this->setMissingmiddleReturnCollectPtn($ptn, $record_datetime);
        } else {
            // 不明データとして作成する
            $collect_working_times = $this->setMissingmiddleReturnCollectPtn('', $record_datetime);
        }
        
        return $collect_working_times;
            
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
            $collect_working_times = collect([
                'mode' => Config::get('const.C005.missing_middle_return_time'),
                'time' => $record_datetime,
                'status' => Config::get('const.C012.forget'),
                'note' => Config::get('const.MEMO_DATA.MEMO_DATA_009'),
                'late' => '0',
                'Leave_early' => '0',
                'interval' => '0',
                'calc' => '0',
                'to_be_confirmed' => '1']);
        } elseif ($ptn == '2') {
            $collect_working_times = collect([
                'mode' => Config::get('const.C005.missing_middle_return_time'),
                'time' => $record_datetime,
                'status' => Config::get('const.C012.missing_middle_return'),
                'note' => Config::get('const.MEMO_DATA.MEMO_DATA_NON'),
                'late' => '0',
                'Leave_early' => '0',
                'interval' => '0',
                'calc' => '0',
                'to_be_confirmed' => '0']);
        } else {
            // 不明データとして作成する
            $collect_working_times = collect([
                'mode' => Config::get('const.C005.missing_middle_return_time'),
                'time' => $record_datetime,
                'status' => Config::get('const.C012.unknown'),
                'note' => Config::get('const.MEMO_DATA.MEMO_DATA_004'),
                'late' => '0',
                'Leave_early' => '0',
                'to_be_confirmed' => '1',
                'interval' => '0']);
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
                $array_massegedata = array_add(['msg' -> $chkdata->user_name.Config::get('const.MSG_ERROR.not_setting_department_id')]);
            } else {
                $array_massegedata = array_add(['msg' -> Config::get('const.MSG_ERROR.not_setting_department_id_nouser')]);
            }
            $chk_result = false;
            Log::error('department_id error');
        }
        // 締日設定されているか
        if ($chkdata->closing == null) {
            $array_massegedata = array_add(['msg' -> Config::get('const.MSG_ERROR.not_setting_closing')]);
            $chk_result = false;
            Log::error('closing error');
        }
        // 時間単位設定されているか
        if ($chkdata->time_unit == null) {
            $array_massegedata = array_add(['msg' -> Config::get('const.MSG_ERROR.not_setting_time_unit')]);
            $chk_result = false;
            Log::error('time_unit error');
        }
        // 時間の丸め設定されているか
        if ($chkdata->time_rounding == null) {
            $array_massegedata = array_add(['msg' -> Config::get('const.MSG_ERROR.not_setting_time_rounding')]);
            $chk_result = false;
            Log::error('time_rounding error');
        }
        // 期首月設定されているか
        if ($chkdata->beginning_month == null) {
            $array_massegedata = array_add(['msg' -> Config::get('const.MSG_ERROR.not_setting_beginning_month')]);
            $chk_result = false;
            Log::error('beginning_month error');
        }
        Log::error('chkSettingData end');

        return $chk_result;
            
    }


}
