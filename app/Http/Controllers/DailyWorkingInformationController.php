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
        $massegedata = "";
        $work_time = new WorkTime();
        $work_time->setDepartmentcodeAttribute($departmentcode);
        $work_time->setUsercodeAttribute($usercode);
        $work_time->setDatefromAttribute($datefrom);
        $work_time->setDatetoAttribute($dateto);
        $chk_result = $work_time->chkWorkingTimeData();
        if ($chk_result) {
            $work_time_result = $work_time->getWorkTimes();
            if(isset($work_time_result)){
                $calc_results = $this->calcDayTime($work_time->getWorkTimes());
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
     * 日次集計計算 
     * 
     *      取得した打刻時刻をもとに、ユーザー単位に 日次集計計算する
     *
     * @param  array worktimes 取得打刻時刻
     * @return void
     */
    private function calcDayTime($worktimes){
        // ユーザー単位処理
        foreach ($worktimes as $result) {
            $kubun = $result->kubun;
            $element_id = $result->element_id;
            $filtered = $collections->where('kubun',$kubun);
            $filtered = $filtered->where('element_id',$element_id);
            $sorted_collects = $filtered->sortBy('item');
            if($result->input_type == 1){           // チェックボックス
                foreach ($sorted_collects as $collect) {
                    for ($i=0; $i < 10; $i++) { 
                        if($i == $collect->item){       // result中に1〜10のチェックフラグ用変数を用意する
                            $flag = "is_check_".$i;     // チェックボックスにチェックしているものはis_check_1 : 1〜　と設定していく
                            $result->$flag = 1;
                            break;
                        }
                    }
                }
            }elseif ($result->input_type == 2) {        // ラジオ
                foreach ($sorted_collects as $collect) {
                    $result->is_check = $collect->answer;
                    break;
                }
            }elseif ($result->input_type == 3) {        // 点数
                foreach ($sorted_collects as $collect) {
                    if(isset($collect->score)){
                        $result->input_score = $collect->score;
                    }
                    break;
                }
            }
        }
    
        return DB::table('users')->get();
    }
}
