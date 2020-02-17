<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnWorkingTimeDates16365 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('working_time_dates', function (Blueprint $table) {
            $table->geometry('attendance_time_positions_5')->nullable()->after('attendance_time_5')->comment('位置情報');
            $table->geometry('attendance_time_positions_4')->nullable()->after('attendance_time_5')->comment('位置情報');
            $table->geometry('attendance_time_positions_3')->nullable()->after('attendance_time_5')->comment('位置情報');
            $table->geometry('attendance_time_positions_2')->nullable()->after('attendance_time_5')->comment('位置情報');
            $table->geometry('attendance_time_positions_1')->nullable()->after('attendance_time_5')->comment('位置情報');
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
