<?php
namespace FSM\StateMachine\Test;

require_once __DIR__ . '/../../../../vendor/autoload.php';
use Fhaculty\Graph\GraphViz;
use Fhaculty\Graph\Graph;
use FSM\StateMachine\Graph\Graph as GraphRender;

use FSM\StateMachine\StateMachine;
use FSM\StateMachine\State\ActionInterface;
use FSM\StateMachine\State\ResolvableState;
use FSM\StateMachine\Condition\ConditionedState;
use FSM\StateMachine\State\StateInterface;
use FSM\StateMachine\State\State;
use FSM\StateMachine\Transition\Transition;
use FSM\StateMachine\Condition\ConditionInterface;
use FSM\StateMachine\Transition\StateTransition;


$stateMachine = null;

class AuthAction implements ActionInterface
{
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function run()
    {
        $this->payment->state = 'authed';
    }
}

class PendingAction implements ActionInterface
{
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function run()
    {
        var_dump('pending commit of payment state: ' . $this->payment->state);
    }
}

class FraudReviewCondition implements ConditionInterface
{
    public function getName()
    {
        return 'Review the payment.';
    }

    public function check()
    {
        return true;
    }
}

class FraudRejectCondition implements ConditionInterface
{
    public function getName()
    {
        return 'Reject the payment.';
    }

    public function check()
    {
        return true;
    }
}

class FraudAcceptPayment implements ConditionInterface
{
    public function getName()
    {
        return 'Accept the payment.';
    }

    public function check()
    {
        return false;
    }
}

class FinancePermissions implements ConditionInterface
{
    public function getName()
    {
        return 'Has finance permissions.';
    }

    public function check()
    {
        return true;
    }
}

class Payment
{
    public $state = 'created';
}



$payment = new Payment();
$stateMachine = new StateMachine('test');

$created = new State('created', StateInterface::TYPE_INITIAL);
$auth = new State('auth', StateInterface::TYPE_INTERMEDIATE);
$auth->addAction(new AuthAction($payment));
$pending = new State('pending', StateInterface::TYPE_INTERMEDIATE);
$auth->addAction(new PendingAction($payment));
$committed = new State('committed', StateInterface::TYPE_FINAL);
$reversed = new State('reversed', StateInterface::TYPE_FINAL);

$authPayment = new Transition($created, $auth);
$commitPayment = new Transition($auth, $committed);
$commitPayment->addCondition(new FraudAcceptPayment());
$commitPaymentReview = new Transition($auth, $pending);
$commitPaymentReview->addCondition(new FraudReviewCondition());
$approvePayment = new Transition($pending, $committed);
$approvePayment->addCondition(new FinancePermissions());
$reversePayment = new Transition($committed, $reversed);
//$commitPayment->addCondition(new ConditionedState(new ReviewCondition(), $committed, $pending));

$stateMachine->addTransition($authPayment);
$stateMachine->addTransition($commitPayment);
$stateMachine->addTransition($approvePayment);
$stateMachine->addTransition($commitPaymentReview);
$stateMachine->addTransition($reversePayment);

$stateMachine->addTrigger('authorize', array($authPayment));
$stateMachine->addTrigger('commit', array($commitPayment, $commitPaymentReview, $approvePayment));
$stateMachine->addTrigger('approve', array($approvePayment));
$stateMachine->addTrigger('reverse', array($reversePayment));

$stateMachine->setCurrentState('created');

// Run tests
var_dump($stateMachine->getCurrentState()->getName());

$stateMachine->trigger('authorize');
var_dump($stateMachine->getCurrentState()->getName());

$stateMachine->trigger('commit');
var_dump($stateMachine->getCurrentState()->getName());



$viz = new GraphViz(new Graph());
$viz->setExecutable('"\\\\DEATHWING\\www\\graphviz-2.36\\release\\bin\\dot.exe"');
$viz->setFormat('svg');

$render = new GraphRender($viz);
echo $render->visualize($stateMachine);
/*


var_dump($stateMachine->getCurrentState()->getName());
*/