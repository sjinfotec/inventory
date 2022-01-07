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
//use App\Http\Controllers\SttingShiftTimeController;

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
            $users = new Customer();
            $systemdate = Carbon::now();
            $authuser = Auth::user();
            $user_code = $authuser->code;
            $applyfrom = new Carbon($data['apply_term_from']);
            $users->setCodeAttribute($target_user_code);
            $users->setNameAttribute($data['name']);
            $users->setCreatedatAttribute($systemdate);
            $users->setCreateduserAttribute($user_code);
            // insert
            $users->insertNewUser();
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
        $office_code = "";
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
            Log::debug('CustomerAddController params_code = '.$params['code']);
            Log::debug('CustomerAddController params_office_code = '.$params['office_code']);
            $code = $params['code'];
            $office_code = $params['office_code'];
            $user = Auth::user();
            $login_user_code = $user->code;
            $login_account_id = $user->account_id;
            $customers = new Customer();
            $customers->setCodeAttribute($code);
            $customers->setOfficecodeAttribute($office_code);
            $details = $customers->getCustomerDetails();

            //return response()->json('hello');
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



     public function getCustomerDetails_bak(Request $request){
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
            $customers = new Customer();
            $customers->setCodeAttribute($code);
            $details = $customers->getCustomerDetails();
    
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












}
