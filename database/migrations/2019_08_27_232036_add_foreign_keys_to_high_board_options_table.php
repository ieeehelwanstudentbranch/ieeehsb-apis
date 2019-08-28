<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToHighBoardOptionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('high_board_options', function(Blueprint $table)
		{
			$table->foreign('user_id', 'high_board_options_hb_id_foreign')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('high_board_options', function(Blueprint $table)
		{
			$table->dropForeign('high_board_options_hb_id_foreign');
		});
	}

}
