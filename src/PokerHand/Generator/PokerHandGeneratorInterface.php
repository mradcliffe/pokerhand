<?php
/**
 * @file
 * PokerHandGeneratorInterface.php
 */

namespace ColumbusPHP\PokerHand\Generator;

/**
 * Interface for generating poker hands for a feed.
 */
interface PokerHandGeneratorInterface {

  /**
   * Create a game of N players
   *
   * @param $players
   *   The number of players
   * @return static
   *   The object.
   */
  static public function createGameOf($players = 2);

  /**
   * Shuffle the deck.
   */
  public function shuffleDeck();

  /**
   * Draw a card if there is one available.
   *
   * @return array
   *   An associative array of card properties:
   *     - card: the card value and suit.
   *     - value: the human-readable card value.
   *     - value_abbr: The card value.
   *     - suit: the human-readable suit name.
   *     - suit_abbr: The suit.
   */
  public function drawCard();

  /**
   * Draw a and deal a card to a player.
   *
   * @return this
   */
  public function dealCard();

  /**
   * Add a hand to be dealt.
   *
   * @param $name
   *   The name of the hand to add, which becomes the key.
   * @return this
   */
  public function addHand($name);

  /**
   * Count the number of players.
   *
   * @return integer
   *   The number of players/hands.
   */
  public function countPlayers();

  /**
   * Count the number of cards in the deck.
   *
   * @return integer
   *   The number of cards remaining in the deck.
   */
  public function countCards();

  /**
   * Count the number of cards in a player's hand.
   *
   * @return $name
   *   The player name i.e. the key.
   * @return integer
   *   The number of cards in a player's hand.
   */
  public function countHand($name);

  /**
   * Generate a deck of cards.
   */
  public function generateDeck();

}
