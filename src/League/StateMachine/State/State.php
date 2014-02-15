<?php
namespace League\StateMachine\State;

class State implements StateInterface
{
    private $name;
    private $type;
    private $actions = array();

    public function __construct($name, $type = StateInterface::TYPE_INTERMEDIATE)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function addAction(ActionInterface $action)
    {
        $this->actions[] = $action;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function process()
    {
    }
}