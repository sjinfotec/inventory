<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnTempCalcWorkingtimes1007 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('temp_calc_workingtimes', function (Blueprint $table) {
            $table->dropColumn('working_interval');
        });
        Schema::table('temp_calc_workingtimes', function (Blueprint $table) {
            $table->unsignedInteger('check_max_times')->after('pattern')->nullable()->comment('打刻回数最大チェック結果');
        });
        Schema::table('temp_calc_workingtimes', function (Blueprint $table) {
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
        Schema::table('temp_calc_workingtimes', function (Blueprint $table) {
            $table->dropColumn('check_max_times');
        });
        Schema::table('temp_calc_workingtimes', function (Blueprint $table) {
            $table->dropColumn('check_result');
        });
    }
}
