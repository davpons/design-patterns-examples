<?php

abstract class EnemyFactory
{
    private array $enemies = [
        Goomba::class,
        Koopa::class,
        Boo::class,
    ];

    abstract public function createEnemy(): EnemyEntity;

    protected function getEnemies(): array
    {
        return $this->enemies;
    }

    protected function getRandomEnemy(array $enemies = null): EnemyEntity
    {
        $enemies = $enemies ?? $this->enemies;
        $randomEnemy = $enemies[array_rand($enemies)];

        return new $randomEnemy();
    }
}

interface EnemyEntity
{
    public function attack(): void;
    public function getDifficulty(): int;
}

class RandomEnemyFactory extends EnemyFactory
{
    public function createEnemy(): EnemyEntity
    {
        return parent::getRandomEnemy();
    }
}

class RandomDifficultEnemyFactory extends EnemyFactory
{
    public function createEnemy(): EnemyEntity
    {
        $difficultEnemies = array_filter($this->getEnemies(), function ($item) {
            return (new $item())->getDifficulty() > 1;
        });  

        return parent::getRandomEnemy($difficultEnemies);
    }
}

class OnlyGoombaEnemyFactory extends EnemyFactory
{
    public function createEnemy(): EnemyEntity
    {
        return new Goomba();
    }
}

class Goomba implements EnemyEntity
{
    public function getDifficulty(): int
    {
        return 3;
    }

    public function attack(): void
    {
        echo 'Ataqueee fuerte de Goomba!';
    }
}

class Koopa implements EnemyEntity
{
    public function getDifficulty(): int
    {
        return 2;
    }

    public function attack(): void
    {
        echo 'Ieeee soy Koopa y te atacoooo!';
    }
}

class Boo implements EnemyEntity
{
    public function getDifficulty(): int
    {
        return 1;
    }

    public function attack(): void
    {
        echo 'Boo boo atacaaa!';
    }
}

class Game
{
    private EnemyFactory $enemyFactory;

    public function __construct(EnemyFactory $enemyFactory)
    {
        $this->enemyFactory = $enemyFactory;
    }

    function play()
    {
        $enemy = $this->enemyFactory->createEnemy();
        $enemy->attack();
    }
}

$game = new Game(new RandomDifficultEnemyFactory());
$game->play();
