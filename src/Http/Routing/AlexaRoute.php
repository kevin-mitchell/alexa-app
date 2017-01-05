<?php

namespace Frijj2k\LarAlexa\Http\Routing;

use Frijj2k\LarAlexa\Http\Routing\Matching\AlexaValidator;
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
     * @return string
     */
    public function getUri()
    {
        return parent::getUri() . $this->getRouteIntent();
    }
}
