<?php

namespace Develpr\AlexaApp\Response\Directives\AudioPlayer;

use Develpr\AlexaApp\Response\Directives\Directive;

class Play extends Directive
{
    const DEFAULT_PLAY_BEHAVIOR = 'REPLACE_ALL';

    const TYPE = 'AudioPlayer.Play';

    private $validPlayBehaviors = ['REPLACE_ALL', 'ENQUEUE', 'REPLACE_ENQUEUED'];

    protected $url = '';

    protected $token = '';

    protected $playBehavior = '';

    protected $offsetInMilliseconds = '';

    protected $expectedPreviousToken = '';


    /**
     * Play constructor.
     *
     * @param string $url
     * @param string $token
     * @param string $playBehavior
     * @param int $offset
     * @param string $expectedPreviousToken
     */
    public function __construct($url, $token = '', $offsetInMilliseconds = 0, $playBehavior = null, $expectedPreviousToken = '')
    {
        $this->url = $url;
        $this->token = $token ?: str_random(64);
        $this->playBehavior = $playBehavior ?: self::DEFAULT_PLAY_BEHAVIOR;
        $this->offsetInMilliseconds = $offsetInMilliseconds;
        $this->expectedPreviousToken = $expectedPreviousToken;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $playAsArray['type'] = self::TYPE;
        $playAsArray['audioItem']['stream'] = [];

        $this->addAttributeToArray('playBehavior', $playAsArray);
        $this->addAttributeToArray('url', $playAsArray['audioItem']['stream']);
        $this->addAttributeToArray('token', $playAsArray['audioItem']['stream']);
        $this->addAttributeToArray('offsetInMilliseconds', $playAsArray['audioItem']['stream']);

        if($this->playBehavior == 'ENQUEUE')
        {
            $this->addAttributeToArray('expectedPreviousToken', $playAsArray['audioItem']['stream']);
        }

        return $playAsArray;
    }



    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @param string $token
     *
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @param string $offsetInMilliseconds
     *
     * @return $this
     */
    public function setOffsetInMilliseconds($offsetInMilliseconds)
    {
        $this->offsetInMilliseconds = $offsetInMilliseconds;

        return $this;
    }

    /**
     * @param string $type
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function setPlayBehavior($playBehavior)
    {
        if (!in_array($playBehavior, $this->validPlayBehaviors)) {
            throw new \Exception('Invalid play behavior supplied');
        }

        $this->playBehavior = $playBehavior;

        return $this;
    }

    /**
     * @param string $token
     *
     * @return $this
     */
    public function setExpectedPreviousToken($expectedPreviousToken)
    {
        if ($this->playBehavior != 'ENQUEUE') {
            throw new \Exception('ExpectedPreviousToken is only allowed for play behavior ENQUEUE');
        }

        $this->expectedPreviousToken = $expectedPreviousToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getPlayBehavior()
    {
        return $this->playBehavior;
    }

    /**
     * @return integer
     */
    public function getOffsetInMilliseconds()
    {
        return $this->offsetInMilliseconds;
    }

    /**
     * @return integer
     */
    public function getExpectedPreviousToken()
    {
        return $this->expectedPreviousToken;
    }
}
