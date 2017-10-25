<?php

namespace Develpr\AlexaApp\Device;

use Develpr\AlexaApp\Contracts\AmazonEchoDevice;
use Illuminate\Database\Eloquent\Model;

class Device extends Model implements AmazonEchoDevice
{
    protected $table = 'alexa_devices';

    protected $hidden = ['password'];

    /**
     * @return mixed
     */
    public function getDeviceId()
    {
        return $this->device_user_id;
    }

    /**
     * @param mixed $deviceId
     */
    public function setDeviceId($deviceId)
    {
        $this->attributes['device_user_id'] = $deviceId;
    }
}
