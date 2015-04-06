<?php namespace Raffie\REST\Adapter\Adapters\PushOver\v1;

use Raffie\REST\Adapter\Adapters\Base;

/*
|--------------------------------------------------------------------------
| PushOver Receipt implementation
|--------------------------------------------------------------------------
*/

class Receipt extends Base 
{
	public $resource      = 'pushover_v1';
	public $relativePath  = 'receipts';

	/**
	 * Static stub to a GET action
	 * 
	 * @param  string $receipt - The receipt returned by the pushover messages API
	 * 
	 * @return Response
	 */
	public static function retrieve($receipt)
	{
		return (new static)->get($receipt . '.json');
	}
}