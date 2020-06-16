<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnWorkingTimetables06031645 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('working_timetables', function (Blueprint $table) {
            $table->decimal('ago_time',4)->nullable()->after('to_time')->comment('出勤時刻前の可能時間（単位：分）');
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
            $table->dropColumn('ago_time');
        });
    }
}
