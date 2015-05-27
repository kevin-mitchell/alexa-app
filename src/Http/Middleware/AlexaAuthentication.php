<?php namespace Develpr\AlexaApp\Http\Middleware;

use Develpr\AlexaApp\Contracts\AmazonEchoDevice;

class AlexaAuthentication {

	public function handle($request, $next)
	{
		/** @var AmazonEchoDevice $device */
		$device = \Alexa::device();

		if( ! $device ){
				return response('Unauthorized.', 401);
		}

		return $next($request);

	}
} 