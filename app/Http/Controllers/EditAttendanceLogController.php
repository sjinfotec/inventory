<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\AttendanceLog;
use App\Http\Controllers\ApiCommonController;
use Carbon\Carbon;


class EditAttendanceLogController extends Controller
{
    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        return view('edit_attendance_log');
    }

    /** 詳細取得
     *
     * @return list results
     */
    public function get(Request $request){
        $this->array_messagedata = array();
        $details = new Collection();
        $result = true;
        try {
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
            if (!isset($params['fromdate'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "fromdate", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $details,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            if (!isset($params['todate'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "todate", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $details,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            if (!isset($params['eventlogs'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "eventlogs", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $details,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }

            Log::debug('  param department_code = '.$params['department_code']);
            Log::debug('  param user_code = '.$params['user_code']);
            Log::debug('  param fromdate = '.$params['fromdate']);
            Log::debug('  param todate = '.$params['todate']);
            // ログインユーザ
            $login_user_id = Auth::user()->code;
            $user_code = $params['user_code'];
            $department_code = null;
            $employment_status = null;
            $attendance_model = new AttendanceLog();
            // eventlogsの検索パラメータ設定
            // 部署雇用形態はusersから
            $apicommon_model = new ApiCommonController();
            $result = $apicommon_model->getUserDepartmentEmploymentRole($login_user_id, $params['fromdate']);
            foreach($result as $item) {
                Log::debug('  apicommon_model department_code = '.$item->department_code);
                Log::debug('  apicommon_model employment_status = '.$item->employment_status);
                $department_code = $item->department_code;
                $employment_status = $item->employment_status;
                $attendance_model->setParamdepartmentcodeAttribute($item->department_code);
                $attendance_model->setParamemploymentstatusAttribute($item->employment_status);
                break;
            }
            $attendance_model->setParamusercodeAttribute($login_user_id);
            // eventlogフィルター
            $array_eventlogs = $params['eventlogs'];
            Log::debug('  array_eventlogs count = '.count($array_eventlogs));
            $collect_eventlogs = new Collection($array_eventlogs);
            $filtered = $collect_eventlogs->whereBetween('event_date', [$params['fromdate'], $params['todate']]);
            Log::debug('  filtered count = '.count($filtered));

            // eventlogsの登録設定
            $systemdate = Carbon::now();
            $attendance_model->setDepartmentcodeAttribute($department_code);
            $attendance_model->setEmploymentstatusAttribute($employment_status);
            $attendance_model->setUsercodeAttribute($login_user_id);
            $attendance_model->setCreateduserAttribute($login_user_id);
            $attendance_model->setCreatedatAttribute($systemdate);
            $this->insertLog($attendance_model, $filtered);
            // 取得パラメータ設定
            $attendance_model->setParamworkingdatefromAttribute($params['fromdate']);
            $attendance_model->setParamworkingdatetoAttribute($params['todate']);
            $attendance_model->setParamdepartmentcodeAttribute($department_code);
            $attendance_model->setParamusercodeAttribute($user_code);
            $details = $attendance_model->getAttendanceLogList($params['fromdate']);
            $brk_department_code = null;
            $brk_user_code = null;
            $brk_working_date = null;
            $brk_item = null;
            $array_details = array();
            $array_user_code = array();
            $array_date = array();
            $attendance_time = "";
            $attendance_record_time = "";
            $attendance_id = "";
            $leaving_time = "";
            $leaving_record_time = "";
            $leaving_id = "";
            $pcstart_time = "";
            $pcstart_record_time = "";
            $pcstart_id = "";
            $pcend_time = "";
            $pcend_record_time = "";
            $pcend_id = "";
            Log::debug('  details count = '.count($details));
            foreach($details as $item) {
                Log::debug('  $item->department_code = '.$item->department_code);
                Log::debug('  $item->user_code = '.$item->user_code);
                Log::debug('  $item->working_date = '.$item->working_date);
                if ($brk_department_code == null) {$brk_department_code = $item->department_code;}
                if ($brk_user_code == null) {$brk_user_code = $item->user_code;}
                if ($brk_working_date == null) {$brk_working_date = $item->working_date;}
                if ($brk_item == null) {$brk_item = $item;}
                if ($brk_department_code != $item->department_code) {
                    if (count($array_date) == 0) {
                        $array_date[] = array(
                            'working_date' => $brk_working_date,
                            'working_date_name' => $brk_item->working_date_name,
                            'attendance_time' => $attendance_time,
                            'leaving_time' => $leaving_time,
                            'pcstart_time' => $pcstart_time,
                            'pcend_time' => $pcend_time,
                            'difference_reason' => $brk_item->difference_reason
                        );
                    }
                    $array_details[] = array(
                        'department_code' => $brk_department_code,
                        'department_name' =>$brk_item->department_name,
                        'employment_status' =>$brk_item->employment_status,
                        'user_code' => $brk_user_code,
                        'user_name' =>$brk_item->user_name,
                        'date' => $array_date
                    );
                    $array_date = array();
                    $brk_item = $item;
                    $brk_working_date = $item->working_date;
                    $brk_user_code = $item->user_code;
                    $brk_department_code = $item->department_code;
                    $attendance_time = "";
                    $leaving_time = "";
                    $pcstart_time = "";
                    $pcend_time = "";
                    $attendance_record_time = "";
                    $leaving_record_time = "";
                    $pcstart_record_time = "";
                    $pcend_record_time = "";
                    $attendance_id = "";
                    $leaving_id = "";
                    $pcstart_id = "";
                    $pcend_id = "";
                } else {
                    if ($brk_user_code != $item->user_code) {
                        if (count($array_date) == 0) {
                            $array_date[] = array(
                                'working_date' => $brk_working_date,
                                'working_date_name' => $brk_item->working_date_name,
                                'attendance_time' => $attendance_time,
                                'attendance_record_time' => $attendance_record_time,
                                'attendance_id' => $attendance_id,
                                'leaving_time' => $leaving_time,
                                'leaving_record_time' => $leaving_record_time,
                                'leaving_id' => $leaving_id,
                                'pcstart_time' => $pcstart_time,
                                'pcstart_record_time' => $pcstart_record_time,
                                'pcstart_id' => $pcstart_id,
                                'pcend_time' => $pcend_time,
                                'pcend_record_time' => $pcend_record_time,
                                'pcend_id' => $pcend_id,
                                'difference_reason' => $brk_item->difference_reason
                            );
                        }
                        $array_details[] = array(
                            'department_code' => $brk_department_code,
                            'department_name' =>$brk_item->department_name,
                            'employment_status' =>$brk_item->employment_status,
                            'user_code' => $brk_user_code,
                            'user_name' =>$brk_item->user_name,
                            'date' => $array_date
                        );
                        $array_date = array();
                        $brk_item = $item;
                        $brk_working_date = $item->working_date;
                        $brk_user_code = $item->user_code;
                        $attendance_time = "";
                        $leaving_time = "";
                        $pcstart_time = "";
                        $pcend_time = "";
                        $attendance_record_time = "";
                        $leaving_record_time = "";
                        $pcstart_record_time = "";
                        $pcend_record_time = "";
                        $attendance_id = "";
                        $leaving_id = "";
                        $pcstart_id = "";
                        $pcend_id = "";
                    } else {
                        if ($brk_working_date != $item->working_date) {
                            $array_date[] = array(
                                'working_date' => $brk_working_date,
                                'working_date_name' => $brk_item->working_date_name,
                                'attendance_time' => $attendance_time,
                                'attendance_record_time' => $attendance_record_time,
                                'attendance_id' => $attendance_id,
                                'leaving_time' => $leaving_time,
                                'leaving_record_time' => $leaving_record_time,
                                'leaving_id' => $leaving_id,
                                'pcstart_time' => $pcstart_time,
                                'pcstart_record_time' => $pcstart_record_time,
                                'pcstart_id' => $pcstart_id,
                                'pcend_time' => $pcend_time,
                                'pcend_record_time' => $pcend_record_time,
                                'pcend_id' => $pcend_id,
                                'difference_reason' => $brk_item->difference_reason
                            );
                            $brk_item = $item;
                            $brk_working_date = $item->working_date;
                            $attendance_time = "";
                            $leaving_time = "";
                            $pcstart_time = "";
                            $pcend_time = "";
                            $attendance_record_time = "";
                            $leaving_record_time = "";
                            $pcstart_record_time = "";
                            $pcend_record_time = "";
                            $attendance_id = "";
                            $leaving_id = "";
                            $pcstart_id = "";
                            $pcend_id = "";
                        }
                    }
                }
                if (isset($item->mode)) {
                    if ($item->mode ==  Config::get('const.C012.attendance')) {
                        if ($attendance_time == "") {
                            $attendance_time = $item->scan_time;
                            $attendance_record_time = $item->record_time;
                            $attendance_id = $item->id;
                        }
                    }
                    if ($item->mode ==  Config::get('const.C012.leaving')) {
                        $leaving_time = $item->scan_time;
                        $leaving_record_time = $item->record_time;
                        $leaving_id = $item->id;
                    }
                    if ($item->mode ==  Config::get('const.C033.logon')) {
                        if ($pcstart_time == "") {
                            $pcstart_time = $item->scan_time;
                            $pcstart_record_time = $item->record_time;
                            $pcstart_id = $item->id;
                        }
                    }
                    if ($item->mode ==  Config::get('const.C033.pcstart')) {
                        if ($pcstart_time == "") {
                            $pcstart_time = $item->scan_time;
                            $pcstart_record_time = $item->record_time;
                            $pcstart_id = $item->id;
                        }
                    }
                    if ($item->mode ==  Config::get('const.C033.logout')) {
                        $pcend_time = $item->scan_time;
                        $pcend_record_time = $item->record_time;
                        $pcend_id = $item->id;
                    }
                    if ($item->mode ==  Config::get('const.C033.pcend')) {
                        $pcend_time = $item->scan_time;
                        $pcend_record_time = $item->record_time;
                        $pcend_id = $item->id;
                    }
                }
            }
            if ($attendance_time != "" || $leaving_time != "" || $pcstart_time != "" || $pcend_time != "") {
                $array_date[] = array(
                    'working_date' => $brk_working_date,
                    'working_date_name' => $brk_item->working_date_name,
                    'attendance_time' => $attendance_time,
                    'attendance_record_time' => $attendance_record_time,
                    'attendance_id' => $attendance_id,
                    'leaving_time' => $leaving_time,
                    'leaving_record_time' => $leaving_record_time,
                    'leaving_id' => $leaving_id,
                    'pcstart_time' => $pcstart_time,
                    'pcstart_record_time' => $pcstart_record_time,
                    'pcstart_id' => $pcstart_id,
                    'pcend_time' => $pcend_time,
                    'pcend_record_time' => $pcend_record_time,
                    'pcend_id' => $pcend_id,
                    'difference_reason' => $brk_item->difference_reason
   );
            }
            if (count($array_date) > 0) {
                $array_details[] = array(
                    'department_code' => $brk_department_code,
                    'department_name' =>$brk_item->department_name,
                    'employment_status' =>$brk_item->employment_status,
                    'user_code' => $brk_user_code,
                    'user_name' =>$brk_item->user_name,
                    'date' => $array_date
                );
            }
            foreach($array_details as $array_detail) {
                Log::debug('  department_code = '.$array_detail['department_code']);
                Log::debug('  department_name = '.$array_detail['department_name']);
                Log::debug('  employment_status = '.$array_detail['employment_status']);
                Log::debug('  user_code = '.$array_detail['user_code']);
                Log::debug('  user_name = '.$array_detail['user_name']);
                foreach($array_detail['date'] as $date) {
                    Log::debug('  working_date = '.$date['working_date']);
                    Log::debug('  working_date_name = '.$date['working_date_name']);
                    Log::debug('  attendance_time = '.$date['attendance_time']);
                    Log::debug('  attendance_record_time = '.$date['attendance_record_time']);
                    Log::debug('  attendance_id = '.$date['attendance_id']);
                    Log::debug('  leaving_time = '.$date['leaving_time']);
                    Log::debug('  leaving_record_time = '.$date['leaving_record_time']);
                    Log::debug('  leaving_id = '.$date['leaving_id']);
                    Log::debug('  pcstart_time = '.$date['pcstart_time']);
                    Log::debug('  pcstart_record_time = '.$date['pcstart_record_time']);
                    Log::debug('  pcstart_id = '.$date['pcstart_id']);
                    Log::debug('  pcend_time = '.$date['pcend_time']);
                    Log::debug('  pcend_record_time = '.$date['pcend_record_time']);
                    Log::debug('  pcend_id = '.$date['pcend_id']);
                    Log::debug('  difference_reason = '.$date['difference_reason']);
                }
            }
            return response()->json(
                ['result' => $result, 'details' => $array_details,
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

    /** イベントログ登録
     *
     */
    public function insertLog($attendance_model, $filtered){
        DB::beginTransaction();
        try {
            foreach ($filtered as $item) {
                    // 登録済みであるか？
                $attendance_model->setParamworkingdatefromAttribute($item['event_date']);
                $attendance_model->setParamworkingdatetoAttribute($item['event_date']);
                $attendance_model->setParameventmodeAttribute($item['event_mode']);
                $attendance_model->setParameventtimeAttribute(substr($item['event_date'],0,4)."/".substr($item['event_date'],4,2)."/".substr($item['event_date'],6,2)." ".$item['event_time']);
                if (!$attendance_model->isExist()) {
                    // イベントログ設定
                    $attendance_model->setWorkingdateAttribute($item['event_date']);
                    $attendance_model->setEventmodeAttribute($item['event_mode']);
                    $attendance_model->setEventtimeAttribute(
                        substr($item['event_date'],0,4)."/".substr($item['event_date'],4,2)."/".substr($item['event_date'],6,2)." ".$item['event_time']);
                    $attendance_model->store();
                }
            }
            DB::commit();
        }catch(\PDOException $pe){
            DB::rollBack();
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.data_insert_erorr'));
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 更新
     *
     * @param Request $request
     * @return response
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
            $details = $params['details'];
            $this->fixData($details);
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
    private function fixData($details){
        $systemdate = Carbon::now();
        $login_user_id = Auth::user()->code;

        $attendance_model = new AttendanceLog();
        DB::beginTransaction();
        try{
            foreach($details as $item) {
                foreach($item['date'] as $date) {
                    if (isset($date['attendance_id']) && $date['attendance_id'] != "") {
                        // 差異理由UPDATE
                        $attendance_model->setParamidAttribute($date['attendance_id']);
                        $attendance_model->setDifferencereasonAttribute($date['difference_reason']);
                        $attendance_model->setUpdateduserAttribute($login_user_id);
                        $attendance_model->setUpdatedatAttribute($systemdate);
                        $attendance_model->updReasonFromID();
                    }
                    if (isset($date['leaving_id']) && $date['leaving_id'] != "") {
                        // 差異理由UPDATE
                        $attendance_model->setParamidAttribute($date['leaving_id']);
                        $attendance_model->setDifferencereasonAttribute($date['difference_reason']);
                        $attendance_model->setUpdateduserAttribute($login_user_id);
                        $attendance_model->setUpdatedatAttribute($systemdate);
                        $attendance_model->updReasonFromID();
                    }
                    if (isset($date['pcstart_id']) && $date['pcstart_id'] != "") {
                        // 差異理由UPDATE
                        $attendance_model->setParamidAttribute($date['pcstart_id']);
                        $attendance_model->setDifferencereasonAttribute($date['difference_reason']);
                        $attendance_model->setUpdateduserAttribute($login_user_id);
                        $attendance_model->setUpdatedatAttribute($systemdate);
                        $attendance_model->updReasonFromID();
                    }
                    if (isset($date['pcend_id']) && $date['pcend_id'] != "") {
                        // 差異理由UPDATE
                        $attendance_model->setParamidAttribute($date['pcend_id']);
                        $attendance_model->setDifferencereasonAttribute($date['difference_reason']);
                        $attendance_model->setUpdateduserAttribute($login_user_id);
                        $attendance_model->setUpdatedatAttribute($systemdate);
                        $attendance_model->updReasonFromID();
                    }
                }
            }
            DB::commit();

        }catch(\PDOException $pe){
            DB::rollBack();
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }
}
