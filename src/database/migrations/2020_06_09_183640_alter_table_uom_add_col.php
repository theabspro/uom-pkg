<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableUomAddCol extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('uoms', function (Blueprint $table) {
			$table->string('short_name', 32)->nullable()->after('code');
			$table->string('description')->nullable()->after('name');
			$table->unique('short_name');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('uoms', function (Blueprint $table) {
			$table->dropUnique('uoms_short_name_unique');
			$table->dropColumn('short_name');
			$table->dropColumn('description');
		});
	}
}
