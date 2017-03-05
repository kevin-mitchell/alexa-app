<?php

namespace Develpr\AlexaApp\Response\Directives\AudioPlayer;

use Develpr\AlexaApp\Response\Directives\Directive;

class ClearQueue extends Directive
{
    const TYPE = 'AudioPlayer.ClearQueue';

    const DEFAULT_CLEAR_BEHAVIOR = 'CLEAR_ALL';

    private $validClearBehaviors = ['CLEAR_ENQUEUED', 'CLEAR_ALL'];

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $clearBehaviorAsArray['type'] = self::TYPE;
        $this->addAttributeToArray('clearBehavior', $clearBehaviorAsArray);

        return $clearBehaviorAsArray;
    }


    /**
     * @param string $type
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function setClearBehavior($clearBehavior)
    {
        if (!in_array($clearBehavior, $this->validClearBehaviors)) {
            throw new \Exception('Invalid clear behavior supplied');
        }

        $this->clearBehavior = $clearBehavior;

        return $this;
    }

    /**
     * @return string
     */
    public function getClearBehavior()
    {
        return $this->clearBehavior;
    }
}
