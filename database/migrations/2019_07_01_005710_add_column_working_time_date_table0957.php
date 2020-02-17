<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnWorkingTimeDateTable0957 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('working_time_date', function (Blueprint $table) {
            //カラム追加
            $table->char('user_code',10)->after('id')->comment('ユーザーコード');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('working_time_date', function (Blueprint $table) {
            //カラム削除    char型に変更するためいったんdrop
            $table->dropColumn('user_code');
        });
    }
}
