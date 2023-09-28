<?php

/**
 * Target define la interfaz específica del dominio utilizada por el código del cliente.
 */
class Target
{
    public function request(): string
    {
        return "Target: The default target's behavior.";
    }
}

/**
 * Adaptee contiene algunos comportamientos útiles, pero su interfaz es incompatible 
 * con el código de cliente existente. El Adaptee necesita alguna adaptación
 * antes de que el código del cliente pueda usarlo.
 */
class Adaptee
{
    public function specificRequest(): string
    {
        return ".eetpadA eht fo roivaheb laicepS";
    }
} 

/**
 * El Adaptador hace que la interfaz del Adaptee sea compatible con la interfaz del Target.
 */
class Adapter extends Target
{
    private $adaptee;

    public function __construct(Adaptee $adaptee)
    {
        $this->adaptee = $adaptee;
    }

    public function request(): string
    {
        return "Adapter: (TRANSLATED) " . strrev($this->adaptee->specificRequest());
    }
}

/**
 * El código del cliente admite todas las clases que siguen la interfaz Target.
 */
class Client
{
    private Target $target;

    public function __construct(Target $target)
    {
        $this->target = $target;
    }

    public function targetRequest(): void
    {
        echo $this->target->request() . '<br><br>';
    }
}

function clientCode(Target $target)
{
    echo $target->request();
}

echo "Cliente: Puedo trabajar bien con los objetos de destino:<br>";
$target = new Target();
$client = new Client($target);
$client->targetRequest();


echo "Cliente: La clase Adaptee tiene una interfaz extraña. Mira, no lo entiendo:<br>";
$adaptee = new Adaptee();
echo "Adaptee: " . $adaptee->specificRequest();
echo "<br><br>";

echo "Cliente: Pero puedo trabajar con él a través del Adaptador:<br>";
$adapter = new Adapter($adaptee);
$client = new Client($adapter);
$client->targetRequest();
