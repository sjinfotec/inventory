<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnGeneralcodes05091351 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('generalcodes', function (Blueprint $table) {
            $table->string('use_free_item')->nullable()->after('secound_code_name')->comment('用途フリー項目');
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
            $table->dropColumn('use_free_item');
        });
    }
}
