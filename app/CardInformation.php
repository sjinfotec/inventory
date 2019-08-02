<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CardInformation extends Model
{
    protected $table = 'card_informations';
    protected $guarded = array('id');

    private $user_code;
    private $department_id;
    private $card_idm;
    private $created_user;
    private $updated_user;
    private $systemdate;

    public function getUserCodeAttribute()
    {
        return $this->user_code;
    }

    public function setUserCodeAttribute($value)
    {
        $this->user_code = $value;
    }

    public function getDepartmentIdAttribute()
    {
        return $this->department_id;
    }

    public function setDepartmentIdAttribute($value)
    {
        $this->department_id = $value;
    }

    public function getCardIdmAttribute()
    {
        return $this->card_idm;
    }

    public function setCardIdmAttribute($value)
    {
        $this->card_idm = $value;
    }

    public function getCreatedUserAttribute()
    {
        return $this->created_user;
    }

    public function setCreatedUserAttribute($value)
    {
        $this->created_user = $value;
    }

    public function getUpdatedUserAttribute()
    {
        return $this->updated_user;
    }

    public function setUpdatedUserAttribute($value)
    {
        $this->updated_user = $value;
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
     * カード情報を持たないユーザーを取得
     *
     * @param [type] $card_idm
     * @return boolean
     */
    public function isCardInfoExists(){
        $data = DB::table('card_informations')
            ->join('users', function ($join) {
                $join->on('users.code', '=', 'card_informations.user_code');
                $join->on('users.department_id', '=', 'card_informations.department_id');
            })
            ->where('card_informations.card_idm',$this->card_idm)
            ->where('users.is_deleted',0)
            ->where('card_informations.is_deleted',0)
            ->exists();

        return $data;
    }

    public function insertCardInfo(){
        DB::table('card_informations')->insert(
            [
                'user_code' => $this->user_code,
                'department_id' => $this->department_id,
                'card_idm' => $this->card_idm,
                'created_user'=>$this->created_user,
                'created_at'=>$this->systemdate
            ]
        );
    }

}
