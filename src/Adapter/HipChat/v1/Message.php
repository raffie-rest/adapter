<?php namespace Raffie\REST\Adapter\HipChat\v1;

use Raffie\REST\Adapter;

/*
|--------------------------------------------------------------------------
| HipScat Message implementation
| - Rafaël Hebing ©opyleft 2015 -
|--------------------------------------------------------------------------
*/

class Message extends Adapter 
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