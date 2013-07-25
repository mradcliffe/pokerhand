# PokerHand

This is class to interface with http://poker.phpcolumbus.org/hand as part of the PHP Columbus Meetup Code Jam.

## API

### PokerHandCollection

- PokerHand\Collection\PokerHandCollection

The main class for this library. Instantiate a PokerHandCollection with a PokerHandFeedInterface.

### PokerHand

- PokerHand\PokerHand

Methods for interacting with a Poker hand to identify the bask rank 1-10 of a hand.

### PokerHandFeedInterface

- PokerHand\Feed\PokerHandFeedPHPColumbus

Implement the feed interface for http://poker.phpcolumbus.org/hand
