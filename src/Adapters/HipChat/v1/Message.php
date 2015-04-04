<?php namespace Raffie\REST\Adapter\Adapters\HipChat\v1;

use Raffie\REST\Adapter\Adapters\Base;

/*
|--------------------------------------------------------------------------
| HipScat Message implementation
| - Rafaël Hebing ©opyleft 2015 -
|--------------------------------------------------------------------------
*/

class Message extends Base 
{
	public $resource      = 'hipchat_v1';
	public $relativePath  = 'rooms/message';

	public static function send($data)
	{
		return (new static)->send($data);
	}

	public function send($data)
	{
		return $this->sendRequest('POST', $data);
	}
}