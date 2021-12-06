<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToSetupsHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('setups_histories', function (Blueprint $table) {
            $table->unique(['order_no', 'seq', 'progress_no', 'setup_history_no'],'order_no_seq_progress_no_history_no_index');
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('setups_histories', function (Blueprint $table) {
            $table->dropIndex('order_no_seq_progress_no_history_no_index');
            //
        });
    }
}
