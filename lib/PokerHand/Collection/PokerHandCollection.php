<?php
/**
 * @file
 * PokerHandCollection.php
 */

namespace Pokerhand\Collection;

use PokerHand\PokerHand;
use PokerHand\Feed\PokerHandFeedInterface;

/**
 * A collection of poker hands.
 */
class PokerHandCollection {

  // Normalized data from the feed.
  public $data;

  // An indexed array of zero or more PokerHand objects.
  public $hands;

  function __construct(PokerHandFeedInterface $feed) {
    $raw = $feed->getData($feed::$url);

    $this->data = $feed->parseData($raw);

    return $this;
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
    }
    else {
      throw new \Exception('There was no hand at this index.');
    }

    return $this;
  }

}
