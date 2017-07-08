<?php

namespace Develpr\AlexaApp\Response\Directives\Dialog;

use Develpr\AlexaApp\Response\Directives\Directive;
use Develpr\AlexaApp\Request\AlexaRequest;

abstract class DialogDirective extends Directive
{
    /** @var AlexaRequest */
    private $alexaRequest;

    /**
     * Get the dialog directive type
     *
     * @return mixed
     */
    abstract public function getType();

    /**
     * Get the alexa request
     *
     * @return AlexaRequest
     */
    public function request()
    {
        if(!isset($this->alexaRequest)) {
            $this->alexaRequest = app(AlexaRequest::class);
        }

        return $this->alexaRequest;
    }

    public function toArray()
    {
        return [
            'type' => $this->getType(),
            'updatedIntent' => [
                'name' => $this->request()->getIntent(),
                'confirmationStatus' => $this->request()->getConfirmationStatus(),
                'slots' => $this->request()->slots(),
            ],
        ];
    }
}