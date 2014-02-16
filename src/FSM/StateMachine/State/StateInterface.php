<?php
namespace FSM\StateMachine\State;

interface StateInterface
{
    const TYPE_INITIAL = 'initial';
    const TYPE_INTERMEDIATE = 'intermediate';
    const TYPE_FINAL = 'final';

    public function addAction(ActionInterface $action);

    public function getName();

    public function getType();

    public function process();
}