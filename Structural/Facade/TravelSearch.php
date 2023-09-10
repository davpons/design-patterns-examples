<?php

class SimpleTravelSearcher
{
    private HotelsApiSubsystem $hotels;
    private FlightsApiSubsystem $flights;

    public function __construct()
    {
        $this->hotels = new HotelsApiSubsystem();
        $this->flights = new FlightsApiSubsystem();
    }

    public function search(
        string $departureDate,
        string $returnDate,
        string $origin,
        string $destination
    ) {
        $this->flights->search(
            $departureDate,
            $returnDate,
            $origin,
            $destination
        );

        echo '<br>---------<br>';

        $this->hotels->search($departureDate, $returnDate, $destination);
    }
}

class FlightsApiSubsystem
{
    public function search(
        string $departureDate,
        string $returnDate,
        string $origin,
        string $destination
    ) {
        echo sprintf(
            '2 vuelos encontrados para "%s" desde "%s" con salida %s',
            $destination,
            $origin,
            $departureDate
        );
    }
}

class HotelsApiSubsystem
{
    public function search(
        string $entryDate,
        string $departureDate,
        string $location
    ) {
        echo sprintf(
            '5 hoteles encontrados en "%s" con entrada %s y salida %s:',
            $location,
            $entryDate,
            $departureDate
        );
    }
}

class TravelClient
{
    public function checkAvailability(
        string $departureDate,
        string $returnDate,
        string $origin,
        string $destination
    ) {
        $searcher = new SimpleTravelSearcher();
        $searcher->search(
            $departureDate,
            $returnDate,
            $origin,
            $destination
        );
    }
}

$travel = new TravelClient();
$travel->checkAvailability(
  "2023-09-10",
  "2023-09-20",
  "Valencia",
  "Edinburgh"
);

