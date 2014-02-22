<?php
namespace Arcturial\StateMachine\Test\Transition;

use Arcturial\StateMachine\Transition\Transition;

class TransitionTest extends \PHPUnit_Framework_TestCase
{
    private $created;
    private $imported;
    private $transition;

    public function setUp()
    {
        $this->created = $this->getMockBuilder('Arcturial\StateMachine\State\State')
            ->disableOriginalConstructor()
            ->getMock();

        $this->imported = $this->getMockBuilder('Arcturial\StateMachine\State\State')
            ->disableOriginalConstructor()
            ->setMethods(array('None'))
            ->getMock();

        $this->transition = new Transition($this->created, $this->imported);
    }

    public function testInitial()
    {
        $this->assertSame($this->created, $this->transition->getInitialState());
    }

    public function testTransitionTo()
    {
        $this->assertSame($this->imported, $this->transition->getTransitionTo());
    }

    public function testIsInitialState()
    {
        $this->assertTrue($this->transition->isInitialState($this->created));
    }

    public function testConditions()
    {
        $condition = $this->getMockBuilder('Arcturial\StateMachine\Condition\ConditionInterface')
            ->setMethods(array('check'))
            ->getMockForAbstractClass();

        $condition->expects($this->once())
            ->method('check')
            ->will($this->returnValue(false));

        $this->transition->addCondition($condition);

        $this->assertFalse($this->transition->process());
    }

    public function testActions()
    {
        $action = $this->getMockBuilder('Arcturial\StateMachine\State\ActionInterface')
            ->setMethods(array('run'))
            ->getMockForAbstractClass();

        $action->expects($this->once())
            ->method('run')
            ->will($this->returnValue(true));

        $this->transition->getTransitionTo()->addAction($action);
        $this->assertTrue($this->transition->process());
    }
}