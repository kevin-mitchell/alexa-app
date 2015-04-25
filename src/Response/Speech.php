<?php  namespace Develpr\AlexaApp\Response; 

use Illuminate\Contracts\Support\Arrayable;

class Speech implements Arrayable
{
    const DEFAULT_TYPE = 'PlainText';

    private $validTypes = ['PlainText'];

    private $text = '';
    private $type = 'PlainText';

    function __construct($text = '', $type = self::DEFAULT_TYPE)
    {
        $this->text = $text;
        $this->type = $type;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'type'  => $this->type,
            'text'  => $this->text
        ];
    }


    /**
     * @param string $type
     */
    public function setType($type)
    {
        if( ! in_array($type, $this->validTypes) )
            throw new \Exception('Invalid speech type');

        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $text
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
        return $this->$text;
    }




} 
