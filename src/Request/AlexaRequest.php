<?php  namespace Develpr\AlexaApp\Request;

use Illuminate\Http\Request;

class AlexaRequest extends Request implements \Develpr\AlexaApp\Contracts\AlexaRequest{

	private $data = null;
	private $processed = false;

	protected function getData()
	{
		if( ! $this->processed )
			$this->process();

		return $this->data;
	}

	/**
	 * returns the request type, i.e. IntentRequest
	 *
	 * @return mixed
	 */
	public function getRequestType()
	{
		return array_get($this->getData(), 'request.type');
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
	 * Get the UserId provided in the request
	 *
	 * @return mixed
	 */
	public function getUserId()
	{
		return array_get($this->getData(), 'session.user.userId');
	}

	/**
	 * Get the unique Application Id
	 *
	 * @return mixed
	 */
	public function getAppId()
	{
		return array_get($this->getData(), 'session.application.applicationId');
	}

	/**
	 * Get all of the session values in an array
	 *
	 * @return array
	 */
	public function getSession()
	{
		$sessionAttributes = array_get($this->getData(), 'session.attributes');

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


	private function process()
	{
		$data = $this->getContent();
		$this->data = json_decode($data, true);
		$this->processed = true;
	}


} 