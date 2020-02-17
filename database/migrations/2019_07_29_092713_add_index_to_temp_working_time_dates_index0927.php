<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToTempWorkingTimeDatesIndex0927 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('temp_working_time_dates', function (Blueprint $table) {
            $table->index(['working_date','employment_status','department_id','user_code'],'temp_working_time_dates_date_status_department_user_code_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('temp_working_time_dates', function (Blueprint $table) {
            //
        });
    }
}
