<?php

interface Toppineable
{
    public function addToppins(array $toppins): void;
}

class Pizza implements Toppineable
{
    private string $name;
    private array $toppins = [];
    private array $baseIngredients = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function addBaseIngredients(array $baseIngredients)
    {
        $this->baseIngredients += $baseIngredients;
    }

    public function addToppins(array $toppins): void
    {
        $this->toppins += $toppins;
    }

    public function __toString(): string
    {
        return sprintf(
            'Delicius pizza "%s" with %s, %s',
            $this->name,
            implode(', ', $this->baseIngredients),
            implode(', ', $this->toppins)
        );
    }    
} 

abstract class PizzaFactory
{
    abstract public function make(): Pizza;
}

class PizzaProsciuttoFactory extends PizzaFactory
{
    public function make(): Pizza
    {
        $pizza = new Pizza('Prosciutto');

        $pizza->addBaseIngredients([
            'tomatoes',
            'mozzarella',
        ]);

        $pizza->addToppins([
            'ham',
            'oliveOil',
        ]);

        return $pizza;
    }
}

class PizzaMargheritaFactory extends PizzaFactory
{
    public function make(): Pizza
    {
        $pizza = new Pizza('Margherita');

        $pizza->addBaseIngredients([
            'tomatoes',
            'mozzarella',
        ]);

        $pizza->addToppins([
            'basil',
            'oliveOil',
        ]);

        return $pizza;
    }
}

class Order
{
    private array $pizzas;

    public function __construct()
    {
        $this->prepare();
    }

    public function prepare()
    {
        $items = [
            'Margherita' => 2,
            'Prosciutto' => 3,
        ];

        foreach ($items as $type => $units) {
            $this->pizzas[] = $type === 'Margherita' 
                ? (new PizzaMargheritaFactory())->make()
                : (new PizzaProsciuttoFactory())->make();
        }
    }

    public function getPizzas(): array
    {
        return $this->pizzas;
    }
}

$pizzas = (new Order())->getPizzas();

foreach($pizzas as $pizza) {
    echo $pizza . '<br>';
}
