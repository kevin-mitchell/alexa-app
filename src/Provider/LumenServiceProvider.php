<?php

namespace Develpr\AlexaApp\Provider;

use Illuminate\Support\ServiceProvider;
use ReflectionClass;

class LumenServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->setupConfig();
        $reflection = new ReflectionClass($this->app);
        $this->app->instance('app.middleware', $this->gatherAppMiddleware($reflection));

        $this->app->register('Develpr\AlexaApp\Provider\AlexaServiceProvider');

        $this->addRequestMiddlewareToBeginning($reflection);
    }

    protected function setupConfig()
    {
        $this->mergeConfigFrom(realpath(__DIR__.'/../../config/alexa.php'), 'alexa');
    }


    /**
     * Add the request middleware to the beginning of the middleware stack on the
     * Lumen application instance.
     *
     * @param \ReflectionClass $reflection
     */
    protected function addRequestMiddlewareToBeginning(ReflectionClass $reflection)
    {
        $property = $reflection->getProperty('middleware');
        $property->setAccessible(true);

        $middleware = $property->getValue($this->app);

        array_unshift($middleware, 'Develpr\AlexaApp\Http\Middleware\Request');

        $property->setValue($this->app, $middleware);
        $property->setAccessible(false);
    }

    /**
     * Gather the application middleware besides this one so that we can send
     * our request through them, exactly how the developer wanted.
     *
     * @param \ReflectionClass $reflection
     *
     * @return array
     */
    protected function gatherAppMiddleware(ReflectionClass $reflection)
    {
        $property = $reflection->getProperty('middleware');
        $property->setAccessible(true);

        $middleware = $property->getValue($this->app);

        return $middleware;
    }
}
