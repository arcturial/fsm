<?php
namespace League\StateMachine\State;

interface StateInterface
{
    const TYPE_INITIAL = 0;
    const TYPE_INTERMEDIATE = 1;
    const TYPE_FINAL = 2;

    public function __construct($name, $type);

    public function addAction(ActionInterface $action);

    public function getName();

    public function getType();

    public function process();
}