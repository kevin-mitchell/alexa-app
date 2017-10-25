<?php

namespace Develpr\AlexaApp\Contracts;

interface DeviceProvider
{
    /**
     * Retrieve a device by the given credentials.
     *
     * @param array $credentials
     *
     * @return AmazonEchoDevice|null
     */
    public function retrieveByCredentials(array $credentials);
}
