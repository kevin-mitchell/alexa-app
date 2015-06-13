<?php  namespace Develpr\AlexaApp\Response; 

use Develpr\AlexaApp\Device\Alexa;
use Illuminate\Contracts\Support\Jsonable;

/**
 * This class represents a valid response to be send back to Alexa.
 *
 * Class AlexaResponse
 * @package Develpr\AlexaApp\Response
 */
class AlexaResponse implements Jsonable
{
    const ALEXA_RESPONSE_VERSION = "1.0";

    /**
     * A key-value pair of session attributes
     * @var array
     */
    private $sessionAttributes = [];

    /**
     * Should the session be ended
     * @var bool
     */
    private $shouldSessionEnd = false;

    /**
     * @var Speech
     */
    private $speech = null;

    /**
     * @var Reprompt
     */
    private $reprompt = null;

	/**
	 * Does this response represent a prompt?
	 *
	 * @var bool
	 */
	private $isPrompt = false;

    /**
     * @var Card
     */
    private $card = null;

	/**
	 * @var Alexa
	 */
	private $alexa;

	function __construct(Speech $speech = null, Card $card = null, Reprompt $reprompt = null)
    {
		$this->speech = $speech;
		$this->card = $card;
		$this->card = $reprompt;
		$this->alexa = app()->make('alexa');

		return $this;
	}


    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        $alexaResponseData = $this->prepareResponseData();

        return json_encode($alexaResponseData);
    }

    /**
     * Set whether or not the session should be ended
     *
     * @param bool $shouldEnd
     * @return $this
     */
    public function endSession($shouldEnd = true)
    {
        $this->shouldSessionEnd = $shouldEnd;

        return $this;
    }

    /**
     * Build the valid Alexa response object
     *
     * @return array
     */
    private function prepareResponseData()
    {
        $responseData = [];

        $responseData['version'] = self::ALEXA_RESPONSE_VERSION;

        $response = [
            'shouldEndSession' => $this->shouldSessionEnd
        ];

		//Check to see if a speech, card, or reprompt object are set and if so
		//add them to the data object
        if( ! is_null($this->speech) && $this->speech instanceof Speech )
            $response['outputSpeech'] = $this->speech->toArray();
        if( ! is_null($this->card) && $this->card instanceof Card )
            $response['card'] = $this->card->toArray();
		if( ! is_null($this->reprompt) && $this->reprompt instanceof Reprompt )
			$response['reprompt'] = $this->reprompt->toArray();

		$sessionAttributes = $this->getSessionData();

		if($sessionAttributes && count($sessionAttributes) > 0)
        	$responseData['sessionAttributes'] = $sessionAttributes;

        $responseData['response'] = $response;

        return $responseData;

    }

    /**
     * @param array $sessionAttributes
     */
    public function setSessionAttributes(array $sessionAttributes)
    {
        $this->sessionAttributes = $sessionAttributes;
    }

    /**
     * @param null $card
     */
    public function setCard(Card $card)
    {
        $this->card = $card;

        return $this;
    }

	public function isPrompt()
	{
		return boolval($this->isPrompt);
	}

	public function setIsPrompt($prompt = false)
	{
		if(is_bool($prompt))
			$this->isPrompt = $prompt;

		return $this;
	}

    /**
     * @param null $speech
     */
    public function setSpeech(Speech $speech)
    {
        $this->speech = $speech;

        return $this;
    }

	/**
	 * @param null $reprompt
	 */
	public function setReprompt(Reprompt $reprompt)
	{
		$this->reprompt = $reprompt;

		return $this;
	}

    private function getSessionData()
    {
        $data = $this->alexa->session();

		if($this->isPrompt()){
			$data['possible_prompt_response'] = true;
			if($this->speech)
				$data['original_prompt'] = $this->speech->getText();
			if($this->alexa->requestType() == "IntentRequest")
				$data['original_prompt_intent'] = $this->alexa->request()->toIntentRequest()->getIntent();
		}

        return $data;
    }



} 
