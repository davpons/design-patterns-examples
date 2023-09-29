<?php

interface Memento
{
    public function getName(): string;
    public function getDate(): string;
}

/**
 * El Concrete Memento contiene la infraestructura
 * para almacenar el estado del Originador.
 */
class ConcreteMemento implements Memento
{
    private string $state;
    private string $date;

    public function __construct(string $state)
    {
        $this->state = $state;
        $this->date = date('Y-m-d H:i:s');
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getName(): string
    {
        return $this->date . ' / (' . substr($this->state, 0, 9) . '...)';
    }

    public function getDate(): string
    {
        return $this->date;
    }
}

/**
 * El Originador mantiene algún estado importante que puede cambiar con el tiempo. 
 * También define un método para guardar el estado dentro de un recuerdo y otro método 
 * para restaurar el estado a partir de él.
 */
class Originator
{
    // En aras de la simplicidad, el estado del creador se almacena dentro de una única variable.
    private string $state;

    public function __construct(string $state)
    {
        $this->state = $state;
        echo "Originator: Mi estado inicial es: {$this->state}<br>";
    }

    // La lógica empresarial del Originador puede afectar su estado interno. 
    // Por lo tanto, el cliente debe hacer una copia de seguridad del estado 
    // antes de iniciar métodos de la lógica empresarial mediante el método save().
    public function doSomething(): void
    {
        echo "Originator: Estoy haciendo algo importante...<br>";
        $this->state = $this->generateRandomString(30);
        echo "Originator: ...y mi estado ha mutado a: {$this->state}<br>";
    }    

    private function generateRandomString(int $length = 10): string
    {
        return substr(
            str_shuffle(
                str_repeat(
                    $x = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
                    ceil($length / strlen($x))
                )
            ), 1, $length
        );
    }

    // Guarda el estado actual dentro de un recuerdo (memento).
    public function save(): Memento
    {
        return new ConcreteMemento($this->state);
    }

    public function restore(Memento $memento): void
    {
        $this->state = $memento->getState();
        echo "Originator: Mi estado ha cambiado a: {$this->state}<br>";
    }
}

/**
 * El Cuidador no depende de la clase Concrete Memento.
 * Por lo tanto, no tiene acceso al estado del autor, almacenado dentro del recuerdo.
 * Funciona con todos los recuerdos a través de la interfaz básica de Memento.
 */
class Caretaker
{
    /**
     * @var Memento[]
     */
    private array $mementos = [];
    private Originator $originator;

    public function __construct(Originator $originator)
    {
        $this->originator = $originator;
    }

    public function backup(): void
    {
        echo "<br>Caretaker: Guardando el estado del originador...<br>";
        $this->mementos[] = $this->originator->save();
    }

    public function undo(): void
    {
        if (count($this->mementos) === 0) {
            return;
        }
        $memento = array_pop($this->mementos);

        echo "Caretaker: Restaurando el estado a: {$memento->getName()}<br>";
        try {
            $this->originator->restore($memento);
        } catch (\Exception $ex) {
            $this->undo();
        }
    }

    public function showHistory(): void
    {
        echo "Caretaker: Esta es la lista de recuerdos (mementos):<br>";
        foreach ($this->mementos as $memento) {
            echo $memento->getName() . '<br>';
        }
    }
}

$originator = new Originator('ola k ase...');
$caretaker = new Caretaker($originator);

$caretaker->backup();
$originator->doSomething();

$caretaker->backup();
$originator->doSomething();

$caretaker->backup();
$originator->doSomething();

echo '<br>----- Historial ------<br>';
$caretaker->showHistory();

echo "<br>Hacemos un rollback!<br>";
$caretaker->undo();

echo "<br>Hacemos un segundo rollback!<br>";
$caretaker->undo();

echo '<br>----- Historial ahora ------<br>';
$caretaker->showHistory();
