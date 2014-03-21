<?php

namespace Az\iPhp\TaskDispatcher;

use Az\iPhp\Common\TokenizedInput;
use Az\iPhp\Event\AutocompleteEvent;
use Az\iPhp\Event\CommandEvent;
use Az\iPhp\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventTaskDispatcher implements TaskDispatcherInterface
{

    protected $dispatcher;
    protected $tokenizedInputLockedTo;
    protected $inputBuffer = '';

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function autocomplete($input)
    {
        $event = new AutocompleteEvent(new TokenizedInput($input));
        $this->dispatcher->dispatch(Events::AUTOCOMPLETE, $event);
        return $event->getResults() ?: [''];
    }

    public function process($input)
    {
        readline_add_history($input);

        if($this->inputBuffer)
        {
            $input = $this->inputBuffer."\n".$input;
        }

        $event = new CommandEvent(new TokenizedInput($input), $this->tokenizedInputLockedTo);
        if($this->tokenizedInputLockedTo)
        {
            $this->tokenizedInputLockedTo($event);
        }
        else
        {
            $this->dispatcher->dispatch(Events::INPUT_LINE, $event);
        }

        // Console output formatting
        if(($this->inputBuffer && !$event->shouldBuffer))
        {
            $event->output .= " ";
        }
        else if(strlen($event->output) && substr($event->output, -1, 1) != "\n")
        {
            $event->output .= "\n";
        }

        if($event->shouldBuffer)
        {
            $this->inputBuffer = $input;
        }
        else
        {
            $this->inputBuffer = '';
        }
        $this->tokenizedInputLockedTo = $event->lockedTo;

        return $event->output;
    }

    protected $no = 1;

    public function getPrompt()
    {
        return $this->inputBuffer ? '   ...: ' : sprintf('In [%d]: ', $this->no);
    }

}

