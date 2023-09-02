<?php

class Surgeon // Cirujano
{
    public function operate(Instrumentalist $instrumentalist): void
    {
        $cuttingTool = $instrumentalist->createCuttingTool();
        $dryingTool = $instrumentalist->createDryingTool();

        echo "Operate with created tools (surgeon)<br>";
        $cuttingTool->cut();
        echo " and ";
        $dryingTool->dry();
    }
}

interface Instrumentalist // Contrato Instrumentalista 
{
    public function createCuttingTool(): CuttingTool;
	public function createDryingTool(): DryingTool;
}

class InstrumentalistInOperatingRoom implements Instrumentalist // Instrumentalista de quirófano
{
    public function createCuttingTool(): CuttingTool
    {
        return new Scalpel();
    }

	public function createDryingTool(): DryingTool
    {
        return new Compress();
    }
}

class InstrumentalistInJungle implements Instrumentalist // Instrumentalista en la jungla
{
    public function createCuttingTool(): CuttingTool
    {
        return new Knife();
    }

	public function createDryingTool(): DryingTool
    {
        return new Rag();
    }
}

abstract class CuttingTool // Herramienta cortante
{
    abstract public function cut(): void;
}

class Knife extends CuttingTool // Cuchillo
{
    public function cut(): void
    {
        echo "performing a cut with knife";
    }
}

class Scalpel extends CuttingTool // Escalpelo
{
    public function cut(): void
    {
        echo "performing a cut with scalpel";
    }
}

abstract class DryingTool // Herramienta secante
{
    abstract public function dry(): void;
}

class Compress extends DryingTool // Compresa
{
    public function dry(): void
    {
        echo "drying with compress";
    }
}

class Rag extends DryingTool // Trapo
{
    public function dry(): void
    {
        echo "drying with rag";
    }
}

$surgeon = new Surgeon();

$surgeon->operate(new InstrumentalistInOperatingRoom);
echo "<br><br>";
$surgeon->operate(new InstrumentalistInJungle);
