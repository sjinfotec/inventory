<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreTimeTablePost;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\WorkingTimeTable;
use App\FeatureItemSelection;
use App\Http\Controllers\ApiCommonController;

class CreateTimeTableController extends Controller
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
        return view('create_time_table',
            compact(
                'authusers',
                'settingtable'
            ));
    }

    /**
     * 詳細取得（CreateTimeTable.vue,CreateApprovalRouteNo.vue）
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
            $time_table = new WorkingTimeTable();
            $time_table->setNoAttribute($no);
            $details = $time_table->getDetail();
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
                $WorkingTimeTable_model = new WorkingTimeTable();
                $WorkingTimeTable_model->setNameAttribute($name);
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
            //feature selection
            $feature_model = new FeatureItemSelection();
            $feature_model->setParamaccountidAttribute(Config::get('const.ACCOUNTID.account_id'));
            $feature_model->setParamselectioncodeAttribute(Config::get('const.EDITION.EDITION'));
            $feature_data = $feature_model->getItem();
            $attendance_count = 0;
            $rest_count = 0;
            foreach($feature_data as $item) {
                if (isset($item->item_code)) {
                    if ($item->item_code == Config::get('const.C042.attendance_count')) {
                        $attendance_count = intval($item->value_select);
                    }
                    if ($item->item_code == Config::get('const.C042.rest_count')) {
                        $rest_count = intval($item->value_select);
                    }
                }
                if ($attendance_count > 0 && $rest_count > 0) {
                    break;
                }
            }
            $details = $params['details'];
            $data_index = 0;
            // Log::debug('store attendance_count = '.$attendance_count);
            for ($i=0;$i<$attendance_count;$i++) {
                $data[$data_index]['working_time_kubun'] = Config::get('const.C004.regular_working_time');
                // Log::debug('store details regularTimes fromTime = '.$details['regularTimes'][$i]['fromTime']);
                // Log::debug('store details regularTimes toTime = '.$details['regularTimes'][$i]['toTime']);
                // $data[$data_index]['from_time'] = $details['regularFrom'][$i]['fromTime'];
                // $data[$data_index]['to_time'] = $details['regularTo'][$i]['regularTo'];
                $data[$data_index]['from_time'] = $details['regularTimes'][$i]['fromTime'];
                $data[$data_index]['to_time'] = $details['regularTimes'][$i]['toTime'];
                // Log::debug('store data working_time_kubun = '.$data[$data_index]['working_time_kubun']);
                // Log::debug('store data from_time = '.$data[$data_index]['from_time']);
                // Log::debug('store data to_time = '.$data[$data_index]['to_time']);
                $data_index++;
            }
            for ($i=0;$i<$rest_count;$i++) {
                $data[$data_index]['working_time_kubun'] = Config::get('const.C004.regular_working_breaks_time');
                // Log::debug('store details regularRestTimes fromTime = '.$details['regularRestTimes'][$i]['fromTime']);
                // Log::debug('store details regularRestTimes toTime = '.$details['regularRestTimes'][$i]['toTime']);
                $data[$data_index]['from_time'] = $details['regularRestTimes'][$i]['fromTime'];
                $data[$data_index]['to_time'] = $details['regularRestTimes'][$i]['toTime'];
                // Log::debug('store data working_time_kubun = '.$data[$data_index]['working_time_kubun']);
                // Log::debug('store data from_time = '.$data[$data_index]['from_time']);
                // Log::debug('store data to_time = '.$data[$data_index]['to_time']);
                $data_index++;
            }
            $data[$data_index]['working_time_kubun'] = Config::get('const.C004.out_of_regular_night_working_time');
            $data[$data_index]['from_time'] = $details['irregularMidNightFrom'];
            $data[$data_index]['to_time'] = $details['irregularMidNightTo'];
            // Log::debug('store data working_time_kubun = '.$data[$data_index]['working_time_kubun']);
            // Log::debug('store data irregularMidNightFrom = '.$data[$data_index]['from_time']);
            // Log::debug('store data irregularMidNightTo = '.$data[$data_index]['to_time']);
            $resultno = $this->insert($data ,$no ,$name);
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
        $user = Auth::user();
        $login_user_code = $user->code;
        $login_user_code_4 = substr($login_user_code, 0 ,4);
        DB::beginTransaction();
        try{
            $systemdate = Carbon::now();
            $time_table = new WorkingTimeTable();
            $term_from = Config::get('const.INIT_DATE.initdate');
            $time_table->setParamaccountidAttribute($login_user_code_4);
            $time_table->setNoAttribute($maxno);
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
            for ($i=0;$i<count($data);$i++) {
                $time_table->setWorkingtimekubunAttribute($data[$i]['working_time_kubun']);
                $time_table->setFromtimeAttribute($data[$i]['from_time']);
                $time_table->setTotimeAttribute($data[$i]['to_time']);
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
            //feature selection
            $feature_model = new FeatureItemSelection();
            $feature_model->setParamaccountidAttribute(Config::get('const.ACCOUNTID.account_id'));
            $feature_model->setParamselectioncodeAttribute(Config::get('const.EDITION.EDITION'));
            $feature_data = $feature_model->getItem();
            $attendance_count = 0;
            $rest_count = 0;
            foreach($feature_data as $item) {
                if (isset($item->item_code)) {
                    if ($item->item_code == Config::get('const.C042.attendance_count')) {
                        $attendance_count = intval($item->value_select);
                    }
                    if ($item->item_code == Config::get('const.C042.rest_count')) {
                        $rest_count = intval($item->value_select);
                    }
                }
                if ($attendance_count > 0 && $rest_count > 0) {
                    break;
                }
            }
            // +1 は深夜時間の分
            $start_index = ($index - 1) * ($attendance_count + $rest_count + 1);
            $end_index = $start_index + $attendance_count + $rest_count;
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
        $user_code = $user->code;
        DB::beginTransaction();
        try{
            //feature selection
            $feature_model = new FeatureItemSelection();
            $feature_model->setParamaccountidAttribute(Config::get('const.ACCOUNTID.account_id'));
            $feature_model->setParamselectioncodeAttribute(Config::get('const.EDITION.EDITION'));
            $feature_data = $feature_model->getItem();
            $attendance_count = 0;
            $rest_count = 0;
            foreach($feature_data as $item) {
                if (isset($item->item_code)) {
                    if ($item->item_code == Config::get('const.C042.attendance_count')) {
                        $attendance_count = intval($item->value_select);
                    }
                    if ($item->item_code == Config::get('const.C042.rest_count')) {
                        $rest_count = intval($item->value_select);
                    }
                }
                if ($attendance_count > 0 && $rest_count > 0) {
                    break;
                }
            }
            // +1 は深夜時間の分
            $start_index = ($index - 1) * ($attendance_count + $rest_count + 1);
            $end_index = $start_index + ($attendance_count + $rest_count);
            for ($i=$start_index; $i <= $end_index; $i++) {
                // Log::debug('$details[$i] = '.$details[$i]['id']);
                $time_table->setIdAttribute($details[$i]['id']);   
                $time_table->setUpdateduserAttribute($user_code);
                $time_table->setUpdatedatAttribute($systemdate);
                $time_table->updateIsDelete();
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
