<?php
/*
 * This file is part of the StateMachine library. A simple
 * finite state machine implementation for PHP.
 */

namespace FSM\StateMachine\State;

/**
 * Actions are called once a transition has been completed
 * and the state needs to be updated. If any action fails
 * it is the responsibility of the state to rollback changes.
 *
 * @category FSM
 * @package  FSM\StateMachine\State
 * @author   Chris Brand <webmaster@cainsvault.com>
 */
interface ActionInterface
{
    /**
     * Run the action.
     *
     * @return boolean
     */
    public function run();
}
