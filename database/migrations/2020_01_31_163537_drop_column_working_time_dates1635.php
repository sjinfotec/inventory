<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnWorkingTimeDates1635 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('working_time_dates', function (Blueprint $table) {
            $table->dropColumn('attendance_positions_1');
            $table->dropColumn('attendance_positions_2');
            $table->dropColumn('attendance_positions_3');
            $table->dropColumn('attendance_positions_4');
            $table->dropColumn('attendance_positions_5');
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
