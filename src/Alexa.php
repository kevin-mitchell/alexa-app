<?php

namespace Frijj2k\LarAlexa;

use Frijj2k\LarAlexa\Contracts\AmazonEchoDevice;
use Frijj2k\LarAlexa\Contracts\DeviceProvider;
use Frijj2k\LarAlexa\Request\AlexaRequest;
use Frijj2k\LarAlexa\Response\AlexaResponse;
use Frijj2k\LarAlexa\Response\AudioFile;
use Frijj2k\LarAlexa\Response\Card;
use Frijj2k\LarAlexa\Response\Speech;
use Frijj2k\LarAlexa\Response\SSML;

class Alexa
{
    /**
     * @var \Frijj2k\LarAlexa\Contracts\AlexaRequest
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
     * @return \Frijj2k\LarAlexa\Contracts\AlexaRequest
     */
    public function request()
    {
        return $this->alexaRequest;
    }

    /**
     * @return \Frijj2k\LarAlexa\Response\AlexaResponse
     */
    public function response()
    {
        return new AlexaResponse();
    }

    /**
     * @param string $statementWords
     * @param string $speechType
     *
     * @return \Frijj2k\LarAlexa\Response\AlexaResponse
     */
    public function say($statementWords, $speechType = Speech::DEFAULT_TYPE)
    {
        $response = new AlexaResponse(new Speech($statementWords, $speechType));

        return $response;
    }

    /**
     * @param string $audioURI
     *
     * @return \Frijj2k\LarAlexa\Response\AlexaResponse
     */
    public function playAudio($audioURI)
    {
        $audio = new AudioFile();
        $audio->addAudioFile($audioURI);

        $response = new AlexaResponse($audio);

        return $response;
    }

    /**
     * @param string $ssmlValue
     *
     * @return \Frijj2k\LarAlexa\Response\AlexaResponse
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
     * @return \Frijj2k\LarAlexa\Response\AlexaResponse
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
     * @return \Frijj2k\LarAlexa\Response\AlexaResponse
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
     * @return \Frijj2k\LarAlexa\Contracts\AmazonEchoDevice|null
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
}
