<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use App\Http\Controllers\ApiCommonController;
use App\DownloadLog;

class FileDownloadController extends Controller
{
    private $array_messagedata = array();
    //
    /**
     * 初期処理
     *
     * @return void
     */
    public function index(Request $request)
    {
        // パラメータチェック
        if (isset($request->filekbn)) {
            return $this->getfileDownload($request);
        } else {
            $authusers = Auth::user();
            $login_user_code = $authusers->code;
            $account_id = $authusers->account_id;
            $edition = Config::get('const.EDITION.EDITION');
            // 設定項目要否判定
            $apicommon = new ApiCommonController();
            $settingtable = $apicommon->getNotSetting();
            // 打刻端末インストールダウンロード情報
            $array_downloadfile_no = array();
            $array_downloadfile_no[] = Config::get('const.FILE_DOWNLOAD_NO.file5');
            $array_downloadfile_no[] = Config::get('const.FILE_DOWNLOAD_NO.file6');
            $downloadfile_cnt = 0;
            $array_impl_isExistDownloadLog = array (
                'account_id' => $account_id,
                'array_downloadfile_no' => $array_downloadfile_no,
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
            return view('file_download',
            compact(
                'authusers',
                'isexistdownload'
            ));
        }
    }

    /** ダウンロード
     *
     * @return list results
     */
    public function getfileDownload($request){
        $filekbn =  '';
        $filePath = '';
        $fileName = '';
        $headers = '';
        $disposition = '';
        $array_file = array();
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file1');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file2');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file3');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file4');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file5');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file6');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file7');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file8');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file9');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file10');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file11');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file12');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file13');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file14');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file15');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file16');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file17');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file18');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file19');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file20');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file21');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file22');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file23');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file24');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file25');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file26');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file27');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file28');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file29');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file30');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file31');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file32');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file33');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file34');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file35');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file36');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file37');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file38');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file39');
        $array_file[] = Config::get('const.FILE_DOWNLOAD_NAME.file40');

        try {
            // パラメータチェック
            $filekbn = $request->filekbn;
            if ($filekbn > 0) {
                $filePath = Config::get('const.FILE_DOWNLOAD_PATH.STORAGE').$array_file[$filekbn-1];
                $fileName = $array_file[$filekbn-1];
                $mimeType = Storage::mimeType($filePath);
                $headers = [['Content-Type' => $mimeType]];
                $disposition = 'attachment';        // 画像ファイルを表示せずダウンロード
                // ダウンロード履歴登録
                $authusers = Auth::user();
                // 打刻端末インストールダウンロード情報
                $login_user_code = $authusers->code;
                $login_account_id = $authusers->account_id;
                $apicommon = new ApiCommonController();
                $account_id = $login_account_id;
                $array_downloadfile_no = array();
                $array_downloadfile_no[] = $filekbn;
                $downloadfile_cnt = 0;
                $array_impl_getDownloadLog = array (
                    'account_id' => $login_account_id,
                    'array_downloadfile_no' => $array_downloadfile_no,
                    'downloadfile_date' => null,
                    'downloadfile_time' => null,
                    'downloadfile_name' => null,
                    'downloadfile_cnt' => $downloadfile_cnt
                );
                $details = $apicommon->getDownloadLog($array_impl_getDownloadLog);
                $r_cnt = 0;
                foreach($details as $item) {
                    $downloadfile_cnt = $item->downloadfile_cnt;
                    $r_cnt++;
                    break;
                }
                $systemdate = Carbon::now();
                $current_date =  $systemdate;
                $target_ymd = $current_date->format('Ymd');
                $target_his = $current_date->format('His');
                $downloadfile_cnt++;
                $downloadlog_model = new DownloadLog();
                if ($r_cnt == 0) {
                    $downloadlog_model->setAccountidAttribute($login_account_id);
                    $downloadlog_model->setDownloadfilenoAttribute($filekbn);
                    $downloadlog_model->setDownloadfiledateAttribute($target_ymd);
                    $downloadlog_model->setDownloadfiletimeAttribute($target_his);
                    $downloadlog_model->setDownloadfilenameAttribute($fileName);
                    $downloadlog_model->setDownloadfilecntAttribute($downloadfile_cnt);
                    $downloadlog_model->setDownloadfileaccountidAttribute($login_user_code);
                    $downloadlog_model->setCreateduserAttribute($login_user_code);
                    $downloadlog_model->setCreatedatAttribute($systemdate);
                    $downloadlog_model->insertDownloadlog();
                } else {
                    $downloadlog_model->setParamAccountidAttribute($login_account_id);
                    $downloadlog_model->setParamDownlodfilenoAttribute($array_downloadfile_no);
                    $array_update = [
                        'downloadfile_date' => $target_ymd,
                        'downloadfile_time' => $target_his,
                        'downloadfile_name' => $fileName,
                        'downloadfile_cnt' => $downloadfile_cnt,
                        'downloadfile_account_id' => $login_user_code,
                        'updated_user' => $login_user_code,
                        'updated_at' => $systemdate
                    ];
                    $downloadlog_model->updDownloadlog($array_update);
                }
            }

            return Storage::download($filePath, $fileName, $headers, $disposition);
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.file_download_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }

}
