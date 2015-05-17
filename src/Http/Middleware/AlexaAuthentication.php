<?php
/**
 * Created by PhpStorm.
 * User: shoelessone
 * Date: 5/14/15
 * Time: 7:01 PM
 */

namespace Develpr\AlexaApp\Http\Middleware;


use Develpr\AlexaApp\Request\AlexaRequest;

class AlexaAuthentication {

	private $alexaRequest;

	function __construct(AlexaRequest $alexaRequest)
	{
		$this->alexaRequest = $alexaRequest;
		// TODO: Implement __construct() method.
	}


	public function handle($request, $next)
	{

		if( ! $this->alexaRequest->isAlexaRequest())
			return $next($request);

		$userId = $this->alexaRequest->getUserId();

		$test = \Auth::once(['alexa_user_id' => $userId, 'password' => $userId]);

		return $next($request);

	}
} 