<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToFeatureItemSelections05211515 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('feature_item_selections', function (Blueprint $table) {
            $table->index(['account_id', 'selection_code', 'item_code', 'item_seq'],'account_selection_code_seq_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('feature_item_selections', function (Blueprint $table) {
            $table->dropIndex('account_selection_code_seq_index');
        });
    }
}
