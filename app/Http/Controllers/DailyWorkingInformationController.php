<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\WorkTime;
use App\WorkingTimedate;
use App\GeneralCodes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class DailyWorkingInformationController extends Controller
{

    // コード値
    const C005_CODE = 'C005';
    const C005_ATTENDANCE_TIME = '1';
    private $general_codes;

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
        $this->general_codes = new GeneralCodes();

        // reqestクエリーセット
        $departmentcode = '';
        if(isset($request->departmentcode)){
            $departmentcode = $request->departmentcode;
        }
        $usercode = '';
        if(isset($request->usercode)){
            $usercode = $request->usercode;
        }
        $datefrom = '';
        if(isset($request->datefrom)){
            $datefrom = $request->datefrom;
        }
        $dateto = '';
        if(isset($request->dateto)){
            $dateto = $request->dateto;
        }
        // 打刻時刻を取得
        $massegedata = "";
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
                foreach ($work_time_result as $result) {
                    Log::debug('calcWorkingTimeDates.' + $result->mode);
                    // ユーザーの出勤・退勤・中抜・戻り時刻の確定処理
                    $kubun = $this->getWorkingTimeDates($result->record_time, $result->mode);
                    Log::debug('calcWorkingTimeDates.' + $kubun);
                }
                //$put_results = $this->calcWorkingTimeDates($work_time_result);
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
                $massegedata .= "該当する勤務時間は見つかりませんでした。";
            }
        } else {
            $calc_results = null;
            $massegedata .= $work_time->getMassegedataAttribute();
        }
        return response()->json(['calc_results' => $calc_results, 'massegedata' => $massegedata]);
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
        $before_user_code = '';
        // ユーザー単位処理
        foreach ($worktimes as $result) {
            Log::debug('calcWorkingTimeDates.' + $result->mode);
            // ユーザーの出勤・退勤・中抜・戻り時刻の確定処理
            $kubun = $this->getWorkingTimeDates($result->record_time, $result->mode);
            Log::debug('calcWorkingTimeDates.' + $kubun);
        }
    
        return DB::table('users')->get();

    }

    /**
     * 出勤・退勤・中抜・戻り時刻の確定処理
     *
     *
     * @return sql取得結果
     */
    private function getWorkingTimeDates($record_time, $mode){

        $array_working_times = array();
        if($mode == $this->general_codes->where('identification_id',C005_CODE)->where('code',C005_ATTENDANCE_TIME)) {
            $array_working_times = array_add(['mode' => $mode, 'time' => $record_time]);
        }

        return $array_working_times;

    }

}
