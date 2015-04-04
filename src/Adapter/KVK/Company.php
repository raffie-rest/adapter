<?php namespace Raffie\REST\Adapter\KVK;

use Raffie\REST\Adapter;

/*
|--------------------------------------------------------------------------
| OpenKVK.nl implementation
| - Rafaël Hebing ©opyleft 2015 -
|--------------------------------------------------------------------------
*/

class Company extends Adapter 
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