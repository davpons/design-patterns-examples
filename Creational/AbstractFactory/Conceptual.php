<?php

interface AbstractProductA
{
    public function usefulFunctionA(): string;
}

class ConcreteProductA1 implements AbstractProductA
{
    public function usefulFunctionA(): string
    {
        return 'The result of the product A1';
    }
}

class ConcreteProductA2 implements AbstractProductA
{
    public function usefulFunctionA(): string
    {
        return 'The result of the product A2';
    }    
}

interface AbstractProductB
{
    public function usefulFunctionB(): string;
    public function anotherUsefulFunctionB(AbstractProductA $collaborator): string;
}

class ConcreteProductB1 implements AbstractProductB
{
    public function usefulFunctionB(): string
    {
        return 'The result of the product B1';
    }

    public function anotherUsefulFunctionB(AbstractProductA $collaborator): string
    {
        $result = $collaborator->usefulFunctionA();

        return "The result of the B1 collaborating with ({$result})";
    }
}

class ConcreteProductB2 implements AbstractProductB
{
    public function usefulFunctionB(): string
    {
        return 'The result of the product B2';
    }

    public function anotherUsefulFunctionB(AbstractProductA $collaborator): string
    {
        $result = $collaborator->usefulFunctionA();

        return "The result of the B2 collaborating with ({$result})";
    }
}

interface AbstractFactory
{
    public function createProductA(): AbstractProductA;
    public function createProductB(): AbstractProductB;
}

class ConcreteFactory1 implements AbstractFactory
{
    public function createProductA(): AbstractProductA
    {
        return new ConcreteProductA1();
    }

    public function createProductB(): AbstractProductB
    {
        return new ConcreteProductB1();
    }
}

class ConcreteFactory2 implements AbstractFactory
{
    public function createProductA(): AbstractProductA
    {
        return new ConcreteProductA2();
    }

    public function createProductB(): AbstractProductB
    {
        return new ConcreteProductB2();
    }
}

class Product
{
    public function getA(AbstractFactory $factory)
    {
        return $factory->createProductA();
    }

    public function getB(AbstractFactory $factory)
    {
        return $factory->createProductB();
    }
}

$product = new Product();

$a1 = $product->getA(new ConcreteFactory1);
$a2 = $product->getA(new ConcreteFactory2);
echo "Product A1 --> " . $a1->usefulFunctionA() . "<br>";
echo "Product A2 --> " . $a2->usefulFunctionA() . "<br>"; 

$b1 = $product->getB(new ConcreteFactory1);
$b2 = $product->getB(new ConcreteFactory2);
echo "Product B1 --> " . $b1->usefulFunctionB() . "<br>";
echo "Product B2 --> " . $b2->usefulFunctionB() . "<br>";
echo "Product B1 collaborating with A1 --> " . $b1->anotherUsefulFunctionB($a1) . "<br>";
echo "Product B1 collaborating with A2 --> " . $b1->anotherUsefulFunctionB($a2) . "<br>";
echo "Product B2 collaborating with A1 --> " . $b2->anotherUsefulFunctionB($a1) . "<br>";
echo "Product B2 collaborating with A2 --> " . $b2->anotherUsefulFunctionB($a2) . "<br>";
