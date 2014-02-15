<?php
namespace League\StateMachine;

use League\StateMachine\State\StateInterface;
use League\StateMachine\Transition\TransitionInterface;
use \SplObjectStore;

class StateMachine
{
    private $activeState;

    private $transitions = array();
    private $states = array();

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

    public function addTransition($alias, TransitionInterface $transition)
    {
        $this->transitions[$alias] = $transition;
    }

    public function getTransitions()
    {
        return $this->transitions;
    }

    public function transition($alias)
    {
        if (!array_key_exists($alias, $this->transitions)) {
            throw new \InvalidArgumentException('Transition not exist');
        }

        $transition = $this->transitions[$alias];

        if ($transition->isInitialState($this->getCurrentState()->getName())) {

            if ($transition->process()) {
                return $this->setCurrentState($transition->getTransitionTo());
            }

        } else {
            throw new \LogicException('Invalid state transition.');
        }
    }
}
