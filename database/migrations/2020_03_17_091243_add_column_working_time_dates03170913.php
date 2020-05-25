<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnWorkingTimeDates03170913 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('working_time_dates', function (Blueprint $table) {
            $table->bigInteger('attendance_time_id_5')->nullable()->after('attendance_time_positions_5')->comment('出勤打刻時刻テーブルID5');
            $table->bigInteger('attendance_time_id_4')->nullable()->after('attendance_time_positions_5')->comment('出勤打刻時刻テーブルID4');
            $table->bigInteger('attendance_time_id_3')->nullable()->after('attendance_time_positions_5')->comment('出勤打刻時刻テーブルID3');
            $table->bigInteger('attendance_time_id_2')->nullable()->after('attendance_time_positions_5')->comment('出勤打刻時刻テーブルID2');
            $table->bigInteger('attendance_time_id_1')->nullable()->after('attendance_time_positions_5')->comment('出勤打刻時刻テーブルID1');
            $table->bigInteger('leaving_time_id_5')->nullable()->after('leaving_time_positions_5')->comment('退勤打刻時刻テーブルID5');
            $table->bigInteger('leaving_time_id_4')->nullable()->after('leaving_time_positions_5')->comment('退勤打刻時刻テーブルID4');
            $table->bigInteger('leaving_time_id_3')->nullable()->after('leaving_time_positions_5')->comment('退勤打刻時刻テーブルID3');
            $table->bigInteger('leaving_time_id_2')->nullable()->after('leaving_time_positions_5')->comment('退勤打刻時刻テーブルID2');
            $table->bigInteger('leaving_time_id_1')->nullable()->after('leaving_time_positions_5')->comment('退勤打刻時刻テーブルID1');
            $table->bigInteger('missing_middle_time_id_5')->nullable()->after('missing_middle_time_positions_5')->comment('私用外出打刻時刻テーブルID5');
            $table->bigInteger('missing_middle_time_id_4')->nullable()->after('missing_middle_time_positions_5')->comment('私用外出打刻時刻テーブルID4');
            $table->bigInteger('missing_middle_time_id_3')->nullable()->after('missing_middle_time_positions_5')->comment('私用外出打刻時刻テーブルID3');
            $table->bigInteger('missing_middle_time_id_2')->nullable()->after('missing_middle_time_positions_5')->comment('私用外出打刻時刻テーブルID2');
            $table->bigInteger('missing_middle_time_id_1')->nullable()->after('missing_middle_time_positions_5')->comment('私用外出打刻時刻テーブルID1');
            $table->bigInteger('missing_middle_return_time_id_5')->nullable()->after('missing_middle_return_time_positions_5')->comment('私用外出戻り打刻時刻テーブルID5');
            $table->bigInteger('missing_middle_return_time_id_4')->nullable()->after('missing_middle_return_time_positions_5')->comment('私用外出戻り打刻時刻テーブルID4');
            $table->bigInteger('missing_middle_return_time_id_3')->nullable()->after('missing_middle_return_time_positions_5')->comment('私用外出戻り打刻時刻テーブルID3');
            $table->bigInteger('missing_middle_return_time_id_2')->nullable()->after('missing_middle_return_time_positions_5')->comment('私用外出戻り打刻時刻テーブルID2');
            $table->bigInteger('missing_middle_return_time_id_1')->nullable()->after('missing_middle_return_time_positions_5')->comment('私用外出戻り打刻時刻テーブルID1');
            $table->bigInteger('public_going_out_time_id_5')->nullable()->after('public_going_out_time_positions_5')->comment('公用外出打刻時刻テーブルID5');
            $table->bigInteger('public_going_out_time_id_4')->nullable()->after('public_going_out_time_positions_5')->comment('公用外出打刻時刻テーブルID4');
            $table->bigInteger('public_going_out_time_id_3')->nullable()->after('public_going_out_time_positions_5')->comment('公用外出打刻時刻テーブルID3');
            $table->bigInteger('public_going_out_time_id_2')->nullable()->after('public_going_out_time_positions_5')->comment('公用外出打刻時刻テーブルID2');
            $table->bigInteger('public_going_out_time_id_1')->nullable()->after('public_going_out_time_positions_5')->comment('公用外出打刻時刻テーブルID1');
            $table->bigInteger('public_going_out_return_time_id_5')->nullable()->after('public_going_out_return_time_positions_5')->comment('公用外出戻り打刻時刻テーブルID5');
            $table->bigInteger('public_going_out_return_time_id_4')->nullable()->after('public_going_out_return_time_positions_5')->comment('公用外出戻り打刻時刻テーブルID4');
            $table->bigInteger('public_going_out_return_time_id_3')->nullable()->after('public_going_out_return_time_positions_5')->comment('公用外出戻り打刻時刻テーブルID3');
            $table->bigInteger('public_going_out_return_time_id_2')->nullable()->after('public_going_out_return_time_positions_5')->comment('公用外出戻り打刻時刻テーブルID2');
            $table->bigInteger('public_going_out_return_time_id_1')->nullable()->after('public_going_out_return_time_positions_5')->comment('公用外出戻り打刻時刻テーブルID1');
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
