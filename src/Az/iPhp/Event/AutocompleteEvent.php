<?php

namespace Az\iPhp\Event;

use Az\iPhp\Common\TokenizedInput;
use Symfony\Component\EventDispatcher\Event;

class AutocompleteEvent extends Event
{

    /** @var TokenizedInput */
    protected $tokenizedInput;
    protected $results = [];

    function __construct(TokenizedInput $tokenizedInput)
    {
        $this->tokenizedInput = $tokenizedInput;
    }

    /**
     * @param array $results
     */
    public function setResults(array $results)
    {
        $this->results = $results;
    }

    public function addResults(array $results)
    {
        $this->results = array_merge($this->results, $results);
    }

    /**
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @return TokenizedInput
     */
    public function getTokenizedInput()
    {
        return $this->tokenizedInput;
    }

}
