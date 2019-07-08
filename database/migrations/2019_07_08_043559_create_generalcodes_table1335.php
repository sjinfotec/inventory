<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeneralcodesTable1335 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('generalcodes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('identification_id',4)->comment('識別');
            $table->unsignedInteger('code')->comment('コード');
            $table->unsignedInteger('sort_seq')->comment('並び順');
            $table->String('identification_name')->nullable()->comment('識別名');
            $table->String('description')->nullable()->comment('説明');
            $table->String('code_name')->nullable()->comment('項目名');
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
        Schema::dropIfExists('generalcodes');
    }
}
