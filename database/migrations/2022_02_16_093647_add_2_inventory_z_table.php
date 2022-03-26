<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Add2InventoryZTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_z', function (Blueprint $table) {
            $table->String('unit',10)->nullable()->comment('単位');
            $table->integer('quantity')->nullable()->comment('入数');
            $table->date('supply_day')->nullable()->comment('納入日');
            $table->integer('supply_quantity')->nullable()->comment('納入数');
            $table->date('order_day')->nullable()->comment('発注日');
            $table->integer('order_quantity')->nullable()->comment('発注数');
            $table->integer('now_inventory')->nullable()->comment('現在在庫');
            $table->String('nbox',16)->nullable()->comment('箱数');
            $table->String('order_address',100)->nullable()->comment('発注先');
            $table->integer('unit_price')->nullable()->comment('単価');
            $table->integer('total')->nullable()->comment('合計');
            $table->String('remarks')->nullable()->comment('備考');
            $table->String('note')->nullable()->comment('メモ/ノート');
            $table->String('status',20)->nullable()->comment('ステータス--最新/履歴');
            $table->String('order_info',20)->nullable()->comment('発注情報--預かり/在庫');
            $table->String('other1',10)->nullable()->comment('その他マーク');
            $table->String('created_user',20)->nullable()->comment('作成ユーザー');
            $table->String('updated_user',20)->nullable()->comment('修正ユーザー');
            $table->timestamps();
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
            $table->dropColumn('unit');
            $table->dropColumn('quantity');
            $table->dropColumn('supply_day');
            $table->dropColumn('supply_quantity');
            $table->dropColumn('order_day');
            $table->dropColumn('order_quantity');
            $table->dropColumn('now_inventory');
            $table->dropColumn('nbox');
            $table->dropColumn('order_address');
            $table->dropColumn('unit_price');
            $table->dropColumn('total');
            $table->dropColumn('remarks');
            $table->dropColumn('note');
            $table->dropColumn('status');
            $table->dropColumn('order_info');
            $table->dropColumn('other1');
            $table->dropColumn('created_user');
            $table->dropColumn('updated_user');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
}
