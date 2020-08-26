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
use App\Department;
use App\Http\Controllers\ApiCommonController;

class CreateDepartmentController extends Controller
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
        $apicommon = new ApiCommonController();
        // 設定項目要否判定
        $settingtable = $apicommon->getNotSetting();
        return view('create_department',
            compact(
                'authusers',
                'settingtable'
            ));
    }

    /**
     * 部署取得
     *
     * @param Request $request
     * @return void
     */
    public function getDetails(Request $request){
        $this->array_messagedata = array();
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
            // if (!isset($params['code'])) {
            //     Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "code", Config::get('const.LOG_MSG.parameter_illegal')));
            //     $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
            //     return response()->json(
            //         ['result' => false, 'details' => $details,
            //         Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            //     );
            // }
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
     * 部署取得
     *
     * @param Request $request
     * @return void
     */
    public function getDetailsFunc($params){
        $code =  $params['code'];
        $killvalue =  $params['killvalue'];
        $this->array_messagedata = array();
        $details = new Collection();
        $result = true;
        $user = Auth::user();
        $login_user_code = $user->code;
        $login_user_code_4 = substr($login_user_code, 0 ,4);
        try {
            $department_model = new Department();
            $dt = new Carbon();
            $from = $dt->copy()->format('Ymd');
            $department_model->setParamapplytermfromAttribute($from);
            $department_model->setParamcodeAttribute($code);
            $department_model->setParamAccountidAttribute($login_user_code_4);
            $department_model->setKillvalueAttribute($killvalue);
            $details = $department_model->getDetails();
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
     * 部署登録
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
        $login_user_code_4 = substr($login_user_code, 0 ,4);
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
            // 部署名チェック
            if ($name != "") {
                $department_model = new Department();
                $department_model->setNameAttribute($name);
                $department_model->setParamAccountidAttribute($login_user_code_4);
                $isExists = $department_model->isExistsName();
                if ($isExists) {
                    $this->array_messagedata[] = str_replace('{0}', "部署", Config::get('const.MSG_ERROR.already_name'));
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
            $login_user_code_4 = substr($login_user_code, 0 ,4);
            $department = new Department();
            $from = Config::get('const.INIT_DATE.initdate');
            $maxdate = Config::get('const.INIT_DATE.maxdate');
            $department->setParamAccountidAttribute($login_user_code_4);
            $max_code = $department->getMaxCode();          // code 自動採番
            if (isset($max_code)) {
                $code = $max_code + 1;
            } else {
                $code = 1;
            }
            $department->setApplytermfromAttribute($from);
            $department->setAccountidAttribute($login_user_code_4);
            $department->setCodeAttribute($code);
            $department->setNameAttribute($name);
            $department->setKillfromdateAttribute($maxdate);
            $department->setCreatedatAttribute($systemdate);
            $department->setCreateduserAttribute($login_user_code);
            $department->insertDepartment();
        
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
        $department_model = new Department();
        $user = Auth::user();
        $login_user_code = $user->code;
        $login_user_code_4 = substr($login_user_code, 0 ,4);

        DB::beginTransaction();
        try{
            $carbon = new Carbon($details['apply_term_from']);
            $from = $carbon->copy()->format('Ymd');
            $department_model->setApplytermfromAttribute($from);
            $department_model->setCodeAttribute($details['code']);
            $department_model->setNameAttribute($details['name']);
            if ($details['kill_from_date'] == "" || $details['kill_from_date'] == null) {
                $department_model->setKillfromdateAttribute(Config::get('const.INIT_DATE.maxdate'));
            } else {
                $dt = new Carbon($details['kill_from_date']);
                $to = $dt->copy()->format('Ymd');
                $department_model->setKillfromdateAttribute($to);
            }
            $department_model->setUpdatedatAttribute($systemdate);   
            $department_model->setUpdateduserAttribute($login_user_code);   
            if ($details['id'] == "" || $details['id'] == null) {
                $department_model->setAccountidAttribute($login_user_code_4);
                $department_model->insertDepartment();
            } else {
                $department_model->setParamAccountidAttribute($login_user_code_4);
                $department_model->setIdAttribute($details['id']);   
                $department_model->updateDepartment();
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
        $user = Auth::user();
        $login_user_code = $user->code;
        $login_user_code_4 = substr($login_user_code, 0 ,4);
        DB::beginTransaction();
        try{
            DB::table('departments')
            ->where('code', 'like', $login_user_code_4."%")
            ->where('id', $id)
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
        $login_user_code_4 = substr($login_user_code, 0 ,4);
        DB::beginTransaction();
        try{
            DB::table('departments')
            ->where('code', 'like', $login_user_code_4."%")
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
