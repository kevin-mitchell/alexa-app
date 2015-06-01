<?php

return [

	/*
	 |--------------------------------------------------------------------------
	 | verifyAppId
	 |--------------------------------------------------------------------------
	 |
	 | Should the application verify that the incoming appId matches your appId?
	 |
	 | @see https://developer.amazon.com/public/solutions/devices/echo/alexa-app-kit/docs/handling-requests-sent-by-the-alexa-service
	 |
	 */
	'verifyAppId' => env('ALEXA_VERIFY_APP_ID', true),

	/*
	 |--------------------------------------------------------------------------
	 | appIds
	 |--------------------------------------------------------------------------
	 |
	 | Application IDs for your application(s)
	 |
	 | @see https://developer.amazon.com/public/solutions/devices/echo/alexa-app-kit/docs/handling-requests-sent-by-the-alexa-service
	 |
	 */
//	'appIds' => env('ALEXA_POSSIBLE_APP_IDS', []),
	'appIds' => env('ALEXA_POSSIBLE_APP_IDS', ["amzn1.echo-sdk-ams.app.9ec3744a-d1b2-48f2-8e08-3b2045c00616"]),


	'certificate' => [

		/*
		|--------------------------------------------------------------------------
		| provider
		|--------------------------------------------------------------------------
		|
		| How should the certificate be stored?
		| `file`, `redis`, `database` and `eloquent` providers are supported
		|
		*/
		'provider' => env('ALEXA_CERTIFICATE_PROVIDER', 'file'),

		/*
		|--------------------------------------------------------------------------
		| filePath
		|--------------------------------------------------------------------------
		|
		| Where should the cert file be saved if downloaded with the `file` provider
		|
		*/
		'filePath' => env('ALEXA_CERTIFICATE_FILE_PATH', storage_path('certificates/')),

	],


	'device' => [

		/*
		|--------------------------------------------------------------------------
		| provider
		|--------------------------------------------------------------------------
		|
		| How should the device be accessed? `database` and `eloquent` providers are supported
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
