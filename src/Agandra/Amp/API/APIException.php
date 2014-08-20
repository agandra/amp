<?php namespace Agandra\Amp\API;

use Agandra\Amp\API\API;
use Agandra\Amp\Reply;

class APIException extends \Exception {}

\App::error(function(\Core\API\APIException $e, $code, $fromConsole) {
	$api = new API();
	$reply = new Reply;
	return $api->respond($reply->fails('APIException', $e->getMessage()), $code);
});