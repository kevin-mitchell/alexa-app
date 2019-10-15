<?php

namespace Develpr\AlexaApp\Response\Directives\Dialog;

use Develpr\AlexaApp\Response\Directives\Directive;
use Develpr\AlexaApp\Request\AlexaRequest;
use Illuminate\Support\Arr;

abstract class DialogDirective extends Directive
{
    /** @var AlexaRequest */
    private $alexaRequest;
    protected $updatedIntent;

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
        if (!isset($this->alexaRequest)) {
            $this->alexaRequest = function_exists('app') ?
                app(AlexaRequest::class) : new AlexaRequest();
        }

        return $this->alexaRequest;
    }

    public function setUpdatedIntent($updatedIntent)
    {
        $this->updatedIntent = $updatedIntent;
    }

    public function toArray()
    {
        return [
            'type' => $this->getType(),
            'updatedIntent' => $this->getUpdatedIntent()
        ];
    }

    protected function getUpdatedIntent()
    {
        return [
            'name' => Arr::get($this->updatedIntent, 'name', $this->request()->getIntent()),
            'confirmationStatus' => Arr::get($this->updatedIntent, 'confirmationStatus', $this->request()->getConfirmationStatus()),
            'slots' => Arr::get($this->updatedIntent, 'slots', $this->request()->slots()),
        ];
    }
}