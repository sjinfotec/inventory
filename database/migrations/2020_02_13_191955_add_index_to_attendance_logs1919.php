<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToAttendanceLogs1919 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->index(['department_code', 'user_code'],'department_code_user_code_index');
            $table->index(['user_code', 'working_date'],'user_code_working_date_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropIndex('department_code_user_code_index');
            $table->dropIndex('user_code_working_date_index');
        });
    }
}
