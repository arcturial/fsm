<?php
namespace League\StateMachine\Graph;

use League\StateMachine\StateMachine;

interface GraphInterface
{
    public function visualize(StateMachine $machine);
}