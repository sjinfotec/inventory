<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CardInformation extends Model
{
    protected $table = 'card_informations';
    protected $guarded = array('id');

    private $user_code;
    private $card_idm;
    private $systemdate;

    public function getUserCodeAttribute()
    {
        return $this->user_code;
    }

    public function setUserCodeAttribute($value)
    {
        $this->user_code = $value;
    }

    public function getCardIdmAttribute()
    {
        return $this->card_idm;
    }

    public function setCardIdmAttribute($value)
    {
        $this->card_idm = $value;
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
    public function isCardInfoExists($card_idm){
        $data = DB::table('card_informations')
            ->join('users','users.code','=','card_informations.user_code')
            ->where('card_informations.card_idm',$card_idm)
            ->where('users.is_deleted',0)
            ->exists();

        return $data;
    }

    public function insertCardInfo(){
        DB::table('card_informations')->insert(
            [
                'user_code' => $this->user_code,
                'card_idm' => $this->card_idm,
                'created_at'=>$this->systemdate
            ]
        );
    }

}
