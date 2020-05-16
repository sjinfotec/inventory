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
use App\UserModel;
use App\WorkTime;
use App\UserHolidayKubun;
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
                        'use_free_item' => $item->use_free_item,
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
                        'use_free_item' => $item->use_free_item,
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
                $result = true;
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
            $details = $params['details'];
            $businessdays = $params['businessdays'];
            $holidays = null;
            if (isset($params['holidays'])) {
                $holidays = $params['holidays'];
            }
            $use_free_items = null;
            if (isset($params['use_free_items'])) {
                $use_free_items = $params['use_free_items'];
            }
            $converts = array();
            // details に入力された区分を上書き
            foreach ($details['array_user_date_data'] as $index => $detail) {
                $formated = new Carbon($detail['date']);
                $converts[$index]['date'] = $formated->format('Ymd');
                $converts[$index]['businessdays'] = $businessdays[$index];
                $converts[$index]['holidays'] = $holidays[$index];
                $converts[$index]['use_free_items'] = $use_free_items[$index];
            }
            // fixData implement
            $array_impl_fixData = array (
                'department_code' => $details['department_code'],
                'employment_status' => $details['employment_status'],
                'user_code' => $details['user_code'],
                'isbatch' => false,
                'initptn' => 0,
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
        $department_code = $params['department_code'];
        $employment_status = $params['employment_status'];
        $user_code = $params['user_code'];
        $isbatch = $params['isbatch'];
        $initptn = $params['initptn'];
        $converts = $params['converts'];
        $systemdate = Carbon::now();
        $user = Auth::user();
        $login_department_code = $user->department_code;
        $login_user_code = $user->code;
        $calendar_model = new Calendar();
        $apicommon_model = new ApiCommonController();
        $user_holiday_model = new UserHolidayKubun();
        $work_time_model =  new WorkTime();

        DB::beginTransaction();
        try{
            $isupdate = true;
            foreach ($params['converts'] as $data) {
                Log::debug("data['date'] = ".$data['date']);
                // パラメータから更新対象のユーザーリストを作成する
                Log::debug("data['date'] = ".$data['date']);
                $userslist = $apicommon_model->getUserInfo($data['date'], $user_code, $department_code, $employment_status);
                foreach ($userslist as $usersitem) {
                    $isupdate = true;
                    // 一括更新の場合は、元データがあった場合休日であれば更新しない
                    Log::debug("initptn = ".$initptn);
                    if ($isbatch && $initptn == Config::get('const.C040.holiday_noupdate')) {
                        $calendar_model->setParamdepartmentcodeAttribute($usersitem->department_code);
                        $calendar_model->setParamemploymentstatusAttribute($usersitem->employment_status);
                        $calendar_model->setParamusercodeAttribute($usersitem->code);
                        $calendar_model->setParamfromdateAttribute($data['date']);
                        $resule_datas = $calendar_model->getCalenderDate();
                        foreach ($resule_datas as $item) {
                            if ($item->business_kubun == Config::get('const.C007.legal_holoday') ||
                                $item->business_kubun == Config::get('const.C007.legal_out_holoday')) {
                                $isupdate = false;
                                break;
                            }
                        }
                    }
                    Log::debug("isupdate = ".$isupdate);
                    if ($isupdate) {
                        // カレンダー更新
                        $calendar_model->setParamdepartmentcodeAttribute($usersitem->department_code);
                        $calendar_model->setParamemploymentstatusAttribute($usersitem->employment_status);
                        $calendar_model->setParamusercodeAttribute($usersitem->code);
                        $calendar_model->setParamfromdateAttribute($data['date']);
                        $calendar_model->setBusinesskubunAttribute($data['businessdays']);
                        $calendar_model->setHolidaykubunAttribute($data['holidays']);
                        $calendar_model->setParamusercodeAttribute($user_code);
                        $calendar_model->setUpdateduserAttribute($login_user_code);
                        $calendar_model->setUpdatedatAttribute($systemdate);
                        $calendar_model->updateCalendar();
                        // 休暇区分更新
                        Log::debug("data['holidays'] = ".$data['holidays']);
                        if ($data['holidays'] != null && $data['holidays'] != "" && $data['holidays'] != "0") {
                            if (strlen($data['use_free_items']) > 0) {
                                Log::debug("data['use_free_items'] = ".$data['use_free_items']);
                                if (substr($data['use_free_items'], Config::get('const.USEFREEITEM.day_holiday'), 1) == "1") {
                                    $working_date = $data['date'];
                                    $user_holiday_model->setParamUsercodeAttribute($usersitem->code);
                                    $user_holiday_model->setParamDepartmentcodeAttribute($usersitem->department_code);
                                    $user_holiday_model->setParamdatefromAttribute($working_date);
                                    $user_holiday_model->setSystemDateAttribute($systemdate);
                                    // 既に存在する場合は論理削除する
                                    $is_exists = $user_holiday_model->isExistsKbn();
                                    if($is_exists){
                                        $user_holiday_model->delKbn();
                                    }
                                    $user_holiday_model->setWorkingdateAttribute($working_date);
                                    $user_holiday_model->setDepartmentcodeAttribute($usersitem->department_code);
                                    $user_holiday_model->setUsercodeAttribute($usersitem->code);
                                    $user_holiday_model->setHolidaykubunAttribute($data['holidays']);
                                    $user_holiday_model->setCreateduserAttribute($login_user_code);
                                    $user_holiday_model->insertKbn();
                                    // 勤怠時刻にIDを登録するのでSELECTする
                                    $user_holiday_kubuns_id = null;
                                    $id_results = $user_holiday_model->getDetail();
                                    foreach($id_results as $item_id) {
                                        $user_holiday_kubuns_id = $item_id->id;
                                        break;
                                    }
                                    // ユーザーリストのユーザーに勤務時間が登録されている場合は論理削除する
                                    $target_ymd =  new Carbon($data['date']);
                                    $year = $target_ymd->format('Y');
                                    $month = $target_ymd->format('m');
                                    $dd = $target_ymd->format('d');
                                    $ymd_start = $year."/".$month."/".$dd." 00:00:00";
                                    $ymd_end = $year."/".$month."/".$dd." 23:59:59";
                                    $work_time_model->setParamStartDateAttribute($ymd_start);
                                    $work_time_model->setParamEndDateAttribute($ymd_end);
                                    $work_time_model->setUsercodeAttribute($usersitem->code);
                                    $users_results = $work_time_model->getUserDetails();
                                    $chk_attendance = false;
                                    // 論理削除する
                                    foreach($users_results as $item) {
                                        if ($item->mode = Config::get('const.C005.attendance_time')) {
                                            $chk_attendance = true;
                                        }
                                        if ($chk_attendance) {
                                            $work_time_model->setIdAttribute($item->id);
                                            $work_time_model->setEditordepartmentcodeAttribute($login_department_code);
                                            $work_time_model->setEditorusercodeAttribute($login_user_code);
                                            $work_time_model->setUpdateduserAttribute($login_user_code);
                                            $work_time_model->setSystemDateAttribute($systemdate);
                                            $work_time_model->delWorkTime();
                                        }
                                        if ($item->mode = Config::get('const.C005.leaving_time')) {
                                            $chk_attendance = false;
                                            break;
                                        }
                                    }
                                    $record_time = $year."/".$month."/".$dd." 00:00:01";
                                    $work_time_model->setUsercodeAttribute($usersitem->code);
                                    $work_time_model->setDepartmentcodeAttribute($usersitem->department_code);
                                    $work_time_model->setRecordtimeAttribute($record_time);
                                    $work_time_model->setModeAttribute(null);
                                    $work_time_model->setUserholidaykubunsidAttribute($user_holiday_kubuns_id);
                                    $work_time_model->setCreateduserAttribute($login_user_code);
                                    $work_time_model->setSystemDateAttribute($systemdate);
                                    $positions_data = null; 
                                    $work_time_model->setPositionsAttribute($positions_data);
                                    $work_time_model->setIseditorAttribute(true);
                                    $work_time_model->setEditordepartmentcodeAttribute($login_department_code);
                                    $work_time_model->setEditorusercodeAttribute($login_user_code);
                                    $work_time_model->insertWorkTime();
                                }
                            }
                        }
                    }
                }
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
     * 一括更新（日付）
     *
     * @param [type] $converts
     * @return void
     */
    public function fixbatch(Request $request){
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
            if (!isset($params['fromdate'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "fromdate", Config::get('const.LOG_MSG.parameter_illegal')));
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
            if (!isset($params['initptn'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "initptn", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $employmentstatus = null;
            if (isset($params['employmentstatus'])) {
                if ($params['employmentstatus'] != "") {
                    $employmentstatus = $params['employmentstatus'];
                }
            }
            $departmentcode = null;
            if (isset($params['departmentcode'])) {
                if ($params['departmentcode'] != "") {
                    $departmentcode = $params['departmentcode'];
                }
            }
            $usercode = null;
            if (isset($params['usercode'])) {
                if ($params['usercode'] != "") {
                    $usercode = $params['usercode'];
                }
            }
            $holidays = null;
            if (isset($params['holidays'])) {
                if ($params['holidays'] != "") {
                    $holidays = $params['holidays'];
                }
            }
            $use_free_items = null;
            if (isset($params['use_free_items'])) {
                if ($params['use_free_items'] != "") {
                    $use_free_items = $params['use_free_items'];
                }
            }
            $todate = null;
            if (isset($params['todate'])) {
                if ($params['todate'] != "") {
                    $todate = $params['todate'];
                }
            }
            $fromdate = $params['fromdate'];
            $businessdays = $params['businessdays'];
            $initptn = $params['initptn'];

            $converts = array();
            // 日付分の設定データを配列に設定する
            $dt = new Carbon($fromdate);
            $dtEnd = new Carbon($todate);
            $index = 0;
            while (true) {
                $formated = $dt;
                $converts[$index]['date'] = $formated->format('Ymd');
                $converts[$index]['businessdays'] = $businessdays;
                $converts[$index]['holidays'] = $holidays;
                $converts[$index]['use_free_items'] = $use_free_items;
                $dt->addDay();
                if ($dt->gt($dtEnd)) {
                    break;
                }
                $index++;
            }
            // fixData implement
            Log::debug('$initptn = '.$initptn);
            $array_impl_fixData = array (
                'department_code' => $departmentcode,
                'employment_status' => $employmentstatus,
                'user_code' => $usercode,
                'isbatch' => true,
                'initptn' => $initptn,
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
     * 一括更新（曜日）
     *
     * @param [type] $converts
     * @return void
     */
    public function fixbatchW(Request $request){
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
            if (!isset($params['fromyear'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "fromyear", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            if (!isset($params['frommonth'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "frommonth", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            if (!isset($params['weekdays'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "weekdays", Config::get('const.LOG_MSG.parameter_illegal')));
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
            if (!isset($params['initptn'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "initptn", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $fromyear = $params['fromyear'];
            $frommonth = $params['frommonth'];
            $weekdays = $params['weekdays'];
            $businessdays = $params['businessdays'];
            $initptn = $params['initptn'];
            $employmentstatus = null;
            if (isset($params['employmentstatus'])) {
                if ($params['employmentstatus'] != "") {
                    $employmentstatus = $params['employmentstatus'];
                }
            }
            $departmentcode = null;
            if (isset($params['departmentcode'])) {
                if ($params['departmentcode'] != "") {
                    $departmentcode = $params['departmentcode'];
                }
            }
            $usercode = null;
            if (isset($params['usercode'])) {
                if ($params['usercode'] != "") {
                    $usercode = $params['usercode'];
                }
            }
            $holidays = null;
            if (isset($params['holidays'])) {
                if ($params['holidays'] != "") {
                    $holidays = $params['holidays'];
                }
            }
            $use_free_items = null;
            if (isset($params['use_free_items'])) {
                if ($params['use_free_items'] != "") {
                    $use_free_items = $params['use_free_items'];
                }
            }
            // 指定年月の１日の曜日を取得
            $dt_frommonth = str_pad($frommonth, 2, 0, STR_PAD_LEFT);
            $dt = new Carbon($fromyear.$dt_frommonth.'01');
            $first_week = $dt->dayOfWeek;       // 0 (日曜)から 6 (土曜) を取得する
            // １日の曜日とパラメータの曜日の差分を求める
            // $weekdaysは0 (月曜)から 6 (日曜) 
            if ($weekdays == 6) {
                $edt_weekdays = 0;
            } else {
                $edt_weekdays = $weekdays + 1;
            }
            $diff = $edt_weekdays - $first_week;
            // 差分より指定曜日の初めの日付を求める
            $day = 1;
            if($diff < 0) { 
                $day += $diff + 7;              // 1日の曜日より前の曜日の場合 
            } else { 
                $day += $diff;                  // 1日の曜日より後の曜日の場合 
            }
            // 指定月の指定曜日の日付を配列に設定する
            $converts = array();
            $dt = new Carbon($fromyear.$dt_frommonth.str_pad($day, 2, 0, STR_PAD_LEFT));
            $dt1 = $dt;
            $index = 0;
            while(true) {
                $set_date = date_format($dt, 'Ymd');
                $chk_month = date_format($dt, 'm');
                if ($chk_month != $dt_frommonth) {
                    break;
                }
                $formated = $dt;
                $converts[$index]['date'] = $set_date;
                $converts[$index]['businessdays'] = $businessdays;
                $converts[$index]['holidays'] = $holidays;
                $converts[$index]['use_free_items'] = $use_free_items;
                $dt = $dt1->addWeek(1); 
                $index++;
            }
            // fixData implement
            $array_impl_fixData = array (
                'department_code' => $departmentcode,
                'employment_status' => $employmentstatus,
                'user_code' => $usercode,
                'isbatch' => true,
                'initptn' => $initptn,
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
