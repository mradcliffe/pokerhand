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

  // Raw data from the feed.
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
   *   An optional index. Defaults to the first hand, 0.
   * @return this
   *   Return the PokerHandCollection object.
   */
  public function setHand(PokerHand $hand, $index = 0) {
    foreach ($this->data[$index]->hand as $n => $card) {
      $hand->setCard($card->value_abbr . $card->suite_abbr, $card->suite_abbr, $card->value_abbr);
    }

    return $this;
  }

}
