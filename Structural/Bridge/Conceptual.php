<?php

/**
 * La Abstracción define la interfaz para la parte de "control"
 * de las dos jerarquías de clases. Mantiene una referencia a un objeto
 * de la jerarquía de implementación y delega todo el trabajo real
 * a este objeto.
 */
class Abstraction
{
    public function __construct(
        protected Implementation $implementation
    ) {}

    public function operation(): string
    {
        return 'Abstraction Base: operation with... ' .
            $this->implementation->operationImplementation();
    }
}

/**
 * Es posible ampliar la Abstracción sin cambiar las clases de Implementación.
 */
class ExtendedAbstraction extends Abstraction
{
    public function operation(): string
    {
        return 'ExtendedAbstraction: Extended operation with... ' .
            $this->implementation->operationImplementation();
    }
}

/**
 * La implementación define la interfaz para todas las clases de implementación.
 * No es necesario que coincida con la interfaz de Abstraction.
 * De hecho, las dos interfaces pueden ser completamente diferentes.
 * Normalmente, la interfaz de Implementación proporciona solo operaciones primitivas,
 * mientras que la Abstracción define operaciones de nivel superior basadas
 * en esas primitivas.
 */
interface Implementation
{
    public function operationImplementation(): string;
}

/**
 * Cada Implementación Concreta corresponde a una plataforma específica e implementa
 * la interfaz de Implementación utilizando la API de esa plataforma.
 */
class ConcreteImplementationA implements Implementation
{
    public function operationImplementation(): string
    {
        return 'ConcreteImplementationA: Here\'s the result on the platform A.<br>';
    }
}

class ConcreteImplementationB implements Implementation
{
    public function operationImplementation(): string
    {
        return 'ConcreteImplementationB: Here\'s the result on the platform B.<br>';
    }
}

/**
 * Excepto en la fase de inicialización, donde un objeto Abstracción se vincula
 * con un objeto de Implementación específico, el código del cliente solo debe
 * depender de la clase Abstracción. De esta manera, el código del cliente
 * puede admitir cualquier combinación de abstracción-implementación.
 */
class Client
{
    /**
     * El código del cliente debería poder funcionar con cualquier combinación
     * de implementación-abstracción preconfigurada.
     */
    public function operateOne(): void
    {
        $implementation = new ConcreteImplementationA();
        $abstraction = new Abstraction($implementation);

        echo $abstraction->operation();
    }

    public function operateTwo(): void
    {
        $implementation = new ConcreteImplementationB();
        $abstraction = new ExtendedAbstraction($implementation);

        echo $abstraction->operation();
    }

    public function operateThree(): void
    {
        $implementation = new ConcreteImplementationB();
        $abstraction = new Abstraction($implementation);

        echo $abstraction->operation();
    }

    public function operateFour(): void
    {
        $implementation = new ConcreteImplementationA();
        $abstraction = new ExtendedAbstraction($implementation);

        echo $abstraction->operation();
    }    
}

$client = new Client();
$client->operateOne();
$client->operateTwo();
$client->operateThree();
$client->operateFour();
