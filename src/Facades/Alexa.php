<?php

namespace Frijj2k\LarAlexa\Facades;

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
