<?php
/**
 * This is a staging script to demo the graphing capabilities of
 * the state machine. There is quite a few lines of code in here because
 * we have our actions/conditions/machine all in one script...so rather look
 * past that fact.
 */
namespace Arcturial\StateMachine\Stage;

require_once __DIR__ . '/../../../../vendor/autoload.php';

use Fhaculty\Graph\GraphViz;
use Fhaculty\Graph\Graph;
use Arcturial\StateMachine\Graph\Graph as GraphRender;
use Arcturial\StateMachine\Graph\Ascii as GraphAscii;

use Arcturial\StateMachine\StateMachine;
use Arcturial\StateMachine\State\ActionInterface;
use Arcturial\StateMachine\State\ResolvableState;
use Arcturial\StateMachine\Condition\ConditionedState;
use Arcturial\StateMachine\State\StateInterface;
use Arcturial\StateMachine\State\State;
use Arcturial\StateMachine\Transition\Transition;
use Arcturial\StateMachine\Condition\ConditionInterface;
use Arcturial\StateMachine\Transition\StateTransition;

// Create an action to execute when transfering to
// "Pending" state
class PendingAction implements ActionInterface
{
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function run()
    {
        var_dump('pending commit of payment state: ' . $this->payment->state);
        return true;
    }
}

// Fraud score suggest the payment should be reviewed
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

// Fraud score suggest the payment should be rejected
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

// Fraud score suggest the payment should be accepted
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

// Condition to check if the correct permissions exists
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

// Payment object. This might be an ORM entity
// that tracks the payment information.
class Payment
{
    public $state = 'created';
}

// The payment state machine. Takes a Payment object
// as argument in order to keep track of the status.
class PaymentMachine extends StateMachine
{
    public function __construct($name, Payment $payment)
    {
        $this->payment = $payment;

        parent::__construct($name);
    }

    protected function configureMachine()
    {
        $created = new State('created', StateInterface::TYPE_INITIAL);
        $auth = new State('auth', StateInterface::TYPE_INTERMEDIATE);
        $pending = new State('pending', StateInterface::TYPE_INTERMEDIATE);
        $pending->addAction(new PendingAction($this->payment));
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

        $this->addTransition($authPayment);
        $this->addTransition($commitPayment);
        $this->addTransition($approvePayment);
        $this->addTransition($commitPaymentReview);
        $this->addTransition($reversePayment);

        $this->addTrigger('authorize', array($authPayment));
        $this->addTrigger('commit', array($commitPayment, $commitPaymentReview, $approvePayment));
        $this->addTrigger('approve', array($approvePayment));

        // Set the current state of the object
        $this->setCurrentState($this->payment->state);
    }
}

// Start up the machine
$payment = new Payment();
$stateMachine = new PaymentMachine('test', $payment);

// Run tests
$stateMachine->trigger('authorize');
$stateMachine->trigger('commit');



// Render a GraphViz graph
$viz = new GraphViz(new Graph());
$viz->setExecutable(''); // Path to dot.exe
$viz->setFormat('svg');
$render = new GraphRender($viz);
// Set the executable path and then uncomment this line
// echo $render->visualize($stateMachine);


// Render an ASCII graph
echo "<br/><br/>";
$render = new GraphAscii();
echo nl2br($render->visualize($stateMachine));