<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToDemandDetails0906 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('demand_details', function (Blueprint $table) {
            $table->index(['no', 'doc_code', 'log_no', 'row_no'],'no_doc_code_log_no_row_no_index');
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
            //
        });
    }
}
