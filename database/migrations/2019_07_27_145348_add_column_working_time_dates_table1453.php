<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnWorkingTimeDatesTable1453 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('working_time_dates', function (Blueprint $table) {
            //カラム追加
            $table->datetime('missing_middle_time_5')->after('leaving_time_5')->nullable()->comment('中抜時刻５');
            $table->datetime('missing_middle_time_4')->after('leaving_time_5')->nullable()->comment('中抜時刻４');
            $table->datetime('missing_middle_time_3')->after('leaving_time_5')->nullable()->comment('中抜時刻３');
            $table->datetime('missing_middle_time_2')->after('leaving_time_5')->nullable()->comment('中抜時刻２');
            $table->datetime('missing_middle_time_1')->after('leaving_time_5')->nullable()->comment('中抜時刻１');
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
    }
}
