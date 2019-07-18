<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\WorkingTimeTable;
use App\Calendar;
use App\Http\Controllers\ApiCommonController;



class CreateCalendarController extends Controller
{
    const SUCCESS = 0;
    const FAILED = 1;

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
}
