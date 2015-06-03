<?php  namespace Develpr\AlexaApp\Provider;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;

class LaravelServiceProvider {

	protected function addRequestMiddlewareToBeginning(Kernel $kernel)
	{
		$kernel->prependMiddleware('Dingo\Api\Http\Middleware\Request');
	}


} 