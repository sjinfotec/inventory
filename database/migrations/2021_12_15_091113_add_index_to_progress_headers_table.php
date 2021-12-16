<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToProgressHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('progress_headers', function (Blueprint $table) {
            $table->unsignedInteger('out_seq')->after('id')->comment('出力順');
        });
        Schema::table('progress_headers', function (Blueprint $table) {
            $table->index(['out_seq'],'out_seq_index');
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('progress_headers', function (Blueprint $table) {
            //
        });
    }
}
