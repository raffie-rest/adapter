<?php namespace Raffie\REST\Adapter\Adapters\PushOver\v1;

use Raffie\REST\Adapter\Adapters\Base;

/*
|--------------------------------------------------------------------------
| PushOver Message implementation
|--------------------------------------------------------------------------
*/

class Message extends Base 
{
	public $resource      = 'pushover_v1';
	public $relativePath  = 'messages.json';

	/**
	 * Static stub to your regular POST data
	 * 
	 * @param  array 	$data - POST payload
	 * 
	 * @return Message instance
	 */
	public static function send($data)
	{
		return (new static)->post($data);
	}
}