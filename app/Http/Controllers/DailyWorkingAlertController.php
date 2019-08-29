<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DailyWorkingAlertController extends Controller
{

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
        $this->collect_massegedata = collect();

        $working_time_dates = null;

        return response()->json(['calcresults' => $working_time_dates, 'massegedata' => $this->array_massegedata]);
    }

}
