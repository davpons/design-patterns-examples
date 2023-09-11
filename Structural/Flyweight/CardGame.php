<?php

class CardGame
{
    private CardFactory $cardFactory;

    public function __construct(CardFactory $cardFactory)
    {
        $this->cardFactory = $cardFactory;
    }

    public function getCard(string $suit, string $value): PlayingCard
    {
        $card = $this->cardFactory->getCard($suit, $value);

        return new PlayingCard($card, false);
    }

    public function getWildCard(): PlayingCard
    {
        return new PlayingCard(new WildCard(), false);
    }
    
    public function setCard(PlayingCard $wildCard, PlayingCard $standardCard): void
    {
        $w = $wildCard->getCard();
        $w->setCard($standardCard->getCard());
    }
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
        $requestedCard = new StandardCard($suit, $value);

        foreach ($this->cards as $card) {
            if ($card->equals($requestedCard)) {
                return $card;
            }
        }

        $this->cards[] = $requestedCard;

        return $requestedCard;
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
    private ?StandardCard $standardCard;

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
    public function main():void
    {
        $this->println('PLAYING FIRST GAME CARD');

        $cardFactory = new CardFactory();
		$cardGame1 = new CardGame($cardFactory);

		$playingCard1a = $cardGame1->getCard('HEARTS', 'SEVEN');
		$playingCard1a->flip();
		$this->println($playingCard1a); 

		$playingCard1b = $cardGame1->getCard('PIKES', 'THREE');
		$playingCard1b->flip();
		$this->println($playingCard1b);        

		$playingCard1c = $cardGame1->getWildCard();
		$cardGame1->setCard($playingCard1c, $playingCard1a);
		$playingCard1c->flip();
		$this->println($playingCard1c);
    }

    public function println(string $value): void
    {
        echo $value . '<br>';
    }
}

(new Client())->main();
