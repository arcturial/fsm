<?php
/*
 * This file is part of the StateMachine library. A simple
 * finite state machine implementation for PHP.
 */

namespace Arcturial\StateMachine\Graph;

use Arcturial\StateMachine\StateMachine;
use Arcturial\StateMachine\State\StateInterface;
use Arcturial\StateMachine\Condition\ConditionInterface;
use Arcturial\StateMachine\Transition\TransitionInterface;
use Fhaculty\Graph\GraphViz;
use Fhaculty\Graph\Graph as GraphBase;

/**
 * Graph abstraction used to visualize a graph of the
 * statemachine using GraphViz software.
 *
 * @category Arcturial
 * @package  Arcturial\StateMachine\Graph
 * @author   Chris Brand <webmaster@cainsvault.com>
 */
class Graph implements GraphInterface
{
    /**
     * @var GraphViz
     */
    private $viz;

    /**
     * @var array
     */
    private $states = array();

    /**
     * Create a new visualizer.
     *
     * @param GraphViz $viz The graph viz driver
     */
    public function __construct(GraphViz $viz)
    {
        $this->viz = $viz;
    }

    /**
     * Add a new node to the graph.
     *
     * @param GraphBase      $graph   The graphing object
     * @param StateInterface $state   The statue to visualize
     * @param boolean        $current Is this the current node
     * @param boolean        $initial Is this an initial node
     * @param boolean        $final   Is this a final node
     *
     * @return Vertex
     */
    public function addNode(GraphBase $graph, StateInterface $state, $current = false, $initial = false, $final = false)
    {
        $node = $graph->createVertex($state->getName());

        $fill = ($initial) ? 'solid,filled' : 'rounded,filled';
        $fill .= ($current) ? ',bold' : '';
        $color = ($current) ? '#5DA7E3' : (($final) ? "#9E0D0D" : '#145C96');

        $node->setLayoutAttribute('shape', 'box');
        $node->setLayoutAttribute('style', $fill);
        $node->setLayoutAttribute('fillcolor', $color);
        $node->setLayoutAttribute('fontname', 'Arial');
        $node->setLayoutAttribute('fontsize', '12');
        $node->setLayoutAttribute('fontcolor', '#FFFFFF');

        return $node;
    }

    /**
     * Add a new condition.
     *
     * @param GraphBase          $graph     The graphing object
     * @param ConditionInterface $condition The condition to visualize
     * @param int                $index     The transition index
     *
     * @return Vertex
     */
    public function addCondition(GraphBase $graph, ConditionInterface $condition, $index)
    {
        $node = $graph->createVertex("[" . $index . "] " .  $condition->getName());

        $node->setLayoutAttribute('shape', 'box');
        $node->setLayoutAttribute('style', 'rounded,dashed');
        $node->setLayoutAttribute('fontname', 'Arial');
        $node->setLayoutAttribute('fontsize', '12');
        $node->setLayoutAttribute('fontcolor', '#111111');

        return $node;
    }

    /**
     * Add a new edge.
     *
     * @param Vertex $vertex   The 'from' vertex
     * @param Vertex $toVertex The 'to' vertex
     *
     * @return Edge
     */
    public function addEdge($vertex, $toVertex)
    {
        $edge = $vertex->createEdgeTo($toVertex);

        $edge->setLayoutAttribute('shape', 'none');
        $edge->setLayoutAttribute('fontname', 'Arial');
        $edge->setLayoutAttribute('fontsize', '12');

        return $edge;
    }

    /**
     * {@inheritdoc}
     */
    public function visualize(StateMachine $machine)
    {
        $graph = $this->viz->getGraph();

        // create some cities
        foreach ($machine->getStates() as $state) {
            $this->states[$state->getName()] = $this->addNode(
                $graph,
                $state,
                $machine->isCurrentState($state->getName()),
                $state->getType() == StateInterface::TYPE_INITIAL,
                $state->getType() == StateInterface::TYPE_FINAL
            );
        }

        foreach ($machine->getTransitions() as $index => $transition) {

            $vertex = $this->states[$transition->getInitialState()->getName()];
            $vertexTo = $this->states[$transition->getTransitionTo()->getName()];

            if ($transition->getCondition()) {
                $condition = $this->addCondition($graph, $transition->getCondition(), $index);

                $this->addEdge($vertex, $condition);
                $vertex = $condition;
            }

            $this->addEdge($vertex, $vertexTo);
        }

        return $this->viz->createImageHtml();
    }
}
