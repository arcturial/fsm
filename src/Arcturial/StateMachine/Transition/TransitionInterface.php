<?php
/*
 * This file is part of the StateMachine library. A simple
 * finite state machine implementation for PHP.
 */

namespace Arcturial\StateMachine\Transition;

use Arcturial\StateMachine\State\StateInterface;
use Arcturial\StateMachine\Condition\ConditionInterface;

/**
 * Transitions are defined paths and conditions that need
 * to be followed from one state to another.
 *
 * @category Arcturial
 * @package  Arcturial\StateMachine\Transition
 * @author   Chris Brand <webmaster@cainsvault.com>
 */
interface TransitionInterface
{
    /**
     * Test if the state is the initial one in this
     * transition.
     *
     * @param StateInterface $initial The state to test
     *
     * @return boolean
     */
    public function isInitialState(StateInterface $initial);

    /**
     * Return the initial state of the transition.
     *
     * @return StateInterface
     */
    public function getInitialState();

    /**
     * Return the 'transfer to' state of this transition.
     *
     * @return StateInterface
     */
    public function getTransitionTo();

    /**
     * Add a condition to this transition.
     *
     * @param ConditionInterface $condition The condition to test against
     *
     * @return boolean
     */
    public function addCondition(ConditionInterface $condition);

    /**
     * Return the condition associated with this transition.
     *
     * @return ConditionInterface
     */
    public function getCondition();

    /**
     * Process a transition and checks the condition.
     *
     * @return boolean
     */
    public function process();

    /**
     * Get parent state machine
     * 
     * @return object
     */
    public function getMachine();
    
    /**
     * Set parent state machine
     */
    public function setMachine($machine);
}
