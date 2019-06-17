<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserCodeToCardInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('card_informations', function (Blueprint $table) {
            $table->char('user_code', 8)->unique()->after('id')->comment('ユーザーコード');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('card_informations', function (Blueprint $table) {
             $table->dropColumn('user_code');
        });
    }
}
