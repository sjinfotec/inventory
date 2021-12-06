<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProgressHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('progress_headers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('order_no',12)->comment('受注番号');
            $table->char('seq',5)->comment('行');
            $table->String('drawing_no')->nullable()->comment('図面番号');
            $table->char('order_date',8)->nullable()->comment('受注日');
            $table->char('supply_date',8)->nullable()->comment('納期');
            $table->char('office_code',2)->nullable()->comment('営業所コード');
            $table->char('customer_code',4)->nullable()->comment('顧客コード');
            $table->String('back_order_customer_name')->nullable()->comment('受注残客先');
            $table->String('order_count')->nullable()->comment('数量');
            $table->String('model_number')->nullable()->comment('型番');
            $table->char('product_code',4)->nullable()->comment('品名コード');
            $table->String('back_order_product_name')->nullable()->comment('受注残品名');
            $table->String('outline_name')->nullable()->comment('摘要');
            $table->String('back_order_quality_name')->nullable()->comment('材質');
            $table->unsignedInteger('order_kingaku')->comment('受注金額');
            $table->char('material_office_code',2)->nullable()->comment('素材納入営業所コード');
            $table->char('material_customer_code',4)->nullable()->comment('素材納入顧客コード');
            $table->char('heat_office_code',2)->nullable()->comment('熱処理営業所コード');
            $table->char('heat_customer_code',4)->nullable()->comment('熱処理顧客コード');
            $table->String('heat_cost')->nullable()->comment('熱処理費');
            $table->char('outsourcing_office_code',2)->nullable()->comment('外注営業所コード');
            $table->char('outsourcing_customer_code',4)->nullable()->comment('外注顧客コード');
            $table->String('outsourcing_cost')->nullable()->comment('外注費');
            $table->String('created_user')->nullable()->comment('作成ユーザー');
            $table->String('updated_user')->nullable()->comment('修正ユーザー');
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
        Schema::dropIfExists('progress_headers');
    }
}
