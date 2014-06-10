<?php
/*
 * This file is part of the StateMachine library. A simple
 * finite state machine implementation for PHP.
 */

namespace Arcturial\StateMachine;

use Arcturial\StateMachine\State\StateInterface;
use Arcturial\StateMachine\Transition\TransitionInterface;
use \SplObjectStorage;
use \LogicException;

/**
 * The state machine is an object that keeps track of all
 * transitions/triggers and states in a machine.
 *
 * @category Arcturial
 * @package  Arcturial\StateMachine
 * @author   Chris Brand <webmaster@cainsvault.com>
 */
abstract class StateMachine
{
    /**
     * @var StateInterface
     */
    private $activeState;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $states = array();

    /**
     * @var array
     */
    private $triggers = array();

    /**
     * @var array
     */
    private $transitions = array();

    /**
     * @var array
     */
    private $callbacks = array();
    
    /**
     * @var ContextLocator
     */
    private $contextLocator;
    
    /**
     * Create a new machine and label it.
     *
     * @param string $name The label of the machine
     */
    public function __construct($name, $contextLocator = null)
    {
        $this->name = $name;
        $this->contextLocator = $contextLocator;

        $this->configureMachine();
    }

    /**
     * Set up some machine tasks like states/transitions/triggers.
     *
     * @return boolean
     */
    abstract protected function configureMachine();

    /**
     * Set the current state. This method allows you to update
     * to state in which a machine currently resides.
     *
     * @param string $state The state of the machine
     *
     * @return boolean
     */
    protected function setCurrentState($state)
    {
        $this->activeState = $state;
    }

    /**
     * Add a new transition to the machine.
     *
     * @param TransitionInterface $transition The transition object
     *
     * @return boolean
     */
    protected function addTransition(TransitionInterface $transition)
    {
        $this->states[$transition->getInitialState()->getName()] = $transition->getInitialState();
        $this->states[$transition->getTransitionTo()->getName()] = $transition->getTransitionTo();
        
        // Inject self into transition
        $transition->setMachine($this);
        
        // Resolve the initial state
        foreach ($this->states as $state) {
            if ($state->getType() == StateInterface::TYPE_INITIAL) {
                $this->setCurrentState($state->getName());
            }
        }

        $this->transitions[] = $transition;
    }

    /**
     * Add a new event trigger. The trigger contains a list of transitions
     * that is supported as part of this triggered process. These are all
     * "potential" transitions.
     *
     * @param string $trigger     The trigger name
     * @param array  $transitions Array of transitions
     *
     * @return boolean
     */
    protected function addTrigger($trigger, $transitions)
    {
        // Ensure all transitions are supported by this machine
        foreach ($transitions as $transition) {
            if (!in_array($transition, $this->getTransitions())) {
                throw new LogicException(
                    'Invalid transition specified at trigger "' . $trigger . '".'
                );
            }
        }

        $this->triggers[$trigger] = $transitions;
    }

    /**
     * Return the name of the machine.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return the current state label.
     *
     * @return string
     */
    public function getCurrentState()
    {
        return $this->states[$this->activeState];
    }

    /**
     * Check if the current state label matches.
     *
     * @param string $state The state name
     *
     * @return boolean
     */
    public function isCurrentState($state)
    {
        return ($this->getCurrentState()->getName() == $state);
    }

    /**
     * Return a list of states. This list contains all the recognized states
     * in the transitions added to this machine.
     *
     * @return array
     */
    public function getStates()
    {
        return $this->states;
    }

    /**
     * Return a list of all state transitions.
     *
     * @return array
     */
    public function getTransitions()
    {
        return $this->transitions;
    }

    /**
     * Return the available transitions from the current active state.
     *
     * @return array
     */
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

    /**
     * Whenever a state changes. This callback will be triggered.
     *
     * @return boolean
     */
    public function onTransition($callback)
    {
        if (is_callable($callback)) {
            $this->callbacks[] = $callback;
        }
    }

    /**
     * Triggers a specific event by name. If you specify $deep = true
     * the function will resolve all available transitions until no more
     * transitions are available.
     *
     * @param string  $trigger The event to trigger
     * @param boolean $deep    Recursive resolve state
     *
     * @return boolean
     */
    public function trigger($trigger, $deep = false)
    {
        $transitions = $this->triggers[$trigger];

        do {

            $path = null;

            foreach ($transitions as $transition) {
                if (!$transition->isInitialState($this->getCurrentState())) {
                    continue;
                }

                // Process a transition and mark it as the new starting point
                if ($transition->process()) {

                    foreach ($this->callbacks as $callback) {
                        call_user_func_array($callback, array($transition));
                    }

                    $path = $transition;
                    $this->setCurrentState($path->getTransitionTo()->getName());
                    break;
                }
            }

        } while ($path != null && $deep);

        return $this->getCurrentState();
    }
    
    public function getContextLocator()
    {
        return $this->contextLocator;
    }
}
