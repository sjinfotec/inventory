<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDemands2124 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demands', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->String('no',12)->comment('申請番号');
            $table->decimal('doc_code',2,0)->comment('申請書類コード');
            $table->unsignedInteger('log_no')->comment('履歴番号');
            $table->String('status',2)->nullable()->comment('ステータス');
            $table->String('department_code',8)->nullable()->comment('申請者部署');
            $table->String('user_code',10)->nullable()->comment('申請者');
            $table->String('demand_date',8)->nullable()->comment('申請日');
            $table->String('date_from',8)->nullable()->comment('申請期間開始');
            $table->String('date_to',8)->nullable()->comment('申請期間終了');
            $table->String('demand_reason',256)->nullable()->comment('申請理由');
            $table->String('before_after',1)->nullable()->comment('事前事後');
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
        Schema::dropIfExists('demands');
    }
}
