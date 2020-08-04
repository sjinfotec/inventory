<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class AttendanceLogController extends Controller
{

    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        $user = Auth::user();
        return view('attendance_log', compact('user'));
    }
}
