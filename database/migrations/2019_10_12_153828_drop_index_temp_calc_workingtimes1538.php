<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropIndexTempCalcWorkingtimes1538 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('temp_calc_workingtimes', function (Blueprint $table) {
            $table->dropIndex('temp_calc_workingtimes_date_status_user_department_index');
        });
        Schema::table('temp_working_time_dates', function (Blueprint $table) {
            $table->dropIndex('temp_working_time_dates_date_status_user_department_index');
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
