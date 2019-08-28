<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToExComOptionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('ex_com_options', function(Blueprint $table)
		{
			$table->foreign('user_id', 'ex_com_options_ex_id_foreign')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('ex_com_options', function(Blueprint $table)
		{
			$table->dropForeign('ex_com_options_ex_id_foreign');
		});
	}

}
