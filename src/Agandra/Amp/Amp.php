<?php namespace Agandra\Amp;

use Agandra\Amp\Base\RepoNotFoundException;

class Amp {

	/**
	 * The instance of the user object
	 *
	 * @var User Instance
	 */
	private $user;

	/**
	 * An array of repo classes
	 *
	 * @var array
	 */
	private $repoClasses;

	/**
	 * Create a validator instance for the input of the class to be validated against
	 * @param array input
	 * @param object class
	 * 
	 * @return Agandra\Amp\AmpValidator;
	 */
	public function validator($input, $class) {
		return new AmpValidator($input, $class);
	}

	public function reply() {
		return new Reply();
	}

	public function check() {
		return call_user_func(\Config::get('amp::user.check')());	
	}

	public function user() {
		if(!$this->user)
			return $this->user = call_user_func(\Config::get('amp::user.current')());		

		return $this->user;
	}

	public function userOrFail() {
		$user = $this->user();

		if(!$user) 
			throw new UserNotLoggedInException();
		
		return $user;
	}

	/**
	 * Create a validator instance for the input of the class to be validated against
	 * @param array input
	 * @param object class
	 * 
	 * @return Agandra\Amp\AmpValidator;
	 */
	public function repo($class) {
		$class = ucfirst($class);
		if(!isset($this->repoClasses[$class])) {
			$fullClass = \Config::get('amp::repo.'.$class);
			if(!$fullClass) 
				throw new RepoNotFoundException($class.' was not found in the Repo config file.');

			$this->repoClasses[$class] = new $fullClass;
		}

		return $this->repoClasses[$class];
	}
}