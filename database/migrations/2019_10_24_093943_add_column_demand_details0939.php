<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnDemandDetails0939 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('demand_details', function (Blueprint $table) {
            $table->double('scheduled_time',5,2)->nullable()->after('time_to')->comment('予定時間');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('demand_details', function (Blueprint $table) {
            $table->dropColumn('scheduled_time');
        });
    }
}
