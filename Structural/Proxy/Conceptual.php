<?php

/**
 * La interfaz Asunto declara operaciones comunes tanto para RealSubject como para Proxy.
 * Siempre que el cliente trabaje con RealSubject usando esta interfaz, podrá pasarle 
 * un proxy en lugar de un sujeto real.
 */
interface Subject
{
    public function request(): void;
}

/**
 * RealSubject contiene cierta lógica empresarial central.
 * Por lo general, los RealSubjects son capaces de realizar algún trabajo útil
 * que también puede ser muy lento o sensible, por ejemplo corregir los datos de entrada.
 * Un Proxy puede resolver estos problemas sin realizar cambios en el código de RealSubject.
 */
class RealSubject implements Subject
{
    public function request(): void
    {
        echo 'RealSubject: Manejando la request.<br>';
    }
}

/**
 * El Proxy tiene una interfaz idéntica a la RealSubject.
 */
class Proxy implements Subject
{
    private RealSubject $realSubject;

    /**
    * El Proxy mantiene una referencia a un objeto de la clase RealSubject.
    * El cliente puede cargarlo de forma diferida o pasarlo al Proxy.
    */
    public function __construct(RealSubject $realSubject)
    {
        $this->realSubject = $realSubject;
    }

    /**
    * Las aplicaciones más comunes del patrón Proxy son carga diferida,
    * almacenamiento en caché, control de acceso, registro, etc.
    * Un Proxy puede realizar una de estas cosas y luego, dependiendo del resultado,
    * pasar la ejecución al mismo método en un objeto RealSubject vinculado.
    */
    public function request(): void
    {
        if ($this->checkAccess()) {
            $this->realSubject->request();
            $this->logAccess();
        }
    }

    private function checkAccess(): bool
    {
        echo 'Proxy: Comprobando el acceso antes de activar una request real.<br>';

        return true;
    }

    private function logAccess(): void
    {
        echo 'Proxy: Registrado tiempo de respuesta de la request.<br>';
    }
}

/**
 * Se supone que el código del cliente funciona con todos los objetos (tanto sujetos como proxies)
 * a través de la interfaz del Subject para admitir tanto sujetos reales como proxies.
 * En la vida real, sin embargo, la mayoría de los clientes trabajan directamente con sus 
 * sujetos reales. En este caso, para implementar el patrón más fácilmente,
 * puedes extender tu proxy desde la clase del sujeto real.
 */
class Client
{
    private RealSubject $realSubject;

    public function __construct()
    {
        $this->realSubject = new RealSubject();
    }

    public function originalRequest()
    {
        echo 'Client: Ejecutando lógica de cliente con un Subject real.<br>';
        $this->realSubject->request();
    }

    public function proxyRequest()
    {
        echo 'Client: Ejecutando lógica de cliente con un proxy.<br>';
        $subject = new Proxy($this->realSubject);
        $subject->request();
    }
}

$client = new Client();
$client->originalRequest();
$client->proxyRequest();
