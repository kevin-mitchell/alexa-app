<?php

namespace Develpr\AlexaApp\Response;

use Illuminate\Contracts\Support\Arrayable;

class Card implements Arrayable
{
    const DEFAULT_CARD_TYPE = 'Simple';
    const LINK_ACCOUNT_CARD_TYPE = 'LinkAccount';
    const STANDARD_CARD_TYPE = 'Standard';
    const SIMPLE_CARD_TYPE = 'Simple';

    private $validCardTypes = ['Simple', 'LinkAccount', 'Standard'];

    //The type of card
    //@see https://developer.amazon.com/public/solutions/devices/echo/alexa-app-kit/docs/alexa-appkit-app-interface-reference
    private $type = 'Simple';

    private $title = '';

    private $subtitle = '';

    /** @var string $content Only applicable for simple card types */
    private $content = '';

    /** @var string $text Only applicable for standard card types */
    private $text = '';

    /** @var array | string $img Only applicable for standard card types */
    private $image = [];

    /**
     * Card constructor
     *
     * @param string $title
     * @param string $subtitle
     * @param string $content
     * @param string $type
     * @param string $text
     * @param array|string $image url for the image to display on the card
     */
    public function __construct(
        $title = '',
        $subtitle = '',
        $content = '',
        $type = self::DEFAULT_CARD_TYPE,
        $text = '',
        $image = []
    ) {
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->content = $content;
        $this->type = $type;
        $this->text = $text;
        $this->setImg($image);
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

        if ($this->isSimpleCard()) {
            $this->addAttributeToArray('content', $cardAsArray);
        }

        if ($this->isStandardCard()) {
            $this->addAttributeToArray('text', $cardAsArray);
            $this->addAttributeToArray('image', $cardAsArray);
        }

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
     * Set content of the simple card
     *
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
     * Set text of the standard card
     *
     * @param string $text
     *
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
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

    /**
     * @param string | array[string] $image the image url
     *
     * @return $this
     */
    public function setImg($image)
    {
        $requiredKeys = ['smallImageUrl', 'largeImageUrl'];

        if (is_array($image) && array_has($image, $requiredKeys)) {
            $this->image  = array_only($image, $requiredKeys);
        } elseif (is_string($image)) {
            $this->image = [
                'smallImageUrl' => $image,
                'largeImageUrl' =>   $image
            ];
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getImg()
    {
        return $this->$image;
    }

    /**
     * @return bool
     */
    public function isStandardCard()
    {
        return $this->type === Card::STANDARD_CARD_TYPE;
    }

    /**
     * @return bool
     */
    public function isSimpleCard()
    {
        return $this->type === Card::SIMPLE_CARD_TYPE;
    }
}
