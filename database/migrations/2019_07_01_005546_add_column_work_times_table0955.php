<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnWorkTimesTable0955 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('work_times', function (Blueprint $table) {
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
        Schema::table('work_times', function (Blueprint $table) {
            //カラム削除    char型に変更するためいったんdrop
            $table->dropColumn('user_code');
        });
    }
}
