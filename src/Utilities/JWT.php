<?php namespace Raffie\REST\Utilities;

/*
|--------------------------------------------------------------------------
| JSON Web Token Generator
|--------------------------------------------------------------------------
|
| Intended for use in conjunction with the Google oAuth2 auth method
|
|  - http://jwt.io/
|
| Raffie ©opyleft 2015 - If you remove this message I will astrally skull fuck you
|*/

use RuntimeException,
	  InvalidArgumentException;

use DateTime,
	  DateTimeZone;

class JWT 
{
  /**
   * Initialized config
   * 
   * @var array
   */
  protected $config = [];

  /**
   * Required config keys
   *
   * Additional testing is done on 'em in initializeConfig
   * 
   * @var array
   */
  protected $config_values = [
    'key'        => 'array with path / passphrase',
    'header'     => 'JWT header',
    'claim_set'  => 'JWT claim set',
    //'scopes'     => 'array with oAuth2 scopes'
  ];

  /**
   * Required config subs
   *
   * Additional testing is done on 'em in initializeConfig
   * 
   * @var array
   */
  protected $config_subs = [
    'key'   => [
      'path'  => 'path to .pem file'
    ],
    'claim_set' => [
      'iss'   => 'issuer claim',
      'scope' => 'scope',
      'exp'   => 'expires',
      'aud'   => 'audience'
    ]
  ];

  /**
   * Minimum PHP version required
   * 
   * @var string
   */
  public static $minimumPHPVersion = '5.4.8';

  /**
   * Used for computing the signature
   *
   * - http://php.net/manual/en/openssl.signature-algos.php
   * 
   * @var constant
   */
  public static $signature_alg = OPENSSL_ALGO_SHA256;

  /**
   * The default grant type employed
   * 
   * @var string
   */
  public static $grant_type = 'urn:ietf:params:oauth:grant-type:jwt-bearer';

  /**
   * The scopes on which audience has to be asserted
   * 
   * @var array
   */
  protected $scopes = [];

  /**
   * Constructor
   * 
   * @param array $config
   */
  public function __construct(array $config, $compatibilityTest = false)
  {
    if($compatibilityTest != false)
    {
      $this->runCompatibilityTest(false);
    }
    
    $this->config = $this->initializeConfig($config);
  }

  /**
   * Display testRunAbility feedback
   * 
   * @return string
   */
  public function runCompatibilityTest($feedback = false)
  {
    return $this->testCompatibility($feedback);
  }

  /**
   * Test if this web server can run this utility
   *
   * (requires OpenSSL / PHP [5.4.8 and up])
   *
   * Throws RuntimeException on fail and feedback == false
   * 
   * @return string(feedback==true)|void(false)
   */
  protected function testCompatibility($feedback = false)
  {
    $hasOpenSSL  = function_exists('openssl_sign') and function_exists('openssl_pkey_get_private');
    $hasRightPHP = version_compare(PHP_VERSION, static::$minimumPHPVersion) > -1;

    if($hasRightPHP && ! $feedback)
    {
      throw new RuntimeException('You need at least PHP ' . static::$minimumPHPVersion);
    }
    if($hasOpenSSL && ! $feedback)
    {
      throw new RuntimeException('You need OpenSSL support for this');
    }
    if($feedback)
    {
      $msg = 'Current PHP version: ' . PHP_VERSION . "\n";
      $msg .= 'Required: ' . static::$minimumPHPVersion . "\n";
      $msg .= 'Check: ' . ($hasRightPHP ? 'OK' : 'FAIL') . "\n";
      $msg .= 'OpenSSL support: ' . ($hasOpenSSL ? 'OK' : 'FAIL');
      
      return $msg;
    }
  }

  /**
   * Validate and transform the config array
   *
   * (requires OpenSSL / PHP [5.4.8 and up])
   *
   * Throws InvalidArgumentException on fail
   *
   * @param array $config
   * 
   * @return void
   */
  protected function initializeConfig(array $config)
  {
    foreach($this->config_values as $key => $value)
    {
      if( ! array_key_exists($key, $config))
        throw new InvalidArgumentException('No ' . $value . ' set');

      if( ! array_key_exists($key, $this->config_subs) || ! is_array($this->config_subs[$key])) 
        continue;

      foreach($this->config_subs[$key] as $subKey => $subValue)
      {
        if( ! array_key_exists($subKey, $config[$key])) 
          throw new InvalidArgumentException('No ' . $subValue . ' set in ' . $value);
      }
    }

    if( ! array_key_exists('pass', $config['key']))
    {
      $config['key']['pass'] = null;
    }

    //$this->addScopes($config['claim_set']['scopes']);

    //$config['claim_set']['exp']   = static::getTimestamp($config['claim_set']['exp']);

    //$config['claim_set']['scope'] = $this->getScope();

    return $config;
  }

  /**
   * Get the current scope attribute
   * 
   * @return string
   */
  public function getScope()
  {
    return join(' ', $this->scopes);
  }
  
  /**
   * Pushes new scopes
   * 
   * @param mixed $scopes - oAuth2 scopes
   */
  public function addScopes($scopes)
  {
    if( ! is_array($scopes))
    {
      array_push($this->scopes, $scopes);
      return;
    }
    $this->scopes = array_merge($this->scopes, $scopes);
  }

  /**
   * Generates and returns the JWT
   *
   * Throws RuntimeException on fail
   * 
   * @return string
   */
  public function generate()
  {
    $encodedHeader   = $this->getHeader();
    $encodedClaimSet = $this->getClaimSet();

    $signaturePlain = $encodedHeader . '.' . $encodedClaimSet;

    $signatureEnc   = $this->getEncodedSignature($signaturePlain);

    $jwt = $encodedHeader . '.' . $encodedClaimSet . '.' . $signatureEnc;

    return $jwt;
  }

  /**
   * Generates and returns the JWT base64 encoded header
   *
   * Throws RuntimeException on fail
   * 
   * @return string
   */
  public function getHeader($encode = true)
  {
    $header = $this->config['header'];

    if( ! $encode) return $header;

    return $this->encodeData($header, 'header');
  }

  /**
   * Generates and returns the JWT base64 encoded claim set
   *
   * Throws RuntimeException on fail
   * 
   * @return string
   */
  public function getClaimSet($encode = true)
  {
    $claimSet = $this->config['claim_set'];

    $claimSet['iat'] = static::getTimestamp();
    
    if(array_key_exists('exp', $claimSet))
    {
      $claimSet['exp'] = is_integer($claimSet['exp']) ? $claimSet['exp'] : static::getTimestamp($claimSet['exp']);
    }

    if( ! $encode) return $claimSet;

    return $this->encodeData($claimSet, 'claim_set');
  }

  /**
   * JSON encode array as object and base64 encode it
   * 
   * @param  array  $data
   * @param  string $type - config key
   * 
   * @return string
   */
  protected function encodeData($data, $type = 'header')
  {
    $object = (object) $data;

    $json = json_encode($object);

    if(json_last_error() != JSON_ERROR_NONE)
    {
      $msg  = 'Failed to JSON encode ' . $this->config_values[$type];
      $msg .="\n" . json_last_error_msg();
      
      throw new InvalidArgumentException($msg);
    }

    return base64_encode($json);
  }

  /**
   * OpenSSL signs the JWT signature and returns it base64 encoded
   *
   * Throws RuntimeException on fail
   * 
   * @return string
   */
  protected function getEncodedSignature($signaturePlain)
  {
    $signatureEncrypted = false;

    $privateKey = $this->getPrivateKey();

    openssl_sign($signaturePlain, $signatureEncrypted, $privateKey, static::$signature_alg);

    if(empty($signatureEncrypted))
    {
      throw new RuntimeException('Failed to generate signature');
    }

    openssl_pkey_free($privateKey);

    return base64_encode($signatureEncrypted);
  }

  /**
   * Generates a UTC timestamp for use in the claim set
   *
   * Might throw a bunch of stuff
   *
   * @param  $str   - valid datetime.format (e.g. '+1 hour')
   * 
   * @return int
   */
  public static function getTimestamp($str = null)
  {
    $old = empty($str) ? time() : strtotime($str);

    $new = new DateTime;

    return $new->setTimezone(new DateTimeZone(date_default_timezone_get()))
           ->setTimestamp($old)
           ->setTimezone(new DateTimeZone('UTC'))
           ->getTimestamp();
  }

  /**
   * Retrieves the private key for signing the signature
   * (freed after signing process)
   * 
   * Throws RuntimeException on fail
   * 
   * @return resource
   */
  protected function getPrivateKey()
  {
    $privateKey = openssl_pkey_get_private($this->config['key']['path'], $this->config['key']['pass']);

    if(empty($privateKey))
    {
      throw new RuntimeException('Failed to retrieve private key');
    }

    return $privateKey;
  }
}