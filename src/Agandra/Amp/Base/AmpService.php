<?php namespace Agandra\Amp\Base;

use Agandra\Amp\Reply;

class AmpService {

	public function reply() {
		return new Reply;
	}

	/**
	 * Call the service method as a queue
	 * @todo need to test this with actual queue system (beanstalk, etc)
	 */
	public function queue() {
		$args = func_get_args();

		$method = $args[0];

		array_shift($args);

		if(!method_exists($this, $method)) {
			throw new \Agandra\Amp\Base\ServiceMethodNotFound;
		}

		// Cant pass $this in a closure
		$that = $this;

		\Queue::push(function($job) use ($that, $method, $args) {
			call_user_func_array([$that, $method], $args);
			$job->delete();
		});
		
	}

}