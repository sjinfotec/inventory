<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToWorkingTimetablesTable1355 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('working_timetables', function (Blueprint $table) {
            $table->index(['apply_term_from','apply_term_to','no'],'working_timetables_apply_term_from_apply_term_to_no_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('working_timetables', function (Blueprint $table) {
            //
        });
    }
}
