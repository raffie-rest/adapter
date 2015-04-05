# Laravel 5 Remote REST adapter

## Table of contents

- [Introduction](#introduction)
- [Future development](#future)
- [Getting started](#getting-started)
- [Supported methods](#supported-methods)
- [Examples](#examples)
  - [From a Laravel 5 ShouldBeQueued, statically](#static)
  - [From a controller or command, with delegate handles](#delegate)
- [Implementing your own Remote REST end-points](#implement)

## <a name="introduction"></a> Introduction
I built this thing on top of guzzle mainly to facilitate remote REST calls from a Laravel 5 application in a more, "abstracted", manner. In L4 and prior, there were all sorts of neat packages and abstractions that seemed to do this well enough, although the L5 transition has rendered many of them incompatible with the new framework.

Although I will never be able to repay the tremendous debt I owe to the creators of the Laravel Framework, I thought the least I could do was share this.

Bear in mind that it is still being developed / bug tested. Any help / feedback is most welcome:

- [E-mail](raffie-rest@gmail.com)
- [Issues](https://github.com/raffie-rest/adapter/issues)

*****

## <a name="future"></a> Future development

### Incorporate Google oAuth2 Service Account auth
 
I already got this set up in another custom Remote REST implementation using this [Custom JWT Utility](https://github.com/raffie-rest/jwt), I wanna incorporate it as a Guzzle authentication method in this project also.

### Unit testing

It's not there yet, as it heavily depends on the API employed.

### Develop a base model / trait for mapping unto `Eloquent\Model` events

Not sure whether it is necessary

*****

## <a name="getting-started"></a> Getting started

Add the package to your repo:

	"raffie-rest/adapter": "dev-master"
	
Register the service provider - `config/app.php`:

	'Raffie\REST\Adapter\AdapterServiceProvider'
	
Publish config file:

	php artisan vendor:publish

Amend it - `config/rest_resources.php`:

		'pushover_v1'	=> [
		'data_type' => 'json',
	    'defaults'  => [
			'base_url'    	=> 'https://api.pushover.net/1',
			'defaults'	  	=> [
				'query'	 	=> [
					'token' => '',		// Input your created app token here
					'user'  => ''		// User / delivery group token
				]
			]
	    ]

As you can see, as far as the defaults are concerned, it's all Guzzle. 
 
- [Read up on Guzzle](http://guzzle.readthedocs.org/en/latest/)
- [Guzzle API Docs](http://api.guzzlephp.org/)
	
That's it, you're all set! No wait...

*****

## <a name="supported-methods"></a> Supported methods

Things like:

	Foo::get()
	Foo::get(1)
	Foo::get(1, 'addresses')
	Foo::get(1, 'addresses', 2)
	Foo::post([])
	Foo::post(1, 'addresses' , [])
	Foo::put(1, [])
	Foo::put(1, 'addresses', 2, [])
	Foo::delete(1)
	Foo::delete(1, 'addresses', 2)

Notice the resemblance to Laravel 5 Resourceful controller URI format?

The same principal applies to non-statics also.

Supported HTTP request types:

- GET
- POST
- PUT
- DELETE
- HEAD
- OPTIONS

*****

## <a name="examples"></a> Examples
 
### <a name="static"></a> From a Laravel 5 ShouldBeQueued, statically

`App\Commands\SendPushOver.php`	:
	
	use Raffie\REST\Adapter\Adapters\PushOver\v1\Message;

	class SendPushover extends Command implements SelfHandling, ShouldBeQueued 
	{
		use InteractsWithQueue, SerializesModels;
	
		protected $message = [
		    'title'     => 'Something went wrong',
		    'message'   => 'Comrade Leader, something went wrong',
		    'url'       => 'http://foo.com',
		    'url_title' => 'Foo',
		    'priority'  => 0,
		    'sound'     => 'bugle'
	    ];
	
		/**
		 * Create a new command instance.
		 *
		 * @return void
		 */
		public function __construct(array $data = [])
		{
			$this->message = $data;
			//
		}
	
		/**
		 * Execute the command.
		 *
		 * @return void
		 */
		public function handle()
		{
			//
			$message = Message::send($this->message);
			
			return $message;
		}

`Foo.php`:

	Queue::bulk([new SendPushover($message)]);

*****

### <a name="delegate"></a> From a controller / command, with delegate handles

Implement the DelegateInterface and go from there:

`PushOverSend.php`:

	use Raffie\REST\Adapter\Adapters\PushOver\v1\Message,
		Raffie\REST\Adapter\Interfaces\DelegateInterface;
	
	class PushOverSend extends Command implements DelegateInterface {
	
		protected $name = 'pushover:send';
	
		protected $description = 'Sends a test push';
	
		protected $message = [
			'title'     => 'Something went wrong',
			'message'   => 'Comrade Leader, something went wrong',
			'url'       => 'http://foo.com',
			'url_title' => 'Foo',
			'priority'  => 0,
			'sound'     => 'bugle'
		];
	
		public function fire()
		{
			$message = new Message($this);
			return $message->post($this->message);
		}
	
	    /**
	     * Request Succeeds
	     * 
	     * @param  mixed $data
	     * @return mixed
	     */
		public function requestSucceeds($data)
		{
			$this->info($data);
		}
		
	    /**
	     * Request Fails
	     * 
	     * @param  Illuminate\Support\MessageBag  $errors
	     * @return mixed
	     */
		public function requestFails(MessageBag $errors)
		{
			foreach($errors->all() as $error)
			{
				$this->error($error);
			}
		}
	}	

*****

## <a name="implement"></a> Implementing your own Remote REST end-points

Easy peasy lemon squeezy, with everything being inherited from its abstract parent:

`Postcode.php`:

	<?php namespace Raffie\REST\Adapter\Adapters;
	
	use Raffie\REST\Adapter\Adapters\Base;
	
	/*
	|--------------------------------------------------------------------------
	| Postcode.nl search implementation
	|--------------------------------------------------------------------------
	*/
	
	class Postcode extends Base 
	{
		public $resource      = 'postcode';	// Corresponds to the rest_resources config key
		public $relativePath  = 'addresses';	// Set the relative path for your resource
	
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
	
Make sure you amend the config file with corresponding settings:

`config/rest_resources.php`:

	'postcode'		=> [
		'data_type' => 'json',		// plain, xml, json
		'defaults'  => [			// guzzle defaults
			'base_url'	=> 'https://api.postcode.nl/rest',
			'defaults'	=> [
				'auth'		=> ['user', 'pass']
			]
		]
	],
