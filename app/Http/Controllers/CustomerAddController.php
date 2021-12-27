<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\StoreUserPost;
use Illuminate\Support\Facades\Auth;
use App\Customer;
use App\Offices;

//use App\WorkingTimeTable;
//use App\GeneralCodes;
//use App\CalendarSettingInformation;
//use App\CardInformation;
//use App\WorkTime;
//use App\WorkTimeLog;
//use App\WorkingTimedate;
// use App\ShiftInformation;
use App\AttendanceLog;

use Carbon\Carbon;
use App\Http\Controllers\ApiCommonController;
use App\Http\Controllers\SttingShiftTimeController;

class CustomerAddController extends Controller
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

        return view('edit_customer',
            compact(
                'authusers',
                'isexistdownload',
                'settingtable'
            ));
    }

    /**
     * 登録
     *
     * @param Request $request
     * @return void
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
            if (!isset($params['details'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "details", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $details = $params['details'];
            // ログインIDチェック
            $target_user_code = null;
            if ($details['code'] != "") {
                $authuser = Auth::user();
                $login_user_code = $authuser->code;
                $login_account_id = $authuser->account_id;
                $user_model = new UserModel();
                $target_user_code = $details['code'];
                $user_model->setParamAccountidAttribute($login_account_id);
                $user_model->setParamcodeAttribute($target_user_code);
                $isExists = $user_model->isExistsCode();
                if ($isExists) {
                    $this->array_messagedata[] = str_replace('{0}', "ログインID", Config::get('const.MSG_ERROR.already_item'));
                    $result = false;
                    return response()->json(
                        ['result' => $result,
                        Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                    );
                }
            }
            // insert
            $result = $this->insert($details, $login_account_id, $target_user_code);
            if (!$result) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "calendarparam", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
            }
            return response()->json(
                ['result' => $result,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            return response()->json(
                ['result' => false,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\Exception $e){
            Log::error($e->getMessage());
            return response()->json(
                ['result' => false,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }
    }

    /**
     * 新規
     *
     * @param [type] $data
     * @return void
     */
    private function insert($data, $account_id, $target_user_code){
        DB::beginTransaction();
        try{
            $users = new UserModel();
            $systemdate = Carbon::now();
            $authuser = Auth::user();
            $user_code = $authuser->code;
            // $users->setApplytermfromAttribute(Config::get('const.INIT_DATE.initdate'));
            $applyfrom = new Carbon($data['apply_term_from']);
            $users->setAccountidAttribute($account_id);
            $users->setApplytermfromAttribute($applyfrom->format('Ymd'));
            $users->setCodeAttribute($target_user_code);
            $users->setDepartmentcodeAttribute($data['department_code']);
            $users->setEmploymentstatusAttribute($data['employment_status']);
            $users->setNameAttribute($data['name']);
            $users->setShortNameAttribute($data['short_name']);
            $users->setKanaAttribute($data['kana']);
            $users->setOfficialpositionAttribute($data['official_position']);
            $users->setKillfromdateAttribute(Config::get('const.INIT_DATE.maxdate'));
            $users->setWorkingtimetablenoAttribute($data['working_timetable_no']);
            $users->setEmailAttribute($data['email']);
            $users->setMobileEmailAttribute($data['mobile_email']);
            $users->setPasswordAttribute(bcrypt($data['password']));
            $users->setCreatedatAttribute($systemdate);
            $users->setCreateduserAttribute($user_code);
            $users->setManagementAttribute($data['management']);
            $users->setRoleAttribute($data['role']);
            // insert
            $users->insertNewUser();
            // calendar作成
            // showCalc implement
            $array_impl_calendarByUser = array (
                'users' => $users,
                'datas' => $data
            );
            $this->calendarByUser($array_impl_calendarByUser);
            DB::commit();
            return true;
        }catch(\PDOException $pe){
            DB::rollBack();
            throw $pe;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * カレンダーを初期設定
     *
     */
    private function calendarByUser($params){

        $users = $params['users'];
        $datas = $params['datas'];
        $fromdate = $users->getApplytermfromAttribute();
        $dt = new Carbon($fromdate);
        Log::debug(' calendarByUser  $dt = '.$dt);
        $dtfrom = $dt->copy()->subDay();
        Log::debug(' calendarByUser  $dt = '.$dt);
        Log::debug(' calendarByUser  $dtfrom = '.$dtfrom);
        $todate = new Carbon($dtfrom);
        Log::debug(' calendarByUser  $dt = '.$dt);
        Log::debug(' calendarByUser  $dtfrom = '.$dtfrom);
        Log::debug(' calendarByUser  $todate = '.$todate);
        $todate->addYear()->subDay();
        Log::debug(' calendarByUser  $dt = '.$dt);
        Log::debug(' calendarByUser  $dtfrom = '.$dtfrom);
        Log::debug(' calendarByUser  $todate = '.$todate);
        try{
            $calendar_setting_model = new CalendarSettingInformation();
            // 作成
            $user = Auth::user();
            $login_user_code = $user->code;
            $login_account_id = $user->account_id;
            $calendar_setting_model->setCreateduserAttribute($login_user_code);
            $calendar_setting_model->setParamAccountidAttribute($login_account_id);
            $calendar_setting_model->getParamemploymentstatusAttribute($users->getEmploymentstatusAttribute());
            $calendar_setting_model->getParamdepartmentcodeAttribute($users->getDepartmentcodeAttribute());
            $calendar_setting_model->setParamusercodeAttribute($users->getCodeAttribute());
            Log::debug(' calendarByUser  $dtfrom = '.$dtfrom);
            Log::debug(' calendarByUser  $todate = '.$todate);
            $calendar_setting_model->setParamfromdateAttribute($dtfrom->format('Ymd'));
            $calendar_setting_model->setParamtodateAttribute($todate->format('Ymd'));
            // 削除
            $calendar_setting_model->delCalendarSettingDate();
            // 作成
            $calendar_setting_model->setParamfromdateAttribute($dtfrom->format('Ymd'));
            $calendar_setting_model->setEmploymentstatusAttribute($users->getEmploymentstatusAttribute());
            $calendar_setting_model->setDepartmentcodeAttribute($users->getDepartmentcodeAttribute());
            $calendar_setting_model->setUsercodeAttribute($users->getCodeAttribute());
            $calendar_setting_model->setWorkingtimetablenoAttribute($users->getWorkingtimetablenoAttribute());
            $calendar_setting_model->setAccountidAttribute($login_account_id);
            $results = $calendar_setting_model->getCalenderDateYear(Config::get('const.CALENDAR_PTN.ptn1'), $datas);
            $temp_array = array();
            foreach($results as $result) {
                $temp_collect = collect($result);
                $temp_array[] = $temp_collect->toArray();
            }
            if (count($temp_array) > 0) {
                $results = $calendar_setting_model->insCalenderDateYear($temp_array);
            }

        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * ユーザー編集
     *
     * @param Request $request
     * @return response
     */
    public function fixUser(Request $request){
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
            if (!isset($params['before_details'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "before_details", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $details = $params['details'];
            $before_details = $params['before_details'];
            $this->update($details, $before_details);
            return response()->json(
                ['result' => true,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            return response()->json(
                ['result' => false,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\Exception $e){
            Log::error($e->getMessage());
            return response()->json(
                ['result' => false,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }
    }


    /**
     * UPDATE
     *
     * @param [type] $details
     * @return boolean
     */
    private function update($data, $before_data){
        $systemdate = Carbon::now();
        $user = Auth::user();
        $login_user_code = $user->code;
        $login_account_id = $user->account_id;
        DB::beginTransaction();
        try{
            $user_model = new UserModel();
            $carbon = new Carbon($data['apply_term_from']);
            $temp_from = $carbon->copy()->format('Ymd');
            $apply_term_from = $temp_from;
            $user_model->setParamAccountidAttribute($login_account_id);
            $user_model->setAccountidAttribute($login_account_id);
            $user_model->setApplytermfromAttribute($apply_term_from);
            $user_model->setCodeAttribute($data['code']);
            $user_model->setDepartmentcodeAttribute($data['department_code']);
            $user_model->setEmploymentstatusAttribute($data['employment_status']);
            $user_model->setNameAttribute($data['name']);
            $user_model->setShortNameAttribute($data['short_name']);
            $user_model->setKanaAttribute($data['kana']);
            $user_model->setOfficialpositionAttribute($data['official_position']);
            $temp_from = Config::get('const.INIT_DATE.maxdate');
            if ($data['kill_from_date'] != "" && $data['kill_from_date'] != null) {
                $carbon = new Carbon($data['kill_from_date']);
                $temp_from = $carbon->copy()->format('Ymd');
            }
            $kill_from_date = $temp_from;
            $user_model->setKillfromdateAttribute($kill_from_date);
            Log::debug('UserAddController update working_timetable_no = '.$data['working_timetable_no']);
            $user_model->setWorkingtimetablenoAttribute($data['working_timetable_no']);
            $user_model->setEmailAttribute($data['email']);
            $user_model->setMobileEmailAttribute($data['mobile_email']);
            $user_model->setCreatedatAttribute($systemdate);
            $user_model->setCreateduserAttribute($login_user_code);
            $user_model->setManagementAttribute($data['management']);
            $user_model->setRoleAttribute($data['role']);
            $isUpdateDepartment = false;
            $isUpdateEmployment = false;
            if ($data['id'] == "" || $data['id'] == null) {
                $user_model->setCreateduserAttribute($login_user_code);
                $user_model->setCreatedatAttribute($systemdate);
                Log::debug('UserAddController update password = '.$data['code']);
                $user_model->setPasswordAttribute(bcrypt($data['code']));
                $user_model->insertNewUser();
                $isUpdateDepartment = true;
                $isUpdateEmployment = true;
            } else {
                $user_model->setIdAttribute($data['id']);   
                $user_model->setUpdateduserAttribute($login_user_code);
                $user_model->setUpdatedatAttribute($systemdate);
                $user_model->updateUser();
                $updateapplytermfrom = null;
                $updateDepartment = null;
                $updateEmployment = null;
                $before_updateapplytermfrom = null;
                $before_updateDepartment = null;
                $before_updateEmployment = null;
                if ($data['apply_term_from'] != null && $data['apply_term_from'] != "") { $updateapplytermfrom = $data['apply_term_from']; }
                if ($before_data['apply_term_from'] != null && $before_data['apply_term_from'] != "") { $before_updateapplytermfrom = $before_data['apply_term_from']; }
                if ($data['department_code'] != null && $data['department_code'] != "") { $updateDepartment = $data['department_code']; }
                if ($before_data['department_code'] != null && $before_data['department_code'] != "") { $before_updateDepartment = $before_data['department_code']; }
                if ($data['employment_status'] != null && $data['employment_status'] != "") { $updateEmployment = $data['employment_status']; }
                if ($before_data['employment_status'] != null && $before_data['employment_status'] != "") { $before_updateEmployment = $before_data['employment_status']; }
                if ($updateapplytermfrom != $before_updateapplytermfrom) {
                    $isUpdateDepartment = true;
                    $isUpdateEmployment = true;
                } else {
                    if ($updateDepartment != $before_updateDepartment) {
                        $isUpdateDepartment = true;
                    }
                    if ($updateEmployment != $before_updateEmployment) {
                        $isUpdateEmployment = true;
                    }
                }
            }
            // 部署・雇用形態変更ありの場合
            if ($isUpdateDepartment || $isUpdateEmployment) {
                // upDepartmentEmployment implement
                $array_impl_upDepartmentEmployment = array (
                    'isUpdateDepartment' => $isUpdateDepartment,
                    'isUpdateEmployment' => $isUpdateEmployment,
                    'login_user_code' => $login_user_code,
                    'account_id' => $login_account_id,
                    'details' => $data,
                    'before_details' => $before_data
                );
                $this->upDepartmentEmployment($array_impl_upDepartmentEmployment);
            }
            DB::commit();

        }catch(\PDOException $pe){
            Log::error($pe->getMessage());
            DB::rollBack();
            throw $pe;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * ユーザー削除
     *
     * @param Request $request
     * @return void
     */
    public function del(Request $request){
        $this->array_messagedata = array();
        $code = "";
        $result = true;
        $authuser = Auth::user();
        $login_user_code = $authuser->code;
        $login_account_id = $authuser->account_id;
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
            Log::debug('useradd del id = '.$details['id']);
            $id = $details['id'];
            DB::beginTransaction();
            // ユーザー履歴削除
            $this->updateIsDelete($id);
            // ほかのテーブルの部署・雇用形態を戻す
            // upDepartmentEmploymentTableBefore implement
            $array_impl_upDepartmentEmploymentTableBefore = array (
                'isUpdateDepartment' => true,
                'isUpdateEmployment' => true,
                'isDelete' => true,
                'login_user_code' => $login_user_code,
                'account_id' => $login_account_id,
                'details' => $details
            );
            $this->upDepartmentEmploymentTableBefore($array_impl_upDepartmentEmploymentTableBefore);
            DB::commit();
        
            return response()->json(
                ['result' => $result,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
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
     * 論理削除
     *
     * @param [type] $code
     * @return void
     */
    private function updateIsDelete($id){
        $users = new UserModel();
        $users->setIdAttribute($id);
        
        try{
            $users->updateIsDelete();

        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /** 詳細取得
     *
     * @return list results
     */
    public function getCustomerDetails(Request $request){
        $this->array_messagedata = array();
        $code = "";
        $result = true;
        try {
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => null,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            if (!isset($params['code'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "code", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => null,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $code = $params['code'];
            $user = Auth::user();
            $login_user_code = $user->code;
            $login_account_id = $user->account_id;
            $users = new Customer();
            $users->setCodeAttribute($code);
            $details = $users->getCustomerDetails();
    
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

    public function releaseCardInfo(Request $request){
        $this->array_messagedata = array();
        $code = "";
        $result = true;
        DB::beginTransaction();
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
            if (!isset($params['card_idm'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "card_idm", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $card_idm = $params['card_idm'];
            $systemdate = Carbon::now();
            $user = Auth::user();
            $user_code = $user->code;
            DB::table('card_informations')->where('card_idm', $card_idm)->update(['is_deleted' => 1,'updated_user' => $user_code ,'updated_at' => $systemdate]);
            DB::commit();
        
            return response()->json(
                ['result' => $result,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            DB::rollBack();
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_update_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * アップロード登録
     *
     * @param Request $request
     * @return response
     */
    public function up(Request $request){
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
            if (!isset($params['usersups'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "usersups", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $usersups = $params['usersups'];
            // csvから登録
            $this->insertCsv($usersups);
            $result = true;
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

    /*
     * CSVユーザー登録
     *
     */
    public function insertCsv($usersups){
        DB::beginTransaction();
        try {
            $authuser = Auth::user();
            $login_user_code = $authuser->code;
            $login_account_id = $authuser->account_id;
            $systemdate = Carbon::now();
            $temp_systemdate = $systemdate->copy()->format('Ymd');
            // 部署情報
            $department_model = new Department();
            $department_model->setParamapplytermfromAttribute($temp_systemdate);
            $department_model->setParamAccountidAttribute($login_account_id);
            $departments = $department_model->getDetails();
            // 雇用形態情報
            $generalcode_model = new GeneralCodes();
            $generalcode_model->setParamidentificationidAttribute(Config::get('const.C001.value'));
            $generalcodes = $generalcode_model->getGeneralcode();
            // タイムテーブル情報
            $taimetable_model = new WorkingTimeTable();
            $taimetable_model->setParamapplytermfromAttribute($temp_systemdate);
            $taimetable_model->setParamaccountidAttribute($login_account_id);
            $taimetables = $taimetable_model->getTimeTables();
            // 全部物理削除
            $user_model = new UserModel();
            $user_model->setParamsystemcodeAttribute($login_account_id);
            $user_model->setParamAccountidAttribute($login_account_id);
            $user_model->setAccountidAttribute($login_account_id);
            //$user_model->setParamsystemcodeAttribute('CSD1000L');
            $user_model->delUserData();
            foreach ($usersups as $item) {
                if ($item['user_code'] != null && $item['user_code'] != "" && $item['user_code'] != "CSD1000L") {
                    $user_model->setCodeAttribute($item['user_code']);
                    // 部署名から部署コード設定
                    $department_code = 1;
                    if ($item['user_department_name'] != null && $item['user_department_name'] != "") {
                        $filtered = $departments->where('name', "=", $item['user_department_name']);
                        foreach ($filtered as $d_item) {
                            $department_code = $d_item->code;
                            break;
                        }
                    }
                    $user_model->setDepartmentcodeAttribute($department_code);
                    // 雇用形態名から雇用形態コード設定
                    $employment_status = 1;
                    if ($item['user_employment_name'] != null && $item['user_employment_name'] != "") {
                        $filtered = $generalcodes->where('code_name', "=", $item['user_employment_name']);
                        foreach ($filtered as $g_item) {
                            $employment_status = $g_item->code;
                            break;
                        }
                    }
                    $user_model->setEmploymentstatusAttribute($employment_status);
                    $user_model->setNameAttribute($item['user_name']);
                    $user_model->setShortNameAttribute($item['user_short_name']);
                    $user_model->setKanaAttribute($item['user_kana']);
                    $user_model->setOfficialpositionAttribute($item['user_official_position']);
                    if (isset($item['user_apply_term_from'])) {
                        if ($item['user_apply_term_from'] != null && $item['user_apply_term_from'] != "") {
                            $user_model->setApplytermfromAttribute($item['user_apply_term_from']);
                        } else {
                            $user_model->setApplytermfromAttribute(Config::get('const.INIT_DATE.initdate'));
                        }
                    } else {
                        $user_model->setApplytermfromAttribute(Config::get('const.INIT_DATE.initdate'));
                    }
                    if ($item['user_kill_from_date'] != null && $item['user_kill_from_date'] != "") {
                        $user_model->setKillfromdateAttribute($item['user_kill_from_date']);
                    } else {
                        $user_model->setKillfromdateAttribute(Config::get('const.INIT_DATE.maxdate'));
                    }
                    $user_model->setPasswordAttribute($item['user_code']);
                    // タイムテーブル名からタイムテーブルNO設定
                    $user_working_timetable_no = 1;
                    if ($item['user_working_timetable_name'] != null && $item['user_working_timetable_name'] != "") {
                        $collect_taimetables = new Collection($taimetables);
                        $filtered = $collect_taimetables->where('name', "=", $item['user_working_timetable_name']);
                        foreach ($filtered as $t_item) {
                            $user_working_timetable_no = $t_item->no;
                            break;
                        }
                    }
                    $user_model->setWorkingtimetablenoAttribute($user_working_timetable_no);
                    // メールアドレス設定
                    $user_email = "sample@sample.com";
                    if ($item['user_email'] != null && $item['user_email'] != "") {
                        $user_email = $item['user_email'];
                    }
                    $user_model->setEmailAttribute($user_email);
                    $user_model->setMobileEmailAttribute($item['user_mobile_email']);
                    $user_model->setCreatedatAttribute($systemdate);
                    $user_model->setCreateduserAttribute($login_user_code);
                    // 勤怠管理設定
                    $user_management = 1;
                    if ($item['user_management'] != null && $item['user_management'] != "") {
                        $user_management = $item['user_management'];
                    }
                    $user_model->setManagementAttribute($user_management);
                    // 権限設定
                    $user_role = 1;
                    if ($item['user_role'] != null && $item['user_role'] != "") {
                        $user_role = $item['user_role'];
                    }
                    $user_model->setRoleAttribute($user_role);
                    $pass_word = bcrypt($item['user_code']);
                    $user_model->setPasswordAttribute($pass_word);
                    $user_model->insertNewUser();
    
                }
            }
            DB::commit();
        }catch(\PDOException $pe){
            DB::rollBack();
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
     * 部署雇用形態更新
     *
     * @param [type] $details
     * @return boolean
     */
    private function upDepartmentEmployment($params){
        $isUpdateDepartment = $params['isUpdateDepartment'];
        $isUpdateEmployment = $params['isUpdateEmployment'];
        $login_user_code = $params['login_user_code'];
        $account_id = $params['account_id'];
        $details = $params['details'];
        $before_details = $params['before_details'];
        $systemdate = Carbon::now();
        try{
            $department_code = null;
            $employment_status = null;
            $array_update = array();
            if ($isUpdateDepartment) {
                $department_code = $details['department_code'];
                $array_update = array_merge($array_update, ['department_code' => $department_code]);
            }
            if ($isUpdateEmployment) {
                $employment_status = $details['employment_status'];
                $array_update = array_merge($array_update, ['employment_status' => $employment_status]);
            }
            // update項目があるか
            if (count($array_update) > 0) {
                $array_update = array_merge($array_update, ['updated_user' => $login_user_code]);
                $array_update = array_merge($array_update, ['updated_at' => $systemdate]);
                // 各テーブル部署雇用形態更新
                // upDepartmentEmploymentTable implement
                $array_impl_upDepartmentEmploymentTable = array (
                    'isUpdateDepartment' => $isUpdateDepartment,
                    'isUpdateEmployment' => $isUpdateEmployment,
                    'login_user_code' => $login_user_code,
                    'account_id' => $account_id,
                    'details' => $details,
                    'before_details' => $before_details,
                    'array_update' => $array_update
                );
                $this->upDepartmentEmploymentTable($array_impl_upDepartmentEmploymentTable);
            }
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "users", Config::get('const.LOG_MSG.data_access_error')));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 各テーブル部署雇用形態更新
     *
     * @param [type] $details
     * @return boolean
     */
    private function upDepartmentEmploymentTable($params){
        $isUpdateDepartment = $params['isUpdateDepartment'];
        $isUpdateEmployment = $params['isUpdateEmployment'];
        $login_user_code = $params['login_user_code'];
        $account_id = $params['account_id'];
        $details = $params['details'];
        $before_details = $params['before_details'];
        $array_update = $params['array_update'];
        $applyfrom = new Carbon($details['apply_term_from']);
        $applyfromYmd = $applyfrom->format('Ymd');
        $w_minus1_applyfromYmd = date_format($applyfrom->copy()->subDay(), 'Ymd');
        try{
            // upDETable implement
            $array_impl_upDETable = array (
                'login_user_code' => $login_user_code,
                'account_id' => $account_id,
                'details' => $details,
                'array_update' => $array_update
            );
            // attendance_logsの更新
            $this->upDEAttendancelogsTable($array_impl_upDETable);
            // calendar_setting_informationsの更新
            $this->upDECalendarsettinginformationsTable($array_impl_upDETable);
            // card_informationsの更新
            $this->upDECardinformationsTable($array_impl_upDETable);
            // work_time_logsの更新
            $this->upDEWorktimelogsTable($array_impl_upDETable);
            // work_timeの更新
            $this->upDEWorktimeTable($array_impl_upDETable);
            // working_time_datesの更新
            $this->upDEWorkingtimedatesTable($array_impl_upDETable);

            // 修正時：修正前の適用日付＜修正後の適用日付の場合、
            //        修正前の適用日付から修正後の適用日付－１日までを以前の状態に戻す
            if (count($before_details) > 0) {
                $before_applyfrom = new Carbon($before_details['apply_term_from']);
                $before_applyfromYmd = $before_applyfrom->format('Ymd');
                if ($before_applyfromYmd < $applyfromYmd) {
                    // 以前の状態に戻す
                    // upDepartmentEmploymentTableBefore implement
                    $array_impl_upDepartmentEmploymentTableBefore = array (
                        'isUpdateDepartment' => $isUpdateDepartment,
                        'isUpdateEmployment' => $isUpdateEmployment,
                        'isDelete' => false,
                        'login_user_code' => $login_user_code,
                        'account_id' => $account_id,
                        'details' => $details
                    );
                    $this->upDepartmentEmploymentTableBefore($array_impl_upDepartmentEmploymentTableBefore);
                }
            }
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "users", Config::get('const.LOG_MSG.data_access_error')));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * attendance_logsテーブル部署雇用形態更新
     *
     * @param [type] $details
     * @return boolean
     */
    private function upDEAttendancelogsTable($params){
        $login_user_code = $params['login_user_code'];
        $account_id = $params['account_id'];
        $details = $params['details'];
        $array_update = $params['array_update'];
        $applyfrom = new Carbon($details['apply_term_from']);
        $applyfromYmd = $applyfrom->format('Ymd');
        try{
            // attendance_logsの更新
            $attendancelog_model = new AttendanceLog();
            $attendancelog_model->setParamAccountidAttribute($account_id);
            $attendancelog_model->setParamusercodeAttribute($details['code']);
            $attendancelog_model->setParamworkingdatefromAttribute($applyfromYmd);
            $res = $attendancelog_model->isExist();
            if ($res) {
                $attendancelog_model->updateCommon($array_update);
            }
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "attendance_logs", Config::get('const.LOG_MSG.data_access_error')));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * calendar_setting_informationsテーブル部署雇用形態更新
     *
     * @param [type] $details
     * @return boolean
     */
    private function upDECalendarsettinginformationsTable($params){
        $login_user_code = $params['login_user_code'];
        $account_id = $params['account_id'];
        $details = $params['details'];
        $array_update = $params['array_update'];
        $applyfrom = new Carbon($details['apply_term_from']);
        $applyfromYmd = $applyfrom->format('Ymd');
        try{
            // acalendar_setting_informationsの更新
            $calendarsetting_model = new CalendarSettingInformation();
            $calendarsetting_model->setParamAccountidAttribute($account_id);
            $calendarsetting_model->setParamusercodeAttribute($details['code']);
            $calendarsetting_model->setParamfromdateAttribute($applyfromYmd);
            $res = $calendarsetting_model->isExistsCalendarSettingDate();
            if ($res) {
                $calendarsetting_model->updateCalecdarSettingCommon($array_update);
            }
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "calendar_setting_informations", Config::get('const.LOG_MSG.data_access_error')));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * card_informationsテーブル部署雇用形態更新
     *
     * @param [type] $details
     * @return boolean
     */
    private function upDECardinformationsTable($params){
        $login_user_code = $params['login_user_code'];
        $account_id = $params['account_id'];
        $details = $params['details'];
        $array_update = $params['array_update'];
        // 雇用形態削除
        unset($array_update['employment_status']);
        $applyfrom = new Carbon($details['apply_term_from']);
        $applyfromYmd = $applyfrom->format('Ymd');
        try{
            // card_informationsの更新
            $card_model = new CardInformation();
            $card_model->setParamAccountidAttribute($account_id);
            $card_model->setParamusercodeAttribute($details['code']);
            $res = $card_model->isCardExists();
            if ($res) {
                $card_model->updateCardCommon($array_update);
            }
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "card_informations", Config::get('const.LOG_MSG.data_access_error')));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * work_time_logsテーブル部署雇用形態更新
     *
     * @param [type] $details
     * @return boolean
     */
    private function upDEWorktimelogsTable($params){
        $login_user_code = $params['login_user_code'];
        $account_id = $params['account_id'];
        $details = $params['details'];
        $array_update = $params['array_update'];
        $applyfrom = new Carbon($details['apply_term_from']);
        $applyfromYmd = $applyfrom->format('Ymd');
        $w_plus1_applyfromYmd = date_format($applyfrom->copy()->addDay(), 'Ymd');
        try{
            // work_time_logsのapplyfrom以降の出勤データを取得し開始時刻にする
            $worktimelog_model = new WorkTimeLog();
            $worktimelog_model->setParamAccountidAttribute($account_id);
            $worktimelog_model->setParamusercodeAttribute($details['code']);
            $worktimelog_model->setParamrecordtimefromAttribute($applyfrom);
            $worktimelog_model->setParammodeAttribute(Config::get('const.C005.attendance_time'));
            $result_record_time1 = null;
            $results = $worktimelog_model->getWorkTimelogDetails();
            foreach($results as $item) {
                if (isset($item->record_time)) {
                    $result_record_time1 = $item->record_time;
                    break;
                }
            }
            // work_time_logsのapplyfrom以降の出勤データがない場合も想定し翌日以降の打刻を取得し比較して開始時刻にする
            $worktimelog_model->setParamusercodeAttribute($details['code']);
            $worktimelog_model->setParamrecordtimefromAttribute($w_plus1_applyfromYmd);
            $worktimelog_model->setParammodeAttribute(null);
            $result_record_time2 = null;
            $results = $worktimelog_model->getWorkTimelogDetails();
            foreach($results as $item) {
                if (isset($item->record_time)) {
                    $result_record_time2 = $item->record_time;
                    break;
                }
            }
            $from_record_time = null;
            if ($result_record_time1 != null && $result_record_time1 != "") {
                if ($result_record_time2 != null && $result_record_time2 != "") {
                    if ($result_record_time1 < $result_record_time2) {
                        $from_record_time = $result_record_time1;
                    } else {
                        $from_record_time = $result_record_time2;
                    }
                } else {
                    $from_record_time = $result_record_time1;
                }
            } else {
                if ($result_record_time2 != null && $result_record_time2 != "") {
                    $from_record_time = $result_record_time2;
                }
            }
            // データが存在した場合
            if ($from_record_time != null) {
                // work_time_logsの更新
                $worktimelog_model->setParamusercodeAttribute($details['code']);
                $worktimelog_model->setParamrecordtimefromnoneditAttribute($from_record_time);
                $worktimelog_model->setParammodeAttribute(null);
                $worktimelog_model->updateWorkTimelogCommon($array_update);
            }
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "work_time_logs", Config::get('const.LOG_MSG.data_access_error')));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * work_timeテーブル部署雇用形態更新
     *
     * @param [type] $details
     * @return boolean
     */
    private function upDEWorktimeTable($params){
        $login_user_code = $params['login_user_code'];
        $account_id = $params['account_id'];
        $details = $params['details'];
        $array_update = $params['array_update'];
        // 雇用形態削除
        unset($array_update['employment_status']);
        $applyfrom = new Carbon($details['apply_term_from']);
        $applyfromYmd = $applyfrom->format('Ymd');
        $w_plus1_applyfromYmd = date_format($applyfrom->copy()->addDay(), 'Ymd');
        try{
            // work_timeのapplyfrom以降の出勤データを取得し開始時刻にする
            $worktime_model = new WorkTime();
            $worktime_model->setParamAccountidAttribute($account_id);
            $worktime_model->setParamModeAttribute(Config::get('const.C005.attendance_time'));
            $worktime_model->setParamUsercodeAttribute($details['code']);
            $worktime_model->setParamdatefromAttribute($applyfrom);
            $result_record_time1 = null;
            $results = $worktime_model->getWorkTimeDetails();
            foreach($results as $item) {
                if (isset($item->record_time)) {
                    $result_record_time1 = $item->record_time;
                    break;
                }
            }
            // work_timeのapplyfrom以降の出勤データがない場合も想定し翌日以降の打刻を取得し比較して開始時刻にする
            $worktime_model->setParamUsercodeAttribute($details['code']);
            $worktime_model->setParamdatefromAttribute($w_plus1_applyfromYmd);
            $worktime_model->setParamModeAttribute(null);
            $result_record_time2 = null;
            $results = $worktime_model->getWorkTimeDetails();
            foreach($results as $item) {
                if (isset($item->record_time)) {
                    $result_record_time2 = $item->record_time;
                    break;
                }
            }
            $from_record_time = null;
            if ($result_record_time1 != null && $result_record_time1 != "") {
                if ($result_record_time2 != null && $result_record_time2 != "") {
                    if ($result_record_time1 < $result_record_time2) {
                        $from_record_time = $result_record_time1;
                    } else {
                        $from_record_time = $result_record_time2;
                    }
                } else {
                    $from_record_time = $result_record_time1;
                }
            } else {
                if ($result_record_time2 != null && $result_record_time2 != "") {
                    $from_record_time = $result_record_time2;
                }
            }
            // データが存在した場合
            if ($from_record_time != null) {
                // work_timeの更新
                $worktime_model->setParamUsercodeAttribute($details['code']);
                $worktime_model->setParamdatefromNoneditAttribute($from_record_time);
                $worktime_model->setParamModeAttribute(null);
                $worktime_model->updateWorkTimeCommon($array_update);
            }
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "work_time", Config::get('const.LOG_MSG.data_access_error')));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * working_time_datesテーブル部署雇用形態更新
     *
     * @param [type] $details
     * @return boolean
     */
    private function upDEWorkingtimedatesTable($params){
        $login_user_code = $params['login_user_code'];
        $account_id = $params['account_id'];
        $details = $params['details'];
        $array_update = $params['array_update'];
        $applyfrom = new Carbon($details['apply_term_from']);
        $applyfromYmd = $applyfrom->format('Ymd');
        try{
            // working_time_datesの更新
            $workingtimedate_model = new WorkingTimedate();
            $workingtimedate_model->setParamAccountidAttribute($account_id);
            $workingtimedate_model->setParamusercodeAttribute($details['code']);
            $workingtimedate_model->setParamdatefromAttribute($applyfromYmd);
            $res = $workingtimedate_model->isExistsWorkingTimeDate();
            if ($res) {
                $workingtimedate_model->updateWorkingTimeDateCommon($array_update);
            }
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "working_time_dates", Config::get('const.LOG_MSG.data_access_error')));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 各テーブル部署雇用形態戻し更新
     *
     * @param [type] $details
     * @return boolean
     */
    private function upDepartmentEmploymentTableBefore($params){
        $isUpdateDepartment = $params['isUpdateDepartment'];
        $isUpdateEmployment = $params['isUpdateEmployment'];
        $isDelete = $params['isDelete'];
        $login_user_code = $params['login_user_code'];
        $account_id = $params['account_id'];
        $details = $params['details'];
        $applyfrom = new Carbon($details['apply_term_from']);
        $applyfromYmd = $applyfrom->format('Ymd');
        $w_minus1_applyfromYmd = date_format($applyfrom->copy()->subDay(), 'Ymd');
        $systemdate = Carbon::now();
        try{
            // users情報を取得（更新削除されたあとの状態となる）
            $users = new UserModel();
            $users->setParamAccountidAttribute($account_id);
            $users->setCodeAttribute($details['code']);
            $users->setKillvalueAttribute(false);
            $users_details = $users->getUserDetails();
            foreach ($users_details as $item) {
                if (isset($item->apply_term_from)) {
                    $item_applyfrom = new Carbon($item->apply_term_from);
                    $item_applyfromYmd = $item_applyfrom->format('Ymd');
                    if ($item_applyfromYmd < $applyfromYmd) {
                        $w_plus1_applyfromYmd = date_format($item_applyfrom->copy()->addDay(), 'Ymd');
                        // 修正前の適用日付から修正後の適用日付－１日まで更新
                        // ただし削除時は以前の適用日付以降を更新
                        // upDETableBefore implement
                        if ($item_applyfromYmd > $w_minus1_applyfromYmd) {
                            $w_minus1_applyfromYmd = $item_applyfromYmd;
                        }
                        $array_impl_upDETableBefore = array (
                            'user_code' => $details['code'],
                            'item_applyfromYmd' => $item_applyfromYmd,
                            'w_minus1_applyfromYmd' => $w_minus1_applyfromYmd,
                            'w_plus1_applyfromYmd' => $w_plus1_applyfromYmd,
                            'item_department_code' => $item->department_code,
                            'item_employment_status' => $item->employment_status,
                            'isDelete' => $isDelete,
                            'isUpdateDepartment' => $isUpdateDepartment,
                            'isUpdateEmployment' => $isUpdateEmployment,
                            'login_user_code' => $login_user_code,
                            'account_id' => $account_id,
                            'systemdate' => $systemdate
                        );
                        // attendance_logsの更新
                        $this->upDEAttendancelogsTableBefore($array_impl_upDETableBefore);
                        // calendar_setting_informationsの更新
                        $this->upDECalendarsettinginformationsTableBefore($array_impl_upDETableBefore);
                        // card_informationsの更新
                        $this->upDECardinformationsTableBefore($array_impl_upDETableBefore);
                        // work_time_logsの更新
                        $this->upDEWorktimelogsTableBefore($array_impl_upDETableBefore);
                        // work_timeの更新
                        $this->upDEWorktimeTableBefore($array_impl_upDETableBefore);
                        // working_time_datesの更新
                        $this->upDEWorkingtimedatesTableBefore($array_impl_upDETableBefore);
                        break;
                    }
                }
            }
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "users shift_informations", Config::get('const.LOG_MSG.data_access_error')));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * attendance_logsテーブル部署雇用形態戻し更新
     *
     * @param [type] $details
     * @return boolean
     */
    private function upDEAttendancelogsTableBefore($params){
        $user_code = $params['user_code'];
        $item_applyfromYmd = $params['item_applyfromYmd'];
        $w_minus1_applyfromYmd = $params['w_minus1_applyfromYmd'];
        $w_plus1_applyfromYmd = $params['w_plus1_applyfromYmd'];
        $item_department_code = $params['item_department_code'];
        $item_employment_status = $params['item_employment_status'];
        $isDelete = $params['isDelete'];
        $isUpdateDepartment = $params['isUpdateDepartment'];
        $isUpdateEmployment = $params['isUpdateEmployment'];
        $login_user_code = $params['login_user_code'];
        $account_id = $params['account_id'];
        $systemdate = $params['systemdate'];
        try{
            // 修正前の適用日付から修正後の適用日付－１日まで更新
            // ただし削除時は以前の適用日付以降を更新
            // attendance_logsの更新
            $attendancelog_model = new AttendanceLog();
            $attendancelog_model->setParamAccountidAttribute($account_id);
            $attendancelog_model->setParamusercodeAttribute($user_code);
            $attendancelog_model->setParamworkingdatefromAttribute($item_applyfromYmd);
            if (!$isDelete) {
                $attendancelog_model->setParamworkingdatetoAttribute($w_minus1_applyfromYmd);
            }
            $res = $attendancelog_model->isExist();
            if ($res) {
                $array_before_update = array();
                if ($isUpdateDepartment) {
                    $array_before_update = array_merge($array_before_update, ['department_code' => $item_department_code]);
                }
                if ($isUpdateEmployment) {
                    $array_before_update = array_merge($array_before_update, ['employment_status' => $item_employment_status]);
                }
                // update項目があるか
                if (count($array_before_update) > 0) {
                    $array_before_update = array_merge($array_before_update, ['updated_user' => $login_user_code]);
                    $array_before_update = array_merge($array_before_update, ['updated_at' => $systemdate]);
                    $attendancelog_model->updateCommon($array_before_update);
                }
            }
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "attendance_logs", Config::get('const.LOG_MSG.data_access_error')));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * calendar_setting_informationsテーブル部署雇用形態戻し更新
     *
     * @param [type] $details
     * @return boolean
     */
    private function upDECalendarsettinginformationsTableBefore($params){
        $user_code = $params['user_code'];
        $item_applyfromYmd = $params['item_applyfromYmd'];
        $w_minus1_applyfromYmd = $params['w_minus1_applyfromYmd'];
        $w_plus1_applyfromYmd = $params['w_plus1_applyfromYmd'];
        $item_department_code = $params['item_department_code'];
        $item_employment_status = $params['item_employment_status'];
        $isDelete = $params['isDelete'];
        $isUpdateDepartment = $params['isUpdateDepartment'];
        $isUpdateEmployment = $params['isUpdateEmployment'];
        $login_user_code = $params['login_user_code'];
        $account_id = $params['account_id'];
        $systemdate = $params['systemdate'];
        try{
            // 修正前の適用日付から修正後の適用日付－１日まで更新
            // ただし削除時は以前の適用日付以降を更新
            // calendar_setting_informationsの更新
            $calendarsetting_model = new CalendarSettingInformation();
            $calendarsetting_model->setParamAccountidAttribute($account_id);
            $calendarsetting_model->setParamusercodeAttribute($user_code);
            $calendarsetting_model->setParamfromdateAttribute($item_applyfromYmd);
            if (!$isDelete) {
                $calendarsetting_model->setParamtodateAttribute($w_minus1_applyfromYmd);
            }
            $res = $calendarsetting_model->isExistsCalendarSettingDate();
            if ($res) {
                $array_before_update = array();
                if ($isUpdateDepartment) {
                    $array_before_update = array_merge($array_before_update, ['department_code' => $item_department_code]);
                }
                if ($isUpdateEmployment) {
                    $array_before_update = array_merge($array_before_update, ['employment_status' => $item_employment_status]);
                }
                // update項目があるか
                if (count($array_before_update) > 0) {
                    $array_before_update = array_merge($array_before_update, ['updated_user' => $login_user_code]);
                    $array_before_update = array_merge($array_before_update, ['updated_at' => $systemdate]);
                    $calendarsetting_model->updateCalecdarSettingCommon($array_before_update);
                }
            }
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "calendar_setting_informations", Config::get('const.LOG_MSG.data_access_error')));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * card_informationsテーブル部署雇用形態戻し更新
     *
     * @param [type] $details
     * @return boolean
     */
    private function upDECardinformationsTableBefore($params){
        $user_code = $params['user_code'];
        $item_applyfromYmd = $params['item_applyfromYmd'];
        $w_minus1_applyfromYmd = $params['w_minus1_applyfromYmd'];
        $w_plus1_applyfromYmd = $params['w_plus1_applyfromYmd'];
        $item_department_code = $params['item_department_code'];
        $item_employment_status = $params['item_employment_status'];
        $isDelete = $params['isDelete'];
        $isUpdateDepartment = $params['isUpdateDepartment'];
        $isUpdateEmployment = $params['isUpdateEmployment'];
        $login_user_code = $params['login_user_code'];
        $account_id = $params['account_id'];
        $systemdate = $params['systemdate'];
        try{
            // card_informationsの更新
            $card_model = new CardInformation();
            $card_model->setParamAccountidAttribute($account_id);
            $card_model->setParamusercodeAttribute($user_code);
            $res = $card_model->isCardExists();
            if ($res) {
                $array_before_update = array();
                if ($isUpdateDepartment) {
                    $array_before_update = array_merge($array_before_update, ['department_code' => $item_department_code]);
                }
                // update項目があるか
                if (count($array_before_update) > 0) {
                    $array_before_update = array_merge($array_before_update, ['updated_user' => $login_user_code]);
                    $array_before_update = array_merge($array_before_update, ['updated_at' => $systemdate]);
                    $card_model->updateCardCommon($array_before_update);
                }
            }
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "card_informations", Config::get('const.LOG_MSG.data_access_error')));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * work_time_logsテーブル部署雇用形態戻し更新
     *
     * @param [type] $details
     * @return boolean
     */
    private function upDEWorktimelogsTableBefore($params){
        $user_code = $params['user_code'];
        $item_applyfromYmd = $params['item_applyfromYmd'];
        $w_minus1_applyfromYmd = $params['w_minus1_applyfromYmd'];  // 修正後の適用日付－１日
        $w_plus1_applyfromYmd = $params['w_plus1_applyfromYmd'];
        $item_department_code = $params['item_department_code'];
        $item_employment_status = $params['item_employment_status'];
        $isDelete = $params['isDelete'];
        $isUpdateDepartment = $params['isUpdateDepartment'];
        $isUpdateEmployment = $params['isUpdateEmployment'];
        $login_user_code = $params['login_user_code'];
        $account_id = $params['account_id'];
        $systemdate = $params['systemdate'];
        try{
            // 修正前の適用日付から修正後の適用日付－１日まで更新
            // ただし削除時は以前の適用日付以降を更新
            // work_time_logsの更新
            $worktimelog_model = new WorkTimeLog();
            $worktimelog_model->setParamAccountidAttribute($account_id);
            $worktimelog_model->setParamusercodeAttribute($user_code);
            $worktimelog_model->setParamrecordtimefromAttribute($item_applyfromYmd);
            if (!$isDelete) {
                $worktimelog_model->setParamrecordtimetoAttribute($w_minus1_applyfromYmd);
            }
            $worktimelog_model->setParammodeAttribute(Config::get('const.C005.attendance_time'));
            $result_record_time1 = null;
            $results = $worktimelog_model->getWorkTimelogDetails();
            foreach($results as $item) {
                if (isset($item->record_time)) {
                    $result_record_time1 = $item->record_time;
                    break;
                }
            }
            // work_time_logsのapplyfrom以降の出勤データがない場合も想定し翌日以降の打刻を取得し比較して開始時刻にする
            $result_record_time2 = null;
            if ($w_plus1_applyfromYmd < $w_minus1_applyfromYmd) {
                $worktimelog_model->setParamusercodeAttribute($user_code);
                $worktimelog_model->setParamrecordtimefromAttribute($w_plus1_applyfromYmd);
                if (!$isDelete) {
                    $worktimelog_model->setParamrecordtimetoAttribute($w_minus1_applyfromYmd);
                }
                $worktimelog_model->setParammodeAttribute(null);
                $results = $worktimelog_model->getWorkTimelogDetails();
                foreach($results as $item) {
                    if (isset($item->record_time)) {
                        $result_record_time2 = $item->record_time;
                        break;
                    }
                }
            }
            $from_record_time = null;
            if ($result_record_time1 != null && $result_record_time1 != "") {
                if ($result_record_time2 != null && $result_record_time2 != "") {
                    if ($result_record_time1 < $result_record_time2) {
                        $from_record_time = $result_record_time1;
                    } else {
                        $from_record_time = $result_record_time2;
                    }
                } else {
                    $from_record_time = $result_record_time1;
                }
            } else {
                if ($result_record_time2 != null && $result_record_time2 != "") {
                    $from_record_time = $result_record_time2;
                }
            }
            // データが存在した場合
            if ($from_record_time != null) {
                $array_before_update = array();
                if ($isUpdateDepartment) {
                    $array_before_update = array_merge($array_before_update, ['department_code' => $item_department_code]);
                }
                if ($isUpdateEmployment) {
                    $array_before_update = array_merge($array_before_update, ['employment_status' => $item_employment_status]);
                }
                // update項目があるか
                if (count($array_before_update) > 0) {
                    $array_before_update = array_merge($array_before_update, ['updated_user' => $login_user_code]);
                    $array_before_update = array_merge($array_before_update, ['updated_at' => $systemdate]);
                    // work_time_logsの更新
                    $worktimelog_model->setParamusercodeAttribute($user_code);
                    $worktimelog_model->setParamrecordtimefromnoneditAttribute($from_record_time);
                    if (!$isDelete) {
                        $worktimelog_model->setParamrecordtimetoAttribute($w_minus1_applyfromYmd);
                    }
                    $worktimelog_model->setParammodeAttribute(null);
                    $worktimelog_model->updateWorkTimelogCommon($array_before_update);
                }
            }
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "work_time_logs", Config::get('const.LOG_MSG.data_access_error')));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * work_timeテーブル部署雇用形態戻し更新
     *
     * @param [type] $details
     * @return boolean
     */
    private function upDEWorktimeTableBefore($params){
        $user_code = $params['user_code'];
        $item_applyfromYmd = $params['item_applyfromYmd'];
        $w_minus1_applyfromYmd = $params['w_minus1_applyfromYmd'];  // 修正後の適用日付－１日
        $w_plus1_applyfromYmd = $params['w_plus1_applyfromYmd'];
        $item_department_code = $params['item_department_code'];
        $item_employment_status = $params['item_employment_status'];
        $isDelete = $params['isDelete'];
        $isUpdateDepartment = $params['isUpdateDepartment'];
        $isUpdateEmployment = $params['isUpdateEmployment'];
        $login_user_code = $params['login_user_code'];
        $account_id = $params['account_id'];
        $systemdate = $params['systemdate'];
        try{
            // 修正前の適用日付から修正後の適用日付－１日まで更新
            // ただし削除時は以前の適用日付以降を更新
            // work_timeの更新
            $worktime_model = new WorkTime();
            $worktime_model->setParamAccountidAttribute($account_id);
            $worktime_model->setParamUsercodeAttribute($user_code);
            $worktime_model->setParamdatefromAttribute($item_applyfromYmd);
            if (!$isDelete) {
                $worktime_model->setParamdatetoAttribute($w_minus1_applyfromYmd);
            }
            $worktime_model->setParamModeAttribute(Config::get('const.C005.attendance_time'));
            $result_record_time1 = null;
            $results = $worktime_model->getWorkTimeDetails();
            foreach($results as $item) {
                if (isset($item->record_time)) {
                    $result_record_time1 = $item->record_time;
                    break;
                }
            }
            // work_time_logsのapplyfrom以降の出勤データがない場合も想定し翌日以降の打刻を取得し比較して開始時刻にする
            $result_record_time2 = null;
            if ($w_plus1_applyfromYmd < $w_minus1_applyfromYmd) {
                $worktime_model->setParamUsercodeAttribute($user_code);
                $worktime_model->setParamdatefromAttribute($w_plus1_applyfromYmd);
                if (!$isDelete) {
                    $worktime_model->setParamdatetoAttribute($w_minus1_applyfromYmd);
                }
                $worktime_model->setParamModeAttribute(null);
                $results = $worktime_model->getWorkTimeDetails();
                foreach($results as $item) {
                    if (isset($item->record_time)) {
                        $result_record_time2 = $item->record_time;
                        break;
                    }
                }
            }
            $from_record_time = null;
            if ($result_record_time1 != null && $result_record_time1 != "") {
                if ($result_record_time2 != null && $result_record_time2 != "") {
                    if ($result_record_time1 < $result_record_time2) {
                        $from_record_time = $result_record_time1;
                    } else {
                        $from_record_time = $result_record_time2;
                    }
                } else {
                    $from_record_time = $result_record_time1;
                }
            } else {
                if ($result_record_time2 != null && $result_record_time2 != "") {
                    $from_record_time = $result_record_time2;
                }
            }
            // データが存在した場合
            if ($from_record_time != null) {
                $array_before_update = array();
                if ($isUpdateDepartment) {
                    $array_before_update = array_merge($array_before_update, ['department_code' => $item_department_code]);
                }
                // update項目があるか
                if (count($array_before_update) > 0) {
                    $array_before_update = array_merge($array_before_update, ['updated_user' => $login_user_code]);
                    $array_before_update = array_merge($array_before_update, ['updated_at' => $systemdate]);
                    // work_time_logsの更新
                    $worktime_model->setParamUsercodeAttribute($user_code);
                    $worktime_model->setParamdatefromNoneditAttribute($from_record_time);
                    if (!$isDelete) {
                        $worktime_model->setParamdatetoAttribute($w_minus1_applyfromYmd);
                    }
                    $worktime_model->setParamModeAttribute(null);
                    $worktime_model->updateWorkTimeCommon($array_before_update);
                }

            }
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "work_time", Config::get('const.LOG_MSG.data_access_error')));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * working_time_datesテーブル部署雇用形態戻し更新
     *
     * @param [type] $details
     * @return boolean
     */
    private function upDEWorkingtimedatesTableBefore($params){
        $user_code = $params['user_code'];
        $item_applyfromYmd = $params['item_applyfromYmd'];
        $w_minus1_applyfromYmd = $params['w_minus1_applyfromYmd'];
        $w_plus1_applyfromYmd = $params['w_plus1_applyfromYmd'];
        $item_department_code = $params['item_department_code'];
        $item_employment_status = $params['item_employment_status'];
        $isDelete = $params['isDelete'];
        $isUpdateDepartment = $params['isUpdateDepartment'];
        $isUpdateEmployment = $params['isUpdateEmployment'];
        $login_user_code = $params['login_user_code'];
        $account_id = $params['account_id'];
        $systemdate = $params['systemdate'];
        try{
            // 修正前の適用日付から修正後の適用日付－１日まで更新
            // ただし削除時は以前の適用日付以降を更新
            // working_time_datesの更新
            $workingtimedate_model = new WorkingTimedate();
            $workingtimedate_model->setParamAccountidAttribute($account_id);
            $workingtimedate_model->setParamusercodeAttribute($user_code);
            $workingtimedate_model->setParamdatefromAttribute($item_applyfromYmd);
            if (!$isDelete) {
                $workingtimedate_model->setParamdatetoAttribute($w_minus1_applyfromYmd);
            }
            $res = $workingtimedate_model->isExistsWorkingTimeDate();
            if ($res) {
                $array_before_update = array();
                if ($isUpdateDepartment) {
                    $array_before_update = array_merge($array_before_update, ['department_code' => $item_department_code]);
                }
                if ($isUpdateEmployment) {
                    $array_before_update = array_merge($array_before_update, ['employment_status' => $item_employment_status]);
                }
                // update項目があるか
                if (count($array_before_update) > 0) {
                    $array_before_update = array_merge($array_before_update, ['updated_user' => $login_user_code]);
                    $array_before_update = array_merge($array_before_update, ['updated_at' => $systemdate]);
                    $workingtimedate_model->updateWorkingTimeDateCommon($array_before_update);
                }
            }
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "working_time_dates", Config::get('const.LOG_MSG.data_access_error')));
            Log::error($e->getMessage());
            throw $e;
        }
    }
}
