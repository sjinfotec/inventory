<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Http\Requests\StoreTimeTablePost;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\WorkingTimeTable;

class CreateTimeTableController extends Controller
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
        $no = $request->no;
        $name = $request->name;
        $data[0]['working_time_kubun'] = 1;
        $data[0]['from_time'] = $request->regularFrom;
        $data[0]['to_time'] = $request->regularTo;
        $data[1]['working_time_kubun'] = 2;
        $data[1]['from_time'] = $request->regularRestFrom1;
        $data[1]['to_time'] = $request->regularRestTo1;
        $data[2]['working_time_kubun'] = 2;
        $data[2]['from_time'] = $request->regularRestFrom2;
        $data[2]['to_time'] = $request->regularRestTo2;
        $data[3]['working_time_kubun'] = 3;
        $data[3]['from_time'] = $request->irregularFrom1;
        $data[3]['to_time'] = $request->irregularTo1;
        $data[4]['working_time_kubun'] = 3;
        $data[4]['from_time'] = $request->irregularFrom2;
        $data[4]['to_time'] = $request->irregularTo2;
        $data[5]['working_time_kubun'] = 3;
        $data[5]['from_time'] = $request->irregularFrom3;
        $data[5]['to_time'] = $request->irregularTo3;
        $data[6]['working_time_kubun'] = 4;
        $data[6]['from_time'] = $request->irregularMidNightFrom;
        $data[6]['to_time'] = $request->irregularMidNightTo;
        if(isset($request->id)){    // UPDATE
            $validatedData = $request->validate([
                'name' => 'required|string|max:191'
            ],[
                'name.required'  => 'タイムテーブル名称を入力してください',
                'name.max'  => 'タイムテーブル名称の最大文字数は 191 です',
            ]);
            $id = $request->id;
            $result = $this->update($data,$id,$name);
        }else{                      // INSERT
            $validatedData = $request->validate([
                'no' => 'required|unique:working_timetables|max:10',
                'name' => 'required|string|max:191'
            ],[
                'no.required'  => 'No を入力してください',
                'no.unique'  => 'No は既に使用済です',
                'no.max'  => 'No の最大文字数は 10 です',
                'name.required'  => 'タイムテーブル名称を入力してください',
                'name.max'  => 'タイムテーブル名称の最大文字数は 191 です',
            ]);
            $result = $this->insert($data,$no,$name);
        }
        if($result){
        }else{
            return false;
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
    private function insert($data,$no,$name){
        $systemdate = Carbon::now();
        $time_table = new WorkingTimeTable();
        $user = Auth::user();
        $user_code = $user->code;
        DB::beginTransaction();
        try{
            $time_table->setNoAttribute($no);
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
            return true;

        }catch(\PDOException $e){
            DB::rollBack();
            return false;
        }
    }

    /**
     * UPDATE
     *
     * @param [type] $data
     * @param [type] $no
     * @param [type] $name
     * @return void
     */
    private function update($data,$id,$name){
        $systemdate = Carbon::now();
        $time_table = new WorkingTimeTable();
        $user = Auth::user();
        $user_code = $user->code;
        DB::beginTransaction();
        try{
            $time_table->setNameAttribute($name);
            $time_table->setUpdateduserAttribute($user_code);
            $time_table->setUpdatedatAttribute($systemdate);
            foreach ($data as $item) {
               $time_table->setIdAttribute($id);
               $time_table->setWorkingtimekubunAttribute($item['working_time_kubun']);
               $time_table->setFromtimeAttribute($item['from_time']);
               $time_table->setTotimeAttribute($item['to_time']);
               $time_table->updateDetail();
               $id++;
            }
            DB::commit();
            return true;

        }catch(\PDOException $e){
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
        $no = $request->no;
        $response = collect();
        $result = $this->updateIsDelete($no);
        if($result){
            $response->put('result',self::SUCCESS);
        }else{
            $response->put('result',self::FAILED);
        }
        return $response;
    }

    /**
     * 論理削除
     *
     * @param [type] $no
     * @return void
     */
    public function updateIsDelete($no){
        $time_table = new WorkingTimeTable();
        $time_table->setNoAttribute($no);
        
        DB::beginTransaction();
        try{
            $time_table->updateIsDelete();
            DB::commit();
            return true;

        }catch(\PDOException $e){
            DB::rollBack();
            return false;
        }
    }

    /**
     * 詳細取得
     *
     * @param Request $request
     * @return void
     */
    public function getDetail(Request $request){
        $carbon = new Carbon();
        $no = $request->no;
        $time_table = new WorkingTimeTable();
        $time_table->setNoAttribute($no);
        $result = $time_table->getDetail();
        foreach ($result as $item) {
            if(isset($item->from_time)){
                $from_time = new Carbon($item->from_time);
                $item->from_time = $from_time->format('H:i');
            }
            if(isset($item->to_time)){
                $to_time = new Carbon($item->to_time);
                $item->to_time = $to_time->format('H:i');
            }
        }
        return $result;
    }

}
