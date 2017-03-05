<?php

namespace Develpr\AlexaApp\Http\Routing;

use Develpr\AlexaApp\Response\AlexaResponse;
use Illuminate\Routing\Router as IlluminateRouter;
use Alexa;

class AlexaRouter extends IlluminateRouter
{
    private $intentRoutes = [];

    private $audioIntents = [
        'AMAZON.PauseIntent',
        'AMAZON.ResumeIntent',
        'AMAZON.CancelIntent',
        'AMAZON.RepeatIntent',
        'AMAZON.NextIntent',
        'AMAZON.PreviousIntent',
        'AMAZON.LoopOnIntent',
        'AMAZON.LoopOffIntent',
        'AMAZON.ShuffleOnIntent',
        'AMAZON.ShuffleOffIntent',
        'AMAZON.StartOverIntent'
    ];

    private $audioEvents = [
        'PlaybackStarted',
        'PlaybackStopped',
        'PlaybackFinished',
        'PlaybackNearlyFinished',
        'PlaybackFailed'
    ];

    /**
     * @param string                     $uri
     * @param string                     $intent
     * @param \Closure|array|string|null $action
     *
     * @return $this
     */
    public function intent($uri, $intent, $action)
    {
        $this->intentRoutes[] = $uri;
        $this->addAlexaRoute('POST', $this->prefix($uri), 'IntentRequest' . $intent, $action);

        return $this;
    }

    /**
     * @param string                     $uri
     * @param \Closure|array|string|null $action
     *
     * @return $this
     */
    public function launch($uri, $action)
    {
        $this->intentRoutes[] = $uri;
        $this->addAlexaRoute('POST', $this->prefix($uri), 'LaunchRequest', $action);

        return $this;
    }

    /**
     * @param string                     $uri
     * @param \Closure|array|string|null $action
     *
     * @return $this
     */
    public function sessionEnded($uri, $action)
    {
        $this->intentRoutes[] = $uri;
        $this->addAlexaRoute('POST', $this->prefix($uri), 'SessionEndedRequest', $action);

        return $this;
    }


    /**
     * @param string                     $uri
     * @param string                     $event
     * @param \Closure|array|string|null $action
     *
     * @return $this
     */
    public function audioPlayer($uri, $event, $action)
    {
        $this->intentRoutes[] = $uri;
        $this->addAlexaRoute('POST', $this->prefix($uri), 'AudioPlayer.' . $event, $action);

        return $this;
    }

    /**
     * Adds the routes needed (or optional) for audio playing with empty responses
     * Can/should be overridden by detailled routes
     *
     * This just enables you to quickly start audio playback
     *
     * @param $uri
     */
    public function audioPlayerRoutes($uri)
    {
        foreach($this->audioEvents as $audioEvent)
        {
            $this->audioPlayer($uri, $audioEvent, function(){});
        }

        foreach($this->audioIntents as $audioIntent)
        {
            switch($audioIntent)
            {
                case 'AMAZON.PauseIntent':
                    $this->intent($uri, $audioIntent, function(){return Alexa::pause();});
                    break;
                case 'AMAZON.ResumeIntent':
                    $this->intent($uri, $audioIntent, function(){return Alexa::resume();});
                    break;
                default:
                    $this->intent($uri, $audioIntent, function(){return new AlexaResponse();});

            }
        }

        $this->sessionEnded($uri, function(){return new AlexaResponse();});
    }


    /**
     * Add a route to the underlying route collection.
     *
     * @param array|string               $methods
     * @param string                     $uri
     * @param string                     $intent
     * @param \Closure|array|string|null $action
     *
     * @return \Illuminate\Routing\Route
     */
    protected function addAlexaRoute($methods, $uri, $intent, $action)
    {
        return $this->routes->add($this->createAlexaRoute($methods, $uri, $intent, $action));
    }

    /**
     * Create a new Route object.
     *
     * @param array|string               $methods
     * @param string                     $uri
     * @param string                     $intent
     * @param \Closure|array|string|null $action
     *
     * @return \Illuminate\Routing\Route
     */
    protected function newAlexaRoute($methods, $uri, $intent, $action)
    {
        return (new AlexaRoute($methods, $uri, $intent, $action))->setContainer($this->container)->setRouter($this);
    }

    /**
     * Create a new route instance.
     *
     * @param array|string               $methods
     * @param string                     $uri
     * @param string                     $intent
     * @param \Closure|array|string|null $action
     *
     * @return \Illuminate\Routing\Route
     */
    protected function createAlexaRoute($methods, $uri, $intent, $action)
    {
        // If the route is routing to a controller we will parse the route action into
        // an acceptable array format before registering it and creating this route
        // instance itself. We need to build the Closure that will call this out.
        if ($this->actionReferencesController($action)) {
            $action = $this->convertToControllerAction($action);
        }

        $route = $this->newAlexaRoute(
            $methods, $this->prefix($uri), $intent, $action
        );

        // If we have groups that need to be merged, we will merge them now after this
        // route has already been created and is ready to go. After we're done with
        // the merge we will be ready to return the route back out to the caller.
        if ($this->hasGroupStack()) {
            $this->mergeGroupAttributesIntoRoute($route);
        }

        $this->addWhereClausesToRoute($route);

        return $route;
    }
}
