<?php

namespace Develpr\AlexaApp\Response;

use Illuminate\Contracts\Support\Arrayable;

class Card implements Arrayable
{
    const DEFAULT_CARD_TYPE = 'Simple';
    const LINK_ACCOUNT_CARD_TYPE = 'LinkAccount';

    private $validCardTypes = ['Simple','LinkAccount'];

    //The type of card
    //@see https://developer.amazon.com/public/solutions/devices/echo/alexa-app-kit/docs/alexa-appkit-app-interface-reference
    private $type = 'Simple';

    private $title = '';

    private $subtitle = '';

    private $content = '';

    /**
     * Card constructor.
     *
     * @param string $title
     * @param string $subtitle
     * @param string $content
     * @param string $type
     */
    public function __construct($title = '', $subtitle = '', $content = '', $type = self::DEFAULT_CARD_TYPE)
    {
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->content = $content;
        $this->type = $type;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $cardAsArray = [];

        //todo: as of now, only *type* is required but this seems likely to change
        $cardAsArray['type'] = $this->type;

        $this->addAttributeToArray('title', $cardAsArray);
        $this->addAttributeToArray('subtitle', $cardAsArray);
        $this->addAttributeToArray('content', $cardAsArray);

        return $cardAsArray;
    }

    /**
     * @param string $attributeName
     * @param array  $outputArray
     */
    private function addAttributeToArray($attributeName, array &$outputArray)
    {
        if (!is_string($this->$attributeName) || strlen($this->$attributeName) > 0) {
            $outputArray[$attributeName] = $this->$attributeName;
        }
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @param string $subtitle
     *
     * @return $this
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param string $type
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function setType($type)
    {
        if (!in_array($type, $this->validCardTypes)) {
            throw new \Exception('Invalid Card type supplied');
        }

        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
