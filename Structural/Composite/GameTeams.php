<?php

abstract class Player
{
    protected string $name;

    public function getName(): string
    {
        return $this->name;
    }

    public abstract function play(): string;
}

class Game
{
    public function __construct()
    {
        echo '<br>Creando el juego...<br>';
    }

    public function play(Player $player1, Player $player2): void
    {
        echo 'Jugando ronda...<br>';

        echo 'Jugando player1: ' . $player1->getName() . '<br>';
        echo $player1->play();

        echo 'Jugando player2: ' . $player2->getName() . '<br>';
        echo $player2->play();
    }
}

class Person extends Player
{
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function play(): string
    {
        return $this->name . ': jugando como persona<br>';
    }
}

class Team extends Player
{
    private \SplObjectStorage $teamMembers;
    private Person $teamRepresentative;

    public function __construct(string $name)
    {
        echo "Creando equipo: $name <br>";

        $this->name = $name;
        $this->teamMembers = new \SplObjectStorage;
    }

    public function addPerson(Person $person, bool $isRepresentative = true): void
    {
        echo "Añadiendo persona: {$person->getName()} al equipo {$this->name}<br>";
        $this->teamMembers->attach($person);
        if ($isRepresentative) {
            $this->teamRepresentative = $person;
        }
    }

    public function removePerson(Person $person): void
    {
        echo "Eliminando persona {$person->getName()} del equipo {$this->name}<br>";
        $this->teamMembers->detach($person);
        if ($person === $this->$teamRepresentative) {
            $this->$teamMembers->rewind();
            $this->$teamRepresentative = $this->$teamMembers->current();
        }
    }

    public function getTeamMembers(): array
    {
        echo "Obteniendo los miembros del equipo {$this->name}...<br>";
        $members = [];
        foreach ($this->teamMembers as $teamMember) {
            $members[] = $teamMember;
        }

        return $members;
    }

    public function setRepresentative(Person $person): void
    {
        $this->teamRepresentative = $person;
    }

    public function play(): string
    {
        foreach ($this->teamMembers as $person) {
            $person->play();
            echo "{$person->getName()} pensando la jugada internamente como miembro del equipo<br>";
        }

        return "{$this->teamRepresentative->getName()} ejecuta la jugada como representante del equipo.<br>";
    }
}

class ClientPlayGame
{
    private Team $team1;
    private Person $person1InTeam1;
    private Person $person2InTeam1;
    private Person $personAlone1;
    private Person $personAlone2;

    public function __construct()
    {
       $this->team1 = new Team('Equipo A');
       $this->person1InTeam1 = new Person('Persona 1A');
       $this->team1->addPerson($this->person1InTeam1);
       $this->person2InTeam1 = new Person('Persona 2A');
       $this->team1->addPerson($this->person2InTeam1);

       $this->personAlone1 = new Person('Persona sola 1');
       $this->personAlone2 = new Person('Persona sola 2');
    }

    public function start()
    {
        $game = new Game();

        echo '<br>Jugando con un equipo contra una persona...<br>';
        $game->play($this->team1, $this->personAlone1);

        echo '<br>Jugando con una persona contra un equipo...<br>';
        $game->play($this->personAlone1, $this->team1);

        echo '<br>Jugando con una persona contra otra persona...<br>';
        $game->play($this->personAlone1, $this->personAlone2);

        echo '<br>Cambiando al representante del equipo 1...<br>';
        $this->team1->setRepresentative($this->person1InTeam1);

        echo '<br>Jugando de nuevo con un equipo contra una persona...<br>';
        $game->play($this->team1, $this->personAlone2);        
    }
}

$playGame = new ClientPlayGame();
$playGame->start();
