<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MonthlyWorkingInformationController extends Controller
{
    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        return view('monthly_working_information');
    }
}
