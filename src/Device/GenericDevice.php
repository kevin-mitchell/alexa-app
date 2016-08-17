<?php

namespace Develpr\AlexaApp\Device;

use Develpr\AlexaApp\Contracts\AmazonEchoDevice;

class GenericDevice implements AmazonEchoDevice
{
    /**
     * @var array
     */
    protected $attributes;

    /**
     * GenericDevice constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * @return mixed
     */
    public function getDeviceId()
    {
        return $this->attributes['device_user_id'];
    }

    /**
     * @param mixed $deviceId
     */
    public function setDeviceId($deviceId)
    {
        $this->attributes['device_user_id'] = $deviceId;
    }

    /**
     * Dynamically access the user's attributes.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->attributes[$key];
    }

    /**
     * Dynamically set an attribute on the user.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Dynamically check if a value is set on the user.
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }

    /**
     * Dynamically unset a value on the user.
     *
     * @param string $key
     */
    public function __unset($key)
    {
        unset($this->attributes[$key]);
    }
}
