<?php

namespace Az\iPhp\Autocompleter;

use Az\iPhp\Event\AutocompleteEvent;

abstract class AbstractAutocompleter
{

    public function onAutocomplete(AutocompleteEvent $event)
    {
        if(!$this->isApplicable($event))
        {
            return;
        }

        $this->doAutocomplete($event);
    }

    abstract protected function isApplicable(AutocompleteEvent $event);
    abstract protected function doAutocomplete(AutocompleteEvent $event);

}
