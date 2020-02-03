<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnWorkingTimeDates1616 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('working_time_dates', function (Blueprint $table) {
            $table->geometry('attendance_positions_5')->nullable()->after('attendance_time_5')->comment('位置情報');
            $table->geometry('attendance_positions_4')->nullable()->after('attendance_time_5')->comment('位置情報');
            $table->geometry('attendance_positions_3')->nullable()->after('attendance_time_5')->comment('位置情報');
            $table->geometry('attendance_positions_2')->nullable()->after('attendance_time_5')->comment('位置情報');
            $table->geometry('attendance_positions_1')->nullable()->after('attendance_time_5')->comment('位置情報');
            $table->geometry('leaving_time_positions_5')->nullable()->after('leaving_time_5')->comment('位置情報');
            $table->geometry('leaving_time_positions_4')->nullable()->after('leaving_time_5')->comment('位置情報');
            $table->geometry('leaving_time_positions_3')->nullable()->after('leaving_time_5')->comment('位置情報');
            $table->geometry('leaving_time_positions_2')->nullable()->after('leaving_time_5')->comment('位置情報');
            $table->geometry('leaving_time_positions_1')->nullable()->after('leaving_time_5')->comment('位置情報');
            $table->geometry('missing_middle_time_positions_5')->nullable()->after('missing_middle_time_5')->comment('位置情報');
            $table->geometry('missing_middle_time_positions_4')->nullable()->after('missing_middle_time_5')->comment('位置情報');
            $table->geometry('missing_middle_time_positions_3')->nullable()->after('missing_middle_time_5')->comment('位置情報');
            $table->geometry('missing_middle_time_positions_2')->nullable()->after('missing_middle_time_5')->comment('位置情報');
            $table->geometry('missing_middle_time_positions_1')->nullable()->after('missing_middle_time_5')->comment('位置情報');
            $table->geometry('missing_middle_return_time_positions_5')->nullable()->after('missing_middle_return_time_5')->comment('位置情報');
            $table->geometry('missing_middle_return_time_positions_4')->nullable()->after('missing_middle_return_time_5')->comment('位置情報');
            $table->geometry('missing_middle_return_time_positions_3')->nullable()->after('missing_middle_return_time_5')->comment('位置情報');
            $table->geometry('missing_middle_return_time_positions_2')->nullable()->after('missing_middle_return_time_5')->comment('位置情報');
            $table->geometry('missing_middle_return_time_positions_1')->nullable()->after('missing_middle_return_time_5')->comment('位置情報');
            $table->geometry('public_going_out_time_positions_5')->nullable()->after('public_going_out_time_5')->comment('位置情報');
            $table->geometry('public_going_out_time_positions_4')->nullable()->after('public_going_out_time_5')->comment('位置情報');
            $table->geometry('public_going_out_time_positions_3')->nullable()->after('public_going_out_time_5')->comment('位置情報');
            $table->geometry('public_going_out_time_positions_2')->nullable()->after('public_going_out_time_5')->comment('位置情報');
            $table->geometry('public_going_out_time_positions_1')->nullable()->after('public_going_out_time_5')->comment('位置情報');
            $table->geometry('public_going_out_return_time_positions_5')->nullable()->after('public_going_out_return_time_5')->comment('位置情報');
            $table->geometry('public_going_out_return_time_positions_4')->nullable()->after('public_going_out_return_time_5')->comment('位置情報');
            $table->geometry('public_going_out_return_time_positions_3')->nullable()->after('public_going_out_return_time_5')->comment('位置情報');
            $table->geometry('public_going_out_return_time_positions_2')->nullable()->after('public_going_out_return_time_5')->comment('位置情報');
            $table->geometry('public_going_out_return_time_positions_1')->nullable()->after('public_going_out_return_time_5')->comment('位置情報');
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
