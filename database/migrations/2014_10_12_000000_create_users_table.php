<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstName');
            $table->string('lastName');
            $table->string('faculty')->nullable();
            $table->string('university')->nullable();
            $table->string('DOB')->nullable();
            $table->string('phone')->nullable();
            $table->string('level')->nullable();
            $table->string('address')->nullable();
            $table->string('position');
            $table->string('image')->nullable();
            $table->enum('status',['active','freezed','deprecated'])->default('active');
            $table->boolean('confirmed')->default('0');
            $table->string('confirmation_code')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('token')->nullable();
            $table->string('password');
            $table->integer('committee_id')->unsigned()->nullable();
//            $table->foreign('committee_id')->references('id')->on('committees');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
