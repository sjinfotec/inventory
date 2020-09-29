<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreCompanyInfoPost;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\CustomerInformation;

class CustomerInformationController extends Controller
{

    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        return view('customer_information');
    }

    /**
     * 会社情報登録
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request){
        $this->array_messagedata = array();
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
            if (!isset($params['form'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "form", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $details = $params['form'];
            $result = $this->insert($details);

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
     * @param [type] $inputs
     * @return void
     */
    private function insert($details){
        $company = new Company();
        $systemdate = Carbon::now();
        $user = Auth::user();
        $login_user_code = $user->code;
        $login_account_id = $user->account_id;
        $apply_term_from = Config::get('const.INIT_DATE.initdate');

        DB::beginTransaction();
        try{
            $company->setParamAccountidAttribute($login_account_id);
            $company->setAccountidAttribute($login_account_id);
            $company->setApplytermfromAttribute($apply_term_from);
            $company->setNameAttribute($details['name']);
            $company->setKanaAttribute($details['kana']);
            $company->setPostcodeAttribute($details['post_code']);
            $company->setAddress1Attribute($details['address1']);
            $company->setAddress2Attribute($details['address2']);
            $company->setAddresskanaAttribute($details['address_kana']);
            $company->setTelnoAttribute($details['tel_no']);
            $company->setFaxnoAttribute($details['fax_no']);
            $company->setRepresentnameAttribute($details['represent_name']);
            $company->setRepresentkanaAttribute($details['represent_kana']);
            $company->setEmailAttribute($details['email']);
            $company->setCreateduserAttribute($login_user_code);
            $company->setCreatedatAttribute($systemdate);
            $is_exists = $company->isExistsInfo();

            if($is_exists){
                $company->delInfo();
            }
            $company->insertCompany();
            DB::commit();
            return true;

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
     * 会社情報取得
     *
     * @return void
     */
    public function getCustomerInfo(){
        $this->array_messagedata = array();
        $details = new Collection();
        $user = Auth::user();
        $login_user_code = $user->code;
        $login_account_id = $user->account_id;
        $result = true;
        try {
            $customer_model = new CustomerInformation();
            $customer_model->setParamAccountidAttribute($login_account_id);
            $details =  $customer_model->getCustomerInfo();

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
     * 会社一覧情報取得
     *
     * @return void
     */
    public function getSsjjooCustomerInfoList(Request $request){
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
            if (!isset($params['sel'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "sel", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $details,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            Log::debug('customerinfomationcontoroller getSsjjooCustomerInfo account_id = '.$params['sel']['account_id']);
            Log::debug('customerinfomationcontoroller getSsjjooCustomerInfo name = '.$params['sel']['name']);
            $account_id = $params['sel']['account_id'];
            $name = $params['sel']['name'];
            $customer_model = new CustomerInformation();
            $customer_model->setParamAccountidAttribute($account_id);
            $customer_model->setParamCompanynameAttribute($name);
            $details =  $customer_model->getCustomerInfoList();

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
     * 会社詳細情報取得
     *
     * @return void
     */
    public function getSsjjooCustomerInfo(Request $request){
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
            if (!isset($params['sel'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "sel", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $details,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            Log::debug('customerinfomationcontoroller getSsjjooCustomerInfo account_id = '.$params['sel']['account_id']);
            Log::debug('customerinfomationcontoroller getSsjjooCustomerInfo name = '.$params['sel']['name']);
            $account_id = $params['sel']['account_id'];
            $name = $params['sel']['name'];
            $customer_model = new CustomerInformation();
            $customer_model->setParamAccountidAttribute($account_id);
            $customer_model->setParamCompanynameAttribute($name);
            $details =  $customer_model->getCustomerInfo();

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
