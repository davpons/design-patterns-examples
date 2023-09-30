<?php

abstract class DeliveryMan
{
    public abstract function getVehicle(): Vehicle;

    public function deliver(): void
    {
        echo 'Delivering (deliveryman)...<br>';
        $vehicle = $this->getVehicle();
        $vehicle->ride();
    }
}

abstract class Vehicle
{
    public abstract function ride(): void;
}

class Bicycle extends Vehicle
{
    public function ride(): void
    {
        echo 'Riding a bicycle... delivery without polluting the environment.<br><br>';
    }
}

class Motorcycle extends Vehicle
{
    public function ride(): void
    {
        echo 'Riding a motorcycle... I go faster than the wind.<br><br>';
    }
}

class DeliveryManOnBicycle extends DeliveryMan
{
     public function getVehicle(): Vehicle
     {
         return new Bicycle();
     }
}

class DeliveryManOnMotorcycle extends DeliveryMan
{
     public function getVehicle(): Vehicle
     {
         return new Motorcycle();
     }
}

class Client
{
    public function fastDelivery(): void
    {
        $deliveryMan = new DeliveryManOnMotorcycle();
        $deliveryMan->deliver();
    }

    public function ecoDelivery(): void
    {
        $deliveryMan = new DeliveryManOnBicycle();
        $deliveryMan->deliver();
    }
}

$client = new Client();
$client->ecoDelivery();
$client->fastDelivery();
