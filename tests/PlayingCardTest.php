<?php
/**
 * @file
 * PlayingCardTest.php
 */

use ColumbusPHP\PlayingCard\PlayingCard;

class PlayingCardTest extends PHPUnit_Framework_TestCase {

  /**
   * Test comparing two cards.
   */
  public function testCardCompare() {
    $a = PlayingCard::createFromString('AS');
    $b = PlayingCard::createFromString('AH');

    $this->assertTrue(PlayingCard::compare($a, $b));
  }

  /**
   * Test creating a card from string.
   */
  public function testCreateCard() {
    $a = PlayingCard::createFromString('AS');
    $this->assertFalse(empty($a->value));
    $this->assertFalse(empty($a->suit));
    $this->assertFalse(empty($a->card));
  }

}
