<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Add2InventoryATable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // テーブル更新
        Schema::table('inventory_a', function (Blueprint $table) {
            $table->String('product_name',100)->nullable()->comment('商品名');
            // AutoIncrement(PrimaryKey)
            //$table->increments('product_id')->nullable()->comment('商品ID');
            $table->integer('product_id')->nullable()->comment('商品ID');
            // 追加
            $table->String('unit',10)->nullable()->comment('単位');
            $table->integer('quantity')->nullable()->comment('入数');
            $table->date('receipt_day')->nullable()->comment('入庫日');
            $table->integer('order_quantity')->nullable()->comment('発注数');
            $table->integer('receipt')->nullable()->comment('入庫数');
            $table->date('delivery_day')->nullable()->comment('出庫日');
            $table->integer('delivery')->nullable()->comment('出庫数');
            $table->integer('now_inventory')->nullable()->comment('現在在庫');
            $table->String('nbox',16)->nullable()->comment('箱数');
            $table->String('dnum',40)->nullable()->comment('出庫No');
            $table->String('rnum',40)->nullable()->comment('残りNo');
            $table->String('shipping_address',100)->nullable()->comment('発送先');
            $table->String('remarks')->nullable()->comment('備考');
            $table->String('status')->nullable()->comment('ステータス--最新/履歴');
            $table->String('order_info',20)->nullable()->comment('発注情報--預かり/在庫');
            $table->String('other1',10)->nullable()->comment('その他マーク');
            $table->String('created_user',20)->nullable()->comment('作成ユーザー');
            $table->String('updated_user',20)->nullable()->comment('修正ユーザー');
            $table->timestamps();
            // product_id列のPrimaryKey制約を削除
            //$table->dropPrimary();
            // id列にPrimaryKey制約を再設定
            //$table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory_a', function (Blueprint $table) {
            $table->dropColumn('product_name');
            $table->dropColumn('product_id');
            $table->dropColumn('unit');
            $table->dropColumn('quantity');
            $table->dropColumn('receipt_day');
            $table->dropColumn('place_order');
            $table->dropColumn('receipt');
            $table->dropColumn('delivery_day');
            $table->dropColumn('delivery');
            $table->dropColumn('now_inventory');
            $table->dropColumn('nbox');
            $table->dropColumn('dnum');
            $table->dropColumn('rnum');
            $table->dropColumn('shipping_address');
            $table->dropColumn('remarks');
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
