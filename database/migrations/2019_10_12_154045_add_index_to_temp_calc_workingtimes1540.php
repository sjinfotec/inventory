<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToTempCalcWorkingtimes1540 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('temp_calc_workingtimes', function (Blueprint $table) {
            $table->index(['working_date','employment_status','department_code','user_code','seq'],'date_emp_department_user_seq_index');
        });
        Schema::table('temp_working_time_dates', function (Blueprint $table) {
            $table->index(['working_date','employment_status','department_code','user_code','seq'],'date_emp_department_user_seq_index');
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
            $table->dropIndex('date_emp_department_user_seq_index');
        });
        Schema::table('temp_working_time_dates', function (Blueprint $table) {
            $table->dropIndex('date_emp_department_user_seq_index');
        });
    }
}
