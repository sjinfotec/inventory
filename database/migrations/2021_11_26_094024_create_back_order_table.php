<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBackOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('back_order', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('order_no',12)->comment('受注番号');
            $table->unsignedInteger('seq')->comment('連番');
            $table->char('order_date',8)->nullable()->comment('受注日');
            $table->String('row_seq')->nullable()->comment('行');
            $table->String('drawing_no')->nullable()->comment('図面番号');
            $table->String('customer_name')->nullable()->comment('客先');
            $table->String('model_number')->nullable()->comment('型番');
            $table->String('product_name')->nullable()->comment('品名');
            $table->String('quality_name')->nullable()->comment('材質');
            $table->String('order_count')->nullable()->comment('数量');
            $table->char('supply_date',8)->nullable()->comment('納期');
            $table->unsignedInteger('order_kingaku')->comment('受注金額');
            $table->String('outline_name')->nullable()->comment('摘要');
            $table->String('created_user')->nullable()->comment('作成ユーザー');
            $table->String('updated_user')->nullable()->comment('修正ユーザー');
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
        Schema::dropIfExists('back_order');
    }
}
