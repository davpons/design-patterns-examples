<?php

/**
 * La interfaz Command declara un método para ejecutar un comando.
 */
interface Command
{
    public function execute(): void;
}

/**
 * Algunos comandos pueden implementar operaciones simples por sí solos.
 */
class SimpleCommand implements Command
{
    public function __construct(
        private string $payload
    ) {}

    public function execute(): void
    {
        echo 'SimpleCommand: Mira, puedo hacer cosas simples como imprimir (' . $this->payload . ')<br>';
    }
}

/**
 * Sin embargo, algunos comandos pueden delegar operaciones
 * más complejas a otros objetos, llamados "receptores".
 */
class ComplexCommand implements Command
{
    public function __construct(
        private Receiver $receiver,
        private string $a,
        private string $b
    ) {}

    /**
     * Los comandos pueden delegar a cualquier método de un receptor.
     */
    public function execute(): void
    {
        echo 'ComplexCommand: Las cosas complejas deben ser realizadas por un objeto receptor.<br>';

        $this->receiver->doSomething($this->a);
        $this->receiver->doSomethingElse($this->b);
    }
}

/**
 * Las clases de Receiver contienen cierta lógica empresarial importante.
 * Saben realizar todo tipo de operaciones asociadas a la realización
 * de una solicitud. De hecho, cualquier clase puede actuar como Síndico.
 */
class Receiver
{
    public function doSomething(string $a): void
    {
        echo 'Receiver: Trabajando en (' . $a . ')<br>';
    }

    public function doSomethingElse(string $b): void
    {
        echo 'Receiver: También trabajando en (' . $b . ')<br>';
    }
}

/**
 * El Invoker está asociado a uno o varios comandos.
 * Envía una solicitud al comando.
 */
class Invoker
{
    private Command $onStart;
    private Command $onFinish;

    /**
     * Inicializar comandos.
     */
    public function setOnStart(Command $command): void
    {
        $this->onStart = $command;
    }

    public function setOnFinish(Command $command): void
    {
        $this->onFinish = $command;
    }

    /**
     * El Invoker no depende de clases concretas de comando o receptor.
     * El invocador pasa una solicitud a un receptor indirectamente,
     * ejecutando un comando.
     */
    public function doSomenthingImportant(): void
    {
        echo 'Invoker: ¿Alguien quiere que se haga algo antes de empezar?<br>';
        if ($this->onStart instanceOf Command) {
            $this->onStart->execute();
        }

        echo 'Invoker: ...Haciendo algo realmente importante...<br>';

        echo 'Invoker: ¿Alguien quiere que se haga algo después de que termine?<br>';
        if ($this->onFinish instanceOf Command) {
            $this->onFinish->execute();
        }
    }
}

/**
 * El código del cliente puede parametrizar un invocador con cualquier comando.
 */
$invoker = new Invoker();
$invoker->setOnStart(new SimpleCommand('¡Di hola!'));
$receiver = new Receiver();
$invoker->setOnFinish(new ComplexCommand($receiver, 'Enviar email', 'Guardar report'));
$invoker->doSomenthingImportant();
