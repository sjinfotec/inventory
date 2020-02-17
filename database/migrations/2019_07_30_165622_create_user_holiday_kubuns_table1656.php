<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserHolidayKubunsTable1656 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_holiday_kubuns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('working_date',8)->comment('日付');
            $table->char('department_id',8)->comment('部署コード');
            $table->char('user_code',10)->comment('ユーザー');
            $table->unsignedDecimal('holiday_kubun',2,0)->nullable()->comment('休暇区分');
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
        Schema::dropIfExists('user_holiday_kubuns');
    }
}
