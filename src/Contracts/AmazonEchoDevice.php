<?php

namespace Develpr\AlexaApp\Contracts;

interface AmazonEchoDevice
{
    /**
     * @return mixed
     */
    public function getDeviceId();

    /**
     * @param mixed $deviceId
     */
    public function setDeviceId($deviceId);
}
