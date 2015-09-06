<?php
/**
 * @file
 * PokerHandCollection.php
 */

namespace ColumbusPHP\PokerHand\Collection;

use ColumbusPHP\PokerHand\PokerHand;
use ColumbusPHP\PokerHand\Feed\PokerHandFeedInterface;

/**
 * A collection of poker hands.
 */
class PokerHandCollection {

  // Normalized data from the feed.
  public $data;

  // An indexed array of zero or more PokerHand objects.
  public $hands;

  /**
   * Construct
   *
   * @param $data
   *   An array of normalized data.
   */
  function __construct($data) {
    $this->data = $data;

    foreach (array_keys($this->data) as $hand_index) {
      $this->setHand(new PokerHand, $hand_index);
    }
  }

  /**
   * Create a set of Poker hands from a feed.
   *
   * @param $feed
   *   PokerHandFeedInterface
   * @return this
   *   PokerHandCollection object
   */
  static public function createFromFeed(PokerHandFeedInterface $feed) {
    $raw = $feed->getData($feed::$url);

    return new static($feed->parseData($raw));
  }

  /**
   * Get a PokerHand by index.
   *
   * @param $index
   *   The hand to retrieve.
   * @return PokerHand
   *   The PokerHand object.
   */
  public function getHand($index = 0) {
    return $this->hands[$index];
  }

  /**
   * Set a PokerHand from the feed data by index into the hands array.
   *
   * @param $hand
   *   PokerHand
   * @param $index
   *   An optional index. Defaults to the first hand, 1.
   * @return this
   *   Return the PokerHandCollection object.
   */
  public function setHand(PokerHand $hand, $index = 1) {
    if (isset($this->data[$index])) {
      foreach ($this->data[$index] as $card_index => $card) {
        $hand->addCard($card['card'], $card['suit'], $card['value']);
      }

      $this->hands[$index] = $hand;
    }
    else {
      throw new \Exception('There is no hand at this index.');
    }

    return $this;
  }

  /**
   * Rank a set of hands.
   *
   * @return this
   *   PokerHandCollection object.
   */
  public function rankHands() {
    if (empty($this->hands)) {
      throw new \Exception('There are no hands to rank.');
    }

    foreach ($this->hands as $i => $hand) {
      $hand->setSets()->setRank();
    }

    return $this;
  }

  /**
   * Sort the hands array
   */
  public function sortHands() {
    try {
      uasort($this->hands, array($this, 'compareHands'));
    }
    catch (Exception $e) {
      print $e->message() . "\n\n";
    }
  }

  /**
   * Compare two Poker hands.
   *
   * @param $a
   *   The first hand.
   * @param $b
   *   The second hand.
   * @return integer
   *   A negative integer if the first hand is weighted above the second hand,
   *   and a positive one if vice versa.
   */
  static public function compareHands(PokerHand $a, PokerHand $b) {
    // Take the easy way out first.
    if ($a->hand_rank > $b->hand_rank) {
      return -1;
    }
    elseif ($a->hand_rank < $b->hand_rank) {
      return 1;
    }

    $a_cards = $a->getScoringCards();
    $a_high_card = $a->getHighCard($a_cards);

    if (count($a_cards) == 5 && $a_high_card::compare($a->getHighCard($a_cards), $b->getHighCard($b->cards))) {
      // Compare the highest card from each full hand.
      return -1;
    }
    elseif (count($a_cards) < 5) {
      $b_cards = $b->getScoringCards();

      if (!$a_high_card::equal($a_high_card, $b->getHighCard($b_cards))) {
        // Only compare the high cards if the values are not the same i.e. pair
        // of 8s vs pair of 5s.
        if ($a_high_card::compare($a->getHighCard($a_cards), $b->getHighCard($b_cards))) {
          // Compare the highest card from the scoring cards in the hand.
          return -1;
        }

        return 1;
      }

      // Get the kicker in the remaining cards in each hand, and compare the
      // kickers.
      $a_kickers = array_diff_key($a->cards, $a_cards);
      $b_kickers = array_diff_key($b->cards, $b_cards);

      $a_high = $a->getHighCard($a_kickers);
      $b_high = $b->getHighCard($b_kickers);

      if (!isset($a_high) || !isset($b_high)) {
        throw new \Exception('A: ' . $a->__toString() . "\nB: " . $b->__toString() . "\n");
      }

      if ($a_high_card::compare($a_high, $b_high)) {
        return -1;
      }
    }

    return 1;
  }

}
