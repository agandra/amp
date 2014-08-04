<?php namespace Agandra\Amp\Facades;

use Illuminate\Support\Facades\Facade;

class AmpFacade extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'amp'; }

}