<?php

namespace Develpr\AlexaApp\Response;

class AudioFile extends SSML
{
    const SIMPLE_SSML_AUDIO_TEMPLATE = '<audio src="{{SRC}}" />';

    private $audioURICollection = [];

    /**
     * @param string $fileURI
     */
    public function addAudioFile($fileURI)
    {
        $this->audioURICollection[] = $fileURI;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        $audioSSML = '';

        foreach ($this->audioURICollection as $position => $URI) {
            $audioSSML .= str_replace('{{SRC}}', $URI, self::SIMPLE_SSML_AUDIO_TEMPLATE);
        }

        $result = str_replace('{{CONTENT}}', $audioSSML, self::SIMPLE_SSML_TEMPLATE);

        return $result;
    }
}
