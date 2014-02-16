<?php
namespace FSM\StateMachine\Transition;

interface TransitionInterface
{
    public function process();
}