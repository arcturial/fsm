<?php
/*
 * This file is part of the StateMachine library. A simple
 * finite state machine implementation for PHP.
 */

namespace FSM\StateMachine\Condition;

/**
 * This condition is used to invert any other condition.
 *
 * @category FSM
 * @package  FSM\StateMachine\Condition
 * @author   Chris Brand <webmaster@cainsvault.com>
 */
class Not implements ConditionInterface
{
    /**
     * @var ConditionInterface
     */
    private $condition;

    /**
     * Construct a new instance.
     *
     * @param ConditionInterface $condition The condition to invert
     */
    public function __construct(ConditionInterface $condition)
    {
        $this->condition = $condition;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return "not(" . $this->condition->getName() . ")";
    }

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        return !$this->condition->check();
    }
}