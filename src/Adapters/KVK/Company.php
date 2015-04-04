<?php namespace Raffie\REST\Adapter\Adapters\KVK;

use Raffie\REST\Adapter\Adapters\Base;

/*
|--------------------------------------------------------------------------
| OpenKVK.nl implementation
|--------------------------------------------------------------------------
*/

class Company extends Base 
{
	public $resource      = 'kvk';
	public $relativePath  = 'json';

	/**
	 * Static stub to your regular GET data
	 * 
	 * @param  array 	$data - GET args
	 * 
	 * @return array tha data
	 */
	public static function search($company)
	{
		return (new static)->get($company);
	}
}