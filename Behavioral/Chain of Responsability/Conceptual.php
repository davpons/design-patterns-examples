<?php

/**
 * La interfaz Handler declara un método para construir la cadena de controladores.
 * También declara un método para ejecutar una solicitud.
 */
interface Handler
{
    public function setNext(Handler $handler): Handler;
    public function handle(string $request): ?string;
}

/**
 * El comportamiento de encadenamiento predeterminado se puede implementar
 * dentro de una clase abstracta como manejador base.
 */
abstract class AbstractHandler implements Handler
{
    private ?Handler $nextHandler = null;

    public function setNext(Handler $handler): Handler
    {
        $this->nextHandler = $handler;

        /**
         * Devolver un controlador desde aquí nos permitirá vincular controladores
         * de una manera conveniente como esta:
         * $monkey->setNext($ardilla)->setNext($perro);
         */
        return $handler;
    }

    public function handle(string $request): ?string
    {
        if ($this->nextHandler) {
            return $this->nextHandler->handle($request);
        }

        return null;
    }
}

/**
 * Todos los manejadores concretos manejan una solicitud
 * o la pasan al siguiente manejador de la cadena.
 */
class MonkeyHandler extends AbstractHandler
{
    public function handle(string $request): ?string
    {
        if ($request === 'Banana') {
            return 'Monkey: I\'ll eat the ' . $request . '<br>';
        }

        return parent::handle($request);
    }
}

class SquirrelHandler extends AbstractHandler
{
    public function handle(string $request): ?string
    {
        if ($request === 'Nut') {
            return 'Squirrel: I\'ll eat the ' . $request . '<br>';
        }

        return parent::handle($request);
    }
}

class DogHandler extends AbstractHandler
{
    public function handle(string $request): ?string
    {
        if ($request === 'MeatBall') {
            return 'Dog: I\'ll eat the ' . $request . '<br>';
        }

        return parent::handle($request);
    }
}

/**
 * El código del cliente suele ser adecuado para trabajar con un único manejador.
 * En la mayoría de los casos, ni siquiera es consciente de que el manejador forma parte de una cadena.
 */
function clientCode(Handler $handler)
{
    foreach (["Nut", "Banana", "Cup of coffee"] as $food) {
        echo 'Client: Who wants a ' . $food . '?<br>';
        $result = $handler->handle($food);
        if ($result) {
            echo ' --> ' . $result;
        } else {
            echo ' --> ' . $food . ' was left untouched.<br>';
        }
    }
}

/**
 * La otra parte del código del cliente construye la cadena real.
 */
$monkey = new MonkeyHandler();
$squirrel = new SquirrelHandler();
$dog = new DogHandler();

$monkey->setNext($squirrel)->setNext($dog);

/**
 * El cliente debería poder enviar una solicitud
 * a cualquier controlador, no solo al primero de la cadena.
 */
echo 'Chain: Monkey > Squirrel > Dog<br><br>';
clientCode($monkey);
echo '<br>';

echo 'Subchain: Squirrel > Dog<br><br>';
clientCode($squirrel);
