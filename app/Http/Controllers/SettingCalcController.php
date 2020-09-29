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


class SettingCalcController extends Controller
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
            'account_id' => $accountid,
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

        return view('setting_calc',
            compact(
                'authusers',
                'isexistdownload',
                'settingtable'
            ));
    }

    /**
     * 詳細取得
     *
     * @param Request $request
     * @return void
     */
    public function get(Request $request){
        $this->array_messagedata = array();
        $details = new Collection();
        $result = true;
        $user = Auth::user();
        $login_user_code = $user->code;
        $login_account_id = $user->account_id;
        try{
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $details,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            if (!isset($params['year'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "year", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $details,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $details = $this->getDetailFunc($params);
            return response()->json(
                ['result' => true, 'details' => $details,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 詳細取得
     *
     * @param Request $request
     * @return void
     */
    public function getDetailFunc($params){
        $target_year = $params['year'];
        $authuser = Auth::user();
        $login_user_code = $authuser->code;
        $login_account_id = $authuser->account_id;
        try{
            $setting_model = new Setting();
            $setting_model->setParamAccountidAttribute($login_account_id);
            $setting_model->setFiscalyearAttribute($target_year);
            $details = $setting_model->getSettingDetails();
            return $details;
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 登録
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request){
        $this->array_messagedata = array();
        $result = true;
        try {
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            if (!isset($params['form'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "form", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $data = $params['form'];
            $fiscal_year = $data["year"];
            $b_month = $data["biginningMonth"];
            $calc_auto_time = $data["calc_auto_time"];
            $oneMonthTotal = $data["oneMonthTotal"];
            $twoMonthTotal = $data["twoMonthTotal"];
            $threeMonthTotal = $data["threeMonthTotal"];
            $year_total = $data["yearTotal"];
            $oneMonthTotal_sp = $data["sp_oneMonthTotal"];
            $year_total_sp = $data["sp_yearTotal"];
            $ave_2_6 = $data["sp_ave_2_6"];
            $interval = $data["sp_interval"];
            $count_sp = $data["sp_count"];
            $up_times = $data["upTime"];
            $closing_date = $data["closingDate"];
            $time_rounds = $data["timeround"];
            $time_units = $data["timeunit"];

            $result = $this->insert(
                $fiscal_year,
                $b_month,
                $calc_auto_time,
                $oneMonthTotal,
                $twoMonthTotal,
                $threeMonthTotal,
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

            return response()->json(
                ['result' => $result,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
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
        $twoMonthTotal,
        $threeMonthTotal,
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
        $authuser = Auth::user();
        $user_code = $authuser->code;
        $login_account_id = $authuser->account_id;

        DB::beginTransaction();
        try{
            $setting->setParamAccountidAttribute($login_account_id);
            $setting->setFiscalyearAttribute($fiscal_year);
            // 既に存在した場合削除新規する
            $is_exists = $setting->isExistsSetting();    
            if($is_exists){
                $setting->delSetting();
            }
            $setting->setAccountidAttribute($login_account_id);
            $setting->setCalcautotimeAttribute($calc_auto_time);
            $setting->setMax1MonthtotalAttribute($oneMonthTotal);
            $setting->setMax2MonthtotalAttribute($twoMonthTotal);
            $setting->setMax3MonthtotalAttribute($threeMonthTotal);
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

        }catch(\PDOException $pe){
            DB::rollBack();
            throw $pe;
        }catch(\Exception $e){
            DB::rollBack();
            Log::error($e->getMessage());
            throw $e;
        }
    }

}
