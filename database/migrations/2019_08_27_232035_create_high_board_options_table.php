<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHighBoardOptionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('high_board_options', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('HB_options', 191);
			$table->integer('user_id')->unsigned()->index('high_board_options_hb_id_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('high_board_options');
	}

}
