<?php namespace Agandra\Amp;

use Agandra\Amp\ContextNotFoundException;
use Agandra\Amp\RulesNotFoundException;

class AmpValidator {

	/**
	 * The input that should be validated against
	 *
	 * @var array
	 */
	protected $input;

	/**
	 * The instance of the class we are using to validate
	 *
	 * @var Class Instance
	 */
	protected $class;

	/**
	 * Any custom validation messages that we should display on failure
	 *
	 * @var array
	 */
	protected $customMessages;

	/**
	 * Array of rules to validate against
	 *
	 * @var array
	 */
	protected $rules;

	/**
	 * The key values of the rules we should use in our validation
	 *
	 * @var array
	 */
	protected $context = [];

	/**
	 * Error messages on validation failure
	 *
	 * @var \Illuminate\Support\MessageBag
	 */
	protected $messages;

	/**
	 * If we will use the default key in rules array
	 *
	 * @var boolean
	 */
	protected $default = true;

	/**
	 * @var \Illuminate\Validator\Validator
	 */
	protected $validator;

	public function __construct($input, $class) {
		$this->input = $input;
		$this->class = $class;
		$this->rules = $class->rules;
		if(!$class->customMessages) {
			$this->customMessages = [];
		} else {
			$this->customMessages = $class->customMessages;
		}
		
	}

	/*
	 * Call to add a set of validation rules that should be used when calling the validator
	 */
	public function addContext($contexts) {
		if(is_array($contexts)) {
			foreach($contexts as $context) {
				$this->context[] = $context;
			}
		} else {
			$this->context[] = $contexts;
		}
	}

	public function removeDefault() {
		$this->default = false;
	}

	/*
	 * Private function to merge all the rules into one rule array, will also parse rules for special values
	 */
	private function _makeValidator() {
		// See if we have already parsed rules and made the validator
		if($this->validator)
			return true;

		$rules = [];

		// Check for default rules array
		if(isset($this->rules['default']) && $this->default) {
			$rules = $this->rules['default'];
		}

		if(!empty($this->context)) {
			foreach($this->context as $context) {

				if(!isset($this->rules[$context]))
					throw new ContextNotFoundException('Context not found in rules', 300);

				// Merge rules into singular array by rule key value
				foreach($this->rules[$context] as $k => $v) {
					if(isset($rules[$k])) {
						$rules[$k] .= '|'.$this->_parseRules($v);
					} else {
						$rules[$k] = $this->_parseRules($v);
					}
				}

			}
		} else {
			if(empty($rules)) {
				$rules = $this->rules;
			}
		}

		// Create validator
		$this->validator = \Validator::make($this->input, $rules, $this->customMessages);
	}

	/*
	 * Private function parse rule to check if there are matching regex values
	 */
	private function _parseRules($rules) {
		$output = preg_replace_callback("^\{{(.*?)\}}^",
			function($matches) {
				return $this->class->$matches[1];
			},
			$rules
		);

		return $output;
	}

	/*
	 * Function to return validator messages on failure
	 */
	public function messages() {
		return $this->messages;
	}

	/*
	 * Check if validator fails
	 */
	public function fails() {
		$this->_makeValidator();

		if($this->validator->fails()) {
			$this->messages = $this->validator->messages();
			return true;
		}

		return false;
	}

	/*
	 * Check if validator passes
	 */
	public function passes() {
		$this->_makeValidator();

		if(!$this->validator->passes()) {
			$this->messages = $this->validator->messages();
			return false;
		}

		return true;
	}
}