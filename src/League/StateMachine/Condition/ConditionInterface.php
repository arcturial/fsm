<?php
namespace League\StateMachine\Condition;

interface ConditionInterface
{
    public function getName();

    public function check();
}