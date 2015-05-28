<?php

return [



	'device' => [

		/*
		|--------------------------------------------------------------------------
		| provider
		|--------------------------------------------------------------------------
		|
		| `database` and `eloquent` providers are supported
		|
		*/
		'provider' => env('ALEXA_DEVICE_PROVIDER', 'eloquent'),

		/*
		|--------------------------------------------------------------------------
		| model
		|--------------------------------------------------------------------------
		|
		| For *eloquent* provider, which model should be used for a Device. A Device
		| model is provided out of the box, but any other eloquent model can be used
		| as long as it implements the AmazonEchoDevice contract.
		|
		*/
		'model' => env('ALEXA_ELOQUENT_DEVICE_MODEL', 'Develpr\AlexaApp\Device\Device'),

		/*
		|--------------------------------------------------------------------------
		| table
		|--------------------------------------------------------------------------
		|
		| For *database* provider, which table will store your alexa device
		| data?
		|
		*/
		'table' => env('ALEXA_DATABASE_DEVICE_TABLE', 'alexa_devices'),

		/*
		|--------------------------------------------------------------------------
		| device_identifier
		|--------------------------------------------------------------------------
		|
		| What is the attribute or table column name for the unique echo device
		| id? This is the attribute used to look up a specific device via either
		| eloquent or database providers.
		|
		*/
		'device_identifier' => env('ALEXA_DEVICE_ID_ATTRIBUTE', 'device_user_id'),

	],

];
