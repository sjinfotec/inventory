<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnWorkingTimeDatesTable1449 extends Migration
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
            $table->datetime('attendance_time_5')->after('working_timetable_name')->nullable()->comment('出勤時刻５');
            $table->datetime('attendance_time_4')->after('working_timetable_name')->nullable()->comment('出勤時刻４');
            $table->datetime('attendance_time_3')->after('working_timetable_name')->nullable()->comment('出勤時刻３');
            $table->datetime('attendance_time_2')->after('working_timetable_name')->nullable()->comment('出勤時刻２');
            $table->datetime('attendance_time_1')->after('working_timetable_name')->nullable()->comment('出勤時刻１');
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
