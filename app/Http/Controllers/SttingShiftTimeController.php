<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SttingShiftTimeController extends Controller
{
    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        return view('setting_shift_time');
    }
}
