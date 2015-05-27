<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Alexa Auth Model
	|--------------------------------------------------------------------------
	|
	| Which model should be used to store Alexa information
	|
	*/

	'device' => [

		'enable' => env("ALEXA_ENABLE_DEVICE", true),

		'provider' => env('ALEXA_DEVICE_PROVIDER', 'eloquent'),

		'model' => env('ALEXA_ELOQUENT_DEVICE_MODEL', 'Develpr\AlexaApp\Device\Device'),

		'table' => env('ALEXA_DATABASE_DEVICE_TABLE', 'alexa_devices'),

		'device_identifier' => env('ALEXA_DEVICE_ID_ATTRIBUTE', 'device_user_id'),

		'auto_create_device' => env('ALEXA_AUTO_CREATE_DEVICE', false),

	],

	'facadeAlias' => env('ALEXA_FACADE_ALIAS', 'Alexa')

];
