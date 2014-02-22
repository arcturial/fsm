<?php
namespace Arcturial\StateMachine\Test\Condition;

use Arcturial\StateMachine\Condition\Not;

class ConditionTest extends \PHPUnit_Framework_TestCase
{
    public function testNot()
    {
        $condition = $this->getMockBuilder('Arcturial\StateMachine\Condition\ConditionInterface')
            ->setMethods(array('check'))
            ->getMockForAbstractClass();

        $condition->expects($this->once())
            ->method('check')
            ->will($this->returnValue(true));

        $not = new Not($condition);

        $this->assertFalse($not->check());
    }
}