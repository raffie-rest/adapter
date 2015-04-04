<?php namespace Raffie\REST\Adapter\Adapters\KVK;

use Raffie\REST\Adapter\Adapters\Base;

/*
|--------------------------------------------------------------------------
| OpenKVK.nl implementation
| - Rafaël Hebing ©opyleft 2015 -
|--------------------------------------------------------------------------
*/

class Company extends Base 
{
	public $resource      = 'kvk';
	public $relativePath  = 'json';

	public static function search($company)
	{
		return (new static)->search($company);
	}

	public function search($company)
	{
		return $this->sendRequest('GET', $company);
	}
}