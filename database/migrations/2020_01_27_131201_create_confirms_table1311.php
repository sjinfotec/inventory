<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfirmsTable1311 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('confirms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('confirm_no')->comment('承認ルート番号');
            $table->unsignedDecimal('seq',2,0)->comment('承認順番');
            $table->unsignedDecimal('main_sub',1,0)->comment('正副');
            $table->char('confirm_department_id',8)->nullable()->comment('ルート適用部署');
            $table->char('user_department_id',8)->comment('部署コード');
            $table->char('user_code',10)->comment('ユーザー');
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
        Schema::dropIfExists('confirms');
    }
}
