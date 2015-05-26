<?php
/**
 * Created by PhpStorm.
 * User: shoelessone
 * Date: 5/21/15
 * Time: 2:46 PM
 */

namespace Develpr\AlexaApp\Domain;


use Develpr\AlexaApp\Request\AlexaRequest;

class Alexa {

	/**
	 * @var \Develpr\AlexaApp\Request\AlexaRequest
	 */
	private $alexaRequest;
	private $session;

	public function __construct(AlexaRequest $alexaRequest)
	{
		$this->alexaRequest = $alexaRequest;

		$this->setupSession();
	}

	public function isAlexaRequest()
	{
		return $this->alexaRequest->isAlexaRequest();
	}

	public function requestType()
	{
		return $this->alexaRequest->getRequestType();
	}

	public function request()
	{
		return $this->alexaRequest;
	}

	public function slot($requestedSlot = "")
	{
		if( $this->alexaRequest->getRequestType() != "IntentRequest"){
			return null;
		}

		return $this->alexaRequest->toIntentRequest()->slot($requestedSlot);

	}

	public function slots()
	{
		if( $this->alexaRequest->getRequestType() != "IntentRequest"){
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
