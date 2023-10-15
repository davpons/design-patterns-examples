<?php

/**
 * La interfaz básica del Componente
 * define operaciones que los decoradores pueden modificar.
 */
interface Component
{
    public function operation(): string;
}

/**
 * Los componentes concretos proporcionan implementaciones
 * predeterminadas de las operaciones.
 * Puede haber varias variaciones de estas clases.
 */
class ConcreteComponent implements Component
{
    public function operation(): string
    {
        return 'ConcreteComponent';
    }
}

/**
 * La clase Decorator base sigue la misma interfaz que los demás componentes.
 * El objetivo principal de esta clase es definir la interfaz envolvente
 * para todos los decoradores concretos.
 * La implementación predeterminada del código empaquetado podría incluir
 * un campo para almacenar un componente empaquetado y los medios
 * para inicializarlo.
 */
class Decorator implements Component
{
    public function __construct(
        protected Component $component
    ) {}

    /**
     * El Decorador delega todo el trabajo al componente envuelto.
     */
    public function operation(): string
    {
        return $this->component->operation();
    }
}

/**
 * Los decoradores concretos llaman al objeto envuelto y
 * alteran su resultado de alguna manera.
 */
class ConcreteDecoratorA extends Decorator
{
    /**
     * Los decoradores pueden llamar a la implementación principal
     * de la operación, en lugar de llamar directamente al objeto envuelto.
     * Este enfoque simplifica la extensión de las clases de decorador.
     */
    public function operation(): string
    {
        return 'ConcreteDecorator A ( ' . parent::operation() . ' )';
    }
}

/**
 * Los decoradores pueden ejecutar su comportamiento
 * antes o después de la llamada a un objeto envuelto.
 */
class ConcreteDecoratorB extends Decorator
{
    public function operation(): string
    {
        return 'ConcreteDecorator B ( ' . parent::operation() . ' )';
    }
}

/**
 * El código del cliente funciona con todos los objetos que utilizan
 * la interfaz Componente. De esta manera puede permanecer independiente
 * de las clases concretas de componentes con los que trabaja.
 */
function clientCode(Component $component)
{
    echo 'RESULTADO: ' . $component->operation();
}

/**
 * De esta manera, el código del cliente puede admitir componentes simples...
 */
$simple = new ConcreteComponent();
echo 'Cliente: Soy un componente simple: ';
echo $simple->operation();
echo '<br><br>';

/**
 * ...y también decorados.
 *
 * Observa cómo los decoradores pueden envolver
 * no solo componentes simples sino también a otros decoradores.
 */
$decorator1 = new ConcreteDecoratorA($simple);
echo 'Cliente: Ahora soy un componente decorado: ';
clientCode($decorator1);
echo '<br><br>';

$decorator2 = new ConcreteDecoratorB($decorator1);
echo 'Cliente: Ahora soy un componente decorado con otro decorador: ';
clientCode($decorator2);
