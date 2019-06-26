<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\WorkTime;
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
    public function show(){
        $work_time = new WorkTime();
        
    }
}
