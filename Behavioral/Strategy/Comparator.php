<?php

class Context
{
    public function __construct(
        private Comparator $comparator
    ) {}

    public function executeStrategy(array $elements): array
    {
        uasort($elements, [$this->comparator, 'compare']);
        return $elements;
    }
}

interface Comparator
{
    public function compare($a, $b): int;
}

class DateComparator implements Comparator
{
    public function compare($a, $b): int
    {
        $aDate = new \DateTime($a['date']);
        $bDate = new \DateTime($b['date']);

        return $aDate <=> $bDate;
    }
}

class IdComparator implements Comparator
{
    public function compare($a, $b): int
    {
        return $a['id'] <=> $b['id'];
    }
}

class Client
{
    public function provideIntegers(): array
    {
        return [
            ['id' => 2],
            ['id' => 1],
            ['id' => 3],
            ['id' => 1],
            ['id' => 3],
            ['id' => 2],
            ['id' => 1],
            ['id' => 1],
        ];
    }

    public function provideDates(): array
    {
        return [
            ['date' => '2014-03-03'],
            ['date' => '2015-03-02'],
            ['date' => '2013-03-01'],
            ['date' => '2013-03-01'],
            ['date' => '2014-02-03'],
            ['date' => '2013-02-01'],
            ['date' => '2015-02-02'],
            ['date' => '2013-02-01'],
        ];
    }

    public function compareIds()
    {
        $context = new Context(new IdComparator());
        $elements = $context->executeStrategy($this->provideIntegers());

        foreach ($elements as $element) {
            echo json_encode($element) . '<br>';
        }
    }

    public function compareDates()
    {
        $context = new Context(new DateComparator());
        $elements = $context->executeStrategy($this->provideDates());

        foreach ($elements as $element) {
            echo json_encode($element) . '<br>';
        }
    }
}

$client = new Client();
$client->compareIds();
$client->compareDates();
