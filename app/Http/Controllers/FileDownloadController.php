<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileDownloadController extends Controller
{
    //
    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        return view('file_download');
    }
}
