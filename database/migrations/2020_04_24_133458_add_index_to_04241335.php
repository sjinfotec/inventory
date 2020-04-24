<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexTo04241335 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shift_informations', function (Blueprint $table) {
            $table->index(['working_timetable_no', 'department_code', 'user_code', 'is_deleted'],'timetable_no_department_user_deleted_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shift_informations', function (Blueprint $table) {
            $table->dropIndex('timetable_no_department_user_deleted_index');
        });
    }
}
