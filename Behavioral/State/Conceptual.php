<?php

/**
 * El Contexto define la interfaz de interés para los clientes.
 * También mantiene una referencia a una instancia de una subclase de Estado,
 * que representa el estado actual del Contexto.
 */
class Context
{
    /**
     * Una referencia al estado actual del contexto.
     */
    private State $state;

    public function __construct(State $state)
    {
        $this->transitionTo($state);
    }

    /**
     * El Contexto permite cambiar el objeto Estado en tiempo de ejecución.
     */
    public function transitionTo(State $state): void
    {
        echo 'Contexto: Transita hacia ' . get_class($state) . '<br>';
        $this->state = $state;
        $this->state->setContext($this);
    }

    /**
     * El Contexto delega parte de su comportamiento al objeto Estado actual.
     */
    public function request1(): void
    {
        $this->state->handle1();
    }

    public function request2(): void
    {
        $this->state->handle2();
    }
}

/**
 * La clase Estado base declara métodos que todos los Estados Concretos
 * deberían implementar y también proporciona una referencia al objeto Contexto,
 * asociado con el Estado. Los Estados pueden utilizar esta referencia
 * para realizar la transición del Contexto a otro Estado.
 */
abstract class State
{
    protected Context $context;

    public function setContext(Context $context): void
    {
        $this->context = $context;
    }

    abstract public function handle1(): void;
    abstract public function handle2(): void;
}

/**
 * Los Estados Concretos implementan diversos comportamientos,
 * asociados a un estado del Contexto.
 */
class ConcreteStateA extends State
{
    public function handle1(): void
    {
        echo 'ConcreteStateA maneja request1...<br>';
        echo 'ConcreteStateA cambia el estado del contexto...<br>';
        $this->context->transitionTo(new ConcreteStateB());
    }

    public function handle2(): void
    {
        echo 'ConcreteStateA maneja request2...<br>';
    }
}

class ConcreteStateB extends State
{
    public function handle1(): void
    {
        echo 'ConcreteStateB maneja request1...<br>';
    }

    public function handle2(): void
    {
        echo 'ConcreteStateB maneja request2...<br>';
        echo 'ConcreteStateB cambia el estado del contexto...<br>';
        $this->context->transitionTo(new ConcreteStateA());
    }
}

$context = new Context(new ConcreteStateA());

$context->request1();
$context->request1();
$context->request2();
$context->request2();
