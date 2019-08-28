<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('firstName', 191);
			$table->string('lastName', 191);
			$table->string('faculty', 191)->nullable();
			$table->string('university', 191)->nullable();
			$table->string('DOB', 191)->nullable();
			$table->string('address', 100)->nullable();
			$table->string('phone', 20)->nullable();
			$table->integer('level')->nullable();
			$table->string('position', 191);
			$table->string('image', 191)->nullable()->default('default.jpg');
			$table->enum('status', array('active','freezed','deprecated'))->default('active');
			$table->boolean('confirmed')->default(0);
			$table->string('confirmation_code', 191)->nullable();
			$table->string('email', 191)->unique();
			$table->dateTime('email_verified_at')->nullable();
			$table->string('token', 191)->nullable();
			$table->string('password', 191);
			$table->char('api_token', 60)->nullable();
			$table->integer('committee_id')->unsigned()->nullable()->index('committee_id');
			$table->string('remember_token', 100)->nullable();
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
		Schema::drop('users');
	}

}
