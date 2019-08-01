<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnDepartmentIdOnCardInformationsTable0902 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('card_informations', function (Blueprint $table) {
            $table->bigInteger('department_id')->after('user_code')->nullable()->comment('部署ID');
            $table->String('updated_user')->after('card_idm')->nullable()->comment('修正ユーザー');
            $table->String('created_user')->after('card_idm')->nullable()->comment('作成ユーザー');
        });

}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
