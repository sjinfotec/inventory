<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DemandController extends Controller
{

    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        return view('demand');
    }

    /**
     * 申請処理
     *
     * @return void
     */
    public function makeDemand()
    {
        return view('demand');
    }

}
