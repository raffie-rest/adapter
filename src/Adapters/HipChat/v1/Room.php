<?php namespace Raffie\REST\Adapter\Adapters\HipChat\v1;

use Raffie\REST\Adapter\Adapters\Base;

/*
|--------------------------------------------------------------------------
| HipScat room implementation
| - Rafaël Hebing ©opyleft 2015 -
|--------------------------------------------------------------------------
*/

class Room extends Base 
{
	public $resource      = 'hipchat_v1';
	public $relativePath  = 'rooms';

	public static function list()
	{
		return (new static)->get('list');
	}

	public function list()
	{
		return $this->sendRequest('GET', 'list');
	}
}