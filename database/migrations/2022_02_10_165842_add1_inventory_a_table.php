<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Add1InventoryATable extends Migration
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
            $table->String('charge',20)->nullable()->comment('担当');
            $table->String('order_no',30)->nullable()->comment('受注番号');
            $table->String('company_name',40)->nullable()->comment('会社名');
            $table->integer('company_id')->nullable()->comment('会社ID');
            // AutoIncrement(PrimaryKey)
            //$table->bigIncrements('company_id')->comment('会社ID');
            // company_id列のPrimaryKey制約を削除
            //$table->dropPrimary();
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
            $table->dropColumn('charge');
            $table->dropColumn('order_no');
            $table->dropColumn('company_name');
            $table->dropColumn('company_id');
        });
    }
}
