<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkingTimeDateTable1847 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('working_time_date', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->String('user_code',30)->comment('ユーザー');
            $table->char('working_date',8)->comment('日付');
            $table->unsignedInteger('working_time_style')->comment('労働時間形態');
            $table->char('attendance_time',6)->nullable()->comment('出勤時刻');
            $table->char('leaving_time',6)->nullable()->comment('退勤時刻');
            $table->char('missing_middle_time',6)->nullable()->comment('中抜時刻');
            $table->char('missing_middle_return_time',6)->nullable()->comment('中抜戻り時刻');
            $table->double('regular_working_times',5,1)->nullable()->comment('所定労働時間');
            $table->double('out_of_regular_working_times',5,1)->nullable()->comment('所定外労働時間');
            $table->double('legal_working_times',5,1)->nullable()->comment('法定労働時間');
            $table->double('out_of_legal_working_times',5,1)->nullable()->comment('法定外労働時間');
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
        Schema::dropIfExists('working_time_date');
    }
}
