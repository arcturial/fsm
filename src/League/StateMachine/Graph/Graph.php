<?php
namespace League\StateMachine\Graph;

use League\StateMachine\StateMachine;
use League\StateMachine\State\StateInterface;
use League\StateMachine\Condition\ConditionInterface;
use League\StateMachine\Transition\TransitionInterface;
use Fhaculty\Graph\GraphViz;
use Fhaculty\Graph\Graph as GraphBase;

class Graph implements GraphInterface
{
    private $viz;
    private $states = array();
    private $conditions = array();

    public function __construct(GraphViz $viz)
    {
        $this->viz = $viz;
    }

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

    public function addEdge($vertex, $toVertex)
    {
        $edge = $vertex->createEdgeTo($toVertex);

        $edge->setLayoutAttribute('shape', 'none');
        $edge->setLayoutAttribute('fontname', 'Arial');
        $edge->setLayoutAttribute('fontsize', '12');

        return $edge;
    }

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
