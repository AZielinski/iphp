<?php

namespace Az\iPhp\Autocompleter;

use Az\iPhp\Event\AutocompleteEvent;

class VariableAutocompleter extends AbstractAutocompleter
{

    protected function doAutocomplete(AutocompleteEvent $event)
    {
        $event->setResults(['pan', 'kracy']);
        $event->stopPropagation();
    }

    protected function isApplicable(AutocompleteEvent $event)
    {
        return $event->getTokenizedInput()->endsWithTokens([
            T_VARIABLE, T_NUM_STRING,
            T_WHITESPACE,
            T_IS_EQUAL, T_IS_NOT_EQUAL, T_IS_GREATER_OR_EQUAL, T_IS_IDENTICAL, T_IS_NOT_IDENTICAL, T_IS_SMALLER_OR_EQUAL,
            T_CLONE, T_COMMENT,
            T_DOLLAR_OPEN_CURLY_BRACES, T_CURLY_OPEN,
            '[', ']', '<<', '>>',
            '+', '-', '/', '*', '%',
            '^', '|', '&',
            '?', ':', '=',
        ], [';', '(', ')', ',']);
    }

}
