<?php
/**
 * @file
 * PokerHand.php
 */

namespace ColumbusPHP\PokerHand;

use ColumbusPHP\PlayingCard\PlayingCard;

class PokerHand {

  /**
   * An indexed array of zero to five PlayingCard objects.
   */
  public $cards;

  /**
   * The base hand rank 1-10.
   */
  public $hand_rank;

  /**
   * Sets of a kind associative array
   *
   * pair: an array of pairs.
   * three: the card value of three of a kind if any.
   * four: the card value of four of a kind if any.
   */
  public $sets;

  /**
   * Human-readable names of hand ranks.
   */
  static public $ranks = array(
    1 => 'High',
    2 => 'One Pair',
    3 => 'Two Pair',
    4 => 'Three of a Kind',
    5 => 'Straight',
    6 => 'Flush',
    7 => 'Full House',
    8 => 'Four of a Kind',
    9 => 'Straight Flush',
    10 => 'Royal Flush',
  );

  /**
   * ordinal card value order by key.
   */
  static public $card_order = array(
    2 => 2,
    3 => 3,
    4 => 4,
    5 => 5,
    6 => 6,
    7 => 7,
    8 => 8,
    9 => 9,
    10 => 10,
    'J' => 11,
    'Q' => 12,
    'K' => 13,
    'A' => 14,
  );

  /**
   * HTML entities for suits.
   */
  static public $suit_chars = array(
    'S' => '&spades;',
    'H' => '&hearts;',
    'D' => '&diams;',
    'C' => '&clubs;',
  );

  /**
   * Add a card to the hand.
   *
   * @param $card
   *   The card index 'KH' for 'King of Hearts',
   * @param $suit
   *   The suit character
   * @param $value
   *   The card value
   * @return this
   *   The object for chaining.
   */
  public function addCard($card, $suit, $value) {
    if (count($this->cards) == 5) {
      throw new Exception('Cannot add another card to the hand.');
    }

    try {
      // Add a new card object.
      $this->cards[$card] = PlayingCard::createFromString($card);
    }
    catch (\Exception $e) {
      throw $e;
    }

    return $this;
  }

  /**
   * Check if a hand is a straight.
   *
   * @return boolean
   *   TRUE if the hand is a straight.
   */
  public function isStraight() {
    $card_order = self::$card_order;

    if ($this->isRoyal()) {
      // All royal cards is always a straight, but not a royal flush. :-)
      return TRUE;
    }

    $straight = array_reduce($this->cards, function(&$result, $item) use ($card_order) {
      if (empty($result['cards'])) {
        $result['cards'][] = $item->value;
        $result['straight'] = TRUE;
      }
      elseif ($result['straight']) {
        // Continue to go through each card if a straight is a possibility.
        $straight_continues = FALSE;

        foreach ($result['cards'] as $card_value) {
          if (($card_value + 1 == $item->value || $card_value - 1 == $item->value) && !in_array($item->value, $result['cards'])) {
            // End the loop if the current card is one greater or less than a
            // card in the straight, AND there are no sets of any thing.
            $straight_continues = TRUE;
            break;
          }
        }

        if ($straight_continues) {
          $result['cards'][] = $item->value;
          $result['straight'] = TRUE;
        }
        else {
          $result['straight'] = FALSE;
        }
      }

      return $result;
    });

    return $straight['straight'];
  }

  /**
   * Check if a hand contains only royalty cards and no sets of royalty cards.
   * I don't like the term face card because 10s don't have faces. >:(
   *
   * @return boolean
   *   TRUE if the hand contains royal cards (10, J, Q, K, A).
   */
  public function isRoyal() {
    $royals = array(10, 'J', 'Q', 'K', 'A');
    $straight = array();
    foreach ($this->cards as $card) {
      if (!in_array($card->value, $royals) || in_array($card->value, $straight)) {
        return FALSE;
      }
      $straight[] = $card->value;
    }

    return TRUE;
  }

  /**
   * Check if a hand has a flush.
   *
   * @return boolean
   *   TRUE if flush, FALSE if not.
   */
  public function isFlush() {
    $flush = array_reduce($this->cards, function(&$result, $item) {
      if (empty($result['suit'])) {
        $result['suit'] = $item->suit;
        $result['count'] = 1;
      }
      elseif (in_array($item->suit, $result)) {
        $result['count']++;
      }

      return $result;
    });

    return $flush['count'] == 5;
  }

  /**
   * Find the one or more sets of the same card value where a set is two or
   * more of the same card value.
   *
   * @return array
   *   An indexed array containing the card value of one or more sets found.
   */
  public function getSetsOfAKind() {
    $items = array(
      'pair' => array(),
      'three' => 0,
      'four' => 0,
    );

    // Count the number of each card value there is.
    $values = array();
    foreach ($this->cards as $card) {
      if (!isset($values[$card->value])) {
        $values[$card->value] = 0;
      }

      $values[$card->value]++;
    }

    // Sort the values into pairs, 3 of a kind, and 4 of a kind given that
    // there can only be one item if 4 of a kind.
    foreach ($values as $card_value => $kind) {
      if ($kind == 4) {
        $items['four'] = $card_value;
        break;
      }
      elseif ($kind == 2) {
        $items['pair'][] = $card_value;
      }
      elseif ($kind == 3) {
        $items['three'] = $card_value;
      }
    }

    return $items;
  }

  /**
   * Set the sets property.
   */
  public function setSets() {
    $this->sets = $this->getSetsOfAKind();

    return $this;
  }

  /**
   * Get the highest value card in a set of cards.
   *
   * @param $cards
   *   An array of card arrays to reduce. This may be the entire hand or it may
   *   be the highest non-scoring card.
   * @return PlayingCard
   *   The PlayingCard object with the highest card value in the set of cards.
   */
  public function getHighCard(array $cards) {
    $suit_rank = array_flip(array_keys(self::$suit_chars));
    $ranks = self::$card_order;

    return array_reduce($cards, function(&$result, $item) use ($suit_rank, $ranks) {
      if (empty($result)) {
        // Set result to the first card.
        $result = $item;
      }
      elseif ($ranks[$item->value] > $ranks[$result->value]) {
        $result = $item;
      }
      elseif ($ranks[$item->value] == $ranks[$result->value] && $suit_rank[$item->suit] < $suit_rank[$result->suit]) {
        // club is a 3 and spades is a 0.
        $result = $item;
      }

      return $result;
    });
  }

  /**
   * Reduce the cards array to just the cards that are a part of the ranked
   * hand, if possible. Otherwise return the entire hand of cards.
   *
   * @return array
   *   An array of card objects.
   */
  public function getScoringCards() {
    if (!isset($this->hand_rank) || !isset($this->sets)) {
      throw new \Exception('Hand must be ranked to use this method.');
    }

    if (in_array($this->hand_rank, array(1, 5, 6, 7, 9, 10))) {
      // Return the entire cards array as the entire hand is ranked.
      return $this->cards;
    }

    // Go through the sets.
    if ($this->sets['four']) {
      $set = array($this->sets['four']);
    }
    elseif ($this->sets['three']) {
      $set = array($this->sets['three']);
    }
    else {
      $set = $this->sets['pair'];
    }
    // Reduce the hand into just the scoring cards for a pair, two pair, three
    // of a kind, or four of a kind.
    return array_reduce($this->cards, function(&$result, $item) use ($set) {
      if ($item->value == $set[0] || (isset($set[1]) && $item->value == $set[1])) {
        $result[$item->card] = $item;
      }
      return $result;
    });
  }

  /**
   * Assign base hand rank.
   *
   * @return this
   *   Return the current object.
   */
  public function setRank() {
    if (empty($this->cards)) {
      throw new Exception('There are no cards in this hand!');
    }

    if ($this->isRoyal() && $this->isFlush()) {
      // Royal flush.
      $this->hand_rank = 10;
    }
    elseif ($this->isFlush() && $this->isStraight()) {
      // Straight flush.
      $this->hand_rank = 9;
    }
    else {
      // Other stuff.

      if ($this->sets['four']) {
        // Four of a kind.
        $this->hand_rank = 8;
      }
      elseif ($this->sets['three'] && count($this->sets['pair']) > 0) {
        // Full House.
        $this->hand_rank = 7;
      }
      elseif ($this->isFlush()) {
        // Flush.
        $this->hand_rank = 6;
      }
      elseif ($this->isStraight()) {
        // Straight.
        $this->hand_rank = 5;
      }
      elseif ($this->sets['three']) {
        // Three of a kind
        $this->hand_rank = 4;
      }
      elseif (count($this->sets['pair']) > 1) {
        // Two pair.
        $this->hand_rank = 3;
      }
      elseif (count($this->sets['pair']) > 0) {
        // One pair.
        $this->hand_rank = 2;
      }
      else {
        $this->hand_rank = 1;
      }
    }

    return $this;
  }

  /**
   * Implement magic method to output as a string.
   */
  public function __toString() {
    mb_internal_encoding("UTF-8");

    $output = '';
    if (empty($this->cards)) {
      return $output;
    }

    // Concatenate each card to output as a nicely-formatted string.
    foreach ($this->cards as $card) {
      $output .= $card->__toString() . ' ';
    }

    return trim($output);
  }

}
