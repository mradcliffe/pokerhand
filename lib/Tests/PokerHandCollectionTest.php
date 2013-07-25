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
    $hands = new PokerHandCollection(new PokerHandFeedDummy);

    $this->assertInstanceOf('PokerHand\Collection\PokerHandCollection', $hands);

    $this->assertCount(2, $hands->data, 'There are 2 hands.');
  }

  public function testSetHand() {
    $hands = new PokerHandCollection(new PokerHandFeedDummy);

    $hands->setHand(new PokerHand);
  }

}
