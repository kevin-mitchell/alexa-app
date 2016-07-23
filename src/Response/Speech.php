<?php  namespace Develpr\AlexaApp\Response;

use Develpr\AlexaApp\Contracts\OutputSpeech;

class Speech implements OutputSpeech
{
    const DEFAULT_TYPE = 'PlainText';
    const SPEECH_TYPE_PLAINTEXT = 'PlainText';
    const SPEECH_TYPE_SSML = 'SSML';

    private $validTypes = ['PlainText', 'SSML'];
    private $value;
    private $type;

    function __construct($value = '', $type = self::DEFAULT_TYPE)
    {
        $this->value = $value;
        $this->type = $type;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $textKey = ($this->getType() == 'SSML') ? 'ssml' : 'text';
        return [
            'type'  => $this->getType(),
            $textKey  => $this->getValue()
        ];
    }


    /**
     * @param string $type
     */
    public function setType($type)
    {
        if( ! in_array($type, $this->validTypes) )
            throw new \Exception('Invalid speech type'); //todo: should be specific exception ?  is this helpful?

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
     * @param string
     */
    public function setValue($value){
        $this->value = $value;
    }

    /**
     * This is here to implement the Speech contract and allow for backwards compatability
     * @return string
     */
    public function getValue(){
        return $this->value;
    }

    /**
     * @deprecated since v 0.3.0
     * @param string $text
     */
    public function setText($text)
    {
        return $this->setValue($text);
    }

    /**
     * @deprecated since v 0.3.0
     * @return string
     */
    public function getText()
    {
        return $this->getValue();
    }


}
