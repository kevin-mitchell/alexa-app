<?php namespace Develpr\AlexaApp\Provider;

use Develpr\AlexaApp\Alexa;
use Develpr\AlexaApp\Certificate\DatabaseCertificateProvider;
use Develpr\AlexaApp\Certificate\EloquentCertificateProvider;
use Develpr\AlexaApp\Certificate\FileCertificateProvider;
use Develpr\AlexaApp\Certificate\RedisCertificateProvider;
use Develpr\AlexaApp\Device\DatabaseDeviceProvider;
use Develpr\AlexaApp\Device\EloquentDeviceProvider;
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

		$this->bindAlexaRequest($request);
		$this->bindCertificateProvider();
		$this->bindAlexa();

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
		$this->app->singleton('Develpr\AlexaApp\Request\AlexaRequest', function() use ($request) {

			$requestType = array_get(json_decode($request->getContent(), true), 'request.type');
			
			//todo: throw an exception?
			if(! $requestType)
				return new NonAlexaRequest;

			$className = 'Develpr\AlexaApp\Request\\' . $requestType;

			if( ! class_exists($className))
			{
				throw new Exception("This type of request is not supported");
			}

			return new $className($request);

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

            $alexaRequest = $this->app->make('Develpr\AlexaApp\Request\AlexaRequest');

            return new Alexa($alexaRequest, $provider, $app['config']['alexa']);

        });
    }

	private function bindCertificateProvider()
	{
		$this->app->bind('Develpr\AlexaApp\Contracts\CertificateProvider', function($app){

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
