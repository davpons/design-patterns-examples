<?php

/**
 * Los iteradores concretos implementan varios algoritmos transversales.
 * Estas clases almacenan la posición transversal actual en todo momento.
 */
Class AlphabeticalOrderIterator implements \Iterator
{
    private WordsCollection $collection;

    /**
     * Almacena la posición transversal actual.
     * Un iterador puede tener muchos otros campos para almacenar el estado de la iteración,
     * especialmente cuando se supone que debe trabajar con un tipo particular de colección.
     */
    private int $position = 0;

    /**
     * Esta variable indica la dirección transversal.
     */
    private bool $reverse = false;

    public function __construct(WordsCollection $collection, bool $reverse = false)
    {
        $this->collection = $collection;
        $this->reverse = $reverse;
    }

    public function rewind(): void
    {
        $this->position = $this->reverse ? count($this->collection->getItems()) -1 : 0;
    }

    public function current(): string
    {
        return $this->collection->getItems()[$this->position];
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        $this->position = $this->position + ($this->reverse ? -1 : 1);
    }

    public function valid(): bool
    {
        return isset($this->collection->getItems()[$this->position]);
    }
}

/**
 * Las colecciones concretas proporcionan uno o varios métodos para recuperar instancias
 * de iterador nuevas, compatibles con la clase de colección.
 */
class WordsCollection implements \IteratorAggregate
{
    private array $items = [];

    public function getItems(): array
    {
        return $this->items;
    }

    public function addItem(string $item): void
    {
        $this->items[] = $item;
    }

    public function getIterator(): \Iterator
    {
        return new AlphabeticalOrderIterator($this);
    }

    public function getReverseIterator(): \Iterator
    {
        return new AlphabeticalOrderIterator($this, true);
    }
}

/**
 * El código del cliente puede conocer o no las clases Concrete Iterator o Collection,
 * según el nivel de direccionamiento indirecto que desees mantener en tu programa.
 */
$collection = new WordsCollection();
$collection->addItem('Alicante');
$collection->addItem('Barcelona');
$collection->addItem('Valencia');

echo 'Recorrido normal:<br>';
foreach ($collection->getIterator() as $item) {
    echo $item . '<br>';
}

echo '<br>Recorrido inverso:<br>';
foreach ($collection->getReverseIterator() as $item) {
    echo $item . '<br>';
}
