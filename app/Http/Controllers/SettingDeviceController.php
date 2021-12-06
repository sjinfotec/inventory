<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Setting;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreSettingPost;
use App\Http\Controllers\ApiCommonController;

class SettingDeviceController extends Controller
{
    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        $authusers = Auth::user();
        $login_user_code = $authusers->code;
        $accountid = $authusers->account_id;

        return view('setting_device',
            compact(
                'authusers'
            ));
    }
    //
}
