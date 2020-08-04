<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToWorkTimeLogs1018 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_time_logs', function (Blueprint $table) {
            $table->index(['user_code', 'department_code', 'record_time'],'user_code_department_code_record_time_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_time_logs', function (Blueprint $table) {
            $table->dropIndex('user_code_department_code_record_time_index');
        });
    }
}
