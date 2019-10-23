<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use App\Http\Controllers\ApiCommonController;
use App\Demand;

class DemandController extends Controller
{
    private $array_messagedata = array();

    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        return view('demand');
    }

    /**
     * 申請処理
     *
     * @return void
     */
    public function makeDemand(Request $request)
    {
        return view('demand');
    }

    /**
     * 申請一覧取得処理
     *
     * @return void
     */
    public function listDemand(Request $request)
    {
        Log::debug('---- 申請一覧取得処理 開始 -------------- ');
        Log::debug('---- パラメータ　$request->doc_code = '.$request->doccode);
        Log::debug('---- パラメータ　$request->usercode = '.$request->usercode);

        $apicommon = new ApiCommonController();
        // reqestクエリーセット
        $doc_code = $apicommon->setRequestQeury($request->doccode);
        $usercode = $apicommon->setRequestQeury($request->usercode);
        if ($usercode == null || $usercode == "") {
            $usercode = Auth::user()->code;
        }

        $list_result = false;
        $demand_model = new Demand();
        $array_demand = array();
        try {
            // パラメータ設定
            $demand_model->setParamDoccodeAttribute($doc_code);
            $demand_model->setParamUsercodeAttribute($usercode);
            $demand_model->setParamLimitAttribute(10);
            // 適用期間日付の取得
            $dt = new Carbon();
            $target_date = $dt->format('Ymd');
            $array_demand = $demand_model->getDemandList($target_date);
        }catch(\PDOException $pe){
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_accesee_eror'));
            $list_result = false;
            Log::error($pe->getMessage());
        }catch(\Exception $e){
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_accesee_eror'));
            $list_result = false;
            Log::error($e->getMessage());
        }
        Log::debug('---- 申請一覧取得処理 終了 -------------- ');
        Log::debug('    $list_result'.$list_result);
        Log::debug('    count $array_demand'.count($array_demand));
        Log::debug('    count $this->array_messagedata'.count($this->array_messagedata));
        return response()->json([
            'list_result' => $list_result,
            'demands' => $array_demand,
            Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata
            ]);
    }

}
