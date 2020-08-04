<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

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
            return view('file_download',
                compact(
                    'authusers'
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
            }

            return Storage::download($filePath, $fileName, $headers, $disposition);
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.file_download_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }

}
