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
            $table->increments('id');
            $table->text('title');
            $table->text('body_sent');
            $table->text('deadline');
            $table->text('files_sent')->nullable();
            $table->text('files_deliver')->nullable();
            $table->text('body_deliver')->nullable();
            $table->text('evaluation')->nullable();
            $table->integer('rate')->nullable();

            $table->enum('status',['pending','deliver','accepted'])->default('pending');
            $table->integer('from')->unsigned();
            $table->integer('to')->unsigned();
            $table->foreign('from')->references('id')->on('users');
            $table->foreign('to')->references('id')->on('users');
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
