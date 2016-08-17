<?php  namespace Develpr\AlexaApp\Response; 

use Illuminate\Contracts\Support\Arrayable;

class Card implements Arrayable
{
    const DEFAULT_CARD_TYPE = "Simple";

    private $validCardTypes = ['Simple'];

    //The type of card
    //todo: as of now only Simple is valid but this is likely to change
    //@see https://developer.amazon.com/public/solutions/devices/echo/alexa-app-kit/docs/alexa-appkit-app-interface-reference
    private $type = "Simple";

    private $title = "";

    private $subtitle = "";

    private $content = "";

    function __construct($title = '', $subtitle = '', $content = '', $type = self::DEFAULT_CARD_TYPE)
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

    private function addAttributeToArray($attributeName, array & $outputArray )
    {
        if( ! is_string($this->$attributeName) || strlen($this->$attributeName) > 0 ){

            $outputArray[$attributeName] = $this->$attributeName;

        }
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @param string $subtitle
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        if( ! in_array($type, $this->validCardTypes) )
            throw new \Exception('Invalid Card type supplied');

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
