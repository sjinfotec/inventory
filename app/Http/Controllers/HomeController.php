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
        $login_user_code = $authusers->code;
        $accountid = substr($login_user_code, 0 ,4);
        $edition = Config::get('const.EDITION.EDITION');
        $apicommon = new ApiCommonController();
        // 打刻端末インストールダウンロード情報
        $downloadfile_no = Config::get('const.FILE_DOWNLOAD_NO.file5');
        $downloadfile_cnt = 0;
        $array_impl_isExistDownloadLog = array (
            'account_id' => $accountid,
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
        // 'authusers',
        return view('home',
            compact(
                'authusers',
                'accountid',
                'edition',
                'isexistdownload',
                'settingtable'
            ));
    }
}
