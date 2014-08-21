<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TestModel extends Migration {

	public function up() {
	
		Schema::create('test_model', function(Blueprint $table) {
			$table->increments('id');
			$table->string('test');
			$table->timestamps();
		});
		
	}
	
	public function down() {
		Schema::drop('test_model');
	}
	
}