<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApprovalRouteNos03111450 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approval_route_nos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('approval_route_no')->comment('承認ルート番号');
            $table->char('apply_department_code',8)->comment('適用部署');
            $table->String('name')->comment('承認ルート名称');
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
        Schema::dropIfExists('approval_route_nos');
    }
}
