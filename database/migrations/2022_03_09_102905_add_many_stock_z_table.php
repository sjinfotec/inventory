<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddManyStockZTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_z', function (Blueprint $table) {
            $table->integer('inv_id')->nullable()->comment('inventoryID');
            $table->String('order_no',30)->nullable()->comment('受注番号');
            $table->String('company_name',40)->nullable()->comment('会社名');
            $table->integer('company_id')->nullable()->comment('会社ID');
            $table->String('product_name',100)->nullable()->comment('商品名');
            $table->integer('product_id')->nullable()->comment('商品ID');
            $table->String('unit',10)->nullable()->comment('単位');
            $table->integer('quantity')->nullable()->comment('入数');
            $table->integer('now_inventory')->nullable()->comment('現在在庫');
            $table->String('nbox',16)->nullable()->comment('箱数');
            $table->integer('stock_now_inventory')->nullable()->comment('棚卸在庫');
            $table->String('stock_nbox',16)->nullable()->comment('棚卸箱数');
            $table->String('status',20)->nullable()->comment('ステータス');
            $table->String('order_info',20)->nullable()->comment('発注情報');
            $table->String('stock_month',12)->nullable()->comment('棚卸月');
            $table->String('created_user',20)->nullable()->comment('作成ユーザー');
            $table->String('updated_user',20)->nullable()->comment('修正ユーザー');
            $table->timestamps();
            $table->boolean('is_deleted')->default(0)->comment('削除フラグ');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_z', function (Blueprint $table) {
            $table->dropColumn('inv_id');
            $table->dropColumn('order_no');
            $table->dropColumn('company_name');
            $table->dropColumn('company_id');
            $table->dropColumn('product_name');
            $table->dropColumn('product_id');
            $table->dropColumn('unit');
            $table->dropColumn('quantity');
            $table->dropColumn('now_inventory');
            $table->dropColumn('nbox');
            $table->dropColumn('status');
            $table->dropColumn('order_info');
            $table->dropColumn('stock_month');
            $table->dropColumn('created_user');
            $table->dropColumn('updated_user');
            $table->dropColumn('is_deleted');

        });
    }
}
