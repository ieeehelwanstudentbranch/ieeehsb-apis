<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateExComOptionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ex_com_options', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('ex_options', 191);
			$table->integer('user_id')->unsigned()->index('ex_com_options_ex_id_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ex_com_options');
	}

}
