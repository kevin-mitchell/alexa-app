# AlexaApp
Set of classes to make creating simple Amazon Echo Alexa Apps easier with Lumem

##Main Features

1. Allows Laravel/Lumen style routing for intent, lauch, and session end requests. 
2. Populates the Lumen Session with the session attributes from Alexa, allowing a single interface for Session data retrieval
3. Populates the response with Lumen's session data to maintain a 1:1 set of session data between Lumen and Alexa
4. Provides classes to easily return `Speech` and/or `Card` responses

For example:

    $app->launchRequest('/alexa-app-demo', function() use $app {
       return new AlexaResponse(new Speech('Welcome to the Alexa App Demo!'));
    });

##Prerequisites

The only thing that is required for AlexaApp is the [Lumen](http://lumen.laravel.com) framework. A number of optional services within Lumen must be enabled, including:

1. Facades
2. Session

##Caution/Warning

As of now this package is somewhat heavy handed, in particular the entire `Application` is being extended and `AlexaApplication` is being used in instead.

This is nessisary at this point as Lumen (and Laravel as of this writing) doesn't provide a great way to change the dispatch routing behaviour. 

**At this point, using the package is likely only a good idea if you are building a stand alone AlexaApp** - in other words, if you have some mission critical Lumen application it's likely not a great idea to run this package on top (though there shouldn't be any problems with doing this).

##Installation

After installing via composer, the `AlexaApplication` needs to be switched in for the stock Lumen version, and middleware needs to be registered, and a provider as well. 

In the `boostrap/app.php` file...

First we need to switch out the original Application:

    //This is the original/stock Application
    //$app = new Laravel\Lumen\Application;
    //Which we'll replace with the AlexaApplication which provides routing
    $app = new Develpr\AlexaApp\AlexaApplication;
   
 
Second, we need to register the AlexaApp service provider which handles setting up the session and binding a special `AlexaRequest` to the IoC container.



