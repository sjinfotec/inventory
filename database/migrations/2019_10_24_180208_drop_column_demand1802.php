<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnDemand1802 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('approvals', function (Blueprint $table) {
            $table->dropColumn('no');
        });
        Schema::table('demand_details', function (Blueprint $table) {
            $table->dropColumn('no');
        });
        Schema::table('demands', function (Blueprint $table) {
            $table->dropColumn('no');
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
