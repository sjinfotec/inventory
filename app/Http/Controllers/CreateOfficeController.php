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
use App\Office;
use App\Http\Controllers\ApiCommonController;

class CreateOfficeController extends Controller
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
        $authusers = Auth::user();
        $login_user_code = $authusers->code;
        $accountid = $authusers->account_id;
        $edition = Config::get('const.EDITION.EDITION');
        // 設定項目要否判定
        $apicommon = new ApiCommonController();

        return view('create_office',
            compact(
                'authusers',
            ));
    }

    /**
     * 営業所取得
     *
     * @param Request $request
     * @return void
     */
    public function getDetails(Request $request){
        $this->array_messagedata = array();
        $result = true;
        Log::debug('CreateOfficeController getDetails actmode = ');
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
            $details = $this->getDetailsFunc($params);
            $r_cnt = 0;
            foreach($details as $item) {
                $r_cnt++;
                break;
            }
            if ($r_cnt == 0) {
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
     * 営業所取得
     *
     * @param Request $request
     * @return void
     */
    public function getDetailsFunc($params){
        $code =  $params['code'];
        $this->array_messagedata = array();
        $details = new Collection();
        $result = true;
        $user = Auth::user();
        $login_user_code = $user->code;
        $login_account_id = $user->account_id;
        try {
            $office_model = new Office();
            $dt = new Carbon();
            $from = $dt->copy()->format('Ymd');
            if(isset($code)){
                $office_model->setParamcodeAttribute($code);
            }
            $details = $office_model->getDetails();
            return $details;
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 営業所登録
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request){
        $this->array_messagedata = array();
        $code = '';
        $result = true;
        $user = Auth::user();
        $login_user_code = $user->code;
        $login_account_id = $user->account_id;
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
            if (!isset($params['name'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "name", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'code' => $code,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            if (isset($params['code'])) {
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.already_data');
                $result = false;
                return response()->json(
                    ['result' => $result, 'code' => $code,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $name = $params['name'];
            // 営業所名チェック
            if ($name != "") {
                $office_model = new Office();
                $office_model->setNameAttribute($name);
                $isExists = $office_model->isExistsName();
                if ($isExists) {
                    $this->array_messagedata[] = str_replace('{0}', "営業所", Config::get('const.MSG_ERROR.already_name'));
                    $result = false;
                    return response()->json(
                        ['result' => $result, 'code' => $code,
                        Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                    );
                }
            }
            $code = $this->insert($code, $name);
    
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
     * INSERT
     *
     * @param [type] $name
     * @return void
     */
    private function insert($code, $name){
        DB::beginTransaction();
        try{
            $systemdate = Carbon::now();
            $user = Auth::user();
            $login_user_code = $user->code;
            $login_account_id = $user->account_id;
            $office = new Office();
            $from = Config::get('const.INIT_DATE.initdate');
            $maxdate = Config::get('const.INIT_DATE.maxdate');
            $max_code = $office->getMaxCode();          // code 自動採番
            if (isset($max_code)) {
                $code = $max_code + 1;
            } else {
                $code = 1;
            }
            $office->setCodeAttribute($code);
            $office->setNameAttribute($name);
            $office->setCreatedatAttribute($systemdate);
            $office->setCreateduserAttribute($login_user_code);
            $office->insertOffice();
        
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
     * 更新
     *
     * @param [type] $details
     * @return boolean
     */
    private function fixData($details){
        $systemdate = Carbon::now();
        $office_model = new Office();
        $user = Auth::user();
        $login_user_code = $user->code;
        $login_account_id = $user->account_id;

        DB::beginTransaction();
        try{
            $office_model->setCodeAttribute($details['code']);
            $office_model->setNameAttribute($details['name']);
            $office_model->setUpdatedatAttribute($systemdate);   
            $office_model->setUpdateduserAttribute($login_user_code);   
            $office_model->setIdAttribute($details['id']);   
            $office_model->updateOffice();
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
        $office_model = new Office();
        $user = Auth::user();
        $user_code = $user->code;
        DB::beginTransaction();
        try{
            $office_model->setIdAttribute($id);
            $office_model->setUpdatedatAttribute($systemdate);   
            $office_model->setUpdateduserAttribute($user_code);   
            $office_model->delOffice();
            DB::commit();

        }catch(\PDOException $pe){
            DB::rollBack();
            throw $pe;
        }
    }


    /**
     * 上書き(Is_deleted)
     *
     * @param [type] $id
     * @return void
     */
    public function updateIsDelete_xx($id){
        $user = Auth::user();
        $login_user_code = $user->code;
        $login_account_id = $user->account_id;
        DB::beginTransaction();
        try{
            DB::table('offices')
            ->where('id', $id)
            ->update(['updated_at'=>$this->updated_at])
            ->update(['updated_user'=>$this->updated_user])
            ->update(['is_deleted' => 1]);
            DB::commit();

        }catch(\PDOException $pe){
            DB::rollBack();
            throw $pe;
        }
    }

    /**
     * 上書き(name)
     *
     * @param [type] $id
     * @param [type] $name
     * @return void
     */
    public function updateName($id,$name){
        $systemdate = Carbon::now();
        $user = Auth::user();
        $login_user_code = $user->code;
        $login_account_id = $user->account_id;
        DB::beginTransaction();
        try{
            DB::table('departments')
            ->where('code', 'like', $login_account_id."%")
            ->where('id', $id)
            ->where('is_deleted', 0)
            ->update(['name' => $name,'updated_at' => $systemdate]);
            DB::commit();
            return true;

        }catch(\PDOException $e){
            DB::rollBack();
            return false;
        }
    }
}
