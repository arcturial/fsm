<?php
namespace League\StateMachine\Transition;

class StateTransition
{
    private $state;

    public function __construct($state)
    {
        $this->state = $state;
    }

    public function setState($state)
    {
        $this->state = $state;
    }

    public function getState()
    {
        return $this->state;
    }
}