<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\InventoryA;
use App\InventoryZ;
use App\StockA;
use App\Http\Controllers\ViewInventoryController;


/**
 * 共通API
 *
 *          情報取得                                : get       : stock
 * 
 *
 */
class ApiCommonController extends Controller
{

    protected $table_inventory_a = 'inventory_a';
    protected $table_inventory_z = 'inventory_z';
    protected $table_stock_a = 'stock_a';

    private $array_messagedata = array();



   


    /**
     * 日付のフォーマット YYYY年MM月DD日（WEEK）
     *
     * @param [type] $dt
     * @param [type] $format
     * @return array
     */
    public function getYMDWeek($dt){
        // フォーマット 2019年10月01日(火)
        $array_week = array();
        $array_week[] = Config::get('const.WEEK_KANJI.sun');
        $array_week[] = Config::get('const.WEEK_KANJI.mon');
        $array_week[] = Config::get('const.WEEK_KANJI.tue');
        $array_week[] = Config::get('const.WEEK_KANJI.wed');
        $array_week[] = Config::get('const.WEEK_KANJI.thu');
        $array_week[] = Config::get('const.WEEK_KANJI.fri');
        $array_week[] = Config::get('const.WEEK_KANJI.sat');
        $target_date = new Carbon($dt);
        $date_name = date_format($target_date, 'Y年m月d日')."（".$array_week[$target_date->dayOfWeek]."）";
//        $date_name = date_format($target_date, 'Ymd');
        return $date_name;
    }



    
 

    
}
