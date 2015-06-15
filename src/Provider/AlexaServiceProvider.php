<?php namespace Develpr\AlexaApp\Provider;

use Develpr\AlexaApp\Alexa;
use Develpr\AlexaApp\Certificate\DatabaseCertificateProvider;
use Develpr\AlexaApp\Certificate\EloquentCertificateProvider;
use Develpr\AlexaApp\Certificate\FileCertificateProvider;
use Develpr\AlexaApp\Certificate\RedisCertificateProvider;
use Develpr\AlexaApp\Device\DatabaseDeviceProvider;
use Develpr\AlexaApp\Device\EloquentDeviceProvider;
use Develpr\AlexaApp\Http\Routing\AlexaRouter;
use Develpr\AlexaApp\Request\AlexaRequest;
use Develpr\AlexaApp\Request\NonAlexaRequest;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Exception;
use Illuminate\Redis\Database as RedisDatabase;

class AlexaServiceProvider extends ServiceProvider
{

	public function boot()
	{
		$this->app['router']->middleware('alexa.certificate', 'Develpr\AlexaApp\Http\Middleware\Certificate');
	}


    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
		$this->setupConfig();

		$request = $this->app->make('request');

		$this->setupClassAliases();
		$this->registerRouter();
		$this->bindAlexaRequest($request);
		$this->bindCertificateProvider();
		$this->bindAlexa();
		$this->registerMiddleware();

    }

	protected function setupClassAliases()
	{
		$this->app->alias('alexa.router', 'Develpr\AlexaApp\Http\Routing\AlexaRouter');
		$this->app->alias('alexa.request', 'Develpr\AlexaApp\Contracts\AlexaRequest');
		$this->app->alias('alexa.certificateProvider', 'Develpr\AlexaApp\Contracts\CertificateProvider');
	}

	protected function registerRouter()
	{
		$this->app->singleton('alexa.router', function ($app) {
			return $app->make('\Develpr\AlexaApp\Http\Routing\AlexaRouter');
		});
	}

	protected function setupConfig()
	{
		$this->mergeConfigFrom(realpath(__DIR__.'/../../config/alexa.php'), 'alexa');
	}

	/**
	 *	Bind the appropriate AlexaResponse type to the IoC container
	 */
	private function bindAlexaRequest(Request $request)
	{
		$this->app->singleton('alexa.request', function() use ($request) {
			return AlexaRequest::capture();
			//todo: originally I had different request types based on the intent type
		});
	}

    private function bindAlexa()
    {
        $this->app->singleton('alexa', function($app){
            $providerType = $app['config']['alexa.device.provider'];
            $provider = null;
            if($providerType == "eloquent"){
                $provider = new EloquentDeviceProvider($app['config']['alexa.device.model']);
            }else if($providerType == "database"){
                $connection = $app['db']->connection();
                $provider = new DatabaseDeviceProvider($connection, $app['config']['alexa.device.table']);
            }else{
                throw new Exception("Unsupported Alexa Device Provider specified - currently only 'database' and 'eloquent' are supported");
            }

            $alexaRequest = $this->app['alexa.request'];

            return new Alexa($alexaRequest, $provider, $app['config']['alexa']);
        });
    }

	/**
	 * Register the middleware.
	 *
	 * @return void
	 */
	protected function registerMiddleware()
	{
		$this->app->singleton('Develpr\AlexaApp\Http\Middleware\Request', function ($app) {
			return new \Develpr\AlexaApp\Http\Middleware\Request($app, $app['alexa.router'], $app['alexa.request'], $app['app.middleware']);
		});
		$this->app->singleton('Develpr\AlexaApp\Http\Middleware\Certificate', function ($app) {
			return new \Develpr\AlexaApp\Http\Middleware\Certificate($app['alexa.request'], $app['alexa.certificateProvider'], $app['config']['alexa']);
		});
	}


	private function bindCertificateProvider()
	{
		$this->app->bind('alexa.certificateProvider', function($app){

			$providerType = $this->app['config']['alexa.certificate.provider'];

			if($providerType == "file"){
				$provider = new FileCertificateProvider(new Filesystem, $this->app['config']['alexa.certificate.filePath']);
			}else if($providerType == "redis"){
				$redis = $app->make('redis');
				$provider = new RedisCertificateProvider($redis);
			}else if($providerType == "eloquent"){
				$provider = new EloquentCertificateProvider($app['config']['alexa.certificate.model']);
			}else if($providerType == "database"){
				$connection = $app['db']->connection();
				$provider = new DatabaseCertificateProvider($connection, $app['config']['alexa.device.table']);
			}else{
				throw new Exception("Unsupported Alexa Certificate Provider specified - currently only 'file', 'database', and 'eloquent' are supported");
			}

			return $provider;
		});
	}
}
