<?php

namespace Az\iPhp\Event;

use Az\iPhp\Common\TokenizedInput;
use Symfony\Component\EventDispatcher\Event;

class CommandEvent extends Event
{

    public $output;
    public $lockedTo;
    public $shouldBuffer = false;

    protected $tokenizedInput;

    function __construct(TokenizedInput $tokenizedInput, $lockedTo)
    {
        $this->tokenizedInput  = $tokenizedInput;
        $this->lockedTo = $lockedTo;
    }

    /**
     * @return TokenizedInput
     */
    public function getTokenizedInput()
    {
        return $this->tokenizedInput;
    }

}
