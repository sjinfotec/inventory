<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\WorkTime;
use App\Http\Controllers\WorkingTimeDateCalcController;
use Carbon\Carbon;

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
        $departmentcodefrom = $request->departmentcodefrom;
        $departmentcodeto = $request->departmentcodeto;
        $usercodefrom = $request->usercodefrom;
        $usercodeto = $request->usercodeto;
        $datefrom = $request->datefrom;
        $dateto = $request->dateto;
        // 打刻時刻を取得
        $work_time = new WorkTime();
        $work_time->setDepartmentcodefromAttribute($departmentcodefrom);
        $work_time->setDepartmentcodetoAttribute($departmentcodeto);
        $work_time->setUsercodefromAttribute($usercodefrom);
        $work_time->setUsercodetoAttribute($usercodeto);
        $work_time->setDatefromAttribute($datefrom);
        $work_time->setDatetoAttribute($dateto);
        $work_time->getWorkingTimeData();
        $results = DB::table('work_times')->get(); // 試しに全部取得
        return $results;
    }
}
