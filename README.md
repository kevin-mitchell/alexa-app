# AlexaApp

[![Latest Version](https://img.shields.io/packagist/v/develpr/alexa-app.svg?style=flat-square)](https://packagist.org/packages/develpr/alexa-app)
[![Total Downloads](https://img.shields.io/packagist/dt/develpr/alexa-app.svg?style=flat-square)](https://packagist.org/packages/develpr/alexa-app)
[![Software License](https://img.shields.io/badge/License-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![StyleCI](https://styleci.io/repos/34590394/shield)](https://styleci.io/repos/34590394)

Set of classes to make creating simple Amazon Echo Alexa Apps easier with Laravel and Lumen ([note that 5.2.x Lumen has a known issue that needs addressing](https://github.com/develpr/alexa-app/issues/5))

- [Main Features](#main-features)
- [Demo](#demo)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)

## Major Update - 0.2.0 - call me beta

I've recently refactored nearly all of this package to make it Laravel compatible, and to avoid the previous heavy handed solution of completely replace the default Lumen `Application`. I've also made a number of changes I feel are for the best, for instance I've decoupled the Laravel/Lumen Session with the Alexa AlexaSkillsKit specific session data, and I've created a single interface to make it possible to handle most Alexa interactions through a single facade. But that's mainly related to the refactor - there are also a *bunch* of new features, including most importantly support for Amazon's AlexaSkillsKit security related requirements.

## Main Features

1. Allows Laravel/Lumen style routing for intent, launch, and session end requests.
2. Handles verification of all security requirements put forth by Amazon, including certificate/signature verification, timestamp verification, etc
3. Provides access to Alexa AlexaSkillsKit session data through familiar Laravel style interface
4. Populates the response with Laravel session data to maintain a 1:1 set of session data between Lumen and Alexa
5. Provides classes to easily return Alexa friendly responses, including `Speech`, `Card`, and `Re-prompt` responses
6. Optionally provides a way to easily retrieve information about the connected Echo device (`$device = Alexa::device();`)

For a quick example:

    AlexaRoute::intent('/alexa-end-point', 'GetAntiJoke', function(){
        Alexa::say("Why was the little boy crying? Because he had a frog stapled to his face!");
    });

## Demo

*I'll be recording a number of new tutorial videos soon.*

## Installation

### Prerequisites

The only thing that is required for AlexaApp is the Laravel or Lumen (versions based on 5.2) framework.

After installing via composer (i.e. `composer require develpr/alexa-app`):

### 1 : Auto-load the appropriate service provider for your framework

The `Develpr\AlexaApp\Provider\LaravelServiceProvider` needs to be added to the array of auto-loaded service providers

#### Laravel

In the `config/app.php` configuration file, add:

    'providers' => [
        ...snip...
        \Develpr\AlexaApp\Provider\LaravelServiceProvider::class,
        ...snip...
    ],

#### Lumen

In your application's `bootstrap/app.php` file, add:

    $app->register(\Develpr\AlexaApp\Provider\LumenServiceProvider::class);


### 2: Adding the facades/aliases for `Alexa` and `AlexaRoute` (optional)

This is not required, but it can be very handy. If you'd prefer, you can inject an instance of the `\Develpr\AlexaApp\Alexa` or `\Develpr\AlexaApp\Routing\AlexaRouter` class, or grab them with `$app['alexa']` or $app['alexa.router'], respectively.

#### Laravel

**If** you'd like to use facades/aliases you'll need to add two separate alias configurations in the `config/app.php` file.

        'aliases' => [
            ...
            'AlexaRoute' => \Develpr\AlexaApp\Facades\AlexaRouter::class,
            'Alexa' => \Develpr\AlexaApp\Facades\Alexa::class,
            ...
        ],

#### Lumen

The truth is I'm not 100% sure if there is an "official" way of adding aliases/facades in Lumen, and I generally don't use custom facades with Lumen, however [as mentioned in this stackexchange post](http://stackoverflow.com/questions/30399766/where-to-register-facades-service-providers-in-lumen), this should work:

First make sure aliases/facades are enabled in your `bootstrap/app.php` file by uncommenting `$app->withFacades();` and then after this add

    class_alias(\Develpr\AlexaApp\Facades\AlexaRouter::class, 'AlexaRoute');
    class_alias(\Develpr\AlexaApp\Facades\Alexa::class, 'Alexa');

For lumen it might be easier to simply use `$app['alexa.router']` or inject an instance of one of the above classes into your class.

### 3: Register Certificate middleware for verifying request comes from Amazon/AlexaSkillsKit (optional)

For any production application, it's important and in fact required by Amazon that you protect your application as [described in their documentation](https://developer.amazon.com/public/solutions/devices/echo/alexa-app-kit/docs/developing-your-app-with-the-alexa-appkit). You do not *need* to register this middleware however, and for certain testing may choose not to.

This package makes this easy by providing middleware that will meet all required security parameters provided by Amazon. At this time, if you'd like to enable this functionality you'll need to register the `Certificate` middleware as outlined by the [Laravel](http://laravel.com/docs/5.1/middleware#registering-middleware)/[Lumen](http://lumen.laravel.com/docs/middleware) documentation.

If you'd like to protect all routes in your application you can simply add the `Certificate` middleware to your global middleware as show below, else you can protect certain end points (i.e. only run the certificate/security check at `/alexa-api-endpoint`).

#### Laravel

To protect **all routes**, in your `app/Http/Kernal.php` file:

    protected $middleware = [
        ...
        \Develpr\AlexaApp\Http\Middleware\Certificate::class,
        ...
    ];

#### Lumen

To protect **all routes**, in your `bootstrap/app.php` file:

    $app->middleware([
        ...snip...
        \Develpr\AlexaApp\Http\Middleware\Certificate::class,
        ...snip...
    ]);


### Everything is installed

At this point, everything should "work" (see below for more information on Usage), but there are a number of elements that may need to be configured.

# #Configuration

A number of things can be modified, or may even need to be modified depending on your application, **most importantly, the security options will need to be setup to match your AppId, etc**. Most if not all of these modifications work the same way regardless if you're using Laravel or Lumen, and all configuration values should be definable in a `config/` file, or by using a/an `.env` file.

If you're using Laravel, you can use the console artisan command to publish the AlexaApp configuration file to your applications configuration directory using `artisan vendor:publish`, or if you prefer (or are using Lumen) you can manually copy this file over from `vendor/develpr/alexa-app/config/alexa.php`.

**There are quite a few comments in the `alexa.php` config file, so please read through this for much more information on specific options!** - I'll only cover the more important, broader options here.

### Certificate/Security

There are a few simple configuration options that need to be set for AlexaApp to successfully verify a request is valid/from Amazon/AlexaSkillsKit.

#### Amazon / AlexaSkillsKit "applicationId"s

This is your AlexaSkillsKit's application id and is used to verify the request is for your application. If you're not sure of what your application id is, the easiest way (for me at least) to find it is by taking a look at a sample request going to your web server from your application. Part of the json body will include `..."application":{"applicationId":"amzn1.echo-sdk-ams.app.9ec3744a-d1b2-48f2-8e08-3b2045c00616"},...` - the applicationId you'll want to enter in the configuration is this `applicationId`.

The `applicationIds` configuration value can be set with the `ALEXA__APPLICATION_IDS` key in an .env file, or in the configuration file directly. Note that the configuration file accepts an *array* of applicationIds in case you are planning on serving multiple applications from one Laravel/Lumen application. The .env file method only allows a single applicationId to be specified.

#### Request timestamp tolerance

As of this writing Amazon specifies that requests should be no older then 150 seconds to prevent replay attacks. This is the default that is set within the default configuration but if you should wish to change this you can do so here. **Also note that if you set this value to 0, the request age will not be checked** - this is useful for testing if you have a sample request that you'd like to keep testing with.

Changes to this can be made in the config file (`'timestampTolerance'`) or by setting `ALEXA_TIMESTAMP_TOLERANCE` in the .env file

#### Certificate provider

By default, AlexaApp will use file storage for locally caching Amazon's remote certificate file. Other providers will be supported shortly, including redis, database, and eloquent. These related options can be seen/configured in the config file.

### Alexa Device

If you'd like to use the device functionality (i.e. `Alexa::device()`), you will more then likely need to configure a number of options.

Essentially, you need to tell Alexa app about where you are persistent and how to access the device information - two providers are supplied at this time, `eloquent` and `database`. If you use the eloquent provider you'll need to be sure eloquent is enabled if you're using Lumen.

#### Device Provider

Currently only `database` and `eloquent` options are supported, but more providers could easily be supported by implementing the `\Develpr\AlexaApp\Contracts\DeviceProvider` contract.

The default device provider is Eloquent, and there is a sample Device in `/vendor/develpr/alexa-app/Device/Device.php` that can be copied to your `app` directory and modified for your purposes. This model can be thought of as similar to the `User` model provided with a base installation of Laravel.

#### Sample migration

There is a sample migration provided with AlexaApp that can be copied to your migrations folder (manually or using console command in Laravel `php artisan vendor:publish --tag="migrations"`) and once migrated, will work "out of the box" with the included `DeviceProvider`s. If you'd prefer not to use this migration that's 100% fine, but you'll want to make sure to take a look at the config file to be sure you modify/understand any options you may need to update for your storage schema.


## Usage

In the following sections you can see how you might use this package. **Note please that while I may use facades/aliases in most of the examples below, you certainly don't need to!** [Check out the Installation section -> facades/aliases](#installation) if you want to read more.

### Routing

There are three types of requests that will be made from the Amazon AlexaApp middleware to your application. Those are

1. LaunchRequest (happens when your application is "opened")
2. SessionEndedRequest (send to your application with the application is closed)
3. IntentRequest (these are the all of the requests that are not one of the above - likely the "bread and butter" of your application - most meaningful interactions)

These three types of requests can be routed within your application just like normal LaravelLumen requests using the new functionality provided by this package! All of these samples would be in your `app/Http/routes.php` most likely.

**LaunchRequest**

    AlexaRoute::launch('/your-app-uri', 'App\Http\Controllers\AnyController@anyMethod');

or

    $app['alexa.router']->launch('/your-app-uri', 'App\Http\Controllers\AnyController@anyMethod');

**SessionEndedRequest**

    AlexaRoute::sessionEnded('/your-app-uri', function() use ($app) {
        return '{"version":"1.0","response":{"shouldEndSession":true}}';
    });

or

    $app['alexa.router']->sessionEnded('/your-app-uri', function() use ($app) {
        return '{"version":"1.0","response":{"shouldEndSession":true}}';
    });

**IntentRequest**

    AlexaRoute::intent('/your-app-uri', 'GetZodiacHoroscopeIntent', 'App\Http\Controllers\AnyController@anyMethod');

or

    $app['alexa.router']->intent('/your-app-uri', 'GetZodiacHoroscopeIntent', 'App\Http\Controllers\AnyController@anyMethod');


Note that in these examples both a closure and a controller was used to handle the request, but there is no specific requirement to use one vs. another based on the request type.

*Note that the other `get`, `post`, `put`, `patch`, `delete`, etc options are still available an are unchanged*

### Session

Session values are passed to and from your application in the json payload from Amazon / AlexaSkillsKit. These are accessible in the `AlexaRequest`, or using the Alexa facade/alias.

#### to retrieve a session value

`$previousChoice = Alexa::session('previousChoice');`

#### to retrieve all session values

`Alexa::session();`

#### to set a session value

`Alexa::session('previousChoice', "Pizza");`

or

`Alexa::setSession('previousChoice', "Pizza");`

#### to unset a session value

`Alexa::unsetSession('previousChoice');`

Session values will also be included in the response json, but **only if you are using the `AlexaResponse` class!**.


### Slots

You can retrieve the value of a slot (only applicable for IntentRequests as of this moment):

`$usersChoice = Alexa::slot('choice');`

If the slot is empty, `null` will be returned.  You can change this default value to something else by passing in your preferred default as the second parameter:

`$usersChoice = Alexa::slot('choice', 'foo');`

### Responses

You can use this package and the Alexa facade to easily create valid responses from your application, but it's worth knowing about the classes behind the facade. The most important thing to know is that `Alexa::say("Hello");` is simply returning a new `\Develpr\AlexaApp\Response\AlexaResponse` object with a `\Develpr\AlexaApp\Response\Speech` object inside.

#### Using the Alexa facade/alias

The easiest way to send a valid response to Amazon/AlexaSkillsKit/an end user is

`return Alexa::say("Oh hi Denny");`

As mentioned above, at the end of the day an `AlexaResponse` is being generated and returned, so you can chain other methods to add other response features. For example...

`return Alexa::say("Oh hi Denny")->withCard(new Card("Hello message"))->endSession();`

...will return a spoken message ("Oh hi Denny"), a card that has a title of "Hello message", and it will end the session.

#### The AlexaResponse

There are a number of useful classes that can be used to generate valid Amazon Echo friendly json responses. There is nothing particularly complex or magical about these classes, they simply make it easier to create valid responses without having to think too much.

The main class is `AlexaResponse` - I intended that an instance of this class would be returned at all times to the Echo. There are a number of useful things you can do.

You can return an instance of this class without doing anything else and that will be a valid response (albeit fairly useless!)

    return new AlexaResponse;

You can tell the Echo that the session should be ended

    $alexaResponse = new AlexaResponse;
    $alexaResponse->endSession();

    return $alexaResponse;

Or, you can add one (or both) Speech/Card/Reprompt objects to have spoken text or a card sent back to the end Echo user (*note that you don't need to return both!*).

    $alexaResponse = new AlexaResponse;
    $alexaResponse->withSpeech(new Speech("Hello!!"));

    $alexaResponse->withCard(new Card("Hello Title", "Hello Subtitle", "Hello content here!"));

    return $alexaResponse;


You can always return this in a single line,

    return new AlexaResponse(new Speech("Hello!!"), new Card("Hello Title", "Hello Subtitle", "Hello content here!"), true);

Here the third parameter, when set to true, will end the session.

## Tests
```
 $ phpunit --configuration phpunit.xml
```

## Thanks

**Thanks to @jasonlewis** - I re-used a lot of the ideas for some of the routing pieces from his [`ding/api`](https://github.com/dingo/api) package.

Thanks to all for checking this out. I'm guessing over the next weeks/months/year many things will change (quickly) with the Amazon Echo developer community, the developer APIs, etc, but I'll do my best to keep up with things and will certainly look at and appreciate any pull requests, feature requests, etc.

##`//todo`

I'd consider this currently to be in a beta. I have no doubt bugs will pop up as I continue to really test this and I'd really appreciate any feedback, bug reports, feature requests, or comments. There are a number of aspects I'm still not sure I won't change a bit (for instance the `Alexa::` facade if you use it does a **lot** of different things and I'm thinking I might be wise to split up some functionality.

1. ~~Find some way of not requiring replacing the default `Application`!~~
2. Add the sessions to the response without requiring the user return an instance of `AlexaResponse`
3. Tests!!!!
4. ~~Add some sort of simple authentication option for authenticating Echo devices/user based on the userIds~~
5. ~~Figure out the best way to verify the request is coming from Amazon - not sure this is possible or will be possible, but hopefully soon~~
6. ~~Add basic helpers for parsing speech from Alexa~~ - not exactly "done", but I've added some options to help. I'd be very interested in your opinion on how this might be done to be helpful!

