<?php namespace Develpr\AlexaApp\Provider;

use Develpr\AlexaApp\Domain\Alexa;
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

		$this->app->singleton('alexa', function(){
			$alexaRequest = $this->app->make('Develpr\AlexaApp\Request\AlexaRequest');
			return new Alexa($alexaRequest);
		});

    }

	protected function setupConfig()
	{
		$this->mergeConfigFrom(realpath(__DIR__.'/../../config/alexa.php'), 'alexa');
	}

	/**
	 *	Bind the approriate AlexaResponse type to the IoC container
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
}
