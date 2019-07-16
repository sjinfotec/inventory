<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropCompanysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('companys');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('companys', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->String('name')->comment('会社名');
            $table->String('kana')->nullable()->comment('会社カナ');
            $table->String('post_code')->nullable()->comment('郵便番号');
            $table->String('address1')->nullable()->comment('住所 1');
            $table->String('address2')->nullable()->comment('住所２');
            $table->String('address_kana')->nullable()->comment('住所カナ');
            $table->String('tel_no')->nullable()->comment('電話番号');
            $table->String('fax_no')->nullable()->comment('FAX番号');
            $table->String('represent_name')->nullable()->comment('代表者氏名');
            $table->String('represent_kana')->nullable()->comment('代表者カナ');
            $table->String('email')->nullable()->comment('e-mail');
            $table->unsignedInteger('month_sm')->nullable()->comment('月締め');
            $table->String('created_user')->nullable()->comment('作成ユーザー');
            $table->String('updated_user')->nullable()->comment('修正ユーザー');
            $table->timestamps();
            $table->boolean('is_deleted')->default(0)->comment('削除フラグ');
        });
    }
}
