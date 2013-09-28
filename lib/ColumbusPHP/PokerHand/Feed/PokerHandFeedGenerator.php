<?php
/**
 * @file
 * PokerHandFeedGenerator.php
 */

namespace ColumbusPHP\PokerHand\Feed;

use ColumbusPHP\PokerHand\Generator\PokerHandGeneratorInterface;
use ColumbusPHP\PokerHand\Feed\PokerHandFeedInterface;

/**
 * Create random N poker hands.
 */
class PokerHandFeedGenerator implements PokerHandFeedInterface, PokerHandGeneratorInterface {

  /**
   * {@inheritdoc }
   */
  static public $url = '';

  private $deck;
  private $players;

  /**
   * {@inheritdoc }
   */
  static public $suit_order = array(
    1 => 'S',
    2 => 'D',
    3 => 'C',
    4 => 'H',
  );

  /**
   * {@inheritdoc }
   */
  static public $card_order = array(
    1 => 'A',
    2 => 2,
    3 => 3,
    4 => 4,
    5 => 5,
    6 => 6,
    7 => 7,
    8 => 8,
    9 => 9,
    10  => 'T',
    11 => 'J',
    12 => 'Q',
    13 => 'K',
  );

  /**
   * Generate a deck of cards.
   */
  function __construct() {
    $this->generateDeck();
  }

  /**
   * {@inheritdoc }
   */
  static public function createGameOf($players = 2) {
    $instance =  new static();

    $instance->shuffleDeck();

    for ($i = 1; $i <= $players; $i++) {
      $instance->addHand('Player ' . $i);
    }

    // Get ready for dealing.
    reset($instance->players);

    return $instance;
  }

  /**
   * {@inheritdoc }
   */
  public function getUrl() {
    return self::$url;
  }

  /**
   * {@inheritdoc }
   */
  public function getData($url) {
    $num = count($this->players);

    for ($i = 0; $i < $num * 5; $i++) {
      if ($this->countCards()) {
        $this->dealCard();
      }
    }

    return $this->players;
  }

  /**
   * {@inheritdoc }
   */
  public function parseData($data) {
    $info = array();

    foreach ($data as $n => $player) {
      $info[$player['name']] = array();
      foreach ($player['hand'] as $card_index => $card) {
        // Normalize the data.
        $card['value'] = str_replace('T', 10, $card['value']);
        $card['card'] = str_replace('T', 10, $card['card']);
        $card_index = str_replace('T', 10, $card_index);
        $info[$player['name']][$card_index] = $card;
      }
    }

    return $info;
  }

  /**
   * {@inheritdoc }
   */
  public function shuffleDeck() {
    $new = array();
    $card_keys = array_keys($this->deck);

    shuffle($card_keys);

    foreach ($card_keys as $card_key) {
      $new[$card_key] = $this->deck[$card_key];
    }

    $this->deck = $new;

    return $this;
  }

  /** 
   * {@inheritdoc }
   */
  public function drawCard() {
    return array_shift($this->deck);
  }

  /**
   * {@inheritdoc }
   */
  public function dealCard() {
    $current = key($this->players);
    $card =$this->drawCard();
    $this->players[$current]['hand'][$card['card']] = $card;

    // Advance to the next player, if possible, or reset.
    $next_hand = next($this->players);

    if (!$next_hand) {
      reset($this->players);
    }

    return $this;
  }

  /**
   * {@inheritdoc }
   */
  public function addHand($name) {
    if (count($this->players) > 14) {
      throw new \Exception('Cannot add any more hands.');
    }

    $this->players[$name] = array(
      'name' => $name,
      'hand' => array(),
    );

    return $this;
  }

  /** 
   * {@inheritdoc }
   */
  public function countPlayers() {
    return count($this->players);
  }

  /**
   * {@inheritdoc }
   */
  public function countCards() {
    return count($this->deck);
  }

  /**
   * {@inheritdoc }
   */
  public function countHand($name) {
    if (!isset($this->players[$name])) {
      print_r($this->players);
      throw new \Exception('Player does not exist');
    }

    return count($this->players[$name]['hand']);
  }

  /**
   * {@inheritdoc }
   */
  public function generateDeck() {
    $suit_ord = 1;

    for ($i = 1; $i <= 52; $i++) {
      $value = ($i % 13 == 0) ? $i / $suit_ord : $i % 13;

      $card = self::$card_order[$value] . self::$suit_order[$suit_ord];

      $this->deck[$card] = array(
        'value' => self::$card_order[$value],
        'suit' => self::$suit_order[$suit_ord],
        'card' => $card,
      );

      if ($i % 13 == 0) {
        $suit_ord++;
      }
    }
  }

}
