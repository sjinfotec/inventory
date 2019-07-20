<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimeTableNoToShiftInformationsTable1709 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shift_informations', function (Blueprint $table) {
            $table->unsignedInteger('working_timetable_no')->after('id')->nullable()->comment('タイムテーブルNo');
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
            $table->dropColumn('working_timetable_no');
        });
    }
}
