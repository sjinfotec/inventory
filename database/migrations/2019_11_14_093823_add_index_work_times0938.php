<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexWorkTimes0938 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_times', function (Blueprint $table) {
            $table->index(['user_code', 'department_code', 'record_time', 'is_deleted'],'user_code_department_code_record_time_deleted_index');
        });
        Schema::table('work_times', function (Blueprint $table) {
            $table->index(['record_time', 'is_deleted'],'record_time_is_deleted_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_times', function (Blueprint $table) {
            $table->dropIndex('user_code_department_code_record_time_deleted_index');
        });
        Schema::table('work_times', function (Blueprint $table) {
            $table->dropIndex('record_time_is_deleted_index');
        });
    }
}
