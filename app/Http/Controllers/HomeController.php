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
        // 打刻端末インストールダウンロード情報
        $login_user_code = $authusers->code;
        // Log::debug('HomeController index in ');
        $login_user_code_4 = substr($login_user_code, 0 ,4);
        $apicommon = new ApiCommonController();
        $account_id = $login_user_code_4;
        $downloadfile_no = Config::get('const.FILE_DOWNLOAD_NO.file5');
        $downloadfile_cnt = 0;
        $array_impl_isExistDownloadLog = array (
            'account_id' => $account_id,
            'downloadfile_no' => $downloadfile_no,
            'downloadfile_date' => null,
            'downloadfile_time' => null,
            'downloadfile_name' => null,
            'downloadfile_cnt' => $downloadfile_cnt
        );
        $isExistDownloadLogs = $apicommon->isExistDownloadLog($array_impl_isExistDownloadLog);
        $isexistdownload = "0";
        if ($isExistDownloadLogs) {
            $isexistdownload = "1";
        }
        // 設定項目要否判定
        $settingtable = $apicommon->getNotSetting();
        return view('home',
            compact(
                'authusers',
                'isexistdownload',
                'settingtable'
            ));
    }
}
