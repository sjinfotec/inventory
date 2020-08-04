<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;


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
        try {
            DB::table($this->table)->insert(
                [
                    'shift_start_time' => $this->shift_start_time,
                    'shift_end_time' => $this->shift_end_time,
                    'created_at'=>$this->systemdate
                ]
            );
        }catch(\PDOException $pe){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_insert_error')).'$pe');
            Log::error($pe->getMessage());
            throw $pe;
        }catch(\Exception $e){
            Log::error('class = '.__CLASS__.' method = '.__FUNCTION__.' '.str_replace('{0}', $this->table, Config::get('const.LOG_MSG.data_insert_error')).'$e');
            Log::error($e->getMessage());
            throw $e;
        }
    }
}
