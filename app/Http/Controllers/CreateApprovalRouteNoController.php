<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\StoreTimeTablePost;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\WorkingTimeTable;
use Illuminate\Support\Facades\Validator;

class CreateApprovalRouteNoController extends Controller
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
        return view('create_approvalroot');
    }

    /**
     * 詳細取得
     *
     * @param Request $request
     * @return void
     */
    public function getDetail(Request $request) {
        $this->array_messagedata = array();
        $result = true;
        $killvalue = false; // 未使用
        try{
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
            if (!isset($params['no'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "no", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => null,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $no = $params['no'];
            if (isset($params['killvalue'])) {
                $killvalue = $params['killvalue']; // 未使用
            }
            $user = Auth::user();
            $login_user_code = $user->code;
            $login_user_code_4 = substr($login_user_code, 0 ,4);
            $time_table = new WorkingTimeTable();
            $time_table->setNoAttribute($no);
            $time_table->setParamaccountidAttribute($login_user_code_4);

            $details = $time_table->getDetailTimeTable();
            return response()->json(
                ['result' => true, 'details' => $details,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 登録
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request){
        $this->array_messagedata = array();
        $no = '';
        $result = true;
        $data = array();
        try{
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'no' => $no,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            if (!isset($params['name'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "name", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'no' => $no,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            if (!isset($params['details'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "details", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'no' => $no,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            if (isset($params['no'])) {
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.already_data');
                $result = false;
                return response()->json(
                    ['result' => $result, 'no' => $no,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $name = $params['name'];
            // タイムテーブル名チェック
            if ($name != "") {
                $user = Auth::user();
                $login_user_code = $user->code;
                $login_user_code_4 = substr($login_user_code, 0 ,4);
                $WorkingTimeTable_model = new WorkingTimeTable();
                $WorkingTimeTable_model->setNameAttribute($name);
                $time_table->setParamaccountidAttribute($login_user_code_4);
                $isExists = $WorkingTimeTable_model->isExistsName();
                if ($isExists) {
                    $this->array_messagedata[] = str_replace('{0}', "タイムテーブル", Config::get('const.MSG_ERROR.already_name'));
                    $result = false;
                    return response()->json(
                        ['result' => $result, 'no' => $no,
                        Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                    );
                }
            }
            $details = $params['details'];
            $data[0]['apply_term_from'] = $details['apply_term_from'];
            $data[0]['working_time_kubun'] = 1;
            $data[0]['from_time'] = $details['regularFrom'];
            $data[0]['to_time'] = $details['regularTo'];
            $data[1]['working_time_kubun'] = 2;
            $data[1]['from_time'] = $details['regularRestFrom1'];
            $data[1]['to_time'] = $details['regularRestTo1'];
            $data[2]['working_time_kubun'] = 2;
            $data[2]['from_time'] = $details['regularRestFrom2'];
            $data[2]['to_time'] = $details['regularRestTo2'];
            $data[3]['working_time_kubun'] = 2;
            $data[3]['from_time'] = $details['regularRestFrom3'];
            $data[3]['to_time'] = $details['regularRestTo3'];
            $data[4]['working_time_kubun'] = 2;
            $data[4]['from_time'] = $details['regularRestFrom4'];
            $data[4]['to_time'] = $details['regularRestTo4'];
            $data[5]['working_time_kubun'] = 2;
            $data[5]['from_time'] = $details['regularRestFrom5'];
            $data[5]['to_time'] = $details['regularRestTo5'];
            $data[6]['working_time_kubun'] = 4;
            $data[6]['from_time'] = $details['irregularMidNightFrom'];
            $data[6]['to_time'] = $details['irregularMidNightTo'];
            $resultno = $this->insert($data ,$no, $name);
            return response()->json(
                ['result' => true, 'no' => $resultno,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 新規
     *
     * @param [type] $data
     * @param [type] $no
     * @param [type] $name
     * @return void
     */
    private function insert($data, $no, $name){
        DB::beginTransaction();
        try{
            $systemdate = Carbon::now();
            $time_table = new WorkingTimeTable();
            $user = Auth::user();
            $login_user_code = $user->code;
            $login_user_code_4 = substr($login_user_code, 0 ,4);
            $term_from = Config::get('const.INIT_DATE.initdate');
            $time_table->setParamaccountidAttribute($login_user_code_4);
            $maxno = $time_table->getMaxNo();
            if (isset($maxno)) {
                $maxno = $maxno + 1;
            } else {
                $maxno = 1;
            }
            $time_table->setNoAttribute($login_user_code_4.$maxno);
            $time_table->setApplytermfromAttribute($term_from);
            $time_table->setNameAttribute($name);
            $time_table->setCreateduserAttribute($login_user_code);
            $time_table->setCreatedatAttribute($systemdate);
            foreach ($data as $item) {
               $time_table->setWorkingtimekubunAttribute($item['working_time_kubun']);
               $time_table->setFromtimeAttribute($item['from_time']);
               $time_table->setTotimeAttribute($item['to_time']);
               $time_table->insert();
            }
            DB::commit();
            return $maxno;
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
     * タイムテーブル編集
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
            if (!isset($params['index'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "index", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $index = $params['index'];
            if (!isset($params['details'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "details", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $details = $params['details'];
            $this->update($index, $details);
            return response()->json(
                ['result' => true,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * UPDATE
     *
     * @param [type] $details
     * @return boolean
     */
    private function update($index, $details){
        $systemdate = Carbon::now();
        $time_table = new WorkingTimeTable();
        $user = Auth::user();
        $user_code = $user->code;
        $no = 0;
        $name = "";
        DB::beginTransaction();
        try{
            $start_index = ($index - 1) * 7;
            $end_index = $start_index + 6;
            for ($i=$start_index; $i <= $end_index; $i++) {
                if($i == $start_index){
                    if(isset($details[$i]['apply_term_from'])){
                        $carbon = new Carbon($details[$i]['apply_term_from']);
                        $temp_from = $carbon->copy()->format('Ymd');
                        $apply_term_from = $temp_from;
                    }
                    if(isset($details[$i]['no'])){
                        $no = $details[$i]['no'];
                    }
                    if(isset($details[$i]['name'])){
                        $name = $details[$i]['name'];
                    }
                }
                $time_table->setApplytermfromAttribute($apply_term_from);
                $time_table->setNameAttribute($name);
                $time_table->setNoAttribute($no);
                $time_table->setWorkingtimekubunAttribute($details[$i]['working_time_kubun']);
                $time_table->setFromtimeAttribute($details[$i]['from_time']);
                $time_table->setTotimeAttribute($details[$i]['to_time']);
                // Log::debug('$i = '.$i);
                // Log::debug('$details[id] = '.$details[$i]['id']);
                // Log::debug('$details[from_time] = '.$details[$i]['from_time']);
                // Log::debug('$details[to_time] = '.$details[$i]['to_time']);
                if ($details[$i]['id'] == "" || $details[$i]['id'] == null) {
                    $time_table->setCreateduserAttribute($user_code);
                    $time_table->setCreatedatAttribute($systemdate);
                    $time_table->insert();
                } else {
                    $time_table->setIdAttribute($details[$i]['id']);   
                    $time_table->setUpdateduserAttribute($user_code);
                    $time_table->setUpdatedatAttribute($systemdate);
                    $time_table->updateDetail();
                }
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
     * タイムテーブル削除
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
            if (!isset($params['index'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "index", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $index = $params['index'];
            if (!isset($params['details'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "details", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $details = $params['details'];
            $this->updateIsDelete($index, $details);
            return response()->json(
                ['result' => $result,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 論理削除
     *
     * @param [type] $no
     * @return void
     */
    public function updateIsDelete($index, $details){
        
        $systemdate = Carbon::now();
        $time_table = new WorkingTimeTable();
        $user = Auth::user();
        $login_user_code = $user->code;
        $login_user_code_4 = substr($login_user_code, 0 ,4);
        DB::beginTransaction();
        try{
            $start_index = ($index - 1) * 7;
            $end_index = $start_index + 6;
            for ($i=$start_index; $i <= $end_index; $i++) {
                // Log::debug('$details[$i] = '.$details[$i]['id']);
                $time_table->setParamAccountidAttribute($login_user_code_4);   
                $time_table->setIdAttribute($details[$i]['id']);   
                $time_table->setUpdateduserAttribute($login_user_code);
                $time_table->setUpdatedatAttribute($systemdate);
                $time_table->updateIsDeleteTimeTable();
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

}
