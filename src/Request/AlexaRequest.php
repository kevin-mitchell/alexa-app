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

} 
