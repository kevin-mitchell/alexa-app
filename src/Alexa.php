<?php

namespace Develpr\AlexaApp;

use Develpr\AlexaApp\Contracts\AmazonEchoDevice;
use Develpr\AlexaApp\Contracts\DeviceProvider;
use Develpr\AlexaApp\Request\AlexaRequest;
use Develpr\AlexaApp\Response\AlexaResponse;
use Develpr\AlexaApp\Response\AudioFile;
use Develpr\AlexaApp\Response\Card;
use Develpr\AlexaApp\Response\Directives\AudioPlayer\Play;
use Develpr\AlexaApp\Response\Directives\AudioPlayer\Stop;
use Develpr\AlexaApp\Response\Reprompt;
use Develpr\AlexaApp\Response\Speech;
use Develpr\AlexaApp\Response\SSML;

class Alexa
{
    /**
     * @var \Develpr\AlexaApp\Contracts\AlexaRequest
     */
    private $alexaRequest;

    /**
     * @var array
     */
    private $session;

    /**
     * @var array
     */
    private $context;

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

    /**
     * Alexa constructor.
     *
     * @param AlexaRequest   $alexaRequest
     * @param DeviceProvider $deviceProvider
     * @param array          $alexaConfig
     */
    public function __construct(AlexaRequest $alexaRequest, DeviceProvider $deviceProvider, array $alexaConfig)
    {
        $this->alexaRequest = $alexaRequest;
        $this->deviceProvider = $deviceProvider;

        $this->setupSession();
        $this->setupContext();

        $this->alexaConfig = $alexaConfig;

        $this->isAlexaRequest = $this->alexaRequest->isAlexaRequest();
    }

    /**
     * @return bool
     */
    public function isAlexaRequest()
    {
        return $this->isAlexaRequest;
    }

    /**
     * @return mixed
     */
    public function requestType()
    {
        return $this->alexaRequest->getRequestType();
    }

    /**
     * @return \Develpr\AlexaApp\Contracts\AlexaRequest
     */
    public function request()
    {
        return $this->alexaRequest;
    }

    /**
     * @return \Develpr\AlexaApp\Response\AlexaResponse
     */
    public function response()
    {
        return new AlexaResponse();
    }

    /**
     * @param string $statementWords
     * @param string $speechType
     *
     * @return \Develpr\AlexaApp\Response\AlexaResponse
     */
    public function say($statementWords, $speechType = Speech::DEFAULT_TYPE)
    {
        $response = new AlexaResponse(new Speech($statementWords, $speechType));

        return $response;
    }

    /**
     * @param string $statementWords
     * @param string $repromptWords
     * @param string $speechType
     *
     * @return \Develpr\AlexaApp\Response\AlexaResponse
     */
    public function reprompt($statementWords, $repromptWords = ' ', $speechType = Speech::DEFAULT_TYPE)
    {
        $response = $this->say($statementWords, $speechType);
        $response->setReprompt(new Reprompt($repromptWords, $speechType))->endSession(false);
        return $response;
    }

    /**
     * @param string $audioURI
     *
     * @return \Develpr\AlexaApp\Response\AlexaResponse
     */
    public function playAudio($audioURI)
    {
        $audio = new AudioFile();
        $audio->addAudioFile($audioURI);

        $response = new AlexaResponse($audio);

        return $response;
    }

    /**
     * @param $url
     * @param null $token
     * @param null $playBehavior
     * @param null $offsetInMilliseconds
     * @param null $expectedPreviousToken
     *
     * @return AlexaResponse
     */
    public function play($url, $token = null, $offsetInMilliseconds = null, $playBehavior = null, $expectedPreviousToken = null)
    {
        if(config('alexa.audio.proxy.enabled'))
        {
            $url = url(config('alexa.audio.proxy.route') . '/' . base64_encode($url));
        }
        
        $audio = new Play($url, $token, $offsetInMilliseconds, $playBehavior, $expectedPreviousToken);

        $response = new AlexaResponse();
        $response->setAudio($audio);

        // Cache the URL that belongs to the token so that we can resume later.
        cache([$audio->getToken() => $audio->getUrl()], 48*60);

        return $response;
    }

    /**
     * Returns a stop response
     *
     * @return AlexaResponse
     */
    public function pause()
    {
        $response = new AlexaResponse();
        $response->withDirective(new Stop());

        return $response;
    }

    /**
     * Resumes from the previous position
     *
     * @return AlexaResponse
     */
    public function resume()
    {
        $token = $this->context('AudioPlayer.token');
        $url = cache($token);
        $offset = $this->context('AudioPlayer.offsetInMilliseconds');
        return $this->play($url, $token, $offset);
    }

    /**
     * @param string $ssmlValue
     *
     * @return \Develpr\AlexaApp\Response\AlexaResponse
     */
    public function ssml($ssmlValue)
    {
        $ssml = new SSML();
        $ssml->setValue($ssmlValue);

        $response = new AlexaResponse($ssml);

        return $response;
    }

    /**
     * @param string $question
     *
     * @return \Develpr\AlexaApp\Response\AlexaResponse
     */
    public function ask($question)
    {
        $response = new AlexaResponse(new Speech($question));

        $response->setIsPrompt(true);

        return $response;
    }

    /**
     * @param string $title
     * @param string $subtitle
     * @param string $content
     *
     * @return \Develpr\AlexaApp\Response\AlexaResponse
     */
    public function card($title = '', $subtitle = '', $content = '')
    {
        $response = new AlexaResponse();

        $response->setCard(new Card($title, $subtitle, $content));

        return $response;
    }

    /**
     * @param array $attributes
     *
     * @return \Develpr\AlexaApp\Contracts\AmazonEchoDevice|null
     */
    public function device($attributes = [])
    {
        if (!$this->isAlexaRequest()) {
            return;
        }

        if (!is_null($this->device)) {
            return $this->device;
        }

        if (!array_key_exists($this->alexaConfig['device']['device_identifier'], $attributes)) {
            $attributes[$this->alexaConfig['device']['device_identifier']] = $this->alexaRequest->getUserId();
        }

        $result = $this->deviceProvider->retrieveByCredentials($attributes);

        if ($result instanceof AmazonEchoDevice) {
            $this->device = $result;
        }

        return $result;
    }

    /**
     * @param string $requestedSlot
     *
     * @return mixed|null
     */
    public function slot($requestedSlot = '')
    {
        return $this->alexaRequest->slot($requestedSlot);
    }

    /**
     * @return array
     */
    public function slots()
    {
        return $this->alexaRequest->slots();
    }

    public function updateSlot($slotName, $value, $confirmed = false) {
        return $this->alexaRequest->updateSlot($slotName, $value, $confirmed);
    }

    /**
     * @param string|null $key
     * @param mixed|null  $value
     *
     * @return array|mixed|null
     */
    public function session($key = null, $value = null)
    {
        if (!is_null($value)) {
            $this->setSession($key, $value);
        } elseif (is_null($key)) {
            return $this->session;
        } else {
            return array_key_exists($key, $this->session) ? $this->session[$key] : null;
        }
    }

    /**
     * @param string|null $key
     *
     * @return array|mixed|null
     */
    public function context($key = null)
    {
        if (is_null($key)) {
            return $this->context;
        } else {
            return array_get($this->context, $key);
        }
    }

    /**
     * @param string|array $key
     * @param mixed|null   $value
     */
    public function setSession($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $aKey => $aValue) {
                $this->session[$aKey] = $aValue;
            }
        } elseif (!is_null($key)) {
            $this->session[$key] = $value;
        }
    }

    /**
     * @param string $key
     */
    public function unsetSession($key)
    {
        unset($this->session[$key]);
    }

    private function setupSession()
    {
        $this->session = $this->alexaRequest->getSession();
    }

    private function setupContext()
    {
        $this->context = $this->alexaRequest->getContext();
    }
}
