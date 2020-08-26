<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Controllers\ApiCommonController;
// use App\ShiftInformation;
use App\CalendarSettingInformation;


class EditShiftTimeController extends Controller
{
    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        return view('edit_shift_time');
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
            if (!isset($params['fromdate'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "fromdate", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $details, 'detail_dates' => $detail_dates,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            // 設定されていない場合
            if (!isset($params['todate'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "todate", Config::get('const.LOG_MSG.parameter_illegal')));
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
            $fromdate = $params['fromdate'];
            $todate = $params['todate'];
            $apicommon_model = new ApiCommonController();
            // addDailyCalc implement
            $array_impl_getCalendarInformations = array (
                'departmentcode' => $departmentcode,
                'employmentstatus' => $employmentstatus,
                'usercode' => $usercode,
                'fromdate' => $fromdate,
                'todate' => $todate
            );
            $results = $apicommon_model->getCalendarInformations($array_impl_getCalendarInformations);
            return $results;
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
            if (!isset($params['working_timetable_no'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "working_timetable_no", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $details = $params['details'];
            $array_working_timetable_no = $params['working_timetable_no'];
            $converts = array();
            // details に入力された区分を上書き
            foreach ($details['array_user_date_data'] as $index => $detail) {
                $formated = new Carbon($detail['date']);
                $converts[$index]['date'] = $formated->format('Ymd');
                $converts[$index]['working_timetable_no'] = $array_working_timetable_no[$index];
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
        $department_code = $params['department_code'];
        $employment_status = $params['employment_status'];
        $user_code = $params['user_code'];
        $converts = $params['converts'];
        $systemdate = Carbon::now();
        $user = Auth::user();
        $login_department_code = $user->department_code;
        $login_user_code = $user->code;
        $login_user_code_4 = substr($login_user_code, 0 ,4);
        $calendar_setting_model = new CalendarSettingInformation();
        $apicommon_model = new ApiCommonController();

        DB::beginTransaction();
        try{
            foreach ($params['converts'] as $data) {
                // パラメータから更新対象のユーザーリストを作成する
                $userslist = $apicommon_model->getUserInfo($data['date'], $user_code, $department_code, $employment_status);
                foreach ($userslist as $usersitem) {
                    // カレンダー更新
                    $calendar_setting_model->setParamAccountidAttribute($login_user_code_4);
                    $calendar_setting_model->setParamdepartmentcodeAttribute($usersitem->department_code);
                    $calendar_setting_model->setParamemploymentstatusAttribute($usersitem->employment_status);
                    $calendar_setting_model->setParamusercodeAttribute($usersitem->code);
                    $calendar_setting_model->setParamfromdateAttribute($data['date']);
                    $calendar_setting_model->setWorkingtimetablenoAttribute($data['working_timetable_no']);
                    $calendar_setting_model->setUpdateduserAttribute($login_user_code);
                    $calendar_setting_model->setUpdatedatAttribute($systemdate);
                    $calendar_setting_model->updateCalendar();
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
            if (!isset($params['working_timetable_no'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "working_timetable_no", Config::get('const.LOG_MSG.parameter_illegal')));
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
            $todate = null;
            if (isset($params['todate'])) {
                if ($params['todate'] != "") {
                    $todate = $params['todate'];
                }
            }
            $fromdate = $params['fromdate'];
            $working_timetable_no = $params['working_timetable_no'];

            $converts = array();
            // 日付分の設定データを配列に設定する
            $dt = new Carbon($fromdate);
            $dtEnd = null;
            if ($todate == null) {
                $dtEnd = $dt;
            } else {
                $dtEnd = new Carbon($todate);
            }
            $index = 0;
            while (true) {
                $formated = $dt;
                $converts[$index]['date'] = $formated->format('Ymd');
                $converts[$index]['working_timetable_no'] = $working_timetable_no;
                $dt->addDay();
                if ($dt->gt($dtEnd)) {
                    break;
                }
                $index++;
            }
            // fixData implement
            $array_impl_fixData = array (
                'department_code' => $departmentcode,
                'employment_status' => $employmentstatus,
                'user_code' => $usercode,
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
            if (!isset($params['fromdate'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "fromdate", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            if (!isset($params['todate'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "todate", Config::get('const.LOG_MSG.parameter_illegal')));
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
            if (!isset($params['working_timetable_no'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "working_timetable_no", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $fromdate = $params['fromdate'];
            $todate = $params['todate'];
            $weekdays = $params['weekdays'];
            $working_timetable_no = $params['working_timetable_no'];
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
            // 開始年月日の曜日を取得
            $dt = new Carbon($fromdate);
            $first_week = $dt->dayOfWeek;       // 0 (日曜)から 6 (土曜) を取得する
            // 指定曜日$weekdaysは0 (月曜)から 6 (日曜) 
            if ($weekdays == 6) {
                $edt_weekdays = 0;
            } else {
                $edt_weekdays = $weekdays + 1;
            }
            $diff = $edt_weekdays - $first_week;
            // 差分より指定曜日の初めの日付を求める
            $dt_ym = date_format($dt, 'Ym');
            $day = date_format($dt, 'd');
            if($diff < 0) { 
                $day += $diff + 7;              // 開始年月日の曜日より前の曜日の場合 
            } else { 
                $day += $diff;                  // 開始年月日の曜日より後の曜日の場合 
            }
            // 指定曜日の日付を配列に設定する
            $converts = array();
            $dt = new Carbon($dt_ym.str_pad($day, 2, 0, STR_PAD_LEFT));
            $dt1 = $dt;
            $dt_todate = new Carbon($todate);
            $dt_todate_ymd = date_format($dt_todate, 'Ymd');
            $index = 0;
            while(true) {
                $set_date = date_format($dt, 'Ymd');
                if ($set_date > $dt_todate_ymd) {
                    break;
                }
                $formated = $dt;
                $converts[$index]['date'] = $set_date;
                $converts[$index]['working_timetable_no'] = $working_timetable_no;
                $dt = $dt1->addWeek(1); 
                $index++;
            }
            // fixData implement
            $array_impl_fixData = array (
                'department_code' => $departmentcode,
                'employment_status' => $employmentstatus,
                'user_code' => $usercode,
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
     * シフト登録
     *
     * @param Request $request
     * @return void
     */
    // public function store(Request $request){
    //     $this->array_messagedata = array();
    //     $result = true;
    //     try {
    //         // パラメータチェック
    //         $params = array();
    //         if (!isset($request->keyparams)) {
    //             Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
    //             $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
    //             return response()->json(
    //                 ['result' => false,
    //                 Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
    //             );
    //         }
    //         $params = $request->keyparams;
    //         if (!isset($params['form'])) {
    //             Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "form", Config::get('const.LOG_MSG.parameter_illegal')));
    //             $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
    //             return response()->json(
    //                 ['result' => false,
    //                 Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
    //             );
    //         }
    //         $data = $params['form'];
    //         $result = $this->insert($data);
    //         return response()->json(
    //             ['result' => $result,
    //             Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
    //         );
    //     }catch(\PDOException $pe){
    //         throw $pe;
    //     }catch(\Exception $e){
    //         Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
    //         Log::error($e->getMessage());
    //         throw $e;
    //     }
    // }

    /**
     * DB書き込み(新規)
     *
     * @param [type] $code
     * @param [type] $shift_start_time
     * @param [type] $shift_end_time
     * @param [type] $from
     * @param [type] $to
     * @return void
     */
    // private function insert($data){
    //     $systemdate = Carbon::now();
    //     DB::beginTransaction();
    //     try{
    //         $working_timetable_no = $data["working_timetable_no"];
    //         $user_code = $data["user_code"];
    //         $department_code = $data["department_code"];
    //         $shift_start_time = $data["shift_start_time"];
    //         $shift_end_time = $data["shift_end_time"];
    //         $from = new Carbon($shift_start_time);
    //         $to = new Carbon($shift_end_time);
    //         $shift_info_model = new ShiftInformation();
    //         $shift_info_model->setUsercodeAttribute($data["user_code"]);
    //         $shift_info_model->setDepartmentcodeAttribute($data["department_code"]);
    //         $shift_info_model->setStarttargetdateAttribute($from);
    //         $shift_info_model->setEndtargetdateAttribute($to);
    //         $is_exists = $shift_info_model->isExistsShiftInfo();
    //         if($is_exists){
    //            $shift_info_model->delShiftInfo();
    //         }
    //         $shift_info_model->setWorkingtimetablenoAttribute($data["working_timetable_no"]);
    //         $shift_info_model->setCreatedatAttribute($systemdate);
    //         // from -> to までtarget_date登録する
    //         for ($i=$from; $i->lte($to); $i->addDay()) {
    //             $target_date = $i->format("Ymd"); 
    //             $shift_info_model->setTargetdateAttribute($target_date);
    //             $shift_info_model->insertUserShift();
    //         }
    //         DB::commit();
    //         return true;

    //     }catch(\PDOException $e){
    //         DB::rollBack();
    //         return false;
    //     }
    // }

    /**
     * DB書き込み(新規)
     *
     * @param [type] $user_code
     * @param [type] $datefrom
     * @param [type] $dateto
     * @param [type] $from
     * @param [type] $to
     * @return void
     */
    // public function insertWeek($data){
    //     $systemdate = Carbon::now();
    //     try{
    //         $user_code = $data["user_code"];
    //         $department_code = $data["department_code"];
    //         $datefrom = $data["datefrom"];
    //         $dateto = $data["dateto"];
    //         $from = new Carbon($datefrom);
    //         $to = new Carbon($dateto);
    //         $details = $data["details"];
    //         $shift_info_model = new ShiftInformation();
    //         $shift_info_model->setUsercodeAttribute($data["user_code"]);
    //         $shift_info_model->setDepartmentcodeAttribute($data["department_code"]);
    //         $shift_info_model->setCreatedatAttribute($systemdate);
    //         // from -> to までtarget_date登録する
    //         for ($i=$from; $i->lte($to); $i->addDay()) {
    //             $target_date = $i->format("Ymd"); 
    //             $dt = new Carbon($target_date);
    //             $Weekindex = $dt->dayOfWeek;
    //             switch ($Weekindex){
    //                 case 0:     //  日曜
    //                     $Weekindex = 6;
    //                     break;
    //                 case 1:     //  月曜
    //                     $inWeekindexdex = 0;
    //                     break;
    //                 default:
    //                     $Weekindex = $Weekindex - 1;
    //                 break;
    //             }
    //             $shift_info_model->setWorkingtimetablenoAttribute($details['timeptn_timetable_w'][$Weekindex]);
    //             $shift_info_model->setTargetdateAttribute($target_date);
    //             $shift_info_model->insertUserShift();
    //         }
    //         return true;

    //     }catch(\PDOException $pe){
    //         Log::error($pe->getMessage());
    //         throw $pe;
    //     }catch(\Exception $e){
    //         Log::error($e->getMessage());
    //         throw $e;
    //     }
    // }
}
