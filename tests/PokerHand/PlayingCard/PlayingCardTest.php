<?php

namespace ColumbusPHP\Tests\PokerHand\PlayingCard\PlayingCardTest;

use ColumbusPHP\PokerHand\PlayingCard\PlayingCard;
use PHPUnit\Framework\TestCase;

class PlayingCardTest extends TestCase {

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
