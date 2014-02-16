<?php
namespace League\StateMachine\Condition;

class Not implements ConditionInterface
{
    private $condition;

    public function __construct(ConditionInterface $condition)
    {
        $this->condition = $condition;
    }

    public function getName()
    {
        return "not(" . $this->condition->getName() . ")";
    }

    public function check()
    {
        return !$this->condition->check();
    }
}