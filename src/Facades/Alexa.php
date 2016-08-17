<?php

namespace Develpr\AlexaApp\Facades;

use Illuminate\Support\Facades\Facade;

class Alexa extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'alexa';
    }
}
