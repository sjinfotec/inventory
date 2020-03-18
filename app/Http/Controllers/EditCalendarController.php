<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ApiCommonController;
use App\WorkingTimeTable;
use App\Calendar;
use App\Setting;

class EditCalendarController extends Controller
{
    // メッセージ
    private $array_messagedata = array();

    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        return view('setting_calendar');
    }

    /**
     * データ取得
     *
     * @param Request $request
     * @return array
     */
    public function getDetail(Request $request){
        $this->array_messagedata = array();
        $details = array();
        $detail_dates = array();
        $result = true;
        $year = null;
        $month = null;
        $employmentstatus = null;
        $departmentcode = null;
        $usercode = null;
        try {
            // パラメータチェック
            $params = array();
            // 設定されていない場合
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $details, 'detail_dates' => $detail_dates,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            // 設定されていない場合
            if (!isset($params['dateyear'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "dateyear", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $details, 'detail_dates' => $detail_dates,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            // 設定されていない場合
            if (!isset($params['datemonth'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "datemonth", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $details, 'detail_dates' => $detail_dates,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            if (isset($params['employmentstatus'])) {
                if ($params['employmentstatus'] != "") {
                    $employmentstatus = $params['employmentstatus'];
                }
            }
            if (isset($params['departmentcode'])) {
                if ($params['departmentcode'] != "") {
                    $departmentcode = $params['departmentcode'];
                }
            }
            if (isset($params['usercode'])) {
                if ($params['usercode'] != "") {
                    $usercode = $params['usercode'];
                }
            }
            $year = $params['dateyear'];
            $month = $params['datemonth'];
            // 検索日付期間設定
            $search_y_m = $year."".$month;
            $dt1 = new Carbon($search_y_m.'15');
            $make_fromdate = $dt1->startOfMonth();
            $dt2 = new Carbon($search_y_m.'15');
            $make_todate = $dt2->endOfMonth();
            $calendar_model = new Calendar();
            $calendar_model->setParamdepartmentcodeAttribute($departmentcode);
            $calendar_model->setParamemploymentstatusAttribute($employmentstatus);
            $calendar_model->setParamusercodeAttribute($usercode);
            $calendar_model->setParamfromdateAttribute($make_fromdate->format('Ymd'));
            $calendar_model->setParamtodateAttribute($make_todate->format('Ymd'));
            $results = $calendar_model->getDetail();
            $current_user_code = null;
            $current_item = null;
            $array_user_data = array();
            $array_user_date_data = array();
            $set_detail_dates = false;
            foreach($results as $item) {
                if($current_user_code == null) {$current_user_code = $item->user_code;}
                if($current_item == null) {$current_item = $item;}
                if($current_user_code == $item->user_code) {
                    if (!$set_detail_dates) {
                        $detail_dates[] = array(
                            'date' => $item->date,
                            'date_name' => $item->date_name
                        );
                    }
                    $array_user_date_data[] = array(
                        'date' => $item->date,
                        'weekday_kubun' => $item->weekday_kubun,
                        'business_kubun' => $item->business_kubun,
                        'holiday_kubun' => $item->holiday_kubun,
                        'date_name' => $item->date_name,
                        'md_name' => $item->md_name,
                        'public_holidays_name' => $item->public_holidays_name,
                        'business_kubun_name' => $item->business_kubun_name,
                        'holiday_kubun_name' => $item->holiday_kubun_name
                    );
                } else {
                    if (count($detail_dates) > 0 && !$set_detail_dates) {
                        $set_detail_dates = true;
                    }
                    $array_user_data[] = array(
                        'department_code' => $current_item->department_code,
                        'employment_status' => $current_item->employment_status,
                        'user_code' => $current_item->user_code,
                        'department_name' => $current_item->department_name,
                        'employment_name' => $current_item->employment_name,
                        'user_name' => $current_item->user_name,
                        'array_user_date_data' => $array_user_date_data
                    );
                    $current_user_code = $item->user_code;
                    $current_item = $item;
                    $array_user_date_data = array();
                    $array_user_date_data[] = array(
                        'date' => $item->date,
                        'weekday_kubun' => $item->weekday_kubun,
                        'business_kubun' => $item->business_kubun,
                        'holiday_kubun' => $item->holiday_kubun,
                        'date_name' => $item->date_name,
                        'md_name' => $item->md_name,
                        'public_holidays_name' => $item->public_holidays_name,
                        'business_kubun_name' => $item->business_kubun_name,
                        'holiday_kubun_name' => $item->holiday_kubun_name
                    );
                }
            }
            // 残り
            if (count($array_user_date_data) > 0) {
                $array_user_data[] = array(
                    'department_code' => $current_item->department_code,
                    'employment_status' => $current_item->employment_status,
                    'user_code' => $current_item->user_code,
                    'department_name' => $current_item->department_name,
                    'employment_name' => $current_item->employment_name,
                    'user_name' => $current_item->user_name,
                    'array_user_date_data' => $array_user_date_data
                );
            }
            $details = $array_user_data;
            if (count($details) == 0) {
                $this->array_messagedata[] = Config::get('const.MSG_INFO.no_data');
                $result = false;
            }
            return response()->json(
                ['result' => $result, 'details' => $details, 'detail_dates' => $detail_dates,
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
     * UPDATE
     *
     * @param [type] $converts
     * @return void
     */
    public function fix(Request $request){
        $this->array_messagedata = array();
        $details = array();
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
            if (!isset($params['details'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "details", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            if (!isset($params['businessdays'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "businessdays", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            if (!isset($params['holidays'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "holidays", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $details = $params['details'];
            $businessdays = $params['businessdays'];
            $holidays = $params['holidays'];
            $converts = array();
            // details に入力された区分を上書き
            foreach ($details['array_user_date_data'] as $index => $detail) {
                $formated = new Carbon($detail['date']);
                $converts[$index]['date'] = $formated->format('Ymd');
                $converts[$index]['businessdays'] = $businessdays[$index];
                $converts[$index]['holidays'] = $holidays[$index];
            }
            // fixData implement
            $array_impl_fixData = array (
                'department_code' => $details['department_code'],
                'employment_status' => $details['employment_status'],
                'user_code' => $details['user_code'],
                'converts' => $converts
            );
            $this->fixData($array_impl_fixData);
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
     * 更新
     *
     * @param [type] $details
     * @return boolean
     */
    private function fixData($params){
        $systemdate = Carbon::now();
        $user = Auth::user();
        $user_code = $user->code;
        $calendar_model = new Calendar();

        DB::beginTransaction();
        try{
            $calendar_model->setParamdepartmentcodeAttribute($params['department_code']);
            $calendar_model->setParamemploymentstatusAttribute($params['employment_status']);
            $calendar_model->setParamusercodeAttribute($params['user_code']);
            $calendar_model->setUpdatedatAttribute($systemdate);

            foreach ($params['converts'] as $data) {
                $calendar_model->setParamfromdateAttribute($data['date']);
                $calendar_model->setDateAttribute($data['date']);
                $calendar_model->setBusinesskubunAttribute($data['businessdays']);
                $calendar_model->setHolidaykubunAttribute($data['holidays']);
                $calendar_model->updateCalendar();
            }
            DB::commit();

        }catch(\PDOException $pe){
            DB::rollBack();
            throw $pe;
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 初期設定
     *
     */
    public function init(Request $request){
        try {
            $this->array_messagedata = array();
            $details = array();
            $formdata = array();
            $result = true;
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
            if (!isset($params['ptn'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "ptn", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            if (!isset($params['dateyear'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "dateyear", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            // if (!isset($params['displaykbn'])) {
            //     Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "displaykbn", Config::get('const.LOG_MSG.parameter_illegal')));
            //     $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
            //     return response()->json(
            //         ['result' => false,
            //         Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            //     );
            // }
            $ptn = $params['ptn'];
            if ($ptn == Config::get('const.CALENDAR_PTN.ptn2')) {
                if (!isset($params['formdata'])) {
                    Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "formdata", Config::get('const.LOG_MSG.parameter_illegal')));
                    $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                    return response()->json(
                        ['result' => false,
                        Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                    );
                }
                $formdata = $params['formdata'];
            } else {
                $formdata = null;
            }
            $departmentcode = $params['departmentcode'];
            $employmentstatus = $params['employmentstatus'];
            $usercode = $params['usercode'];
            $dateyear = $params['dateyear'];
            // 設定されている場合
            $datemonth = null;
            if (isset($params['datemonth'])) {
                $datemonth = $params['datemonth'];
            }
            $displaykbn = null;
            if (isset($params['displaykbn'])) {
                $displaykbn = $params['displaykbn'];
            }
            // showCalc implement
            $array_impl_initCalendar = array (
                'ptn' => $ptn,
                'departmentcode' => $departmentcode,
                'employmentstatus' => $employmentstatus,
                'usercode' => $usercode,
                'dateyear' => $dateyear,
                'datemonth' => $datemonth,
                'displaykbn' => $displaykbn,
                'formdata' => $formdata
            );
            $this->initCalendar($array_impl_initCalendar);

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
     * 初期設定実行
     *
     */
    public function initCalendar($params){

        $ptn = $params['ptn'];
        $employmentstatus = $params['employmentstatus'];
        $departmentcode = $params['departmentcode'];
        $usercode = $params['usercode'];
        $dateyear = $params['dateyear'];
        $datemonth = $params['datemonth'];
        $displaykbn = $params['displaykbn'];
        $formdata = $params['formdata'];
        $fromdate = "";
        $todate = "";
        DB::beginTransaction();
        try{
            $calendar_model = new Calendar();
            $setting_model = new Setting();
            if (isset($datemonth)) {
                // if ($displaykbn == Config::get('const.C034.closing')) {
                //     // 締日基準
                //     // 前月締日取得
                //     $setting_model->setParamYearAttribute($dateyear);
                //     $dt = new Carbon($dateyear.$datemonth."15");
                //     $beforedate = $dt->subMonth();
                //     Log::debug('$beforedate = '.$beforedate);
                //     $setting_model->setParamFiscalmonthAttribute(date_format($beforedate, 'm'));
                //     $begining = $setting_model->getMonthClosing();
                //     if (isset($begining)) {
                //         Log::debug($dateyear.$datemonth.str_pad($begining, 2, "0", STR_PAD_LEFT));
                //         $dt = new Carbon($dateyear.$datemonth.str_pad($begining, 2, "0", STR_PAD_LEFT));
                //         $fromdate = $dt->addDay();
                //     } else {
                //         $this->array_messagedata[] =  array(Config::get('const.RESPONCE_ITEM.message') =>Config::get('const.MSG_ERROR.not_setting_closing'));
                //         return false;
                //     }
                //     // 当月締日取得
                //     $setting_model->setParamYearAttribute($dateyear);
                //     $dt = new Carbon($dateyear.$datemonth."15");
                //     $setting_model->setParamFiscalmonthAttribute(date_format($dt, 'm'));
                //     $begining = $setting_model->getMonthClosing();
                //     if (isset($begining)) {
                //         Log::debug($dateyear.$datemonth.str_pad($begining, 2, "0", STR_PAD_LEFT));
                //         $dt = new Carbon($dateyear.$datemonth.str_pad($begining, 2, "0", STR_PAD_LEFT));
                //         $todate = $dt->addDay();
                //     } else {
                //         $this->array_messagedata[] =  array(Config::get('const.RESPONCE_ITEM.message') =>Config::get('const.MSG_ERROR.not_setting_closing'));
                //         return false;
                //     }
                // } else {
                    // 1日基準
                    $setting_model->setParamYearAttribute($dateyear);
                    $dt = new Carbon($dateyear.$datemonth."01");
                    $fromdate = $dt;
                    $dt = new Carbon($dateyear.$datemonth."01");
                    $todate = $dt->addMonth()->subDay();
                // }
            } else {
                if ($displaykbn == Config::get('const.C024.closing')) {
                    // 期首月取得
                    $setting_model = new Setting();
                    $setting_model->setParamYearAttribute($dateyear);
                    $begining = $setting_model->getBeginingMonth();
                    if (isset($begining)) {
                        $fromdate = new Carbon($dateyear.str_pad($begining, 2, "0", STR_PAD_LEFT).'01');
                        $dt1 = new Carbon($dateyear.str_pad($begining, 2, "0", STR_PAD_LEFT).'01');
                        $todate = $dt1;
                        $todate->addYear()->subDay();
                    } else {
                        $this->array_messagedata[] =  array(Config::get('const.RESPONCE_ITEM.message') =>Config::get('const.MSG_ERROR.not_setting_beginning_month'));
                        return false;
                    }
                } else {
                    $dt = new Carbon($dateyear.'0101');
                    $fromdate = new Carbon($dateyear.'0101');
                    $todate = $fromdate;
                    $todate->addYear()->subDay();
                    $fromdate = new Carbon($dateyear.'0101');
                }
            }
            $dtfrom = $fromdate;
            if ($employmentstatus == "") { $employmentstatus = null; }
            if ($departmentcode == "") { $departmentcode = null; }
            if ($usercode == "") { $usercode = null; }
            // 作成
            $apicommon = new ApiCommonController();
            $users_datas = $apicommon->getUserDepartmentEmploymentRole($usercode, date_format($todate, 'Ymd'));
            $dt = $fromdate->copy()->subDay();
            $user = Auth::user();
            $login_user_code = $user->code;
            $calendar_model->setCreateduserAttribute($login_user_code);
            foreach($users_datas as $users_data) {
                $isins = true;
                if (isset($employmentstatus)) {
                    if ($employmentstatus != $users_data->employment_status) {
                        $isins = false;
                    }
                }
                if (isset($departmentcode)) {
                    if ($departmentcode != $users_data->department_code) {
                        $isins = false;
                    }
                }
                if (isset($usercode)) {
                    if ($usercode != $users_data->code) {
                        $isins = false;
                    }
                }
                if ($isins) {
                    $calendar_model->getParamemploymentstatusAttribute($users_data->employment_status);
                    $calendar_model->getParamdepartmentcodeAttribute($users_data->department_code);
                    $calendar_model->setParamusercodeAttribute($users_data->code);
                    $calendar_model->setParamfromdateAttribute(date_format($dtfrom, 'Ymd'));
                    $calendar_model->setParamtodateAttribute(date_format($todate, 'Ymd'));
                    // 削除
                    $calendar_model->delDate();
                    // 作成
                    $calendar_model->setParamfromdateAttribute(date_format($dt, 'Ymd'));
                    $calendar_model->setEmploymentstatusAttribute($users_data->employment_status);
                    $calendar_model->setDepartmentcodeAttribute($users_data->department_code);
                    $calendar_model->setUsercodeAttribute($users_data->code);
                    $results = $calendar_model->getCalenderDateYear($ptn, $formdata);
                    $temp_array = array();
                    foreach($results as $result) {
                        $temp_collect = collect($result);
                        $temp_array[] = $temp_collect->toArray();
                    }
                    if (count($temp_array) > 0) {
                        $results = $calendar_model->insCalenderDateYear($temp_array);
                    }
                }
            } 

            DB::commit();

        }catch(\PDOException $pe){
            DB::rollBack();
            throw $pe;
        }catch(\Exception $e){
            DB::rollBack();
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 複写設定（未使用）
     *
     */
    public function copyinit(Request $request){
        try {
            $this->array_messagedata = array();
            $details = array();
            $formdata = array();
            $result = true;
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
            if (!isset($params['dateyear'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "dateyear", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            if (!isset($params['datemonth'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "datemonth", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $departmentcode = $params['departmentcode'];
            $employmentstatus = $params['employmentstatus'];
            $usercode = $params['usercode'];
            $dateyear = $params['dateyear'];
            $datemonth = $params['datemonth'];
            Log::debug('departmentcode = '.$departmentcode);
            Log::debug('employmentstatus = '.$employmentstatus);
            Log::debug('usercode = '.$usercode);
            Log::debug('dateyear = '.$dateyear);
            Log::debug('datemonth = '.$datemonth);
            // showCalc implement
            $array_impl_copyinitCalendar = array (
                'departmentcode' => $departmentcode,
                'employmentstatus' => $employmentstatus,
                'usercode' => $usercode,
                'dateyear' => $dateyear,
                'datemonth' => $datemonth
            );
            $this->copyinitCalendar($array_impl_copyinitCalendar);

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
     * 初期設定実行
     *
     */
    public function copyinitCalendar($params){

        $employmentstatus = $params['employmentstatus'];
        $departmentcode = $params['departmentcode'];
        $usercode = $params['usercode'];
        $dateyear = $params['dateyear'];
        $datemonth = $params['datemonth'];
        $fromdate = "";
        $todate = "";
        DB::beginTransaction();
        try{
            $calendar_model = new Calendar();
            $setting_model = new Setting();
            // 当月1日基準
            $setting_model->setParamYearAttribute($dateyear);
            $dt = new Carbon($dateyear.$datemonth."01");
            $fromdate = $dt;
            $dt = new Carbon($dateyear.$datemonth."01");
            $todate = $dt->addMonth()->subDay();
            Log::debug('$todate = '.$todate);
            // 削除
            if ($employmentstatus == "") { $employmentstatus = null; }
            if ($departmentcode == "") { $departmentcode = null; }
            if ($usercode == "") { $usercode = null; }
            $calendar_model->getParamdepartmentcodeAttribute($employmentstatus);
            $calendar_model->getParamemploymentstatusAttribute($departmentcode);
            $calendar_model->setParamusercodeAttribute($usercode);
            $calendar_model->setParamfromdateAttribute(date_format($fromdate, 'Ymd'));
            $calendar_model->setParamtodateAttribute(date_format($todate, 'Ymd'));
            $calendar_model->delDate();
            // 作成
            $apicommon = new ApiCommonController();
            $users_datas = $apicommon->getUserDepartmentEmploymentRole($usercode, date_format($todate, 'Ymd'));
            $dt = $fromdate;
            $dt->subDay();
            $calendar_model->setParamfromdateAttribute(date_format($dt, 'Ymd'));
            $calendar_model->setParamtodateAttribute(date_format($todate, 'Ymd'));
            $user = Auth::user();
            $login_user_code = $user->code;
            $calendar_model->setCreateduserAttribute($login_user_code);
            foreach($users_datas as $users_data) {
                $calendar_model->setDepartmentcodeAttribute($users_data->department_code);
                $calendar_model->setEmploymentstatusAttribute($users_data->employment_status);
                $calendar_model->setUsercodeAttribute($users_data->code);
                $results = $calendar_model->getCalenderDateYear($ptn, $formdata);
                $temp_array = array();
                foreach($results as $result) {
                    $temp_collect = collect($result);
                    $temp_array[] = $temp_collect->toArray();
                }
                if (count($temp_array) > 0) {
                    $results = $calendar_model->insCalenderDateYear($temp_array);
                }
            } 

            DB::commit();

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
