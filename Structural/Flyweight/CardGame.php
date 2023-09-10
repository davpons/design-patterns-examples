<?php

class CardGame
{

}

class PlayingCard implements Stringable
{
    private Card $card;
    private bool $facedUp;

    public function __construct(Card $card, bool $facedUp = false)
    {
        $this->card = $card;
        $this->facedUp = $facedUp;
    }

    public function getCard(): Card
    {
        return $this->card;
    }

    public function isFacedUp(): bool
    {
        return $this->facedUp;
    }

    public function flip(): void
    {
        $this->facedUp = !$this->facedUp;
    }

    public function __toString(): string
    {
        return 'PlayingCard [facedUp=' . $this->facedUp . '] -> ' . $this->card;
    }
}

class CardFactory
{
    private array $cards = [];

    public function getCard(string $suit, string $value): Card
    {
        foreach ($this->cards as $card) {

        } 
    }
}

abstract class Card implements Stringable
{
    abstract public function getSuit();
    abstract public function getValue();

    public function equals(Card $card)
    {
        return $card->getSuit() === $this->getSuit()
               && $card->getValue === $this->getValue();
    }
}

class StandardCard extends Card
{
    private string $suit;
    private string $value;

    public function __construct(string $suit, string $value)
    {
        $this->suit = $suit;
        $this->value = $value;
    }

    public function getSuit(): string
    {
        return $this->suit;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return sprintf(
            'StandardCard [suit: %s, value: %s]',
            $this->suit,
            $this->value
        );
    }
}

class WildCard extends Card
{
    private StandardCard $standardCard;

    public function __construct()
    {
        $this->standardCard = null;
    }

    public function setCard(StandardCard $standardCard): void
    {
        $this->standardCard = $standardCard;
    }

    public function getSuit(): ?string
    {
        if ($this->standardCard !== null) {
            return $this->standardCard->getSuit();
        }

        return null;
    }

    public function getValue(): string
    {
        if ($this->standardCard !== null) {
            return $this->standardCard->getValue();
        }

        return null;
    }

    public function __toString(): string
    {
        return 'WildCard [] -> ' . $this->standardCard;
    }
}

class Client
{

}
