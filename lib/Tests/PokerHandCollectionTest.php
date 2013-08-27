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

  /**
   * Test creating a set of poker hands from a feed. Use the dummy feed here
   * because why not.
   */
  public function testFeedData() {
    $hands = PokerHandCollection::createFromFeed(new PokerHandFeedDummy);

    $this->assertInstanceOf('PokerHand\Collection\PokerHandCollection', $hands);

    $this->assertCount(2, $hands->data, 'There are 2 hands.');
  }

  /**
   * Test the setHand method.
   */
  public function testSetHand() {
    $hands = PokerHandCollection::createFromFeed(new PokerHandFeedDummy);

    $hands->setHand(new PokerHand);
  }

  /**
   * Test getHand method.
   */
  public function testGetHand() {
    $hands = PokerHandCollection::createFromFeed(new PokerHandFeedDummy);

    $this->assertEquals($hands->hands[1]->__toString(), $hands->getHand(1)->__toString());
  }

  /**
   * Test sort hands.
   */
  public function testSortHands() {
    $hands = new PokerHandCollection(array());

    $a = new PokerHand;
    $a
      ->addCard('KS', 'S', 'K')
      ->addCard('KD', 'D', 'K')
      ->addCard('5H', 'H', 5)
      ->addCard('9D', 'D', 9)
      ->addCard('3C', 'C', 3)
      ->setSets()
      ->setRank();

    $b = new PokerHand;
    $b
      ->addCard('AC', 'C', 'A')
      ->addCard('AH', 'H', 'A')
      ->addCard('5C', 'C', 5)
      ->addCard('3D', 'D', 3)
      ->addCard('4S', 'S', 4)
      ->setSets()
      ->setRank();

    $hands->hands['Player 1'] = $a;
    $hands->hands['Player 2'] = $b;
    $hands->sortHands();

    // B wins and is sorted first.
    $this->assertEquals('Player 2', key($hands->hands));

    $c = new PokerHand;
    $c
      ->addCard('AS', 'S', 'A')
      ->addCard('AD', 'D', 'A')
      ->addCard('4D', 'D', 4)
      ->addCard('7D', 'D', 7)
      ->addCard('5C', 'C', 5)
      ->setSets()
      ->setRank();

    $hands->hands['Player 3'] = $c;
    $hands->sortHands();

    // Player 3 wins and is sorted first.
    $this->assertEquals('Player 3', key($hands->hands));
  }

  /**
   * Test comparing two crap hands.
   */
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

  /**
   * Test comparing two hands full of scoring cards.
   */
  public function testAllScoringHandCompare() {
    $a = new PokerHand();
    $a
      ->addCard('QD', 'D', 'Q')
      ->addCard('8D', 'D', 8)
      ->addCard('7D', 'D', 7)
      ->addCard('4D', 'D', 4)
      ->addCard('3D', 'D', 3)
      ->setSets()
      ->setRank();

    $b = new PokerHand();
    $b
      ->addCard('6H', 'H', 6)
      ->addCard('6C', 'C', 6)
      ->addCard('QS', 'S', 'Q')
      ->addCard('QH', 'H', 'Q')
      ->addCard('QC', 'C', 'Q')
      ->setSets()
      ->setRank();

    // B > A rank
    $this->assertEquals(1, PokerHandCollection::compareHands($a, $b));

    $c = new PokerHand();
    $c
      ->addCard('5H', 'H', 5)
      ->addCard('5C', 'C', 5)
      ->addCard('JS', 'S', 'J')
      ->addCard('JH', 'H', 'J')
      ->addCard('JC', 'C', 'J')
      ->setSets()
      ->setRank();

    // B has some hand rank as C, but is greater.
    $this->assertEquals(-1, PokerHandCollection::compareHands($b, $c));
  }

  /**
   * Test comparing two hands.
   */
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
