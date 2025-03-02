<?php

// Interfaz base (Componente abstracto)
interface Player {
    public function attack();
    public function getDamage();
}

// Hoja: Jugador individual
class SinglePlayer implements Player {
    private string $name;
    private int $damage;

    public function __construct($name, $damage) {
        $this->name = $name;
        $this->damage = $damage;
    }

    public function attack() {
        return "{$this->name} ataca causando {$this->damage} de daño.<br>";
    }

    public function getDamage() {
        return $this->damage;
    }
}

// Composite: Equipo o grupo de unidades
class CompositePlayer implements Player {
    private string $name;
    private array $players = []; // Lista para jugadores, equipos o grupos

    public function __construct($name) {
        $this->name = $name;
    }

    public function addPlayer(Player $player) {
        $this->players[] = $player;
    }

    public function attack() {
        $results = ["Grupo {$this->name} ordena ataque:<br>"];
        foreach ($this->players as $player) {
            $results[] = $player->attack();
        }
        return implode("-- ", $results);
    }

    public function getDamage() {
        $total = 0;
        foreach ($this->players as $player) {
            $total += $player->getDamage();
        }
        return $total;
    }
}

// Ejemplo de uso
$guerrero = new SinglePlayer("Guerrero", 10);
$mago = new SinglePlayer("Mago", 15);
$arquero = new SinglePlayer("Arquero", 8);

// Equipo pequeño
$equipo1 = new CompositePlayer("Equipo Alpha");
$equipo1->addPlayer($guerrero);
$equipo1->addPlayer($mago);

// Equipo con más jugadores
$equipo2 = new CompositePlayer("Equipo Beta");
$equipo2->addPlayer($arquero);

// Grupo grande con equipos
$alianza = new CompositePlayer("Alianza del Norte");
$alianza->addPlayer($equipo1);
$alianza->addPlayer($equipo2);

// Probando atacar y daño
echo "Acciones de ataque:<br><br>";
echo $guerrero->attack() . "<br>";
echo $equipo1->attack() . "<br>";
echo $alianza->attack() . "<br>";

echo "<strong>Daño total:</strong><br>";
echo "Guerrero: " . $guerrero->getDamage() . "<br>";
echo "Equipo Alpha: " . $equipo1->getDamage() . "<br>";
echo "Alianza: " . $alianza->getDamage() . "<br>";
