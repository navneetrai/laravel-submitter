<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Submission Config
	|--------------------------------------------------------------------------
	*/

	/**
	 * Services
	 */
	'services' => [

		'facebook' => [
			'client_id'     => '',
			'client_secret' => '',
			'scope'         => ['email', 'public_profile', 'publish_actions'],
		],

		'twitter' => [
		    'client_id'     => '',
		    'client_secret' => '',
		    // No scope - oauth1 doesn't need scope
		],

		'youtube' => [
		    'client_id'     => '',
		    'client_secret' => '',
		    'scope'         => [
							    'profile', 'email',
							    'https://www.googleapis.com/auth/youtube',
								'https://www.googleapis.com/auth/youtube.upload',
								'https://www.googleapis.com/auth/youtube.readonly'
								],
		],


	]

];