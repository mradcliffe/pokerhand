<?php
/**
 * @file
 * PokerHandTest.php
 */

use ColumbusPHP\PlayingCard\PlayingCard;
use ColumbusPHP\PokerHand\PokerHand;

/**
 * Test PokerHand methods.
 */
class PokerHandTest extends PHPUnit_Framework_TestCase {

  /**
   * Test flushes
   */
  public function testFlush() {
    $hand = new PokerHand();
    $values = array(5, 'K', 3, 'J', 2);

    // Create an array of clubs.
    for ($i = 0; $i < 5; $i++) {
      // Add a card value of the same suit.

      $hand->addCard($values[$i] . 'C', 'C', $values[$i]);
    }

    $this->assertTrue($hand->isFlush(), $hand->__toString());

    // Set a card to not a club.
    $hand->cards['2C']->suit = 'H';

    $this->assertFalse($hand->isFlush(), $hand->__toString());
  }

  /**
   * Test a random straight.
   */
  public function testStraight() {
    $hand = new PokerHand();
    $suits = array('S', 'H', 'D', 'C');

    for ($i = 2; $i < 7; $i++) {
      // Add a card in sequence of different suits.
      $suit_index = $i % 4;
      $hand->addCard($i . $suits[$suit_index], $suits[$suit_index], $i);
    }

    $this->assertTrue($hand->isStraight(), $hand->__toString());

    // Set the rank and assert it.
    $hand->setRank();
    $this->assertEquals(5, $hand->hand_rank);

    // Generate a new hand with 4 cards in a straight.
    $hand = new PokerHand();
    for ($i = 2; $i < 6; $i++) {
      $suit_index = $i % 4;
      $hand->addCard($i . $suits[$suit_index], $suits[$suit_index], $i);
    }

    // Set one card out of order.
    $hand->addCard('10H', 'H', 10);

    $this->assertFalse($hand->isStraight(), $hand->__toString());

    // Set the rank and assert it's not a straight.
    $hand->setRank();
    $this->assertEquals(1, $hand->hand_rank);
  }

  /**
   * Test a straight of royal cards, which has its own logic in code.
   */
  public function testRoyalStraight() {
    $hand = new PokerHand();
    $suits = array('S', 'H', 'D', 'C');
    $values = array(0 => '10', 1 => 'J', 2 => 'Q', 3 => 'K', 4 => 'A');

    for ($i = 0; $i < 5; $i++) {
      // Add a card in sequence of different suits.
      $suit_index = $i % 4;
      $hand->addCard($values[$i] . $suits[$suit_index], $suits[$suit_index], $values[$i]);
    }

    $this->assertTrue($hand->isStraight(), $hand->__toString());
  }

  /**
   * Test a straight flush.
   */
  public function testStraightFlush() {
    $hand = new PokerHand();

    for ($i = 2; $i < 7; $i++) {
      $hand->addCard($i . 'C', 'C', $i);
    }

    $this->assertTrue($hand->isFlush() && $hand->isStraight(), $hand->__toString());

    // Set rank and assert it.
    $hand->setRank();
    $this->assertEquals(9, $hand->hand_rank);
  }

  /**
   * Test a royal straight flush.
   */
  public function testRoyalFlush() {
    $hand = new PokerHand();
    $royals = array(10 => 10, 11 => 'J', 12 => 'Q', 13 => 'K', 14 => 'A');

    for ($i = 10; $i < 15; $i++) {
      $hand->addCard($royals[$i] . 'H', 'H', $royals[$i]);
    }
    $this->assertTrue($hand->isRoyal() && $hand->isFlush(), $hand->__toString());

    // Set rank and assert it.
    $hand->setRank();
    $this->assertEquals(10, $hand->hand_rank);
  }

  /**
   * Test a full house
   */
  public function testFullHouse() {
    $hand = new PokerHand();
    $hand
      ->addCard('5S', 'S', 5)
      ->addCard('10D', 'D', 10)
      ->addCard('5H', 'H', 5)
      ->addCard('10H', 'H', 10)
      ->addCard('10C', 'C', 10)
      ->setSets()
      ->setRank();

    $this->assertEquals(7, $hand->hand_rank, $hand->__toString());
    $this->assertCount(1, $hand->sets['pair']);
    $this->assertEquals(10, $hand->sets['three']);
  }

  /**
   * Test various sets and logic behind getting sets: 1 pair, 2 pair,
   * 3 of a kind, and 4 of a kind.
   *
   * @todo break this test into multiple tests.
   */
  public function testSets() {
    // Pair of 5s.
    $hand = new PokerHand();
    $hand
      ->addCard('5S', 'S', 5)
      ->addCard('10D', 'D', 10)
      ->addCard('5H', 'H', 5)
      ->addCard('QC', 'C', 'Q')
      ->addCard('KH', 'H', 'K')
      ->setSets()
      ->setRank();

    $hand_output = $hand->__toString();

    $this->assertEquals(2, $hand->hand_rank, $hand_output);
    $this->assertCount(1, $hand->sets['pair'], $hand_output);
    $this->assertEquals(5, $hand->sets['pair'][0], $hand_output);

    // 2 Pair, 5s and 10s.
    $hand = new PokerHand();
    $hand
      ->addCard('5S', 'S', 5)
      ->addCard('10D', 'D', 10)
      ->addCard('5H', 'H', 5)
      ->addCard('QC', 'C', 'Q')
      ->addCard('10H', 'H', 10)
      ->setSets()
      ->setRank();

    $hand_output = $hand->__toString();

    $this->assertEquals(3, $hand->hand_rank, $hand_output);
    $this->assertCount(2, $hand->sets['pair'], $hand_output);

    // 3 of a Kind
    $hand = new PokerHand();
    $hand
      ->addCard('5S', 'S', 5)
      ->addCard('5D', 'D', 5)
      ->addCard('5H', 'H', 5)
      ->addCard('QC', 'C', 'Q')
      ->addCard('10H', 'H', 10)
      ->setSets()
      ->setRank();

    $hand_output = $hand->__toString();

    $this->assertEquals(4, $hand->hand_rank, $hand_output);
    $this->assertCount(0, $hand->sets['pair'], $hand_output);
    $this->assertEquals(5, $hand->sets['three'], $hand_output);
    $this->assertEquals(0, $hand->sets['four'], $hand_output);

    // 4 of a Kind
    $hand = new PokerHand();
    $hand
      ->addCard('5S', 'S', 5)
      ->addCard('5D', 'D', 5)
      ->addCard('5H', 'H', 5)
      ->addCard('QC', 'C', 'Q')
      ->addCard('5C', 'C', 5)
      ->setSets()
      ->setRank();

    $this->assertEquals(8, $hand->hand_rank, $hand_output);
    $this->assertCount(0, $hand->sets['pair'], $hand_output);
    $this->assertEquals(0, $hand->sets['three'], $hand_output);
    $this->assertEquals(5, $hand->sets['four']);
  }

  /**
   * Test functionality to grab the highest ranked card in a hand.
   */
  public function testHighCard() {
    $hand = new PokerHand();
    $suits = array('S', 'H', 'D', 'C');

    $hand
      ->addCard('5H', 'H', 5)
      ->addCard('QS', 'S', 'Q')
      ->addCard('9C', 'C', 9)
      ->addCard('AS', 'S', 'A')
      ->addCard('AC', 'S', 'A');
    $high_card = $hand->getHighCard($hand->cards);

    $this->assertEquals($high_card->card, 'AS', $hand->__toString());

    // Test a particular set of hands that failed code once.
    $a = new PokerHand();
    $a
      ->addCard('QD', 'H', 'Q')
      ->addCard('8C', 'C', 8)
      ->addCard('6S', 'S', 6)
      ->addCard('4D', 'D', 4)
      ->addCard('3D', 'D', 3)
      ->setSets()
      ->setRank();
    $a_card = $a->getHighCard($a->cards);
    $this->assertEquals('QD', $a_card->card);

    $b = new PokerHand();
    $b
      ->addCard('6H', 'H', 6)
      ->addCard('QC', 'C', 'Q')
      ->addCard('KC', 'C', 'K')
      ->addCard('9S', 'S', 9)
      ->addCard('2C', 'C', 2)
      ->setSets()
      ->setRank();
    $b_card = $b->getHighCard($b->cards);
    $this->assertEquals('KC', $b_card->card);

    $this->assertFalse(PlayingCard::compare($a_card, $b_card), $a->__toString() . '<' . $b->__toString());

    // Test a particular set of hands that failed code regularly.
    // KD 3H 2H 7S KC
    // 9D JS JC 6H 5S
    $one = new PokerHand();
    $one
      ->addCard('KD', 'D', 'K')
      ->addCard('3H', 'H', 3)
      ->addCard('2H', 'H', 2)
      ->addCard('7S', 'S', 7)
      ->addCard('KC', 'C', 'K')
      ->setSets()
      ->setRank();
    $two = new PokerHand();
    $two
      ->addCard('9D', 'D', 9)
      ->addCard('JS', 'S', 'J')
      ->addCard('JC', 'C', 'J')
      ->addCard('6H', 'H', 6)
      ->addCard('5S', 'S', 5)
      ->setSets()
      ->setRank();

    $one_kickers = array_diff_key($one->cards, $one->getScoringCards());
    $one_high = $one->getHighCard($one_kickers);
    $this->assertEquals('7S', $one_high->card, $one->__toString());

    $two_kickers = array_diff_key($two->cards, $two->getScoringCards());
    $two_high = $two->getHighCard($two_kickers);
    $this->assertEquals('9D', $two_high->card, $two->__toString());
  }

  /**
   * Test high card that is not a part of a ranking set of cards.
   */
  public function testHighScoreCard() {
    $hand = new PokerHand();
    $suits = array('S', 'H', 'D', 'C');

    $hand
      ->addCard('5H', 'H', 5)
      ->addCard('10S', 'S', 10)
      ->addCard('9C', 'C', 9)
      ->addCard('10D', 'D', 10)
      ->addCard('9S', 'S', 9)
      ->setSets()
      ->setRank();

    $high_card = $hand->getHighCard($hand->getScoringCards());

    $this->assertEquals($high_card->card, '10S', $hand->__toString());
  }

  /**
   * Test string output.
   */
  public function testToString() {
    $hand = new PokerHand();
    // Assert empty.
    $this->assertEmpty($hand->__toString());

    $suits = array('S', 'H', 'D', 'C');

    for ($i = 2; $i < 7; $i++) {
      // Add a card in sequence of different suits.
      $suit_index = $i % 4;
      $hand->addCard($i . $suits[$suit_index], $suits[$suit_index], $i);
    }

    $this->assertEquals('2♦ 3♣ 4♠ 5♥ 6♦', $hand->__toString(), $hand->__toString());
  }

}

