<?php

/**
 * La clase abstracta define un método de plantilla que contiene un esqueleto de algún algoritmo,
 * compuesto por llamadas a (normalmente) operaciones primitivas abstractas.
 * Las subclases concretas deberían implementar estas operaciones, pero dejar intacto 
 * el método de plantilla.
 */
abstract class AbstractClass
{
    // El método de plantilla define el esqueleto de un algoritmo.
    final public function templateMethod()
    {
        $this->baseOperation1();
        $this->requiredOperation1();
        $this->baseOperation2();
        $this->hook1();
        $this->requiredOperation2();
        $this->baseOperation3();
        $this->hook2();
    }

    protected function baseOperation1(): void
    {
        echo "AbstractClass says: I am doing the bulk of the work<br>";
    }

    protected function baseOperation2(): void
    {
        echo "AbstractClass says: But I let subclasses override some operations<br>";
    }

    protected function baseOperation3(): void
    {
        echo "AbstractClass says: But I am doing the bulk of the work anyway<br>";
    }

    // Estas operaciones deben implementarse en subclases.
    abstract protected function requiredOperation1(): void;
    abstract protected function requiredOperation2(): void;

    /**
     * Estos son "ganchos". Las subclases pueden anularlas, pero no es obligatorio
     * ya que los enlaces ya tienen una implementación predeterminada (pero vacía).
     * Los ganchos proporcionan puntos de extensión adicionales en algunos lugares 
     * cruciales del algoritmo.
     */
    protected function hook1(): void { }
    protected function hook2(): void { }       
}

/**
 * Las clases concretas tienen que implementar todas las operaciones abstractas de la clase base.
 * También pueden anular algunas operaciones con una implementación predeterminada.
 */
class ConcreteClass1 extends AbstractClass
{
    protected function requiredOperation1(): void
    {
        echo "ConcreteClass1 says: Implemented Operation1<br>";
    }

    protected function requiredOperation2(): void
    {
        echo "ConcreteClass1 says: Implemented Operation2<br>";
    }
}

/**
 * Normalmente, las clases concretas anulan sólo una fracción de las operaciones de la clase base.
 */
class ConcreteClass2 extends AbstractClass
{
    protected function requiredOperation1(): void
    {
        echo "ConcreteClass2 says: Implemented Operation1<br>";
    }

    protected function requiredOperation2(): void
    {
        echo "ConcreteClass2 says: Implemented Operation2<br>";
    }

    protected function hook1(): void
    {
        echo "ConcreteClass2 says: Overridden Hook1<br>";
    }
}

/*
    El código del cliente llama al método de plantilla para codificar 
    no tiene que conocer la clase concreta de un objeto con el que trabaja, 
    siempre y cuando trabaje con objetos a través de la interfaz de su clase base.
*/
function clientCode(AbstractClass $class)
{
    $class->templateMethod();
}

echo "Same client code can work with different subclasses:<br>";
clientCode(new ConcreteClass1());
echo "<br>";

echo "Same client code can work with different subclasses:<br>";
clientCode(new ConcreteClass2());
echo "<br>";
