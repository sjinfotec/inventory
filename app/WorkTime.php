<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WorkTime extends Model
{
    protected $table = 'work_times';
    protected $guarded = array('id');

    private $mode;
    private $user_code;
    private $systemdate;

    public function getUserCodeAttribute()
    {
        return $this->user_code;
    }

    public function setUserCodeAttribute($value)
    {
        $this->user_code = $value;
    }

    public function getModeAttribute()
    {
        return $this->mode;
    }

    public function setModeAttribute($value)
    {
        $this->mode = $value;
    }

    public function getSystemDateAttribute()
    {
        return $this->systemdate;
    }

    public function setSystemDateAttribute($value)
    {
        $this->systemdate = $value;
    }

    /**
     * 勤怠登録
     *
     * @return void
     */
    public function insertWorkTime(){
        DB::table('work_times')->insert(
            [
                'user_code' => $this->user_code,
                'record_time' => $this->systemdate,
                'mode' => $this->mode,
                'created_at'=>$this->systemdate
            ]
        );
    }

}
