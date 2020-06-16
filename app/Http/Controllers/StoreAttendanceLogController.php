<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\ApiCommonController;
use App\AttendanceLog;
use Carbon\Carbon;

class StoreAttendanceLogController extends Controller
{
    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        $authusers = Auth::user();
        $loginusercode = $authusers->code;
        return view('store_attendance_log', compact('authusers'));
    }

    /**
     * 登録
     *
     * @param Request $request
     * @return response
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
            if (!isset($params['user_code'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "user_code", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            if (!isset($params['eventlogs'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "eventlogs", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            // ログインユーザ設定
            $login_user_code = Auth::user()->code;
            $user_code = $params['user_code'];
            $eventlogs = $params['eventlogs'];
            $department_code = null;
            $employment_status = null;
            // eventlogsの検索パラメータ設定
            // 部署雇用形態はusersから
            $apicommon_model = new ApiCommonController();
            $result = $apicommon_model->getUserDepartmentEmploymentRole($login_user_code, null);
            foreach($result as $item) {
                $department_code = $item->department_code;
                $employment_status = $item->employment_status;
                break;
            }
            Log::debug('  user_code = '.$user_code);
            Log::debug('  department_code = '.$department_code);
            Log::debug('  employment_status = '.$employment_status);
            Log::debug('  login_user_code = '.$login_user_code);
            $attendance_model = new AttendanceLog();
            $attendance_model->setParamdepartmentcodeAttribute($department_code);
            $attendance_model->setParamemploymentstatusAttribute($employment_status);
            $attendance_model->setParamusercodeAttribute($user_code);
            // logファイルは１年分のデータのため、現在日から１年前を登録開始日とする。
            // $dt = Carbon::now();
            // $dt_from = date_format($dt->subYear(), 'Ymd');
            $maxdate = $attendance_model->getWorkingdate();
            $dt_from = $maxdate;
            // eventlogフィルター
            $collect_eventlogs = new Collection($eventlogs);
            $filtered = null;
            if (isset($maxdate)) {
                Log::debug('  event_date >= '.$dt_from);
                $filtered = $collect_eventlogs->where('event_date', ">=", $dt_from);
            } else {
                Log::debug('  event_date >= 20190101');
                $filtered = $collect_eventlogs->where('event_date', ">=", "20190101");
            }

            // eventlogsの登録設定
            Log::debug('  count($filtered) = '.count($filtered));
            if (count($filtered) > 0) {
                $systemdate = Carbon::now();
                $attendance_model->setDepartmentcodeAttribute($department_code);
                $attendance_model->setEmploymentstatusAttribute($employment_status);
                $attendance_model->setUsercodeAttribute($user_code);
                $attendance_model->setCreateduserAttribute($login_user_code);
                $attendance_model->setCreatedatAttribute($systemdate);
                $this->insertLog($attendance_model, $filtered);
            }
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

    /** イベントログ登録
     *
     */
    public function insertLog($attendance_model, $filtered){
        DB::beginTransaction();
        try {
            foreach ($filtered as $item) {
                // 登録済みであるか？
                Log::debug('  $item[event_date] = '.$item['event_date']);
                Log::debug('  $item[event_mode] = '.$item['event_mode']);
                $attendance_model->setParamworkingdatefromAttribute($item['event_date']);
                $attendance_model->setParamworkingdatetoAttribute($item['event_date']);
                $attendance_model->setParameventmodeAttribute($item['event_mode']);
                $attendance_model->setParameventtimeAttribute(substr($item['event_date'],0,4)."/".substr($item['event_date'],4,2)."/".substr($item['event_date'],6,2)." ".$item['event_time']);
                Log::debug('  setParameventtimeAttribute = '.substr($item['event_date'],0,4)."/".substr($item['event_date'],4,2)."/".substr($item['event_date'],6,2)." ".$item['event_time']);
                if (!$attendance_model->isExist()) {
                    // イベントログ設定
                    $attendance_model->setWorkingdateAttribute($item['event_date']);
                    $attendance_model->setEventmodeAttribute($item['event_mode']);
                    $attendance_model->setEventtimeAttribute(
                        substr($item['event_date'],0,4)."/".substr($item['event_date'],4,2)."/".substr($item['event_date'],6,2)." ".$item['event_time']);
                    $attendance_model->store();
                }
            }
            DB::commit();
        }catch(\PDOException $pe){
            DB::rollBack();
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.data_insert_error'));
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
