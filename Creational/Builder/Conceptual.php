<?php

/**
 * La interfaz Builder especifica métodos
 * para crear las diferentes partes de los objetos Producto.
 */
interface Builder
{
    public function producePartA(): void;
    public function producePartB(): void;
    public function producePartC(): void;
}

/**
 * Las clases Concrete Builder implementan la interfaz Builder
 * y proporcionan implementaciones específicas de los pasos de construcción.
 * Un programa puede tener varias variaciones de Builders,
 * implementadas de manera diferente.
 */
class ConcreteBuilder1 implements Builder
{
    private Product1 $product;

    /**
     * Una instancia de Builder nueva debe contener un objeto de producto
     * en blanco, que se utiliza en el ensamblaje posterior.
     */
    public function __construct()
    {
        $this->reset();
    }

    public function reset(): void
    {
        $this->product = new Product1;
    }

    /**
     * Todos los pasos de producción
     * funcionan con la misma instancia de producto.
     */
    public function producePartA(): void
    {
        $this->product->parts[] = 'PartA1';
    }

    public function producePartB(): void
    {
        $this->product->parts[] = 'PartB1';
    }

    public function producePartC(): void
    {
        $this->product->parts[] = 'PartC1';
    }

    /**
     * Se supone que Concrete Builder debe proporcionar sus propios métodos
     * para obtener resultados. Esto se debe a que varios tipos de
     * constructores pueden crear productos completamente diferentes
     * que no siguen la misma interfaz.
     * Por lo tanto, dichos métodos no se pueden declarar en la
     * interfaz básica del Builder (al menos en un lenguaje de programación
     * de tipo estático). Ten en cuenta que PHP es un lenguaje de tipo dinámico
     * y este método PUEDE estar en la interfaz base.
     * Sin embargo, no lo declararemos allí en aras de la claridad.
     *
     * Por lo general, después de devolver el resultado final al cliente,
     * se espera que una instancia de construcción esté lista para comenzar
     * a producir otro producto. Por eso es una práctica habitual llamar
     * al método reset al final del cuerpo del método `getProduct`.
     * Sin embargo, este comportamiento no es obligatorio y puede hacer que
     * sus constructores esperen una llamada de reinicio explícita desde
     * el código del cliente antes de deshacerse del resultado anterior.
     */
    public function getProduct(): Product1
    {
        $result = $this->product;
        $this->reset();

        return $result;
    }
}

/**
 * Tiene sentido utilizar el patrón Builder sólo cuando sus productos
 * son bastante complejos y requieren una configuración extensa.
 *
 * A diferencia de otros patrones creacionales, diferentes constructores
 * concretos pueden producir productos no relacionados. En otras palabras,
 * es posible que los resultados de varios constructores no siempre sigan
 * la misma interfaz.
 */
class Product1
{
    public array $parts = [];

    public function listParts(): void
    {
        echo 'Product parts: ' . implode(', ', $this->parts) . '<br><br>';
    }
}

/**
 * El Director sólo es responsable de ejecutar los pasos de construcción
 * en una secuencia particular. Resulta útil a la hora de producir productos
 * según un pedido o configuración específicos. Estrictamente hablando,
 * la clase Director es opcional, ya que el cliente puede controlar
 * a los constructores directamente.
 */
class Director
{
    private Builder $builder;

    /**
     * El Director trabaja con cualquier instancia Builder que le pase
     * el código del cliente. De esta forma, el código del cliente puede
     * alterar el tipo final del producto recién ensamblado.
     */
    public function setBuilder(Builder $builder): void
    {
        $this->builder = $builder;
    }

    /**
     * El director puede construir varias variaciones de productos
     * siguiendo los mismos pasos de construcción.
     */
    public function buildMinimalViableProduct(): void
    {
        $this->builder->producePartA();
    }

    public function buildFullFeaturedProduct(): void
    {
        $this->builder->producePartA();
        $this->builder->producePartB();
        $this->builder->producePartC();
    }
}

/**
 * El código del cliente crea un objeto Builder, lo pasa al director
 * y luego inicia el proceso de construcción.
 * El resultado final se recupera del objeto Builder.
 */
function clientCode(Director $director)
{
    $builder = new ConcreteBuilder1();
    $director->setBuilder($builder);

    echo 'Producto estándar básico:<br>';
    $director->buildMinimalViableProduct();
    $builder->getProduct()->listParts();

    echo 'Producto estándar con todas las funciones:<br>';
    $director->buildFullFeaturedProduct();
    $builder->getProduct()->listParts();

    // El patrón Builder se puede utilizar sin una clase Director.
    echo 'Producto Custom:<br>';
    $builder->producePartA();
    $builder->producePartC();
    $builder->getProduct()->listParts();
}

$director = new Director();
clientCode($director);
