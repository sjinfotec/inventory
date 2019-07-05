<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnMstCalendarTable1745 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('mst_calendar', function (Blueprint $table) {
            //カラム追加
            $table->unsignedInteger('leave_kubun')->after('weekday_kubun')->comment('休暇区分');
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
        Schema::table('mst_calendar', function (Blueprint $table) {
            //カラム削除
            $table->dropColumn('leave_kubun');
        });
    }
}
