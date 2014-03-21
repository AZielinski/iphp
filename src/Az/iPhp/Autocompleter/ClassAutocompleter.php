<?php

namespace Az\iPhp\Autocompleter;

use Az\iPhp\Event\AutocompleteEvent;

class ClassAutocompleter
{

    public function onAutocomplete(AutocompleteEvent $event)
    {
        $lastToken = @end($event->getTokenizedInput()->getTokens());
        $event->addResults(['$pan', '$kracy']);
        $event->stopPropagation();
    }

}
