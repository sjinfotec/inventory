<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeatureItemSelections05211439 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feature_item_selections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('account_id',8)->comment('アカウントID');
            $table->unsignedDecimal('selection_code',2,0)->comment('選択種類');
            $table->unsignedDecimal('item_code',2,0)->comment('項目コード 1から連番（内部コード）');
            $table->unsignedDecimal('item_seq',2,0)->comment('出力順');
            $table->String('item_name')->nullable()->comment('項目名');
            $table->String('item_kanji_name')->nullable()->comment('項目漢字名');
            $table->String('item_out_name')->nullable()->comment('出力項目名');
            $table->boolean('value_select')->default(1)->comment('項目選択値');
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
        Schema::dropIfExists('feature_item_selections');
    }
}
