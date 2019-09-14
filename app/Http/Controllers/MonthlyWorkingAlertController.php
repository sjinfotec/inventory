<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\WorkingTimedate;

class MonthlyWorkingAlertController extends Controller
{
    private $array_messagedata = array();

    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        return view('monthly_working_alert');
    }

    /**
     * 月次アラート表示 
     *
     * @return void
     */
    public function show(Request $request){

        Log::debug('---- 月次アラート表示 開始 -------------- ');
        Log::debug('---- パラメータ　$request->datefrom = '.$request->datefrom);
        Log::debug('---- パラメータ　$request->displaykbn = '.$request->displaykbn);
        Log::debug('---- パラメータ　$request->employmentstatus = '.$request->employmentstatus);
        Log::debug('---- パラメータ　$request->departmentcode = '.$request->departmentcode);
        Log::debug('---- パラメータ　$request->usercode = '.$request->usercode);

        $apicommon = new ApiCommonController();
        // reqestクエリーセット
        $datefrom = $apicommon->setRequestQeury($request->datefrom);
        $displaykbn = $apicommon->setRequestQeury($request->displaykbn);
        $employmentstatus = $apicommon->setRequestQeury($request->employmentstatus);
        $departmentcode = $apicommon->setRequestQeury($request->departmentcode);
        $usercode = $apicommon->setRequestQeury($request->usercode);

        $working_time_items = array();
        $working_time_values = array();
        $workingtimedate_model = new WorkingTimedate();
        // 日付開始終了の作成
        $workingtimedate_model->setArrayParamdatefromAttribute($datefrom, $displaykbn);
        $array_messagedata = $workingtimedate_model->getMassegedataAttribute();
        if (count($array_messagedata) > 0) {
            return response()->json([
                'timeitems' => $working_time_items,
                'timevalues' => $working_time_values,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata
            ]);
        }
        // アラート開始年月を設定(datetoで)
        $array_alert_date = array();
        $array_alert_date = $workingtimedate_model->getArrayParamdatetoAttribute();
        $alert_from_ym = date_format(new Carbon($array_alert_date[0]), 'Ym');
        Log::debug('アラート開始年月 $alert_from_ym = '.$alert_from_ym);
        // $datefromの６か月前の年月を求める
        $w_dt2 = new Carbon($datefrom);
        $w_dt3 = new Carbon($datefrom);
        $w_dt6 = new Carbon($datefrom);
        $date_2before_ym = date_format($w_dt2->subMonthNoOverflow(2), 'Ym');
        $date_3before_ym = date_format($w_dt3->subMonthNoOverflow(3), 'Ym');
        $date_6before_ym = date_format($w_dt6->subMonthNoOverflow(6), 'Ym');
        Log::debug('$datefromの2か月前の年月 $date_2before_ym = '.$date_2before_ym);
        Log::debug('$datefromの3か月前の年月 $date_3before_ym = '.$date_3before_ym);
        Log::debug('$datefromの6か月前の年月 $date_6before_ym = '.$date_6before_ym);

        $w_date = new Carbon($alert_from_ym.'01');
        $working_date_1 = date_format($w_date, 'Y/m');
        $working_date_2 = date_format($w_date->addMonthNoOverflow(),'Y/m');
        $working_date_3 = date_format($w_date->addMonthNoOverflow(),'Y/m');
        $working_date_4 = date_format($w_date->addMonthNoOverflow(),'Y/m');
        $working_date_5 = date_format($w_date->addMonthNoOverflow(),'Y/m');
        $working_date_6 = date_format($w_date->addMonthNoOverflow(),'Y/m');
        $working_date_7 = date_format($w_date->addMonthNoOverflow(),'Y/m');
        $working_date_8 = date_format($w_date->addMonthNoOverflow(),'Y/m');
        $working_date_9 = date_format($w_date->addMonthNoOverflow(),'Y/m');
        $working_date_10 = date_format($w_date->addMonthNoOverflow(),'Y/m');
        $working_date_11 = date_format($w_date->addMonthNoOverflow(),'Y/m');
        $working_date_12 = date_format($w_date->addMonthNoOverflow(),'Y/m');

        $working_time_items[] = array(
            'department_name' => '部署',
            'employment_status_name' => '勤務形態',
            'user_name' => '氏名',
            'working_date_1' => $working_date_1,
            'working_date_2' => $working_date_2,
            'working_date_3' => $working_date_3,
            'working_date_4' => $working_date_4,
            'working_date_5' => $working_date_5,
            'working_date_6' => $working_date_6,
            'working_date_7' => $working_date_7,
            'working_date_8' => $working_date_8,
            'working_date_9' => $working_date_9,
            'working_date_10' => $working_date_10,
            'working_date_11' => $working_date_11,
            'working_date_12' => $working_date_12
        );

        // 累計時間取得
        $array_alert_date = $workingtimedate_model->setParamEmploymentStatusAttribute($employmentstatus);
        $array_alert_date = $workingtimedate_model->setParamDepartmentcodeAttribute($departmentcode);
        $array_alert_date = $workingtimedate_model->setParamUsercodeAttribute($usercode);
        $monthly_alerts = $workingtimedate_model->getMonthlyAlertTimeSum($datefrom.'01');
        if (count($monthly_alerts) == 0) {
            Log::debug(Config::get('const.MSG_INFO.no_alert_data'));
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_INFO.no_alert_data'));
            return response()->json([
                'timeitems' => $working_time_items,
                'timevalues' => $working_time_values,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata
            ]);
        }

        // 計算エリア
        foreach($monthly_alerts as $monthly_alert) {
            // 項目名称設定（最初のみ）
            if (count($working_time_items) == 0) {
            }
            $user_alert_chk = true;
            $manthly_alert_warning_1_chk = Config::get('const.ALERT_INFO_RESULT.OK');
            $manthly_alert_warning_2_chk = Config::get('const.ALERT_INFO_RESULT.OK');
            $manthly_alert_warning_3_chk = Config::get('const.ALERT_INFO_RESULT.OK');
            $manthly_alert_warning_12_chk = Config::get('const.ALERT_INFO_RESULT.OK');
            $month_alert_45_total_cnt_chk = Config::get('const.ALERT_INFO_RESULT.OK');
            $manthly_alert_warning_100_total_chk = Config::get('const.ALERT_INFO_RESULT.OK');
            $manthly_alert_ave_2_chk = Config::get('const.ALERT_INFO_RESULT.OK');
            $manthly_alert_ave_3_chk = Config::get('const.ALERT_INFO_RESULT.OK');
            $manthly_alert_ave_4_chk = Config::get('const.ALERT_INFO_RESULT.OK');
            $manthly_alert_ave_5_chk = Config::get('const.ALERT_INFO_RESULT.OK');
            $manthly_alert_ave_6_chk = Config::get('const.ALERT_INFO_RESULT.OK');
            $manthly_alert_warning_720_total_chk = Config::get('const.ALERT_INFO_RESULT.OK');
            $this_month_index = 0;
            $this_2before_index = 0;
            $this_3before_index = 0;
            $this_6before_index = 0;
            $this_12before_index = 0;
            // 45時間/月チェック
            $dt_alert_from_ym = new Carbon($alert_from_ym.'01');
            $dt_datefrom = new Carbon($datefrom.'01');
            $this_month_index = $dt_alert_from_ym->diffInMonths($dt_datefrom);
            Log::debug('当月index = '.$this_month_index);
            $thisMonth_total = $this->getTotalItemData($monthly_alert, $this_month_index);
            Log::debug('当月total = '.$thisMonth_total);
            if ($thisMonth_total > Config::get('const.C023.manthly_alert_warning_1')) {
                $manthly_alert_warning_1_chk = Config::get('const.ALERT_INFO_RESULT.NG');
                $user_alert_chk = false;
            }
            // 81時間/2か月チェック
            if ($this_month_index > 0) {
                $this_2before_index = $this_month_index - 1;
            } else {
                $this_2before_index = 0;
            }
            Log::debug('2か月index = '.$this_2before_index);
            $month_2before_total = 0;
            for ($i=$this_2before_index;$i<=$this_month_index;$i++) {
                $w_total = $this->getTotalItemData($monthly_alert, $i);
                $month_2before_total = $month_2before_total + $w_total;
            }
            Log::debug('2か月total = '.$month_2before_total);
            if ($month_2before_total > Config::get('const.C023.manthly_alert_warning_2')) {
                $manthly_alert_warning_2_chk = Config::get('const.ALERT_INFO_RESULT.NG');
                $user_alert_chk = false;
            }
            // 120時間/3か月チェック
            if ($this_month_index > 1) {
                $this_3before_index = $this_month_index - 2;
            } else {
                $this_3before_index = 0;
            }
            Log::debug('3か月index = '.$this_3before_index);
            $month_3before_total = 0;
            for ($i=$this_3before_index;$i<=$this_month_index;$i++) {
                $w_total = $this->getTotalItemData($monthly_alert, $i);
                $month_3before_total = $month_3before_total + $w_total;
            }
            Log::debug('3か月total = '.$month_2before_total);
            if ($month_3before_total > Config::get('const.C023.manthly_alert_warning_3')) {
                $manthly_alert_warning_3_chk = Config::get('const.ALERT_INFO_RESULT.NG');
                $user_alert_chk = false;
            }
            // 360時間/12か月チェック
            if ($this_month_index > 10) {
                $this_12before_index = $this_month_index - 11;
            } else {
                $this_12before_index = 0;
            }
            Log::debug('12か月index = '.$this_12before_index);
            $month_12before_total = 0;
            for ($i=$this_12before_index;$i<=$this_month_index;$i++) {
                $w_total = $this->getTotalItemData($monthly_alert, $i);
                $month_12before_total = $month_12before_total + $w_total;
            }
            Log::debug('12か月total = '.$month_2before_total);
            if ($month_12before_total > Config::get('const.C023.manthly_alert_warning_4')) {
                $manthly_alert_warning_12_chk = Config::get('const.ALERT_INFO_RESULT.NG');
                $user_alert_chk = false;
            }
            // <特別条項>残業が45時間超えた月が合計6か月に対する警告
            $month_45_total_cnt = 0;
            for ($i=0;$i<=$this_month_index;$i++) {
                $w_total = $this->getTotalItemData($monthly_alert, $i);
                if ($w_total > Config::get('const.C023.manthly_alert_warning_1')) {
                    $month_45_total_cnt++;
                }
            }
            Log::debug('5時間超えた月が合計6か月 total = '.$month_45_total_cnt);
            if ($month_45_total_cnt > Config::get('const.C023.manthly_alert_warning_5')) {
                $month_alert_45_total_cnt_chk = Config::get('const.ALERT_INFO_RESULT.NG');
                $user_alert_chk = false;
            }
            // <特別条項>残業が45時間超えた月が最大100時間を超えないに対する警告
            if ($thisMonth_total > Config::get('const.C023.manthly_alert_warning_6')) {
                $manthly_alert_warning_100_total_chk = Config::get('const.ALERT_INFO_RESULT.NG');
                $user_alert_chk = false;
            }
            // <特別条項>2ヵ月ないし6か月平均で80時間以内に対する警告
            if ($this_month_index > 4) {
                $this_6before_index = $this_month_index - 5;
            } else {
                $this_6before_index = 0;
            }
            Log::debug('6か月index = '.$this_6before_index);
            $month_2before_total = 0;
            $month_3before_total = 0;
            $month_4before_total = 0;
            $month_5before_total = 0;
            $month_6before_total = 0;
            $month_12before_total = 0;
            for ($i=$this_6before_index;$i<=$this_month_index;$i++) {
                $w_total = $this->getTotalItemData($monthly_alert, $i);
                $diffindex = $this_month_index - $i;
                if ($diffindex <= 1) {
                    $month_2before_total = $month_2before_total + $w_total;
                    $month_3before_total = $month_2before_total + $w_total;
                    $month_4before_total = $month_2before_total + $w_total;
                    $month_5before_total = $month_2before_total + $w_total;
                    $month_6before_total = $month_2before_total + $w_total;
                } elseif ($diffindex <= 2) {
                    $month_3before_total = $month_2before_total + $w_total;
                    $month_4before_total = $month_2before_total + $w_total;
                    $month_5before_total = $month_2before_total + $w_total;
                    $month_6before_total = $month_2before_total + $w_total;
                } elseif ($diffindex <= 3) {
                    $month_4before_total = $month_2before_total + $w_total;
                    $month_5before_total = $month_2before_total + $w_total;
                    $month_6before_total = $month_2before_total + $w_total;
                } elseif ($diffindex <= 4) {
                    $month_5before_total = $month_2before_total + $w_total;
                    $month_6before_total = $month_2before_total + $w_total;
                } elseif ($diffindex <= 5) {
                    $month_6before_total = $month_2before_total + $w_total;
                }
                $month_12before_total = $month_12before_total + $w_total;
            }
            $month_2before_ave = $month_2before_total / 2;
            $month_3before_ave = $month_3before_total / 3;
            $month_4before_ave = $month_4before_total / 4;
            $month_5before_ave = $month_5before_total / 5;
            $month_6before_ave = $month_6before_total / 6;
            Log::debug('2か月 total ave = '.$month_2before_total.' '.$month_2before_ave);
            Log::debug('3か月 total ave = '.$month_3before_total.' '.$month_3before_ave);
            Log::debug('4か月 total ave = '.$month_4before_total.' '.$month_4before_ave);
            Log::debug('5か月 total ave = '.$month_5before_total.' '.$month_5before_ave);
            Log::debug('6か月 total ave = '.$month_6before_total.' '.$month_6before_ave);
            if ($month_2before_ave > Config::get('const.C023.manthly_alert_warning_7')) {
                $manthly_alert_ave_2_chk = Config::get('const.ALERT_INFO_RESULT.NG');
                $user_alert_chk = false;
            }
            if ($month_3before_ave > Config::get('const.C023.manthly_alert_warning_7')) {
                $manthly_alert_ave_3_chk = Config::get('const.ALERT_INFO_RESULT.NG');
                $user_alert_chk = false;
            }
            if ($month_4before_ave > Config::get('const.C023.manthly_alert_warning_7')) {
                $manthly_alert_ave_4_chk = Config::get('const.ALERT_INFO_RESULT.NG');
                $user_alert_chk = false;
            }
            if ($month_5before_ave > Config::get('const.C023.manthly_alert_warning_7')) {
                $manthly_alert_ave_5_chk = Config::get('const.ALERT_INFO_RESULT.NG');
                $user_alert_chk = false;
            }
            if ($month_6before_ave > Config::get('const.C023.manthly_alert_warning_7')) {
                $manthly_alert_ave_6_chk = Config::get('const.ALERT_INFO_RESULT.NG');
                $user_alert_chk = false;
            }
            // <特別条項>720時間/年に対する警告
            if ($month_12before_total > Config::get('const.C023.manthly_alert_warning_8')) {
                $manthly_alert_warning_720_total_chk = Config::get('const.ALERT_INFO_RESULT.NG');
                $user_alert_chk = false;
            }
            Log::debug('user_name = '.$monthly_alert->user_name);
            Log::debug('manthly_alert_warning_1_chk = '.$manthly_alert_warning_1_chk);
            Log::debug('manthly_alert_warning_2_chk = '.$manthly_alert_warning_2_chk);
            Log::debug('manthly_alert_warning_3_chk = '.$manthly_alert_warning_3_chk);
            Log::debug('manthly_alert_warning_12_chk = '.$manthly_alert_warning_12_chk);
            Log::debug('month_alert_45_total_cnt_chk = '.$month_alert_45_total_cnt_chk);
            Log::debug('manthly_alert_warning_100_total_chk = '.$manthly_alert_warning_100_total_chk);
            Log::debug('manthly_alert_ave_2_chk = '.$manthly_alert_ave_2_chk);
            Log::debug('manthly_alert_ave_3_chk = '.$manthly_alert_ave_3_chk);
            Log::debug('manthly_alert_ave_4_chk = '.$manthly_alert_ave_4_chk);
            Log::debug('manthly_alert_ave_5_chk = '.$manthly_alert_ave_5_chk);
            Log::debug('manthly_alert_ave_6_chk = '.$manthly_alert_ave_6_chk);
            Log::debug('manthly_alert_warning_720_total_chk = '.$manthly_alert_warning_720_total_chk);
            $working_time_values[] = array(
                'department_name' => $monthly_alert->department_name,
                'employment_status_name' => $monthly_alert->employment_status_name,
                'user_name' => $monthly_alert->user_name,
                'total_working_times_1' => $monthly_alert->total_working_times_1,
                'total_working_times_2' => $monthly_alert->total_working_times_2,
                'total_working_times_3' => $monthly_alert->total_working_times_3,
                'total_working_times_4' => $monthly_alert->total_working_times_4,
                'total_working_times_5' => $monthly_alert->total_working_times_5,
                'total_working_times_6' => $monthly_alert->total_working_times_6,
                'total_working_times_7' => $monthly_alert->total_working_times_7,
                'total_working_times_8' => $monthly_alert->total_working_times_8,
                'total_working_times_9' => $monthly_alert->total_working_times_9,
                'total_working_times_10' => $monthly_alert->total_working_times_10,
                'total_working_times_11' => $monthly_alert->total_working_times_11,
                'total_working_times_12' => $monthly_alert->total_working_times_12
            );
            $working_warning_items[] = array(
                'department_name' => '　',
                'employment_status_name' => '　',
                'user_name' => '　',
                'total_working_times_1' => Config::get('const.ALERT_MONTHLY_ITEM.items_1'),
                'total_working_times_2' => Config::get('const.ALERT_MONTHLY_ITEM.items_2'),
                'total_working_times_3' => Config::get('const.ALERT_MONTHLY_ITEM.items_3'),
                'total_working_times_4' => Config::get('const.ALERT_MONTHLY_ITEM.items_4'),
                'total_working_times_5' => Config::get('const.ALERT_MONTHLY_ITEM.items_5'),
                'total_working_times_6' => Config::get('const.ALERT_MONTHLY_ITEM.items_6'),
                'total_working_times_7' => Config::get('const.ALERT_MONTHLY_ITEM.items_7'),
                'total_working_times_8' => Config::get('const.ALERT_MONTHLY_ITEM.items_8'),
                'total_working_times_9' => Config::get('const.ALERT_MONTHLY_ITEM.items_9'),
                'total_working_times_10' => Config::get('const.ALERT_MONTHLY_ITEM.items_10'),
                'total_working_times_11' => Config::get('const.ALERT_MONTHLY_ITEM.items_11'),
                'total_working_times_12' => Config::get('const.ALERT_MONTHLY_ITEM.items_12'),
                'manthly_alert_warning_1_chk' => $manthly_alert_warning_1_chk,
                'manthly_alert_warning_2_chk' => $manthly_alert_warning_2_chk,
                'manthly_alert_warning_3_chk' => $manthly_alert_warning_3_chk,
                'manthly_alert_warning_12_chk' => $manthly_alert_warning_12_chk,
                'month_alert_45_total_cnt_chk' => $month_alert_45_total_cnt_chk,
                'manthly_alert_warning_100_total_chk' => $manthly_alert_warning_100_total_chk,
                'manthly_alert_ave_2_chk' => $manthly_alert_ave_2_chk,
                'manthly_alert_ave_3_chk' => $manthly_alert_ave_3_chk,
                'manthly_alert_ave_4_chk' => $manthly_alert_ave_4_chk,
                'manthly_alert_ave_5_chk' => $manthly_alert_ave_5_chk,
                'manthly_alert_ave_6_chk' => $manthly_alert_ave_6_chk,
                'manthly_alert_warning_720_total_chk' => $manthly_alert_warning_720_total_chk,
                'user_alert_chk' => $user_alert_chk
            );
        }

        return response()->json([
            'timeitems' => $working_time_items,
            'timevalues' => $working_time_values,
            'warningitems' => $working_warning_items,
            Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata
            ]);
    }

    /**
     * 月次アラート項目特定 
     *
     * @return void
     */
    public function getTotalItemData($monthly_alert, $itemIndex){
        if ($itemIndex == 0) {
            return $monthly_alert->total_working_times_1;
        } elseif ($itemIndex == 1) {
            return $monthly_alert->total_working_times_2;
        } elseif ($itemIndex == 2) {
            return $monthly_alert->total_working_times_3;
        } elseif ($itemIndex == 3) {
            return $monthly_alert->total_working_times_4;
        } elseif ($itemIndex == 4) {
            return $monthly_alert->total_working_times_5;
        } elseif ($itemIndex == 5) {
            return $monthly_alert->total_working_times_6;
        } elseif ($itemIndex == 6) {
            return $monthly_alert->total_working_times_7;
        } elseif ($itemIndex == 7) {
            return $monthly_alert->total_working_times_8;
        } elseif ($itemIndex == 8) {
            return $monthly_alert->total_working_times_9;
        } elseif ($itemIndex == 9) {
            return $monthly_alert->total_working_times_10;
        } elseif ($itemIndex == 10) {
            return $monthly_alert->total_working_times_11;
        } elseif ($itemIndex == 11) {
            return $monthly_alert->total_working_times_12;
        }
    }

}
