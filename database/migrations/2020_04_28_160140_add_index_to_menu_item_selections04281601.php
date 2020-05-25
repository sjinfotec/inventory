<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToMenuItemSelections04281601 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menu_item_selections', function (Blueprint $table) {
            $table->index(['account_id', 'item_code'],'account_item_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu_item_selections', function (Blueprint $table) {
            $table->dropIndex('account_item_index');
        });
    }
}
