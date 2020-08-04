<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTempWorkingTimeDates03131519 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('temp_working_time_dates', function (Blueprint $table) {
            $table->boolean('is_editor')->nullable()->after('check_interval')->default(0)->comment('編集フラグ　true:勤怠編集');
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
            $table->dropColumn('is_editor');
        });
    }
}
