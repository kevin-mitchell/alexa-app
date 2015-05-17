<?php
/**
 * Created by PhpStorm.
 * User: shoelessone
 * Date: 5/14/15
 * Time: 7:56 PM
 */

namespace Develpr\AlexaApp\Request;


class NonAlexaRequest implements AlexaRequest {
	/**
	 * Is this request formatted as an Amazon Echo/Alexa request?
	 *
	 * @return bool
	 */
	public function isAlexaRequest()
	{
		return false;
	}


	/**
	 * returns the request type, i.e. IntentRequest
	 *
	 * @return mixed
	 */
	public function getRequestType()
	{
		return null;
	}

	/**
	 * Get the UserId provided in the request
	 *
	 * @return mixed
	 */
	public function getUserId()
	{
		return null;
	}


} 