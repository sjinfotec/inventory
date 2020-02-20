<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnAttendanceLogs1037 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->string('difference_reason',255)->nullable()->after('event_time')->comment('差異理由');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropColumn('difference_reason');
        });
    }
}
