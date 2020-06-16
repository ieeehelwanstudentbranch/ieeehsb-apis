<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title',200);
            $table->string('body_sent',3000);
            $table->dateTime('deadline');
            $table->string('file_sent',2000);
            $table->string('body_delivered',3000);
            $table->string('file_delivered',3000);
            $table->string('evaluation',3000);
            $table->mediumInteger('rate');
            $table->unsignedBigInteger('status_id');
            $table->foreign('status_id')->references('id')->on('statuses')->onUpdate('cascade');
            $table->unsignedBigInteger('from');
            $table->foreign('from')->references('id')->on('volunteers');
            $table->unsignedBigInteger('to');
            $table->foreign('to')->references('id')->on('volunteers');
            $table->unsignedBigInteger('comm_id');
            $table->foreign('comm_id')->references('id')->on('committees');








            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
