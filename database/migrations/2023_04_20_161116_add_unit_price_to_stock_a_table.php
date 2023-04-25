<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUnitPriceToStockATable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_a', function (Blueprint $table) {
            $table->string('remarks', 200)->nullable()->after('stock_nbox')->comment('備考');
            $table->decimal('total', $precision = 12, $scale = 2)->nullable()->after('stock_nbox')->comment('合計');
            $table->decimal('unit_price', $precision = 10, $scale = 2)->nullable()->after('stock_nbox')->comment('単価');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_a', function (Blueprint $table) {
            $table->dropColumn('remarks');
            $table->dropColumn('total');
            $table->dropColumn('unit_price');
        });
    }
}
