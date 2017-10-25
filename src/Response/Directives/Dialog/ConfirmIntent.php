<?php

namespace Develpr\AlexaApp\Response\Directives\Dialog;

/**
 * Class ConfirmIntent sends Alexa a command to confirm the all the information the user has
 * provided for the intent before the skill takes action.
 *
 * NOTE: You should provide a prompt to ask the user for confirmation.
 * Be sure to repeat back all the values the user needs to confirm in the prompt
 *
 * @see https://developer.amazon.com/public/solutions/alexa/alexa-skills-kit/docs/dialog-interface-reference#confirmintent
 * @package Develpr\AlexaApp\Response\Directives\Dialog
 */
class ConfirmIntent extends DialogDirective
{
    const TYPE = 'Dialog.ConfirmIntent';

    public function getType()
    {
        return $this::TYPE;
    }
}