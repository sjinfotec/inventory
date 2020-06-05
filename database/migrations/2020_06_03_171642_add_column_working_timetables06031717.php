<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnWorkingTimetables06031717 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('working_timetables', function (Blueprint $table) {
            $table->unsignedInteger('ago_time_no')->nullable()->after('to_time')->comment('出勤時刻前の場合のリンクNO');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('working_timetables', function (Blueprint $table) {
            $table->dropColumn('ago_time_no');
        });
    }
}
