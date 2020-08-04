<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToCsvItemSelections04271114 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('csv_item_selections', function (Blueprint $table) {
            $table->index(['account_id', 'selection_code', 'item_code', 'item_seq'],'account_selection_item_seq_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('csv_item_selections', function (Blueprint $table) {
            $table->dropIndex('account_selection_item_seq_index');
        });
    }
}
