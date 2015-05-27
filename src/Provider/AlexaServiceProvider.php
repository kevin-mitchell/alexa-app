<?php namespace Develpr\AlexaApp\Provider;

use Develpr\AlexaApp\Alexa;
use Develpr\AlexaApp\Device\DatabaseDeviceProvider;
use Develpr\AlexaApp\Device\EloquentDeviceProvider;
use Develpr\AlexaApp\Request\NonAlexaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;


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
				throw new \Exception("This type of request is not supported");
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

                $provider = new DatabaseDeviceProvider($connection, $app['config']['alexa.device.model']);

            }else{
                throw new \Exception("Unsupported Alexa Device Provider specified - currently only 'database' and 'eloquent' are supported");
            }

            $alexaRequest = $this->app->make('Develpr\AlexaApp\Request\AlexaRequest');

            return new Alexa($alexaRequest, $provider, $app['config']['alexa']);

        });
    }
}
