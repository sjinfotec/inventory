<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ApiCommonController;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        //$authusers = Auth::user();

        return view('home',
            //compact(
                //'authusers'
            //)
        );
    }
    public function contents_select()
    {
        $dataarr = array(
            "selecthtml" => "select_cnt",
        );
        return view('home',
            [
                'dataarr' => $dataarr
            ]
        );
    }
}
