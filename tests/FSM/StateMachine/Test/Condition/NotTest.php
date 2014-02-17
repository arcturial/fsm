<?php
namespace FSM\StateMachine\Test\Condition;

use FSM\StateMachine\Condition\Not;

class ConditionTest extends \PHPUnit_Framework_TestCase
{
    public function testNot()
    {
        $condition = $this->getMockBuilder('FSM\StateMachine\Condition\ConditionInterface')
            ->setMethods(array('check'))
            ->getMockForAbstractClass();

        $condition->expects($this->once())
            ->method('check')
            ->will($this->returnValue(true));

        $not = new Not($condition);

        $this->assertFalse($not->check());
    }
}