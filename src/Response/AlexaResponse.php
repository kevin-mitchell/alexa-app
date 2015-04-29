<?php  namespace Develpr\AlexaApp\Response; 

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\Response;
use Illuminate\Session\SessionManager;
use Symfony\Component\HttpFoundation\Request;

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
     * @var Card
     */
    private $card = null;

    function __construct(Speech $speech = null, Card $card = null, $shouldSessionEnd = false)
    {
        $this->card = $card;
        $this->speech = $speech;
        $this->shouldSessionEnd = $shouldSessionEnd;

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
     * This method will build the Alexa valid response data
     *
     * @return array
     */
    private function prepareResponseData()
    {
        $responseData = [];

        /** @var SessionManager $sessionData */
        $sessionData = session();


        $responseData['version'] = self::ALEXA_RESPONSE_VERSION;

        $response = [
            'shouldEndSession' => $this->shouldSessionEnd
        ];

        if( ! is_null($this->card) && $this->card instanceof Card )
            $response['card'] = $this->card->toArray();

        if( ! is_null($this->speech) && $this->speech instanceof Speech )
            $response['outputSpeech'] = $this->speech->toArray();

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

    /**
     * @param null $speech
     */
    public function setSpeech(Speech $speech)
    {
        $this->speech = $speech;

        return $this;
    }

    private function getSessionData()
    {
        $data = \Session::all();

        return $data;
    }



} 
