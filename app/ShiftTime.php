<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class ShiftTime extends Model
{
    protected $table = 'shift_times';
    protected $guarded = array('id');

    private $shift_start_time;
    private $shift_end_time;
    private $systemdate;

    public function getStartTimeAttribute()
    {
        return $this->shift_start_time;
    }

    public function setStartTimeAttribute($value)
    {
        $this->shift_start_time = $value;
    }

    public function getEndTimeAttribute()
    {
        return $this->shift_end_time;
    }

    public function setEndTimeAttribute($value)
    {
        $this->shift_end_time = $value;
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
    public function insertShiftTime(){
        DB::table($this->table)->insert(
            [
                'shift_start_time' => $this->shift_start_time,
                'shift_end_time' => $this->shift_end_time,
                'created_at'=>$this->systemdate
            ]
        );
    }
}
