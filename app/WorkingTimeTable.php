<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class WorkingTimeTable extends Model
{
    protected $table = 'working_timetables';
    protected $table_users = 'users';
    protected $table_generalcodes = 'generalcodes';
    // protected $guarded = array('id');

    private $id;
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

    public function getIdAttribute()
    {
        return $this->id;
    }

    public function setIdAttribute($value)
    {
        $this->id = $value;
    }

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

    /**
     * セレクト用データ取得
     *
     * @return void
     */
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
            ->where($this->table.'.is_deleted', '0')
            ->groupBy($this->table.'.no',$this->table.'.name')
            ->get();

        return $data;
    }

    /**
     * 登録(INSERT)
     *
     * @return void
     */
    public function insert(){
        DB::table($this->table)->insert(
            [
                'no' => $this->no,
                'name' => $this->name,
                'working_time_kubun' => $this->working_time_kubun,
                'from_time' => $this->from_time,
                'to_time' => $this->to_time,
                'created_user' => $this->created_user,
                'created_at' => $this->created_at,
            ]
        );
    }

    /**
     * 登録(UPDATE)
     *
     * @return void
     */
    public function updateDetail(){
        DB::table($this->table)
            ->where($this->table.'.id', $this->id)
            ->where($this->table.'.is_deleted', 0)
            ->update(
                [
                    'name' => $this->name,
                    'working_time_kubun' => $this->working_time_kubun,
                    'from_time' => $this->from_time,
                    'to_time' => $this->to_time,
                    'updated_user' => $this->updated_user,
                    'updated_at' => $this->updated_at,
                ]
            );
    }

    /**
     * 詳細取得
     *
     * @return void
     */
    public function getDetail(){
        $data = DB::table($this->table)
            ->select(
                $this->table.'.id',
                $this->table.'.no',
                $this->table.'.name',
                $this->table.'.working_time_kubun',
                $this->table.'.from_time',
                $this->table.'.to_time',
                $this->table.'.created_user',
                $this->table.'.updated_user'
            )
            ->where($this->table.'.no', $this->no)
            ->where($this->table.'.is_deleted', 0)
            ->get();

        return $data;
    }

    /**
     * 論理削除
     *
     * @return void
     */
    public function updateIsDelete(){
        DB::table($this->table)
            ->where('no', $this->no)
            ->update(['is_deleted' => 1]);
    }

}
