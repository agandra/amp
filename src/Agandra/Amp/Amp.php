<?php namespace Agandra\Amp;

class Amp {

	/**
	 * The instance of the user object
	 *
	 * @var User Instance
	 */
	private $user;

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
		return new AmpReply();
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
}