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

		'enable' => boolval(env("ALEXA_ENABLE_DEVICE", true)),

		'model' => env('ALEXA_DEVICE_MODEL', 'Develpr\AlexaApp\Domain\Device'),

	],

	'facadeAlias' => env('ALEXA_FACADE_ALIAS', 'Alexa')

];
