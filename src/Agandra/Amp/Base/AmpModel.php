<?php namespace Agandra\Amp\Base;

class AmpModel extends \Eloquent {

	/**
	 * Validation rules for class
	 *
	 * @var array
	 */
	public $rules = [];
 
	/**
	 * Custom validation messages for class
	 *
	 * @var array
	 */
	public $customMessages = [];
 
	/**
	 * Validation error messages
	 *
	 * @var array
	 */
	protected $messages;
 
	/**
	 * Values that should be hashed upon insertion into the database
	 *
	 * @var array
	 */
	protected $autoHash = [];

	protected $context = [];
 

	/*
	 * Registering boot events
	 */
	protected static function boot() {
		parent::boot();

		static::saving(function($model){
			// If model ID is empty, means new record, if it is not incrementing means UUID type
			if(empty($model->id) && ($model->incrementing === false)) {
				$model->id = (string) \Uuid::generate(4);
			}
		});
	}


	/*
	 * Return user column key
	 */
	public function userColumnKey() {
		return \Config::get('amp::user.columnName');
	}

	public function context($context) {
		if($context === false){
			$this->context = false;
		} else {
			$this->context[] = $context;
		}

		return $this;
	}

	/*
	 * Automatically run validation rules, if they pass save the record to the DB, otherwise return validation messages
	 */
	public function autoSave($data = null) {
		if($data === null)
				$data = \Input::all();

		// Run beforeValidation hook, for user to manipulate data
		if(method_exists($this, 'beforeValidation')) {
			$this->beforeValidation($data);
		}

		$exists = false;

		// Start DB transaction, so we can rollback on error
		$db = \DB::transaction(function() use ($data, $exists) {

			// Make sure the fillable attributes are set, otherwise posibility of user manipulating variables
			if(!property_exists($this, 'fillable'))
				throw new \Exception('Model needs fillable defined');

			$context = [];

			$exists = $this->exists;

			// Choose current ruleset
			if($this->context !== false) {

				if(empty($this->context)) {
					if($exists && isset($this->rules['edit']))
						$context[] = 'edit';
					elseif(!$exists && isset($this->rules['create']))
						$context[] = 'create';
				} else {
					$context = $this->context;
				}
				
				// Validate
				if(!$this->validate($context, $data)) {
					return false;
				}

			}

			if(!array_key_exists($this->userColumnKey(), $data) && in_array($this->userColumnKey(), $this->fillable, true) && \Amp::user())
				$data[$this->userColumnKey()] = \Amp::user()->id;

			// AutoHash all variables that need to be protected
			if(is_array($this->autoHash)) {
				foreach($this->autoHash as $hash) {
					if(isset($data[$hash])) {
						$data[$hash] = \Hash::make($data[$hash]);
					}
				}
			}

			$this->fill($data);

			// Run beforeSave hook
			if(method_exists($this, 'beforeSave')) {
				$this->beforeSave($data);
			}

			// Save record
			$this->save();

			// Run afterSave hook
			if(method_exists($this, 'afterSave')) {
				$this->afterSave($data);
			}

			return true;
		});

		if(!$exists) {
			\Event::fire(get_called_class().'.Created', [$this]);
		} else {
			\Event::fire(get_called_class().'.Updated', [$this]);
		}
		
		return $db;
	}

	/*
	 * Use Amp Validator to validate ruleset
	 */
	public function validate($contexts = [], $data = null) {
		if(!$data) 
			$data = \Input::all();

		$valid = \Amp::validator($data, $this);

		// Allows us to add different contexts for validation
		foreach($contexts as $context) {
			$valid->addContext($context);
		}

		if($valid->fails()) {
			$this->messages = $valid->messages();
			return false;
		}

		return true;
	}

	// Get validation messages
	public function messages() {
		if(!$this->messages) 
			return false;

		return $this->messages;
	}

}