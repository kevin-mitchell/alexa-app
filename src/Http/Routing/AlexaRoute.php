<?php

namespace Develpr\AlexaApp\Http\Routing;

use Develpr\AlexaApp\Http\Routing\Matching\AlexaValidator;
use Illuminate\Routing\Matching\UriValidator;
use Illuminate\Routing\Route;

class AlexaRoute extends Route
{
    /**
     * @var string
     */
    private $routeIntent;

    /**
     * AlexaRoute constructor.
     * @param array|string   $methods
     * @param string         $uri
     * @param string         $intent
     * @param \Closure|array $action
     */
    public function __construct($methods, $uri, $intent, $action)
    {
        parent::__construct($methods, $uri, $action);

        $this->routeIntent = $intent;
    }

    /**
     * @return string
     */
    public function getRouteIntent()
    {
        return $this->routeIntent;
    }

    /**
     * Set the router instance on the route.
     *
     * @param  \Illuminate\Routing\Router $router
     *
     * @return $this
     */
    public function setRouter(\Illuminate\Routing\Router $router)
    {
        $this->router = $router;

        return $this;
    }

    /**
     * Before Laravel 5.4, `uri()` was `getUri()` for the time being, we'll make this
     * friendly to both pre and post 5.4 with this check
     * todo: version 5.5+ we should remove this check to clean things up and update readme
     * @return string
     */
    public function getUri()
    {
        return parent::getUri() . $this->getRouteIntent();
    }

    /**
     * Returns the URI for the request. Note that with Laravel
     *
     * @return string
     */
    public function uri() {
        return parent::uri() . $this->getRouteIntent();
    }

    /**
     * Compile the route into a Symfony CompiledRoute instance.
     *
     * @return void
     */
    protected function compileRoute()
    {
        if (version_compare(\Illuminate\Foundation\Application::VERSION, '5.5.0') < 0) {
            if ( is_callable( "parent::extractOptionalParameters" ) ) {
                return parent::compileRoute();
            }
        }

        if (! $this->compiled) {
            $tempRouterIntent = $this->routeIntent;
            $this->routeIntent = "";
            $this->compiled = parent::compileRoute();
            $this->routeIntent = $tempRouterIntent;
        }

        return $this->compiled;
    }
}
