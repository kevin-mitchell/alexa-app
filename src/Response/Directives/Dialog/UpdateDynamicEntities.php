<?php

namespace Develpr\AlexaApp\Response\Directives\Dialog;

class UpdateDynamicEntities extends DialogDirective
{
    const TYPE = 'Dialog.UpdateDynamicEntities';
    const UPDATE_BEHAVIOR_CLEAR = 'CLEAR';
    const UPDATE_BEHAVIOR_REPLACE = 'REPLACE';

    private $updateBehavior;
    private $types;

    public function __construct($types = null)
    {
        $this->types = $types;
    }

    public function getType()
    {
        return $this::TYPE;
    }

    public function getTypes()
    {
        return $this->types;
    }

    public function setUpdateBehavior($updateBehavior)
    {
        $this->updateBehavior = $updateBehavior;
    }

    public function toArray()
    {
        $array = [
            'updateBehavior' => $this->updateBehavior ?: self::UPDATE_BEHAVIOR_REPLACE,
            'type' => $this->getType(),
            'types' => [],
        ];

        $this->types->each(function ($type) use (&$array) {
            $array['types'][] = $type->toArray();
        });

        return $array;
    }
}