<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropIndexAllTable1719 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('card_informations', function (Blueprint $table) {
            $table->dropUnique('users_code_unique');
        });
        Schema::table('companies', function (Blueprint $table) {
            $table->dropIndex('companies_apply_term_from_apply_term_to_index');
        });
        Schema::table('daily_updated_informations', function (Blueprint $table) {
            $table->dropIndex('daily_updated_informations_department_id_user_code_index');
        });
        Schema::table('departments', function (Blueprint $table) {
            $table->dropIndex('departments_apply_term_from_apply_term_to_index');
        });
        Schema::table('shift_informations', function (Blueprint $table) {
            $table->dropIndex('shift_informations_no_user_code_department_id_index');
        });
        Schema::table('temp_calc_workingtimes', function (Blueprint $table) {
            $table->dropIndex('temp_calc_workingtimes_date_status_department_user_index');
        });
        Schema::table('temp_working_time_dates', function (Blueprint $table) {
            $table->dropIndex('temp_working_time_dates_date_status_department_user_code_index');
        });
        Schema::table('user_holiday_kubuns', function (Blueprint $table) {
            $table->dropIndex('user_holiday_kubuns_date_department_user_code_index');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_apply_term_from_apply_term_to_code_index');
        });
        Schema::table('work_times', function (Blueprint $table) {
            $table->dropIndex('work_times_index');
            $table->dropIndex('work_times_user_code_department_code_index');
        });
        Schema::table('working_time_dates', function (Blueprint $table) {
            $table->dropIndex('working_time_dates_date_status_department_id_user_code_index');
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
