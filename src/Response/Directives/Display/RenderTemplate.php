<?php

namespace Develpr\AlexaApp\Response\Directives\Display;

use Develpr\AlexaApp\Response\Directives\Directive;
use Develpr\AlexaApp\Response\Directives\Display\Templates\Template;

class RenderTemplate extends Directive
{
    const TYPE = 'Display.RenderTemplate';

    protected $template;

    public function __construct(Template $template)
    {
        $this->template = $template;

    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'type' => self::TYPE,
            'template' => $this->template->toArray()
        ];
    }

}
