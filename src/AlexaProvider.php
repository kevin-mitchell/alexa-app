<?php namespace Develpr\AlexaApp;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class AlexaProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    public function boot()
    {
        $request = $this->app->make('request');

		$this->bindAlexaRequest($request);
        $this->setupSession($request);
    }

    private function setupSession(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if(! $data)
            $data = json_decode($request->input('content'), true);

        $sessionAttributes = array_get($data, 'session.attributes');
        if( ! $sessionAttributes )
            return;
            
        foreach($sessionAttributes as $key => $value)
        {
            \Session::put($key, $value);
        }

    }

	private function bindAlexaRequest(Request $request)
	{
		$this->app->bind('Develpr\AlexaApp\Request\AlexaRequest', function() use ($request) {

			$requestType = array_get(json_decode($request->getContent(), true), 'request.type');

			if($requestType == null)
				$requestType = array_get(json_decode($request->input('content'), true), 'request.type');

			$className = 'Develpr\AlexaApp\Request\' . $requestType;

			if( ! class_exists($className))
			{
				throw new \Exception("This type of request is not supported");
			}

			return new $className($request);

		});
	}
}
