<?php namespace Develpr\AlexaApp\Response;

use Develpr\AlexaApp\Contracts\OutputSpeech;

/**
 * Used for directly creating SSML - more useful as SSML becomes standard in more ASK apps
 *
 * Class SSML
 * @package Develpr\AlexaApp\Response
 */
class SSML implements OutputSpeech
{
    const TYPE = 'SSML';
    const TYPE_KEY = 'ssml';

    const SIMPLE_SSML_TEMPLATE = '<speak>{{CONTENT}}</speak>';

    private $ssml = self::SIMPLE_SSML_TEMPLATE;

    public function toArray()
    {
        return [
            'type' => self::TYPE,
            'ssml' => $this->getValue()
        ];
    }

    public function setValue($value)
    {
        $this->ssml = $value;

        return $this;
    }

    public function setSSML($ssml)
    {
        return $this->setValue($ssml);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->ssml;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return self::TYPE;
    }

}