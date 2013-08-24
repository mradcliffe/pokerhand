<?php
/**
 * @file
 * PlayingCardTest.php
 */

use PlayingCard\PlayingCard;

class PlayingCardTest extends PHPUnit_Framework_TestCase {

  public function testCardCompare() {
    $a = PlayingCard::createFromString('AS');
    $b = PlayingCard::createFromString('AH');

    $this->assertTrue(PlayingCard::compare($a, $b));
  }

  public function testCreateCard() {
    $a = PlayingCard::createFromString('AS');
    $this->assertFalse(empty($a->value));
    $this->assertFalse(empty($a->suit));
    $this->assertFalse(empty($a->card));
  }

}
