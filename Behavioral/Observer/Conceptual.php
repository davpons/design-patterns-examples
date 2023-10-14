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
  * El Sujeto observado posee algún estado importante y notifica
  * a los observadores cuando el estado cambia.
  */
class Subject implements \SplSubject
{
    /**
     * Por simplicidad, el estado del Sujeto, esencial para todos los suscriptores,
     * se almacena en esta variable.
     */
    public int $state;

    /**
     * Lista de suscriptores. En la vida real, la lista de suscriptores se puede
     * almacenar de forma más completa (clasificada por tipo de evento, etc.).
     */
    private \SplObjectStorage $observers;

    public function __construct()
    {
        $this->observers = new \SplObjectStorage;
    }

    /**
     * Los métodos de gestión de suscripciones.
     */
    public function attach(\SplObserver $observer): void
    {
        echo 'Subject: Attach an observer.<br>';
        $this->observers->attach($observer);
    }

    public function detach(\SplObserver $observer): void
    {
        $this->observers->detach($observer);
        echo 'Subject: Detached an observer.<br>';
    }

    public function notify(): void
    {
        echo 'Subject: Notifying observers...<br>';
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    /**
     * Normalmente, la lógica de suscripción es sólo una fracción
     * de lo que un Sujeto observado realmente puede hacer.
     * Los sujetos suelen tener alguna lógica empresarial importante, 
     * que activa un método de notificación cada vez que algo importante 
     * está a punto de suceder (o después).
     */
    public function someBusinessLogic(): void
    {
        echo '<br>Subject: I\'m doing something important.<br>';
        $this->state = rand(0, 10);

        echo 'Subject: My state has just changed to: ' . $this->state . '<br>';
        $this->notify();
    }
}

/**
 * Los Observadores Concretos reaccionan a las actualizaciones emitidas 
 * por el Sujeto al que estaban suscritos.
 */
class concreteObserverA implements \SplObserver
{
    public function update(\SplSubject $subject): void
    {
        if ($subject->state < 3) {
            echo 'ConcreteObserverA: Reacted to event.<br>';
        }
    }
}

class concreteObserverB implements \SplObserver
{
    public function update(\SplSubject $subject): void
    {
        if ($subject->state === 0 || $subject->state >= 2) {
            echo 'ConcreteObserverB: Reacted to event.<br>';
        }
    }
}

/**
 * Código cliente.
 */
$subjectObserved = new Subject();
$o1 = new ConcreteObserverA();
$o2 = new ConcreteObserverB();

$subjectObserved->attach($o1);
$subjectObserved->attach($o2);

$subjectObserved->someBusinessLogic();
$subjectObserved->someBusinessLogic();

$subjectObserved->detach($o2);

$subjectObserved->someBusinessLogic();
