<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class ExcelSpreadsheetController extends Controller
{

    /** 
     * excelファイルの行データを配列に設定する
     *
     */
    public function getExcelRowData($params){
        // cellnodata セルに値が無い時に値を指定
        // calcresult trueを指定するとセル内の計算結果を返して、falseなら計算式が表示される
        // cellformat 各セルのフォーマットを利用するかしないか
        // indexkey trueだと、A1形式がキーになった配列。falseだと0から始まる数字がキーなった配列
        Log::debug('getExcelRowData file_name = '.$params['filename']);
        $filename = $params['filename'];
        $cellnodata = $params['cellnodata'];
        $calcresult = $params['calcresult'];
        $cellformat = $params['cellformat'];
        $indexkey = $params['indexkey'];
        try {
            $reader = new Xlsx();
            Log::debug('getExcelRowData file_name = '.Config::get('const.FILEPATH.import_path')."/".$filename);
            $spreadsheet = $reader->load(Config::get('const.FILEPATH.import_path')."/".$filename);
            return $spreadsheet->getActiveSheet()->toArray($cellnodata, $calcresult, $cellformat, $indexkey);
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.data_insert_error'));
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }
    //
}
