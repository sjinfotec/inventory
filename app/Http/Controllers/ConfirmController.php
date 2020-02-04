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
    public function gettable(Request $request)
    {
        $this->array_messagedata = array();
        $array_confirm = array();
        $array_final_confirm = array();
        $array_confirm_all = array();
        // $array_root_title = array();
        $array_root = array();
        $result = true;
        try {
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    'confirms' => $array_confirm,
                    'final_confirms' => $array_final_confirm,
                    'root_user_confirm' => $array_root,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            $confirm_no = $params['confirm_no'];
            $user_department_code = $params['user_department_code'];
            $confirm_department_code = $params['confirm_department_code'];
            $user_code = $params['user_code'];
            $seq = $params['seq'];
            $main_sub = $params['main_sub'];
            $confirm_model = new Confirm();
            // パラメータ設定
            $confirm_model->setParamConfirmnoAttribute($confirm_no);
            $confirm_model->setParamUserepartmentcodeAttribute($user_department_code);
            $confirm_model->setParamConfirmdepartmentcodeAttribute($confirm_department_code);
            $confirm_model->setParamUsercodeAttribute($user_code);
            $confirm_model->setParamMainsubAttribute($main_sub);
            if (isset($seq)) {
                $confirm_model->setParamSeqAttribute($seq);
                if ($seq == Config::get('const.CONFIRM_SEQ.final_confirm')) {
                    $array_final_confirm = $confirm_model->selectConfirm(null);
                } else {
                    $array_confirm = $confirm_model->selectConfirm(null);
                }
            } else {
                $confirm_model->setParamSeqAttribute(Config::get('const.CONFIRM_SEQ.not_final_confirm'));
                $array_confirm = $confirm_model->selectConfirm(null);
                $confirm_model->setParamSeqAttribute(Config::get('const.CONFIRM_SEQ.final_confirm'));
                $array_final_confirm = $confirm_model->selectConfirm(null);
                // 全件読み直し（collectの複数ソートがうまくいかないため）
                $confirm_model->setParamSeqAttribute(null);
                $array_confirm_all = $confirm_model->selectConfirm(null);
            }
            if (count($array_confirm) > 0 || count($array_final_confirm) > 0) {
                // **************************************************************************************
                // *  
                // *    [array_root]
                // *        ['confirm_no' => 9, 'seqs' => arrayseqs]
                // *            [arrayseqs]
                // *                ['seq' => 9, 'seqs' => main_subs]
                // *                [main_subs]
                // *                    ['main_sub_name' => '正', 'user_name' => 'ＮＮＮ']
                // *
                // **************************************************************************************
                // $array_root_confirm_concat = $array_confirm->concat($array_final_confirm);
                // $array_root_confirm_sort = $array_root_confirm_concat->sortBy('main_sub')->sortBy('seq')->sortBy('confirm_no');
                $array_roots = array();
                $array_seqs = array();
                $array_main_subs = array();
                $brk_confirm_no = 0;
                $brk_seq = 0;
                foreach($array_confirm_all as $item) {
                    if ($brk_confirm_no == 0) {
                        $brk_confirm_no = $item->confirm_no;
                    }
                    if ($brk_seq == 0) {
                        $brk_seq = $item->seq;
                    }
                    if ($brk_confirm_no != $item->confirm_no) {
                        $array_seqs[] = array(
                            'seq' => $brk_seq,
                            'main_subs' => $array_main_subs,
                        );
                        $array_roots[] = array(
                            'confirm_no' => $brk_confirm_no,
                            'seqs' => $array_seqs,
                        );
                        $brk_confirm_no = $item->confirm_no;
                        $brk_seq = $item->seq;
                        $array_seqs = array();
                        $array_main_subs = array();
                    }
                    if ($brk_seq != $item->seq) {
                        $array_seqs[] = array(
                            'seq' => $brk_seq,
                            'main_subs' => $array_main_subs,
                        );
                        $brk_seq = $item->seq;
                        $array_main_subs = array();
                    }
                    $array_main_subs[] = array(
                        'main_sub_name' => $item->main_sub_name,
                        'user_name' => $item->user_name
                    );
                }
                if (count($array_main_subs) > 0) {
                    $array_seqs[] = array(
                        'seq' => $brk_seq,
                        'main_subs' => $array_main_subs,
                    );
                    $array_roots[] = array(
                        'confirm_no' => $brk_confirm_no,
                        'seqs' => $array_seqs,
                    );
                }
                return response()->json(
                    ['result' => $result,
                    'confirms' => $array_confirm,
                    'final_confirms' => $array_final_confirm,
                    'root_user_confirm' => $array_roots,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            } else {
                return response()->json(
                    ['result' => $result,
                    'confirms' => $array_confirm,
                    'final_confirms' => $array_final_confirm,
                    'root_user_confirm' => $array_root,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }

        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 承認ルート取得処理
     *
     * @return void
     */
    public function show(Request $request)
    {
        $this->array_messagedata = array();
        $array_confirm = array();
        $array_final_confirm = array();
        $result = true;
        try {
            // パラメータチェック
            $params = array();
            if (!isset($request->keyparams)) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "keyparams", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    'confirms' => $array_confirm,
                    'final_confirms' => $array_final_confirm,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $params = $request->keyparams;
            $departmentcode = $params['departmentcode'];
            $confirm_model = new Confirm();
                // パラメータ設定
            $confirm_model->setParamDepartmentcodeAttribute($departmentcode);
            $confirm_model->setParamSeqAttribute(Config::get('const.CONFIRM_SEQ.not_final_confirm'));
            $array_confirm = $confirm_model->selectConfirm();
            $confirm_model->setParamSeqAttribute(Config::get('const.CONFIRM_SEQ.final_confirm'));
            $array_final_confirm = $confirm_model->selectConfirm();

            if (count($array_confirm) > 0 || count($array_final_confirm) > 0) {
                return response()->json(
                    ['result' => $result,
                    'confirms' => $array_confirm,
                    'final_confirms' => $array_final_confirm,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            } else {
                $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_INFO.no_confirm_data'));
                return response()->json(
                    ['result' => $result,
                    'confirms' => $array_confirm,
                    'final_confirms' => $array_final_confirm,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }

        }catch(\PDOException $pe){
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.Config::get('const.LOG_MSG.unknown_error'));
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 承認ルート作成処理
     *
     * @return void
     */
    public function store(Request $request)
    {
        $this->array_messagedata = array();
        $array_confirm = array();
        $array_final_confirm = array();
        $confirmno = 0;
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
            if (!isset($params['confirmno'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "confirmno", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $confirmno = $params['confirmno'];

            if (!isset($params['confirms'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "confirms", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $array_confirm = $params['confirms'];

            if (!isset($params['final_confirms'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "final_confirms", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $array_final_confirm = $params['final_confirms'];
            $apicommon = new ApiCommonController();
            $store_result = $this->insertConfirm($confirmno, $array_confirm, $array_final_confirm);

            Log::debug('---- 承認ルート作成処理 終了 -------------- ');
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
     * 承認ルートINSERT処理
     *
     * @return void
     */
    private function insertConfirm($confirmno, $array_confirm, $array_final_confirm)
    {
        $insert_result = true;
        $confirm_model = new Confirm();
        $setconfirmno = 0;
        DB::beginTransaction();
        try {
            if ($confirmno == 0) {
                $result = $confirm_model->getMaxNo();
                foreach($result as $item) {
                    $setconfirmno = $item->MAXNO + 1;
                    break;
                }
            } else {
                // delete処理
                // パラメータ設定
                $setconfirmno = $confirmno;
                $confirm_model->setParamConfirmnoAttribute($setconfirmno);
                $confirm_model->setParamSeqAttribute(null);
                $confirm_model->deleteConfirm();
            }
            // insert処理
            // パラメータ設定 承認者
            $systemdate = Carbon::now();
            $user = Auth::user();
            $auth_user_code = $user->code;
            $seq = 0;
            $confirm_model->setConfirmnoAttribute($setconfirmno);
            foreach($array_confirm as $result) {
                Log::debug('array_confirm main_sub'.$result['main_sub']);

                $confirm_model->setMainsubAttribute($result['main_sub']);
                $confirm_model->setConfirmDepartmentcodeAttribute($result['confirm_department_code']);
                $confirm_model->setUserdepartmentcodeAttribute($result['user_department_code']);
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
                if ($confirmno == 0) {
                    $confirm_model->setCreateduserAttribute($auth_user_code);
                    $confirm_model->setCreatedatAttribute($systemdate);
                    $confirm_model->setUpdateduserAttribute(null);
                    $confirm_model->setUpdatedatAttribute(null);
                } else {
                    $confirm_model->setCreateduserAttribute($result['created_user']);
                    $confirm_model->setCreatedatAttribute($result['created_at']);
                    $confirm_model->setUpdateduserAttribute($auth_user_code);
                    $confirm_model->setUpdatedatAttribute($systemdate);
                }
                $confirm_model->insertConfirm();
            }

            // パラメータ設定 最終承認者
            $seq = Config::get('const.CONFIRM_SEQ.final_confirm');
            $confirm_model->setConfirmnoAttribute($setconfirmno);
            foreach($array_final_confirm as $result) {
                Log::debug('array_final_confirm main_sub'.$result['main_sub']);
                $confirm_model->setSeqAttribute($seq);
                $confirm_model->setMainsubAttribute($result['main_sub']);
                $confirm_model->setConfirmDepartmentcodeAttribute($result['confirm_department_code']);
                $confirm_model->setUserdepartmentcodeAttribute($result['user_department_code']);
                $confirm_model->setUsercodeAttribute($result['user_code']);
                if ($confirmno == 0) {
                    $confirm_model->setCreateduserAttribute($auth_user_code);
                    $confirm_model->setCreatedatAttribute($systemdate);
                    $confirm_model->setUpdateduserAttribute(null);
                    $confirm_model->setUpdatedatAttribute(null);
                } else {
                    $confirm_model->setCreateduserAttribute($result['created_user']);
                    $confirm_model->setCreatedatAttribute($result['created_at']);
                    $confirm_model->setUpdateduserAttribute($auth_user_code);
                    $confirm_model->setUpdatedatAttribute($systemdate);
                }
                $confirm_model->insertConfirm();
            }
            DB::commit();
        }catch(\PDOException $pe){
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_accesee_eror'));
            DB::rollBack();
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            $this->array_messagedata[] = array( Config::get('const.RESPONCE_ITEM.message') => Config::get('const.MSG_ERROR.data_accesee_eror'));
            DB::rollBack();
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * 承認ルート削除処理
     *
     * @return void
     */
    public function del(Request $request)
    {
        $this->array_messagedata = array();
        $confirmno = 0;
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
            if (!isset($params['confirmno'])) {
                Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', "confirmno", Config::get('const.LOG_MSG.parameter_illegal')));
                $this->array_messagedata[] = Config::get('const.MSG_ERROR.parameter_illegal');
                return response()->json(
                    ['result' => false,
                    Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                );
            }
            $confirmno = $params['confirmno'];
            $confirm_model = new Confirm();
            // パラメータ設定
            $confirm_model->setParamConfirmnoAttribute($confirmno);
            $confirm_model->deleteConfirm();

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
}
