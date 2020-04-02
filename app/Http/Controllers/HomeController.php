<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $authusers = Auth::user();
        $loginusercode = $authusers->code;
        $generaluser = Config::get('const.C025.general_user');
        $generalapproveruser = Config::get('const.C025.general_approver__user');
        $adminuser = Config::get('const.C025.admin_user');
        $distribution = Config::get('const.DISTRIBUTION.DISTRIBUTION');
        $distribution43z = Config::get('const.DISTRIBUTION_VALUE.43z');
        $distributionssjjoo = Config::get('const.DISTRIBUTION_VALUE.SSJJOO');
        $edition = Config::get('const.EDITION.EDITION');
        $editiondemo = Config::get('const.EDITION_VALUE.DEMO');
        $editiontrial = Config::get('const.EDITION_VALUE.TRIAL');
        $editioncroud = Config::get('const.EDITION_VALUE.CROUD');
        $editionssjjoo = Config::get('const.EDITION_VALUE.SSJJOO');
        $editionclient = Config::get('const.EDITION_VALUE.CLIENT');
        return view('home',
            compact(
                'authusers',
                'generaluser',
                'generalapproveruser',
                'adminuser',
                'distribution',
                'distribution43z',
                'distributionssjjoo',
                'edition',
                'editiondemo',
                'editiontrial',
                'editioncroud',
                'editionssjjoo',
                'editionclient'
            ));
    }
}
