<?php namespace Agandra\Amp;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Collection;

class Reply {

	/**
	 * If this request was a success, initialized to false
	 *
	 * @var boolean
	 */
	private $success = false;

	/**
	 * Any data to be passed in the reply
	 *
	 * @var array
	 */
	private $data = [];

	/**
	 * Code of response
	 *
	 * @var int
	 */
	private $code;

	/**
	 * Any errors returned if success is false
	 *
	 * @var Illuminate\Support\MessageBag
	 */
	private $errors;

	/**
	 * Exception name if success is false
	 *
	 * @var string
	 */
	private $exception;

	public function __construct() {
		// Make errors variable an empty MessageBag
		$this->errors = new MessageBag;
	}

	/**
	 * Return this object in array form
	 *
	 * @return array
	 */
	public function toArray() {
		$response = [];

		if($this->success) {
			$response['success'] = true;
			$response['data'] = $this->data;

			if(!is_array($response['data']))
				$response['data'] = $response['data']->toArray();

			if($this->code === 0)
				$response['code'] = 200;
			else
				$response['code'] = $this->code;
		} else {
			$response['success'] = false;
			$response['errors'] = $this->errors->toArray();
			$response['exception'] = $this->exception;
			
			if($this->code === 0)
				$response['code'] = 300;
			else
				$response['code'] = $this->code;
		}

		return $response;
	}

	/**
	 * Set the data attribute for this reply
	 * @param array data
	 *
	 * @return this
	 */
	public function data($data) {
		$this->success(true);
		$this->data = $data;
		return $this;
	}

	/**
	 * Set the success attribute for this reply
	 * @param boolean success
	 *
	 * @return this
	 */
	public function success($success) {
		$this->success = $success;
		return $this;
	}

	/**
	 * Set the code attribute for this reply
	 * @param int code
	 *
	 * @return this
	 */
	public function code($code) {
		$this->code = $code;
		return $this;
	}

	/**
	 * Set the errors attribute for this reply
	 * @param MessageBag errors
	 *
	 * @return this
	 */
	public function errors(MessageBag $errors) {
		$this->errors = $errors;
		return $this;
	}

	/**
	 * Set the exception attribute for this reply
	 * @param string exception
	 *
	 * @return this
	 */
	public function exception($exception) {
		$this->exception = $exception;
		return $this;
	}

	/**
	 * Add an error to the message bag instance
	 * @param string key
	 * @param string value
	 *
	 * @return this
	 */
	public function addError($key, $value) {
		$this->errors->add($key, $value);
		return $this;
	}

	/**
	 * Get the code attribute for this reply
	 *
	 * @return int
	 */
	public function getCode() {
		if(!$this->code)
			return 0;

		return $this->code;
	}

	/**
	 * Get the errors attribute
	 * @param boolean toArray
	 *
	 * @return mixed
	 */
	public function getErrors($toArray = false) {
		if($toArray)
			return $this->errors->toArray();
		else 
			return $this->errors;
	}

	public function getException() {
		if(!$this->exception)
			return 'GenericException';

		return $this->exception;
	}

	public function getData() {
		return $this->data;
	}

	/**
	 * Get the key value for the data array
	 * @param string key
	 *
	 * @return mixed
	 */
	public function key($key) {
		if(array_key_exists($this->data[$key])) {
			return $this->data[$key];
		} else {
			return false;
		}
	}

	
	/*
    |--------------------------------------------------------------------------
    | Methods for generating generic reply states for quick usage
    |--------------------------------------------------------------------------
    |
    */
	
	public function validationException($errors) {
		$this->success(false)->code(300)->exception('ValidationException');

		if($errors instanceof MessageBag) {
			$this->errors = $errors;
		} elseif(is_array($errors)) {
			foreach($errors as $key=>$error) {
				$this->errors->add($key, $error);
			}
		} else {
			$this->errors->add('other', $errors);
		}

		return $this;
	}

	public function permissionException($message = '') {
		$this->success(false)->code(300)->exception('PermissionException');

		if(!$message) {
			$message = 'User does not have permission to access that resource';
		}
		
		$this->addError('Permission', $message);

		return $this;
	}

	

	public function pass($key = '', $data = []) {
		$this->success = true;
		$this->code = 200;

		if(is_array($key))
			$this->data($key);
		else {
			if($key)
				$this->data[$key] = $data;
		}

		return $this;
	}

	public function fail($exception, $message = '') {
		$this->code = 300;
		$this->exception = $exception;
		if($message) {
			$this->errors->add('exception', $message);
		}
		return $this;
	}

	public function passes() {
		return $this->success;
	}

	public function fails() {
		return !$this->success;
	}

	

}