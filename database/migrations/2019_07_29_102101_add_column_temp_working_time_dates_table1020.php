<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTempWorkingTimeDatesTable1020 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('temp_working_time_dates', function (Blueprint $table) {
            //カラム追加
            $table->datetime('missing_middle_return_time_5')->after('working_timetable_name')->nullable()->comment('戻り時刻５');
            $table->datetime('missing_middle_return_time_4')->after('working_timetable_name')->nullable()->comment('戻り時刻４');
            $table->datetime('missing_middle_return_time_3')->after('working_timetable_name')->nullable()->comment('戻り時刻３');
            $table->datetime('missing_middle_return_time_2')->after('working_timetable_name')->nullable()->comment('戻り時刻２');
            $table->datetime('missing_middle_return_time_1')->after('working_timetable_name')->nullable()->comment('戻り時刻１');
            $table->datetime('missing_middle_time_5')->after('working_timetable_name')->nullable()->comment('中抜時刻５');
            $table->datetime('missing_middle_time_4')->after('working_timetable_name')->nullable()->comment('中抜時刻４');
            $table->datetime('missing_middle_time_3')->after('working_timetable_name')->nullable()->comment('中抜時刻３');
            $table->datetime('missing_middle_time_2')->after('working_timetable_name')->nullable()->comment('中抜時刻２');
            $table->datetime('missing_middle_time_1')->after('working_timetable_name')->nullable()->comment('中抜時刻１');
            $table->datetime('leaving_time_5')->after('working_timetable_name')->nullable()->comment('退勤時刻５');
            $table->datetime('leaving_time_4')->after('working_timetable_name')->nullable()->comment('退勤時刻４');
            $table->datetime('leaving_time_3')->after('working_timetable_name')->nullable()->comment('退勤時刻３');
            $table->datetime('leaving_time_2')->after('working_timetable_name')->nullable()->comment('退勤時刻２');
            $table->datetime('leaving_time_1')->after('working_timetable_name')->nullable()->comment('退勤時刻１');
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
