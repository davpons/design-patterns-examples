<?php

/**
 * El Contexto define la interfaz de interés para los clientes.
 */
class Context
{
    /**
     * El Contexto mantiene una referencia a uno de los objetos de Estrategia.
     * El Contexto no conoce la clase concreta de una estrategia.
     * Debería funcionar con todas las estrategias a través de la interfaz de Estrategia.
     */
    private Strategy $strategy;

    /**
     * Por lo general, el contexto acepta una estrategia a través del constructor,
     * pero también proporciona un configurador para cambiarla en tiempo de ejecución.
     */
    public function __construct(Strategy $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * Normalmente, el Contexto permite reemplazar un objeto de Estrategia
     * en tiempo de ejecución.
     */
    public function setStrategy(Strategy $strategy): void
    {
        $this->strategy = $strategy;
    }

    /**
     * El Contexto delega parte del trabajo al objeto Estrategia 
     * en lugar de implementar múltiples versiones del algoritmo por sí solo.
     */
    public function doSomeBusinessLogic(): void
    {
        echo 'Contexto: ordenar datos usando la estrategia (no estoy seguro de cómo lo hará)...<br>';
        $result = $this->strategy->doAlgorithm(['a', 'b', 'c', 'd', 'e', 'f']);
        echo implode(', ', $result) . '<br>';
    }
}

/**
 * La interfaz Estrategia declara operaciones comunes a todas
 * las versiones compatibles de algún algoritmo.
 * El Contexto utiliza esta interfaz para llamar al algoritmo definido 
 * por Estrategias Concretas.
 */
interface Strategy
{
    public function doAlgorithm(array $data): array;
}

/**
 * Las Estrategias Concretas implementan el algoritmo mientras
 * siguen la interfaz de la Estrategia base.
 * La interfaz los hace intercambiables en el contexto.
 */
class ConcreteStrategyA implements Strategy
{
    public function doAlgorithm(array $data): array
    {
        sort($data);
        return $data;
    }    
}

class ConcreteStrategyB implements Strategy
{
    public function doAlgorithm(array $data): array
    {
        rsort($data);
        return $data;
    }
}

/**
 * El código del cliente elige una estrategia concreta y la pasa al contexto.
 * El cliente debe ser consciente de las diferencias entre estrategias
 * para poder tomar la decisión correcta.
 */
$context = new Context(new ConcreteStrategyA());
echo 'Cliente: La estrategia está configurada para ordenación normal:<br>';
$context->doSomeBusinessLogic();

echo '<br><br>';

echo 'Cliente: La estrategia está configurada ahora para invertir el orden:<br>';
$context->setStrategy(new ConcreteStrategyB());
$context->doSomeBusinessLogic();
