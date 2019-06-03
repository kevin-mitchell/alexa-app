<?php

namespace Develpr\AlexaApp\Response\Directives\Connections;


use Develpr\AlexaApp\Response\Directives\Directive;

class SendRequest extends Directive
{
    const TYPE = 'Connections.SendRequest';

    private $name;
    private $payload;
    private $token;

    public function __construct($name, $payload, $token = '')
    {
        $this->name = $name;
        $this->payload = $payload;
        $this->token = $token;
    }

    public function getType()
    {
        return $this::TYPE;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPayload()
    {
        return $this->payload;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function toArray()
    {
        return [
            'type' => $this->getType(),
            'name' => $this->getName(),
            'payload' => $this->getPayload(),
            'token' => $this->getToken(),
        ];
    }

}