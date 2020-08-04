<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnGeneralcodes05260919 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('generalcodes', function (Blueprint $table) {
            $table->string('physical_name')->nullable()->after('description')->comment('物理名称');
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
            $table->dropColumn('physical_name');
        });
    }
}
