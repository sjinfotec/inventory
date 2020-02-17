<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\WorkingTimeTable;
use App\Calendar;
use App\Setting;

class EditCalendarController extends Controller
{
    // メッセージ
    private $array_messagedata = array();
    const SUCCESS = 0;
    const FAILED = 1;

    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        return view('setting_calendar');
    }

    /**
     * データ取得
     *
     * @param Request $request
     * @return array
     */
    public function getDetail(Request $request){
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
            if (!isset($params['year'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "year", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $details,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            if (!isset($params['month'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "month", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false, 'details' => $details,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $year = $params['year'];
            $month = str_pad($params['month'],2,0,STR_PAD_LEFT);
            $search_y_m = $year."".$month;
            $calendar_model = new Calendar();
            $calendar_model->setDateAttribute($search_y_m);
            $details = $calendar_model->getDetail();
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
     * 登録
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request){
        // request
        $details = $request->details;
        $business_days = $request->businessdays;
        $holi_days = $request->holidays;
        $converts = array();
        $response = collect();
    
        // details に入力された区分を上書き
        foreach ($details as $index => $detail) {
            $formated = new Carbon($detail['date']);
            $converts[$index]['date'] = $formated->format('Ymd');
            $converts[$index]['business_kubun'] = $business_days[$index];
            $converts[$index]['holiday_kubun'] = $holi_days[$index];
        }
        $result = $this->update($converts);
        if($result){
            $response->put('result',self::SUCCESS);
        }else{
            $response->put('result',self::FAILED);
        }
        return $response;
    }

    /**
     * UPDATE
     *
     * @param [type] $converts
     * @return void
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
            if (!isset($params['businessdays'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "businessdays", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            if (!isset($params['holidays'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "holidays", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $details = $params['details'];
            $businessdays = $params['businessdays'];
            $holidays = $params['holidays'];
            $converts = array();
            // details に入力された区分を上書き
            foreach ($details as $index => $detail) {
                $formated = new Carbon($detail['date']);
                $converts[$index]['date'] = $formated->format('Ymd');
                $converts[$index]['businessdays'] = $businessdays[$index];
                $converts[$index]['holidays'] = $holidays[$index];
                Log::debug('$converts[$index][date] = '.$converts[$index]['date']);
                Log::debug('$converts[$index][businessdays] = '.$converts[$index]['businessdays']);
                Log::debug('$businessdays[$index] = '.$businessdays[$index]);
                Log::debug('$converts[$index][holidays] = '.$converts[$index]['holidays']);
                Log::debug('$holidays[$index] = '.$holidays[$index]);
            }
    
            $this->fixData($converts);
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
     * 更新
     *
     * @param [type] $details
     * @return boolean
     */
    private function fixData($details){
        $systemdate = Carbon::now();
        $user = Auth::user();
        $user_code = $user->code;
        $calendar_model = new Calendar();

        DB::beginTransaction();
        try{
            $calendar_model->setUpdateduserAttribute($user_code);
            $calendar_model->setUpdatedatAttribute($systemdate);

            foreach ($details as $data) {
                $calendar_model->setDateAttribute($data['date']);
                Log::debug('$data[businessdays] = '.$data['businessdays']);
                $calendar_model->setBusinesskubunAttribute($data['businessdays']);
                $calendar_model->setHolidaykubunAttribute($data['holidays']);
                $calendar_model->updateCalendar();
            }
            DB::commit();

        }catch(\PDOException $pe){
            DB::rollBack();
            throw $pe;
        }catch(\Exception $e){
            DB::rollBack();
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 初期設定
     *
     */
    public function init(Request $request){
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
            if (!isset($params['datefrom'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "datefrom", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            if (!isset($params['displaykbn'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "displaykbn", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $datefrom = $params['datefrom'];
            $displaykbn = $params['displaykbn'];
            $this->initCalendar($datefrom, $displaykbn);

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
     * 初期設定実行
     *
     */
    public function initCalendar($datefrom, $displaykbn){

        $calendar_model = new Calendar();
        DB::beginTransaction();
        try{
            // 開始日付作成
            if ($displaykbn == 1) {
                // 締日取得
                $setting_model = new Setting();
                $setting_model->setParamYearAttribute($datefrom);
                $begining = $setting_model->getBeginingMonth();
                if (isset($begining)) {
                    Log::debug($datefrom.str_pad($begining, 2, "0", STR_PAD_LEFT).'01');
                    $dt = new Carbon($datefrom.str_pad($begining, 2, "0", STR_PAD_LEFT).'01');
                    $dt_next = new Carbon($datefrom.str_pad($begining, 2, "0", STR_PAD_LEFT).'01');
                    $dt_next->addYear()->subDay();
                } else {
                    $this->array_messagedata[] =  array(Config::get('const.RESPONCE_ITEM.message') =>Config::get('const.MSG_ERROR.not_setting_beginning_month'));
                    return false;
                }
            } else {
                $dt = new Carbon($datefrom.'0101');
                $dt_next = new Carbon($datefrom.'0101');
                $dt_next->addYear()->subDay();
            }
            // 1年分削除
            $calendar_model->delCalenderDateYear(date_format($dt, 'Ymd'), date_format($dt_next, 'Ymd'));
            $dt->subDay();
            $calendar_model->setDateAttribute(date_format($dt, 'Ymd'));
            $user = Auth::user();
            $user_code = $user->code;
            $calendar_model->setCreateduserAttribute($user_code);
            $results = $calendar_model->getCalenderDateYear(date_format($dt, 'Ymd'));
            $temp_array = array();
            foreach($results as $result) {
                $temp_collect = collect($result);
                $temp_array[] = $temp_collect->toArray();
            } 
            $results = $calendar_model->insCalenderDateYear($temp_array);
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
}
