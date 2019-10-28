<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropIndexDemand1813 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('approvals', function (Blueprint $table) {
            $table->dropIndex('no_log_no_index');
            $table->dropIndex('doc_code_no_log_no_index');
        });
        Schema::table('demand_details', function (Blueprint $table) {
            $table->dropIndex('no_doc_code_log_no_row_no_index');
        });
        Schema::table('demands', function (Blueprint $table) {
            $table->dropIndex('no_log_no_index');
            $table->dropIndex('doc_code_no_log_no_index');
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
