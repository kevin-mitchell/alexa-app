<?php  namespace Develpr\AlexaApp\Provider;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;
use ReflectionClass;

class LaravelServiceProvider extends ServiceProvider {

	public function boot()
	{
		$this->publishes([
			realpath(__DIR__.'/../../config/alexa.php') => config_path('alexa.php'),
		]);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		/** @var \App\Http\Kernel $kernel */
		$kernel = $this->app->make('Illuminate\Contracts\Http\Kernel');
		$this->app->instance('app.middleware', $this->gatherAppMiddleware($kernel));
		$this->addRequestMiddlewareToBeginning($kernel);

		//Register our universal service provider
		$this->app->register('Develpr\AlexaApp\Provider\AlexaServiceProvider');
	}


	protected function addRequestMiddlewareToBeginning(Kernel $kernel)
	{
		/** @var \App\Http\Kernel $kernel */
		$kernel->prependMiddleware('Develpr\AlexaApp\Http\Middleware\Request');
	}

	/**
	 * Gather the application middleware besides this one so that we can send
	 * our request through them, exactly how the developer wanted.
	 *
	 * @param \Illuminate\Contracts\Http\Kernel $kernel
	 *
	 * @return array
	 */
	protected function gatherAppMiddleware(Kernel $kernel)
	{
		$reflection = new ReflectionClass($kernel);

		$property = $reflection->getProperty('middleware');
		$property->setAccessible(true);

		$middleware = $property->getValue($kernel);

		$middleware = $this->unsetCsrfMiddlware($middleware);

		return $middleware;
	}

	private function unsetCsrfMiddlware($allMiddleware)
	{
		$newMiddleware = [];

		foreach($allMiddleware as $position => $aMiddleware){
			if(strpos(strtolower($aMiddleware), 'verifycsrftoken') !== false)
				continue;
			$newMiddleware[$position] = $aMiddleware;
		}

		return $newMiddleware;
	}


} 