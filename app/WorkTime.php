<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WorkTime extends Model
{
    protected $table = 'work_times';
    protected $guarded = array('id');

    private $mode;
    private $user_id;
    private $systemdate;


    public function insertWorkTime(){
        DB::table('work_times')->insert(
            [
                'user_id' => $user_id,
                'product_id' => $product_id,
                'created_at'=>$systemdate
            ]
        );
    }

}
