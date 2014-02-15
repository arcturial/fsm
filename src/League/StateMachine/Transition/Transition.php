<?php
namespace League\StateMachine\Transition;

use League\StateMachine\State\StateInterface;
use League\StateMachine\Transition\TransitionInterface;
use League\StateMachine\Transition\StateTransition;
use \SplObjectStorage;

class Transition implements TransitionInterface
{
    private $initial;
    private $transition;
    private $conditions;

    public function __construct(array $initial, $transition)
    {
        $this->initial = $initial;
        $this->transition = $transition;
        $this->conditions = new SplObjectStorage();
    }

    public function isInitialState($initial)
    {
        return in_array($initial, $this->initial);
    }

    public function getTransitionTo()
    {
        return $this->transition;
    }

    public function addCondition(ConditionInterface $condition)
    {
        $this->conditions->attach($condition);
    }

    public function getConditions()
    {
        return $this->conditions;
    }

    public function process()
    {
        $state = true;

        foreach ($this->conditions as $condition)
        {
            $state &= $condition->check();
        }

        return $state;
    }
}