<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDemandDetails0857 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demand_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->String('no',12)->comment('申請番号');
            $table->decimal('doc_code',2,0)->comment('申請書類コード');
            $table->unsignedInteger('log_no')->comment('履歴番号');
            $table->unsignedInteger('row_no')->comment('行番号');
            $table->String('working_item')->nullable()->comment('作業項目');
            $table->String('date_from',8)->nullable()->comment('作業期間開始日付');
            $table->String('time_from',6)->nullable()->comment('作業期間開始時刻');
            $table->String('date_to',8)->nullable()->comment('作業期間終了日付');
            $table->String('time_to',6)->nullable()->comment('作業期間終了時刻');
            $table->String('demand_reason',256)->nullable()->comment('申請理由');
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
        Schema::dropIfExists('demand_details');
    }
}
