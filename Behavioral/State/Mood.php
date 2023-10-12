<?php

class Person
{
    private Mood $mood;

    public function __construct()
    {
        $this->mood = new Happy();
        echo 'Persona creada con estado Happy por defecto.<br>';
    }

    public function eat(): void
    {
        $this->mood->eat();
        echo 'Después de comer mantenemos el mismo estado que teniamos.<br>';
    }

    public function dance(): void
    {
        $this->mood->dance();
        echo 'Después de bailar establecemos estado en Happy.<br>';
        $this->mood = new Happy();
    }

    public function work(): void
    {
        $this->mood->work();
        echo 'Después de trabajar el estado es Sad.<br>';
        $this->mood = new Sad();
    }
}

abstract class Mood
{
    protected self $nextMood;

    abstract public function eat(): void;
    abstract public function dance(): void;
    abstract public function work(): void;
}

class Happy extends Mood
{
    public function eat(): void
    {
        echo 'Comiendo en estado Happy.<br>';
    }

    public function dance(): void
    {
        echo 'Bailando en estado Happy.<br>';
    }

    public function work(): void
    {
        echo 'Trabajando en estado Happy.<br>';
    }
}

class Sad extends Mood
{
    public function eat(): void
    {
        echo 'Comiendo en estado Sad.<br>';
    }

    public function dance(): void
    {
        echo 'Bailando en estado Sad.<br>';
    }

    public function work(): void
    {
        echo 'Trabajando en estado Sad.<br>';
    }
}

$person = new Person();
$person->work();
$person->eat();
$person->dance();
