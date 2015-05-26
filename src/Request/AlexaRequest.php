<?php  namespace Develpr\AlexaApp\Request; 

interface AlexaRequest
{

    /**
     * returns the request type, i.e. IntentRequest
     *
     * @return mixed
     */
    public function getRequestType();

	/**
	 * Is this request formatted as an Amazon Echo/Alexa request?
	 *
	 * @return bool
	 */
	public function isAlexaRequest();

	/**
	 * Get the UserId provided in the request
	 *
	 * @return mixed
	 */
	public function getUserId();

	/**
	 * Attempt to return an IntentRequest
	 *
	 * @return IntentRequest|null
	 */
	public function toIntentRequest();

	/**
	 * Attempt to return a SessionEndedRequest
	 *
	 * @return SessionEndedRequest|null
	 */
	public function toSessionEndedRequest();

	/**
	 * Attempt to return a LaunchRequest
	 *
	 * @return LaunchRequest|null
	 */
	public function toLaunchRequest();

	/**
	 * Get all of the session values in an array
	 *
	 * @return array
	 */
	public function getSession();

	/**
	 * Get a particular session value by key
	 *
	 * @param String $key
	 * @return mixed|null
	 */
	public function getSessionValue($key = null);


} 
