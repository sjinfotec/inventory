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
use App\Http\Controllers\ApiCommonController;



class CreateCalendarController extends Controller
{
    const SUCCESS = 0;
    const FAILED = 1;
    // メッセージ
    private $array_messagedata = array();

    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        return view('create_calendar');
    }

    /**
     * 登録
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request){
        $business_kubun = $request->businessday_kubun;     // 出勤区分
        $holiday_kubun = $request->holiday_kubun;       // 休暇区分
        $systemdate = Carbon::now();
        $user = Auth::user();
        $user_code = $user->code;
        $dates = $request->dates;
        $convert_dates = array();
        $common = new ApiCommonController();
        $response = collect();
    
        // GMTで渡された日付を ymd + weekに変換
        foreach ($dates as $date) {
            $new_date = new Carbon($date);
            $new_date = new \Carbon\Carbon($new_date);
            $date = $new_date->copy()->addDay();
            $convert_dates[] = $common->getWeekDay($date,'Ymd');
        }
        $result = $this->insert($business_kubun,$holiday_kubun,$user_code,$systemdate,$convert_dates);
        if($result){
            $response->put('result',self::SUCCESS);
        }else{
            $response->put('result',self::FAILED);
        }
        return $response;
    }

    /**
     * INSERT
     *
     * @param [type] $business_kubun
     * @param [type] $holiday_kubun
     * @param [type] $user_code
     * @param [type] $systemdate
     * @param [type] $convert_dates
     * @return void
     */
    private function insert($business_kubun,$holiday_kubun,$user_code,$systemdate,$convert_dates){
        $calendar = new Calendar();
        DB::beginTransaction();
        try{
            $calendar->setBusinesskubunAttribute($business_kubun);
            $calendar->setHolidaykubunAttribute($holiday_kubun);
            $calendar->setCreateduserAttribute($user_code);
            $calendar->setCreatedatAttribute($systemdate);

            foreach ($convert_dates as $date) {
                // 既にdateが存在する場合　→　削除新規
                $calendar->setDateAttribute($date['date']);
                $is_exists = $calendar->isExistsDate();
                if($is_exists){
                    $calendar->delDate();
                }
                $calendar->setWeekdaykubunAttribute($date['id']);
                $calendar->insert();
            }
            DB::commit();
            return true;

        }catch(\PDOException $e){
            DB::rollBack();
            return false;
        }
    }

    /**
     * 初期設定
     *
     */
    public function init(Request $request){
        $apicommon = new ApiCommonController();
        // reqestクエリーセット
        $datefrom = $apicommon->setRequestQeury($request->datefrom);
        $displaykbn = $apicommon->setRequestQeury($request->displaykbn);
        $result = true;

        try{
            $this->initCalendar($datefrom, $displaykbn);

        }catch(\PDOException $pe){
            $result =  false;

        }catch(\Exception $e){
            $result =  false;
        }

        return response()->json(
            ['result' => $result,
            Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]);
    }

    /**
     * 初期設定実行
     *
     */
    public function initCalendar($datefrom, $displaykbn){

        Log::debug('datefrom = '.$datefrom);
        Log::debug('displaykbn = '.$displaykbn);
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
            $this->array_messagedata[] =  array(Config::get('const.RESPONCE_ITEM.message') =>Config::get('const.MSG_ERROR.data_insert_error'));
            Log::error(str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')));
            Log::error($pe->getMessage());
            DB::rollBack();
            return false;
        }catch(\Exception $e){
            $this->array_messagedata[] =  array(Config::get('const.RESPONCE_ITEM.message') =>Config::get('const.MSG_ERROR.data_insert_error'));
            Log::error(str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_select_erorr')));
            Log::error($e->getMessage());
            DB::rollBack();
            $result =  false;
        }
    }
}
