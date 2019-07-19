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

class EditCalendarController extends Controller
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
        return view('edit_calendar');
    }

    /**
     * データ取得
     *
     * @param Request $request
     * @return array
     */
    public function getDetail(Request $request){
        $carbon = new Carbon();
        $year = $request->year;
        $month = str_pad($request->month,2,0,STR_PAD_LEFT);
        $search_y_m = $year."".$month;
        $business_kubun = $request->business_kubun;
        $holiday_kubun = $request->holiday_kubun;
        $calendar = new Calendar();
        $calendar->setDateAttribute($search_y_m);
        $calendar->setBusinesskubunAttribute($business_kubun);
        $calendar->setHolidaykubunAttribute($holiday_kubun);
        
        $result = $calendar->getDetail();
        foreach ($result as $data) {
            $formated = new Carbon($data->date);
            $data->date = $formated->format('Y/m/d');
        }
        return $result;
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
    private function update($converts){
        $calendar = new Calendar();
        $systemdate = Carbon::now();
        $user = Auth::user();
        $user_code = $user->code;

        DB::beginTransaction();
        try{
            $calendar->setCreateduserAttribute($user_code);
            $calendar->setCreatedatAttribute($systemdate);

            foreach ($converts as $data) {
                // 既にdateが存在する場合　→　削除新規
                $calendar->setDateAttribute($data['date']);
                $calendar->setBusinesskubunAttribute($data['business_kubun']);
                $calendar->setHolidaykubunAttribute($data['holiday_kubun']);
                $calendar->updateCalendar();
            }
            DB::commit();
            return true;

        }catch(\PDOException $e){
            DB::rollBack();
            return false;
        }
    }
}
