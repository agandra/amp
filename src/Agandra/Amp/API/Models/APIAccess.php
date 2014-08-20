<?php namespace Agandra\Amp\API\Models;

use Agandra\Amp\Base\AmpModel;

class APIAccess extends AmpModel {

	protected $table = 'api_access';
	public $timestamps = false;
	protected $fillable = ['name', 'public_key', 'secret'];

}
