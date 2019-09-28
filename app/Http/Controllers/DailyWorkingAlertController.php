<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\WorkTime;
use App\Calendar;

class DailyWorkingAlertController extends Controller
{

    private $array_messagedata = array();

    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        return view('daily_working_alert');
    }

    /**
     * 日次アラート表示 
     *
     * @return void
     */
    public function show(Request $request){

        // パラメータのチェック
        // datefromとdatetoがあるが、このメソッドではdatefrom=datetoであること
        Log::debug('------------- 日次アラート表示開始 ----------------');
        Log::debug('    パラメータ  $request->datefrom = '.$request->datefrom);
        Log::debug('    パラメータ  $request->employmentstatus = '.$request->employmentstatus);
        Log::debug('    パラメータ  $request->departmentcode = '.$request->departmentcode);
        Log::debug('    パラメータ  $request->usercode = '.$request->usercode);
        $calc_result = true;
        $add_result = true;
        // reqestクエリーセット
        // 指定日付はfromで受信
        $datefrom = null;
        if(isset($request->datefrom)){
            $datefrom = $request->datefrom;
        }
        // datetoをdatefromにし、datefromは１週間前に設定する
        $dateto = $datefrom;
        if(isset($datefrom)){
            $dt = new Carbon($datefrom.' 00:00:00');
            $dtfrom = date("Y-m-d H:i:s",strtotime($dt."-1 week"));
            $datefrom = date_format(new Carbon($dtfrom), 'Ymd');
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
        $this->collect_massegedata = collect();

        $working_time_alerts = null;
        $work_time_model = new WorkTime();

        // 打刻時刻を取得
        $work_time_model->setParamDatefromAttribute($datefrom);
        $work_time_model->setParamDatetoAttribute($dateto);
        $work_time_model->setParamemploymentstatusAttribute($employmentstatus);
        $work_time_model->setParamDepartmentcodeAttribute($departmentcode);
        $work_time_model->setParamUsercodeAttribute($usercode);
        // パラメータのチェック
        // datefromとdatetoがあるが、このメソッドではdatefrom=datetoであること
        Log::debug('------------- パラメータのチェック ----------------');
        Log::debug('    パラメータ  datefrom = '.$datefrom);
        Log::debug('    パラメータ  dateto = '.$dateto);
        Log::debug('    パラメータ  employmentstatus = '.$employmentstatus);
        Log::debug('    パラメータ  departmentcode = '.$departmentcode);
        Log::debug('    パラメータ  usercode = '.$usercode);
        $chk_work_time = $work_time_model->chkWorkingTimeData();
        if ($chk_work_time) {
            Log::debug('------------- アラート開始　------------------');
            $working_time_alerts = $work_time_model->getAlertData($dateto);
            if (count($working_time_alerts) == 0) {
                $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_INFO.no_alert_data'));
            }
            Log::debug('------------- アラート終了　------------------');
            Log::debug('  $working_time_alerts count = '.count($working_time_alerts));
            Log::debug('  $array_messagedata count = '.count($this->array_messagedata));
        } else {
            Log::debug('------------- パラメータのチェック NG  ----------------');
            $this->array_messagedata[] = $work_time->getMassegedataAttribute();
        }

        $date_name = '';
        $calender_model = new Calendar();
        $calender_model->setDateAttribute(date_format(new Carbon($datefrom), 'Ymd'));
        $calendars = $calender_model->getCalenderDate();
        if (count($calendars) > 0) {
            foreach ($calendars as $result) {
                if (isset($result->date_name)) {
                    $date_name = $result->date_name;
                }
                break;
            }
        }


        return response()->json([
            'alertresults' => $working_time_alerts,
            'datename' => $date_name,
            Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]);
    }

}
