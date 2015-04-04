<?php namespace Raffie\REST\Adapter\Adapters\PushOver\v1;

use Raffie\REST\Adapter\Adapters\Base;

/*
|--------------------------------------------------------------------------
| Postcode.nl search implementation
| - Rafaël Hebing ©opyleft 2015 -
|--------------------------------------------------------------------------
*/

class Postcode extends Base 
{
	public $resource      = 'postcode';
	public $relativePath  = 'addresses';

	public static function search($postcode, $housenumber, $housenumber_addition = '')
	{
		return (new static)->search($postcode, $housenumber, $housenumber_addition);
	}

	public function search($postcode, $housenumber, $housenumber_addition = '')
	{	
		return $this->sendRequest('GET', $postcode, $housenumber, $housenumber_addition);
	}
}