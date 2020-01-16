<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnWorkingTimeDates1428 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('working_time_dates', function (Blueprint $table) {
            $table->double('late_night_working_hours',6,2)->nullable()->after('late_night_overtime_hours')->comment('深夜労働時間（深夜残業時間帯 - 深夜残業時間）');
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
            $table->dropColumn('late_night_working_hours');
        });
    }
}
