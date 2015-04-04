<?php namespace Raffie\REST\Adapter\Adapters;

use Raffie\REST\Adapter\Adapters\Base;

/*
|--------------------------------------------------------------------------
| Postcode.nl search implementation
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