<?php
/*
 * This file is part of the StateMachine library. A simple
 * finite state machine implementation for PHP.
 */

namespace FSM\StateMachine\Graph;

use FSM\StateMachine\StateMachine;
use FSM\StateMachine\State\StateInterface;
use FSM\StateMachine\Condition\ConditionInterface;
use FSM\StateMachine\Transition\TransitionInterface;
use Fhaculty\Graph\GraphViz;
use Fhaculty\Graph\Graph as GraphBase;

/**
 * Visualize graph transitions as ASCII text.
 *
 * @category FSM
 * @package  FSM\StateMachine\Graph
 * @author   Chris Brand <webmaster@cainsvault.com>
 */
class Ascii implements GraphInterface
{

    /**
     * {@inheritdoc}
     */
    public function visualize(StateMachine $machine)
    {
        $result = array();
        foreach ($machine->getTransitions() as $index => $transition) {

            $from = $transition->getInitialState();
            $to = $transition->getTransitionTo();
            $condition = $transition->getCondition();

            $row = "";
            $row .= $from->getName() . " -> ";

            if ($condition) {
                $row .= "[" . $condition->getName() . "] -> ";
            }

            $row .= $to->getName();

            $result[] = $row;
        }

        return implode(PHP_EOL, $result);
    }
}
