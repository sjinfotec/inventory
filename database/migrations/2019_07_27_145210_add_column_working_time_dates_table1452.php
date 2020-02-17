<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnWorkingTimeDatesTable1452 extends Migration
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
            $table->datetime('leaving_time_5')->after('attendance_time_5')->nullable()->comment('退勤時刻５');
            $table->datetime('leaving_time_4')->after('attendance_time_5')->nullable()->comment('退勤時刻４');
            $table->datetime('leaving_time_3')->after('attendance_time_5')->nullable()->comment('退勤時刻３');
            $table->datetime('leaving_time_2')->after('attendance_time_5')->nullable()->comment('退勤時刻２');
            $table->datetime('leaving_time_1')->after('attendance_time_5')->nullable()->comment('退勤時刻１');
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
