<?php namespace Develpr\AlexaApp\Device;

use Develpr\AlexaApp\Contracts\AmazonEchoDevice;
use Illuminate\Database\Eloquent\Model;

class Device extends Model implements AmazonEchoDevice{

    protected $table = "alexa_devices";

    protected $hidden = array('password');

    public function getDeviceId()
    {
        return $this->device_user_id;
    }

    public function setDeviceId($deviceId)
    {
        $this->attributes['device_user_id'] = $deviceId;
    }

}
