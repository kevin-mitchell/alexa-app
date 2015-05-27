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

    /**
     * Attempt to return an IntentRequest
     *
     * @return IntentRequest|null
     */
    public function toIntentRequest()
    {
        return null;
    }

    /**
     * Get all of the session values in an array
     *
     * @return array
     */
    public function getSession()
    {
        return null;
    }

    /**
     * Get a particular session value by key
     *
     * @param String $key
     * @return mixed|null
     */
    public function getSessionValue($key = null)
    {
        return null;
    }

    /**
     * Attempt to return a SessionEndedRequest
     *
     * @return SessionEndedRequest|null
     */
    public function toSessionEndedRequest()
    {
        return null;
    }

    /**
     * Attempt to return a LaunchRequest
     *
     * @return LaunchRequest|null
     */
    public function toLaunchRequest()
    {
        return null;
    }


} 