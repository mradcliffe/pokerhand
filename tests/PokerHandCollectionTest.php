<?php
/**
 * @file
 * PokerHandCollectionTest.php
 */

use ColumbusPHP\PokerHand\PokerHand;
use ColumbusPHP\PokerHand\Collection\PokerHandCollection;
use ColumbusPHP\PokerHand\Feed\PokerHandFeedDummy;

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

    $this->assertInstanceOf('ColumbusPHP\PokerHand\Collection\PokerHandCollection', $hands);

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
   * Test sort hands. There is a bug in sortHands that this test is not
   * covering.
   */
  public function testSortSimpleHands() {
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
    reset($hands->hands);
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
    reset($hands->hands);

    // Player 3 wins and is sorted first.
    $this->assertEquals('Player 3', key($hands->hands));
  }

  /**
   * Test sortHands with greater than 2 cards.
   */
  public function testSortComplexHands() {
    $hands = new PokerHandCollection(array());

    // Player 1 has High Card, 10,
    $a = new PokerHand;
    $a
      ->addCard('10S', 'S', 10)
      ->addCard('5D', 'D', 5)
      ->addCard('2S', 'S', 2)
      ->addCard('4H', 'H', 4)
      ->addCard('JD', 'D', 'J');
    $hands->hands['a'] = $a;

    // Player 2 has One Pair, K K.
    $b = new PokerHand;
    $b
      ->addCard('3D', 'D', 3)
      ->addCard('KC', 'C', 'K')
      ->addCard('9S', 'S', 9)
      ->addCard('KH', 'H', 'K')
      ->addCard('JH', 'H', 'J');
    $hands->hands['b'] = $b;

    // Player 3 has One Pair, 8 8,
    $c = new PokerHand;
    $c
      ->addCard('8D', 'D', 8)
      ->addCard('KS', 'S', 'K')
      ->addCard('10C', 'C', 10)
      ->addCard('8H', 'H', 8)
      ->addCard('JC', 'C', 'J');
    $hands->hands['c'] = $c;

    // Player 4 has One Pair, 10 10,
    $d = new PokerHand;
    $d
      ->addCard('10H', 'H', 10)
      ->addCard('5H', 'H', 5)
      ->addCard('10D', 'D', 10)
      ->addCard('AS', 'S', 'A')
      ->addCard('9C', 'C', 9);
    $hands->hands['d'] = $d;

    // Player 5 has High Card, 7
    $e = new PokerHand;
    $e
      ->addCard('7C', 'C', 7)
      ->addCard('5C', 'C', 5)
      ->addCard('3H', 'H', 3)
      ->addCard('2D', 'D', 2)
      ->addCard('4D', 'D', 4);
    $hands->hands['e'] = $e;

    // Player 6 has High Card, K
    $f = new PokerHand;
    $f
      ->addCard('KH', 'H', 'K')
      ->addCard('QH', 'H', 'S')
      ->addCard('4C', 'C', 4)
      ->addCard('7D', 'D', 7)
      ->addCard('9D', 'D', 9);
    $hands->hands['f'] = $f;

    $hands->rankHands()->sortHands();
    reset($hands->hands);

    // B wins.
    $first = array_shift($hands->hands);
    $this->assertEquals($b->__toString(), $first->__toString(), 'B wins');

    // D.
    $second = array_shift($hands->hands);
    $this->assertEquals($d->__toString(), $second->__toString(), 'D second');

    // C.
    $third = array_shift($hands->hands);
    $this->assertEquals($c->__toString(), $third->__toString(), 'C third');

    // F
    $fourth = array_shift($hands->hands);
    $this->assertEquals($f->__toString(), $fourth->__toString(), 'F fourth');

    // A
    $fifth = array_shift($hands->hands);
    $this->assertEquals($a->__toString(), $fifth->__toString(), 'A fifth');

    // E
    $sixth = array_shift($hands->hands);
    $this->assertEquals($e->__toString(), $sixth->__toString(), 'E sixth');
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

    $hands->hands['Player 1'] = $a;
    $hands->hands['Player 2'] = $b;

    // Rank hands.
    $hands->rankHands();

    // Test the hand compare, which should test kicker compare.
    $this->assertEquals(1, PokerHandCollection::compareHands($a, $b), $a->__toString() . ' < ' . $b->__toString());
    
    // Sort hands.
    $hands->sortHands();
    reset($hands->hands);

    // Make sure the full sortHands thing works with kicker compare.
    $this->assertEquals('Player 2', key($hands->hands));
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
