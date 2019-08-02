<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Http\Requests\StoreDepartmentPost;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CreateDepartmentController extends Controller
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
        return view('create_department');
    }

    /**
     * 部署名取得
     *
     * @param Request $request
     * @return void
     */
    public function getDetails(Request $request){
        $id = $request->id;
        $details = DB::table('departments')->where('id', $id)->where('is_deleted', 0)->orderby('id','asc')->get();
        return $details;
    }

    /**
     * 部署登録
     *
     * @param Request $request
     * @return void
     */
    public function store(StoreDepartmentPost $request){
        $name = $request->name;
        if(isset($request->id)){    // UPDATE
            $id = $request->id;
            $result = $this->updateName($id,$name);
        }else{                      // INSERT
             $result = $this->insert($name);
        }
       
        if($result){
        }else{
            return false;
        }
    }

    /**
     * INSERT
     *
     * @param [type] $name
     * @return void
     */
    private function insert($name){
        $systemdate = Carbon::now();
        DB::beginTransaction();
        try{
           
            DB::table('departments')->insert(
                [
                    'name' => $name,
                    'created_at'=>$systemdate
                ]
            );
            DB::commit();
            return true;

        }catch(\PDOException $e){
            Log::error($e->getMessage());
            DB::rollBack();
            return false;
        }
    }

    /**
     * 論理削除
     *
     * @param Request $request
     * @return void
     */
    public function del(Request $request){
        $id = $request->id;
        $response = collect();
        $result = $this->updateIsDelete($id);
        if($result){
            $response->put('result',self::SUCCESS);
        }else{
            $response->put('result',self::FAILED);
        }
        return $response;
    }

    /**
     * 上書き(Is_deleted)
     *
     * @param [type] $id
     * @return void
     */
    public function updateIsDelete($id){
        DB::beginTransaction();
        try{
            DB::table('departments')
            ->where('id', $id)
            ->update(['is_deleted' => 1]);
            DB::commit();
            return true;

        }catch(\PDOException $e){
            DB::rollBack();
            return false;
        }
    }

    /**
     * 上書き(name)
     *
     * @param [type] $id
     * @param [type] $name
     * @return void
     */
    public function updateName($id,$name){
        $systemdate = Carbon::now();
        DB::beginTransaction();
        try{
            DB::table('departments')
            ->where('id', $id)
            ->where('is_deleted', 0)
            ->update(['name' => $name,'updated_at' => $systemdate]);
            DB::commit();
            return true;

        }catch(\PDOException $e){
            DB::rollBack();
            return false;
        }
    }
}
