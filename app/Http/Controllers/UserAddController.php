<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\StoreUserPost;
use Illuminate\Support\Facades\Auth;
use App\UserModel;
use App\Department;
use App\WorkingTimeTable;
use App\GeneralCodes;
use App\Calendar;
use Carbon\Carbon;

class UserAddController extends Controller
{
    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        //return view('user_add');
        return view('edit_user');
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
            if ($details['code'] != "") {
                $user_model = new UserModel();
                $user_model->setParamcodeAttribute($details['code']);
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
            $result = $this->insert($details);
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
    private function insert($data){
        DB::beginTransaction();
        try{
            $users = new UserModel();
            $systemdate = Carbon::now();
            $user = Auth::user();
            $user_code = $user->code;
            $users->setApplytermfromAttribute(Config::get('const.INIT_DATE.initdate'));
            $users->setCodeAttribute($data['code']);
            $users->setDepartmentcodeAttribute($data['department_code']);
            $users->setEmploymentstatusAttribute($data['employment_status']);
            $users->setNameAttribute($data['name']);
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
            // $calendar_model = new Calendar();
            // $calendar_model->setUsercodeAttribute($data['code']);
            // $calendar_model->setCreateduserAttribute($user_code);
            // $calendar_model->setCreatedatAttribute($systemdate);
            // $result = $calendar_model->storeByUser();
            $result = true;
            if ($result) {
                DB::commit();
            } else {
                DB::rollBack();
            }
            return $result;
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
            $details = $params['details'];
            $this->update($details);
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
    private function update($data){
        $systemdate = Carbon::now();
        $user = Auth::user();
        $user_code = $user->code;
        DB::beginTransaction();
        try{
            $user_model = new UserModel();
            $carbon = new Carbon($data['apply_term_from']);
            $temp_from = $carbon->copy()->format('Ymd');
            $apply_term_from = $temp_from;
            $user_model->setApplytermfromAttribute($apply_term_from);
            $user_model->setCodeAttribute($data['code']);
            $user_model->setDepartmentcodeAttribute($data['department_code']);
            $user_model->setEmploymentstatusAttribute($data['employment_status']);
            $user_model->setNameAttribute($data['name']);
            $user_model->setKanaAttribute($data['kana']);
            $user_model->setOfficialpositionAttribute($data['official_position']);
            $temp_from = Config::get('const.INIT_DATE.maxdate');
            if ($data['kill_from_date'] != "" && $data['kill_from_date'] != null) {
                $carbon = new Carbon($data['kill_from_date']);
                $temp_from = $carbon->copy()->format('Ymd');
            }
            $kill_from_date = $temp_from;
            $user_model->setKillfromdateAttribute($kill_from_date);
            $user_model->setWorkingtimetablenoAttribute($data['working_timetable_no']);
            $user_model->setEmailAttribute($data['email']);
            $user_model->setMobileEmailAttribute($data['mobile_email']);
            $user_model->setCreatedatAttribute($systemdate);
            $user_model->setCreateduserAttribute($user_code);
            $user_model->setManagementAttribute($data['management']);
            $user_model->setRoleAttribute($data['role']);
            if ($data['id'] == "" || $data['id'] == null) {
                $user_model->setCreateduserAttribute($user_code);
                $user_model->setCreatedatAttribute($systemdate);
                Log::debug('update password = '.$data['code']);
                $user_model->setPasswordAttribute(bcrypt($data['code']));
                $user_model->insertNewUser();
            } else {
                $user_model->setIdAttribute($data['id']);   
                $user_model->setUpdateduserAttribute($user_code);
                $user_model->setUpdatedatAttribute($systemdate);
                $user_model->updateUser();
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
            if (!isset($params['id'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "id", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $id = $params['id'];
            $this->updateIsDelete($id);
        
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
     * 論理削除
     *
     * @param [type] $code
     * @return void
     */
    public function updateIsDelete($id){
        $users = new UserModel();
        $users->setIdAttribute($id);
        
        DB::beginTransaction();
        try{
            $users->updateIsDelete();
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

    /** 詳細取得
     *
     * @return list results
     */
    public function getUserDetails(Request $request){
        $this->array_messagedata = array();
        $code = "";
        $killvalue = false;
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
            if (isset($params['killvalue'])) {
                $killvalue = $params['killvalue'];
            }
            $users = new UserModel();
            $users->setCodeAttribute($code);
            $users->setKillvalueAttribute($killvalue);
            $details = $users->getUserDetails();
    
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
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_update_erorr')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_update_erorr')).'$e');
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
            $login_user_code = Auth::user()->code;
            $systemdate = Carbon::now();
            $temp_systemdate = $systemdate->copy()->format('Ymd');
            // 部署情報
            $department_model = new Department();
            $department_model->setParamapplytermfromAttribute($temp_systemdate);
            $departments = $department_model->getDetails();
            // 雇用形態情報
            $generalcode_model = new GeneralCodes();
            $generalcode_model->setParamidentificationidAttribute(Config::get('const.C001.value'));
            $generalcodes = $generalcode_model->getGeneralcode();
            // タイムテーブル情報
            $taimetable_model = new WorkingTimeTable();
            $taimetable_model->setParamapplytermfromAttribute($temp_systemdate);
            $taimetables = $taimetable_model->getTimeTables();
            // 全部物理削除
            $user_model = new UserModel();
            $user_model->setParamsystemcodeAttribute(Config::get('const.ACCOUNTID.account_id'));
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
                        $filtered = $taimetables->where('name', "=", $item['user_working_timetable_name']);
                        foreach ($filtered as $g_item) {
                            $user_working_timetable_no = $g_item->no;
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
}
