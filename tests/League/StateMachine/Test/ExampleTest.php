<?php
namespace League\StateMachine\Test;

require_once __DIR__ . '/../../../../vendor/autoload.php';
use Fhaculty\Graph\GraphViz;
use Fhaculty\Graph\Graph;
use League\StateMachine\Graph\Graph as GraphRender;

use League\StateMachine\StateMachine;
use League\StateMachine\State\ActionInterface;
use League\StateMachine\State\StateInterface;
use League\StateMachine\State\State;
use League\StateMachine\Transition\Transition;
use League\StateMachine\Transition\ConditionInterface;
use League\StateMachine\Transition\StateTransition;


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

class ImportAction implements ActionInterface
{
    public function __construct(Import $import)
    {
        $this->import = $import;
    }

    public function run()
    {
        $this->import = 'imported';
    }
}

class ImportCondition implements ConditionInterface
{
    public function getName()
    {
        return 'Ready to import exchange rates.';
    }

    public function check()
    {
        return true;
    }
}

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

class Import
{
    public $state = 'created';
}




$import = new Import();
$stateMachine = new StateMachine();

$created = new State('created', StateInterface::TYPE_INITIAL);
$imported = new State('imported');
$imported->addAction(new ImportAction($import));

$stateMachine->addState($created);
$stateMachine->addState($imported);
$stateMachine->addState(new State('live', StateInterface::TYPE_FINAL));

$import = new Transition(array('created'), 'imported');
$import->addCondition(new ImportCondition());

$live = new Transition(array('imported'), 'live');
$live->addCondition(new LiveCondition());

$stateMachine->addTransition('import', $import);
$stateMachine->addTransition('live', $live);

$stateMachine->setCurrentState('created');


$stateMachine->transition('import');
$stateMachine->transition('live');


$viz = new GraphViz(new Graph());
//$viz->setExecutable('');
$viz->setFormat('svg');

$render = new GraphRender($viz);
echo $render->visualize($stateMachine);