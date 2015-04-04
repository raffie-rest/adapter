<?php

return [
	'hipchat_v1' 	=> [
		'data_type' => 'json',
	    'defaults'  => [
			'base_url'    => 'https://api.hipchat.com/v1',
			'defaults'	  => [
				'query'	 	  => [
					'auth_token' => '',
					'format'	 => 'json'
				]
			]
	    ]
	],
	'pushover_v1'	=> [
		'data_type' => 'json',
	    'defaults'  => [
			'base_url'    	=> 'https://api.pushover.net/1',
			'defaults'	  	=> [
				'query'	 	=> [
					'token' => '',
					'user'  => ''
				]
			]
	    ]
	],
	'postcode'		=> [
		'data_type' => 'json',
		'defaults'  => [
			'base_url'	=> 'https://api.postcode.nl/rest',
			'defaults'	=> [
				'auth'		=> ['', '']
			]
		]
	],
	'kvk'			=> [
		'data_type'	=> 'json',
		'defaults'	=> [
			'base_url' => 'http://officieel.openkvk.nl/json'
		]
	]
];