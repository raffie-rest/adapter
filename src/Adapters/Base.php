<?php namespace Raffie\REST\Adapter\Adapters;

use InvalidArgumentException,
	RuntimeException;

use Config;

use Illuminate\Support\MessageBag;

use GuzzleHttp\Client,
	GuzzleHttp\Message\Response;

use GuzzleHttp\Exception\ClientException,
	GuzzleHttp\Exception\ServerException,
	GuzzleHttp\Exception\RequestException;

use Raffie\REST\Adapter\Interfaces\DelegateInterface;

/*
|--------------------------------------------------------------------------
| Intermediate class for working with REST in L5
|
| Raffie ©opyleft 2015 - If you remove this message I will astrally skull fuck you
|
|--------------------------------------------------------------------------
*/

abstract class Base 
{
	// References the corresponding key in rest_resources.php

	public $resource  		= '';

	// can be json, xml or plain, determines output format

	public $dataType  		= 'json';

	// available data types for transmission / parsing

	public $availableDataTypes = [
		'xml', 
		'json',
		'plain'
	];

	// This little piggy must be specified
	public $relativePath 	= '';

	// These little piggies get filled automagically
	public $absolutePath	= '';
	public $requestPath 	= '';

	// The data originally forked to the adapter

	protected $originalData = [];
	
	// Above data translated to ready2swallow request array

	protected $requestData  = [
		//body
		//json
		'headers'	=> [
			//e.g. ; don't register headers here
			'X-Powered-By'	=> 'Pietje Puk'
		]
	];

	// Required config attributes, throws InvalidArgumentException when not present

	public $requiredConfigValues = [
		'defaults.base_url'
	];

	// Available HTTP request types

	public $requestTypes = [
		'GET', 
		'POST', 
		'PUT', 
		'DELETE', 
		'HEAD', 
		'OPTIONS'
	];

	// For the retrieved config array

	protected $config 	= [];

	// Client instance

	protected $client 	= false;

	// Determines as to whether or not to use the delegate handles for exceptions

	protected $delegate = false;

    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

	public function __construct(DelegateInterface $delegate = null)
	{
		$this->setConfig();
		$this->setDataType();
		$this->setAbsolutePath();

		if($delegate != null)
		{
			$this->delegate = $delegate;
		}
	}

    /*
    |--------------------------------------------------------------------------
    | Public Static
    |--------------------------------------------------------------------------
    */

    /**
     * This ones for above mentioned request types
     *
     * See also:
     *
     * http://php.net/manual/en/language.oop5.overloading.php
     * 
     * @param  string $name       method called
     * @param  array  $arguments  arguments padded
     * 
     * @return mixed  Response Data
     */
	public static function __callStatic($name, $arguments)
	{
		$name = strtoupper($name);

		return (new static)->sendRequest($name, $arguments);
	}

	public function __call($name, $arguments)
	{
		$name = strtoupper($name);
		
		$this->setRequestMethod($name);

		return $this->sendRequest($name, $arguments);
	}

    /*
    |--------------------------------------------------------------------------
    | Magickal functions
    |--------------------------------------------------------------------------
    */

    /**
     * [newClient description]
     * @return [type] [description]
     */
	protected function newClient()
	{
		if(empty($this->config))
		{
			throw new InvalidArgumentException('REST config is empty');
		}

		return new Client($this->config['defaults']);
	}

	/**
	 * [validateConfig description]
	 * @param  array  $config [description]
	 * @return [type]         [description]
	 */
	protected function validateConfig(array $config = [])
	{
		if(empty($config))
		{
			throw new InvalidArgumentException('No config values found');
		}

		foreach($this->requiredConfigValues as $key)
		{
			$config = 'rest_resources.'.$this->resource.'.'.$key;
			$value  = Config::get($config);

			if( ! $config) throw new InvalidArgumentException('Missing required value ['.$config.']');
		}
	}

	/**
	 * [sendRequest description]
	 * @param  string $method    [description]
	 * @param  array  $arguments [description]
	 * @return [type]            [description]
	 */
	public function sendRequest($method = 'GET', array $arguments = [])
	{
		$closure = function() use($method, $arguments) {

			$request = $this->prepareRequest($method, $arguments);

			$response = $this->getClient()->send($request);

			return $this->parseResponse($response);
		};

		if( ! $this->delegate)
		{
			return $closure();
		}

		try
		{
			$response        = $closure();
			$parsedResponse  = $this->parseResponse($response);

			return $this->delegate->requestSucceeds($parsedResponse);
		}
		catch(RequestException $e)
		{
			$response        = $e->getResponse();
			$parsedResponse  = $this->parseResponse($response);

			return $this->delegate->requestFails(new MessageBag(['errors' => $parsedResponse]));
		}
		/*
		catch(InvalidArgumentException $e)
		{
			$message = $e->getMessage();

			return $this->delegate->requestFails(new MessageBag([$message]));
		}
		*/
	}

	/**
	 * [prepareRequest description]
	 * @param  string $method    [description]
	 * @param  array  $arguments [description]
	 * @return [type]            [description]
	 */
	protected function prepareRequest($method = 'GET', array $arguments = [])
	{
		$dataHad    = false;

		for($i = 0; $i < sizeof($arguments); $i++)
		{
			if( ! is_array($arguments[$i]))
			{
				$this->appendRequestPath($arguments[$i]);
				continue;
			}
			if($dataHad)
			{
				$this->setRequestHeaders($arguments[$i]);
				continue;
			}

			$this->setRequestData($arguments[$i]);
			$dataHad = true;
		}

		return $this->getClient()->createRequest($method, $this->requestPath, $this->requestData);
	}

	/**
	 * [parseResponse description]
	 * @param  [type] $response [description]
	 * @return [type]           [description]
	 */
	protected function parseResponse($response)
	{
		if( ! $response instanceof Response)
		{
			return $response;
		}
		if($this->dataType == 'json')
		{
			return $response->json();
		}
		else if($this->dataType == 'xml')
		{
			return $response->xml();
		}
		return $response->getBody();
	}

	/**
	 * [parseErrorResponse description]
	 * @param  GuzzleResponse $response [description]
	 * @return [type]                   [description]
	 */
	protected function parseErrorResponse($response)
	{
		return $this->parseResponse($response);
	}

    /*
    |--------------------------------------------------------------------------
    | Setters
    |--------------------------------------------------------------------------
    */

    /**
     * Getters
     * 
     * @return GuzzleHttp\Client
     */
	protected function getClient()
	{
		if( ! $this->client)
		{
			$this->client = $this->newClient();
		}

		return $this->client;
	}

    /*
    |--------------------------------------------------------------------------
    | Setters
    |--------------------------------------------------------------------------
    */

    /**
     * Sets config based on the resource attribute
     * 
     * @param string $resource
     */
	protected function setConfig()
	{
		if(empty($this->resource))
		{
			throw new InvalidArgumentException('No REST resource specified');
		}

		$config = Config::get('rest_resources.' . $this->resource);

		$this->validateConfig($config);

		$this->config = $config;
	}

	/**
	 * [setRequestMethod description]
	 * @param string $method [description]
	 */
	protected function setRequestMethod($method = 'GET')
	{
		if( ! in_array($method, $this->requestTypes))
		{
			throw new InvalidArgumentException('Please specify a valid request type [' . join(', ', $this->requestTypes) . ']');
		}

		$this->requestMethod = $method;
	}

	/**
	 * [setRequestData description]
	 * @param [type] $data [description]
	 */
	protected function setRequestData($data)
	{
		$this->originalData = $data;

		$this->requestData['body'] = $data;
	}

	/**
	 * [setRequestHeaders description]
	 * @param array $headers [description]
	 */
	protected function setRequestHeaders(array $headers = [])
	{
		if( ! empty($this->config['defaults']['headers']))
		{
			$headers = array_merge($this->config['defaults']['headers'], $headers);
		}

		$this->requestData['headers'] = $headers;
	}


	/**
	 * [appendRequestPath description]
	 * @param string $segment [description]
	 */
	protected function appendRequestPath($segment = '')
	{
		$first = substr($segment, 0, 1);

		if( ! empty($segment))
		{
			if( ! in_array($first, ['?', '/'])) $this->requestPath .= '/'; 
			$this->requestPath .= $segment;
		}
	}

	/**
	 * [setAbsolutePath description]
	 */
	protected function setAbsolutePath()
	{
		$this->absolutePath  = $this->config['defaults']['base_url'];

		$absPath = $this->absolutePath;

		if( ! in_array($this->relativePath, ['?', '/'])) $absPath .= '/'; 

		$absPath .= $this->relativePath;

		$this->requestPath = $absPath;
	}

	/**
	 * [setDataType description]
	 * @param string $type [description]
	 */
	protected function setDataType($type = '')
	{
		if(empty($type) && ! empty($this->config['data_type']))
		{
			$type = $this->config['data_type'];
		}
		if( ! in_array($type, $this->availableDataTypes))
		{
			throw new InvalidArgumentException('Please specify a valid data type [' .join(', ', $this->availableDataTypes). ']');
		}

		$this->dataType = $type;
	}
}