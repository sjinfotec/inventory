<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMmstockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mmstock', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->integer('inv_id')->nullable()->comment('inventoryID');
            $table->string('product_name', 100)->nullable()->comment('商品名');
            $table->integer('product_code')->nullable()->comment('商品ID');
            $table->string('unit', 10)->nullable()->comment('単位');
            $table->integer('quantity')->nullable()->comment('入数');
            $table->integer('now_inventory')->nullable()->comment('現在在庫');
            $table->string('nbox', 16)->nullable()->comment('箱数');
            $table->integer('stock_now_inventory')->nullable()->comment('棚卸在庫');
            $table->string('stock_nbox', 16)->nullable()->comment('棚卸箱数');
            $table->string('status', 20)->nullable()->comment('ステータス');
            $table->char('stock_month', 7)->nullable()->comment('棚卸月');
            $table->string('created_user', 20)->nullable()->comment('作成ユーザー');
            $table->string('updated_user', 20)->nullable()->comment('修正ユーザー');
            $table->timestamps();
            $table->boolean('is_deleted')->nullable()->comment('削除フラグ')->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mmstock');
    }
}
