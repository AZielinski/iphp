<?php

namespace Az\iPhp\Autocompleter;

use Az\iPhp\Event\AutocompleteEvent;

class ArrayKeyAutocompleter extends AbstractAutocompleter
{

    protected function doAutocomplete(AutocompleteEvent $event)
    {
        $event->addResults(['index1', 'index2']);
    }

    protected function isApplicable(AutocompleteEvent $event)
    {
        return $event->getTokenizedInput()->endsWithTokens([
            T_VARIABLE, T_NUM_STRING,
            T_ENCAPSED_AND_WHITESPACE, T_WHITESPACE
        ], ['['], true);
    }

}