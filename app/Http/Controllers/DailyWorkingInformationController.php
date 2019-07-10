<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\WorkTime;
use App\Http\Controllers\WorkingTimeDateCalcController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class DailyWorkingInformationController extends Controller
{
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
        $work_time = new WorkTime();
        $work_time->setDepartmentcodeAttribute($departmentcode);
        $work_time->setUsercodeAttribute($usercode);
        $work_time->setDatefromAttribute($datefrom);
        $work_time->setDatetoAttribute($dateto);
        $result = $work_time->chkWorkingTimeData();
        if (!$result) {
            return $result;
        }
        
        $results = DB::table('work_times')->get(); // 試しに全部取得
        return $results;
    }
}
