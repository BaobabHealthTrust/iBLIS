<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitWardMapsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//visit_types table
		Schema::create('visit_types', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->string('name');
            $table->timestamps();
      	});

		Schema::create('visittype_wards', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->integer('visit_type_id')->unsigned();
			$table->integer('ward_id')->unsigned();

			$table->foreign('visit_type_id')->references('id')->on('visit_types')->onDelete('cascade');
			$table->foreign('ward_id')->references('id')->on('wards')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
		Schema::table('visittype_wards', function (Blueprint $table) {
			$table->dropForeign('visittype_wards_visit_type_id_foreign');
			$table->dropForeign('visittype_wards_ward_id_foreign');
		});

		Schema::drop('visit_types');
		Schema::drop('visittype_wards');
	}

}
