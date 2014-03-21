<?php

namespace Az\iPhp\Cli;

use Az\iPhp\TaskDispatcher\TaskDispatcherInterface;

class ReadlineCliHandler implements CliInterface
{

    /** @var TaskDispatcherInterface */
    protected $taskDispatcher;

    public function startInputLoop(TaskDispatcherInterface $d)
    {
        $this->taskDispatcher = $d;

        $this->installCallbackHandler($this->taskDispatcher);
        readline_completion_function(function() {
                $info = readline_info();
                $buffer = substr($info['line_buffer'], 0, $info['point']);
                return $this->taskDispatcher->autocomplete($buffer);
        });

        while (true)
        {
            $w = NULL;
            $e = NULL;
            $n = stream_select($r = array(STDIN), $w, $e, null);
            if ($n && in_array(STDIN, $r))
            {
                readline_callback_read_char();
            }
        }
    }

    protected function installCallbackHandler(TaskDispatcherInterface $d)
    {
        readline_callback_handler_install($d->getPrompt(), [$this, 'rlCallback']);
    }

    protected function rlCallback($ret)
    {
        $output = $this->taskDispatcher->process($ret);
        if($output)
        {
            echo $output."\n";
        }
        $this->installCallbackHandler($this->taskDispatcher);
    }

}

