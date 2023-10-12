<?php

class OrderContext
{
    private State $state;

    public static function create(): self
    {
        $order = new self();
        $order->state = new StateCreated();

        return $order;
    }

    public function setState(State $state): void
    {
        $this->state = $state;
    }

    public function proceedToNext(): void
    {
        $this->state->proceedToNext($this);
    }

    public function toString(): string
    {
        return $this->state->toString();
    }
}

interface State
{
    public function proceedToNext(OrderContext $context): void;
    public function toString(): string;
}

class StateCreated implements State
{
    public function proceedToNext(OrderContext $context): void
    {
        $context->setState(new StateShipped());
    }

    public function toString(): string
    {
        return 'created';
    }
}

class StateShipped implements State
{
    public function proceedToNext(OrderContext $context): void
    {
        $context->setState(new StateDone());
    }

    public function toString(): string
    {
        return 'shipped';
    }
}

class StateDone implements State
{
    public function proceedToNext(OrderContext $context): void
    {
        // no es necesario hacer nada.
    }

    public function toString(): string
    {
        return 'done';
    }
}

$order = OrderContext::create();
echo $order->toString() . '<br>';

$order->proceedToNext();
echo $order->toString() . '<br>';

$order->proceedToNext();
echo $order->toString() . '<br>';

$order->proceedToNext();
echo $order->toString() . '<br>';
