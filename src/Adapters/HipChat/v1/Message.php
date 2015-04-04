<?php namespace Raffie\REST\Adapter\Adapters\HipChat\v1;

use Raffie\REST\Adapter\Adapters\Base;

/*
|--------------------------------------------------------------------------
| HipScat Message implementation
|--------------------------------------------------------------------------
*/

class Message extends Base 
{
	public $resource      = 'hipchat_v1';
	public $relativePath  = 'rooms/message';

	/**
	 * Static stub to your regular POST data
	 * 
	 * @param  array 	$data - POST payload
	 * 
	 * @return array response data
	 */
	public static function send($data)
	{
		return (new static)->post($data);
	}
}