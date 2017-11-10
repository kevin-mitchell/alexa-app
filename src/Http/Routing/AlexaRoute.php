<?php

namespace Develpr\AlexaApp\Http\Routing;

use Develpr\AlexaApp\Http\Routing\Matching\AlexaValidator;
use Illuminate\Routing\Matching\UriValidator;
use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCompiler;

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
     * Get the route validators for the instance.
     *
     * @return array
     */
    public static function getValidators()
    {
        $validators = parent::getValidators();

        foreach ($validators as $key => $validator) {
            if ($validator instanceof UriValidator) {
                break;
            }
        }

        $validators[] = new AlexaValidator;

        return $validators;
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

            //todo: this is a bit ugly - we should go deeper and solve the real problem
            //This is ugly - before 5.4, we didn't use "uri()" method in the RouteCompiler (there was no
            //route compiler!), and instead we used the private uri instance variable. Which meant that
            //`uri()` and `uri` were different.
            $tempRouterIntent = $this->routeIntent;
            $this->routeIntent = "";
            $this->compiled = (new RouteCompiler($this))->compile();
            $this->routeIntent = $tempRouterIntent;
        }

        return $this->compiled;
    }
}
