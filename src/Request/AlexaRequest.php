<?php

namespace Develpr\AlexaApp\Request;

use Illuminate\Http\Request;

class AlexaRequest extends Request implements \Develpr\AlexaApp\Contracts\AlexaRequest
{
    const CONFIRMED_STATUS = 'CONFIRMED';
    const DENIED_STATUS = 'DENIED';
    const NO_CONFIRMATION_STATUS = 'NONE';

    private $data = null;
    private $processed = false;
    private $intent = null;
    private $slots = [];
    private $promptResponse = null;
    private $confirmationStatus = null;

    protected function getData()
    {
        if (!$this->processed) {
            $this->process();
        }

        return $this->data;
    }

    public function isProcessed()
    {
        return $this->processed;
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
        return array_get($this->getData(), $this->hasSession() ? 'session.user.userId' : 'context.System.user.userId');
    }

    /**
     * Get the accessToken provided in the request
     *
     * @return mixed
     */
    public function getAccessToken()
    {
        return array_get($this->getData(), $this->hasSession() ? 'session.user.accessToken' : 'context.System.user.accessToken');
    }

    /**
     * Get the unique Application Id
     *
     * @return mixed
     */
    public function getAppId()
    {
        return  array_get($this->getData(), $this->hasSession() ? 'session.application.applicationId' : 'context.System.application.applicationId');
    }

    /**
     * Checks if the request contains session informatiom
     *
     * @return bool
     */
    public function hasSession()
    {
        return array_has($this->getData(), 'session');
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
     * Get the contect in an array
     *
     * @return array
     */
    public function getContext()
    {
        $context = array_get($this->getData(), 'context');

        if (!$context) {
            return [];
        }

        return $context;
    }

    /**
     * Get the dialog state possible values are:
     * "STARTED", "IN_PROGRESS", or "COMPLETED"
     *
     * @return string|null
     */
    public function dialogState()
    {
        return array_get($this->getData(), 'request.dialogState');
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
     * Update a slot
     *
     * @param $slotName
     * @param $value
     * @param bool $confirmed
     * @param bool $denied
     *
     * @return $this
     */
    public function updateSlot($slotName, $value, $confirmed = null)
    {
        if (!$this->processed) {
            $this->process();
        }

        if (array_has($this->slots, [$slotName])) {
            $this->slots[$slotName]['value'] = $value;

            if ($confirmed) {
                $this->slots[$slotName]['confirmationStatus'] = $this::CONFIRMED_STATUS;
            } elseif (!is_null($confirmed) && !$confirmed) {
                $this->slots[$slotName]['confirmationStatus'] = $this::DENIED_STATUS;
            } else {
                $this->slots[$slotName]['confirmationStatus'] = $this::NO_CONFIRMATION_STATUS;
            }
        }

        return $this;
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
        $this->confirmationStatus = array_get($this->data, 'request.intent.confirmationStatus', '');

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

    public function getConfirmationStatus()
    {
        if (!$this->processed) {
            $this->process();
        }

        return $this->confirmationStatus;
    }
}
