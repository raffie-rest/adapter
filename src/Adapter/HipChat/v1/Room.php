<?php namespace Raffie\REST\Adapter\HipChat\v1;

use Raffie\REST\Adapter;

/*
|--------------------------------------------------------------------------
| HipScat room implementation
| - Rafaël Hebing ©opyleft 2015 -
|--------------------------------------------------------------------------
*/

class Room extends Adapter 
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