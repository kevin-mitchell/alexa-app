<?php namespace Develpr\AlexaApp;

use Develpr\AlexaApp\Contracts\DeviceProvider;
use Develpr\AlexaApp\Contracts\AlexaRequest;
use Develpr\AlexaApp\Response\AlexaResponse;
use Develpr\AlexaApp\Response\Speech;

class Alexa {

	/**
	 * @var \Develpr\AlexaApp\Contracts\AlexaRequest
	 */
	private $alexaRequest;

	private $session;

	/**
	 * @var Contracts\DeviceProvider
	 */
	private $deviceProvider;

	/**
	 * @var array
	 */
	private $alexaConfig;

	/**
	 * @var bool
	 */
	private $isAlexaRequest;

	public function __construct(AlexaRequest $alexaRequest, DeviceProvider $deviceProvider, array $alexaConfig)
	{
		$this->alexaRequest = $alexaRequest;
		$this->deviceProvider = $deviceProvider;

		$this->setupSession();

		$this->alexaConfig = $alexaConfig;

		$this->isAlexaRequest = $this->alexaRequest->isAlexaRequest();
	}

	public function isAlexaRequest()
	{
		return $this->isAlexaRequest;
	}

	public function requestType()
	{
		return $this->alexaRequest->getRequestType();
	}

	public function request()
	{
		return $this->alexaRequest;
	}

	public function response()
	{
		return new AlexaResponse;
	}

	public function say($statementWords)
	{
		$response = new AlexaResponse(new Speech($statementWords));

		return $response;
	}

	public function ask($question)
	{
		$response = new AlexaResponse(new Speech($question));

		$response->setIsPrompt(true);

		return $response;
	}

	public function device($attributes = [])
	{

		if( ! $this->isAlexaRequest() )
			return null;

		if( ! array_key_exists($this->alexaConfig['device']['device_identifier'], $attributes))
			$attributes[$this->alexaConfig['device']['device_identifier']] = $this->alexaRequest->getUserId();

		$result = $this->deviceProvider->retrieveByCredentials($attributes);

		return $result;
	}

	public function slot($requestedSlot = "")
	{

		if( ! $this->isAlexaRequest() || $this->alexaRequest->getRequestType() != "IntentRequest"){
			return null;
		}

		return $this->alexaRequest->toIntentRequest()->slot($requestedSlot);

	}

	public function slots()
	{
		if( ! $this->isAlexaRequest() || $this->alexaRequest->getRequestType() != "IntentRequest"){
			return null;
		}

		return $this->alexaRequest->toIntentRequest()->slots();
	}

	public function session($key = null, $value = null)
	{
		if( ! is_null($value) ){
			$this->setSession($key, $value);
		}
		else if( is_null($key) ){
			return $this->session;
		}
		else{
			return array_key_exists($key, $this->session) ? $this->session[$key] : null;
		}
	}

	public function setSession($key, $value = null)
	{
		if( is_array($key) ){
			foreach($key as $aKey => $aValue){
				$this->session[$aKey] = $aValue;
			}
		}
		else if( ! is_null($key) ) {
			$this->session[$key] = $value;
		}
	}

	public function unsetSession($key)
	{
		unset($this->session[$key]);
	}


	private function setupSession()
	{
		$this->session = $this->alexaRequest->getSession();
	}


}
