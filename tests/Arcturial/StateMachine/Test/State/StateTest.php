<?php
namespace Arcturial\StateMachine\Test\State;

use Arcturial\StateMachine\State\State;
use Arcturial\StateMachine\State\StateInterface;

class StateTest extends \PHPUnit_Framework_TestCase
{
    public function testAction()
    {
        $action = $this->getMock('Arcturial\StateMachine\State\ActionInterface', array('run'));
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