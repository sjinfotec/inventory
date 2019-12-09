<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\StoreTimeTablePost;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\WorkingTimeTable;
use Illuminate\Support\Facades\Validator;

class CreateTimeTableController extends Controller
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
        return view('create_time_table');
    }

    /**
     * 登録
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request){
        $data = array();
        try{
            $no = $request->no;
            $kbn = $request->kbn;
            $kbn = $request->kbn;
            if ($kbn == Config::get('const.DB_KBN.store')) {
                if (isset($no)) {
                    $this->array_messagedata[] = Config::get('const.MSG_ERROR.already_data');
                    return response()->json(
                        ['result' => true, 'no' => $result,
                        Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
                    );
                }
            }
            $name = $request->name;
            $details = $request->details;
            $data[0]['apply_term_from'] = $details['apply_term_from'];
            $data[0]['working_time_kubun'] = 1;
            $data[0]['from_time'] = $details['regularFrom'];
            $data[0]['to_time'] = $details['regularTo'];
            $data[1]['working_time_kubun'] = 2;
            $data[1]['from_time'] = $details['regularRestFrom1'];
            $data[1]['to_time'] = $details['regularRestTo1'];
            $data[2]['working_time_kubun'] = 2;
            $data[2]['from_time'] = $details['regularRestFrom2'];
            $data[2]['to_time'] = $details['regularRestTo2'];
            $data[3]['working_time_kubun'] = 2;
            $data[3]['from_time'] = $details['regularRestFrom3'];
            $data[3]['to_time'] = $details['regularRestTo3'];
            $data[4]['working_time_kubun'] = 2;
            $data[4]['from_time'] = $details['regularRestFrom4'];
            $data[4]['to_time'] = $details['regularRestTo4'];
            $data[5]['working_time_kubun'] = 2;
            $data[5]['from_time'] = $details['regularRestFrom5'];
            $data[5]['to_time'] = $details['regularRestTo5'];
            $data[6]['working_time_kubun'] = 4;
            $data[6]['from_time'] = $details['irregularMidNightFrom'];
            $data[6]['to_time'] = $details['irregularMidNightTo'];
            $result = $this->insert($kbn , $data ,$no, $name);
            return response()->json(
                ['result' => true, 'no' => $result,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            return response()->json(
                ['result' => false, 'no' => '',
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\Exception $e){
            Log::error($e->getMessage());
            return response()->json(
                ['result' => false, 'no' => '',
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }
    }

    /**
     * 新規
     *
     * @param [type] $data
     * @param [type] $no
     * @param [type] $name
     * @return void
     */
    private function insert($kbn , $data, $no, $name){
        DB::beginTransaction();
        try{
            $systemdate = Carbon::now();
            $time_table = new WorkingTimeTable();
            $user = Auth::user();
            $user_code = $user->code;
            $maxno = 1;
            if ($kbn == Config::get('const.DB_KBN.store')) {
                $term_from = Config::get('const.INIT_DATE.initdate');
                $maxno = $time_table->getMaxNo();
                if (isset($maxno)) {
                    $maxno = $maxno + 1;
                } else {
                    $maxno = 1;
                }
            } else {
                $maxno = $no;
                Log::debug("data[0]['apply_term_from'] = ".$data[0]['apply_term_from']);
                if(isset($data[0]['apply_term_from'])){
                    $carbon = new Carbon($data[0]['apply_term_from']);
                    Log::debug("carbon = ".$carbon);
                    $from = $carbon->copy()->format('Ymd');
                    $term_from = $from;
                    Log::debug("term_from = ".$term_from);
                }
            }
            $time_table->setNoAttribute($maxno);
            $time_table->setApplytermfromAttribute($term_from);
            $time_table->setNameAttribute($name);
            $time_table->setCreateduserAttribute($user_code);
            $time_table->setCreatedatAttribute($systemdate);
            foreach ($data as $item) {
               $time_table->setWorkingtimekubunAttribute($item['working_time_kubun']);
               $time_table->setFromtimeAttribute($item['from_time']);
               $time_table->setTotimeAttribute($item['to_time']);
               $time_table->insert();
            }
            DB::commit();
            return $maxno;
        }catch(\PDOException $pe){
            Log::error($pe->getMessage());
            DB::rollBack();
            return -1;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
            return -2;
        }
    }

    /**
     * タイムテーブル編集
     *
     * @param Request $request
     * @return response
     */
    public function fix(Request $request){ 
        try{
            $no = $request->no;
            $details = $request->details;
            $response = collect();
            $result = $this->update($no, $details);
            return response()->json(
                ['result' => true, 'no' => $no,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            return response()->json(
                ['result' => false, 'no' => '',
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\Exception $e){
            return response()->json(
                ['result' => false, 'no' => '',
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }
    }

    /**
     * タイムテーブル追加
     *
     * @param Request $request
     * @return response
     */
    public function add(Request $request){ 
        DB::beginTransaction();
        try{
            $details = $request->all();
            $data = array();
            $no = $details['details']['no'];
            $name = $details['details']['name'];
            $data[0]['apply_term_from'] = $details['details']['apply_term_from'];
            $data[0]['working_time_kubun'] = 1;
            $data[0]['from_time'] = $details['details']['regularFrom'];
            $data[0]['to_time'] = $details['details']['regularTo'];
            $data[1]['working_time_kubun'] = 2;
            $data[1]['from_time'] = $details['details']['regularRestFrom1'];
            $data[1]['to_time'] = $details['details']['regularRestTo1'];
            $data[2]['working_time_kubun'] = 2;
            $data[2]['from_time'] = $details['details']['regularRestFrom2'];
            $data[2]['to_time'] = $details['details']['regularRestTo2'];
            $data[3]['working_time_kubun'] = 2;
            $data[3]['from_time'] = $details['details']['regularRestFrom3'];
            $data[3]['to_time'] = $details['details']['regularRestTo3'];
            $data[4]['working_time_kubun'] = 2;
            $data[4]['from_time'] = $details['details']['regularRestFrom4'];
            $data[4]['to_time'] = $details['details']['regularRestTo4'];
            $data[5]['working_time_kubun'] = 2;
            $data[5]['from_time'] = $details['details']['regularRestFrom5'];
            $data[5]['to_time'] = $details['details']['regularRestTo5'];
            $data[6]['working_time_kubun'] = 4;
            $data[6]['from_time'] = $details['details']['irregularMidNightFrom'];
            $data[6]['to_time'] = $details['details']['irregularMidNightTo'];
            $response = collect();
            $result = $this->insert($data,$no,$name);
            if($result){
                $response->put('result',self::SUCCESS);
            }else{
                $response->put('result',self::FAILED);
            }
            DB::commit();
            return $response;
        }catch(\PDOException $pe){
            Log::error($pe->getMessage());
            DB::rollBack();
            $response->put('result',self::FAILED);
            return $response;ex
        }catch(\Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
            $response->put('result',self::FAILED);
            return $response;
        }
    }

    /**
     * UPDATE
     *
     * @param [type] $details
     * @return boolean
     */
    private function update($no, $details){
        $systemdate = Carbon::now();
        $time_table = new WorkingTimeTable();
        $user = Auth::user();
        $user_code = $user->code;
        DB::beginTransaction();
        try{
            $time_table->setUpdateduserAttribute($user_code);
            $time_table->setUpdatedatAttribute($systemdate);
            $start_index = ($no - 1) * 7;
            $end_index = $start_index + 6;
            for ($i=$start_index; $i <= $end_index; $i++) {
                if($i == $start_index){
                    if(isset($details[$i]['apply_term_from'])){
                        $carbon = new Carbon($details[$i]['apply_term_from']);
                        $temp_from = $carbon->copy()->format('Ymd');
                        $apply_term_from = $temp_from;
                    }
                    if(isset($details[$i]['name'])){
                        $name = $details[$i]['name'];
                    }
                }
                $time_table->setApplytermfromAttribute($apply_term_from);
                $time_table->setIdAttribute($details[$i]['id']);
                $time_table->setNameAttribute($name);
                $time_table->setNoAttribute($no);
                $time_table->setWorkingtimekubunAttribute($details[$i]['working_time_kubun']);
                $time_table->setFromtimeAttribute($details[$i]['from_time']);
                $time_table->setTotimeAttribute($details[$i]['to_time']);
                $time_table->updateDetail();
            }
            DB::commit();
            return true;

        }catch(\PDOException $pe){
            Log::error($pe->getMessage());
            DB::rollBack();
            return false;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
            return false;
        }
    }

    /**
     * タイムテーブル削除
     *
     * @param Request $request
     * @return void
     */
    public function del(Request $request){
        try{
            $no = $request->no;
            $temp_from = $request->apply_term_from;
            $carbon = new Carbon($temp_from);
            $apply_term_from = $carbon->copy()->format('Ymd');
            
            $response = collect();
            $result = $this->updateIsDelete($no,$apply_term_from);
            if($result){
                $response->put('result',self::SUCCESS);
            }else{
                $response->put('result',self::FAILED);
            }
            return $response;
        }catch(\PDOException $pe){
            Log::error($pe->getMessage());
            DB::rollBack();
            $response->put('result',self::FAILED);
            return $response;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
            $response->put('result',self::FAILED);
            return $response;
        }
    }

    /**
     * 論理削除
     *
     * @param [type] $no
     * @return void
     */
    public function updateIsDelete($no,$apply_term_from){
        
        DB::beginTransaction();
        try{
            $time_table = new WorkingTimeTable();
            $time_table->setNoAttribute($no);
            $time_table->setApplytermfromAttribute($apply_term_from);
            $time_table->updateIsDelete();
            DB::commit();
            $response->put('result',self::SUCCESS);
            return $response;
        }catch(\PDOException $pe){
            Log::error($pe->getMessage());
            DB::rollBack();
            $response->put('result',self::FAILED);
            return $response;
        }catch(\Exception $e){
            Log::error($e->getMessage());
            DB::rollBack();
            $response->put('result',self::FAILED);
            return $response;
        }
    }

    /**
     * 詳細取得
     *
     * @param Request $request
     * @return void
     */
    public function getDetail(Request $request){
        try{
            $no = $request->no;
            $time_table = new WorkingTimeTable();
            $time_table->setNoAttribute($no);
            $result = $time_table->getDetail();
            return response()->json(
                ['result' => true, 'details' => $result,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\PDOException $pe){
            Log::error($pe->getMessage());
            return response()->json(
                ['result' => false, 'details' => null,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }catch(\Exception $e){
            Log::error($e->getMessage());
            return response()->json(
                ['result' => false, 'details' => null,
                Config::get('const.RESPONCE_ITEM.messagedata') => $this->array_messagedata]
            );
        }
    }

}
