<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDownloadLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('download_logs', function (Blueprint $table) {
            $table->char('account_id',8)->comment('アカウントID');
            $table->unsignedDecimal('downloadfile_no',3,0)->comment('ダウンロードファイル番号');
            $table->char('downloadfile_date',8)->comment('最新ダウンロード日付');
            $table->char('downloadfile_time',6)->comment('最新ダウンロード時刻');
            $table->String('downloadfile_name')->comment('ファイル名');
            $table->integer('downloadfile_cnt')->default(0)->comment('ダウンロード回数');
            $table->char('downloadfile_account_id',8)->comment('最新ダウンロードアカウントID');
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
        Schema::dropIfExists('download_logs');
    }
}
