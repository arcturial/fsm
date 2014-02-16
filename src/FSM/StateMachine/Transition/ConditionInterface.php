<?php
namespace FSM\StateMachine\Transition;

interface ConditionInterface
{
    public function getName();

    /**
     * Return state
     */
    public function check();
}