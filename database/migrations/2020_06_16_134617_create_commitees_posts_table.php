<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommiteesPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('committee_posts', function(Blueprint $table)
   {
       $table->bigIncrements('id');
       $table->unsignedBigInteger('comm_id');
       $table->foreign('comm_id')->references('id')->on('committees');
       $table->unsignedBigInteger('post_id');
       $table->foreign('post_id')->references('id')->on('posts');
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
          Schema::dropIfExists('committee_posts');
    }
}
