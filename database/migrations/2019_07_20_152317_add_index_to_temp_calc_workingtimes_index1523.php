<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToTempCalcWorkingtimesIndex1523 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('temp_calc_workingtimes', function (Blueprint $table) {
            //
            $table->index(['working_date','employment_status', 'department_id', 'user_code'],'temp_calc_workingtimes_date_status_department_user_index');
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
