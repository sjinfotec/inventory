<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApprovals2125 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->String('no',12)->comment('申請番号');
            $table->decimal('doc_code',2,0)->comment('申請書類コード');
            $table->decimal('seq',2,0)->comment('承認順番');
            $table->unsignedInteger('log_no')->comment('履歴番号');
            $table->String('status',2)->nullable()->comment('ステータス');
            $table->String('department_code',8)->nullable()->comment('承認者部署');
            $table->String('user_code',10)->nullable()->comment('承認者');
            $table->String('approval_date',8)->nullable()->comment('承認日または差し戻し日');
            $table->String('remand_reason',256)->nullable()->comment('差し戻し理由');
            $table->String('mail_result',1)->nullable()->comment('メール送信結果');
            $table->String('mail_address')->nullable()->comment('メール宛先');
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
        Schema::dropIfExists('approvals');
    }
}
