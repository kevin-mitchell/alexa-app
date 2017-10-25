<?php

namespace Develpr\AlexaApp\Response;

class Reprompt extends Speech
{
    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $textKey = ($this->getType() === 'SSML') ? 'ssml' : 'text';

        return [
            'outputSpeech' => [
                'type' => $this->getType(),
                $textKey => $this->getValue(),
            ],
        ];
    }
}
