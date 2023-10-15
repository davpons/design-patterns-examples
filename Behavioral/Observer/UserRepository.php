<?php

/**
 * PHP dispone de 2 interfaces integradas relacionadas con el patrón Observer.
 *
 * Interfaz integrada para sujetos observados:
 *
 * @link http://php.net/manual/en/class.splsubject.php
 *
 *     interface SplSubject
 *     {
 *         // Adjunte un observador al sujeto.
 *         public function attach(SplObserver $observer);
 *
 *         // Separe al observador del sujeto.
 *         public function detach(SplObserver $observer);
 *
 *         // Notificar a todos los observadores sobre un evento.
 *         public function notify();
 *     }
 *
 * Interfaz integrada para observadores:
 *
 * @link http://php.net/manual/en/class.splobserver.php
 *
 *     interface SplObserver
 *     {
 *         public function update(SplSubject $subject);
 *     }
 */

/**
 * El UserRepository representa un Subject.
 * Varios objetos están interesados en rastrear su estado interno,
 * ya sea agregando un nuevo usuario o eliminando uno.
 */
class UserRepository implements \SplSubject
{
    private array $users = [];

    /**
     * Aquí va la infraestructura de gestión real de Observer.
     * Ten en cuenta que no es todo de lo que nuestra clase es responsable.
     * Su lógica empresarial principal se enumera debajo de estos métodos.
     */

    private array $observers = [];

    public function __construct()
    {
        /**
         * Un grupo de eventos especial para observadores
         * que quieran escuchar todos los eventos.
         */
        $this->observers['*'] = [];
    }

    private function initEventGroup(string $event = '*'): void
    {
        if (!isset($this->observers[$event])) {
            $this->observers[$event] = [];
        }
    }

    private function getEventObservers(string $event = '*'): array
    {
        $this->initEventGroup($event);
        $group = $this->observers[$event];
        $all = $this->observers['*'];

        return array_merge($group, $all);
    }

    public function attach(\SplObserver $observer, string $event = '*'): void
    {
        $this->initEventGroup($event);
        $this->observers[$event][] = $observer;
    }

    public function detach(\SplObserver $observer, string $event = '*'): void
    {
        foreach ($this->getEventObservers($event) as $key => $s) {
            if ($s === $observer) {
                unset($this->observers[$event][$key]);
            }
        }
    }

    public function notify(string $event = '*', $data = null): void
    {
        echo 'UserRepository: Transmitiendo (Broadcasting) evento ' . $event . '<br>';
        foreach ($this->getEventObservers($event) as $observer) {
            $observer->update($this, $event, $data);
        }
    }

    /**
     * Estos son métodos que representan la lógica de negocio de la clase.
     */
    public function initialize($filename): void
    {
        echo 'UserRepository: Cargando registros de usuario desde un archivo.<br>';

        $this->notify('users:init', $filename);
    }

    public function createUser(array $data): User
    {
        echo 'UserRepository: Creando usuario.<br>';

        $user = new User();
        $user->update($data);

        $id = uniqid();
        $user->update(['id' => $id]);
        $this->users[$id] = $user;

        $this->notify('users:created', $user);

        return $user;
    }

    public function updateUser(User $user, array $data): ?User
    {
        echo 'UserRepository: Actualizando usuario.<br>';

        $id = $user->attributes['id'];
        if (!isset($this->users[$id])) {
            return null;
        }

        $user = $this->users[$id];
        $user->update($data);

        $this->notify('users:updated', $user);

        return $user;
    }

    public function deleteUser(User $user): void
    {
        echo 'UserRepository: Eliminando usuario.<br>';

        $id = $user->attributes['id'];
        if (!isset($this->users[$id])) {
            return;
        }

        unset($this->users[$id]);

        $this->notify('users:deleted', $user);
    }
}

/**
 * Mantenemos la clase User trivial ya que no es el foco del ejemplo.
 */
class User
{
    public array $attributes = [];

    public function update(array $data): void
    {
        $this->attributes = array_merge($this->attributes, $data);
    }
}
/**
 * Este componente concreto registra cualquier evento al que esté suscrito.
 */
class Logger implements \SplObserver
{
    public function __construct(private string $filename)
    {
        if (file_exists($this->filename)) {
            unlink($this->filename);
        }
    }

    public function update(
        \SplSubject $repository,
        string $event = null,
        mixed $data = null
    ): void {
        $entry = date('Y-m-d H:i:s') . ': ' .
            $event . ' with data: ' . json_encode($data) . '<br>';

        file_put_contents($this->filename, $entry, FILE_APPEND);

        echo "Logger: Registrada entrada del evento '$event'.<br>";
    }
}

/**
 * Este Componente de Concreto envía instrucciones iniciales a nuevos usuarios.
 * El cliente es responsable de adjuntar este componente a un evento de
 * creación de usuario adecuado.
 */
class OnboardingNotification implements \SplObserver
{
    public function __construct(
        private string $adminEmail
    ) {}

    public function update(
        \SplSubject $repository,
        string $event = null,
        $data = null
    ): void {
        echo 'OnboardingNotification: Notificación enviada a: ' . $this->adminEmail . '<br>';
    }
}

/**
 * Código cliente.
 */

$repository = new UserRepository();
$repository->attach(new Logger(__DIR__ . '/log.txt'), '*');
$repository->attach(new OnboardingNotification('admin@mail.com'), 'users:created');

$repository->initialize(__DIR__ . '/users.csv');

$user = $repository->createUser([
    'name' => 'Pepito La Flor',
    'email' => 'pepitolaflor@mail.com',
]);

$repository->deleteUser($user);
