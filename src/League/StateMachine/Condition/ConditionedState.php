<?php
namespace League\StateMachine\Condition;

use League\StateMachine\Condition\ConditionInterface;
use League\StateMachine\State\StateInterface;
use League\StateMachine\State\ActionInterface;

class ConditionedState implements StateInterface
{
    private $condition;
    private $state = null;

    public function __construct(ConditionInterface $condition, StateInterface $true, StateInterface $false)
    {
        $this->condition = $condition;
        $this->true = $true;
        $this->false = $false;
    }

    private function _resolve()
    {
        if ($this->condition->check()) {
            return $this->true;
        } else {
            return $this->false;
        }
    }

    public function getState()
    {
        return $this->_resolve();
    }

    public function addAction(ActionInterface $action)
    {
        $this->getState()->addAction($action);
    }

    public function getName()
    {
        return $this->getState()->getName();
    }

    public function getType()
    {
        return $this->getState()->getType();
    }

    public function process()
    {
        return $this->getState()->process();
    }
}