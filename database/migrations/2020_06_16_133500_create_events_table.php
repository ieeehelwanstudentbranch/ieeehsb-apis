<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',200);
            $table->string('information',1000);
            $table->dateTime('from');
            $table->dateTime('to');
            $table->string('location');
            $table->tinyInteger('numOfSessions');
            $table->unsignedBigInteger('award_id');
            $table->foreign('award_id')->references('id')->on('awards')->onUpdate('cascade');
            $table->unsignedBigInteger('chapter_id');
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
        Schema::dropIfExists('events');
    }
}
