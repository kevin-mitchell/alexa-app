<?php

namespace Develpr\AlexaApp\Response\Directives\Display\Templates;


class TextContent extends Template
{
    protected $primaryText;
    protected $secondaryText;
    protected $tertiaryText;

    public function __construct(Text $primaryText, Text $secondaryText = null, Text $tertiaryText = null)
    {
        $this->primaryText = $primaryText;
        $this->secondaryText = $secondaryText;
        $this->tertiaryText = $tertiaryText;
    }

    public function toArray()
    {
        $return['primaryText'] = $this->primaryText->toArray();

        if($this->secondaryText) {
            $return['secondaryText'] = $this->secondaryText->toArray();
        }

        if($this->tertiaryText) {
            $return['tertiaryText'] = $this->tertiaryText->toArray();
        }

        return $return;
    }
}
