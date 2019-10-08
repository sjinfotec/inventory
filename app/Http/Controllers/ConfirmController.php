<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConfirmController extends Controller
{

    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        return view('confirm');
    }

    /**
     * 承認ルート作成処理
     *
     * @return void
     */
    public function settingRoot()
    {
        return view('confirm');
    }
}
