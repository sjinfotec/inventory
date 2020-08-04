<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnTempWorkingTimeDates03170911 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('temp_working_time_dates', function (Blueprint $table) {
            $table->dropColumn('is_editor');
            $table->dropColumn('editor_department_code');
            $table->dropColumn('editor_user_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
