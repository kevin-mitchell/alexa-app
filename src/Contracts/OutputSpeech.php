<?php

namespace Develpr\AlexaApp\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface OutputSpeech extends Arrayable
{
    /**
     * The "value" represents the actual speech "output" - i.e. the text to be spoken or ssml to be output
     *
     * @return string
     */
    public function getValue();

    /**
     * The type of speech (i.e. SSML, PlainText, etc)
     *
     * @return string
     */
    public function getType();
}
