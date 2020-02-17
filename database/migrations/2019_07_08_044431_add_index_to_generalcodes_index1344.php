<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToGeneralcodesIndex1344 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('generalcodes', function (Blueprint $table) {
            $table->index(['identification_id', 'code','sort_seq'],'generalcodes_identification_id_code_sort_seq');
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
            //
        });
    }
}
