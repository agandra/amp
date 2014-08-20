<?php namespace Agandra\Amp\API\Models;

use Agandra\Amp\Base\AmpModel;

class APIToken extends AmpModel {

	protected $table = 'api_tokens';

	protected $fillable = [
        'device_id',
        'user_id',
        'token'
    ];
	
	public $rules = [
		'device_id' => 'Required|AlphaNum',
		'user_id' => 'Required'
	];

}
