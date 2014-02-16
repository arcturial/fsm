<?php
namespace FSM\StateMachine\Test;

require_once __DIR__ . '/../../../../vendor/autoload.php';
use Fhaculty\Graph\GraphViz;
use Fhaculty\Graph\Graph;
use FSM\StateMachine\Graph\Graph as GraphRender;

use FSM\StateMachine\StateMachine;
use FSM\StateMachine\State\ActionInterface;
use FSM\StateMachine\State\StateInterface;
use FSM\StateMachine\State\State;
use FSM\StateMachine\Transition\Transition;
use FSM\StateMachine\Condition\ConditionInterface;
use FSM\StateMachine\Condition\Not;


$stateMachine = null;

/*
class ExampleTest extends \PHPUnit_Framework_TestCase
{
    public function testStage()
    {
        global $stateMachine;

        var_dump($stateMachine->getCurrentState()->getName());
        $stateMachine->transition('import');
        var_dump($stateMachine->getCurrentState()->getName());
    }
}
*/

class LiveCondition implements ConditionInterface
{
    public function getName()
    {
        return 'Variance < 3%';
    }

    public function check()
    {
        return true;
    }
}

class FinancePermissions implements ConditionInterface
{
    public function getName()
    {
        return 'Has finance permissions';
    }

    public function check()
    {
        return true;
    }
}



$stateMachine = new StateMachine('test');

$created = new State('created', StateInterface::TYPE_INITIAL);
$imported = new State('imported');
$pending = new State('pending');
$approved = new State('approved');
$live = new State('live', StateInterface::TYPE_FINAL);


$importTransition = new Transition($created, $imported);
$livePendingTransition = new Transition($imported, $pending);
$livePendingTransition->addCondition(new Not(new LiveCondition()));
$requestApprovedTransition = new Transition($pending, $approved);
$liveApprovedTransition = new Transition($approved, $live);
$liveApprovedTransition->addCondition(new FinancePermissions());
$importLiveTransition = new Transition($imported, $live);
$importLiveTransition->addCondition(new LiveCondition());

$stateMachine->addTransition($importTransition);
$stateMachine->addTransition($livePendingTransition);
$stateMachine->addTransition($requestApprovedTransition);
$stateMachine->addTransition($liveApprovedTransition);
$stateMachine->addTransition($importLiveTransition);

$stateMachine->addTrigger('import', array($importTransition));
$stateMachine->addTrigger('makeLive', array($livePendingTransition, $importLiveTransition, $requestApprovedTransition, $liveApprovedTransition));

$stateMachine->setCurrentState('created');


$stateMachine->trigger('import');
$stateMachine->trigger('makeLive');


$viz = new GraphViz(new Graph());
$viz->setExecutable('"\\\\DEATHWING\\www\\graphviz-2.36\\release\\bin\\dot.exe"');
$viz->setFormat('svg');

$render = new GraphRender($viz);
echo $render->visualize($stateMachine);