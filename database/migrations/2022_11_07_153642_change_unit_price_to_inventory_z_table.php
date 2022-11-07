<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUnitPriceToInventoryZTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_z', function (Blueprint $table) {
            $table->decimal('unit_price', $precision = 10, $scale = 2)->change();
            $table->decimal('total', $precision = 12, $scale = 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory_z', function (Blueprint $table) {
            //
        });
    }
}
