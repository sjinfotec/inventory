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
use App\Company;
use App\Http\Controllers\ApiCommonController;

class CreateCompanyInformationController extends Controller
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

        return view('create_company_information',
            compact(
                'authusers',
                'isexistdownload',
                'settingtable'
            ));
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
     * 会社情報取得（request）
     *
     * @return void
     */
    public function getCompanyInfo(){
        $this->array_messagedata = array();
        try {
            $details = $this->getCompanyInfoFunc();
            return response()->json(
                ['result' => true, 'details' => $details,
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
     * 会社情報取得
     *
     * @return void
     */
    public function getCompanyInfoFunc(){
        $this->array_messagedata = array();
        $details = new Collection();
        $user = Auth::user();
        $login_user_code = $user->code;
        $login_account_id = $user->account_id;
        $result = true;
        try {
            $company = new Company();
            $company->setParamAccountidAttribute($login_account_id);
            $details =  $company->getCompanyInfo();

            return $details;
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }
}
