<?php
namespace FSM\StateMachine\Test\State;

use FSM\StateMachine\State\ActionInterface;

class ActionTest extends \PHPUnit_Framework_TestCase
{
    public function testRun()
    {
        $action = new TestAction();

        $this->assertTrue($action->run());
    }
}

class TestAction implements ActionInterface
{
    public function run()
    {
        return true;
    }
}