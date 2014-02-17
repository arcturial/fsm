<?php
/*
 * This file is part of the StateMachine library. A simple
 * finite state machine implementation for PHP.
 */

namespace FSM\StateMachine\Condition;

/**
 * Conditions are blocks of logic that evaluates to
 * a true/false definition.
 *
 * @category FSM
 * @package  FSM\StateMachine\Condition
 * @author   Chris Brand <webmaster@cainsvault.com>
 */
interface ConditionInterface
{
    /**
     * Return the name of the condition. This a string
     * that represents the logic as a label.
     *
     * @return string
     */
    public function getName();

    /**
     * Run the condition.
     *
     * @return boolean
     */
    public function check();
}
