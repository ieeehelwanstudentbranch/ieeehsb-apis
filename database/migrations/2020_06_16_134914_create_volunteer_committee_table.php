<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVolunteerCommitteeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('vol_committees', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->unsignedBigInteger('vol_id');
          $table->foreign('vol_id')->references('id')->on('volunteers');
          $table->unsignedBigInteger('committee_id');
          $table->foreign('committee_id')->references('id')->on('committees');
          // $table->unsignedBigInteger('position_id');
          // $table->foreign('position_id')->references('id')->on('positions');
          $table->unsignedBigInteger('season_id');
          $table->foreign('season_id')->references('id')->on('seasons');
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
        Schema::table('vol_committee', function (Blueprint $table) {
            //
        });
    }
}
