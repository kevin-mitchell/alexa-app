<?php namespace Develpr\AlexaApp;

use Develpr\AlexaApp\Contracts\AmazonEchoDevice;
use Develpr\AlexaApp\Contracts\DeviceProvider;
use Develpr\AlexaApp\Request\AlexaRequest;
use Develpr\AlexaApp\Response\AlexaResponse;
use Develpr\AlexaApp\Response\AudioFile;
use Develpr\AlexaApp\Response\Card;
use Develpr\AlexaApp\Response\Speech;
use Develpr\AlexaApp\Response\SSML;

class Alexa {

	/**
	 * @var \Develpr\AlexaApp\Contracts\AlexaRequest
	 */
	private $alexaRequest;

	/**
	 * @var array
	 */
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
	 * @var AmazonEchoDevice | null
	 */
	private $device;

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

	public function say($statementWords, $speechType = Speech::DEFAULT_TYPE)
	{
		$response = new AlexaResponse(new Speech($statementWords, $speechType));

		return $response;
	}

	public function playAudio($audioURI)
	{
		$audio = new AudioFile();
		$audio->addAudioFile($audioURI);

		$response = new AlexaResponse($audio);

		return $response;
	}

	public function ssml($ssml)
	{
		$ssml = new SSML();
		$ssml->setValue($ssml);

		$response = new AlexaResponse($ssml);

		return $response;
	}

	public function ask($question)
	{
		$response = new AlexaResponse(new Speech($question));

		$response->setIsPrompt(true);

		return $response;
	}

	public function card($title = "", $subtitle = "", $content = "")
	{
		$response = new AlexaResponse();

		$response->setCard(new Card($title, $subtitle, $content));

		return $response;
	}

	public function device($attributes = [])
	{

		if( ! $this->isAlexaRequest() )
			return null;

		if( ! is_null($this->device) )
			return $this->device;

		if( ! array_key_exists($this->alexaConfig['device']['device_identifier'], $attributes))
			$attributes[$this->alexaConfig['device']['device_identifier']] = $this->alexaRequest->getUserId();

		$result = $this->deviceProvider->retrieveByCredentials($attributes);

		if($result instanceof AmazonEchoDevice)
			$this->device = $result;

		return $result;
	}

	public function slot($requestedSlot = "")
	{
		return $this->alexaRequest->slot($requestedSlot);
	}

	public function slots()
	{
		return $this->alexaRequest->slots();
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
