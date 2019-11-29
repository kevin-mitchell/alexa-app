<?php

namespace Develpr\AlexaApp\Response\Directives\Display\Templates;


class Text
{
    protected $text;
    protected $type;

    public function __construct($text, $type = 'PlainText')
    {
        $this->text = $text;
        $this->type = $type;
    }

    public function toArray()
    {
        return [
            'text' => $this->text,
            'type' => $this->type
        ];
    }
}
