<?php

namespace Develpr\AlexaApp\Response\Directives\Display\Templates;

abstract class Template
{
    protected $type;
    protected $token;
    protected $backButton = true;
    protected $title;
    protected $textContent;
    protected $backgroundImage;
    protected $image;

    public function setBackButton($backButton)
    {
        $this->backButton = $backButton;
        return $this;
    }

    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function setBackgroundImage($backgroundImage)
    {
        $this->backgroundImage = $backgroundImage;
        return $this;
    }

    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    public function setTextContent($textContent)
    {
        $this->textContent = $textContent;
        return $this;
    }


}
