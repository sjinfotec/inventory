<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTempWorkingTimeDates1111 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('working_time_dates', function (Blueprint $table) {
            $table->unsignedInteger('check_interval')->after('check_max_times')->nullable()->comment('インターバルチェック結果');
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
            $table->dropColumn('check_interval');
        });
    }
}
