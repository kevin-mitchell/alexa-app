<?php

namespace Develpr\AlexaApp\Response\Directives\Connections;


class InSkillPurchase
{

    private $productId;
    private $token;

    public function __construct($productId, $token = '')
    {
        $this->productId = $productId;
        $this->token = $token;
    }

    public function asDirective()
    {
        return new SendRequest('Buy', ['InSkillProduct' => ['productId' => $this->productId]], $this->token);
    }

    public function toArray()
    {
        return $this->asDirective()->toArray();
    }

}