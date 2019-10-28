<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToDemand1814 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('approvals', function (Blueprint $table) {
            $table->index(['no','seq','log_no'],'no_seq_log_no_index');
        });
        Schema::table('demand_details', function (Blueprint $table) {
            $table->index(['no','log_no','row_no'],'no_seq_log_no_row_no_index');
        });
        Schema::table('demands', function (Blueprint $table) {
            $table->index(['no','log_no'],'no_log_no_index');
            $table->index(['doc_code','demand_now'],'doc_code_demand_now_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('approvals', function (Blueprint $table) {
            $table->dropIndex('no_seq_log_no_index');
        });
        Schema::table('demand_details', function (Blueprint $table) {
            $table->dropIndex('no_seq_log_no_row_no_index');
        });
        Schema::table('demands', function (Blueprint $table) {
            $table->dropIndex('no_log_no_index');
            $table->dropIndex('doc_code_demand_now_index');
        });
    }
}
