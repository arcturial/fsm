<?php
/*
 * This file is part of the StateMachine library. A simple
 * finite state machine implementation for PHP.
 */

namespace FSM\StateMachine\Transition;

use FSM\StateMachine\State\StateInterface;
use FSM\StateMachine\Transition\TransitionInterface;
use FSM\StateMachine\Condition\ConditionInterface;
use \SplObjectStorage;

/**
 * Transitions are defined paths and conditions that need
 * to be followed from one state to another. This is the default
 * transition object.
 *
 * @category FSM
 * @package  FSM\StateMachine\Transition
 * @author   Chris Brand <webmaster@cainsvault.com>
 */
class Transition implements TransitionInterface
{
    /**
     * @var StateInterface
     */
    private $initial;

    /**
     * @var StateInterface
     */
    private $transition;

    /**
     * @var ConditionInterface
     */
    private $condition;

    /**
     * Construct a new transition.
     *
     * @param StateInterface $initial    The initial state
     * @param StateInterface $transition The state to transfer to
     */
    public function __construct(StateInterface $initial, StateInterface $transition)
    {
        $this->initial = $initial;
        $this->transition = $transition;
        $this->conditions = new SplObjectStorage();
    }

    /**
     * {@inheritdoc}
     */
    public function isInitialState(StateInterface $initial)
    {
        return $this->initial == $initial;
    }

    /**
     * {@inheritdoc}
     */
    public function getInitialState()
    {
        return $this->initial;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransitionTo()
    {
        return $this->transition;
    }

    /**
     * {@inheritdoc}
     */
    public function addCondition(ConditionInterface $condition)
    {
        $this->condition = $condition;
    }

    /**
     * {@inheritdoc}
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * {@inheritdoc}
     */
    public function process()
    {
        if ($this->condition) {
            return $this->condition->check();
        }

        return true;
    }
}