<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToMstWorkingTime1833 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_working_time', function (Blueprint $table) {
            //
            $table->index(['working_time_style', 'code','sort_seq'],'mst_working_time_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mst_working_time', function (Blueprint $table) {
            //
        });
    }
}
