<?php
/**
 * @file
 * PlayingCard.php
 */

namespace ColumbusPHP\PlayingCard;

/**
 * Provide methods on playing cards.
 */
class PlayingCard {

  public $suit;
  public $value;
  public $card;
  public $rank;

  static public $suits = array('S', 'H', 'D', 'C');

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
   * Create a card object from a string representation of the card.
   *
   * @param $value
   *   The string representation of the card e.g. KH, 8C, etc...
   * @return PlayingCard
   *   A PlayingCard object.
   */
  static public function createFromString($value = '') {
    if (empty($value)) {
      throw new \Exception('Card is empty.');
    }

    $value_length = strlen($value);

    if ($value_length < 2 || $value_length > 3) {
      throw new \Exception('Card does not have a valid string representation.');
    }

    // Split the string into card value and suti.
    $suit = substr($value, -1);

    if (!self::validateSuit($suit)) {
      throw new \Exception('Card does not have a valid suit: ' . $suit);
    }

    $card_value = substr($value, 0, $value_length - strlen($suit));

    if (!self::validateValue($card_value)) {
      throw new \Exception('Card does not have a valid value: ' . $card_value);
    }

    $card = new static();

    $card->value = $card_value;
    $card->suit = $suit;
    $card->card = $value;
    $card->rank = -1;

    return $card;
  }

  /**
   * Validate the value of a card.
   *
   * @param $value
   *   The card value.
   * @return boolean
   *   TRUE if the card value is valid.
   */
  static public function validateValue($value) {
    return in_array($value, array_keys(self::$card_order));
  }

  /**
   * Validate the suit of a card.
   *
   * @param $suit
   *   The card suit to test.
   * @return boolean
   *   TRUE if the card suit is valid.
   */
  static public function validateSuit($suit) {
    return in_array($suit, self::$suits);
  }

  /**
   * Compare two card objects.
   *
   * @param $a
   *   The first PlayingCard object.
   * @param $b
   *   The second PlayingCard object.
   * @return boolean
   *   TRUE if the first card is weighted higher than the second card.
   */
  static public function compare(PlayingCard $a, PlayingCard $b) {
    $suit_rank = array_flip(self::$suits);

    // Value is greater or the suit is greater when the value is the same.
    if (self::$card_order[$a->value] > self::$card_order[$b->value] ||
        (self::$card_order[$a->value] == self::$card_order[$b->value] &&
         $suit_rank[$a->suit] < $suit_rank[$b->suit])) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Compare two card objects value properties to check if the value is the
   * same, but without checking suit.
   *
   * @param $a
   *   PlayingCard
   * @param $b
   *   PlayingCard
   * @return boolean
   *   TRUE if the cards have equal values.
   */
  static public function equal(PlayingCard $a, PlayingCard $b) {
    return self::$card_order[$a->value] == self::$card_order[$b->value];
  }

  /**
   * Output the string representation of a card.
   */
  public function __toString() {
    mb_internal_encoding("UTF-8");

    $output = $this->value . self::$suit_chars[$this->suit];

    return html_entity_decode(trim($output), ENT_COMPAT, 'UTF-8');
  }

}
