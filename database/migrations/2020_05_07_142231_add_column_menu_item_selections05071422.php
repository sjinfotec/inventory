<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnMenuItemSelections05071422 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menu_item_selections', function (Blueprint $table) {
            $table->decimal('item_code', 3, 0)->after('selection_code')->comment('項目コード 1から（内部コード）');
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
            $table->dropColumn('item_code');
        });
    }
}
