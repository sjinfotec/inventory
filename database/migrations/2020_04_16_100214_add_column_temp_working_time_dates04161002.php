<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTempWorkingTimeDates04161002 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('temp_working_time_dates', function (Blueprint $table) {
            $table->double('out_of_legal_working_holiday_night_overtime_hours', 6, 2)->nullable()->after('out_of_legal_working_holiday_hours')->comment('法定外休日深夜残業時間');
            $table->double('legal_working_holiday_night_overtime_hours', 6, 2)->nullable()->after('legal_working_holiday_hours')->comment('法定休日深夜残業時間');
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
            $table->dropColumn('out_of_legal_working_holiday_night_overtime_hours');
            $table->dropColumn('legal_working_holiday_night_overtime_hours');
        });
    }
}
