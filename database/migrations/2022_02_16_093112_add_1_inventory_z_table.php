<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Add1InventoryZTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_z', function (Blueprint $table) {
            $table->String('charge',20)->nullable()->comment('担当');
            $table->String('order_no',30)->nullable()->comment('受注番号');
            $table->String('company_name',40)->nullable()->comment('会社名');
            $table->integer('company_id')->nullable()->comment('会社ID');
            $table->String('product_name',100)->nullable()->comment('商品名');
            $table->integer('product_id')->nullable()->comment('商品ID');
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
            $table->dropColumn('charge');
            $table->dropColumn('order_no');
            $table->dropColumn('company_name');
            $table->dropColumn('company_id');
            $table->dropColumn('product_name');
            $table->dropColumn('product_id');
        });
    }
}
