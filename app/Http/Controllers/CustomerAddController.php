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
                    ['result' => false, 'code' => $code,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            Log::debug('CustomerAddController store insert before check name = '.$params['name']);
            if (!isset($params['name'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "name", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'name' => $name,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            Log::debug('CustomerAddController store insert before check = '.$params['office_code']);
            if (!isset($params['office_code'])) {
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.already_data');
                $result = false;
                return response()->json(
                    ['result' => $result, 'office_code' => $office_code,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }

            $name = $params['name'];
            $office_code = $params['office_code'];

            // 名チェック
            if ($name != "") {
                $customer_model = new Customer();
                $customer_model->setNameAttribute($name);
                $isExists = $customer_model->isExistsName();
                if ($isExists) {
                    $this->array_messagedata[] = str_replace('{0}', "顧客", Config::get('const.MSG_ERROR.already_name'));
                    $result = false;
                    return response()->json(
                        ['result' => $result, 'code' => $code,
                        Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                    );
                }
            }
            //$code = $this->insert($office_code, $name );

            // insert
            $result = $this->insert($office_code, $name );
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
    private function insert($office_code, $name){
        DB::beginTransaction();
        try{
            $customers = new Customer();
            $systemdate = Carbon::now();
            $authuser = Auth::user();
            $user_code = $authuser->code;
            //$applyfrom = new Carbon($data['apply_term_from']);
            $max_code = $customers->getMaxCode($office_code);          // code 自動採番
            if (isset($max_code)) {
                $code = $max_code + 1;
            } else {
                $code = 1;
            }
            $code = sprintf('%02d', $code);
            Log::debug('CustomerAddController store insert in auto-code = '.$code);
            $customers->setCodeAttribute($code);
            $customers->setOfficecodeAttribute($office_code);
            $customers->setNameAttribute($name);
            $customers->setCreatedatAttribute($systemdate);
            $customers->setCreateduserAttribute($user_code);
            // insert
            $customers->insertNewCustomer();
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
            $details = $params['details'];
            //$before_details = $params['before_details'];
            //$this->update($details, $before_details);
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
    //private function update($data, $before_data){
    private function update($data){
        $systemdate = Carbon::now();
        $user = Auth::user();
        $login_user_code = $user->code;
        $login_account_id = $user->account_id;
        DB::beginTransaction();
        try{
            $customer_model = new Customer();
            $carbon = new Carbon();
            $temp_from = $carbon->copy()->format('Ymd');
            $apply_term_from = $temp_from;
            $customer_model->setCodeAttribute($data['code']);
            $customer_model->setOfficecodeAttribute($data['office_code']);
            $customer_model->setNameAttribute($data['name']);
            $temp_from = Config::get('const.INIT_DATE.maxdate');
            Log::debug('CustomerAddController update code = '.$data['code']);
            $customer_model->setCreatedatAttribute($systemdate);
            $customer_model->setCreateduserAttribute($login_user_code);
            if ($data['id'] == "" || $data['id'] == null) {
                $customer_model->setCreateduserAttribute($login_user_code);
                $customer_model->setCreatedatAttribute($systemdate);
                Log::debug('UserAddController update non-id = '.$data['code']);
                $customer_model->insertNewCustomer();
            } else {
                $customer_model->setIdAttribute($data['id']);   
                $customer_model->setUpdateduserAttribute($login_user_code);
                $customer_model->setUpdatedatAttribute($systemdate);
                $customer_model->updateCustomer();
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
     * 削除
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
            // 削除
            $this->updateIsDelete($id);
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
        $systemdate = Carbon::now();
        $user = Auth::user();
        $login_user_code = $user->code;
        $customers = new Customer();
        $customers->setIdAttribute($id);
        $customers->setUpdateduserAttribute($login_user_code);
        $customers->setUpdatedatAttribute($systemdate);

        try{
            $customers->updateIsDelete();

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
        Log::debug('CustomerAddController getCustomerDetails go-in ');

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


















}
