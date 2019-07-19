<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable1713 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedDecimal('fiscal_year',4,0)->comment('年度');
            $table->unsignedDecimal('fiscal_month',2,0)->comment('月');
            $table->unsignedDecimal('closing',2,0)->comment('締日');
            $table->double('uplimit_time',6,2)->comment('上限残業時間');
            $table->double('statutory_uplimit_time',6,2)->comment('法定上限残業時間');
            $table->unsignedInteger('time_unit')->comment('時間単位');
            $table->unsignedInteger('time_rounding')->comment('時間の丸め');
            $table->double('max_3month_total',7,2)->comment('３ヶ月累計');
            $table->double('max_6month_total',7,2)->comment('６ヶ月累計');
            $table->double('max_12month_total',7,2)->comment('１年間累計');
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
        Schema::dropIfExists('settings');
    }
}
