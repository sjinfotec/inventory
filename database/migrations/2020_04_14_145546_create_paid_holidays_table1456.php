<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaidHolidaysTable1456 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::dropIfExists('paid_holidays');
        Schema::create('paid_holidays', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->String('user_code',10)->comment('ユーザーコード');
            $table->char('year',4)->comment('年度');
            $table->unsignedInteger('type')->comment('タイプ');
            $table->float('paid_this_year', 8, 2)->comment('本年度付与日数');
            $table->float('paid_last_year', 8, 2)->comment('昨年度残日数');
            $table->float('paid_sum', 8, 2)->comment('付与日数');
            $table->String('created_user')->nullable()->comment('作成ユーザー');
            $table->String('updated_user')->nullable()->comment('修正ユーザー');
            $table->boolean('is_deleted')->default(0);
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
        Schema::dropIfExists('paid_holidays');
    }
}
