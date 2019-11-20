<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnWorkingTimeDates1545 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('working_time_dates', function (Blueprint $table) {
            $table->double('legal_working_holiday_hours', 6, 2)->after('missing_middle_hours')->nullable()->comment('法定休日労働時間');
            $table->double('out_of_legal_working_holiday_hours', 6, 2)->after('missing_middle_hours')->nullable()->comment('法定外休日労働時間');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('working_time_dates', function (Blueprint $table) {
            $table->dropColumn('legal_working_holiday_hours');
            $table->dropColumn('out_of_legal_working_holiday_hours');
        });
    }
}
