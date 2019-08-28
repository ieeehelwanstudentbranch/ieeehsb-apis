<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCommitteesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('committees', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 191)->unique('un_name');
			$table->string('mentor', 191)->nullable();
			$table->string('director', 191)->nullable();
			$table->string('hr_coordinator', 191)->nullable();
			$table->integer('director_id')->unsigned()->nullable()->index('committees_user_id_foreign');
			$table->integer('hr_coordinator_id')->unsigned()->nullable();
			$table->integer('mentor_id')->unsigned()->nullable();
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
		Schema::drop('committees');
	}

}
