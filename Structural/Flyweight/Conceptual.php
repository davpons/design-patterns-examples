<?php

class Flyweight
{
    private $sharedState;

    public function __construct($sharedState)
    {
        $this->sharedState = $sharedState;
    }

    public function operation($uniqueState): void
    {
        echo sprintf(
            'Flyweight: Display shared (%s) and unique (%s) state.<br>',
            json_encode($this->sharedState),
            json_encode($uniqueState)
        );
    }
}

class FlyweightFactory
{
    /**
     * @var Flyweight[]
     */    
    private array $flyweights = [];

    public function __construct(array $initialFlyweights)
    {
        foreach ($initialFlyweights as $state) {
            $this->flyweights[$this->getKey($state)] = new Flyweight($state);
        }
    }

    /**
     * Returns a Flyweight's string hash for a given state.
     */
    private function getKey(array $state): string
    {
        ksort($state);

        return implode('_', $state);
    }

    public function getFlyweight(array $sharedState): Flyweight
    {
        $key = $this->getKey($sharedState);

        if (!isset($this->flyweights[$key])) {
            echo 'FlyweightFactory: Can\'t find a flyweight, creating new one.<br>';
            $this->flyweights[$key] = new Flyweight($sharedState);
        } else {
            echo 'FlyweightFactory: Reusing existing flyweight.<br>';
        }

        return $this->flyweights[$key];
    }

    public function listFlyweight(): void
    {
        $count = count($this->flyweights);
        echo 'FlyweightFactory: I have ' . $count . ' flyweights<br>';
        foreach ($this->flyweights as $key => $flyweight) {
            echo $key . '<br>';
        }
    }
}

class Client
{
    private FlyweightFactory $flyweightFactory;

    public function __construct()
    {
        $this->flyweightFactory = new FlyweightFactory([
            ["Chevrolet", "Camaro2018", "pink"],
            ["Mercedes Benz", "C300", "black"],
            ["Mercedes Benz", "C500", "red"],
            ["BMW", "M5", "red"],
            ["BMW", "X6", "white"],

            // ...
        ]);
    }

    public function listFlyweight(): void
    {
        $this->flyweightFactory->listFlyweight();
    }

    public function addCarToDatabase(
        $plates,
        $owner,
        $brand,
        $model,
        $color
    ): void {
        echo 'Client: Adding a car to database...<br>';
        $flyweight = $this->flyweightFactory->getFlyweight([
            $brand,
            $model,
            $color
        ]);
        $flyweight->operation([$plates, $owner]);
    }
}


$client = new Client();
$client->listFlyweight();

echo '<br>------<br><br>';

$client->addCarToDatabase(
    "CL234IR",
    "James Doe",
    "BMW",
    "M5",
    "red",
);

$client->listFlyweight();
