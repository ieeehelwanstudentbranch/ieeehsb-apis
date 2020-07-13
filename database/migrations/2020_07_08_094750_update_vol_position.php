<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateVolPosition extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vol_committees', function (Blueprint $table) {
            // $table->dropForeign(['position_id']);
            // $table->dropColumn('position_id');
            $table->string('position',200)->after('committee_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vol_committees', function (Blueprint $table) {
            //
        });
    }
}
