<?php

namespace Develpr\AlexaApp\Contracts;

interface AlexaRequest
{
    /**
     * returns the request type, i.e. IntentRequest
     *
     * @return mixed
     */
    public function getRequestType();

    /**
     * Is this request formatted as an Amazon Echo/Alexa request?
     *
     * @return bool
     */
    public function isAlexaRequest();

    /**
     * Get the UserId provided in the request
     *
     * @return mixed
     */
    public function getUserId();


    /**
     * Get the accessToken provided in the request
     *
     * @return mixed
     */
    public function getAccessToken();

    /**
     * Get the unique Application Id
     *
     * @return mixed
     */
    public function getAppId();

    /**
     * Get all of the session values in an array
     *
     * @return array
     */
    public function getSession();

    /**
     * Get a particular session value by key
     *
     * @param string $key
     *
     * @return mixed|null
     */
    public function getSessionValue($key = null);

    /**
     * @return int
     */
    public function getTimestamp();
}
