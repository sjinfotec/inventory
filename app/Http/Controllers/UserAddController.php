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
}
