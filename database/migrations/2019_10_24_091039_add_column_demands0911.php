<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnDemands0911 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('demands', function (Blueprint $table) {
            $table->boolean('is_deleted')->after('updated_at')->default(0)->comment('削除フラグ');
            $table->String('updated_user')->after('mail_address')->nullable()->comment('作成ユーザー');
            $table->String('created_user')->after('mail_address')->nullable()->comment('修正ユーザー');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('demands', function (Blueprint $table) {
            $table->dropColumn('is_deleted');
            $table->dropColumn('updated_user');
            $table->dropColumn('created_user');
        });
    }
}
