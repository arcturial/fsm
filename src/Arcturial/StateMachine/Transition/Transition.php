<?php
/*
 * This file is part of the StateMachine library. A simple
 * finite state machine implementation for PHP.
 */

namespace Arcturial\StateMachine\Transition;

use Arcturial\StateMachine\State\StateInterface;
use Arcturial\StateMachine\Transition\TransitionInterface;
use Arcturial\StateMachine\Condition\ConditionInterface;
use \SplObjectStorage;

/**
 * Transitions are defined paths and conditions that need
 * to be followed from one state to another. This is the default
 * transition object.
 *
 * @category Arcturial
 * @package  Arcturial\StateMachine\Transition
 * @author   Chris Brand <webmaster@cainsvault.com>
 */
class Transition implements TransitionInterface
{
    /**
     * @var StateInterface
     */
    private $initial;

    /**
     * @var StateInterface
     */
    private $transition;

    /**
     * @var ConditionInterface
     */
    private $condition;

    /**
     *
     * @var object
     */
    protected $context = null;

    /**
     * Construct a new transition.
     *
     * @param StateInterface $initial    The initial state
     * @param StateInterface $transition The state to transfer to
     */
    public function __construct(StateInterface $initial, StateInterface $transition)
    {
        $this->initial = $initial;
        $this->transition = $transition;
        
        // Inject self into states
        $this->initial->transition = $this;
        $this->transition->transition = $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isInitialState(StateInterface $initial)
    {
        return $this->initial == $initial;
    }

    /**
     * {@inheritdoc}
     */
    public function getInitialState()
    {
        return $this->initial;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransitionTo()
    {
        return $this->transition;
    }

    /**
     * {@inheritdoc}
     */
    public function addCondition(ConditionInterface $condition)
    {
        $this->condition = $condition;
        
        // Inject self into condition
        $this->condition->transition = $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * {@inheritdoc}
     */
    public function process()
    {
        if ($this->condition && $this->condition->check()) {
            // Process to the transition
            
            return $this->getTransitionTo()->process();
        }

        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * {@inheritdoc}
     */
    public function setContext($context)
    {
        $this->context = $context;
    }
}
