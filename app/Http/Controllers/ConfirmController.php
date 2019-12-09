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
use App\Confirm;

class ConfirmController extends Controller
{
    private $array_messagedata = array();

    /**
     * 初期処理
     *
     * @return void
     */
    public function index()
    {
        return view('confirm');
    }

    /**
     * 承認ルート取得処理
     *
     * @return void
     */
    public function show(Request $request)
    {
        Log::debug('---- 承認ルート取得処理 開始 -------------- ');
        Log::debug('---- パラメータ　$request->departmentcode = '.$request->departmentcode);

        $apicommon = new ApiCommonController();
        // reqestクエリーセット
        $departmentcode = $apicommon->setRequestQeury($request->departmentcode);

        $show_result = false;
        $confirm_model = new Confirm();
        $array_confirm = array();
        $array_final_confirm = array();
        try {
            // パラメータ設定
            $confirm_model->setParamDepartmentcodeAttribute($departmentcode);
            $confirm_model->setParamSeqAttribute(Config::get('const.CONFIRM_SEQ.not_final_confirm'));
            $array_confirm = $confirm_model->selectConfirm();
            $confirm_model->setParamSeqAttribute(Config::get('const.CONFIRM_SEQ.final_confirm'));
            $array_final_confirm = $confirm_model->selectConfirm();

            if (count($array_confirm) > 0 || count($array_final_confirm) > 0) {
                $show_result = true;
            } else {
                $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_INFO.no_confirm_data'));
                $show_result = true;
            }
        }catch(\PDOException $pe){
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_accesee_eror'));
            $show_result = false;
            Log::error($pe->getMessage());
        }catch(\Exception $e){
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_accesee_eror'));
            $show_result = false;
            Log::error($e->getMessage());
        }
        Log::debug('---- 承認ルート取得処理 終了 -------------- ');
        Log::debug('    $show_result'.$show_result);
        Log::debug('    count $confirms'.count($array_confirm));
        Log::debug('    count $final_confirms'.count($array_final_confirm));
        Log::debug('    count $this->array_messagedata'.count($this->array_messagedata));
        return response()->json([
            'show_result' => $show_result,
            'confirms' => $array_confirm,
            'final_confirms' => $array_final_confirm,
            Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata
            ]);
    }

    /**
     * 承認ルート作成処理
     *
     * @return void
     */
    public function store(Request $request)
    {
        Log::debug('---- 承認ルート作成処理 開始 -------------- ');
        Log::debug('---- パラメータ　$request->departmentcode = '.$request->departmentcode);

        $apicommon = new ApiCommonController();
        // reqestクエリーセット
        $departmentcode = $apicommon->setRequestQeury($request->departmentcode);
        $array_confirm = array();
        $array_final_confirm = array();
        $array_confirm = $request->confirms;
        $array_final_confirm = $request->final_confirms;

        $store_result = $this->insertConfirm($departmentcode, $array_confirm, $array_final_confirm);
        Log::debug('---- 承認ルート作成処理 終了 -------------- ');
        Log::debug('    $store_result'.$store_result);
        Log::debug('    count $this->array_messagedata'.count($this->array_messagedata));
        return response()->json([
            'store_result' => $store_result,
            Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata
            ]);
    }

    /**
     * 承認ルートINSERT処理
     *
     * @return void
     */
    private function insertConfirm($departmentcode, $array_confirm, $array_final_confirm)
    {
        $insert_result = true;
        $confirm_model = new Confirm();
        // delete&insert処理
        DB::beginTransaction();
        try {
            // delete処理
            // パラメータ設定
            $confirm_model->setParamDepartmentcodeAttribute($departmentcode);
            $confirm_model->setParamSeqAttribute(null);
            $confirm_model->deleteConfirm();
            // insert処理
            // パラメータ設定 承認者
            $confirm_model->setParamDepartmentcodeAttribute($departmentcode);
            $confirm_model->setParamSeqAttribute(Config::get('const.CONFIRM_SEQ.not_final_confirm'));
            $confirm_model->setDepartmentcodeAttribute($departmentcode);
            $systemdate = Carbon::now();
            $user = Auth::user();
            $auth_user_code = $user->code;
            $seq = 0;
            foreach($array_confirm as $result) {
                $confirm_model->setConfirmDepartmentcodeAttribute($result['confirm_department_code']);
                $confirm_model->setUsercodeAttribute($result['user_code']);
                if ($result['main_sub'] == Config::get('const.C027.main')) {
                    $seq++;
                } else {
                    if ($seq == 0) {
                        $seq++;
                    }
                }
                if ($seq == 99) { $seq = 98; }
                $confirm_model->setSeqAttribute($seq);
                $confirm_model->setMainsubAttribute($result['main_sub']);
                $confirm_model->setCreateduserAttribute($auth_user_code);
                $confirm_model->setCreatedatAttribute($systemdate);
                $confirm_model->setUpdateduserAttribute(null);
                $confirm_model->setUpdatedatAttribute(null);
                $confirm_model->insertConfirm();
            }

            // パラメータ設定 最終承認者
            $confirm_model->setParamSeqAttribute(Config::get('const.CONFIRM_SEQ.final_confirm'));
            $seq = Config::get('const.CONFIRM_SEQ.final_confirm');
            foreach($array_final_confirm as $result) {
                $confirm_model->setConfirmDepartmentcodeAttribute($result['confirm_department_code']);
                $confirm_model->setUsercodeAttribute($result['user_code']);
                $confirm_model->setSeqAttribute($seq);
                $confirm_model->setMainsubAttribute($result['main_sub']);
                $confirm_model->setCreateduserAttribute($auth_user_code);
                $confirm_model->setCreatedatAttribute($systemdate);
                $confirm_model->setUpdateduserAttribute(null);
                $confirm_model->setUpdatedatAttribute(null);
                $confirm_model->insertConfirm();
            }
            DB::commit();
        }catch(\PDOException $pe){
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_accesee_eror'));
            DB::rollBack();
            Log::error($pe->getMessage());
            $insert_result = false;
        }catch(\Exception $e){
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_accesee_eror'));
            DB::rollBack();
            Log::error($e->getMessage());
            $insert_result = false;
        }

        return $insert_result;
    }
}
