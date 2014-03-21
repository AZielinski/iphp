<?php

namespace Az\iPhp\Runner;

use Az\iPhp\Cli\CliInterface;
use Az\iPhp\TaskDispatcher\TaskDispatcherInterface;

class BasicRunner implements RunnerInterface
{

    /** @var InputHandlerInterface */
    protected $cli;

    /** @var TaskDispatcherInterface */
    protected $taskDispatcher;

    public function __construct(CliInterface $cli, TaskDispatcherInterface $taskDispatcher)
    {
        $this->cli = $cli;
        $this->taskDispatcher = $taskDispatcher;
    }

    public function run()
    {
        $this->cli->startInputLoop($this->taskDispatcher);
    }

}