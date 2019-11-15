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


class SettingCalcController extends Controller
{
    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        return view('setting_calc');
    }

    /**
     * 詳細取得
     *
     * @param Request $request
     * @return void
     */
    public function get(Request $request){
        $target_year = $request->year;
        $setting = new Setting();
        $setting->setFiscalyearAttribute($target_year);
        $details = $setting->getDetails();
        return $details;
    }

    /**
     * 登録
     *
     * @param Request $request
     * @return void
     */
  public function store(Request $request){
        // request
        $fiscal_year = $request->settings["year"];
        $b_month = $request->settings["biginningMonth"];
        $calc_auto_time = $request->settings["calc_auto_time"];
        $oneMonthTotal = $request->settings["oneMonthTotal"];
        $year_total = $request->settings["yearTotal"];
        $oneMonthTotal_sp = $request->settings["sp_oneMonthTotal"];
        $year_total_sp = $request->settings["sp_yearTotal"];
        $ave_2_6 = $request->settings["sp_ave_2_6"];
        $interval = $request->settings["sp_interval"];
        $count_sp = $request->settings["sp_count"];
        $up_times = $request->settings["upTime"];
        $closing_date = $request->settings["closingDate"];
        $time_rounds = $request->settings["timeround"];
        $time_units = $request->settings["timeunit"];
        $converts = array();
        $response = collect();
    
        $result = $this->insert(
            $fiscal_year,
            $b_month,
            $calc_auto_time,
            $oneMonthTotal,
            $year_total,
            $oneMonthTotal_sp,
            $year_total_sp,
            $ave_2_6,
            $interval,
            $count_sp,
            $up_times,
            $closing_date,
            $time_rounds,
            $time_units
        );

        if($result){
        }else{
            return false;
        }
    
    }

    /**
     * 書き込み
     *
     * @param [type] $year
     * @param [type] $b_month
     * @param [type] $calc_auto_time
     * @param [type] $oneMonthTotal
     * @param [type] $year_total
     * @param [type] $ave_2_6
     * @param [type] $interval
     * @param [type] $up_times
     * @param [type] $closing_date
     * @param [type] $time_rounds
     * @param [type] $time_units
     * @return void
     */
    private function insert(
        $fiscal_year,
        $b_month,
        $calc_auto_time,
        $oneMonthTotal,
        $year_total,
        $oneMonthTotal_sp,
        $year_total_sp,
        $ave_2_6,
        $interval,
        $count_sp,
        $up_times,
        $closing_date,
        $time_rounds,
        $time_units
    ){
        $setting = new Setting();
        $systemdate = Carbon::now();
        $user = Auth::user();
        $user_code = $user->code;

        DB::beginTransaction();
        try{
            $setting->setFiscalyearAttribute($fiscal_year);
            // 既に存在した場合削除新規する
            $is_exists = $setting->isExistsSetting();    
            if($is_exists){
                $setting->delSetting();
            }
            $setting->setCalcautotimeAttribute($calc_auto_time);
            $setting->setMax1MonthtotalAttribute($oneMonthTotal);
            $setting->setMax2MonthtotalAttribute(Config::get('const.C021.manthly_alert_error_2'));    // 未使用
            $setting->setMax3MonthtotalAttribute(Config::get('const.C021.manthly_alert_error_3'));    // 未使用
            $setting->setMax6MonthtotalAttribute(0);    // 未使用
            $setting->setMax12MonthtotalAttribute($year_total);
            $setting->setAve26timespAttribute($ave_2_6);
            $setting->setMax12MonthtotalspAttribute($year_total_sp);
            $setting->setMax1MonthtotalspAttribute($oneMonthTotal_sp);
            $setting->setBeginningmonthAttribute($b_month);
            $setting->setCountspAttribute($count_sp);
            $setting->setIntervalAttribute($interval);
            $setting->setCreateduserAttribute($user_code);
            $setting->setCreatedatAttribute($systemdate);
        
            for ($i=0; $i < 12; $i++) {
                $month = $i + 1;
                $setting->setFiscalmonthAttribute($month);
                $year = $fiscal_year;
                // 期首月基準で year 設定
                if($month >= $b_month){
                    $setting->setYearAttribute($year);
                }else{
                    $setting->setYearAttribute($year + 1);
                }
                if(isset($closing_date[$i])){
                    $setting->setClosingAttribute($closing_date[$i]);
                }else{
                    $setting->setClosingAttribute(null);
                }
                if(isset($up_times[$i])){
                    $setting->setUplimittimeAttribute($up_times[$i]);
                }else{
                    $setting->setUplimittimeAttribute(null);
                }
                if(isset($time_units[$i]) || !empty($time_units[$i])){
                    $setting->setTimeunitAttribute($time_units[$i]);
                }else{
                    $setting->setTimeunitAttribute(null);
                }
                if(isset($time_rounds[$i]) || !empty($time_rounds[$i])){
                    $setting->setTimeroundingAttribute($time_rounds[$i]);
                }else{
                    $setting->setTimeroundingAttribute(null);
                }
                $setting->insertSettings();
            }
            DB::commit();
            return true;

        }catch(\PDOException $e){
            DB::rollBack();
            return false;
        }
    }

}
