<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVolunteerEventposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('volunteer_eventpos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('eventpos_id');
            $table->foreign('eventpos_id')->references('id')->on('event_positions');
            $table->unsignedBigInteger('vol_id');
            $table->foreign('vol_id')->references('id')->on('vol_committees');
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
        Schema::dropIfExists('volunteer_eventpos');
    }
}
