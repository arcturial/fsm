<?php
namespace FSM\StateMachine\Test\State;

use FSM\StateMachine\State\State;
use FSM\StateMachine\State\StateInterface;

class StateTest extends \PHPUnit_Framework_TestCase
{
    public function testAction()
    {
        $action = $this->getMock('FSM\StateMachine\State\ActionInterface', array('run'));
        $action->expects($this->once())
            ->method('run')
            ->will($this->returnValue(true));

        $state = new State('test');
        $state->addAction($action);

        $this->assertTrue($state->process());
    }

    public function testType()
    {
        $state = new State('test', StateInterface::TYPE_FINAL);

        $this->assertSame('test', $state->getName());
        $this->assertSame(StateInterface::TYPE_FINAL, $state->getType());
    }
}