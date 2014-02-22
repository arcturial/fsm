<?php
/*
 * This file is part of the StateMachine library. A simple
 * finite state machine implementation for PHP.
 */

namespace Arcturial\StateMachine\State;

/**
 * States are nodes in a state graph. They define certain 'states'
 * within a process. This is a default state object.
 *
 * @category Arcturial
 * @package  Arcturial\StateMachine\State
 * @author   Chris Brand <webmaster@cainsvault.com>
 */
class State implements StateInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $actions = array();

    /**
     * Construct a new state object.
     *
     * @param string $name The name of the state
     * @param string $type The type of state node
     */
    public function __construct($name, $type = StateInterface::TYPE_INTERMEDIATE)
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function addAction(ActionInterface $action)
    {
        $this->actions[] = $action;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function process()
    {
        $result = true;

        foreach ($this->actions as $action) {
            $result &= $action->run();
        }

        return (bool) $result;
    }
}
