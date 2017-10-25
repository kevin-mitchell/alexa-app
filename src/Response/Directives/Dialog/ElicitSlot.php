<?php

namespace Develpr\AlexaApp\Response\Directives\Dialog;

/**
 * Class ElicitSlot sends Alexa a command to ask the user for the value of a specific slot.
 *
 * You should provide a prompt to ask the user for the slot value.
 *
 * @see https://developer.amazon.com/public/solutions/alexa/alexa-skills-kit/docs/dialog-interface-reference#elicitslot
 *
 * @package Develpr\AlexaApp\Response\Directives\Dialog
 */
class ElicitSlot extends DialogDirective
{
    const TYPE = 'Dialog.ElicitSlot';

    /** @var string $slotToElicit */
    private $slotToElicit;

    /**
     * ElicitSlot constructor.
     *
     * @param $slotToElicit
     */
    public function __construct($slotToElicit)
    {
        $this->slotToElicit = $slotToElicit;
    }

    public function getType()
    {
        return $this::TYPE;
    }

    public function toArray()
    {
        return array_merge([
            'slotToElicit' => $this->slotToElicit,
        ], parent::toArray());
    }
}