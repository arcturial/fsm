<?php
namespace League\StateMachine\Graph;

use League\StateMachine\StateMachine;
use League\StateMachine\State\StateInterface;
use Fhaculty\Graph\GraphViz;
use Fhaculty\Graph\Graph as GraphBase;

class Graph implements GraphInterface
{
    private $viz;
    private $vertices = array();

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

    public function addEdge($state, $toState)
    {
        $edge = $this->vertices[$state]->createEdgeTo($this->vertices[$toState]);

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
            $this->vertices[$state->getName()] = $this->addNode(
                $graph,
                $state,
                $machine->isCurrentState($state->getName()),
                $state->getType() == StateInterface::TYPE_INITIAL,
                $state->getType() == StateInterface::TYPE_FINAL
            );
        }

        foreach ($this->vertices as $state => $vertex) {
            foreach ($machine->getTransitions() as $transition) {
                if ($transition->isInitialState($state)) {
                    $transitionTo = $transition->getTransitionTo();
                    $edge = $this->addEdge($state, $transitionTo);

                    // Get conditions
                    $label = "";

                    foreach ($transition->getConditions() as $condition) {
                        $if = "IF: " . $condition->getName();
                        $class = get_class($condition);

                        $label .= str_pad($if, strlen($if) + 8, " ", STR_PAD_BOTH) . PHP_EOL
                            . str_pad($class, strlen($class) + 8, " ", STR_PAD_BOTH) . PHP_EOL;
                    }

                    $edge->setLayoutAttribute('label', $label);
                }
            }
        }

        return $this->viz->createImageHtml();
    }
}
