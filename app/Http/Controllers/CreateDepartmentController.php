<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CreateDepartmentController extends Controller
{
    const SUCCESS = 0;
    const FAILED = 1;

    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        return view('create_department');
    }
}
