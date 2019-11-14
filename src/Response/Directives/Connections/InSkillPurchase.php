<?php

namespace Develpr\AlexaApp\Response\Directives\Connections;

class InSkillPurchase
{
    private $productId;
    private $token;
    private $type;

    public function __construct($productId, $token = '', $type = 'Buy')
    {
        $this->productId = $productId;
        $this->token = $token;
        $this->type = $type;
    }

    public function asDirective()
    {
        return new SendRequest($this->type, ['InSkillProduct' => ['productId' => $this->productId]], $this->token);
    }

    public function toArray()
    {
        return $this->asDirective()->toArray();
    }

}
