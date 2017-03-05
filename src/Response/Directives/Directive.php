<?php

namespace Develpr\AlexaApp\Response\Directives;

use Illuminate\Contracts\Support\Arrayable;

abstract class Directive implements Arrayable
{
    /**
     * @param string $attributeName
     * @param array  $outputArray
     */
    protected function addAttributeToArray($attributeName, array &$outputArray)
    {
        if (!is_string($this->$attributeName) || strlen($this->$attributeName) > 0) {
            $outputArray[$attributeName] = $this->$attributeName;
        }
    }
}
