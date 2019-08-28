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

}
