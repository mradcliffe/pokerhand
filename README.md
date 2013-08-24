# PokerHand

This is class to interface with http://poker.phpcolumbus.org/hand as part of the PHP Columbus Meetup Code Jam.

## Challenge

`php run.php`

## API

```php
// Create a new poker hand collection from a feed.
$game = PokerHandCollection::createFromFeed(PokerHandFeedGenerator::createGameOf(2));

// Rank all the hands and sort them.
$game
  ->rankHands()
  ->sortHands();

// The hands property is now sorted by the winner down.
foreach ($game->hands as $name => $hand) {
  print $name . ": " . $hand->__toString() . "\n";
}

// See the static variables and constants in PokerHand for more information on
// how to format things nicely.
```

## Interfaces / Classes

### PokerHandCollection

- PokerHand\Collection\PokerHandCollection
  - The main class for this library. Instantiate a PokerHandCollection with a PokerHandFeedInterface.
  - PokerHandCollection::hands
     - An associative array of PokerHand objects keyed by the hand name i.e. Player 1, Player 2.
  - PokerHandCollection::data
     - An associative array of normalized hand data from the feed keyed by the hand name, and contains an array of card data.
        - A card array is an associative array similar to the array in PokerHand::hand. If a feed provides 10 as "T", then this will be converted back to 10.

### PokerHand

- PokerHand\PokerHand
   - Methods for interacting with a Poker hand to identify the bask rank 1-10 of a hand.
   - PokerHand::hand
      - An associative array of PlayingCard objects.
   - PokerHand::hand_rank
     - The base rank of the hand from 1 to 10 where 1 is high card and 10 is a royal straight flush.

```php
// Format a poker hand as a UTF-8 string.
print $hand->__toString();

// Format the hand rank for a given hand of cards.
$hand->setSets()->setRank();
print $hand::ranks[$hand->hand_rank];
```

### PlayingCard

- PlayingCard\PlayingCard
  - A low-level class for representing a playing card. New.
  - PlayingCard::value
  - PlayingCard::card
  - PlayingCard::suit

```php
// Create a playing card from a string value.
$ace_of_spades = PlayingCard::createFromString('AS');
echo $ace_of_spades->__toString();
```

### PokerHandFeedInterface

- PokerHand\Feed\PokerHandFeedPHPColumbus
   - Implements the feed interface for http://poker.columbusphp.org/hand

- PokerHand\Feed\PokerHandFeedDummy
   - Implements a dummy feed that returns in the same format as PokerHandFeedPHPColumbus.

- PokerHand\Feed\PokerHandFeedGenerator
   - Implements a feed and implements PokerHandGenerator to create greater than 2 players feed.

## PokerHandGeneratorInterface

- PokerHand\Feed\PokerHandFeedGenerator
   - @todo use PlayingCard\PlayingCard in the generator.

```php
  // This static method will generate the deck, shuffle, and add the players.
  // The deck is generated in the fairly standard A23456789TJQK, SDCH order.
  $game = PokerHandFeedGenerator::createGameOf(5);

  // Deal 5 cards to each of the 5 players for a total of 25 cards. The current
  // player recieves the next card from the deck, and then the internal pointer
  // is reset.
  for ($i = 0; $i < 25; $i++) {
    $game->dealCard();
  }
```
