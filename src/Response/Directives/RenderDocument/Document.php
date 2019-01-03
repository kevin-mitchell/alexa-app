<?php

namespace Develpr\AlexaApp\Response\Directives\RenderDocument;


class Document
{
    const TYPE = 'APL';
    const VERSION = '1.0';

    public static function fromJson($json)
    {
        return json_decode($json, true);
    }

    public function toArray()
    {
        return [
            'type' => self::TYPE,
            'version' => self::VERSION,
            'mainTemplate' => [
                'items' => [
                    [
                        'type' => 'Container',
                        'height' => '100vh',
                        'items' => [
                            [
                                'type' => 'Text',
                                'text' => 'Example text'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
