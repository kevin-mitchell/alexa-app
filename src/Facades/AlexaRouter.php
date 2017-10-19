<?php

namespace Pallant\AlexaApp\Facades;

use Illuminate\Support\Facades\Facade;

class AlexaRouter extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'alexa.router';
    }
}
