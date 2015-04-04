<?php namespace Raffie\REST\Adapter\Adapters\HipChat\v1;

use Raffie\REST\Adapter\Adapters\Base;

/*
|--------------------------------------------------------------------------
| HipScat room implementation
|--------------------------------------------------------------------------
*/

class Room extends Base 
{
	public $resource      = 'hipchat_v1';
	public $relativePath  = 'rooms';

	/**
	 * Static stub to your regular GET data
	 * 
	 * @param  array 	$data - GET args
	 * 
	 * @return array of rooms
	 */
	public static function list()
	{
		return (new static)->get('list');
	}
}