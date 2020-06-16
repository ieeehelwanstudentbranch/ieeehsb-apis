<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVolunteersCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('vol_comments', function(Blueprint $table)
   {
       $table->bigIncrements('id');
       $table->unsignedBigInteger('vol_id');
       $table->foreign('vol_id')->references('id')->on('volunteers');
       $table->unsignedBigInteger('comment_id');
       $table->foreign('comment_id')->references('id')->on('comments');
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
         Schema::dropIfExists('vol_comments');
     }

}
