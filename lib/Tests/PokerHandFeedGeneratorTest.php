<?php
/**
 * @file
 * PokerHandFeedGeneratorTest.php
 */

use ColumbusPHP\PokerHand\Feed\PokerHandFeedGenerator;

/**
 * Test the poker hand generator feed.
 */
class PokerHandFeedGeneratorTest extends PHPUnit_Framework_TestCase {

  /**
   * Test that generator creates a deck of 52 cards.
   */
  public function testConstruct() {
    $game = new PokerHandFeedGenerator();

    $this->assertEquals(52, $game->countCards());
  }

  /**
   * Test that generator creates a proper game for number of players.
   */
  public function testNumPlayers() {
    $game = PokerHandFeedGenerator::createGameOf(2);

    $this->assertEquals(52, $game->countCards());
    $this->assertEquals(2, $game->countPlayers());
  }

  /**
   * Test that generator can deal 1 card.
   */
  public function testDeal() {
    $game = PokerHandFeedGenerator::createGameOf(2);

    $game->dealCard();
    $this->assertEquals(51, $game->countCards());
    $this->assertEquals(1, $game->countHand('Player 1'));
  }

  /**
   * Test that generator can deal hands to players.
   */
  public function testDealAllPlayers() {
    $game = PokerHandFeedGenerator::createGameOf(2);

    $data = $game->getData(array());

    $this->assertEquals(42, $game->countCards());
  }

  /**
   * Test that generator can empty the deck.
   */
  public function testAllCards() {
    $game = new PokerHandFeedGenerator();

    for ($i = 1; $i <= 52; $i++) {
      $card = $game->drawCard();
      $this->assertNotNull($card['value'], $i);
      $this->assertNotNull($card['suit'], $i);
    }
  }
}
