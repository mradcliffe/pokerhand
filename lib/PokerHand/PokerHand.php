<?php
/**
 * @file
 * PokerHand.php
 */

namespace PokerHand;

class PokerHand {

  /**
   * An indexed array of assocative array of card information.
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
    1 => 'High Card',
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
    if (count($cards) == 5) {
      throw new Exception('Cannot add another card to the hand.');
    }

    // Set properties on the card.
    $this->cards[$card] = array(
      'card' => $card,
      'suit' => $suit,
      'value' => $value,
      'rank' => -1,
    );

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
        $result['cards'][] = $item['value'];
        $result['straight'] = TRUE;
      }
      elseif ($result['straight']) {
        // Continue to go through each card if a straight is a possibility.
        $straight_continues = FALSE;

        foreach ($result['cards'] as $card_value) {
          if ($card_value + 1 == $item['value'] || $card_value - 1 == $item['value']) {
            // End the loop if the current card is one greater or less than a
            // card in the straight.
            $straight_continues = TRUE;
            break;
          }
        }

        if ($straight_continues) {
          $result['cards'][] = $item['value'];
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
   * Check if a hand contains only royalty cards.
   *
   * @return boolean
   *   TRUE if the hand contains royal cards (10, J, Q, K, A).
   */
  public function isRoyal() {
    $royals = array(10, 'J', 'Q', 'K', 'A');
    foreach ($this->cards as $card) {
      if (!in_array($card['value'], $royals)) {
        return FALSE;
      }
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
        $result['suit'] = $item['suit'];
        $result['count'] = 1;
      }
      elseif (in_array($item['suit'], $result)) {
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
    foreach ($this->cards as $card) {
      if (!isset($values[$card['value']])) {
        $values[$card['value']] = 1;
      }

      $values[$card['value']]++;
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
      elseif ($this->sets['three'] && count($this->sets['two']) > 0) {
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
      $output .= $card['value'] . self::$suit_chars[$card['suit']] . ' ';
    }

    return html_entity_decode(trim($output), ENT_COMPAT, 'UTF-8');
  }

}
