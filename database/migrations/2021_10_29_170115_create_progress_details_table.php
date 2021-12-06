<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProgressDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('progress_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('order_no',12)->comment('受注番号');
            $table->char('seq',5)->comment('行');
            $table->unsignedInteger('progress_no')->comment('工程NO');
            $table->char('device_code',6)->nullable()->comment('機器コード');
            $table->char('department_code',2)->nullable()->comment('部署コード');
            $table->char('users_code',4)->nullable()->comment('社員コード');
            $table->char('process_history_no',4)->nullable()->comment('加工履歴No');
            $table->char('setup_history_no',4)->nullable()->comment('段取り履歴No');
            $table->char('complete_date',8)->nullable()->comment('完了日');
            $table->String('qr_code')->nullable()->comment('QRコード');
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
        Schema::dropIfExists('progress_details');
    }
}
