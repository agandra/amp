<?php namespace Agandra\Amp\Base;

use Agandra\Amp\Base\RepoGetNotImplementedException,
	Agandra\Amp\Base\ModelClassNotSetException,
	Agandra\Amp\Base\ResultNotFoundException;

class AmpRepo {

	/**
	 * Returns the matching id for the model
	 * @param int id (int or array of ints)
	 *
	 * @return \Core\User\Models\User
	**/
	public function find($id) {
		if(!method_exists($this,'query')) {
			throw new RepoGetNotImplementedException();
		}

		return $this->query(['id'=>$id])->get()->first();
	}

	public function findOrFail($id) {
		$result = $this->find($id);

		if(!$result)
			throw new ResultNotFoundException();

		return $result;
	}
	/**
	 * Returns a Builder instance for use in constructing a query
	 *
	 * Example usage:
	 * $result = $this->getQueryBuilder()->find($id);
	 *
	 * @return \Illuminate\Database\Query\Builder
	 */
	protected function getQueryBuilder()
	{
		if(!isset($this->modelClass)) {
			throw new ModelClassNotSetException();
		}

		$modelClass = $this->modelClass;
		return with(new $modelClass)->newQuery();
	}

}