<?php

namespace Develpr\AlexaApp\Response\Directives\Dialog;

use Develpr\AlexaApp\Response\Directives\Directive;

class Delegate extends Directive
{
    const TYPE = 'Dialog.Delegate';


    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'type' => self::TYPE
        ];
    }
}
