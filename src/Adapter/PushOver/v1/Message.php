<?php namespace Raffie\REST\Adapter\PushOver\v1;

use Raffie\REST\Adapter;

/*
|--------------------------------------------------------------------------
| PushOver Message implementation
| - Rafaël Hebing ©opyleft 2015 -
|--------------------------------------------------------------------------
*/

class Message extends Adapter 
{
	public $resource      = 'pushover_v1';
	public $relativePath  = 'messages.json';

	public static function send($data)
	{
		return (new static)->send($data);
	}

	public function send($data)
	{
		return $this->sendRequest('POST', $data);
	}
}