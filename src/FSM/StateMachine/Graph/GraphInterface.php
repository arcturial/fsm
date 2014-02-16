<?php
namespace FSM\StateMachine\Graph;

use FSM\StateMachine\StateMachine;

interface GraphInterface
{
    public function visualize(StateMachine $machine);
}