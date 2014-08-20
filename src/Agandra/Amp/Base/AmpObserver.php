<?php namespace Agandra\Amp\Base;

class AmpObserver {

	protected $class;

	public function __construct() {
		$this->class = get_called_class();
	}
}