<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTempCalcWorkingtimes03170903 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('temp_calc_workingtimes', function (Blueprint $table) {
            $table->bigInteger('work_times_id')->nullable()->after('positions')->comment('打刻時刻テーブルID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('temp_calc_workingtimes', function (Blueprint $table) {
            $table->dropColumn('work_times_id');
        });
    }
}
