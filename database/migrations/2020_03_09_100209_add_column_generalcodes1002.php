<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnGeneralcodes1002 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('generalcodes', function (Blueprint $table) {
            //カラム追加
            $table->string('secound_code_name')->nullable()->after('code_name')->comment('項目名略称');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('generalcodes', function (Blueprint $table) {
            $table->dropColumn('secound_code_name');
        });
    }
}
