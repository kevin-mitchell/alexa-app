<?php

namespace Develpr\AlexaApp\Response\Directives\Display\Templates;


class BodyTemplate6 extends Template
{
    protected $type = 'BodyTemplate6';

    public function __construct($title, TextContent $textContent, Image $image, Image $backgroundImage, $backButton = true, $token ='')
    {
        $this->title = $title;
        $this->textContent = $textContent;
        $this->image = $image;
        $this->backgroundImage = $backgroundImage;
        $this->backButton = $backButton;
        $this->token = $token;
    }

    public function toArray()
    {
        return [
            'type' => $this->type,
            'token' => $this->token,
            'backButton' => $this->backButton ? 'VISIBLE' : 'HIDDEN',
            'image' => $this->image->toArray(),
            'backgroundImage' => $this->backgroundImage->toArray(),
            'title' => $this->title,
            'textContent' => $this->textContent->toArray(),
        ];
    }
}
