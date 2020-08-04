<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToAllTable1733 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('card_informations', function (Blueprint $table) {
            $table->index(['user_code','department_code'],'card_informations_user_code_department_code_index');
        });
        Schema::table('companies', function (Blueprint $table) {
            $table->index(['apply_term_from'],'companies_apply_term_from_index');
        });
        Schema::table('daily_updated_informations', function (Blueprint $table) {
            $table->index(['user_code','department_code'],'daily_updated_informations_user_code_department_code_index');
        });
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('code');
        });
        Schema::table('departments', function (Blueprint $table) {
            $table->char('code',8)->after('apply_term_from')->comment('部署コード');
        });
        Schema::table('departments', function (Blueprint $table) {
            $table->index(['code','apply_term_from'],'departments_user_code_apply_term_from_index');
        });
        Schema::table('shift_informations', function (Blueprint $table) {
            $table->index(['working_timetable_no','user_code','department_code'],'shift_informations_no_user_code_department_code_index');
        });
        Schema::table('temp_calc_workingtimes', function (Blueprint $table) {
            $table->index(['working_date','employment_status','user_code','department_code'],'temp_calc_workingtimes_date_status_user_department_index');
        });
        Schema::table('temp_working_time_dates', function (Blueprint $table) {
            $table->index(['working_date','employment_status','user_code','department_code'],'temp_working_time_dates_date_status_user_department_index');
        });
        Schema::table('user_holiday_kubuns', function (Blueprint $table) {
            $table->index(['working_date','user_code','department_code'],'user_holiday_kubuns_date_user_department_index');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->index(['apply_term_from','code','department_code'],'users_apply_term_code_department_code_index');
        });
        Schema::table('work_times', function (Blueprint $table) {
            $table->index(['user_code','department_code','record_time','mode'],'work_times_users_department_record_time_mode_index');
        });
        Schema::table('working_time_dates', function (Blueprint $table) {
            $table->index(['working_date','employment_status','user_code','department_code'],'working_time_dates_date_status_users_department_index');
        });
        Schema::table('working_timetables', function (Blueprint $table) {
            $table->index(['apply_term_from','no','working_time_kubun'],'working_timetables_apply_no_time_kubun_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
