<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToWorkingTimeDateTable1023 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('working_time_date', function (Blueprint $table) {
            //
            $table->dropIndex('working_time_date_index');
            $table->index(['user_code','working_date','working_time_style'],'working_time_date_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('working_time_date', function (Blueprint $table) {
            //
        });
    }
}
