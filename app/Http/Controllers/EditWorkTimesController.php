<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Http\Requests\StoreUserPost;
use App\WorkTime;
use Carbon\Carbon;


class EditWorkTimesController extends Controller
{
    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        return view('edit_work_times');
    }

    /** 詳細取得
     *
     * @return list results
     */
    public function get(Request $request){
        $code = $request->code;
        $year = $request->year;
        $month = $request->month;
        $closing = $users = DB::table('settings')->where('fiscal_year', $year)->where('fiscal_month', $month)->where('is_deleted', 0)->value('closing');
        $ymd_start = $year."/".$month."/".$closing." 00:00:00";
        $ymd_end = $year."/".$month."/".$closing." 23:59:59";
        $closing_start = new Carbon($ymd_start);
        $closing_end = new Carbon($ymd_end);
        $closing_start = $closing_start->copy()->subMonth()->addDay()->format('Y/m/d H:i:s');
        $closing_end = $closing_end->format('Y/m/d H:i:s');

        $work_times = new WorkTime();
        $work_times->setUsercodeAttribute($code);
        $work_times->setParamStartDateAttribute($closing_start);
        $work_times->setParamEndDateAttribute($closing_end);

        $details = $work_times->getUserDetails();
        $count = 0;
        $before_date = "";
        foreach ($details as $detail) {
            $detail->kbn_flag = 0;
            if(isset($detail->record_time)){
                $carbon = new Carbon($detail->record_time);
                $detail->date = $carbon->copy()->format('Y年m月d日');
                $detail->time = $carbon->copy()->format('H:i:s');
                if($detail->date != $before_date){
                    $detail->kbn_flag = 1;
                }
                $before_date = $detail->date;
            }
        }
        
        return $details;
    }
}
