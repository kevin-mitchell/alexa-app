# AlexaApp
Set of classes to make creating simple Amazon Echo Alexa Apps easier with Laravel and Lumen

##MAJOR UPDATE!

I've recently refactored nearly all of this package to make it Laravel compatible, and to aviod the previous
heavy handed solution of completely replace the default `Application`. I've also made a number of changes I feel
are for the best, for instance I've decoupled the Laravel/Lumen Session with the Alexa AppKit specific session data,
and I've created a single interface to make it possible to handle most Alexa interactions through a single facade.

##Main Features

1. Allows Laravel/Lumen style routing for intent, launch, and session end requests.
2. Handles verification of all security requirements put forth by Amazon, including certificate/signature verification, timestamp verification, etc
3. Provides access to Alexa AppKit session data through familiar Laravel style interface
4. Populates the response with Laravel session data to maintain a 1:1 set of session data between Lumen and Alexa
5. Provides classes to easily return Alexa friendly responses, including `Speech`, `Card`, and `Re-prompt` responses

For example:

	AlexaRoute::intent('/alexa-end-point', 'GetAntiJoke', function(){
		Alexa::say("Why was the little boy crying? Because he had a frog stappled to his face!");
	});

##Demo

*I'll be recording a number of new tutorial videos soon.*

##Prerequisites

The only thing that is required for AlexaApp is the Laravel or Lumen (versions based on 5.1 )framework.

##Installation

After installing via composer:

###Laravel

The `Develpr\AlexaApp\Provider\LaravelServiceProvider` needs to be added to the array of auto-loaded service providers in the `config/app.php` configuration file.

	'providers' => [
		...
		`Develpr\AlexaApp\Provider\LaravelServiceProvider`
		...
	],

**If** you'd like to use facades/aliases you'll need to add two separate alias configurations in the `config/app.php` file (note below I'm using the ::class operator, but you just use the full class path as a string if you prefer!)

		'aliases' => [
			...
			'AlexaRoute' => \Develpr\AlexaApp\Facades\AlexaRouter::class,
    		'Alexa' => \Develpr\AlexaApp\Facades\Alexa::class,
    		...
    	],

For any production application, it's important and in fact required by Amazon that you protect your application as described

This package makes this easy by providing middleware that will meet all required security parameters provided by Amazon. At this time, if you'd like to enable this functionality you'll need to register the `Certificate` middleware as outlined by [Laravel's own documentation](http://laravel.com/docs/5.1/middleware#registering-middleware).

If you'd like to protect all routes in your application you can simply add the `Certificate` middleware to the list of middleware in your `app/Http/Kernal.php` file:

	protected $middleware = [
		...
		\Develpr\AlexaApp\Http\Middleware\Certificate::class,
		...
	];

##Usage

###Routing

There are three types of requests that will be made from the Amazon AlexaApp middleware to your application. Those are

1. LaunchRequest (happens when your application is "opened")
2. SessionEndedRequest (send to your application with the application is closed)
3. IntentRequest (these are the all of the requests that are not one of the above - most meaningful interactions)

These three types of requests can be routed within your application just like normal Lumen requests using the new functionality provided by this package! 

**LaunchRequest**

    $app->launch('/your-app-uri', 'App\Http\Controllers\AnyController@anyMethod');
	
**SessionEndedRequest**
	
    $app->sessionEnded('/your-app-uri', function() use ($app) {
        return '{"version":"1.0","response":{"shouldEndSession":true}}';
    })

**IntentRequest**

    $app->intent('/your-app-uri', 'GetZodiacHoroscopeIntent', 'App\Http\Controllers\AnyController@anyMethod');

Note that in these examples both a closure and a controller was used to handle the request, but there is no specific requirement to use one vs. another based on the request type. In fact there is nothing "special" about the routes in terms of how they are actually handled (controller vs closure vs ??), it's just standard Lumen.

*Note that the other `get`, `post`, `put`, `patch`, `delete`, etc options are still available an are unchanged*


###Session

Session values are passed to and from your application in the json payload. There are many ways of dealing with this, but I thought that it might be handy if you could just access the Alexa "native" session values using Lumen's Session facade.

To access a session value that was passed to your application from the Amazon AlexaApp middleware, simply use the name of the session variable as you normally would with Lumen

`$previousChoice = Session::get('choice');`

Session values will also be included in the response json, but **only if you are using the `AlexaResponse` class!**

###IntentRequest

You can always type hint an `IntentRequest` or otherwise retrieve an instance of this class from the IoC container, and it can be useful (though admittedly limitedly so at this point!) for retrieving the "slot" values from the IntentRequest. For example

    $intentRequest = $app->make(IntentRequest::class);
    $requestedMeal = $intentRequest->slot('RequestedMeal');
	
Essentially this is simply parsing the "slots" from the json string and returning them, but it may save a bit of time for you to use this instead of parsing these out yourself. I had considered figuring out if there was some way of cleaning extending/adding to the `Input` facade to add something like an `Input::slot('RequestedMeal')` option, but for now this should work!



###Responses

There are a number of useful classes that can be used to generate valid Amazon Echo friendly json responses. There is nothing particularly complex or magical about these classes, they simply make it easier to create valid responses without having to think too much.

The main class is `AlexaResponse` - I intended that an instance of this class would be returned at all times to the Echo. There are a number of useful things you can do.

You can return an instance of this class without doing anything else and that will be a valid response (albeit fairly useless!)

    return new AlexaResponse;

You can tell the Echo that the session should be ended

    $alexaResponse = new AlexaResponse;
    $alexaResponse->endSession();
    
    return $alexaResponse;
	
Or, you can add one (or both) Speech/Card objects to have spoken text or a card sent back to the end Echo user (*note that you don't need to return both!*).

    $alexaResponse = new AlexaResponse;
    $alexaResponse->setSpeech(new Speech("Hello!!"));
    
    $alexaResponse->setCard(new Card("Hello Title", "Hello Subtitle", "Hello content here!"));
    
    return $alexaResponse;


You can always return this in a single line, 

    return new AlexaResponse(new Speech("Hello!!"), new Card("Hello Title", "Hello Subtitle", "Hello content here!"), true);

Here the third parameter, when set to true, will end the session.

#Auth (very very unstable)

1. Configure the database
2. Register the AlexaAuthentication middleware
3. Enable Eloquent

AUTH_DRIVER=eloquent
AUTH_MODEL=App\:

ALEXA_AUTH_MODEL=Develpr\AlexaApp\AlexaUser




###Thanks

Thanks for checking this out. I'm guessing over the next weeks/months/year many things will change (quickly) with the Amazon Echo developer community, the developer APIs, etc, but I'll do my best to keep up with things and will certainly look at and appreciate any pull requests, feature requests, etc.

###To Do

1. Find some way of not requiring replacing the default `Application`!
2. Add the sessions to the response without requiring the user return an instance of `AlexaResponse`
3. Tests!!!!
4. Add some sort of simple authentication option for authenticating Echo devices/user based on the userIds
5. Figure out the best way to verify the request is coming from Amazon - not sure this is possible or will be possible, but hopefully so
6. Add basic helpers for parsing speech from Alexa

