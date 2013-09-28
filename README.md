# PokerHand

This is a suite of classes to interface with http://poker.phpcolumbus.org/hand
as part of the PHP Columbus Meetup Code Jam. The challenge is to compare two
Poker hands, determine the winner, and determine each hand rank.

I chose the over-engineered approach because in essence this is simply a large
if-elseif-else, and I don't make life easy.

## Challenge

1. `cd pokerhand`
2. `composer install`
3. `php run.php`

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

### Generate PHP Docs

- `mkdir docs`
- `vendor/bin/phpdoc -d lib -t docs`

### PokerHandCollection

- ColumbusPHP\PokerHand\Collection\PokerHandCollection
  - The main class for this library. Instantiate a PokerHandCollection with a PokerHandFeedInterface.
  - PokerHandCollection::hands
     - An associative array of PokerHand objects keyed by the hand name i.e. Player 1, Player 2.
  - PokerHandCollection::data
     - An associative array of normalized hand data from the feed keyed by the hand name, and contains an array of card data.
        - A card array is an associative array similar to the array in PokerHand::hand. If a feed provides 10 as "T", then this will be converted back to 10.

### PokerHand

- ColumbusPHP\PokerHand\PokerHand
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

- ColumbusPHP\PlayingCard\PlayingCard
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

- ColumbusPHP\PokerHand\Feed\PokerHandFeedPHPColumbus
   - Implements the feed interface for http://poker.columbusphp.org/hand

- ColumbusPHP\PokerHand\Feed\PokerHandFeedDummy
   - Implements a dummy feed that returns in the same format as PokerHandFeedPHPColumbus.

- ColumbusPHP\PokerHand\Feed\PokerHandFeedGenerator
   - Implements a feed and implements PokerHandGenerator to create greater than 2 players feed.

## PokerHandGeneratorInterface

- ColumbusPHP\PokerHand\Feed\PokerHandFeedGenerator
   - @todo use ColumbusPHP\PlayingCard\PlayingCard in the generator.

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

## Unit Tests

- `mkdir coverage`
- `vendor/bin/phpunit --coverage-html coverage lib/Tests`

## Notes

### Poker Hands

1. High Card
2. One Pair
3. Two Pair
4. Three of a Kind
5. Straight
6. Flush
7. Full House
8. Four of a Kind
9. Straight Flush
10. Royal Flush

### Example JSON from Feed

```json
[{"name":1,"hand":[{"card":"8C","suite":"Clubs","suite_abbr":"C","value":"Eight","value_abbr":8},{"card":"KS","suite":"Spades","suite_abbr":"S","value":"King","value_abbr":"K"},{"card":"QD","suite":"Diamonds","suite_abbr":"D","value":"Queen","value_abbr":"Q"},{"card":"JS","suite":"Spades","suite_abbr":"S","value":"Jack","value_abbr":"J"},{"card":"2S","suite":"Spades","suite_abbr":"S","value":"Two","value_abbr":2}]},{"name":2,"hand":[{"card":"3S","suite":"Spades","suite_abbr":"S","value":"Three","value_abbr":3},{"card":"4H","suite":"Hearts","suite_abbr":"H","value":"Four","value_abbr":4},{"card":"AS","suite":"Spades","suite_abbr":"S","value":"Ace","value_abbr":"A"},{"card":"4C","suite":"Clubs","suite_abbr":"C","value":"Four","value_abbr":4},{"card":"3H","suite":"Hearts","suite_abbr":"H","value":"Three","value_abbr":3}]}]

```
