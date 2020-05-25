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
        $authusers = Auth::user();
        $loginusercode = $authusers->code;
        return view('edit_attendance_log', compact('authusers'));
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
    
                // ログインユーザ
            $login_user_id = Auth::user()->code;
            $user_code = $params['user_code'];
            $employment_status = null;
            if (isset($params['employment_status'])) {
                $employment_status = $params['employment_status'];
            }
            $department_code = null;
            if (isset($params['department_code'])) {
                $department_code = $params['department_code'];
            }
            $user_code = null;
            if (isset($params['user_code'])) {
                $user_code = $params['user_code'];
            }
            $differencetime = 0;
            if (isset($params['differencetime']) && $params['differencetime'] != "") {
                $differencetime = $params['differencetime'];
            }
            // 勤怠ログデータ取得
            $attendance_model = new AttendanceLog();
            $attendance_model->setParamemploymentstatusAttribute($employment_status);
            $attendance_model->setParamdepartmentcodeAttribute($department_code);
            $attendance_model->setParamusercodeAttribute($user_code);
            $attendance_model->setParamworkingdatefromAttribute($params['fromdate']);
            $attendance_model->setParamworkingdatetoAttribute($params['todate']);
            $details = $attendance_model->getAttendanceLogList($params['fromdate']);
            // 返却用配列設定
            $brk_department_code = null;
            $brk_user_code = null;
            $brk_working_date = null;
            $brk_item = null;
            $array_details = array();
            $array_user_code = array();
            $array_date = array();
            // 出退勤
            $attendance_time = "";
            $attendance_record_time = "";
            $attendance_id = "";
            $leaving_time = "";
            $leaving_record_time = "";
            $leaving_id = "";
            // PC起動
            $pcstart_time = "";
            $pcstart_record_time = "";
            $pcstart_id = "";
            $pcstart_difference_reason = "";
            $pcstart_time_pcstart = "";
            $pcstart_record_time_pcstart = "";
            $pcstart_id_pcstart = "";
            $pcstart_difference_reason_pcstart = "";
            $pcstart_time_logon = "";
            $pcstart_record_time_logon = "";
            $pcstart_id_logon = "";
            $pcstart_difference_reason_logon = "";
            // PC終了
            $pcend_time = "";
            $pcend_record_time = "";
            $pcend_id = "";
            $pcend_difference_reason = "";
            $pcend_time_pcend = "";
            $pcend_record_time_pcend = "";
            $pcend_id_pcend = "";
            $pcend_difference_reason_pcend = "";
            $pcend_time_logout = "";
            $pcend_record_time_logout = "";
            $pcend_id_logout = "";
            $pcend_difference_reason_logout = "";
            $apicommon_model = new ApiCommonController();
            foreach($details as $item) {
                if ($brk_department_code == null) {$brk_department_code = $item->department_code;}
                if ($brk_user_code == null) {$brk_user_code = $item->user_code;}
                if ($brk_working_date == null) {$brk_working_date = $item->working_date;}
                if ($brk_item == null) {$brk_item = $item;}
                $isdiff = false;
                if ($brk_department_code != $item->department_code) {
                    // 優先順位  pcstart<logon  pcend<logout  windows10はlogonかlogoutが多い
                    // PC起動
                    $pcstart_time = "";
                    $pcstart_record_time = "";
                    $pcstart_id = "";
                    $pcstart_difference_reason = "";
                    if ($pcstart_time_logon != "") {
                        $pcstart_time = $pcstart_time_logon;
                        $pcstart_record_time = $pcstart_record_time_logon;
                        $pcstart_id = $pcstart_id_logon;
                        $pcstart_difference_reason = $pcstart_difference_reason_logon;
                    } else if ($pcstart_time_pcstart != "") {
                        $pcstart_time = $pcstart_time_pcstart;
                        $pcstart_record_time = $pcstart_record_time_pcstart;
                        $pcstart_id = $pcstart_id_pcstart;
                        $pcstart_difference_reason = $pcstart_difference_reason_pcstart;
                    }
                    // PC終了
                    $pcend_time = "";
                    $pcend_record_time = "";
                    $pcend_id = "";
                    $pcend_difference_reason = "";
                    if ($pcend_time_logout != "") {
                        $pcend_time = $pcend_time_logout;
                        $pcend_record_time = $pcend_record_time_logout;
                        $pcend_id = $pcend_id_logout;
                        $pcend_difference_reason = $pcend_difference_reason_logout;
                    } else if ($pcend_time_pcend != "") {
                        $pcend_time = $pcend_time_pcend;
                        $pcend_record_time = $pcend_record_time_pcend;
                        $pcend_id = $pcend_id_pcend;
                        $pcend_difference_reason = $pcend_difference_reason_pcend;
                    }
                    if ($pcstart_difference_reason == null || $pcstart_difference_reason == "") {
                        $pcstart_difference_reason = $pcend_difference_reason_pcend;
                    }
                    if ($this->isDiffTime($attendance_record_time, $pcstart_record_time, $differencetime)) {
                        $isdiff = true;
                    } else if ($this->isDiffTime($pcend_record_time, $leaving_record_time, $differencetime)) {
                        $isdiff = true;
                    }
                    if ($isdiff) {
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
                                'difference_reason' => $pcstart_difference_reason
                            );
                        }
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
                    $array_date = array();
                    $brk_item = $item;
                    $brk_working_date = $item->working_date;
                    $brk_user_code = $item->user_code;
                    $brk_department_code = $item->department_code;
                    $attendance_time = "";
                    $attendance_record_time = "";
                    $attendance_id = "";
                    $leaving_time = "";
                    $leaving_record_time = "";
                    $leaving_id = "";
                    $pcstart_time_pcstart = "";
                    $pcstart_record_time_pcstart = "";
                    $pcstart_id_pcstart = "";
                    $pcstart_time_logon = "";
                    $pcstart_record_time_logon = "";
                    $pcstart_id_logon = "";
                    $pcend_time_pcend = "";
                    $pcend_record_time_pcend = "";
                    $pcend_id_pcend = "";
                    $pcend_time_logout = "";
                    $pcend_record_time_logout = "";
                    $pcend_id_logout = "";
                } else {
                    if ($brk_user_code != $item->user_code) {
                        // 優先順位  pcstart<logon  pcend<logout  windows10はlogonかlogoutが多い
                        // PC起動
                        $pcstart_time = "";
                        $pcstart_record_time = "";
                        $pcstart_id = "";
                        $pcstart_difference_reason = "";
                        if ($pcstart_time_logon != "") {
                            $pcstart_time = $pcstart_time_logon;
                            $pcstart_record_time = $pcstart_record_time_logon;
                            $pcstart_id = $pcstart_id_logon;
                            $pcstart_difference_reason = $pcstart_difference_reason_logon;
                        } else if ($pcstart_time_pcstart != "") {
                            $pcstart_time = $pcstart_time_pcstart;
                            $pcstart_record_time = $pcstart_record_time_pcstart;
                            $pcstart_id = $pcstart_id_pcstart;
                            $pcstart_difference_reason = $pcstart_difference_reason_pcstart;
                        }
                        // PC終了
                        $pcend_time = "";
                        $pcend_record_time = "";
                        $pcend_id = "";
                        $pcend_difference_reason = "";
                        if ($pcend_time_logout != "") {
                            $pcend_time = $pcend_time_logout;
                            $pcend_record_time = $pcend_record_time_logout;
                            $pcend_id = $pcend_id_logout;
                            $pcend_difference_reason = $pcend_difference_reason_logout;
                        } else if ($pcend_time_pcend != "") {
                            $pcend_time = $pcend_time_pcend;
                            $pcend_record_time = $pcend_record_time_pcend;
                            $pcend_id = $pcend_id_pcend;
                            $pcend_difference_reason = $pcend_difference_reason_pcend;
                        }
                        if ($pcstart_difference_reason == null || $pcstart_difference_reason == "") {
                            $pcstart_difference_reason = $pcend_difference_reason_pcend;
                        }
                        if ($this->isDiffTime($attendance_record_time, $pcstart_record_time, $differencetime)) {
                            $isdiff = true;
                        } else if ($this->isDiffTime($pcend_record_time, $leaving_record_time, $differencetime)) {
                            $isdiff = true;
                        }
                        if ($isdiff) {
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
                                    'difference_reason' => $pcstart_difference_reason
                                );
                            }
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
                        $array_date = array();
                        $brk_item = $item;
                        $brk_working_date = $item->working_date;
                        $brk_user_code = $item->user_code;
                        $attendance_time = "";
                        $attendance_record_time = "";
                        $attendance_id = "";
                        $leaving_time = "";
                        $leaving_record_time = "";
                        $leaving_id = "";
                        $pcstart_time_pcstart = "";
                        $pcstart_record_time_pcstart = "";
                        $pcstart_id_pcstart = "";
                        $pcstart_time_logon = "";
                        $pcstart_record_time_logon = "";
                        $pcstart_id_logon = "";
                        $pcend_time_pcend = "";
                        $pcend_record_time_pcend = "";
                        $pcend_id_pcend = "";
                        $pcend_time_logout = "";
                        $pcend_record_time_logout = "";
                        $pcend_id_logout = "";
                    } else {
                        if ($brk_working_date != $item->working_date) {
                            // 優先順位  pcstart<logon  pcend<logout  windows10はlogonかlogoutが多い
                            // PC起動
                            $pcstart_time = "";
                            $pcstart_record_time = "";
                            $pcstart_id = "";
                            $pcstart_difference_reason = "";
                            if ($pcstart_time_logon != "") {
                                $pcstart_time = $pcstart_time_logon;
                                $pcstart_record_time = $pcstart_record_time_logon;
                                $pcstart_id = $pcstart_id_logon;
                                $pcstart_difference_reason = $pcstart_difference_reason_logon;
                            } else if ($pcstart_time_pcstart != "") {
                                $pcstart_time = $pcstart_time_pcstart;
                                $pcstart_record_time = $pcstart_record_time_pcstart;
                                $pcstart_id = $pcstart_id_pcstart;
                                $pcstart_difference_reason = $pcstart_difference_reason_pcstart;
                            }
                            // PC終了
                            $pcend_time = "";
                            $pcend_record_time = "";
                            $pcend_id = "";
                            $pcend_difference_reason = "";
                            if ($pcend_time_logout != "") {
                                $pcend_time = $pcend_time_logout;
                                $pcend_record_time = $pcend_record_time_logout;
                                $pcend_id = $pcend_id_logout;
                                $pcend_difference_reason = $pcend_difference_reason_logout;
                            } else if ($pcend_time_pcend != "") {
                                $pcend_time = $pcend_time_pcend;
                                $pcend_record_time = $pcend_record_time_pcend;
                                $pcend_id = $pcend_id_pcend;
                                $pcend_difference_reason = $pcend_difference_reason_pcend;
                            }
                            if ($pcstart_difference_reason == null || $pcstart_difference_reason == "") {
                                $pcstart_difference_reason = $pcend_difference_reason_pcend;
                            }
                            if ($this->isDiffTime($attendance_record_time, $pcstart_record_time, $differencetime)) {
                                $isdiff = true;
                            } else if ($this->isDiffTime($pcend_record_time, $leaving_record_time, $differencetime)) {
                                $isdiff = true;
                            }
                            if ($isdiff) {
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
                                    'difference_reason' => $pcstart_difference_reason
                                );
                            }
                            $brk_item = $item;
                            $brk_working_date = $item->working_date;
                            $attendance_time = "";
                            $leaving_time = "";
                            $attendance_time = "";
                            $attendance_record_time = "";
                            $attendance_id = "";
                            $leaving_time = "";
                            $leaving_record_time = "";
                            $leaving_id = "";
                            $pcstart_time_pcstart = "";
                            $pcstart_record_time_pcstart = "";
                            $pcstart_id_pcstart = "";
                            $pcstart_time_logon = "";
                            $pcstart_record_time_logon = "";
                            $pcstart_id_logon = "";
                            $pcend_time_pcend = "";
                            $pcend_record_time_pcend = "";
                            $pcend_id_pcend = "";
                            $pcend_time_logout = "";
                            $pcend_record_time_logout = "";
                            $pcend_id_logout = "";
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
                        if ($pcstart_time_logon == "") {
                            $pcstart_time_logon = $item->scan_time;
                            $pcstart_record_time_logon = $item->record_time;
                            $pcstart_id_logon = $item->id;
                            $pcstart_difference_reason_logon = $item->difference_reason;
                        }
                    }
                    if ($item->mode ==  Config::get('const.C033.pcstart')) {
                        if ($pcstart_time_pcstart == "") {
                            $pcstart_time_pcstart = $item->scan_time;
                            $pcstart_record_time_pcstart = $item->record_time;
                            $pcstart_id_pcstart = $item->id;
                            $pcstart_difference_reason_pcstart = $item->difference_reason;
                        }
                    }
                    if ($item->mode ==  Config::get('const.C033.logout')) {
                        $pcend_time_logout = $item->scan_time;
                        $pcend_record_time_logout = $item->record_time;
                        $pcend_id_logout = $item->id;
                        $pcend_difference_reason_logout = $item->difference_reason;
                    }
                    if ($item->mode ==  Config::get('const.C033.pcend')) {
                        $pcend_time_pcend = $item->scan_time;
                        $pcend_record_time_pcend = $item->record_time;
                        $pcend_id_pcend = $item->id;
                        $pcend_difference_reason_pcend = $item->difference_reason;
                    }
                }
            }
            if ($attendance_time != "" || $leaving_time != "" || $pcstart_time != "" || $pcend_time != "") {
                $isdiff = false;
                // 優先順位  pcstart<logon  pcend<logout  windows10はlogonかlogoutが多い
                // PC起動
                $pcstart_time = "";
                $pcstart_record_time = "";
                $pcstart_id = "";
                $pcstart_difference_reason = "";
                if ($pcstart_time_logon != "") {
                    $pcstart_time = $pcstart_time_logon;
                    $pcstart_record_time = $pcstart_record_time_logon;
                    $pcstart_id = $pcstart_id_logon;
                    $pcstart_difference_reason = $pcstart_difference_reason_logon;
                } else if ($pcstart_time_pcstart != "") {
                    $pcstart_time = $pcstart_time_pcstart;
                    $pcstart_record_time = $pcstart_record_time_pcstart;
                    $pcstart_id = $pcstart_id_pcstart;
                    $pcstart_difference_reason = $pcstart_difference_reason_pcstart;
                }
                // PC終了
                $pcend_time = "";
                $pcend_record_time = "";
                $pcend_id = "";
                $pcend_difference_reason = "";
                if ($pcend_time_logout != "") {
                    $pcend_time = $pcend_time_logout;
                    $pcend_record_time = $pcend_record_time_logout;
                    $pcend_id = $pcend_id_logout;
                    $pcend_difference_reason = $pcend_difference_reason_logout;
                } else if ($pcend_time_pcend != "") {
                    $pcend_time = $pcend_time_pcend;
                    $pcend_record_time = $pcend_record_time_pcend;
                    $pcend_id = $pcend_id_pcend;
                    $pcend_difference_reason = $pcend_difference_reason_pcend;
                }
                if ($pcstart_difference_reason == null || $pcstart_difference_reason == "") {
                    $pcstart_difference_reason = $pcend_difference_reason_pcend;
                }
                if ($this->isDiffTime($attendance_record_time, $pcstart_record_time, $differencetime)) {
                    $isdiff = true;
                } else if ($this->isDiffTime($pcend_record_time, $leaving_record_time, $differencetime)) {
                    $isdiff = true;
                }
                if ($isdiff) {
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
                        'difference_reason' => $pcstart_difference_reason
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

    /**
     * 登録
     *
     * @param Request $request
     * @return response
     */
    public function store(Request $request){
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
            if (!isset($params['user_code'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "user_code", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            if (!isset($params['eventlogs'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "eventlogs", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            // ログインユーザ設定
            $login_user_code = Auth::user()->code;
            $user_code = $params['user_code'];
            $eventlogs = $params['eventlogs'];
            $department_code = null;
            $employment_status = null;
            // eventlogsの検索パラメータ設定
            // 部署雇用形態はusersから
            $apicommon_model = new ApiCommonController();
            $result = $apicommon_model->getUserDepartmentEmploymentRole($login_user_code, null);
            foreach($result as $item) {
                $department_code = $item->department_code;
                $employment_status = $item->employment_status;
                break;
            }
            Log::debug('  user_code = '.$user_code);
            Log::debug('  department_code = '.$department_code);
            Log::debug('  employment_status = '.$employment_status);
            Log::debug('  login_user_code = '.$login_user_code);
            $attendance_model = new AttendanceLog();
            $attendance_model->setParamdepartmentcodeAttribute($department_code);
            $attendance_model->setParamemploymentstatusAttribute($employment_status);
            $attendance_model->setParamusercodeAttribute($user_code);
            $maxdate = $attendance_model->getWorkingdate();
            Log::debug('  maxdate = '.$maxdate);
            Log::debug('  eventlogs count = '.count($eventlogs));
            foreach ($eventlogs as $item) {
                Log::debug('  event_date = '.$item['event_date']);
            }
            // eventlogフィルター
            $collect_eventlogs = new Collection($eventlogs);
            $filtered = null;
            if (isset($maxdate)) {
                Log::debug('  event_date >= '.$maxdate);
                $filtered = $collect_eventlogs->where('event_date', ">=", $maxdate);
            } else {
                Log::debug('  event_date >= 20190101');
                $filtered = $collect_eventlogs->where('event_date', ">=", "20190101");
            }
            Log::debug('  filtered count = '.count($filtered));

            // eventlogsの登録設定
            if (count($filtered) > 0) {
                $systemdate = Carbon::now();
                $attendance_model->setDepartmentcodeAttribute($department_code);
                $attendance_model->setEmploymentstatusAttribute($employment_status);
                $attendance_model->setUsercodeAttribute($user_code);
                $attendance_model->setCreateduserAttribute($login_user_code);
                $attendance_model->setCreatedatAttribute($systemdate);
                $this->insertLog($attendance_model, $filtered);
            }
            // 取得パラメータ設定
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

    /**
     * 差異時間判定
     *
     * @param [type] $details
     * @return boolean
     */
    private function isDiffTime($from_time, $to_time, $diffTime){
        Log::debug('isDiffTime $from_time = '.$from_time);
        Log::debug('isDiffTime $to_time = '.$to_time);
        Log::debug('isDiffTime $diffTime = '.$diffTime);
        if ($diffTime == 0) { return true; }
        if (($from_time == null || $from_time == "") || ($to_time == null || $to_time == "")) 
        { 
            return false;
        }
        // 計算開始
        $apicommon_model = new ApiCommonController();
        $diffTimeSerial = $apicommon_model->diffTimeSerial($from_time, $to_time);
        Log::debug('isDiffTime $diffTimeSerial = '.$diffTimeSerial);
        Log::debug('isDiffTime $diffTime * 60 = '.$diffTime * 60);
        if ($diffTimeSerial < 0) { $diffTimeSerial = 0 - $diffTimeSerial; }
        if ($diffTimeSerial >= $diffTime * 60) { return true; }

        return false;
    }
}
