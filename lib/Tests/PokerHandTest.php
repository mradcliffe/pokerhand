<?php
/**
 * @file
 * PokerHandTest.php
 */


use PokerHand\PokerHand;

/**
 * Test PokerHand methods.
 */
class PokerHandTest extends PHPUnit_Framework_TestCase {

  public function testFlush() {
    $hand = new PokerHand();
    $values = array(5, 'K', 3, 'J', 2);

    // Create an array of clubs.
    for ($i = 0; $i < 5; $i++) {
      // Add a card value of the same suit.

      $hand->addCard($values[$i] . 'C', 'C', $values[$i]);
    }

    $this->assertTrue($hand->isFlush());

    // Set a card to not a club.
    $hand->cards['2C']['suit'] = 'H';

    $this->assertFalse($hand->isFlush());
  }

  public function testStraight() {
    $hand = new PokerHand();
    $suits = array('S', 'H', 'D', 'C');

    for ($i = 2; $i < 7; $i++) {
      // Add a card in sequence of different suits.
      $suit_index = $i % 4;
      $hand->addCard($i . $suits[$suit_index], $suits[$suit_index], $i);
    }

    $this->assertTrue($hand->isStraight());

    // Set a card to something out of order.
    $hand->cards['2H']['value'] = 10;

    $this->assertFalse($hand->isStraight());
  }

  public function testStraightFlush() {
    $hand = new PokerHand();

    for ($i = 2; $i < 7; $i++) {
      $hand->addCard($i . 'C', 'C', $i);
    }

    $this->assertTrue($hand->isFlush() && $hand->isStraight());
  }

  public function testRoyalFlush() {
    $hand = new PokerHand();
    $royals = array(10 => 10, 11 => 'J', 12 => 'Q', 13 => 'K', 14 => 'A');

    for ($i = 10; $i < 15; $i++) {
      $hand->addCard($royals[$i] . 'H', 'H', $royals[$i]);
    }
    $this->assertTrue($hand->isRoyal() && $hand->isFlush());
  }

}

