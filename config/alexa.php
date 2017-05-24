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
     | skipCsrfCheck
     |--------------------------------------------------------------------------
     |
     | Should we attempt to skip the CSRF middleware on the AlexaApp routes? For security reasons it may
     | be best to exclude the Alexa/AlexaSkillsKit specific routes in the `VerifyCsrfToken` middleware's `$except` array
     |
     */
    'skipCsrfCheck' => boolval(env('ALEXA_SKIP_CSRF', true)),

    /*
     |--------------------------------------------------------------------------
     | applicationIds
     |--------------------------------------------------------------------------
     |
     | Application IDs for your application(s)
     |
     | @see https://developer.amazon.com/public/solutions/devices/echo/alexa-app-kit/docs/handling-requests-sent-by-the-alexa-service
     |
     */
    'applicationIds' => env('ALEXA__APPLICATION_IDS', []),


    /*
     |--------------------------------------------------------------------------
     | timestampTolerance
     |--------------------------------------------------------------------------
     |
     | This is the amount of time your application should allow pass before
     | considering a request invalid. This is to prevent replay attacks.
     | Note that if this value is set to 0 the timestamp will not be checked
     | which is designed for testing.
     |
     | @see https://developer.amazon.com/public/solutions/devices/echo/alexa-app-kit/docs/developing-your-app-with-the-alexa-appkit
     |
     */
    'timestampTolerance' => env('ALEXA_TIMESTAMP_TOLERANCE', 150),

    /*
    |--------------------------------------------------------------------------
    | origin
    |--------------------------------------------------------------------------
    |
    | These configuration options relate to verifying that the request origin is
    | really Amazon's official AlexaSkillsKit system. Note that while you can change these
    | if you want to make sample/test request from your own Alexa simulator,
    | you can also simply not include the Certificate middleware when testing
    | your application.
    |
    */
    'origin' => [
        /*
        |--------------------------------------------------------------------------
        | host
        |--------------------------------------------------------------------------
        |
        | The valid host the the request origin needs to match with
        | (this probably shouldn't be touched unless you're testing, etc)
        |
        */
        'host' => env('ALEXA_ORIGIN_HOST', 's3.amazonaws.com'),

        /*
        |--------------------------------------------------------------------------
        | path
        |--------------------------------------------------------------------------
        |
        | The valid path the the request origin needs to match with
        | (this probably shouldn't be touched unless you're testing, etc)
        |
        */
        'path' => env('ALEXA_ORIGIN_PATH', '/echo.api/'),


        /*
        |--------------------------------------------------------------------------
        | scheme
        |--------------------------------------------------------------------------
        |
        | The scheme (https is default/correct for real requests the origin needs to match with
        | (this probably shouldn't be touched unless you're testing, etc)
        |
        */
        'scheme' => env('ALEXA_ORIGIN_SCHEME', 'https'),

        /*
        |--------------------------------------------------------------------------
        | port
        |--------------------------------------------------------------------------
        |
        | IF SPECIFIED, which port should the origin request be over?
        | (this probably shouldn't be touched unless you're testing, etc)
        |
        */
        'port' => env('ALEXA_ORIGIN_PORT', '443'),
    ],

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

    /*
    |--------------------------------------------------------------------------
    | prompts
    |--------------------------------------------------------------------------
    |
    | Configuration related to "asking" questions and automatically routing "answers"
    | to the proper intent/configurations
    |
    */
    'prompt' => [

        /*
        |--------------------------------------------------------------------------
        | auto_route_enabled
        |--------------------------------------------------------------------------
        |
        |    Should alexa-app automatically attempt to route Intents matching the configured
        |    Intent to a specified Intent/route?
        |
        */
        'enable' => env('ALEXA_PROMPT_ENABLE', true),

        /*
        |--------------------------------------------------------------------------
        | prompt_response_intent
        |--------------------------------------------------------------------------
        |
        |    The Intent that will be used to route your responses to the specified Intent
        |
        */
        'response_intent' => env('ALEXA_PROMPT_RESPONSE_INTENT', 'PromptResponse'),
    ],

    /*
    |--------------------------------------------------------------------------
    | device
    |--------------------------------------------------------------------------
    |
    | Configuration related to the persistence of the Alexa device
    |
    */
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

    /*
    |--------------------------------------------------------------------------
    | audio
    |--------------------------------------------------------------------------
    |
    | Configuration related to playing audio
    |
    */
    'audio' => [
        /*
        |--------------------------------------------------------------------------
        | proxy
        |--------------------------------------------------------------------------
        |
        | Amazon doesn't accept HTTP audio files. If you set a proxy, all audio
        | files are routed via a proxy which encapsulates the audio file in a m3u
        | playlist that can be served via HTTPS.
        |
        */
        'proxy' => [
            'enabled' => env('ALEXA_AUDIO_PROXY_ENABLED', 'false'),
            'route' => env('ALEXA_AUDIO_PROXY_ROUTE', '/alexa/audio/proxy')
        ]
    ]

];
