<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnWorkingTimeDates1009 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('working_time_dates', function (Blueprint $table) {
            $table->dropColumn('working_interval');
        });
        Schema::table('working_time_dates', function (Blueprint $table) {
            $table->unsignedInteger('check_max_times')->after('pattern')->nullable()->comment('打刻回数最大チェック結果');
        });
        Schema::table('working_time_dates', function (Blueprint $table) {
            $table->unsignedInteger('check_result')->after('pattern')->nullable()->comment('打刻チェック結果');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('working_time_dates', function (Blueprint $table) {
            $table->dropColumn('check_max_times');
        });
        Schema::table('working_time_dates', function (Blueprint $table) {
            $table->dropColumn('check_result');
        });
    }
}
