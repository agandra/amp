<?php namespace Agandra\Amp\Tests;

use Illuminate\Support\Facades\Config;

class AmpTest extends \TestCase {

	public function setUp() {
		\Illuminate\Foundation\Testing\TestCase::setUp();

		$path = realpath(__DIR__.'/migrations');
		$path = substr($path, strlen(getcwd())+1);
		
		\Artisan::call('migrate', array('--path'=>$path));

		include_once(dirname(__FILE__).'/TestModel.php');
	}

	public function testBase() {
		$test = new TestModel;
		$test->autoSave(['test'=>'asdf','notfillable'=>'qwer']);

		$matchingRec = TestModel::all()->first();
		$this->assertEquals('asdf', $matchingRec->test);
		$this->assertEquals(null, $matchingRec->notfillable);
	}
	
}