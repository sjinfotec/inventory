<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropShiftStartTimeToShiftInformationsTable1724 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shift_informations', function (Blueprint $table) {
            $table->dropColumn('shift_start_time');
            $table->dropColumn('shift_end_time');
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
            //
        });
    }
}
