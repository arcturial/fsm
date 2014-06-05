Finite State Machine
====================

Master: [![Build Status](https://secure.travis-ci.org/arcturial/fsm.png?branch=master)](http://travis-ci.org/arcturial/fsm)

THis library provides a simple integrated Finite State Machine. The state machine consts of States/Transitions/Triggers.
This is experimental fork of arturial/fsm with the focus on building business process related tool.

1. States
---------

States define the...states that your machine can be in at any given point during the process. States are connected by transitions.

``` php
$stateCreated = new State('created', StateInterface::TYPE_INITIAL);
$stateImported = new State('imported');
$stateLive = new State('imported', StateInterface::TYPE_FINAL);
```

State types include `TYPE_INITIAL`, `TYPE_INTERMEDIATE`, `TYPE_FINAL`.

### Actions

States can contain actions. Actions are set tasks that need to be performed in order to successfully transition. Like saying, "when a payment goes
into a pending state, we need to notify a support consultant.".

``` php
$stateImported = new State('imported');
$stateImported->addAction(new NotifyAction());
$stateImported->addAction(new TwitterAction());
```

2. Transitions
--------------

Transitions are logical links (paths) between different states. Transitions can also have a transfer condition. Transfer conditions
need to evaluate to `true` to transition to a new state. Transitions are constructed by specifying it's initial state and the state to
which it needs to transfer.

``` php
$transitionCreatedImported = new Transition($stateCreated, $stateImported);
$transitionImportedLive = new Transition($stateImported, $stateLive);
```

### Conditions

A transition can have an optional condition. This condition must evaluate to true. Conditions can easily be recycled by different states.

``` php
class ImportPermissions implements \FSM\StateMachine\Condition\ConditionInterface
{
    public function check()
    {
        return true; // Return true/false depending on the outcome of your condition
    }
}

$transitionCreatedImported = new Transition($stateCreated, $stateImported);
$transitionCreatedImported->addCondition(new ImportPermissions());

$transitionImportedLive = new Transition($stateImported, $stateLive);
```

3. Triggers
-----------

Triggers are certain events that can be fired that will attempt to push the StateMachine forward. Triggers can be associated with
a series of different transitions and will automatically determine which transition path to follow.

``` php
$stateMachine->addTrigger('triggerName', array($transitionCreatedImported, $transitionImportedLive));
```

4. Usage Example
----------------

Putting it all together, the state machine will look something like this:

``` php

// Set up a condition
class ImportPermissions implements \FSM\StateMachine\Condition\ConditionInterface
{
    public function check()
    {
        return true; // Return true/false depending on the outcome of your condition
    }
}

// Set up a machine
class ImportMachine extends \FSM\StateMachine\StateMachine
{
    public function __construct($name)
    {
        // Potentially inject some custom dependencies?
        parent::__construct($name);
    }

    protected function configureMachine()
    {
        $stateCreated = new State('created', StateInterface::TYPE_INITIAL);
        $stateImported = new State('imported');
        $stateLive = new State('imported', StateInterface::TYPE_FINAL);

        $transitionCreatedImported = new Transition($stateCreated, $stateImported);
        $transitionCreatedImported->addCondition(new ImportPermissions());

        $transitionImportedLive = new Transition($stateImported, $stateLive);

        // Add the supported machine transitions
        $this->addTransition($transitionCreatedImported);
        $this->addTransition($transitionImportedLive);

        // Add machine triggers
        $this->addTrigger('import', array($transitionCreatedImported, $transitionImportedLive));
        $this->addTrigger('live', array($transitionImportedLive));

        // The initial state can be determined by external dependencies
        // if required
        $this->setCurrentState('created');
    }
}

$machine = new ImportMachine();

echo $machine->getCurrentState(); // echo 'created'

// Trigger machine import
$machine->trigger('import');

echo $machine->getCurrentState(); // echo 'imported' (update the ImportPermissions return to false will echo 'created')

// Trigger machine 'mark as live'
$machine->trigger('live');

echo $machine->getCurrentState(); // echo 'live'

```

Since the "import" trigger contains both the `import` and `live` transition. This trigger can be "deep resolved". This means
that it can go straight from importing to making it live. To enable deep resolution you can pass `$machine->trigger('import', true);`


5. More Examples
----------------

For a more in depth example and a visual representation of a state graph. You can view tests/FSM/StateMachine/Stage/Graph.php in your browser. You need to
optionally download the [GraphViz](http://www.graphviz.org/) library in order to generate a more detailed image.