<?php

namespace Frijj2k\LarAlexa\Provider;

use Frijj2k\LarAlexa\Alexa;
use Frijj2k\LarAlexa\Certificate\DatabaseCertificateProvider;
use Frijj2k\LarAlexa\Certificate\EloquentCertificateProvider;
use Frijj2k\LarAlexa\Certificate\FileCertificateProvider;
use Frijj2k\LarAlexa\Certificate\RedisCertificateProvider;
use Frijj2k\LarAlexa\Device\DatabaseDeviceProvider;
use Frijj2k\LarAlexa\Device\EloquentDeviceProvider;
use Frijj2k\LarAlexa\Request\AlexaRequest;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Exception;

class AlexaServiceProvider extends ServiceProvider
{
    public function boot()
    {
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $request = $this->app->make('request');

        $this->registerRouter();
        $this->bindAlexaRequest($request);
        $this->bindCertificateProvider();
        $this->bindAlexa();
        $this->registerMiddleware();

    }

    protected function registerRouter()
    {
        $this->app->singleton('alexa.router', function ($app) {
            return $app->make(\Frijj2k\LarAlexa\Http\Routing\AlexaRouter::class);
        });
    }


    /**
     * Bind the appropriate AlexaResponse type to the IoC container
     */
    private function bindAlexaRequest(Request $request)
    {
        $this->app->singleton('alexa.request', function($app) use ($request) {
            /** @var AlexaRequest $alexaRequest */
            $alexaRequest = AlexaRequest::capture();

            if (!$app['config']['alexa.prompt.enable'] || $alexaRequest->getIntent() !== $app['config']['alexa.prompt.response_intent'] || is_null($alexaRequest->getPromptResponseIntent())) {
                return $alexaRequest;
            } else{
                $alexaRequest->setPromptResponse(true);

                return $alexaRequest;
            }
        });
    }

    private function bindAlexa()
    {
        $this->app->singleton('alexa', function($app) {
            $providerType = $app['config']['alexa.device.provider'];
            $provider = null;

            if ($providerType == "eloquent") {
                $provider = new EloquentDeviceProvider($app['config']['alexa.device.model']);
            } elseif ($providerType == "database") {
                $connection = $app['db']->connection();
                $provider = new DatabaseDeviceProvider($connection, $app['config']['alexa.device.table']);
            } else {
                throw new Exception("Unsupported Alexa Device Provider specified - currently only 'database' and 'eloquent' are supported");
            }

            $alexaRequest = $this->app['alexa.request'];

            return new Alexa($alexaRequest, $provider, $app['config']['alexa']);
        });
    }

    /**
     * Register the middleware.
     */
    protected function registerMiddleware()
    {
        $this->app->singleton(\Frijj2k\LarAlexa\Http\Middleware\Request::class, function ($app) {
            return new \Frijj2k\LarAlexa\Http\Middleware\Request($app, $app['alexa.router'], $app['alexa.request'], $app['alexa.router.middleware']);
        });

        $this->app->singleton(\Frijj2k\LarAlexa\Http\Middleware\Certificate::class, function ($app) {
            return new \Frijj2k\LarAlexa\Http\Middleware\Certificate($app['alexa.request'], $app['alexa.certificateProvider'], $app['config']['alexa']);
        });
    }

    private function bindCertificateProvider()
    {
        $this->app->bind('alexa.certificateProvider', function($app) {
            $providerType = $this->app['config']['alexa.certificate.provider'];

            switch ($providerType) {
                case 'file':
                    return new FileCertificateProvider(new Filesystem, $this->app['config']['alexa.certificate.filePath']);
                case 'redis':
                    return new RedisCertificateProvider($app->make('redis'));
                case 'eloquent':
                    return new EloquentCertificateProvider($app['config']['alexa.certificate.model']);
                case 'database':
                    return new DatabaseCertificateProvider($app['db']->connection(), $app['config']['alexa.device.table']);
            }

            throw new Exception("Unsupported Alexa Certificate Provider specified - currently only 'file', 'database', and 'eloquent' are supported");
        });
    }
}
