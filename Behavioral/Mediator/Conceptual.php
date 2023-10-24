<?php

/**
 * La interfaz Mediator declara un método utilizado por los componentes
 * para notificar al mediador sobre varios eventos.
 * El Mediador puede reaccionar a estos eventos y pasar la ejecución
 * a otros componentes.
 */
interface Mediator
{
    public function notify(object $sender, string $event): void;
}

/**
 * Los Mediadores Concretos implementan un comportamiento cooperativo
 * coordinando varios componentes.
 */
class ConcreteMediator implements Mediator
{
    private Component1 $component1;
    private Component2 $component2;

    public function __construct(Component1 $c1, Component2 $c2)
    {
        $this->component1 = $c1;
        $this->component1->setMediator($this);
        $this->component2 = $c2;
        $this->component2->setMediator($this);
    }

    public function notify(object $sender, string $event): void
    {
        if ($event === 'A') {
            echo 'El mediador reacciona en A y desencadena las siguientes operaciones:<br>';
            $this->component2->doC();
        }

        if ($event === 'D') {
            echo 'El mediador reacciona en D y desencadena las siguientes operaciones:<br>';
            $this->component1->doB();
            $this->component2->doC();
        }
    }
}

/**
 * El BaseComponent proporciona la funcionalidad básica de almacenar
 * la instancia de un mediador dentro de los objetos del componente.
 */
class BaseComponent
{
    public function __construct(
        protected ?Mediator $mediator = null
    ) {}

    public function setMediator(Mediator $mediator): void
    {
        $this->mediator = $mediator;
    }
}

/**
 * Los componentes concretos implementan varias funciones.
 * No dependen de otros componentes. Tampoco dependen de
 * ninguna clase mediadora concreta.
 */
class Component1 extends BaseComponent
{
    public function doA(): void
    {
        echo 'Component 1 hace A<br>';
        $this->mediator->notify($this, 'A');
    }

    public function doB(): void
    {
        echo 'Component 1 hace B<br>';
        $this->mediator->notify($this, 'B');
    }
}

class Component2 extends BaseComponent
{
    public function doC(): void
    {
        echo 'Component 2 hace C<br>';
        $this->mediator->notify($this, 'C');
    }

    public function doD(): void
    {
        echo 'Component 2 hace D<br>';
        $this->mediator->notify($this, 'D');
    }
}

/**
 * Cliente
 */
$c1 = new Component1();
$c2 = new Component2();
$mediator = new ConcreteMediator($c1, $c2);

echo 'El cliente desencadena (triggers) la operación A.<br>';
$c1->doA();

echo '<br><br>';

echo 'El cliente desencadena (triggers) la operación D.<br>';
$c2->doD();
