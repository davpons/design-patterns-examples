<?php

/**
 * La clase Component base declara operaciones comunes
 * para objetos simples y complejos de una composición.
 */
abstract class Component
{
    protected ?Component $parent;

    public function __construct()
    {
        $this->parent = null;
    }

    /**
     * Opcionalmente, el componente base puede declarar una interfaz
     * para configurar y acceder a un padre del componente en una estructura de árbol.
     * También puede proporcionar alguna implementación predeterminada para estos métodos.
     */
    public function setParent(?Component $parent): void
    {
        $this->parent = $parent;
    }

    public function getParent(): ?Component
    {
        return $this->parent;
    }

    /*
     * En algunos casos, sería beneficioso definir las operaciones de administración
     * de hijos directamente en la clase Componente base. De esta manera, no necesitarás
     * exponer ninguna clase de componente concreta al código del cliente,
     * incluso durante el ensamblaje del árbol de objetos.
     * La desventaja es que estos métodos estarán vacíos para los componentes a nivel de hoja.
     */
    public function add(Component $component): void { }

    public function remove(Component $component): void { }

    /*
     * Es conveniente proporcionar un método que permita al código
     * del cliente determinar si un componente puede tener hijos.
     */
    public function isComposite(): bool
    {
        return false;
    }

    /*
     * El componente base puede implementar algún comportamiento predeterminado
     * o dejarlo en manos de clases concretas (declarando el método que contiene
     * el comportamiento como "abstracto").
     */
    abstract public function operation(): string;
}

/**
 * La clase Leaf representa los objetos finales de una composición. Una hoja no puede tener hijos.
 * Por lo general, son los objetos Hoja los que hacen el trabajo real, mientras que los 
 * objetos Compuestos sólo deleguen a sus subcomponentes.
 */
class Leaf extends Component
{
    public function operation(): string
    {
        return '🍁 Hoja';
    }
}

/**
 * La clase Composite representa los componentes complejos que pueden tener hijos.
 * Por lo general, los objetos compuestos delegan el trabajo real a sus hijos 
 * y luego "resumen" el resultado.
 */
class Composite extends Component
{
    protected \SplObjectStorage $children;

    public function __construct()
    {
        parent::__construct();
        $this->children = new \SplObjectStorage();
    }

    /*
     * Un objeto compuesto puede agregar o eliminar otros componentes
     * tanto simples como complejos de su lista secundaria o de ella.
     */
    public function add(Component $component): void
    {
        $this->children->attach($component);
        $component->setParent($this);
    }

    public function remove(Component $component): void
    {
        $this->children->detach($component);
        $component->setParent(null);
    }

    public function isComposite(): bool
    {
        return true;
    }

    /**
     * El Compuesto ejecuta su lógica primaria de una manera particular.
     * Atraviesa recursivamente a todos sus hijos, recopilando y sumando sus resultados.
     * Dado que los hijos del compuesto pasan estas llamadas a sus hijos y así sucesivamente,
     * como resultado se recorre todo el árbol de objetos.
     */
    public function operation(): string
    {
        $results = [];
        foreach ($this->children as $child) {
            $results[] = $child->operation();
        }

        $nodeType = $this->getParent() === null ? '🌳 Arbol' : ' 🎋 Rama';

        return $nodeType . '( ' . implode(' + ', $results) . ' )';
    }
}

function clientCode(Component $component)
{
    echo 'RESULTADO: ' . $component->operation();
}

$leaf = new Leaf();
clientCode($leaf);
echo '<br>----<br>';

$tree = new Composite();
$branch1 = new Composite();
$branch1->add(new Leaf());
$branch1->add(new Leaf());
$branch2 = new Composite();
$branch2->add(new Leaf());
$tree->add($branch1);
$tree->add($branch2);
clientCode($tree);
echo '<br>----<br>';

/*
 * Gracias a que las operaciones de gestión de hijos se declaran en la clase Component base,
 * el código del cliente puede trabajar con cualquier componente, simple o complejo,
 * sin depender de sus clases concretas.
 */
function clientCode2(Component $component1, Component $component2)
{
    if ($component1->isComposite()) {
        $component1->add($component2);
    }
    echo 'RESULTADO: ' . $component1->operation();
}

/*
 * El Cliente no necesita verificar las clases
 * de los diferentes componentes incluso al gestionar el árbol:
 */
clientCode2($tree, $leaf);
