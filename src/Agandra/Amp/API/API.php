<?php namespace Agandra\Amp\API;

use Agandra\Amp\API\APIException;
use Agandra\Amp\API\Models\APIAccess;
use Agandra\Amp\API\Models\APIToken;
use Agandra\Amp\Reply;

class API {

	protected $secret;
	protected $salt;
	public $token;

	public function __construct() {
		$this->salt = \Config::get('amp::salt');
	}

	public function reply() {
		return new Reply;
	}

	public function respond(Reply $reply, $code = 200) {
		return \Response::json($reply->toArray(), $code);
	}

	public function validateAPI() {
		if(!\Input::get('api_key')) {
			throw new APIException('Invalid API Key', 401);
		}

		$api = APIAccess::where('public_key', '=', \Input::get('api_key'))->get();

		if($api->isEmpty()) {
			throw new APIException('Invalid API Key', 401);
		}

		$this->setSecret($api->first()->secret);

		if(!\Input::get('device_id')) {
			throw new APIException('Invalid Device ID', 401);
		}
	}

	public function authFilter() {

		// Only need to check if user is not already logged in
		// If user is already logged in, means ajax request
		if(!Amp::check()) {
			if(!\Input::get('auth_token')) {
				throw new APIException('Auth token not set', 401);
			}

			$token = APIToken::where('device_id', '=', \Input::get('device_id'))->where('token','=',\Input::get('auth_token'))->get();

			if($token->isEmpty()) {
				throw new APIException('User not authenticated', 401);
			}

			$token = $token->first();

			if(\Input::get('auth_token') != $token->token) {
				throw new APIException('Invalid auth token', 401);
			}

			$user = \Repo::call('User')->find($token->user_id);
			if(!$user) {
				throw new APIException('User not found', 401);
			}
		}
		
	}

	public function setUser() {
		// Should all succeed because of auth filter
		$token = APIToken::where('device_id', '=', \Input::get('device_id'))->where('token','=',\Input::get('auth_token'))->get()->first();

		if($token) {
			$user = \Repo::call('User')->find($token->user_id);

			if($user) {
				\Auth::login($user);
				$this->token = $token;
			}
		}
		
	}

	public function validateHash() {
		$input = \Input::all();
	}

	public function setSecret($secret) {
		$this->secret = $secret;
	}

	public function getSecret() {
		return $this->secret;
	}

	public function createToken($user, $device_id) {
		// Check if API Token already exists for this user/device id combo
		$apiToken = APIToken::where('user_id', '=', $user->id)->where('device_id', '=', $device_id)->get()->first();

		if($apiToken) {
			return $apiToken->token;
		}

		$apiToken = new APIToken;
		$apiToken->device_id = $device_id;
		$apiToken->user_id = $user->id;

		// Create specific unique token
		$token = md5($device_id . rand() . time() . $this->salt . $user->id);
		$apiToken->token = $token;
		$apiToken->save();

		return $token;
	}

	public function deleteToken($user, $device_id) {
		$apiToken = APIToken::where('user_id', '=', $user->id)->where('device_id', '=', $device_id)->get()->first();

		if($apiToken)
			$apiToken->delete();

		return true;
	}

}