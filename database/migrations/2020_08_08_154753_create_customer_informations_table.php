<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_informations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('account_id',8)->comment('アカウントID');
            $table->unsignedDecimal('entry_type',2,0)->comment('問い合わせ種類');
            $table->char('entry_date',8)->comment('問い合わせ日付');
            $table->char('entry_time',6)->comment('問い合わせ時刻');
            $table->char('effective_from_date',8)->comment('有効期限開始');
            $table->char('effective_to_date',8)->comment('有効期限終了');
            $table->String('company_name')->comment('会社名');
            $table->String('representative_name')->comment('担当者氏名');
            $table->String('phone_number')->comment('電話番号');
            $table->String('email_value')->comment('email');
            $table->String('post_code')->nullable()->comment('郵便番号');
            $table->String('address_value')->comment('住所');
            $table->String('entry_contents')->nullable()->comment('問い合わせ内容');
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
        Schema::dropIfExists('customer_informations');
    }
}
