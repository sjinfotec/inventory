<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApprovalAuthorizers03111503 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approval_authorizers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('approval_route_no')->comment('承認ルート番号');
            $table->decimal('seq', 2, 0)->comment('承認順番');
            $table->decimal('main_sub', 2, 0)->comment('正副');
            $table->char('approval_department_code',8)->comment('承認部署');
            $table->char('approval_user_code',10)->comment('承認者');
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
        Schema::dropIfExists('approval_authorizers');
    }
}
