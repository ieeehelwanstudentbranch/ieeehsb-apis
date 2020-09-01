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
            $table->string('logo',500);
            $table->string('cover',500);
            $table->dateTime('from');
            $table->dateTime('to');
            $table->string('location');
            $table->string('awards',2000);
            $table->unsignedBigInteger('award_id')->nullable();
            $table->unsignedBigInteger('chapter_id')->nullable();
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
