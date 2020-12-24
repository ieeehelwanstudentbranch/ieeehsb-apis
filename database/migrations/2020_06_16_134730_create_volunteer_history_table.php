<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVolunteerHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('vol_history', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->unsignedBigInteger('vol_id');
          $table->foreign('vol_id')->references('id')->on('volunteers');
          $table->unsignedBigInteger('season_id');
          $table->foreign('season_id')->references('id')->on('seasons');
          $table->unsignedBigInteger('position_id');
          $table->foreign('position_id')->references('id')->on('positions');
          $table->string('achievments',2000)->nullable();
          $table->string('skills',2000)->nullable();
          $table->string('courses',2000)->nullable();
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
           Schema::dropIfExists('vol_history');
     }

}
