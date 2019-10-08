<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfirmsTable2011 extends Migration
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
            $table->char('department_id',8)->comment('部署コード');
            $table->char('user_code',10)->comment('ユーザー');
            $table->unsignedDecimal('seq',2,0)->nullable()->comment('承認順番');
            $table->unsignedDecimal('main_sub',1,0)->nullable()->comment('正副');
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
