<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameWorkingTimeStyleNameToEmploymentStatusNameOnTempWorkingTimeDates1017 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('temp_working_time_dates', function (Blueprint $table) {
            //
            $table->renameColumn('working_time_style_name', 'employment_status_name');
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
            $table->renameColumn('employment_status_name', 'working_time_style_name');
        });
    }
}
