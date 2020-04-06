<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReferenceInVisitTypes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('visit_types', function(Blueprint $table)
		{
			$table->string('description')->nullable();
            $table->binary('reference_id')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('visit_types', function(Blueprint $table)
		{
			$table->dropColumn('reference_id');
			$table->dropColumn('description');
		});
	}

}
