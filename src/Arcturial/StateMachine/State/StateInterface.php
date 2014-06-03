<?php
/*
 * This file is part of the StateMachine library. A simple
 * finite state machine implementation for PHP.
 */

namespace Arcturial\StateMachine\State;

/**
 * States are nodes in a state graph. They define certain 'states'
 * within a process.
 *
 * @category Arcturial
 * @package  Arcturial\StateMachine\State
 * @author   Chris Brand <webmaster@cainsvault.com>
 */
interface StateInterface
{
    /**
     * @var string
     */
    const TYPE_INITIAL = 'initial';

    /**
     * @var string
     */
    const TYPE_INTERMEDIATE = 'intermediate';

    /**
     * @var string
     */
    const TYPE_FINAL = 'final';

    /**
     * Add a new state action.
     *
     * @param ActionInterface $action The action to perform
     *
     * @return boolean
     */
    public function addAction(ActionInterface $action);

    /**
     * Return the state name
     *
     * @return string
     */
    public function getName();

    /**
     * Return the state type.
     *
     * @return string
     */
    public function getType();

    /**
     * Get transition, executed on given state
     *
     * @return \Arcturial\StateMachine\Transition\TransitionInterface
     */
    public function getTransition();

    /**
     * Process the actions of the state.
     *
     * @return boolean
     */
    public function process();
}
