<?php

namespace Develpr\AlexaApp\Response\Directives\Dialog;

/**
 * Class ConfirmSlot sends Alexa a command to confirm the value of a specific slot
 * before continuing with the dialog.
 *
 * NOTE: You should Provide a prompt to ask the user for confirmation. Be sure
 * to repeat back the value to confirm in the prompt.
 *
 * @see https://developer.amazon.com/public/solutions/alexa/alexa-skills-kit/docs/dialog-interface-reference#confirmslot
 *
 * @package Develpr\AlexaApp\Response\Directives\Dialog
 */
class ConfirmSlot extends DialogDirective
{
    const TYPE = 'Dialog.ConfirmSlot';

    /** @var string $slotToElicit */
    private $slotToConfirm;

    /**
     * ElicitSlot constructor.
     *
     * @param $slotToElicit
     */
    public function __construct($slotToConfirm)
    {
        $this->slotToConfirm = $slotToConfirm;
    }

    public function getType()
    {
      return $this::TYPE;
    }

    public function toArray()
    {
        return array_merge([
            'slotToConfirm' => $this->slotToConfirm,
        ], parent::toArray());
    }

}