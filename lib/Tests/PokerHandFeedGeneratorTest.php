<?php
/**
 * @file
 * PokerHandFeedGeneratorTest.php
 */

use PokerHand\Feed\PokerHandFeedGenerator;

/**
 * Test the poker hand generator feed.
 */
class PokerHandFeedGeneratorTest extends PHPUnit_Framework_TestCase {

  public function testConstruct() {
    $game = new PokerHandFeedGenerator();

    $this->assertEquals(52, $game->countCards());
  }

  public function testNumPlayers() {
    $game = PokerHandFeedGenerator::createGameOf(2);

    $this->assertEquals(52, $game->countCards());
    $this->assertEquals(2, $game->countPlayers());
  }

  public function testDeal() {
    $game = PokerHandFeedGenerator::createGameOf(2);

    $game->dealCard();
    $this->assertEquals(51, $game->countCards());
    $this->assertEquals(1, $game->countHand('Player 1'));
  }

  public function testDealAllPlayers() {
    $game = PokerHandFeedGenerator::createGameOf(2);

    $data = $game->getData(array());

    $this->assertEquals(42, $game->countCards());
  }

  public function testAllCards() {
    $game = new PokerHandFeedGenerator();

    for ($i = 1; $i <= 52; $i++) {
      $card = $game->drawCard();
      $this->assertNotNull($card['value'], $i);
      $this->assertNotNull($card['suit'], $i);
    }
  }



}
