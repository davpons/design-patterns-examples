<?php

/**
 * La interfaz Componente declara un método "accept"
 * que debe tomar la interfaz base del visitante como argumento.
 */
interface Component
{
    public function accept(Visitor $visitor): void;
}

/**
 * Cada Componente Concreto debe implementar el método `accept`
 * de tal manera que llame al método del visitante correspondiente
 * a la clase del componente.
 */
class ConcreteComponentA implements Component
{
    /**
     * Ten en cuenta que estamos llamando a `visitConcreteComponentA`,
     * que coincide con el nombre de la clase actual. De esta forma
     * le hacemos saber al visitante la clase del componente
     * con el que trabaja.
     */
    public function accept(Visitor $visitor): void
    {
        $visitor->visitConcreteComponentA($this);
    }

    /**
     * Los componentes concretos pueden tener métodos especiales
     * que no existen en su clase base o interfaz.
     * El visitante puede utilizar estos métodos ya que conoce la clase
     * concreta del componente.
     */
    public function exclusiveMethodOfConcreteComponentA(): string
    {
        return 'A';
    }
}

class ConcreteComponentB implements Component
{
    public function accept(Visitor $visitor): void
    {
        $visitor->visitConcreteComponentB($this);
    }

    public function specialMethodOfConcreteComponentB(): string
    {
        return 'B';
    }
}

/**
 * La interfaz de visitante declara un conjunto de métodos de visita
 * que corresponden a clases de componentes. La firma de un método de visita
 * permite al visitante identificar la clase exacta del componente
 * con el que está tratando.
 */
interface Visitor
{
    public function visitConcreteComponentA(ConcreteComponentA $element): void;
    public function visitConcreteComponentB(ConcreteComponentB $element): void;
}

/**
 * Concrete Visitor implementa varias versiones del mismo algoritmo,
 * que puede funcionar con todas las clases de componentes concretos.
 *
 * El mayor beneficio del patrón Visitante es utilizarlo con una estructura
 * de objetos compleja, como un árbol compuesto. En este caso, podría resultar
 * útil almacenar algún estado intermedio del algoritmo mientras se ejecutan
 * los métodos del visitante sobre varios objetos de la estructura.
 */
class ConcreteVisitor1 implements Visitor
{
    public function visitConcreteComponentA(ConcreteComponentA $element): void
    {
        echo $element->exclusiveMethodOfConcreteComponentA() . ' + ConcreteVisitor1<br>';
    }

    public function visitConcreteComponentB(ConcreteComponentB $element): void
    {
        echo $element->specialMethodOfConcreteComponentB() . ' + ConcreteVisitor1<br>';
    }
}

class ConcreteVisitor2 implements Visitor
{
    public function visitConcreteComponentA(ConcreteComponentA $element): void
    {
        echo $element->exclusiveMethodOfConcreteComponentA() . ' + ConcreteVisitor2<br>';
    }

    public function visitConcreteComponentB(ConcreteComponentB $element): void
    {
        echo $element->specialMethodOfConcreteComponentB() . ' + ConcreteVisitor2<br>';
    }
}

/**
 * El código del cliente puede ejecutar operaciones de visitantes
 * sobre cualquier conjunto de elementos sin conocer sus clases concretas.
 * La operación de aceptación dirige una llamada a la operación apropiada
 * en el objeto visitante.
 */
function clientCode(array $components, Visitor $visitor): void
{
    foreach ($components as $component) {
        $component->accept($visitor);
    }
}

$components = [
    new ConcreteComponentA(),
    new ConcreteComponentB(),
];

echo 'El código cliente funciona con todos los visitantes a través de la interfaz de visitante básica:<br>';
$visitor1 = new ConcreteVisitor1();
clientCode($components, $visitor1);
echo '<br>';

echo 'Permite que el mismo código cliente funcione con diferentes tipos de visitantes:<br>';
$visitor2 = new ConcreteVisitor2();
clientCode($components, $visitor2);
