<?php

namespace Frijj2k\LarAlexa\Contracts;

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
