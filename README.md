# AlexaApp
Set of classes to make creating simple Amazon Echo Alexa Apps easier with Lumem

##Main Features

1. Allows Lumen/Laravel style routing for intent, lauch, and session end requests. 
2. Populates the Lumen Session with the session attributes from Alexa, allowing a single interface for Session data retrieval
3. Populates the response with Lumen's session data to maintain a 1:1 set of session data between Lumen and Alexa
4. Provides classes to easily return Alexa friendly responses, including `Speech` and/or `Card` responses

For example:

    $app->launchRequest('/alexa-app-demo', function() use $app {
       return new AlexaResponse(new Speech('Welcome to the Alexa App Demo!'));
    });
    
##Demo

Here is a ~30 minute demo that starts with an instance of Lumen being installed (and a web server with a self-signed cert) and goes to a complete Alexa application.

(https://www.youtube.com/watch?v=8uizl_LWCi8)[![AlexaApp Demo on Youtube](http://www.develpr.com/uploads/images/alexa_app_demo_video_image.jpg)]

##Prerequisites

The only thing that is required for AlexaApp is the [Lumen](http://lumen.laravel.com) framework. A number of optional services within Lumen must be enabled, including:

1. Facades

##Caution/Warning

As of now this package is somewhat heavy handed, in particular the entire `Application` is being extended/replaced `AlexaApplication` is being used in instead.

This is nessisary at this point as Lumen (and Laravel as of this writing) doesn't provide a great way to change the dispatch routing behaviour. I add a number of new options for routing requests to controllers/etc and the logic to match these routes can't be modified without modifying/extending some of the logic in the Application class.

Because of this, **at this point, using the package is likely only a good idea if you are building a stand alone AlexaApp** - in other words, if you have some mission critical Lumen application it's likely not a great idea to run this package on top (though there shouldn't be any problems with doing this).

Additionally, **this package does not (*yet*) in anyway work to verify that the request coming in is being sent by Amazon.** - frankly at this point I'm unsure of the best way to do this, but I'm assuming it will be via some sort of "signature" that amazon will send. The documentation provided by Amazon includes a possibly relevant header, but from I haven't found any further information on this yet.

    Signature:
    SignatureCertChainUrl:


##Installation

After installing via composer, the `AlexaApplication` needs to be switched in for the stock Lumen version, and middleware needs to be registered, and a provider as well. 

In the `boostrap/app.php` file...

First we need to switch out the original Application:

    //This is the original/stock Application
    /*$app = new Laravel\Lumen\Application(
        realpath(__DIR__.'/../')
    );*/
    //Which we'll replace with the AlexaApplication which provides routing
    $app = new Develpr\AlexaApp\AlexaApplication(
        realpath(__DIR__.'/../')
    );
   
 
Second, we need to register the AlexaApp service provider which handles setting up the session and binding a special `AlexaRequest` to the IoC container.

     $app->register(\Develpr\AlexaApp\AlexaProvider::class);

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


###Thanks

Thanks for checking this out. I'm guessing over the next weeks/months/year many things will change (quickly) with the Amazon Echo developer community, the developer APIs, etc, but I'll do my best to keep up with things and will certainly look at and appreciate any pull requests, feature requests, etc.

###To Do

1. Find some way of not requiring replacing the default `Application`!
2. Add the sessions to the response without requiring the user return an instance of `AlexaResponse`
3. Tests!!!!
4. Add some sort of simple authentication option for authenticating Echo devices/user based on the userIds
5. Figure out the best way to verify the request is coming from Amazon - not sure this is possible or will be possible, but hopefully so
6. Add basic helpers for parsing speech from Alexa

