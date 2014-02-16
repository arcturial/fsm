<?php
namespace League\StateMachine\Transition;

use League\StateMachine\State\StateInterface;
use League\StateMachine\Transition\TransitionInterface;
use League\StateMachine\Transition\StateTransition;
use League\StateMachine\Condition\ConditionedState;
use League\StateMachine\Condition\ConditionInterface;
use \SplObjectStorage;

class Transition implements TransitionInterface
{
    private $initial;
    private $transition;
    private $condition;

    public function __construct($initial, $transition)
    {
        $this->initial = $initial;
        $this->transition = $transition;
        $this->conditions = new SplObjectStorage();
    }

    public function getHash()
    {
        // bad temp hash
        $unique = spl_object_hash($this->initial) . spl_object_hash($this->transition) . spl_object_hash($this->conditions);
        return md5($unique);
    }

    public function isInitialState($initial)
    {
        return $this->initial == $initial;
    }

    public function getInitialState()
    {
        return $this->initial;
    }

    public function getTransitionTo()
    {
        return $this->transition;
    }

    public function addCondition(ConditionInterface $condition)
    {
        $this->condition = $condition;
    }

    public function getCondition()
    {
        return $this->condition;
    }

    public function process()
    {
        if ($this->condition) {
            return $this->condition->check();
        }

        return true;
    }
}