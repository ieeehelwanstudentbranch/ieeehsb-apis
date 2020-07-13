<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChapterChairmanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chapter_chairman', function (Blueprint $table) {
             $table->unsignedBigInteger('chapter_id');
            $table->foreign('chapter_id')->references('id')->on('chapters')->onUpdate('cascade');
             $table->unsignedBigInteger('vol_id');
            $table->foreign('vol_id')->references('id')->on('volunteers')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
                Schema::dropIfExists('chapter_chairman');
        // Schema::table('chapter_chairman', function (Blueprint $table) {
            //
        // });
    }
}
