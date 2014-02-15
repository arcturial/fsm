<?php
namespace League\StateMachine\Transition;

interface TransitionInterface
{
    public function process();
}