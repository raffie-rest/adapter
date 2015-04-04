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

	/**
	 * Static stub to your regular GET data
	 * 
	 * @param  args - GET query string
	 * 
	 * @return Postcode instance
	 */
	public static function search($postcode, $housenumber, $housenumber_addition = '')
	{
		return (new static)->get($postcode, $housenumber, $housenumber_addition);
	}
}