<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CardInformation extends Model
{
    protected $table = 'card_informations';
    protected $guarded = array('id');


    public function selectCycles(){

        $informations = DB::table('card_informations')
            ->select('card_informations.year',
                    'card_informations.user_id',
                    'card_informations.superior_user_id'
            )
            ->get();

        return $informations;
    }

}
