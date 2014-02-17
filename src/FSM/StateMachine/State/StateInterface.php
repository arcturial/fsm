<?php
/*
 * This file is part of the StateMachine library. A simple
 * finite state machine implementation for PHP.
 */

namespace FSM\StateMachine\State;

/**
 * States are nodes in a state graph. They define certain 'states'
 * within a process.
 *
 * @category FSM
 * @package  FSM\StateMachine\State
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
     * Process the actions of the state.
     *
     * @return boolean
     */
    public function process();
}
