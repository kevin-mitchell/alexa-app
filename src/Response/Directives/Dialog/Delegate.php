<?php

namespace Develpr\AlexaApp\Response\Directives\Dialog;

/**
 * Class Delegate sends Alexa a command to handle the next turn in the dialog with the user.
 *
 * You can use this directive if the skill has a dialog model and the current status of the
 * dialog (dialogState) is either STARTED or IN_PROGRESS. You cannot return this directive
 * if the dialogState is COMPLETED.
 *
 * @see https://developer.amazon.com/public/solutions/alexa/alexa-skills-kit/docs/dialog-interface-reference#delegate
 *
 * @package Develpr\AlexaApp\Response\Directives\Dialog
 */
class Delegate extends DialogDirective
{
    const TYPE = 'Dialog.Delegate';

    /**
     * @return string
     */
    public function getType()
    {
        return $this::TYPE;
    }
}
