<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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

    public function get(Request $request){
        $target_year = $request->year;
        $setting = new Setting();
        $setting->setFiscalyearAttribute($target_year);
        $details = $setting->getDetails();
        return $details;
    }

    public function store(StoreSettingPost $request){
        // request
        $year = $request->year;
        $b_month = $request->biginningMonth;
        $three_month_total = $request->threeMonthTotal;
        $six_month_total = $request->sixMonthTotal;
        $year_total = $request->yaerTotal;
        $interval = $request->interval;
        $up_times = $request->upTime;
        $closing_date = $request->closingDate;
        $time_rounds = $request->timeround;
        $time_units = $request->timeunit;
        $converts = array();
        $response = collect();
    
        $result = $this->insert($year,$b_month,$three_month_total,$six_month_total,$year_total,
                                $interval,$up_times,$closing_date,$time_rounds,$time_units);

        if($result){
        }else{
            return false;
        }
    
    }

    /**
     * 登録
     *
     * @param [type] $year
     * @param [type] $b_month
     * @param [type] $three_month_total
     * @param [type] $six_month_total
     * @param [type] $year_total
     * @param [type] $interval
     * @param [type] $up_times
     * @param [type] $closing_date
     * @param [type] $time_rounds
     * @param [type] $time_units
     * @return void
     */
    private function insert($year,$b_month,$three_month_total,$six_month_total,$year_total,
                                $interval,$up_times,$closing_date,$time_rounds,$time_units){
        $setting = new Setting();
        $systemdate = Carbon::now();
        $user = Auth::user();
        $user_code = $user->code;

        DB::beginTransaction();
        try{
            $setting->setFiscalyearAttribute($year);
            // 既に存在した場合削除新規する
            $is_exists = $setting->isExistsSetting();    
            if($is_exists){
                $setting->delSetting();
            }
            $setting->setMax3MonthtotalAttribute($three_month_total);
            $setting->setMax6MonthtotalAttribute($six_month_total);
            $setting->setMax12MonthtotalAttribute($year_total);
            $setting->setBeginningmonthAttribute($b_month);
            $setting->setIntervalAttribute($interval);
            $setting->setYearAttribute($year);
            $setting->setCreateduserAttribute($user_code);
            $setting->setCreatedatAttribute($systemdate);
        
            for ($i=0; $i < 12; $i++) {
                $month = $i + 1;
                $setting->setFiscalmonthAttribute($month);
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
