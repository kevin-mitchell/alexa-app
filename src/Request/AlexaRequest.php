<?php

namespace Develpr\AlexaApp\Request;

use Illuminate\Http\Request;

class AlexaRequest extends Request implements \Develpr\AlexaApp\Contracts\AlexaRequest
{
    private $data = null;
    private $processed = false;
    private $intent = null;
    private $slots = [];
    private $promptResponse = null;

    protected function getData()
    {
        if (!$this->processed) {
            $this->process();
        }

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
     * @return string|null
     */
    public function getPromptResponseIntent()
    {
        $intent = trim($this->getSessionValue('original_prompt_intent'));

        return (strlen($intent) > 0) ? $intent : null;
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
     * Is this a new session according to Amazon AlexaSkillsKit?
     *
     * @return bool
     */
    public function isNewSession()
    {
        return boolval(array_get($this->getData(), 'session.new'));
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
     * Get the accessToken provided in the request
     *
     * @return mixed
     */
    public function getAccessToken()
    {
        return array_get($this->getData(), 'session.user.accessToken');
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

        if (!$sessionAttributes) {
            return [];
        }

        return $sessionAttributes;
    }

    /**
     * Get a particular session value by key
     *
     * @param string $key
     *
     * @return mixed|null
     */
    public function getSessionValue($key = null)
    {
        return array_key_exists($key, $this->getSession()) ? $this->getSession()[$key] : null;
    }

    /**
     * @return string|null
     */
    public function getIntent()
    {
        return array_get($this->getData(), 'request.intent.name');
    }

    /**
     * @param string     $slotKey
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    public function slot($slotKey, $default = null)
    {
        if (!$this->processed) {
            $this->process();
        }

        $key_exists = (array_key_exists($slotKey, $this->slots));

        if (!$key_exists) {
            return $default;
        }

        return (array_key_exists('value', $this->slots[$slotKey])) ? $this->slots[$slotKey]['value'] : $default;
    }

    /**
     * @return array
     */
    public function slots()
    {
        if (!$this->processed) {
            $this->process();
        }

        return $this->slots;
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return strtotime(array_get($this->getData(), 'request.timestamp'));
    }

    private function process()
    {
        $data = $this->getContent();
        $this->data = json_decode($data, true);
        $this->intent = array_get($this->data, 'request.intent.name');
        $this->slots = array_get($this->data, 'request.intent.slots', []);

        $this->processed = true;
    }

    /**
     * @param bool $promptResponse
     */
    public function setPromptResponse($promptResponse)
    {
        $this->promptResponse = $promptResponse;
    }

    /**
     * @return bool
     */
    public function isPromptResponse()
    {
        return boolval($this->promptResponse);
    }
}
