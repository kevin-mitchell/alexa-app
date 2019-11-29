<?php

namespace Develpr\AlexaApp\Response\Directives\RenderDocument;

use Develpr\AlexaApp\Response\Directives\Directive;
use Illuminate\Support\Str;

class RenderDocument extends Directive
{
    const TYPE = 'Alexa.Presentation.APL.RenderDocument';

    protected $document;
    protected $datasources;

    public function __construct($document = null, $datasources = null)
    {
        $this->document = $document;
        $this->datasources = $datasources;
    }

    public function setDocument($document)
    {
        $this->document = $document;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'type' => self::TYPE,
            'token' => Str::random(),
            'document' => $this->document instanceof Document ? $this->document->toArray() : Document::fromJson($this->document),
            'datasources' => $this->datasources
        ];
    }

}
