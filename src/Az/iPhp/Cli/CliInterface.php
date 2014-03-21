<?php

namespace Az\iPhp\Cli;

use Az\iPhp\TaskDispatcher\TaskDispatcherInterface;

interface CliInterface
{

    public function startInputLoop(TaskDispatcherInterface $d);

}

