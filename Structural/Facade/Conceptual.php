<?php

class SubsystemFacade
{
    protected ?Subsystem1 $subsystem1;
    protected ?Subsystem2 $subsystem2;

    public function __construct(
        Subsystem1 $subsystem1 = null, 
        Subsystem2 $subsystem2 = null
    ) {
        $this->subsystem1 = $subsystem1 ?? new Subsystem1();
        $this->subsystem2 = $subsystem2 ?? new Subsystem2();
    }

    public function operation(): string
    {
        $result = "Facade initializes subsystems:<br>";
        $result .= $this->subsystem1->init();
        $result .= $this->subsystem2->init();
        $result .= "Facade orders subsystems to perform the action:<br>";
        $result .= $this->subsystem1->operation();
        $result .= $this->subsystem2->operation();

        return $result;
    }
}

class Subsystem1
{
    public function init(): string
    {
         return "Subsystem1: Ready!<br>";
    }

    public function operation(): string
    {
        return "Subsystem1: Go!<br>";
    }    
}

class Subsystem2
{
    public function init(): string
    {
         return "Subsystem2: Ready!<br>";
    }

    public function operation(): string
    {
        return "Subsystem2: Go!<br>";
    } 
}

class Client
{
    public function interactWithSubsystems()
    {
        // $subsystem1 = new Subsystem1();
        // $subsystem2 = new Subsystem2();
        // $facade = new SubsystemFacade($subsystem1, $subsystem2);

        $facade = new SubsystemFacade();

        echo $facade->operation();
    }
}

(new Client())->interactWithSubsystems();
