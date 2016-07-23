<?php  namespace Develpr\AlexaApp\Response; 

use Illuminate\Contracts\Support\Jsonable;
use Develpr\AlexaApp\Contracts\OutputSpeech as Speech;
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
	 * @var \Develpr\AlexaApp\Alexa
	 */
	private $alexa;

	/**
	 * The Intent that a response should be routed to if a response comes in
	 *
	 * @var string | null
	 */
	private $promptResponseIntent = null;

	/**
	 * @param Speech $speech
	 * @param Card $card
	 * @param Reprompt $reprompt
	 */
	function __construct(Speech $speech = null, Card $card = null, Reprompt $reprompt = null)
    {
		$this->speech = $speech;
		$this->card = $card;
		$this->card = $reprompt;

		//this... I'm not sure about this... I might at some point attach the session data
		//to the response in middleware to remove this somewhat hacky feeling dependency
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

        return json_encode($alexaResponseData, JSON_UNESCAPED_SLASHES);
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

	public function withCard(Card $card)
	{
		return $this->setCard($card);
	}

	public function withReprompt(Reprompt $reprompt)
	{
		return $this->setReprompt($reprompt);
	}

	public function withSpeech(Speech $speech)
	{
		return $this->setSpeech($speech);
	}

	/**
	 * @param Card $card
	 * @return $this
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
	 * @param Speech $speech
	 * @return $this
	 */
	public function setSpeech(Speech $speech)
    {
        $this->speech = $speech;

        return $this;
    }

	/**
	 * @param Reprompt $reprompt
	 * @return $this
	 */
	public function setReprompt(Reprompt $reprompt)
	{
		$this->reprompt = $reprompt;

		return $this;
	}

	public function setPromptResponseIntent($intent){
		$this->promptResponseIntent = $intent;
		return $this;
	}

	public function sendResponseTo($intent){
		return $this->setPromptResponseIntent($intent);
	}

    private function getSessionData()
    {
        $data = $this->alexa->session();

		if($this->isPrompt()){
			$data['possible_prompt_response'] = true;
			if($this->promptResponseIntent)
				$data['prompt_response_intent'] = $this->promptResponseIntent;
			if($this->speech)
				$data['original_prompt'] = $this->speech->getValue();
			if($this->alexa->requestType() == "IntentRequest")
				$data['original_prompt_intent'] = $this->alexa->request()->getIntent();
		}

        return $data;
    }



} 
