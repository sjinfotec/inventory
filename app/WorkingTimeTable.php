<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class WorkingTimeTable extends Model
{
    protected $table = 'working_timetables';
    protected $table_users = 'users';
    protected $table_generalcodes = 'generalcodes';
    protected $guarded = array('id');

    private $no;                  
    private $name;                  
    private $working_time_kubun;                  
    private $from_time;                  
    private $to_time;                  
    private $created_user;                  
    private $updated_user;                  
    private $created_at;                  
    private $updated_at;                  
    private $is_deleted;                  

     
    public function getNoAttribute()
    {
        return $this->no;
    }

    public function setNoAttribute($value)
    {
        $this->no = $value;
    }
     
    public function getNameAttribute()
    {
        return $this->name;
    }

    public function setNameAttribute($value)
    {
        $this->name = $value;
    }
     
    public function getWorkingtimekubunAttribute()
    {
        return $this->working_time_kubun;
    }

    public function setWorkingtimekubunAttribute($value)
    {
        $this->working_time_kubun = $value;
    }
     
    public function getFromtimeAttribute()
    {
        return $this->from_time;
    }

    public function setFromtimeAttribute($value)
    {
        $this->from_time = $value;
    }
     
    public function getTotimeAttribute()
    {
        return $this->to_time;
    }

    public function setTotimeAttribute($value)
    {
        $this->to_time = $value;
    }
     
    public function getCreateduserAttribute()
    {
        return $this->created_user;
    }

    public function setCreateduserAttribute($value)
    {
        $this->created_user = $value;
    }
     
    public function getUpdateduserAttribute()
    {
        return $this->updated_user;
    }

    public function setUpdateduserAttribute($value)
    {
        $this->updated_user = $value;
    }
     
    public function getCreatedatAttribute()
    {
        return $this->created_at;
    }

    public function setCreatedatAttribute($value)
    {
        $this->created_at = $value;
    }
     
    public function getUpdatedatAttribute()
    {
        return $this->updated_at;
    }

    public function setUpdatedatAttribute($value)
    {
        $this->updated_at = $value;
    }
     
    public function getIsdeletedAttribute()
    {
        return $this->is_deleted;
    }

    public function setIsdeletedAttribute($value)
    {
        $this->is_deleted = $value;
    }

    public function getTimeTables(){
        $code_name = DB::table($this->table_generalcodes)
            ->where($this->table_generalcodes.'.identification_id', 'C999')
            ->where($this->table_generalcodes.'.is_deleted', '0')
            ->where($this->table_generalcodes.'.code', '1')
            ->value('code_name');

        $data = DB::table($this->table)
            ->select(
                $this->table.'.no',
                $this->table.'.name'
            )
            ->whereNotIn($this->table.'.no', [$code_name])
            ->groupBy($this->table.'.no',$this->table.'.name')
            ->get();

        return $data;
    }

}
