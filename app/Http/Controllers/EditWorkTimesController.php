<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\StoreUserPost;
use App\Http\Controllers\ApiCommonController;
use App\WorkTime;
use App\UserHolidayKubun;
use Carbon\Carbon;


class EditWorkTimesController extends Controller
{
    private $array_messagedata = array();

    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        $authusers = Auth::user();
        return view('edit_work_times',
            compact(
                'authusers'
            ));
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
            if (!isset($params['code'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "code", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $details,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            if (!isset($params['ymd'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "ymd", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $details,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            // 前日から翌日にする→当日に変更
            $target_ymd =  new Carbon($params['ymd']);
            $year = $target_ymd->format('Y');
            $month = $target_ymd->format('m');
            $dd = $target_ymd->format('d');
            $ymd_start = $year."/".$month."/".$dd." 00:00:00";

            $ymd_end = $year."/".$month."/".$dd." 23:59:59";

            $closing_start = new Carbon($ymd_start);
            $closing_end = new Carbon($ymd_end);
            $closing_start = $closing_start->copy()->subMonth()->addDay()->format('Y/m/d H:i:s');
            $closing_end = $closing_end->format('Y/m/d H:i:s');
            $code =  $params['code'];
    
            $work_times = new WorkTime();
            $work_times->setUsercodeAttribute($code);
            $work_times->setParamStartDateAttribute($ymd_start);
            $work_times->setParamEndDateAttribute($ymd_end);
    
            $details = $work_times->getUserDetails();
            // $count = 0;
            // $before_date = "";
            // $apicommon_model = new ApiCommonController();
            // foreach ($details as $detail) {
            //     $detail->kbn_flag = 0;
            //     $detail->user_holiday_kbn="";
            //     if(isset($detail->record_time)){
            //         $carbon = new Carbon($detail->record_time);
            //         $detail->date = $carbon->copy()->format('Y/m/d');
            //         $search_date = $carbon->copy()->format('Ymd');
            //         // 個人休暇区分取得
            //         $holiday_kbn = $apicommon_model->getUserHolidaykbn($code, $search_date);
            //         $detail->time = $carbon->copy()->format('H:i');
            //         /*if($detail->date != $before_date){
            //             $detail->kbn_flag = 1;
            //         } */
            //         if(isset($holiday_kbn)){
            //             $detail->kbn_flag = 1;
            //             $detail->user_holiday_kbn=$holiday_kbn;
            //         }
            //         $before_date = $detail->date;
            //         Log::debug('$detail->time = '.$detail->user_holiday_kbn);
            //     }
            // }
            /*if (count($details) == 0) {
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.not_found_data');
                $result = false;
            }*/
            return response()->json(
                ['result' => $result, 'details' => $details,
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
     *  20200320    登録も削除も更新も現在ここで処理している
     *
     * @param Request $request
     * @return response
     */
    public function fixtime(Request $request){
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
            if (!isset($params['target_date'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "target_date", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            if (!isset($params['details'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "details", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            if (!isset($params['beforeids'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "beforeids", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $user_code = $params['user_code'];
            $target_date = $params['target_date'];
            $details = $params['details'];
            $beforeids = $params['beforeids'];
            // fixTimeData implement
            $array_impl_addAttendanceWork = array (
                'user_code' => $user_code,
                'target_date' => $target_date,
                'details' => $details,
                'beforeids' => $beforeids
            );
            // $this->fixTimeData($array_impl_fixData);
            $apicommon_model = new ApiCommonController();
            $apicommon_model->addAttendanceWork($array_impl_addAttendanceWork);
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
    // private function fixTimeData($params){
    //     $user_code = $params['user_code'];
    //     $target_date = $params['target_date'];
    //     $details = $params['details'];
    //     $beforeids = $params['beforeids'];

    //     $systemdate = Carbon::now();
    //     $work_time_model = new WorkTime();
    //     $apicommon_model = new ApiCommonController();

    //     DB::beginTransaction();
    //     try{
    //         $user = Auth::user();
    //         $login_user_code = $user->code;
    //         $login_department_code = null;
    //         $dt = new Carbon($target_date);
    //         $target_date = $dt->format('Ymd');
    //         // ログインユーザー部署ApiCommonControllerで取得
    //         $login_department_code = null;
    //         $dep_results = $apicommon_model->getUserDepartment($login_user_code, $target_date);
    //         foreach($dep_results as $item) {
    //             $login_department_code = $item->department_code;
    //             break;
    //         }
    //         $department_code = null;
    //         // 休暇登録
    //         //部署選択されていない場合は部署コードないためApiCommonControllerで取得
    //         if ($department_code == null) {
    //             $dep_results = $apicommon_model->getUserDepartment($user_code, $target_date);
    //             foreach($dep_results as $dep_result) {
    //                 $department_code = $dep_result->department_code;
    //                 break;
    //             }
    //         }
    //         $working_date = $target_date;
    //         $user_holiday = new UserHolidayKubun();
    //         $user_holiday->setParamUsercodeAttribute($user_code);
    //         $user_holiday->setParamDepartmentcodeAttribute($department_code);
    //         $user_holiday->setParamdatefromAttribute($working_date);
    //         $user_holiday->setSystemDateAttribute($systemdate);
    //         // 既に存在する場合は論理削除する
    //         $is_exists = $user_holiday->isExistsKbn();
    //         if($is_exists){
    //             $user_holiday->delKbn();
    //         }
    //         $user_holiday_kubuns_id = null;
    //         foreach($details as $item) {
    //             if($item['kbn_flag'] == 1){     // 休暇区分のみ登録
    //                 $user_holiday->setWorkingdateAttribute($working_date);
    //                 $user_holiday->setDepartmentcodeAttribute($department_code);
    //                 $user_holiday->setUsercodeAttribute($user_code);
    //                 $user_holiday->setHolidaykubunAttribute($item['user_holiday_kbn']);
    //                 $user_holiday->setCreateduserAttribute($login_user_code);
    //                 $user_holiday->insertKbn();
    //                 // 勤怠時刻にIDを登録するのでSELECTする
    //                 $id_results = $user_holiday->getDetail();
    //                 foreach($id_results as $item_id) {
    //                     $user_holiday_kubuns_id = $item_id->id;
    //                     break;
    //                 }
    //                 // 休暇の場合は先頭行のみの処理でよいのでbreakする
    //                 break;
    //             }
    //         }
    //         // 勤怠時刻登録
    //         // beforeidsが存在した場合は論理削除する
    //         for($i=0;$i<count($beforeids);$i++) {
    //             $work_time_model->setIdAttribute($beforeids[$i]);
    //             $work_time_model->setEditordepartmentcodeAttribute($login_department_code);
    //             $work_time_model->setEditorusercodeAttribute($login_user_code);
    //             $work_time_model->setUpdateduserAttribute($login_user_code);
    //             $work_time_model->setSystemDateAttribute($systemdate);
    //             $work_time_model->delWorkTime();
    //         }
    //         foreach($details as $item) {
    //             //部署選択されていない場合は部署コードないためApiCommonControllerで取得
    //             if ($department_code == null) {
    //                 if (isset($item['department_code'])) {
    //                     if ($item['department_code'] == "" || $item['department_code'] == null) {
    //                         $dep_results = $apicommon_model->getUserDepartment($item['user_code'], $target_date);
    //                         foreach($dep_results as $dep_result) {
    //                             $department_code = $dep_result->department_code;
    //                             break;
    //                         }
    //                     } else {
    //                         $department_code = $item['department_code'];
    //                     }
    //                 } else {
    //                     $dep_results = $apicommon_model->getUserDepartment($item['user_code'], $target_date);
    //                     foreach($dep_results as $dep_result) {
    //                         $department_code = $dep_result->department_code;
    //                         break;
    //                     }
    //                 }
    //             }
    //             $record_time = null;
    //             if ($item['time'] != "" && $item['time'] != null) {
    //                 $record_time = $item['date']." ".$item['time'];     // DB用
    //             } else {
    //                 $record_time = $item['date']." 00:00:01";
    //             }
    //             $work_time_model->setUsercodeAttribute($item['user_code']);
    //             $work_time_model->setDepartmentcodeAttribute($department_code);
    //             $work_time_model->setRecordtimeAttribute($record_time);
    //             $work_time_model->setModeAttribute($item['mode']);
    //             $work_time_model->setUserholidaykubunsidAttribute($user_holiday_kubuns_id);
    //             $work_time_model->setCreateduserAttribute($login_user_code);
    //             $work_time_model->setSystemDateAttribute($systemdate);
    //             $positions_data = null; 
    //             if ((isset($item['x_positions']) && isset($item['y_positions']))) {
    //                 if (($item['x_positions'] != "") && ($item['y_positions'] != "")) {
    //                     $positions_data = $item['x_positions'].' '.$item['y_positions'];
    //                 }
    //             }
    //             $work_time_model->setPositionsAttribute($positions_data);
    //             $work_time_model->setIseditorAttribute(true);
    //             $work_time_model->setEditordepartmentcodeAttribute($login_department_code);
    //             $work_time_model->setEditorusercodeAttribute($login_user_code);
    //             $work_time_model->insertWorkTime();
    //         }
    //         DB::commit();

    //     }catch(\PDOException $pe){
    //         DB::rollBack();
    //         throw $pe;
    //     }catch(\Exception $e){
    //         Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
    //         Log::error($e->getMessage());
    //         DB::rollBack();
    //         throw $e;
    //     }
    // }

    /**
     * 登録
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request){
        $this->array_messagedata = array();
        $code = '';
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
            $this->insertData($details);
    
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
     * INSERT
     *
     * @param [type] $name
     * @return void
     */
    private function insertData($details){
        DB::beginTransaction();
        try{
            $systemdate = Carbon::now();
            $work_time_model = new WorkTime();
            $user = Auth::user();
            $login_user_code = $user->code;
            $apicommon_model = new ApiCommonController();
            $dt = new Carbon($details['date']);
            $target_date = $dt->format('Ymd');

            //追加の場合部署選択されていない場合は部署コードないためApiCommonControllerで取得
            $department_code = $details['department_code'];
            if ($details['department_code'] == "" || $details['department_code'] == null) {
                $dep_results = $apicommon_model->getUserDepartment($details['user_code'], $target_date);
                foreach($dep_results as $item) {
                    $department_code = $item->department_code;
                    break;
                }
            }
            // ログインユーザー部署ApiCommonControllerで取得
            $login_department_code = null;
            $dep_results = $apicommon_model->getUserDepartment($login_user_code, $target_date);
            foreach($dep_results as $item) {
                $login_department_code = $item->department_code;
                break;
            }
            // 勤怠時刻登録
            if ($details['time'] != "" && $details['time'] != null) {
                $record_time = $details['date']." ".$details['time'];     // DB用
            } else {
                $record_time = $details['date']." 00:00:01";
            }
            $work_time_model->setUsercodeAttribute($details['user_code']);
            $work_time_model->setDepartmentcodeAttribute($department_code);
            $work_time_model->setRecordtimeAttribute($record_time);
            $work_time_model->setModeAttribute($details['mode']);
            $work_time_model->setCreateduserAttribute($login_user_code);
            $work_time_model->setSystemDateAttribute($systemdate);
            if($details['id'] != "" && $details['id'] != null) {
                // 既に存在する場合は論理削除する
                $work_time_model->setIdAttribute($details[$details_index]['id']);
                $work_time_model->setEditordepartmentcodeAttribute($login_department_code);
                $work_time_model->setEditorusercodeAttribute($login_user_code);
                $work_time_model->setUpdateduserAttribute($login_user_code);
                $work_time_model->setSystemDateAttribute($systemdate);
                $work_time_model->delWorkTime();
            }
            $work_time_model->setPositionsAttribute(null);
            $work_time_model->setIseditorAttribute(true);
            $work_time_model->setEditordepartmentcodeAttribute($login_department_code);
            $work_time_model->setEditorusercodeAttribute($login_user_code);
            $work_time_model->insertWorkTime();
        
            // 休暇登録
            if($details['kbn_flag'] == 1){     // 休暇区分のみ登録
                $ymd = new Carbon($details['date']);
                $working_date = $ymd->copy()->format('Ymd');
                $user_holiday = new UserHolidayKubun();
                $user_holiday->setUsercodeAttribute($details['user_code']);
                $user_holiday->setWorkingdateAttribute($working_date);
                $user_holiday->setSystemDateAttribute($systemdate);
                // 既に存在する場合は論理削除する
                $is_exists = $user_holiday->isExistsKbn();
                if($is_exists){
                    $user_holiday->delKbn();
                }
                $user_holiday->setDepartmentcodeAttribute($department_code);
                $user_holiday->setHolidaykubunAttribute($details['user_holiday_kbn']);
                $user_holiday->setCreateduserAttribute($login_user_code);
                $user_holiday->insertKbn();
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
            if (!isset($params['index'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "index", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $details = $params['details'];
            $details_index = $params['index'];
            $this->fixData($details, $details_index);
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
    private function fixData($details, $details_index){
        $systemdate = Carbon::now();
        $work_time_model = new WorkTime();
        $apicommon_model = new ApiCommonController();
        $user = Auth::user();
        $login_user_code = $user->code;
        $login_department_code = null;
        $dt = new Carbon($details[$details_index]['date']);
        $target_date = $dt->format('Ymd');

        DB::beginTransaction();
        try{
            // ログインユーザー部署ApiCommonControllerで取得
            $login_department_code = null;
            $dep_results = $apicommon_model->getUserDepartment($login_user_code, $target_date);
            foreach($dep_results as $item) {
                $login_department_code = $item->department_code;
                break;
            }
            // 勤怠時刻登録
            if ($details['time'] != "" && $details['time'] != null) {
                $record_time = $details['date']." ".$details['time'];     // DB用
            } else {
                $record_time = $details['date']." 00:00:01";
            }
            $work_time_model->setUsercodeAttribute($details['user_code']);
            $work_time_model->setDepartmentcodeAttribute($details['department_code']);
            $work_time_model->setRecordtimeAttribute($record_time);
            $work_time_model->setModeAttribute($details['mode']);
            $work_time_model->setCreateduserAttribute($login_user_code);
            $work_time_model->setSystemDateAttribute($systemdate);
            // 既に存在するので論理削除する
            $work_time_model->setIdAttribute($details[$details_index]['id']);
            $work_time_model->setEditordepartmentcodeAttribute($login_department_code);
            $work_time_model->setEditorusercodeAttribute($login_user_code);
            $work_time_model->setUpdateduserAttribute($login_user_code);
            $work_time_model->setSystemDateAttribute($systemdate);
            $work_time_model->delWorkTime();
            $positions_data = null; 
            if ((isset($details['x_positions']) && isset(details['y_positions']))) {
                if (($details['x_positions'] != "") && ($details['y_positions'] != "")) {
                    $positions_data = $details['x_positions'].' '.$details['y_positions'];
                }
            }
            // $work_time_model->setPositionsAttribute($positions_data);
            // $work_time_model->setIseditorAttribute(true);
            // $work_time_model->setEditordepartmentcodeAttribute($login_department_code);
            // $work_time_model->setEditorusercodeAttribute($login_user_code);
            // $work_time_model->insertWorkTime();
            $this->insertWorkTime($details);
            // 休暇登録
            if($details[$details_index]['kbn_flag'] == 1){     // 休暇区分のみ登録
                $ymd = new Carbon($details[$details_index]['date']);
                $working_date = $ymd->copy()->format('Ymd');
                $user_holiday = new UserHolidayKubun();
                $user_holiday->setUsercodeAttribute($details[$details_index]['user_code']);
                $user_holiday->setWorkingdateAttribute($working_date);
                $user_holiday->setSystemDateAttribute($systemdate);
                // 既に存在する場合は論理削除する
                $is_exists = $user_holiday->isExistsKbn();
                if($is_exists){
                    $user_holiday->delKbn();
                }
                $user_holiday->setDepartmentcodeAttribute($details[$details_index]['department_code']);
                $user_holiday->setHolidaykubunAttribute($details[$details_index]['user_holiday_kbn']);
                $user_holiday->setCreateduserAttribute($login_user_code);
                $user_holiday->insertKbn();
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
     * レコード削除
     *
     * @param Request $request
     * @return void
     */
    public function del(Request $request){
        $this->array_messagedata = array();
        $details = array();
        $result = true;
        $user = Auth::user();
        $login_user_code = $user->code;
        $login_department_code = null;
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
            $work_time_model = new WorkTime();
            $apicommon_model = new ApiCommonController();
            $systemdate = Carbon::now();
            // ログインユーザー部署ApiCommonControllerで取得
            $login_department_code = null;
            $dt = new Carbon($details['date']);
            $target_date = $dt->format('Ymd');
            $dep_results = $apicommon_model->getUserDepartment($login_user_code, $target_date);
            foreach($dep_results as $item) {
                $login_department_code = $item->department_code;
                break;
            }
            $work_time_model->setIdAttribute($details['id']);
            $work_time_model->setSystemDateAttribute($systemdate);
            $work_time_model->setEditordepartmentcodeAttribute($login_department_code);
            $work_time_model->setEditorusercodeAttribute($login_user_code);
            $work_time_model->setUpdateduserAttribute($login_user_code);
            $work_time_model->delWorkTime();
    
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
     * 勤怠新規登録
     *
     * @param Request $request
     * @return response
     */
    // public function add(Request $request){
    //     // request
    //     $date = $request->date;
    //     $time = $request->time;
    //     $carbon = new Carbon($date);
    //     $date = $carbon->format("Y/m/d");
    //     $record_time = $date." ".$time;     // DB用
    //     $code = $request->user_code;
    //     $mode = $request->mode;
    //     $holiday_kbn = $request->holiday_kbn;
    //     $work_time = new WorkTime();
    //     $systemdate = Carbon::now();
    //     $department_code = DB::table('users')->where('code', $code)->where('is_deleted', 0)->value('department_code');
    //     $user = Auth::user();
    //     $login_user_code = $user->code;
    //     $response = collect();

    //     if($holiday_kbn != ""){     // 休暇区分のみ登録
    //         DB::beginTransaction();
    //             try{
    //                 $record_time = $date;
    //                 $ymd = new Carbon($record_time);
    //                 $working_date = $ymd->copy()->format('Ymd');
    //                 $user_holiday = new UserHolidayKubun();
    //                 $user_holiday->setUsercodeAttribute($code);
    //                 $user_holiday->setWorkingdateAttribute($working_date);
    //                 $user_holiday->setSystemDateAttribute($systemdate);
    //                 // 既に存在する場合は論理削除する
    //                 $is_exists = $user_holiday->isExistsKbn();
    //                 if($is_exists){
    //                     $user_holiday->delKbn();
    //                 }
    //                 $user_holiday->setDepartmentcodeAttribute($department_code);
    //                 $user_holiday->setHolidaykubunAttribute($holiday_kbn);
    //                 $user_holiday->setCreateduserAttribute($login_user_code);
    //                 $user_holiday->insertKbn();

    //                 // 空の出勤データも登録
    //                 $work_time->setUsercodeAttribute($code);
    //                 $work_time->setDepartmentcodeAttribute($department_code);
    //                 $record_time = $date." "."0:00:00";
    //                 $work_time->setRecordtimeAttribute($record_time);
    //                 $work_time_model->setIseditorAttribute(true);
    //                 $work_time->setCreateduserAttribute($login_user_code);
    //                 $work_time->setSystemDateAttribute($systemdate);
    //                 $result = $work_time->insertWorkTime();
            
    //                 DB::commit();
    //                 $result = true;

    //             }catch(\PDOException $e){
    //                 DB::rollBack();
    //                 $result = false;
    //             }
    
    //     }else{                      // 通常の登録
    //         DB::beginTransaction();
    //             try{
    //                 $work_time->setUsercodeAttribute($code);
    //                 $work_time->setDepartmentcodeAttribute($department_code);
    //                 $work_time->setRecordtimeAttribute($record_time);
    //                 $work_time->setModeAttribute($mode);
    //                 $work_time->setCreateduserAttribute($login_user_code);
    //                 $work_time->setSystemDateAttribute($systemdate);
    //                 $work_time_model->setIseditorAttribute(true);
    //                 $result = $work_time->insertWorkTime();
    //                 DB::commit();
    //                 $result = true;

    //             }catch(\PDOException $e){
    //                 DB::rollBack();
    //                 $result = false;
    //             }
    //     }
    //     if($result){
    //         $response->put('result',self::SUCCESS);
    //     }else{
    //         $response->put('result',self::FAILED);
    //     }
    //     return $response;
        
    // }

    /**
     * 登録
     *
     * @param [type] $details
     * @return void
     */
    private function insertWorkTime($details){
        $work_time = new WorkTime();
        $user_holiday = new UserHolidayKubun();
        $systemdate = Carbon::now();
        $user = Auth::user();
        $converts = array();
        $converts = $details;       // 個人休暇登録用
        $login_user_code = $user->code;
        $login_department_code = null;
        DB::beginTransaction();
        try{
            $work_time->setCreateduserAttribute($login_user_code);
            $work_time->setSystemDateAttribute($systemdate);
            foreach ($details as $detail) {
                if ($login_department_code == null) {
                    // ログインユーザー部署ApiCommonControllerで取得
                    $dt = new Carbon($detail['date']);
                    $target_date = $dt->format('Ymd');
                    $dep_results = $apicommon_model->getUserDepartment($login_user_code, $target_date);
                    foreach($dep_results as $item) {
                        $login_department_code = $item->department_code;
                        break;
                    }
                }
                    // 論理削除新規
                if(isset($detail['id'])){
                    if($details['id'] != "" && $details['id'] != null) {
                        // 既に存在する場合は論理削除する
                        $work_time->setIdAttribute($detail['id']);
                        $work_time->setSystemDateAttribute($systemdate);
                        $work_time->setEditordepartmentcodeAttribute($login_department_code);
                        $work_time->setEditorusercodeAttribute($login_user_code);
                        $work_time->setUpdateduserAttribute($login_user_code);
                        $work_time->delWorkTime();
                    }
                }
                // 新規登録
                if(!isset($detail['time'])){
                    $detail['time'] = "00:00:00";
                }
                $record_time = $detail['date']." ".$detail['time'];
                $work_time->setUsercodeAttribute($detail['user_code']);
                $work_time->setDepartmentcodeAttribute($detail['department_code']);
                $work_time->setRecordtimeAttribute($record_time);
                $work_time->setModeAttribute($detail['mode']);
                if(isset($detail['user_holiday_kbn'])){         // １日休暇が入っているものはmodeはnullにする
                    if ($detail['user_holiday_kbn'] == Config::get('const.C013.morning_off')) {
                        $work_time->setModeAttribute($detail['mode']);
                    } elseif ($detail['user_holiday_kbn'] == Config::get('const.C013.afternoon_off')) {
                        $work_time->setModeAttribute($detail['mode']);
                    } elseif ($detail['user_holiday_kbn'] == Config::get('const.C013.late_work')) {
                        $work_time->setModeAttribute($detail['mode']);
                    } elseif ($detail['user_holiday_kbn'] == Config::get('const.C013.leave_early_work')) {
                        $work_time->setModeAttribute($detail['mode']);
                    } else {
                        $work_time->setModeAttribute(null);
                    }
                } else {
                    $work_time->setModeAttribute($detail['mode']);
                }
    
                $work_time_model->setIseditorAttribute(true);
                $work_time->insertWorkTime();
                if($detail['kbn_flag'] == 1){         // 個人休暇が入っているものはuser_holiday_kubunsへ登録
                    $date = new Carbon($detail['record_time']);
                    $working_date = $date->copy()->format('Ymd');
                    $user_holiday->setUsercodeAttribute($detail['user_code']);
                    $user_holiday->setWorkingdateAttribute($working_date);
                    $user_holiday->setSystemDateAttribute($systemdate);
                    // 既に存在する場合は論理削除する
                    $is_exists = $user_holiday->isExistsKbn();
                    if($is_exists){
                        $user_holiday->delKbn();
                    }
                    $user_holiday->setDepartmentcodeAttribute($detail['department_code']);
                    $user_holiday->setHolidaykubunAttribute($detail['user_holiday_kbn']);
                    $user_holiday->setCreateduserAttribute($login_user_code);
                    $user_holiday->insertKbn();
                }
            }
            
            DB::commit();
            return true;

        }catch(\PDOException $e){
            DB::rollBack();
            return false;
        }
    }

    /**
     * 休暇判定
     * 
     * @param [type] $details
     * @return void
     */
    private function getHoridayinfo($user_holiday_kbn){
        $atendance_time = null;
        $leaving_time = null;
        $mode = null;
        if ($user_holiday_kbn == Config::get('const.C013.deemed_business_trip')) {
            $work_time->setModeAttribute($detail['mode']);
        } elseif ($detail['user_holiday_kbn'] == Config::get('const.C013.deemed_direct_go')) {
            $work_time->setModeAttribute($detail['mode']);
        } elseif ($detail['user_holiday_kbn'] == Config::get('const.C013.deemed_direct_return')) {
            $work_time->setModeAttribute($detail['mode']);
        } elseif ($detail['user_holiday_kbn'] == Config::get('const.C013.leave_early_work')) {
            $work_time->setModeAttribute($detail['mode']);
        } else {
            $work_time->setModeAttribute(null);
        }
    }
}
