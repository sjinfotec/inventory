<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\StoreDepartmentPost;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\GeneralCodes;

class SettingEmploymentController extends Controller
{
    // メッセージ
    private $array_messagedata = array();

    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        return view('setting_employment');
    }

    /**
     * 雇用形態取得
     *
     * @param Request $request
     * @return void
     */
    public function getDetails(Request $request){
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
            if (!isset($params['identification_id'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "identification_id", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $details,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $identificationid =  $params['identification_id'];
            $code = null;
            if (isset($params['code'])) {
                $code =  $params['code'];
            }
            // 雇用形態情報
            $generalcode_model = new GeneralCodes();
            $generalcode_model->setParamidentificationidAttribute($identificationid);
            $generalcode_model->setParamcodeAttribute($code);
            $details = $generalcode_model->getGeneralcode();
            if (count($details) == 0) {
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.not_found_data');
                $result = false;
            }
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
        return $details;
    }

    /**
     * 雇用形態登録
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
                    ['result' => false, 'code' => $code,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            if (!isset($params['identification_id'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "identification_id", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'code' => $code,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            if (!isset($params['code_name'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "name", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'code' => $code,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $identificationid =  $params['identification_id'];
            $code_name = $params['code_name'];
            // 雇用形態名チェック
            if ($code_name != "") {
                $generalcode_model = new GeneralCodes();
                $generalcode_model->setParamidentificationidAttribute($identificationid);
                $generalcode_model->setParamcodenameAttribute($code_name);
                $isExists = $generalcode_model->isExistsName();
                if ($isExists) {
                    $this->array_messagedata[] = str_replace('{0}', "雇用形態", Config::get('const.MSG_ERROR.already_name'));
                    $result = false;
                    return response()->json(
                        ['result' => $result, 'code' => $code,
                        Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                    );
                }
            }
            $code = $this->insert($identificationid, $code_name);
    
            return response()->json(
                ['result' => $result, 'code' => $code,
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
     * @param [type] $code_name
     * @return void
     */
    private function insert($identificationid, $code_name){
        DB::beginTransaction();
        try{
            $systemdate = Carbon::now();
            $user = Auth::user();
            $generalcode_model = new GeneralCodes();
            $user_code = $user->code;
            $generalcode_model->setParamidentificationidAttribute($identificationid);
            $max_code = $generalcode_model->getMaxCode();           // code 自動採番
            $max_seq = $generalcode_model->getMaxSeq();             // seq 自動採番
            $code = 0;
            if (isset($max_code)) {
                $code = $max_code + 1;
            } else {
                $code = 1;
            }
            $seq = 0;
            if (isset($max_seq)) {
                $seq = $max_seq + 1;
            } else {
                $seq = 1;
            }
            $generalcode_model->setIdentificationidAttribute($identificationid);
            $generalcode_model->setCodeAttribute($code);
            $generalcode_model->setSortseqAttribute($seq);
            // 現在雇用形態のみ
            $generalcode_model->setIdentificationnameAttribute("雇用形態");
            $generalcode_model->setDescriptionAttribute("");
            $generalcode_model->setPhysicalnameAttribute("");
            $generalcode_model->setCodenameAttribute($code_name);
            $generalcode_model->setSecoundcodenameAttribute($code_name);
            $generalcode_model->setCreatedatAttribute($systemdate);
            $generalcode_model->setCreateduserAttribute($user_code);
            $generalcode_model->insertGeneral();
        
            DB::commit();
            return $code;

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
     * 更新ボタン押下 
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

        return $response;
    }

    /**
     * 更新
     *
     * @param [type] $details
     * @return boolean
     */
    private function fixData($details){
        $systemdate = Carbon::now();
        $generalcode_model = new GeneralCodes();
        $user = Auth::user();
        $user_code = $user->code;

        DB::beginTransaction();
        try{
            $generalcode_model->setIdAttribute($details['id']);
            $generalcode_model->setCodenameAttribute($details['code_name']);
            $generalcode_model->setSecoundcodenameAttribute($details['code_name']);
            $generalcode_model->setUsefreeitemAttribute($details['use_free_item']);
            $generalcode_model->setUpdatedatAttribute($systemdate);   
            $generalcode_model->setUpdateduserAttribute($user_code);   
            $generalcode_model->updateGeneral();
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
     * 論理削除
     *
     * @param Request $request
     * @return void
     */
    public function del(Request $request){
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

        return $response;
    }

    /**
     * 上書き(Is_deleted)
     *
     * @param [type] $id
     * @return void
     */
    public function updateIsDelete($id){
        $systemdate = Carbon::now();
        $generalcode_model = new GeneralCodes();
        $user = Auth::user();
        $user_code = $user->code;
        DB::beginTransaction();
        try{
            $generalcode_model->setIdAttribute($id);
            $generalcode_model->updateGeneralIsdelete();
            $generalcode_model->setUpdatedatAttribute($systemdate);   
            $generalcode_model->setUpdateduserAttribute($user_code);   
            DB::commit();

        }catch(\PDOException $pe){
            DB::rollBack();
            throw $pe;
        }
    }
}
