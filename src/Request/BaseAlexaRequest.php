<?php  namespace Develpr\AlexaApp\Request; 

use Illuminate\Http\Request;

abstract class BaseAlexaRequest implements AlexaRequest
{
    private $data = [];

    function __construct(Request $request)
    {
        $this->data = $this->extractRequestData($request);

        $this->setupRequest($this->data);

    }

    /**
     * Return an array of
     *
     * @param array $data
     * @return mixed
     */
    protected abstract function setupRequest(array $data);

    /**
     * @param $request
     */
    private function extractRequestData($request)
    {
		$data = json_decode($request->getContent(), true);

        return $data;
    }

    /**
     * returns the request type, i.e. IntentRequest
     *
     * @return string
     */
    public function getRequestType()
    {
        return array_get($this->data, 'request.type');
    }

	/**
	 * Is this request formatted as an Amazon Echo/Alexa request?
	 *
	 * @return bool
	 */
	public function isAlexaRequest()
	{
		return !(is_null($this->getRequestType()));
	}

	/**
	 * Return the user id provided in in the request
	 *
	 * @return mixed
	 */
	public function getUserId()
	{
		return array_get($this->data, 'session.user.userId');
	}

	/**
	 * Get the unique Application Id
	 *
	 * @return mixed
	 */
	public function getAppId()
	{
		return array_get($this->data, 'session.application.applicationId');
	}

	/**
	 * Attempt to return an IntentRequest
	 *
	 * @return IntentRequest|null
	 */
	public function toIntentRequest()
	{
		if( ! $this->isAlexaRequest() || ! $this->getRequestType() == "IntentRequest")
			return null;

		else
			return $this;
	}

	/**
	 * Attempt to return a SessionEndedRequest
	 *
	 * @return SessionEndedRequest|null
	 */
	public function toSessionEndedRequest()
	{
		if( ! $this->isAlexaRequest() || ! $this->getRequestType() == "SessionEndedRequest")
			return null;

		else
			return $this;
	}

	/**
	 * Attempt to return a LaunchRequest
	 *
	 * @return LaunchRequest|null
	 */
	public function toLaunchRequest()
	{
		if( ! $this->isAlexaRequest() || ! $this->getRequestType() == "LaunchRequest")
			return null;

		else
			return $this;
	}

	/**
	 * Get all of the session values in an array
	 *
	 * @return array
	 */
	public function getSession()
	{
		$sessionAttributes = array_get($this->data, 'session.attributes');

		if(! $sessionAttributes)
			return [];

		return $sessionAttributes;
	}

	/**
	 * Get a particular session value by key
	 *
	 * @param String $key
	 * @return mixed|null
	 */
	public function getSessionValue($key = null)
	{
		return array_key_exists($key, $this->getSession()) ? $this->getSession()[$key] : null;
	}





} 
