<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatmanageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matmanage', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->date('mdate')->nullable()->comment('日付');
            $table->string('department', 20)->nullable()->comment('部署');
            $table->string('charge', 20)->nullable()->comment('担当');
            $table->string('product_name', 100)->nullable()->comment('商品名');
            $table->integer('product_code')->nullable()->comment('商品CODE');
            $table->string('product_number', 30)->nullable()->comment('商品Number');
            $table->string('unit', 10)->nullable()->comment('単位');
            $table->integer('quantity')->nullable()->comment('入数');
            $table->integer('receipt')->nullable()->comment('入庫数');
            $table->integer('delivery')->nullable()->comment('出庫数');
            $table->integer('now_inventory')->nullable()->comment('現在在庫');
            $table->string('nbox', 16)->nullable()->comment('箱数');
            $table->string('order_address', 100)->nullable()->comment('発注先');
            $table->integer('unit_price')->nullable()->comment('単価');
            $table->integer('total')->nullable()->comment('合計');
            $table->string('remarks', 200)->nullable()->comment('備考');
            $table->string('note', 200)->nullable()->comment('メモ/ノート');
            $table->string('status', 20)->nullable()->comment('ステータス--最新/履歴');
            $table->string('marks', 10)->nullable()->comment('マーク');
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
        Schema::dropIfExists('matmanage');
    }
}
