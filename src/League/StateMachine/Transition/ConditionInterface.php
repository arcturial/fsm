<?php
namespace League\StateMachine\Transition;

interface ConditionInterface
{
    public function getName();

    public function check();
}