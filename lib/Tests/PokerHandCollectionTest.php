<?php
/**
 * @file
 * PokerHandCollectionTest.php
 */

use PokerHand\PokerHand;
use PokerHand\Collection\PokerHandCollection;
use PokerHand\Feed\PokerHandFeedDummy;

/**
 * Tests for PokerHandCollection.php
 */
class PokerHandCollectionTest extends PHPUnit_Framework_TestCase {

  public function testFeedData() {
    $hands = PokerHandCollection::createFromFeed(new PokerHandFeedDummy);

    $this->assertInstanceOf('PokerHand\Collection\PokerHandCollection', $hands);

    $this->assertCount(2, $hands->data, 'There are 2 hands.');
  }

  public function testSetHand() {
    $hands = PokerHandCollection::createFromFeed(new PokerHandFeedDummy);

    $hands->setHand(new PokerHand);
  }

  public function testKickerCompare() {
    $hands = new PokerHandCollection(array());

    $a = new PokerHand;
    $a
      ->addCard('KS', 'S', 'K')
      ->addCard('KD', 'D', 'K')
      ->addCard('5H', 'H', 5)
      ->addCard('9D', 'D', 9)
      ->addCard('3C', 'C', 3);

    $b = new PokerHand;
    $b
      ->addCard('KC', 'C', 'K')
      ->addCard('KH', 'H', 'K')
      ->addCard('5C', 'C', 5)
      ->addCard('10D', 'D', 10)
      ->addCard('3S', 'S', 3);

    $hands->hands[] = $a;
    $hands->hands[] = $b;

    // Rank hands.
    $hands->rankHands();

    // Pull out the kickers for each hand.
    $a_kickers = array_diff_key($a->cards, $a->getScoringCards());
    $this->assertNotEmpty($a_kickers);
    $b_kickers = array_diff_key($b->cards, $b->getScoringCards());
    $this->assertNotEmpty($b_kickers);

    // Get the high cards from the kicker possibilities.
    $a_high = $a->getHighCard($a_kickers);
    $this->assertEquals('9D', $a_high->card);
    $b_high = $b->getHighCard($b_kickers);
    $this->assertEquals('10D', $b_high->card);

    $this->assertFalse($a_high::compare($a_high, $b_high), $a_high->card . ' < ' . $b_high->card);
  }

  public function testHandCompare() {
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

    $this->assertEquals(1, PokerHandCollection::compareHands($a, $b)); 
  }

}
