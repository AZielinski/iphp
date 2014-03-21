<?php

namespace Az\iPhp\TaskDispatcher;

interface TaskDispatcherInterface
{

    /**
     * Returns list of matches
     *
     * @param $tokenizedInput
     * @return array
     */
    public function autocomplete($tokenizedInput);

    /**
     * Returns tokenizedInput output
     *
     * @param $tokenizedInput
     * @return string
     */
    public function process($tokenizedInput);

    public function getPrompt();

}

