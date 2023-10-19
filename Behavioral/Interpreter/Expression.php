<?php

abstract class AbstractExpression
{
    abstract public function interpret(Context $context): bool;
}

class Context
{
    private array $poolVariable;

    public function lookUp(string $name): bool
    {
        if (!key_exists($name, $this->poolVariable)) {
            throw new \Exception("No exist variable $name");
        }

        return $this->poolVariable[$name];
    }

    public function assign(VariableExpression $variable, bool $val): void
    {
        $this->poolVariable[$variable->getName()] = $val;
    }
}

/**
 * This TerminalExpression
 */
class VariableExpression extends AbstractExpression
{
    public function __construct(
        private string $name
    ) {}

    public function interpret(Context $context): bool
    {
        return $context->lookUp($this->name);
    }

    public function getName(): string
    {
        return $this->name;
    }
}

/**
 * This NoTerminalExpression
 */
class AndExpression extends AbstractExpression
{
    public function __construct(
        private AbstractExpression $first,
        private AbstractExpression $second
    ) {}

    public function interpret(Context $context): bool
    {
        return $this->first->interpret($context) &&
               $this->second->interpret($context);
    }
}

/**
 * This NoTerminalExpression
 */
class OrExpression extends AbstractExpression
{
    public function __construct(
        private AbstractExpression $first,
        private AbstractExpression $second
    ) {}

    public function interpret(Context $context): bool
    {
        return $this->first->interpret($context) ||
               $this->second->interpret($context);
    }
}

$context = new Context();
$a = new VariableExpression('A');
$b = new VariableExpression('B');
$c = new VariableExpression('C');

$context->assign($a, false);
$context->assign($b, false);
$context->assign($c, true);

$exp1 = new OrExpression($a, $b);
var_dump($exp1->interpret($context));

$exp2 = new OrExpression($exp1, $c);
var_dump($exp2->interpret($context));
