<?php

interface Product
{
    public function operation(): string;
}

abstract class Creator
{
    abstract public function factoryMethod(): Product;

    public function someOperation(): string
    {
        $product = $this->factoryMethod();

        $result = "Creator: The same creator's code has just worked with " .
            $product->operation();

        return $result;
    }
}

class ConcreteCreator1 extends Creator
{
    public function factoryMethod(): Product
    {
        return new ConcreteProduct1();
    }
}

class ConcreteCreator2 extends Creator
{
    public function factoryMethod(): Product
    {
        return new ConcreteProduct2();
    }
}

class ConcreteProduct1 implements Product
{
    public function operation(): string
    {
        return "{Result of the ConcreteProduct 1}";
    }
}

class ConcreteProduct2 implements Product
{
    public function operation(): string
    {
        return "{Result of the ConcreteProduct 2}";
    }
}

class Client
{
    public function doStuff(Creator $creator)
    {
        echo "Client: I'm not aware of the creator's class, but it still works.<br>"
            . $creator->someOperation() . "<br><br>";
    }
}

$client = new Client();
$client->doStuff(new ConcreteCreator1());
$client->doStuff(new ConcreteCreator2());
