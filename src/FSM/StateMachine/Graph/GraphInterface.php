<?php
/*
 * This file is part of the StateMachine library. A simple
 * finite state machine implementation for PHP.
 */

namespace FSM\StateMachine\Graph;

use FSM\StateMachine\StateMachine;

/**
 * The graph interface defines some methods which should
 * be used to create any visualization adapters. These adapters
 * will interact with external graph visualization tools.
 *
 * @category FSM
 * @package  FSM\StateMachine\Graph
 * @author   Chris Brand <webmaster@cainsvault.com>
 */
interface GraphInterface
{
    /**
     * Visualize a state machine.
     *
     * @param StateMachine $machine The machine to render
     *
     * @return string
     */
    public function visualize(StateMachine $machine);
}
