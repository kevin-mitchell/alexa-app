<?php

namespace Develpr\AlexaApp\Response\Directives\Display\Templates;


class Image
{
    protected $url;
    protected $contentDescription;

    public function __construct($url, $contentDescription = '')
    {
        $this->url = $url;
        $this->contentDescription = $contentDescription;
    }

    public function toArray()
    {
        return [
            'contentDescription' => $this->contentDescription,
            'sources' => [
                [
                    'url' => $this->url
                ]
            ]
        ];
    }
}
