<?php

interface EnemyFactory
{
    public function createNormalEnemy(): Enemy;
    public function createBossEnemy(): Enemy;
}

abstract class Enemy
{
    abstract public function attack(): void;
}

class NormalEnemy extends Enemy
{
    public function attack(): void
    {
        echo 'El enemigo "normal" ataca.';
    }
}

class BossEnemy extends Enemy
{
    public function attack(): void
    {
        echo 'El enemigo "boss" ataca.';
    }
}

class GameEnemyFactory implements EnemyFactory
{
    public function createNormalEnemy(): Enemy
    {
        return new NormalEnemy();
    }

    public function createBossEnemy(): Enemy
    {
        return new BossEnemy();
    }
}

class Game
{
    public function createEnemies(EnemyFactory $factory): void
    {
        $factory->createNormalEnemy()->attack();
        echo '<br>';
        $factory->createBossEnemy()->attack();
    }
}

(new Game())->createEnemies(new GameEnemyFactory());
