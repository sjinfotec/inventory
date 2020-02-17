<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnAllTable1700 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('card_informations', function (Blueprint $table) {
            $table->char('department_code',8)->after('user_code')->comment('部署コード');
        });
        Schema::table('daily_updated_informations', function (Blueprint $table) {
            $table->char('department_code',8)->after('user_code')->comment('部署コード');
        });
        Schema::table('departments', function (Blueprint $table) {
            $table->char('code',8)->after('id')->comment('部署コード');
        });
        Schema::table('shift_informations', function (Blueprint $table) {
            $table->char('department_code',8)->after('user_code')->comment('部署コード');
        });
        Schema::table('temp_calc_workingtimes', function (Blueprint $table) {
            $table->char('department_code',8)->after('user_code')->comment('部署コード');
        });
        Schema::table('temp_working_time_dates', function (Blueprint $table) {
            $table->char('department_code',8)->after('user_code')->comment('部署コード');
        });
        Schema::table('user_holiday_kubuns', function (Blueprint $table) {
            $table->char('department_code',8)->after('user_code')->comment('部署コード');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->char('department_code',8)->after('code')->comment('部署コード');
        });
        Schema::table('work_times', function (Blueprint $table) {
            $table->char('department_code',8)->after('user_code')->comment('部署コード');
        });
        Schema::table('working_time_dates', function (Blueprint $table) {
            $table->char('department_code',8)->after('user_code')->comment('部署コード');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('card_informations', function (Blueprint $table) {
            $table->dropColumn('department_code');
        });
        Schema::table('daily_updated_informations', function (Blueprint $table) {
            $table->dropColumn('department_code');
        });
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('code');
        });
        Schema::table('shift_informations', function (Blueprint $table) {
            $table->dropColumn('department_code');
        });
        Schema::table('temp_calc_workingtimes', function (Blueprint $table) {
            $table->dropColumn('department_code');
        });
        Schema::table('temp_working_time_dates', function (Blueprint $table) {
            $table->dropColumn('department_code');
        });
        Schema::table('user_holiday_kubuns', function (Blueprint $table) {
            $table->dropColumn('department_code');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('department_code');
        });
        Schema::table('work_times', function (Blueprint $table) {
            $table->dropColumn('department_code');
        });
        Schema::table('working_time_dates', function (Blueprint $table) {
            $table->dropColumn('department_code');
        });
    }
}
