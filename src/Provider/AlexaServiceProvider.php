<?php namespace Develpr\AlexaApp\Provider;

use Develpr\AlexaApp\Request\NonAlexaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;


class AlexaServiceProvider extends ServiceProvider
{

    public function boot()
    {
		$request = $this->app->make('request');

        $this->setupSession($request);
    }


    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
		$request = $this->app->make('request');

		$this->bindAlexaRequest($request);
    }

	protected function setupConfig()
	{
		$this->mergeConfigFrom(realpath(__DIR__.'/../../config/alexa.php'), 'alexa');
	}

	/**
	 *	Add session values from json payload to Lumen session
	 */
    private function setupSession(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $sessionAttributes = array_get($data, 'session.attributes');
		
        if( ! $sessionAttributes )
            return;
            
        foreach($sessionAttributes as $key => $value)
        {
            \Session::put($key, $value);
        }

    }

	/**
	 *	Bind the approriate AlexaResponse type to the IoC container
	 */
	private function bindAlexaRequest(Request $request)
	{
		$this->app->bind('Develpr\AlexaApp\Request\AlexaRequest', function() use ($request) {

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
