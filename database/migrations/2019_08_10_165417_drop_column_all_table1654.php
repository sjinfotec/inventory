<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnAllTable1654 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 各テーブルのdepartment_id削除
        Schema::table('card_informations', function (Blueprint $table) {
            $table->dropColumn('department_id');
        });
        Schema::table('daily_updated_informations', function (Blueprint $table) {
            $table->dropColumn('department_id');
        });
        Schema::table('shift_informations', function (Blueprint $table) {
            $table->dropColumn('department_id');
        });
        Schema::table('temp_calc_workingtimes', function (Blueprint $table) {
            $table->dropColumn('department_id');
        });
        Schema::table('temp_working_time_dates', function (Blueprint $table) {
            $table->dropColumn('department_id');
        });
        Schema::table('user_holiday_kubuns', function (Blueprint $table) {
            $table->dropColumn('department_id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('department_id');
        });
        Schema::table('work_times', function (Blueprint $table) {
            $table->dropColumn('department_id');
        });
        Schema::table('working_time_dates', function (Blueprint $table) {
            $table->dropColumn('department_id');
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
