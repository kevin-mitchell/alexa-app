<?php
/**
 * Created by PhpStorm.
 * User: shoelessone
 * Date: 5/14/15
 * Time: 7:01 PM
 */

namespace Develpr\AlexaApp\Http\Middleware;


use Develpr\AlexaApp\AlexaUser;
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

		$userData = ['alexa_user_id' => $userId, 'password' => $userId];

		$loggedIn = \Auth::once($userData);

		if( ! $loggedIn && $userId ){

			$user = new AlexaUser;
			$user->alexa_user_id = $userId;
			$user->password = crypt($userId);
			$user->save();

			$loggedIn = \Auth::once($userData);

		}

		return $next($request);

	}
} 