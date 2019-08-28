<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTasksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tasks', function(Blueprint $table)
		{
			$table->increments('id');
			$table->text('title', 65535);
			$table->text('body_sent', 65535);
			$table->text('deadline', 65535);
			$table->text('files_sent', 65535)->nullable();
			$table->text('files_deliver', 65535)->nullable();
			$table->text('body_deliver', 65535)->nullable();
			$table->text('evaluation', 65535)->nullable();
			$table->integer('rate')->nullable();
			$table->enum('status', array('pending','deliver','accepted'))->default('pending');
			$table->integer('from')->unsigned()->index('tasks_from_foreign');
			$table->integer('to')->unsigned()->index('tasks_to_foreign');
			$table->integer('committee_id')->unsigned()->nullable();
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
		Schema::drop('tasks');
	}

}
