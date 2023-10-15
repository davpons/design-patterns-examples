<?php

abstract class AbstractSubject {
    abstract public function attach(AbstractObserver $observerIn): void;
    abstract public function detach(AbstractObserver $observerIn): void;
    abstract public function notify(): void;
}

abstract class AbstractObserver {
    abstract function update(AbstractSubject $subjectIn): void;
}

class PatternSubject extends AbstractSubject
{
    private ?string $favorites = null;
    private array $observers = [];

    public function attach(AbstractObserver $observerIn): void
    {
        $this->observers[] = $observerIn;
    }

    public function detach(AbstractObserver $observerIn): void
    {
        $oKey = array_search($observerIn, $this->observers);
        if (false !== $oKey) {
            unset($this->observers[$oKey]);
        }
    }

    public function notify(): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    public function updateFavorites(string $newFavorites): void
    {
        $this->favorites = $newFavorites;
        $this->notify();
    }

    public function getFavorites(): string
    {
        return $this->favorites;
    }
}

class PatternObserver extends AbstractObserver
{
    public function update(AbstractSubject $subject): void
    {
        echo '**OBSERVADOR - ALERTA DE CHISMES NUEVOS PATRONES FAVORITOS**<br>';
        echo 'Nuevos patrones favoritos: ' . $subject->getFavorites() . '<br>';
    }
}

$patternGossiper = new PatternSubject();
$patternGossipFan = new PatternObserver();

$patternGossiper->attach($patternGossipFan);
$patternGossiper->updateFavorites('abstract factory, decorator, visitor');
$patternGossiper->updateFavorites('abstract factory, observer, decorator');

$patternGossiper->detach($patternGossipFan);
$patternGossiper->updateFavorites('abstract factory, observer, visitor');
