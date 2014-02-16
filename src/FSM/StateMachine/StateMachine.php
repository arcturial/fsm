<?php
namespace FSM\StateMachine;

use FSM\StateMachine\State\StateInterface;
use FSM\StateMachine\Transition\TransitionInterface;
use \SplObjectStorage;

class StateMachine
{
    private $activeState;
    private $name;
    private $states = array();
    private $triggers = array();
    private $transitions = array();

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCurrentState()
    {
        return $this->states[$this->activeState];
    }

    public function setCurrentState($state)
    {
        if (array_key_exists($state, $this->states)) {
            $this->activeState = $state;
        }

        return $this;
    }

    public function isCurrentState($state)
    {
        return ($this->getCurrentState()->getName() == $state);
    }

    public function addState(StateInterface $state)
    {
        $this->states[$state->getName()] = $state;
    }

    public function getStates()
    {
        return $this->states;
    }

    public function addTransition(TransitionInterface $transition)
    {
        $this->addState($transition->getInitialState());
        $this->addState($transition->getTransitionTo());

        $this->transitions[] = $transition;
    }

    public function getTransitions()
    {
        return $this->transitions;
    }

    public function getAvailableTransitions()
    {
        $result = array();

        foreach ($this->transitions as $transition) {
            if ($transition->isInitialState($this->getCurrentState())) {
                $result[] = $transition;
            }
        }

        return $result;
    }

    public function addTrigger($trigger, $transitions)
    {
        foreach ($transitions as $transition) {
            if (!in_array($transition, $this->getTransitions())) {
                throw new \LogicException('Invalid transition specified.');
            }
        }

        $this->triggers[$trigger] = $transitions;
    }

    public function trigger($trigger, $deep = false)
    {
        $transitions = $this->triggers[$trigger];

        do
        {
            $path = null;

            foreach ($transitions as $transition) {
                if (!$transition->isInitialState($this->getCurrentState())) {
                    continue;
                }

                // Process a transition and mark it as the new starting point
                if ($transition->process()) {
                    $path = $transition;
                    $this->setCurrentState($path->getTransitionTo()->getName());
                    break;
                }
            }

        } while ($path != null && $deep);

        return $this->getCurrentState();
    }
}
